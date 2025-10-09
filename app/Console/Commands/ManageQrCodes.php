<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\File;

class ManageQrCodes extends Command
{
    protected $signature = 'qr:manage {action=stats} {--hours=24}';
    protected $description = 'Gestionar QR codes guardados (stats, clear, test)';

    public function handle()
    {
        $action = $this->argument('action');
        $qrService = new QrCodeService();

        switch ($action) {
            case 'stats':
                $this->showStats($qrService);
                break;
            case 'clear':
                $this->clearQrCodes($qrService);
                break;
            case 'test':
                $this->testQrGeneration($qrService);
                break;
            default:
                $this->error('Acción no válida. Use: stats, clear, test');
        }
    }

    private function showStats($qrService)
    {
        $this->info('📊 Estadísticas de QR Codes:');
        $this->line('');

        $qrDir = storage_path('app/qr_codes');
        if (!is_dir($qrDir)) {
            $this->warn('Directorio de QR codes no existe');
            return;
        }

        $files = File::allFiles($qrDir);
        $totalFiles = count($files);
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        $this->line("📁 Total de archivos: {$totalFiles}");
        $this->line("💾 Tamaño total: " . $this->formatBytes($totalSize));
        $this->line("📂 Directorio: {$qrDir}");

        if ($totalFiles > 0) {
            $avgSize = $totalSize / $totalFiles;
            $this->line("📏 Tamaño promedio: " . $this->formatBytes($avgSize));
        }
    }

    private function clearQrCodes($qrService)
    {
        $this->info('🧹 Limpiando QR codes...');
        
        $deleted = $qrService->clearOldQrCodes(0);
        
        $this->info("✅ Eliminados {$deleted} archivos QR");
    }

    private function testQrGeneration($qrService)
    {
        $this->info('🧪 Probando generación de QR codes...');
        
        $testReferences = [
            '000100111759087003001',
            '000100111759087003002',
            '000100111759087003003',
            '000100111759087003004',
            '000100111759087003005'
        ];

        // Limpiar QR codes existentes para prueba limpia
        $this->line("🧹 Limpiando QR codes existentes...");
        $qrService->clearOldQrCodes(0);

        $this->line("📊 Probando generación individual...");
        $startTime = microtime(true);
        
        foreach ($testReferences as $ref) {
            $base64 = $qrService->generateQrCodeBase64($ref);
            $this->line("✅ Generado: {$ref} (tamaño: " . strlen($base64) . " bytes)");
        }
        
        $endTime = microtime(true);
        $individualTime = round(($endTime - $startTime) * 1000, 2);
        
        $this->line("");
        $this->line("📊 Probando generación en lote...");
        $startTime = microtime(true);
        
        $batchResults = $qrService->generateMultipleQrCodes($testReferences);
        
        $endTime = microtime(true);
        $batchTime = round(($endTime - $startTime) * 1000, 2);
        
        $this->line("");
        $this->info("📈 Resultados de la prueba:");
        $this->line("🔸 Generación individual: {$individualTime}ms");
        $this->line("🔸 Generación en lote: {$batchTime}ms");
        $this->line("🔸 Mejora: " . round(($individualTime / $batchTime), 2) . "x más rápido");
        $this->line("🔸 Promedio individual: " . round($individualTime / count($testReferences), 2) . "ms por QR");
        $this->line("🔸 Promedio lote: " . round($batchTime / count($testReferences), 2) . "ms por QR");
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