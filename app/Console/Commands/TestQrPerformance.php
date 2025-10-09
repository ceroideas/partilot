<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EndroidQrCodeService;
use App\Http\Controllers\DesignController;

class TestQrPerformance extends Command
{
    protected $signature = 'qr:performance-test {count=100}';
    protected $description = 'Probar rendimiento de QR codes con y sin optimización de imágenes';

    public function handle()
    {
        $count = (int) $this->argument('count');
        
        $this->info("🚀 Probando rendimiento de QR codes con {$count} referencias...");
        $this->line("");

        // Generar referencias de prueba
        $references = [];
        for ($i = 1; $i <= $count; $i++) {
            $references[] = '000100111759087003' . str_pad($i, 3, '0', STR_PAD_LEFT);
        }

        // Probar con optimización de imágenes DESHABILITADA
        $this->line("📊 Probando SIN optimización de imágenes...");
        config(['qr_optimization.optimize_images' => false]);
        
        $startTime = microtime(true);
        $qrService = new EndroidQrCodeService();
        $results1 = $qrService->generateMultipleQrCodes($references);
        $endTime = microtime(true);
        $timeWithoutOptimization = round(($endTime - $startTime) * 1000, 2);

        // Limpiar cache
        $qrService->clearQrCache();

        // Probar con optimización de imágenes HABILITADA
        $this->line("📊 Probando CON optimización de imágenes...");
        config(['qr_optimization.optimize_images' => true]);
        
        $startTime = microtime(true);
        $results2 = $qrService->generateMultipleQrCodes($references);
        $endTime = microtime(true);
        $timeWithOptimization = round(($endTime - $startTime) * 1000, 2);

        $this->line("");
        $this->info("📈 Resultados de la prueba de rendimiento:");
        $this->line("🔸 Sin optimización de imágenes: {$timeWithoutOptimization}ms");
        $this->line("🔸 Con optimización de imágenes: {$timeWithOptimization}ms");
        
        if ($timeWithOptimization > $timeWithoutOptimization) {
            $slowdown = round(($timeWithOptimization / $timeWithoutOptimization), 2);
            $this->line("🔸 La optimización de imágenes ralentiza: {$slowdown}x");
        } else {
            $speedup = round(($timeWithoutOptimization / $timeWithOptimization), 2);
            $this->line("🔸 La optimización de imágenes acelera: {$speedup}x");
        }
        
        $this->line("");
        $this->info("💡 Recomendación:");
        if ($timeWithOptimization > $timeWithoutOptimization * 1.5) {
            $this->line("🔸 DESHABILITAR optimización de imágenes para mejor rendimiento");
            $this->line("🔸 Configurar: QR_OPTIMIZE_IMAGES=false en .env");
        } else {
            $this->line("🔸 La optimización de imágenes no afecta significativamente el rendimiento");
        }
        
        // Mostrar configuración actual
        $this->line("");
        $this->info("⚙️ Configuración actual:");
        $this->line("🔸 QR_OPTIMIZE_IMAGES: " . (config('qr_optimization.optimize_images') ? 'true' : 'false'));
        $this->line("🔸 QR_CODE_SIZE: " . config('qr_optimization.qr_code.size', 120));
        $this->line("🔸 QR_CODE_MARGIN: " . config('qr_optimization.qr_code.margin', 0));
        $this->line("🔸 QR_BATCH_SIZE: " . config('qr_optimization.performance.batch_size', 100));
    }
}
