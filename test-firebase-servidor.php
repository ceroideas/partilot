<?php

/**
 * Test simplificado para servidor de producción
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

echo "========================================\n";
echo "  TEST FIREBASE EN SERVIDOR\n";
echo "========================================\n\n";

try {
    // 1. Verificar fecha del servidor
    echo "1️⃣  FECHA DEL SERVIDOR\n";
    echo "   -------------------\n";
    $fecha = date('Y-m-d H:i:s');
    echo "   Fecha actual: $fecha\n";
    
    // Verificar si la fecha es correcta (no debe ser octubre 2025)
    if (strpos($fecha, '2025-10-19') !== false) {
        echo "   ⚠️  PROBLEMA: Fecha incorrecta (futuro)\n";
        echo "   🔧 SOLUCIÓN: Ejecutar 'sudo ntpdate -s time.nist.gov'\n\n";
    } else {
        echo "   ✅ Fecha correcta\n\n";
    }
    
    // 2. Inicializar Firebase
    echo "2️⃣  INICIALIZACIÓN FIREBASE\n";
    echo "   -------------------------\n";
    
    $credentialsPath = storage_path('firebase-credentials.json');
    $firebase = (new Factory())->withServiceAccount($credentialsPath);
    $messaging = $firebase->createMessaging();
    echo "   ✅ Firebase inicializado correctamente\n\n";
    
    // 3. Obtener usuario
    echo "3️⃣  USUARIO CON TOKEN\n";
    echo "   ------------------\n";
    
    $user = User::whereNotNull('fcm_token')->first();
    if (!$user) {
        die("   ❌ No hay usuarios con tokens FCM\n");
    }
    
    echo "   ✅ Usuario: {$user->name}\n";
    echo "   ✅ Token: " . substr($user->fcm_token, 0, 50) . "...\n\n";
    
    // 4. Crear y enviar mensaje
    echo "4️⃣  ENVIANDO NOTIFICACIÓN\n";
    echo "   ----------------------\n";
    
    $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create(
            '🚀 Test Servidor',
            'Notificación desde servidor - ' . date('H:i:s')
        ));
    
    echo "   ⏳ Enviando...\n";
    
    try {
        $result = $messaging->send($message);
        
        echo "   ✅ ¡ÉXITO TOTAL!\n";
        echo "   ✅ Message ID: $result\n";
        echo "   ✅ Timestamp: " . date('Y-m-d H:i:s') . "\n\n";
        
        echo "🎉 NOTIFICACIÓN ENVIADA CORRECTAMENTE\n";
        echo "Firebase API V1 funciona perfectamente en el servidor.\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
        echo "   ❌ ERROR DE AUTENTICACIÓN\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
        if (strpos($e->getMessage(), 'invalid_grant') !== false) {
            echo "🔧 DIAGNÓSTICO: Error 'invalid_grant'\n";
            echo "   📋 CAUSAS POSIBLES:\n";
            echo "   1. Reloj del servidor desincronizado (MÁS PROBABLE)\n";
            echo "   2. Credenciales incorrectas\n";
            echo "   3. API no habilitada\n\n";
            
            echo "   🛠️  SOLUCIONES:\n";
            echo "   1. Sincronizar reloj: sudo ntpdate -s time.nist.gov\n";
            echo "   2. Verificar fecha: date\n";
            echo "   3. Si persiste, regenerar credenciales\n\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
        echo "   Tipo: " . get_class($e) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR CRÍTICO: {$e->getMessage()}\n";
    exit(1);
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "TEST COMPLETADO\n";
echo str_repeat("=", 40) . "\n";
