<?php

/**
 * Script para verificar tokens FCM actuales
 */

echo "========================================\n";
echo "  VERIFICADOR DE TOKENS FCM\n";
echo "========================================\n\n";

try {
    // Bootstrap Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // 1. Verificar usuarios con tokens
    echo "1️⃣  USUARIOS CON TOKENS FCM\n";
    echo "   ------------------------\n";
    
    $users = \App\Models\User::whereNotNull('fcm_token')->get();
    
    if ($users->count() > 0) {
        echo "   ✅ Encontrados {$users->count()} usuario(s) con tokens:\n\n";
        
        foreach ($users as $user) {
            $token = $user->fcm_token;
            $tokenPreview = substr($token, 0, 20) . '...' . substr($token, -10);
            echo "   👤 {$user->name} ({$user->email})\n";
            echo "      Token: $tokenPreview\n";
            echo "      Longitud: " . strlen($token) . " caracteres\n\n";
        }
    } else {
        echo "   ❌ No hay usuarios con tokens FCM\n";
        echo "   📋 Necesitas regenerar los tokens en el navegador\n\n";
    }
    
    // 2. Estadísticas
    echo "2️⃣  ESTADÍSTICAS\n";
    echo "   -------------\n";
    echo "   📊 Total usuarios: " . \App\Models\User::count() . "\n";
    echo "   📊 Con tokens FCM: " . \App\Models\User::whereNotNull('fcm_token')->count() . "\n";
    echo "   📊 Sin tokens FCM: " . \App\Models\User::whereNull('fcm_token')->count() . "\n\n";
    
    // 3. Próximo paso
    if ($users->count() > 0) {
        echo "🎯 PRÓXIMO PASO:\n";
        echo "   Ejecuta: php test-firebase-real.php\n\n";
    } else {
        echo "🎯 PRÓXIMO PASO:\n";
        echo "   1. Ve al navegador y regenera el token\n";
        echo "   2. Ejecuta: php verificar-tokens.php\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo str_repeat("=", 40) . "\n";
echo "VERIFICACIÓN COMPLETADA\n";
echo str_repeat("=", 40) . "\n";
