<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QrCodeService;

class TestQrSpeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:test-speed {count=50 : Número de QR codes a generar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar velocidad de generación de QR codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count');
        $qrService = new QrCodeService();
        
        // Generar referencias de prueba
        $references = [];
        for ($i = 1; $i <= $count; $i++) {
            $references[] = '000100081758907775' . str_pad($i, 3, '0', STR_PAD_LEFT);
        }
        
        $this->info("Generando {$count} QR codes...");
        
        // Probar método normal
        $startTime = microtime(true);
        $qrService->generateMultipleQrCodes($references);
        $normalTime = microtime(true) - $startTime;
        
        // Limpiar cache
        Cache::flush();
        
        // Probar método ultra-rápido
        $startTime = microtime(true);
        $qrService->generateQrCodesUltraFast($references);
        $ultraTime = microtime(true) - $startTime;
        
        // Mostrar resultados
        $this->info("=== Resultados de Velocidad ===");
        $this->line("Método normal: " . round($normalTime, 2) . " segundos");
        $this->line("Método ultra-rápido: " . round($ultraTime, 2) . " segundos");
        
        if ($ultraTime > 0) {
            $improvement = round((($normalTime - $ultraTime) / $normalTime) * 100, 2);
            $this->line("Mejora: {$improvement}% más rápido");
        }
        
        // Limpiar archivos de prueba
        $qrService->clearOldQrCodes(0);
        
        return 0;
    }
}
