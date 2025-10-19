<?php
/**
 * Script para verificar la configuración de Firebase
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "========================================\n";
echo "  VERIFICACIÓN DE CONFIGURACIÓN FIREBASE\n";
echo "========================================\n\n";

// 1. Verificar configuración básica
echo "1️⃣  CONFIGURACIÓN BÁSICA\n";
echo "   ------------------\n";

$config = config('firebase');

echo "   Project ID: " . ($config['project_id'] ?? '❌ NO CONFIGURADO') . "\n";
echo "   Messaging Sender ID: " . ($config['messaging_sender_id'] ?? '❌ NO CONFIGURADO') . "\n";
echo "   App ID: " . (isset($config['app_id']) ? '✓ Configurado' : '❌ NO CONFIGURADO') . "\n";
echo "   Server Key: " . (isset($config['server_key']) && !empty($config['server_key']) ? '✓ Configurado (' . strlen($config['server_key']) . ' caracteres)' : '❌ NO CONFIGURADO') . "\n";

// 2. Verificar tokens en base de datos
echo "\n2️⃣  TOKENS EN BASE DE DATOS\n";
echo "   ---------------------\n";

$users = \App\Models\User::whereNotNull('fcm_token')->get();

if ($users->isEmpty()) {
    echo "   ❌ No hay usuarios con tokens FCM\n";
} else {
    echo "   ✓ {$users->count()} usuario(s) con tokens:\n";
    foreach ($users as $user) {
        $tokenPrefix = substr($user->fcm_token, 0, 30);
        echo "      - {$user->name} ({$user->email}): {$tokenPrefix}...\n";
    }
}

// 3. Verificar el formato del Server Key
echo "\n3️⃣  VERIFICACIÓN DEL SERVER KEY\n";
echo "   --------------------------\n";

if (isset($config['server_key']) && !empty($config['server_key'])) {
    $serverKey = $config['server_key'];
    
    // El Server Key legacy de Firebase suele empezar con "AAAA" y tener ~150-180 caracteres
    if (strlen($serverKey) < 100) {
        echo "   ⚠️  El Server Key parece muy corto (" . strlen($serverKey) . " caracteres)\n";
        echo "       Los Server Keys de Firebase suelen tener 150-180 caracteres\n";
    } elseif (strpos($serverKey, 'AAAA') === 0) {
        echo "   ✓ El formato parece correcto (comienza con AAAA)\n";
        echo "   ✓ Longitud: " . strlen($serverKey) . " caracteres\n";
    } else {
        echo "   ⚠️  El Server Key no comienza con 'AAAA'\n";
        echo "       Esto podría indicar un formato incorrecto\n";
    }
    
    echo "   Primeros 20 caracteres: " . substr($serverKey, 0, 20) . "...\n";
} else {
    echo "   ❌ Server Key no configurado\n";
}

// 4. Verificar el project ID del token
echo "\n4️⃣  VERIFICACIÓN DE COINCIDENCIA\n";
echo "   ----------------------------\n";

if ($users->isNotEmpty() && isset($config['messaging_sender_id'])) {
    $testToken = $users->first()->fcm_token;
    
    echo "   Token de prueba: " . substr($testToken, 0, 40) . "...\n";
    echo "   Sender ID en config: " . $config['messaging_sender_id'] . "\n";
    
    // Los tokens FCM tienen el formato: [random]:[random]
    // Y están asociados a un proyecto específico
    
    echo "\n   ⚠️  IMPORTANTE: Verifica que el Server Key sea del MISMO proyecto\n";
    echo "       que generó este token FCM.\n";
}

// 5. Instrucciones
echo "\n5️⃣  INSTRUCCIONES PARA OBTENER EL SERVER KEY CORRECTO\n";
echo "   -----------------------------------------------\n";
echo "   1. Ve a: https://console.firebase.google.com/\n";
echo "   2. Selecciona tu proyecto: 'inicio-de-sesion-94ddc'\n";
echo "   3. Haz clic en el ⚙️ (configuración) → Project Settings\n";
echo "   4. Ve a la pestaña: Cloud Messaging\n";
echo "   5. Busca la sección: 'Cloud Messaging API (Legacy)'\n";
echo "   6. Copia el 'Server key'\n";
echo "   7. Pégalo en tu .env como: FIREBASE_SERVER_KEY=...\n\n";

echo "   📝 NOTA IMPORTANTE:\n";
echo "   Si la API Legacy está deshabilitada, necesitarás habilitarla:\n";
echo "   - En Firebase Console → Cloud Messaging\n";
echo "   - Busca 'Cloud Messaging API (Legacy)'\n";
echo "   - Habilita la API si está deshabilitada\n\n";

// 6. Test de conectividad
echo "\n6️⃣  TEST DE CONECTIVIDAD A FIREBASE\n";
echo "   ------------------------------\n";

if (isset($config['server_key']) && !empty($config['server_key']) && $users->isNotEmpty()) {
    echo "   Intentando enviar notificación de prueba...\n\n";
    
    $testUser = $users->first();
    $firebaseService = new \App\Services\FirebaseService();
    
    try {
        $result = $firebaseService->sendToDevice(
            $testUser->fcm_token,
            '🧪 Test de Verificación',
            'Este es un test de configuración de Firebase',
            ['test' => true]
        );
        
        if ($result) {
            echo "   ✅ ¡ÉXITO! La notificación se envió correctamente\n";
            echo "   → Revisa tu dispositivo/navegador\n";
        } else {
            echo "   ❌ Error al enviar la notificación\n";
            echo "   → Revisa storage/logs/laravel.log para más detalles\n";
            echo "\n   El error 404 indica que:\n";
            echo "   • El Server Key no es del proyecto correcto\n";
            echo "   • La Cloud Messaging API (Legacy) está deshabilitada\n";
            echo "   • El proyecto de Firebase no existe o fue eliminado\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Excepción: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⏭️  Omitiendo test (falta configuración o tokens)\n";
}

echo "\n========================================\n";
echo "  FIN DE LA VERIFICACIÓN\n";
echo "========================================\n\n";

