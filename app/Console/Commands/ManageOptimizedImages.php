<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;

class ManageOptimizedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize 
                            {--clear : Limpiar imágenes optimizadas}
                            {--stats : Mostrar estadísticas}
                            {--test : Probar optimización con una imagen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar imágenes optimizadas para PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $imageService = new ImageOptimizationService();

        if ($this->option('clear')) {
            $this->clearOptimizedImages($imageService);
        } elseif ($this->option('stats')) {
            $this->showStats($imageService);
        } elseif ($this->option('test')) {
            $this->testOptimization($imageService);
        } else {
            $this->showHelp();
        }
    }

    /**
     * Limpiar imágenes optimizadas
     */
    private function clearOptimizedImages($imageService)
    {
        $deleted = $imageService->clearOptimizedImages();
        $this->info("Se eliminaron {$deleted} imágenes optimizadas.");
    }

    /**
     * Mostrar estadísticas
     */
    private function showStats($imageService)
    {
        $stats = $imageService->getOptimizationStats();
        
        $this->info("=== Estadísticas de Imágenes Optimizadas ===");
        $this->line("Archivos optimizados: {$stats['file_count']}");
        $this->line("Tamaño total: {$stats['total_size_mb']} MB");
        $this->line("Tamaño en bytes: {$stats['total_size']}");
    }

    /**
     * Probar optimización
     */
    private function testOptimization($imageService)
    {
        $testImage = $this->ask('Ingresa la ruta de la imagen a probar');
        
        if (!file_exists($testImage)) {
            $this->error("La imagen no existe: {$testImage}");
            return;
        }

        $originalSize = filesize($testImage);
        $this->info("Tamaño original: " . round($originalSize / 1024, 2) . " KB");

        $optimizedPath = $imageService->optimizeImage($testImage);
        
        if ($optimizedPath && file_exists($optimizedPath)) {
            $optimizedSize = filesize($optimizedPath);
            $savings = round((($originalSize - $optimizedSize) / $originalSize) * 100, 2);
            
            $this->info("Tamaño optimizado: " . round($optimizedSize / 1024, 2) . " KB");
            $this->info("Ahorro: {$savings}%");
        } else {
            $this->error("No se pudo optimizar la imagen");
        }
    }

    /**
     * Mostrar ayuda
     */
    private function showHelp()
    {
        $this->info("=== Gestión de Imágenes Optimizadas ===");
        $this->line("");
        $this->line("Comandos disponibles:");
        $this->line("  --clear    Limpiar todas las imágenes optimizadas");
        $this->line("  --stats    Mostrar estadísticas de optimización");
        $this->line("  --test     Probar optimización con una imagen");
        $this->line("");
        $this->line("Ejemplos:");
        $this->line("  php artisan images:optimize --clear");
        $this->line("  php artisan images:optimize --stats");
        $this->line("  php artisan images:optimize --test");
    }
}
