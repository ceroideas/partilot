<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class ImportEmailTemplates extends Command
{
    protected $signature = 'emails:import-templates {path : Ruta al JSON export (ej. Mails y notificaciones para partilot (1).json)} {--force : Sobrescribir campos existentes}';
    protected $description = 'Importa plantillas de emails desde el JSON caótico de “Mails y notificaciones para partilot”.';

    public function handle(): int
    {
        $path = $this->argument('path');
        if (!is_string($path) || $path === '' || !file_exists($path)) {
            $this->error("No existe el archivo: {$path}");
            return self::FAILURE;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            $this->error('No se pudo leer el archivo.');
            return self::FAILURE;
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            $this->error('El JSON no tiene el formato esperado (array).');
            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $imported = 0;
        $skipped = 0;

        foreach ($json as $row) {
            if (!is_array($row)) {
                continue;
            }

            $title = isset($row['MAILS Y NOTIFICACIONES']) ? (string) $row['MAILS Y NOTIFICACIONES'] : null;
            $title = $title ? trim($title) : null;
            if (! $title) {
                $skipped++;
                continue;
            }

            $key = $this->makeKeyFromTitle($title);

            $column3 = isset($row['Column3']) ? (string) $row['Column3'] : null; // “cuando hacer / condiciones”
            $column4 = isset($row['Column4']) ? (bool) $row['Column4'] : true; // email on/off (según tu JSON)
            $column5 = isset($row['Column5']) ? (string) $row['Column5'] : null; // asunto + cuerpo
            $column6 = isset($row['Column6']) ? (bool) $row['Column6'] : false; // notificación on/off (según tu JSON)

            $subjectTemplate = null;
            $bodyTemplate = null;

            if (is_string($column5) && $column5 !== '') {
                // Captura “Asunto: ....” hasta el salto de línea
                if (preg_match('/Asunto:\s*(.+?)(\r?\n|$)/u', $column5, $m)) {
                    $subjectTemplate = trim($m[1] ?? '');
                    // Elimina la primera línea del asunto
                    $bodyTemplate = trim(preg_replace('/Asunto:\s*.+?(\r?\n|$)/u', '', $column5, 1) ?? '');
                } else {
                    $bodyTemplate = trim($column5);
                }
            }

            $data = [
                'key' => $key,
                'title' => $title,
                'trigger_text' => $column3,
                'condition_text' => null,
                'subject_template' => $subjectTemplate,
                'body_template' => $bodyTemplate,
                'enabled_email' => $column4,
                'enabled_notification' => $column6,
                'metadata' => [
                    'source_columns' => array_keys($row),
                    'raw' => [
                        'Column3' => $column3,
                        'Column4' => $column4,
                        'Column5_has' => $column5 ? true : false,
                        'Column6' => $column6,
                        'Column7' => $row['Column7'] ?? null,
                    ],
                ],
            ];

            $existing = EmailTemplate::query()->where('key', $key)->first();
            if ($existing && ! $force) {
                $skipped++;
                continue;
            }

            EmailTemplate::updateOrCreate(['key' => $key], $data);
            $imported++;
        }

        $this->info("Importación completada. Importadas: {$imported}, saltadas: {$skipped}.");
        return self::SUCCESS;
    }

    private function makeKeyFromTitle(string $title): string
    {
        $key = mb_strtolower(trim($title));
        $key = preg_replace('/[^a-z0-9]+/u', '_', $key);
        $key = trim((string) preg_replace('/_+/', '_', $key));
        return $key ?: 'template_' . md5($title);
    }
}

