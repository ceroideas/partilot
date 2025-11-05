<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Lottery;
use App\Models\Set;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DesignFormat;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageOptimizationService;
use App\Services\QrCodeService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DesignController extends Controller
{
    // Paso 1: Seleccionar entidad
    public function selectEntity()
    {
        $entities = Entity::all();
        return view('design.add', compact('entities'));
    }

    // Paso 2: Seleccionar sorteo
    public function selectLottery($entity_id = null)
    {
        if (!$entity_id) {
            $entity_id = session('design_entity_id');
        }
        $entity = Entity::findOrFail($entity_id);
        
        // Mostrar solo sorteos que tienen sets asociados para esta entidad
        $lotteries = \App\Models\Lottery::whereHas('reserves', function($query) use ($entity_id) {
                $query->where('entity_id', $entity_id)
                      ->whereHas('sets', function($setQuery) {
                          $setQuery->where('status', 1); // Solo sets activos
                      });
            })
            ->whereDate('deadline_date', '!=', date('Y-m-d')) // Excluir sorteos de hoy
            ->orderBy('draw_date', 'desc')
            ->get();
            
        return view('design.add_lottery', compact('entity', 'lotteries'));
    }

    // Paso 3: Seleccionar set
    public function selectSet()
    {
        $entity_id = session('design_entity_id');
        $lottery_id = session('design_lottery_id');

        $entity = \App\Models\Entity::findOrFail($entity_id);
        $lottery = \App\Models\Lottery::findOrFail($lottery_id);
        // Buscar todos los sets de la entidad y sorteo (a través de la reserva)
        $sets = \App\Models\Set::where('entity_id', $entity_id)
            ->whereHas('reserve', function($q) use ($lottery_id) {
                $q->where('lottery_id', $lottery_id);
            })
            ->get();
        // Obtener la reserva principal (opcional, para la vista)
        $reserve = \App\Models\Reserve::where('entity_id', $entity_id)->where('lottery_id', $lottery_id)->first();
        return view('design.add_set', compact('entity', 'lottery', 'sets', 'reserve'));
    }

    // Paso 4: Mostrar formato final
    public function format(Request $request)
    {
        $entity = Entity::findOrFail(session('design_entity_id'));
        $lottery = Lottery::findOrFail(session('design_lottery_id'));
        $set = Set::findOrFail($request->set_id);
        $reservation_numbers = $set->reserve ? $set->reserve->reservation_numbers : [];
        return view('design.format', compact('entity', 'lottery', 'set', 'reservation_numbers'));
    }

    // Guardar selección de entidad en sesión y redirigir a selección de sorteo
    public function storeEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);
        $entity_id = $request->entity_id;
        session(['design_entity_id' => $entity_id]);
        return redirect()->route('design.selectLottery');
    }

    // Guardar selección de sorteo y redirigir a selección de set
    public function storeLottery(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id',
            'lottery_id' => 'required|integer|exists:lotteries,id'
        ]);
        session(['design_entity_id' => $request->entity_id]);
        session(['design_lottery_id' => $request->lottery_id]);

        return redirect()->route('design.selectSet');
    }
/*
    // Guardar el formato de diseño enviado desde la vista
    public function storeFormat(Request $request)
    {
        $data = $request->validate([
            'entity_id' => 'required|integer|exists:entities,id',
            'lottery_id' => 'required|integer|exists:lotteries,id',
            'set_id' => 'required|integer|exists:sets,id',
            'format' => 'nullable|string',
            'page' => 'nullable|string',
            'rows' => 'nullable|integer',
            'cols' => 'nullable|integer',
            'orientation' => 'nullable|string',
            'margin_up' => 'nullable|numeric',
            'margin_right' => 'nullable|numeric',
            'margin_left' => 'nullable|numeric',
            'margin_top' => 'nullable|numeric',
            'identation' => 'nullable|numeric',
            'matrix_box' => 'nullable|numeric',
            'page_rigth' => 'nullable|numeric',
            'page_bottom' => 'nullable|numeric',
            'guide_color' => 'nullable|string',
            'guide_weight' => 'nullable|numeric',
            'participation_number' => 'nullable|integer',
            'participation_from' => 'nullable|integer',
            'participation_to' => 'nullable|integer',
            'participation_page' => 'nullable|integer',
            'guides' => 'nullable|boolean',
            'generate' => 'nullable|string',
            'documents' => 'nullable|string',
            'blocks' => 'nullable|json',
        ]);

        // Decodificar blocks si viene como string JSON
        if (isset($data['blocks']) && is_string($data['blocks'])) {
            $data['blocks'] = json_decode($data['blocks'], true);
        }

        $designFormat = DesignFormat::create($data);

        return redirect()->back()->with('success', 'Formato guardado correctamente.');
    }*/

    // Guardar el formato de diseño enviado desde el frontend (API)
    public function saveFormat(Request $request)
    {
        $data = $request->validate([
            'format' => 'nullable|string',
            'page' => 'nullable|string',
            'rows' => 'nullable|integer',
            'cols' => 'nullable|integer',
            'orientation' => 'nullable|string',
            'margins' => 'nullable|array',
            'margin_custom' => 'nullable|numeric',
            'identation' => 'nullable|numeric',
            'matrix_box' => 'nullable|numeric',
            'horizontal_space' => 'nullable|numeric',
            'vertical_space' => 'nullable|numeric',
            'participation_html' => 'nullable|string',
            'cover_html' => 'nullable|string',
            'back_html' => 'nullable|string',
            'backgrounds' => 'nullable|array',
            'output' => 'nullable|array',
            'snapshot_path' => 'nullable|string',
        ]);

        // return response()->json([$request->design_lottery_id,$request->design_entity_id],422);

        $data['set_id'] = $request->input('set_id', 1);

        $data['entity_id'] = $request->design_entity_id ?? 1;
        $data['lottery_id'] = $request->design_lottery_id ?? 1;

        // Guardar los bloques de diseño y configuración en el campo blocks (JSON)
        $data['blocks'] = [
            'participation_html' => $data['participation_html'] ?? '',
            'cover_html' => $data['cover_html'] ?? '',
            'back_html' => $data['back_html'] ?? '',
            'backgrounds' => $data['backgrounds'] ?? [],
            'output' => $data['output'] ?? [],
            'margins' => $data['margins'] ?? [],
        ];

        // Guardar también en columnas explícitas
        $data['participation_html'] = $data['blocks']['participation_html'];
        $data['cover_html'] = $data['blocks']['cover_html'];
        $data['back_html'] = $data['blocks']['back_html'];
        $data['backgrounds'] = $data['blocks']['backgrounds'];
        $data['output'] = $data['blocks']['output'];
        $data['margins'] = $data['blocks']['margins'];
        $data['snapshot_path'] = $data['snapshot_path'] ?? null;
        // return $data;

        $designFormat = DesignFormat::create($data);

        return response()->json(['success' => true, 'id' => $designFormat->id]);
    }

    // PDF: Participación
    public function generatePdfParticipation($id)
    {
        $design = DesignFormat::findOrFail($id);
        $html = $design->participation_html;
        return $this->renderPdfFromHtml($html, 'participation.pdf');
    }

    // PDF: Portada
    public function generatePdfCover($id)
    {
        $design = DesignFormat::findOrFail($id);
        $html = $design->cover_html;
        return $this->renderPdfFromHtml($html, 'cover.pdf');
    }

    // PDF: Trasera
    public function generatePdfBack($id)
    {
        $design = DesignFormat::findOrFail($id);
        $html = $design->back_html;
        return $this->renderPdfFromHtml($html, 'back.pdf');
    }

    // Utilidad para renderizar PDF desde HTML crudo
    protected function renderPdfFromHtml($html, $filename = 'document.pdf')
    {
        return view('design.pdf_base', ['html' => $html]);
        $pdf = \PDF::loadView('design.pdf_base', ['html' => $html]);
        return $pdf->download($filename);
    }

    public function exportPdf(Request $request)
    {
        // Aumentar límites para PDFs grandes
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');
        
        $html = $request->input('participation_html');
        
        // Optimizar HTML antes de generar PDF
        $publicPath = public_path();
        $html = str_replace(url('/'), $publicPath, $html);
        $html = $this->adjustWidthsForDomPdf($html);
        
        // Configurar opciones de DomPDF para mejor rendimiento
        $pdf = Pdf::loadHTML($html);
        $pdf->getDomPDF()->setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ]);
        
        return $pdf->download('diseño.pdf');
    }

    // Ajusta el width y height de los elementos con width, height y padding para DomPDF, sin importar el orden en el style
    private function adjustWidthsForDomPdf($html) {
        return preg_replace_callback(
            '/style="([^"]*)"/i',
            function ($matches) {
                $style = $matches[1];

                // Buscar width, height y padding (en cualquier orden)
                if (
                    preg_match('/width:\s*(\d+)px;?/i', $style, $widthMatch) &&
                    preg_match('/height:\s*(\d+)px;?/i', $style, $heightMatch) &&
                    preg_match('/padding:\s*(\d+)px;?/i', $style, $paddingMatch)
                ) {
                    $width = (int)$widthMatch[1];
                    $height = (int)$heightMatch[1];
                    $padding = (int)$paddingMatch[1];
                    $newWidth = $width - ($padding * 2);
                    $newHeight = $height - ($padding * 2);

                    // Reemplazar los valores en el style
                    $style = preg_replace('/width:\s*\d+px;?/i', "width: {$newWidth}px;", $style);
                    $style = preg_replace('/height:\s*\d+px;?/i', "height: {$newHeight}px;", $style);
                }
                return 'style="' . $style . '"';
            },
            $html
        );
    }

    public function exportParticipationPdf($id)
    {
        // Aumentar límites para PDFs grandes
        ini_set('max_execution_time', 300); // 5 minutos
        ini_set('memory_limit', '1024M');   // 1GB
        
        $design = DesignFormat::findOrFail($id);
        
        // Cache del HTML procesado para evitar reprocesar
        $cacheKey = 'participation_html_' . $id;
        $participation_html = cache()->remember($cacheKey, 3600, function() use ($design) {
            $html = $design->participation_html;
            // Reemplazar la URL base por la ruta absoluta del sistema de archivos
            $publicPath = public_path();
            $html = str_replace(url('/'), $publicPath, $html);
            // Ajustar widths para DomPDF
            return $this->adjustWidthsForDomPdf($html);
        });

        // Determinar tamaño y orientación
        $page = $design->page ?? 'a3';
        $orientation = $design->orientation ?? 'h';
        $pdfOrientation = ($orientation === 'h') ? 'landscape' : 'portrait';

        // Obtener tickets del set con eager loading optimizado
        $set = $design->set_id ? Set::select('id', 'tickets', 'total_participations')->find($design->set_id) : null;
        $tickets = $set && $set->tickets ? $set->tickets : [];
        $total_participations = $set->total_participations ?? 0;

        // Determinar rango de tickets a imprimir
        $generate_mode = $design->output['generate_mode'] ?? 1;
        if ($generate_mode == 1) {
            $from = 1;
            $to = $total_participations;
        } else {
            $from = $design->output['participation_from'] ?? 1;
            $to = $design->output['participation_to'] ?? $total_participations;
        }
        
        // Calcular filas y columnas
        $rows = $design->rows ?? 1;
        $cols = $design->cols ?? 1;
        $per_page = $rows * $cols;
        $total = $to - $from + 1;
        $total_pages = ceil($total / $per_page);

        // Obtener tickets a imprimir
        $tickets_to_print = array_slice($tickets, $from - 1, $to - $from + 1);

        // Optimizar HTML de participación (configurable)
        if (config('qr_optimization.optimize_images', false)) {
            $participation_html = $this->optimizeParticipationHtml($participation_html, $tickets_to_print);
        }

        // Generar QR codes en lote para todas las referencias únicas (usando Endroid - ultra-optimizado)
        $qrService = new \App\Services\EndroidQrCodeService();
        $uniqueReferences = [];
        foreach ($tickets_to_print as $ticket) {
            if (isset($ticket['r']) && !in_array($ticket['r'], $uniqueReferences)) {
                $uniqueReferences[] = $ticket['r'];
            }
        }
        
        // Usar el método más eficiente según la cantidad
        // if (count($uniqueReferences) > 200) {
            $qrCodes = $qrService->generateUltraFastQrCodes($uniqueReferences);
        /*} else {
            $qrCodes = $qrService->generateMultipleQrCodes($uniqueReferences);
        }*/

        // Para PDFs muy grandes (>500 participaciones), usar procesamiento por lotes
        if ($total > 500) {
            return $this->generatePdfInChunks($design, $participation_html, $tickets, $from, $to, $rows, $cols, $page, $pdfOrientation, $qrCodes);
        }
        
        // Ordenar tickets en modo guillotina (optimizado)
        $pages = $this->generatePagesOptimized($tickets_to_print, $total_pages, $per_page);

        /*return view('design.pdf_participation', [
            'pages' => $pages,
            'participation_html' => $participation_html,
            'rows' => $rows,
            'cols' => $cols,
            'qrCodes' => $qrCodes,
        ]);*/

        $pdf = Pdf::loadView('design.pdf_participation', [
            'pages' => $pages,
            'participation_html' => $participation_html,
            'rows' => $rows,
            'cols' => $cols,
            'qrCodes' => $qrCodes,
        ])->setPaper($page, $pdfOrientation);
        
        // Limpiar QR codes temporales después de generar el PDF
        $this->cleanupTempQrCodes();
        
        return $pdf->stream('participacion.pdf');
    }

    public function exportCoverPdf($id)
    {
        return $this->generateOptimizedPdf($id, 'cover_html', 'portada.pdf');
    }

    public function exportBackPdf($id)
    {
        return $this->generateOptimizedPdf($id, 'back_html', 'trasera.pdf');
    }

    /**
     * Método genérico optimizado para generar PDFs
     */
    private function generateOptimizedPdf($id, $htmlField, $filename)
    {
        // Aumentar límites para PDFs grandes
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');
        
        $design = DesignFormat::findOrFail($id);
        
        // Cache del HTML procesado con optimización de imágenes
        $cacheKey = $htmlField . '_' . $id;
        $html = cache()->remember($cacheKey, 3600, function() use ($design, $htmlField) {
            $html = $design->$htmlField;
            
            // Usar servicio de optimización de imágenes
            $imageService = new ImageOptimizationService();
            $html = $imageService->optimizeHtmlImages($html);
            
            // Reemplazar la URL base por la ruta absoluta del sistema de archivos
            $publicPath = public_path();
            $html = str_replace(url('/'), $publicPath, $html);
            // Ajustar widths para DomPDF
            return $this->adjustWidthsForDomPdf($html);
        });

        // Determinar tamaño y orientación
        $page = $design->page ?? 'a3';
        $orientation = $design->orientation ?? 'h';
        $pdfOrientation = ($orientation === 'h') ? 'landscape' : 'portrait';

        // Determinar la vista a usar según el tipo de PDF
        $viewName = 'design.pdf_base'; // Vista por defecto
        if ($htmlField === 'cover_html') {
            $viewName = 'design.pdf_cover';
        } elseif ($htmlField === 'back_html') {
            $viewName = 'design.pdf_back';
        }

        // Usar vista optimizada para mejor rendimiento
        $pdf = Pdf::loadView($viewName, ['html' => $html]);
        $pdf->setPaper($page, $pdfOrientation);
        
        // Configurar opciones de DomPDF para mejor rendimiento
        $pdf->getDomPDF()->setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'enable_remote' => true,
            'enable_html5_parser' => true,
            'enable_php' => true,
        ]);
        
        return $pdf->download($filename);
    }

    /**
     * Muestra el formulario para editar un formato existente.
     */
    public function editFormat($id)
    {
        $format = DesignFormat::findOrFail($id);
        $set = $format->set_id ? Set::find($format->set_id) : null;
        $reservation_numbers = $set && $set->reserve ? $set->reserve->reservation_numbers : [];
        return view('design.edit_format', compact('format', 'set', 'reservation_numbers'));
    }

    /**
     * Actualiza el formato en la base de datos.
     */
    public function updateFormat(Request $request, $id)
    {
        // return $request->all();

        $format = DesignFormat::findOrFail($id);
        // Procesar el JSON enviado desde el frontend (campo 'data')
        // if ($request->has('data')) {
            // $data = json_decode($request->input('data'), true);
            $data = $request->all();
            if (is_array($data)) {
                // Asignar los campos principales
                $format->format = $data['format'] ?? $format->format;
                $format->page = $data['page'] ?? $format->page;
                $format->rows = $data['rows'] ?? $format->rows;
                $format->cols = $data['cols'] ?? $format->cols;
                $format->orientation = $data['orientation'] ?? $format->orientation;
                $format->identation = $data['identation'] ?? $format->identation;
                $format->matrix_box = $data['matrix_box'] ?? $format->matrix_box;
                $format->horizontal_space = $data['horizontal_space'] ?? $format->horizontal_space;
                $format->vertical_space = $data['vertical_space'] ?? $format->vertical_space;
                $format->margin_custom = $data['margin_custom'] ?? $format->margin_custom;
                $format->participation_html = $data['participation_html'] ?? $format->participation_html;
                $format->cover_html = $data['cover_html'] ?? $format->cover_html;
                $format->back_html = $data['back_html'] ?? $format->back_html;
                $format->snapshot_path = $data['snapshot_path'] ?? $format->snapshot_path;
                // Guardar los campos JSON como string si corresponde
                if (isset($data['margins'])) $format->margins = $data['margins'];
                if (isset($data['backgrounds'])) $format->backgrounds = $data['backgrounds'];
                if (isset($data['output'])) $format->output = $data['output'];
                $format->save();
                return response()->json(['success' => true, 'redirect' => route('design.editFormat', $id)]);
            }
        // }
        // return response()->json(['success' => false], 200);
    }

    /**
     * Generar páginas optimizado para evitar bucles anidados costosos
     */
    private function generatePagesOptimized($tickets_to_print, $total_pages, $per_page)
    {
        $pages = [];
        $ticket_count = count($tickets_to_print);
        
        for ($p = 0; $p < $total_pages; $p++) {
            $pages[$p] = [];
            for ($i = 0; $i < $per_page; $i++) {
                $ticket_index = $p + ($i * $total_pages);
                if ($ticket_index < $ticket_count) {
                    $pages[$p][$i] = $tickets_to_print[$ticket_index];
                }
            }
        }
        
        return $pages;
    }

    /**
     * Generar PDF en lotes para PDFs muy grandes
     */
    private function generatePdfInChunks($design, $participation_html, $tickets, $from, $to, $rows, $cols, $page, $pdfOrientation, $qrCodes = [])
    {
        $per_page = $rows * $cols;
        $chunk_size = 100; // Procesar de 100 en 100
        $total = $to - $from + 1;
        $total_pages = ceil($total / $per_page);
        
        // Crear archivo temporal para combinar PDFs
        $temp_files = [];
        
        for ($chunk_start = $from - 1; $chunk_start < $to; $chunk_start += $chunk_size) {
            $chunk_end = min($chunk_start + $chunk_size, $to);
            $chunk_tickets = array_slice($tickets, $chunk_start, $chunk_end - $chunk_start);
            
            // Calcular páginas para este chunk
            $chunk_pages = ceil(count($chunk_tickets) / $per_page);
            $pages = $this->generatePagesOptimized($chunk_tickets, $chunk_pages, $per_page);
            
            // Generar PDF para este chunk
            $pdf = Pdf::loadView('design.pdf_participation', [
                'pages' => $pages,
                'participation_html' => $participation_html,
                'rows' => $rows,
                'cols' => $cols,
                'qrCodes' => $qrCodes,
            ])->setPaper($page, $pdfOrientation);
            
            // Guardar en archivo temporal
            $temp_file = storage_path('app/temp_pdf_' . $chunk_start . '.pdf');
            $pdf->save($temp_file);
            $temp_files[] = $temp_file;
        }
        
        // Combinar PDFs usando una librería como TCPDF o FPDI
        return $this->combinePdfFiles($temp_files, 'participacion.pdf');
    }

    /**
     * Combinar múltiples archivos PDF en uno solo
     */
    private function combinePdfFiles($temp_files, $filename)
    {
        // Usar FPDI para combinar PDFs
        $pdf = new \setasign\Fpdi\Fpdi();
        
        foreach ($temp_files as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($i = 1; $i <= $pageCount; $i++) {
                $pdf->AddPage();
                $pdf->useTemplate($pdf->importPage($i));
            }
        }
        
        // Limpiar archivos temporales
        foreach ($temp_files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Método alternativo para PDFs muy grandes usando colas
     */
    public function exportParticipationPdfAsync($id)
    {
        $design = DesignFormat::findOrFail($id);
        
        // Para PDFs muy grandes (>1000 participaciones), usar procesamiento asíncrono
        $set = $design->set_id ? Set::select('id', 'total_participations')->find($design->set_id) : null;
        $total_participations = $set->total_participations ?? 0;
        
        if ($total_participations > 1000) {
            // Generar un ID único para el trabajo
            $job_id = 'pdf_' . $id . '_' . time();
            
            // Dispatch job para procesar en background
            Queue::push(new \App\Jobs\GenerateParticipationPdfJob($id, $job_id));
            
            return response()->json([
                'status' => 'processing',
                'job_id' => $job_id,
                'message' => 'El PDF se está generando en segundo plano. Te notificaremos cuando esté listo.',
                'check_url' => route('design.checkPdfStatus', $job_id)
            ]);
        }
        
        // Para PDFs pequeños, usar el método normal
        return $this->exportParticipationPdf($id);
    }

    /**
     * Verificar el estado de un PDF en procesamiento
     */
    public function checkPdfStatus($job_id)
    {
        $file_path = storage_path('app/generated_pdfs/' . $job_id . '.pdf');
        
        if (file_exists($file_path)) {
            return response()->json([
                'status' => 'completed',
                'download_url' => route('design.downloadPdf', $job_id)
            ]);
        }
        
        return response()->json([
            'status' => 'processing',
            'message' => 'El PDF aún se está generando...'
        ]);
    }

    /**
     * Descargar PDF generado
     */
    public function downloadPdf($job_id)
    {
        $file_path = storage_path('app/generated_pdfs/' . $job_id . '.pdf');
        
        if (!file_exists($file_path)) {
            abort(404, 'PDF no encontrado');
        }
        
        return response()->download($file_path, 'participacion.pdf')->deleteFileAfterSend(true);
    }


    /**
     * Optimizar imágenes reutilizables en el HTML
     */
    private function optimizeReusableImages($html)
    {
        // Detectar todas las imágenes en el HTML
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        $images = $matches[1];
        
        if (empty($images)) {
            return $html;
        }

        // Agrupar imágenes por hash de contenido (imágenes idénticas)
        $imageGroups = [];
        $optimizedImages = [];
        
        foreach ($images as $imagePath) {
            $fullPath = $this->getImageFullPath($imagePath);
            if (file_exists($fullPath)) {
                $imageHash = md5_file($fullPath);
                if (!isset($imageGroups[$imageHash])) {
                    $imageGroups[$imageHash] = [
                        'original_path' => $imagePath,
                        'full_path' => $fullPath,
                        'optimized_path' => $this->optimizeImage($fullPath, $imageHash),
                        'count' => 0
                    ];
                }
                $imageGroups[$imageHash]['count']++;
                $optimizedImages[$imagePath] = $imageGroups[$imageHash]['optimized_path'];
            }
        }

        // Reemplazar todas las referencias a imágenes con las optimizadas
        foreach ($optimizedImages as $originalPath => $optimizedPath) {
            $html = str_replace($originalPath, $optimizedPath, $html);
        }

        return $html;
    }

    /**
     * Obtener la ruta completa de una imagen
     */
    private function getImageFullPath($imagePath)
    {
        // Si ya es una ruta absoluta
        if (strpos($imagePath, public_path()) === 0) {
            return $imagePath;
        }
        
        // Si es una URL relativa
        if (strpos($imagePath, '/') === 0) {
            return public_path() . $imagePath;
        }
        
        // Si es una URL completa
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Ruta relativa desde public
        return public_path() . '/' . ltrim($imagePath, '/');
    }

    /**
     * Optimizar una imagen individual
     */
    private function optimizeImage($imagePath, $imageHash)
    {
        $cacheDir = storage_path('app/optimized_images');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $optimizedPath = $cacheDir . '/' . $imageHash . '.jpg';
        
        // Si ya existe la imagen optimizada, devolverla
        if (file_exists($optimizedPath)) {
            return $optimizedPath;
        }

        // Optimizar la imagen
        $this->compressImage($imagePath, $optimizedPath);
        
        return $optimizedPath;
    }

    /**
     * Comprimir imagen para reducir tamaño
     */
    private function compressImage($sourcePath, $destinationPath)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            copy($sourcePath, $destinationPath);
            return;
        }

        $mimeType = $imageInfo['mime'];
        
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                copy($sourcePath, $destinationPath);
                return;
        }

        if (!$sourceImage) {
            copy($sourcePath, $destinationPath);
            return;
        }

        // Comprimir a JPEG con calidad 85% (balance entre calidad y tamaño)
        imagejpeg($sourceImage, $destinationPath, 85);
        imagedestroy($sourceImage);
    }

    /**
     * Optimizar HTML de participación (simplificado - solo si es necesario)
     */
    private function optimizeParticipationHtml($html, $tickets)
    {
        // Solo optimizar imágenes si hay muchas (para evitar ralentizar)
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        $baseImages = $matches[1];
        
        // Solo optimizar si hay pocas imágenes (para no ralentizar)
        if (count($baseImages) <= 5) {
            $imageService = new ImageOptimizationService();
            
            foreach ($baseImages as $imagePath) {
                $optimizedPath = $imageService->optimizeImage($imagePath);
                if ($optimizedPath) {
                    $html = str_replace($imagePath, $optimizedPath, $html);
                }
            }
        }

        return $html;
    }

    /**
     * Preparar QR codes para todas las participaciones (simplificado)
     */
    private function prepareQrCodesForTickets($tickets)
    {
        if (empty($tickets)) {
            return;
        }

        // Solo generar QR codes únicos para evitar duplicados
        $uniqueReferences = [];
        foreach ($tickets as $ticket) {
            if (isset($ticket['r']) && !in_array($ticket['r'], $uniqueReferences)) {
                $uniqueReferences[] = $ticket['r'];
            }
        }

        // Pre-generar QR codes únicos en lote (mucho más eficiente)
        $qrService = new QrCodeService();
        $qrService->generateMultipleQrCodes($uniqueReferences);
    }

    /**
     * Limpiar QR codes temporales después de generar PDF (deshabilitado)
     */
    private function cleanupTempQrCodes()
    {
        // Los QR codes se mantienen para reutilización
        // Solo se limpian manualmente con el comando
        // $qrService = new QrCodeService();
        // $qrService->clearOldQrCodes(0);
    }

    /**
     * Versiones asíncronas para cover y back PDFs
     */
    public function exportCoverPdfAsync($id)
    {
        return $this->generateOptimizedPdfAsync($id, 'cover_html', 'portada.pdf');
    }

    public function exportBackPdfAsync($id)
    {
        return $this->generateOptimizedPdfAsync($id, 'back_html', 'trasera.pdf');
    }

    /**
     * Método genérico para PDFs asíncronos
     */
    private function generateOptimizedPdfAsync($id, $htmlField, $filename)
    {
        $design = DesignFormat::findOrFail($id);
        
        // Para PDFs simples, usar procesamiento asíncrono solo si es muy grande
        $html = $design->$htmlField;
        $htmlSize = strlen($html);
        
        if ($htmlSize > 500000) { // Si el HTML es muy grande (>500KB)
            $job_id = 'pdf_' . $htmlField . '_' . $id . '_' . time();
            
            // Dispatch job para procesar en background
            Queue::push(new \App\Jobs\GenerateSimplePdfJob($id, $htmlField, $job_id, $filename));
            
            return response()->json([
                'status' => 'processing',
                'job_id' => $job_id,
                'message' => 'El PDF se está generando en segundo plano. Te notificaremos cuando esté listo.',
                'check_url' => route('design.checkPdfStatus', $job_id)
            ]);
        }
        
        // Para PDFs pequeños, usar el método normal optimizado
        return $this->generateOptimizedPdf($id, $htmlField, $filename);
    }

    public function saveSnapshot(Request $request) {
        $validated = $request->validate([
            'design_id' => 'required|exists:sets,id',
            'snapshot' => 'required|string',
        ]);
        $set = \App\Models\Set::findOrFail($validated['design_id']);
        $imgData = $validated['snapshot'];
        $img = str_replace('data:image/png;base64,', '', $imgData);
        $img = str_replace(' ', '+', $img);
        $fileName = 'design_snapshots/design_set_' . $set->id . '.png';
        \Storage::disk('public')->put($fileName, base64_decode($img));
        // $set->snapshot_path = $fileName;
        // $set->save();
        return ['success' => true, 'path' => $fileName];
    }

    /**
     * Mostrar todos los formatos de diseño.
     */
    public function index()
    {
        $designs = DesignFormat::with(['entity', 'lottery', 'set'])->orderByDesc('id')->get();
        return view('design.index', compact('designs'));
    }
} 