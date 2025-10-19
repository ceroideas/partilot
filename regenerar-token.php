<?php

/**
 * Script para regenerar token FCM y limpiar tokens expirados
 */

echo "========================================\n";
echo "  REGENERADOR DE TOKENS FCM\n";
echo "========================================\n\n";

try {
    // 1. Limpiar tokens FCM existentes
    echo "1️⃣  LIMPIANDO TOKENS EXISTENTES\n";
    echo "   ----------------------------\n";
    
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Limpiar tokens FCM
    $updated = \App\Models\User::whereNotNull('fcm_token')->update(['fcm_token' => null]);
    
    echo "   ✅ Tokens FCM limpiados: $updated usuarios\n\n";
    
    // 2. Instrucciones para regenerar
    echo "2️⃣  INSTRUCCIONES PARA REGENERAR\n";
    echo "   ------------------------------\n";
    echo "   1. Ve a: https://ceroideas.es/partilot/public/notifications\n";
    echo "   2. Abre F12 (DevTools)\n";
    echo "   3. En la consola ejecuta:\n";
    echo "      firebaseNotifications.requestPermissionAndGetToken()\n";
    echo "   4. O simplemente recarga la página y acepta las notificaciones\n\n";
    
    // 3. Verificar después de regenerar
    echo "3️⃣  VERIFICAR DESPUÉS DE REGENERAR\n";
    echo "   ---------------------------------\n";
    echo "   Ejecuta: php verificar-tokens.php\n\n";
    
    echo "🎯 PRÓXIMO PASO:\n";
    echo "   1. Regenera el token en el navegador\n";
    echo "   2. Ejecuta: php verificar-tokens.php\n";
    echo "   3. Ejecuta: php test-firebase-real.php\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo str_repeat("=", 40) . "\n";
echo "REGENERACIÓN INICIADA\n";
echo str_repeat("=", 40) . "\n";
