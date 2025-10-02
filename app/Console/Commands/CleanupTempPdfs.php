<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempPdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:cleanup {--hours=24 : Horas después de las cuales limpiar archivos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar archivos PDF temporales antiguos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $tempPath = storage_path('app/temp_pdfs');
        $generatedPath = storage_path('app/generated_pdfs');
        
        $cleaned = 0;
        
        // Limpiar archivos temporales
        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*.pdf');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime->timestamp) {
                    unlink($file);
                    $cleaned++;
                }
            }
        }
        
        // Limpiar archivos generados antiguos
        if (is_dir($generatedPath)) {
            $files = glob($generatedPath . '/*.pdf');
            foreach ($files as $file) {
                if (filemtime($file) < $cutoffTime->timestamp) {
                    unlink($file);
                    $cleaned++;
                }
            }
        }
        
        $this->info("Se limpiaron {$cleaned} archivos PDF temporales.");
        
        return 0;
    }
}
