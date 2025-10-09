<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QrCodeService;

class TestUltraFastQr extends Command
{
    protected $signature = 'qr:ultra-test {count=1000}';
    protected $description = 'Probar generación ultra-rápida de QR codes';

    public function handle()
    {
        $count = (int) $this->argument('count');
        
        $this->info("🚀 Probando generación ultra-rápida de {$count} QR codes...");
        $this->line("");

        // Generar referencias de prueba con estructura similar
        $references = [];
        for ($i = 1; $i <= $count; $i++) {
            $references[] = '000100111759087003' . str_pad($i, 3, '0', STR_PAD_LEFT);
        }

        // Limpiar QR codes existentes
        $this->line("🧹 Limpiando QR codes existentes...");
        $qrService = new QrCodeService();
        $qrService->clearOldQrCodes(0);

        // Probar generación ultra-rápida
        $this->line("⚡ Iniciando generación ultra-rápida...");
        $startTime = microtime(true);
        
        $results = $qrService->generateMultipleQrCodes($references);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        $this->line("");
        $this->info("📊 Resultados de la prueba ultra-rápida:");
        $this->line("🔸 Total QR codes: {$count}");
        $this->line("🔸 Tiempo total: {$duration}ms");
        $this->line("🔸 Promedio por QR: " . round($duration / $count, 2) . "ms");
        $this->line("🔸 QR codes por segundo: " . round($count / ($duration / 1000), 2));
        
        // Mostrar estadísticas
        $stats = $qrService->getQrCodeStats();
        $this->line("");
        $this->info("📈 Estadísticas finales:");
        $this->line("🔸 Archivos generados: {$stats['total_files']}");
        $this->line("🔸 Tamaño total: " . $this->formatBytes($stats['total_size']));
        $this->line("🔸 Tamaño promedio: " . $this->formatBytes($stats['average_size']));
        
        // Estimación para diferentes cantidades
        $this->line("");
        $this->info("📊 Estimaciones para otras cantidades:");
        $this->line("🔸 500 QR codes: ~" . round(($duration / $count) * 500 / 1000, 1) . " segundos");
        $this->line("🔸 2000 QR codes: ~" . round(($duration / $count) * 2000 / 1000, 1) . " segundos");
        $this->line("🔸 5000 QR codes: ~" . round(($duration / $count) * 5000 / 1000, 1) . " segundos");
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
