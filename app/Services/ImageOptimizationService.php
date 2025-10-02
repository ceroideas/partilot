<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    private $cacheDir;
    private $maxImageSize = 1920; // Máximo ancho/alto para redimensionar
    private $quality = 85; // Calidad JPEG (1-100)

    public function __construct()
    {
        $this->cacheDir = storage_path('app/optimized_images');
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Optimizar todas las imágenes en un HTML
     */
    public function optimizeHtmlImages($html)
    {
        // Detectar todas las imágenes
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        $images = array_unique($matches[1]);
        
        if (empty($images)) {
            return $html;
        }

        $optimizedImages = [];
        
        foreach ($images as $imagePath) {
            $optimizedPath = $this->optimizeImage($imagePath);
            if ($optimizedPath) {
                $optimizedImages[$imagePath] = $optimizedPath;
            }
        }

        // Reemplazar todas las referencias
        foreach ($optimizedImages as $original => $optimized) {
            $html = str_replace($original, $optimized, $html);
        }

        return $html;
    }

    /**
     * Optimizar una imagen individual
     */
    public function optimizeImage($imagePath)
    {
        $fullPath = $this->getImageFullPath($imagePath);
        
        if (!file_exists($fullPath)) {
            return $imagePath;
        }

        // Generar hash del archivo para cache
        $fileHash = md5_file($fullPath);
        $optimizedPath = $this->cacheDir . '/' . $fileHash . '.jpg';
        
        // Si ya existe la versión optimizada, devolverla
        if (file_exists($optimizedPath)) {
            return $optimizedPath;
        }

        // Optimizar la imagen
        if ($this->compressAndResizeImage($fullPath, $optimizedPath)) {
            return $optimizedPath;
        }

        return $imagePath;
    }

    /**
     * Obtener la ruta completa de una imagen
     */
    private function getImageFullPath($imagePath)
    {
        // URL completa
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Ruta absoluta
        if (strpos($imagePath, public_path()) === 0) {
            return $imagePath;
        }
        
        // Ruta relativa desde public
        if (strpos($imagePath, '/') === 0) {
            return public_path() . $imagePath;
        }
        
        return public_path() . '/' . ltrim($imagePath, '/');
    }

    /**
     * Comprimir y redimensionar imagen
     */
    private function compressAndResizeImage($sourcePath, $destinationPath)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Cargar imagen según su tipo
        $sourceImage = $this->loadImageByType($sourcePath, $mimeType);
        if (!$sourceImage) {
            return false;
        }

        // Calcular nuevas dimensiones manteniendo proporción
        $newDimensions = $this->calculateNewDimensions($originalWidth, $originalHeight);
        
        // Crear imagen redimensionada
        $resizedImage = imagecreatetruecolor($newDimensions['width'], $newDimensions['height']);
        
        // Preservar transparencia para PNG
        if ($mimeType === 'image/png') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
        }

        // Redimensionar
        imagecopyresampled(
            $resizedImage, $sourceImage,
            0, 0, 0, 0,
            $newDimensions['width'], $newDimensions['height'],
            $originalWidth, $originalHeight
        );

        // Guardar como JPEG optimizado
        $result = imagejpeg($resizedImage, $destinationPath, $this->quality);
        
        // Limpiar memoria
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $result;
    }

    /**
     * Cargar imagen según su tipo MIME
     */
    private function loadImageByType($path, $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Calcular nuevas dimensiones manteniendo proporción
     */
    private function calculateNewDimensions($originalWidth, $originalHeight)
    {
        // Si la imagen ya es pequeña, no redimensionar
        if ($originalWidth <= $this->maxImageSize && $originalHeight <= $this->maxImageSize) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight
            ];
        }

        // Calcular proporción
        $ratio = min($this->maxImageSize / $originalWidth, $this->maxImageSize / $originalHeight);
        
        return [
            'width' => (int)($originalWidth * $ratio),
            'height' => (int)($originalHeight * $ratio)
        ];
    }

    /**
     * Limpiar cache de imágenes optimizadas
     */
    public function clearOptimizedImages()
    {
        $files = glob($this->cacheDir . '/*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }

    /**
     * Obtener estadísticas de optimización
     */
    public function getOptimizationStats()
    {
        $files = glob($this->cacheDir . '/*');
        $totalSize = 0;
        $fileCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $fileCount++;
            }
        }
        
        return [
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
}
