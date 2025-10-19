<?php
/**
 * Script de prueba para enviar notificaciones Firebase
 * 
 * Uso: php test-firebase-notification.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseService;
use App\Models\User;

echo "\n=== TEST DE NOTIFICACIONES FIREBASE ===\n\n";

// 1. Verificar configuración
echo "1. Verificando configuración de Firebase...\n";
$config = config('firebase');
echo "   - API Key: " . (isset($config['api_key']) ? '✓ Configurado' : '✗ No configurado') . "\n";
echo "   - Project ID: " . ($config['project_id'] ?? 'No configurado') . "\n";
echo "   - Server Key: " . (isset($config['server_key']) && !empty($config['server_key']) ? '✓ Configurado' : '✗ No configurado') . "\n";

if (!isset($config['server_key']) || empty($config['server_key'])) {
    echo "\n⚠️  ADVERTENCIA: Server Key no configurado en .env\n";
    echo "   Agrega: FIREBASE_SERVER_KEY=tu-server-key\n\n";
}

// 2. Verificar usuarios con tokens
echo "\n2. Verificando usuarios con tokens FCM...\n";
$usersWithTokens = User::whereNotNull('fcm_token')->get();

if ($usersWithTokens->isEmpty()) {
    echo "   ✗ No hay usuarios con tokens FCM registrados\n";
    echo "   → Abre la aplicación en el navegador para registrar un token\n\n";
    exit(1);
}

echo "   ✓ Encontrados " . $usersWithTokens->count() . " usuario(s) con tokens:\n";
foreach ($usersWithTokens as $user) {
    $tokenPreview = substr($user->fcm_token, 0, 50) . '...';
    echo "      - {$user->name} ({$user->email}): {$tokenPreview}\n";
}

// 3. Seleccionar usuario para prueba
$testUser = $usersWithTokens->first();
echo "\n3. Usuario de prueba seleccionado: {$testUser->name} ({$testUser->email})\n";

// 4. Verificar server key antes de enviar
if (!isset($config['server_key']) || empty($config['server_key'])) {
    echo "\n✗ No se puede enviar notificación sin Server Key\n";
    echo "   Configura FIREBASE_SERVER_KEY en tu archivo .env\n\n";
    exit(1);
}

// 5. Enviar notificación de prueba
echo "\n4. Enviando notificación de prueba...\n";

$firebaseService = new FirebaseService();

try {
    $result = $firebaseService->sendToDevice(
        $testUser->fcm_token,
        '🔔 Prueba de Notificación',
        'Esta es una notificación de prueba desde Partilot',
        [
            'test' => 'true',
            'timestamp' => now()->toIso8601String(),
            'user_id' => $testUser->id
        ]
    );

    if ($result) {
        echo "   ✓ Notificación enviada exitosamente\n";
        echo "   → Revisa tu navegador para ver la notificación\n";
    } else {
        echo "   ✗ Error al enviar la notificación\n";
        echo "   → Revisa storage/logs/laravel.log para más detalles\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Excepción al enviar notificación: " . $e->getMessage() . "\n";
    echo "   → Revisa storage/logs/laravel.log para más detalles\n";
}

echo "\n=== FIN DEL TEST ===\n\n";

