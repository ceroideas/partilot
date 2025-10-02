<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DesignFormat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateSimplePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $designId;
    protected $htmlField;
    protected $jobId;
    protected $filename;

    /**
     * Create a new job instance.
     */
    public function __construct($designId, $htmlField, $jobId, $filename)
    {
        $this->designId = $designId;
        $this->htmlField = $htmlField;
        $this->jobId = $jobId;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Aumentar límites para el job
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');
        
        $design = DesignFormat::findOrFail($this->designId);
        
        // Cache del HTML procesado
        $cacheKey = $this->htmlField . '_' . $this->designId;
        $html = cache()->remember($cacheKey, 3600, function() use ($design) {
            $html = $design->{$this->htmlField};
            $publicPath = public_path();
            $html = str_replace(url('/'), $publicPath, $html);
            return $this->adjustWidthsForDomPdf($html);
        });

        // Configuración
        $page = $design->page ?? 'a3';
        $orientation = $design->orientation ?? 'h';
        $pdfOrientation = ($orientation === 'h') ? 'landscape' : 'portrait';

        // Generar PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($page, $pdfOrientation);
        
        // Configurar opciones de DomPDF
        $pdf->getDomPDF()->setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ]);
        
        // Guardar PDF
        $final_path = storage_path('app/generated_pdfs/' . $this->jobId . '.pdf');
        Storage::makeDirectory('generated_pdfs');
        $pdf->save($final_path);
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
}
