<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Cache;

class EndroidQrCodeService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = url('comprobar-participacion?ref=');
    }

    /**
     * Generar QR code como base64 (optimizado con Endroid)
     */
    public function generateQrCodeBase64($reference)
    {
        // Cache simple para evitar regenerar
        $cacheKey = 'endroid_qr_base64_' . md5($reference);
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            return $cached;
        }
        
        $url = $this->baseUrl . $reference;
        
        // Crear QR code con Endroid (mucho más rápido)
        $qrCode = QrCode::create($url)
            ->setSize(200)
            ->setMargin(2);
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
        
        // Cache por 30 minutos
        Cache::put($cacheKey, $base64, 1800);
        
        return $base64;
    }

    /**
     * Generar múltiples QR codes en lote (ultra-optimizado con Endroid - solo memoria)
     */
    public function generateMultipleQrCodes($references)
    {
        $results = [];
        $toGenerate = [];
        
        // Verificar cuáles ya están en cache
        foreach ($references as $reference) {
            $cacheKey = 'endroid_qr_base64_' . md5($reference);
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                $results[$reference] = $cached;
            } else {
                $toGenerate[] = $reference;
            }
        }
        
        // Generar los que no están en cache (solo en memoria, sin archivos)
        if (!empty($toGenerate)) {
            $batchResults = $this->generateUltraFastBatchInMemory($toGenerate);
            $results = array_merge($results, $batchResults);
        }
        
        return $results;
    }

    /**
     * Generación ultra-rápida con Endroid (solo memoria, sin archivos)
     */
    private function generateUltraFastBatchInMemory($references)
    {
        $results = [];
        $baseUrl = $this->baseUrl;
        
        // Configuración ultra-optimizada para máxima velocidad
        $qrCode = QrCode::create('')
            ->setSize(config('qr_optimization.qr_code.size', 120))
            ->setMargin(config('qr_optimization.qr_code.margin', 0));
        
        $writer = new PngWriter();
        
        foreach ($references as $reference) {
            $url = $baseUrl . $reference;
            
            // Actualizar solo la URL (más eficiente)
            $qrCode = $qrCode->setData($url);
            $result = $writer->write($qrCode);
            
            $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
            
            // Cache inmediato (solo en memoria)
            $cacheKey = 'endroid_qr_base64_' . md5($reference);
            Cache::put($cacheKey, $base64, 1800);
            
            $results[$reference] = $base64;
        }
        
        return $results;
    }

    /**
     * Generación ultra-rápida con Endroid (versión con archivos - mantenida para compatibilidad)
     */
    private function generateUltraFastBatch($references)
    {
        $results = [];
        $baseUrl = $this->baseUrl;
        
        // Configuración ultra-optimizada
        $qrCode = QrCode::create('')
            ->setSize(150) // Tamaño optimizado
            ->setMargin(1); // Margen mínimo
        
        $writer = new PngWriter();
        
        foreach ($references as $reference) {
            $url = $baseUrl . $reference;
            
            // Actualizar solo la URL (más eficiente)
            $qrCode = $qrCode->setData($url);
            $result = $writer->write($qrCode);
            
            $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
            
            // Cache inmediato
            $cacheKey = 'endroid_qr_base64_' . md5($reference);
            Cache::put($cacheKey, $base64, 1800);
            
            $results[$reference] = $base64;
        }
        
        return $results;
    }

    /**
     * Generación ultra-rápida con configuración mínima (solo memoria)
     */
    public function generateUltraFastQrCodes($references)
    {
        $results = [];
        $baseUrl = $this->baseUrl;
        
        // Configuración ultra-optimizada para máxima velocidad
        $qrCode = QrCode::create('')
            ->setSize(100) // Tamaño mínimo
            ->setMargin(0); // Sin margen
        
        $writer = new PngWriter();
        
        // Procesar en lotes para mejor gestión de memoria
        $batchSize = 100; // Lotes más grandes para mejor eficiencia
        $batches = array_chunk($references, $batchSize);
        
        foreach ($batches as $batchIndex => $batch) {
            error_log("Endroid QR: Procesando lote " . ($batchIndex + 1) . " de " . count($batches) . " (" . count($batch) . " QR codes)");
            
            foreach ($batch as $reference) {
                $url = $baseUrl . $reference;
                
                // Actualizar solo la URL
                $qrCode = $qrCode->setData($url);
                $result = $writer->write($qrCode);
                
                $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
                
                // Cache inmediato (solo en memoria)
                $cacheKey = 'endroid_qr_base64_' . md5($reference);
                Cache::put($cacheKey, $base64, 1800);
                
                $results[$reference] = $base64;
            }
        }
        
        return $results;
    }

    /**
     * Limpiar cache de QR codes
     */
    public function clearQrCache()
    {
        // Limpiar cache de Endroid QR codes
        $keys = Cache::get('endroid_qr_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('endroid_qr_keys');
    }

    /**
     * Obtener estadísticas de cache
     */
    public function getCacheStats()
    {
        return [
            'cache_driver' => config('cache.default'),
            'cache_prefix' => 'endroid_qr_base64_',
            'cache_ttl' => '30 minutos'
        ];
    }
}
