<?php

namespace App\Support;

/**
 * Concatena fragmentos PDF con FPDI respetando orientación y tamaño de cada página.
 */
class FpdiPdfMerge
{
    /**
     * @param  list<string>  $tempFiles  Rutas a PDFs intermedios (se borran después de fusionar).
     * @param  string|false|null  $saveToPath  Si es string no vacío, escribe ahí con Output('F').
     */
    public static function mergeTemporaryFiles(array $tempFiles, $saveToPath = false): ?string
    {
        $pdf = new \setasign\Fpdi\Fpdi();

        foreach ($tempFiles as $file) {
            if (! is_file($file)) {
                continue;
            }
            $pageCount = $pdf->setSourceFile($file);
            for ($i = 1; $i <= $pageCount; $i++) {
                $templateId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($templateId);
                if ($size === false) {
                    continue;
                }
                $orientation = $size['orientation'] ?? 'P';
                $pdf->AddPage($orientation, $size);
                $pdf->useTemplate($templateId);
            }
        }

        foreach ($tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        if ($saveToPath !== false && $saveToPath !== null && $saveToPath !== '') {
            $pdf->Output('F', $saveToPath);

            return null;
        }

        return $pdf->Output('S');
    }
}
