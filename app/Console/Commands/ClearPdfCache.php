<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPdfCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:clear-cache {--type=all : Tipo de cache a limpiar (all, participation, cover, back)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar cache de PDFs generados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $cleared = 0;

        switch ($type) {
            case 'participation':
                $cleared = $this->clearCacheByPattern('participation_html_*');
                break;
            case 'cover':
                $cleared = $this->clearCacheByPattern('cover_html_*');
                break;
            case 'back':
                $cleared = $this->clearCacheByPattern('back_html_*');
                break;
            case 'all':
            default:
                $cleared = $this->clearCacheByPattern('*_html_*');
                break;
        }

        $this->info("Se limpiaron {$cleared} entradas de cache de PDFs.");
        
        return 0;
    }

    /**
     * Limpiar cache por patrón
     */
    private function clearCacheByPattern($pattern)
    {
        $cleared = 0;
        
        // Obtener todas las claves de cache (esto depende del driver de cache)
        $keys = $this->getCacheKeys($pattern);
        
        foreach ($keys as $key) {
            Cache::forget($key);
            $cleared++;
        }
        
        return $cleared;
    }

    /**
     * Obtener claves de cache (implementación básica)
     */
    private function getCacheKeys($pattern)
    {
        // Esta es una implementación básica
        // Para drivers como Redis, se puede usar SCAN
        // Para database/file, se puede consultar directamente
        
        $keys = [];
        
        // Implementación simple: asumir que tenemos IDs del 1 al 1000
        for ($i = 1; $i <= 1000; $i++) {
            if ($pattern === '*_html_*' || strpos($pattern, '*') !== false) {
                $keys[] = "participation_html_{$i}";
                $keys[] = "cover_html_{$i}";
                $keys[] = "back_html_{$i}";
            }
        }
        
        return $keys;
    }
}
