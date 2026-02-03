<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageHelper
{
    /**
     * Guardar imagen temporalmente en sesión para persistencia en errores de validación
     */
    public static function saveTemporaryImage(UploadedFile $file, string $sessionKey): string
    {
        $filename = 'temp_' . time() . '_' . $file->hashName();
        $path = $file->storeAs('temp', $filename, 'public');
        
        session([$sessionKey => $path]);
        
        return $path;
    }

    /**
     * Obtener ruta de imagen temporal desde sesión
     */
    public static function getTemporaryImage(string $sessionKey): ?string
    {
        $path = session($sessionKey);
        
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
        
        return null;
    }

    /**
     * Limpiar imagen temporal de sesión
     */
    public static function clearTemporaryImage(string $sessionKey): void
    {
        $path = session($sessionKey);
        
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        
        session()->forget($sessionKey);
    }

    /**
     * Mover imagen temporal a ubicación final
     */
    public static function moveTemporaryImage(string $sessionKey, string $destinationPath): ?string
    {
        $tempPath = session($sessionKey);
        
        if (!$tempPath || !Storage::disk('public')->exists($tempPath)) {
            return null;
        }
        
        $filename = basename($tempPath);
        $finalPath = $destinationPath . '/' . str_replace('temp_', '', $filename);
        
        Storage::disk('public')->move($tempPath, $finalPath);
        session()->forget($sessionKey);
        
        return $finalPath;
    }
}
