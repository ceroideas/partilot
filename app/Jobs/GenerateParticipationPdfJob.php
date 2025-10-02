<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DesignFormat;
use App\Models\Set;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateParticipationPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $designId;
    protected $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct($designId, $jobId)
    {
        $this->designId = $designId;
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Aumentar límites para el job
        ini_set('max_execution_time', 0); // Sin límite de tiempo
        ini_set('memory_limit', '2048M');  // 2GB
        
        $design = DesignFormat::findOrFail($this->designId);
        
        // Cache del HTML procesado
        $cacheKey = 'participation_html_' . $this->designId;
        $participation_html = cache()->remember($cacheKey, 3600, function() use ($design) {
            $html = $design->participation_html;
            $publicPath = public_path();
            $html = str_replace(url('/'), $publicPath, $html);
            return $this->adjustWidthsForDomPdf($html);
        });

        // Obtener datos del set
        $set = $design->set_id ? Set::select('id', 'tickets', 'total_participations')->find($design->set_id) : null;
        $tickets = $set && $set->tickets ? $set->tickets : [];
        $total_participations = $set->total_participations ?? 0;

        // Determinar rango
        $generate_mode = $design->output['generate_mode'] ?? 1;
        if ($generate_mode == 1) {
            $from = 1;
            $to = $total_participations;
        } else {
            $from = $design->output['participation_from'] ?? 1;
            $to = $design->output['participation_to'] ?? $total_participations;
        }

        // Configuración
        $rows = $design->rows ?? 1;
        $cols = $design->cols ?? 1;
        $per_page = $rows * $cols;
        $page = $design->page ?? 'a3';
        $orientation = $design->orientation ?? 'h';
        $pdfOrientation = ($orientation === 'h') ? 'landscape' : 'portrait';

        // Procesar en chunks más pequeños para evitar problemas de memoria
        $chunk_size = 50; // Reducir chunk size para jobs
        $temp_files = [];
        
        for ($chunk_start = $from - 1; $chunk_start < $to; $chunk_start += $chunk_size) {
            $chunk_end = min($chunk_start + $chunk_size, $to);
            $chunk_tickets = array_slice($tickets, $chunk_start, $chunk_end - $chunk_start);
            
            $chunk_pages = ceil(count($chunk_tickets) / $per_page);
            $pages = $this->generatePagesOptimized($chunk_tickets, $chunk_pages, $per_page);
            
            $pdf = Pdf::loadView('design.pdf_participation', [
                'pages' => $pages,
                'participation_html' => $participation_html,
                'rows' => $rows,
                'cols' => $cols,
            ])->setPaper($page, $pdfOrientation);
            
            $temp_file = storage_path('app/temp_pdf_' . $this->jobId . '_' . $chunk_start . '.pdf');
            $pdf->save($temp_file);
            $temp_files[] = $temp_file;
        }
        
        // Combinar PDFs
        $this->combinePdfFiles($temp_files, $this->jobId);
    }

    /**
     * Generar páginas optimizado
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
     * Ajustar widths para DomPDF
     */
    private function adjustWidthsForDomPdf($html) {
        return preg_replace_callback(
            '/style="([^"]*)"/i',
            function ($matches) {
                $style = $matches[1];
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

                    $style = preg_replace('/width:\s*\d+px;?/i', "width: {$newWidth}px;", $style);
                    $style = preg_replace('/height:\s*\d+px;?/i', "height: {$newHeight}px;", $style);
                }
                return 'style="' . $style . '"';
            },
            $html
        );
    }

    /**
     * Combinar archivos PDF
     */
    private function combinePdfFiles($temp_files, $job_id)
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        
        foreach ($temp_files as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($i = 1; $i <= $pageCount; $i++) {
                $pdf->AddPage();
                $pdf->useTemplate($pdf->importPage($i));
            }
        }
        
        // Guardar PDF final
        $final_path = storage_path('app/generated_pdfs/' . $job_id . '.pdf');
        Storage::makeDirectory('generated_pdfs');
        $pdf->Output('F', $final_path);
        
        // Limpiar archivos temporales
        foreach ($temp_files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}
