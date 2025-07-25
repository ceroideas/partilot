<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Lottery;
use App\Models\Set;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DesignFormat;

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
        // Mostrar solo sorteos cuya fecha sea distinta a la actual
        $today = date('Y-m-d');
        $lotteries = \App\Models\Lottery::whereDate('deadline_date', '!=', $today)->get();
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
        return view('design.format', compact('entity', 'lottery', 'set'));
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

        $designFormat = \App\Models\DesignFormat::create($data);

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
            'identation' => 'nullable|numeric',
            'matrix_box' => 'nullable|numeric',
            'horizontal_space' => 'nullable|numeric',
            'vertical_space' => 'nullable|numeric',
            'participation_html' => 'nullable|string',
            'cover_html' => 'nullable|string',
            'back_html' => 'nullable|string',
            'backgrounds' => 'nullable|array',
            'output' => 'nullable|array',
        ]);

        $data['entity_id'] = session('design_entity_id') ?? 1;
        $data['lottery_id'] = session('design_lottery_id') ?? 1;
        $data['set_id'] = $request->input('set_id', 1);

        // Guardar los bloques de diseño y configuración en el campo blocks (JSON)
        $data['blocks'] = [
            'participation_html' => $data['participation_html'] ?? '',
            'cover_html' => $data['cover_html'] ?? '',
            'back_html' => $data['back_html'] ?? '',
            'backgrounds' => $data['backgrounds'] ?? [],
            'output' => $data['output'] ?? [],
        ];

        // Guardar también en columnas explícitas
        $data['participation_html'] = $data['blocks']['participation_html'];
        $data['cover_html'] = $data['blocks']['cover_html'];
        $data['back_html'] = $data['blocks']['back_html'];
        $data['backgrounds'] = $data['blocks']['backgrounds'];
        $data['output'] = $data['blocks']['output'];

        $designFormat = \App\Models\DesignFormat::create($data);

        return response()->json(['success' => true, 'id' => $designFormat->id]);
    }

    // PDF: Participación
    public function generatePdfParticipation($id)
    {
        $design = \App\Models\DesignFormat::findOrFail($id);
        $html = $design->participation_html;
        return $this->renderPdfFromHtml($html, 'participation.pdf');
    }

    // PDF: Portada
    public function generatePdfCover($id)
    {
        $design = \App\Models\DesignFormat::findOrFail($id);
        $html = $design->cover_html;
        return $this->renderPdfFromHtml($html, 'cover.pdf');
    }

    // PDF: Trasera
    public function generatePdfBack($id)
    {
        $design = \App\Models\DesignFormat::findOrFail($id);
        $html = $design->back_html;
        return $this->renderPdfFromHtml($html, 'back.pdf');
    }

    // Utilidad para renderizar PDF desde HTML crudo
    protected function renderPdfFromHtml($html, $filename = 'document.pdf')
    {
        $pdf = \PDF::loadView('design.pdf_base', ['html' => $html]);
        return $pdf->download($filename);
    }

    public function exportPdf(Request $request)
    {
        $html = $request->input('participation_html'); // o el campo que corresponda
        $pdf = Pdf::loadHTML($html);
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
        $design = DesignFormat::findOrFail($id);
        $participation_html = $design->participation_html;

        // Reemplazar la URL base por la ruta absoluta del sistema de archivos
        $publicPath = public_path();
        $participation_html = str_replace('http://127.0.0.1:8000', $publicPath, $participation_html);

        // Ajustar widths para DomPDF
        $participation_html = $this->adjustWidthsForDomPdf($participation_html);

        // return view('design.pdf_participation', ['participation_html' => $participation_html]);

        $pdf = Pdf::loadView('design.pdf_participation', [
            'participation_html' => $participation_html
        ]);
        $pdf->setPaper('a3','landscape');
        return $pdf->stream('participacion.pdf');
    }

    public function exportCoverPdf($id)
    {
        $design = DesignFormat::findOrFail($id);
        $html = $design->cover_html;
        $pdf = Pdf::loadHTML($html);
        return $pdf->download('portada.pdf');
    }

    public function exportBackPdf($id)
    {
        $design = DesignFormat::findOrFail($id);
        $html = $design->back_html;
        $pdf = Pdf::loadHTML($html);
        return $pdf->download('trasera.pdf');
    }

    /**
     * Muestra el formulario para editar un formato existente.
     */
    public function editFormat($id)
    {
        $format = \App\Models\DesignFormat::findOrFail($id);
        // Puedes cargar relaciones si es necesario
        return view('design.edit_format', compact('format'));
    }

    /**
     * Actualiza el formato en la base de datos.
     */
    public function updateFormat(\Illuminate\Http\Request $request, $id)
    {
        $format = \App\Models\DesignFormat::findOrFail($id);
        // Procesar el JSON enviado desde el frontend (campo 'data')
        if ($request->has('data')) {
            $data = json_decode($request->input('data'), true);
            if (is_array($data)) {
                // Asignar los campos principales
                $format->format = $data['format'] ?? $format->format;
                $format->page = $data['page'] ?? $format->page;
                $format->rows = $data['rows'] ?? $format->rows;
                $format->cols = $data['cols'] ?? $format->cols;
                $format->orientation = $data['orientation'] ?? $format->orientation;
                $format->identation = $data['identation'] ?? $format->identation;
                $format->matrix_box = $data['matrix_box'] ?? $format->matrix_box;
                // $format->horizontal_space = $data['horizontal_space'] ?? $format->horizontal_space;
                // $format->vertical_space = $data['vertical_space'] ?? $format->vertical_space;
                $format->participation_html = $data['participation_html'] ?? $format->participation_html;
                $format->cover_html = $data['cover_html'] ?? $format->cover_html;
                $format->back_html = $data['back_html'] ?? $format->back_html;
                // Guardar los campos JSON como string si corresponde
                // if (isset($data['margins'])) $format->margins = json_encode($data['margins']);
                if (isset($data['backgrounds'])) $format->backgrounds = json_encode($data['backgrounds']);
                if (isset($data['output'])) $format->output = json_encode($data['output']);
                $format->save();
                return response()->json(['success' => true, 'redirect' => route('design.editFormat', $id)]);
            }
        }
        return response()->json(['success' => false], 200);
    }
} 