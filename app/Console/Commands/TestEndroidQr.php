<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EndroidQrCodeService;

class TestEndroidQr extends Command
{
    protected $signature = 'qr:endroid-test {count=100}';
    protected $description = 'Probar generación ultra-rápida con Endroid QR Code';

    public function handle()
    {
        $count = (int) $this->argument('count');
        
        $this->info("🚀 Probando Endroid QR Code con {$count} QR codes...");
        $this->line("");

        // Generar referencias de prueba
        $references = [];
        for ($i = 1; $i <= $count; $i++) {
            $references[] = '000100111759087003' . str_pad($i, 3, '0', STR_PAD_LEFT);
        }

        // Limpiar cache
        $this->line("🧹 Limpiando cache...");
        $qrService = new EndroidQrCodeService();
        $qrService->clearQrCache();

        // Probar generación individual
        $this->line("📊 Probando generación individual...");
        $startTime = microtime(true);
        
        foreach ($references as $ref) {
            $base64 = $qrService->generateQrCodeBase64($ref);
        }
        
        $endTime = microtime(true);
        $individualTime = round(($endTime - $startTime) * 1000, 2);
        
        // Limpiar cache para prueba limpia
        $qrService->clearQrCache();
        
        // Probar generación en lote
        $this->line("📊 Probando generación en lote...");
        $startTime = microtime(true);
        
        $results = $qrService->generateMultipleQrCodes($references);
        
        $endTime = microtime(true);
        $batchTime = round(($endTime - $startTime) * 1000, 2);
        
        // Limpiar cache para prueba ultra-rápida
        $qrService->clearQrCache();
        
        // Probar generación ultra-rápida
        $this->line("📊 Probando generación ultra-rápida...");
        $startTime = microtime(true);
        
        $ultraResults = $qrService->generateUltraFastQrCodes($references);
        
        $endTime = microtime(true);
        $ultraTime = round(($endTime - $startTime) * 1000, 2);
        
        $this->line("");
        $this->info("📈 Resultados de la prueba Endroid:");
        $this->line("🔸 Total QR codes: {$count}");
        $this->line("🔸 Generación individual: {$individualTime}ms");
        $this->line("🔸 Generación en lote: {$batchTime}ms");
        $this->line("🔸 Generación ultra-rápida: {$ultraTime}ms");
        $this->line("");
        $this->line("🔸 Promedio individual: " . round($individualTime / $count, 2) . "ms por QR");
        $this->line("🔸 Promedio lote: " . round($batchTime / $count, 2) . "ms por QR");
        $this->line("🔸 Promedio ultra-rápido: " . round($ultraTime / $count, 2) . "ms por QR");
        $this->line("");
        $this->line("🔸 QR codes/seg individual: " . round($count / ($individualTime / 1000), 2));
        $this->line("🔸 QR codes/seg lote: " . round($count / ($batchTime / 1000), 2));
        $this->line("🔸 QR codes/seg ultra-rápido: " . round($count / ($ultraTime / 1000), 2));
        
        // Mejoras
        $this->line("");
        $this->info("📊 Mejoras de rendimiento:");
        $this->line("🔸 Lote vs Individual: " . round($individualTime / $batchTime, 2) . "x más rápido");
        $this->line("🔸 Ultra vs Individual: " . round($individualTime / $ultraTime, 2) . "x más rápido");
        $this->line("🔸 Ultra vs Lote: " . round($batchTime / $ultraTime, 2) . "x más rápido");
        
        // Estimaciones
        $this->line("");
        $this->info("📊 Estimaciones para otras cantidades:");
        $this->line("🔸 500 QR codes (ultra): ~" . round(($ultraTime / $count) * 500 / 1000, 1) . " segundos");
        $this->line("🔸 1000 QR codes (ultra): ~" . round(($ultraTime / $count) * 1000 / 1000, 1) . " segundos");
        $this->line("🔸 2000 QR codes (ultra): ~" . round(($ultraTime / $count) * 2000 / 1000, 1) . " segundos");
    }
}
