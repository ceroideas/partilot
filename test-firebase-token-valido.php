<?php

/**
 * Test específico para verificar si el token FCM es válido
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
echo "  TEST TOKEN FCM VÁLIDO\n";
echo "========================================\n\n";

try {
    // 1. Inicializar Firebase
    echo "1️⃣  INICIALIZACIÓN\n";
    echo "   ---------------\n";
    
    $credentialsPath = storage_path('firebase-credentials.json');
    $firebase = (new Factory())->withServiceAccount($credentialsPath);
    $messaging = $firebase->createMessaging();
    echo "   ✅ Firebase inicializado\n\n";
    
    // 2. Obtener usuario
    echo "2️⃣  USUARIO Y TOKEN\n";
    echo "   ----------------\n";
    
    $user = User::whereNotNull('fcm_token')->first();
    if (!$user) {
        die("   ❌ No hay usuarios con tokens FCM\n");
    }
    
    echo "   ✅ Usuario: {$user->name}\n";
    echo "   ✅ Email: {$user->email}\n";
    echo "   ✅ Token: " . substr($user->fcm_token, 0, 50) . "...\n";
    echo "   ✅ Longitud token: " . strlen($user->fcm_token) . " caracteres\n\n";
    
    // 3. Crear mensaje MUY simple
    echo "3️⃣  MENSAJE SIMPLE\n";
    echo "   ---------------\n";
    
    $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create('Test', 'Simple'));
    
    echo "   ✅ Mensaje creado (sin datos adicionales)\n\n";
    
    // 4. Intentar enviar
    echo "4️⃣  ENVIANDO\n";
    echo "   ---------\n";
    
    try {
        echo "   ⏳ Enviando mensaje simple...\n";
        $result = $messaging->send($message);
        
        echo "   ✅ ¡ÉXITO!\n";
        echo "   ✅ Message ID: $result\n\n";
        
        echo "🎉 TOKEN FCM VÁLIDO - NOTIFICACIÓN ENVIADA\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
        echo "   ❌ Mensaje inválido\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
        // Intentar con mensaje aún más básico
        echo "   🔄 Intentando con mensaje básico...\n";
        try {
            $basicMessage = CloudMessage::withTarget('token', $user->fcm_token);
            $result = $messaging->send($basicMessage);
            
            echo "   ✅ ¡ÉXITO con mensaje básico!\n";
            echo "   ✅ Message ID: $result\n\n";
            
        } catch (\Exception $e2) {
            echo "   ❌ También falló el mensaje básico\n";
            echo "   Error: {$e2->getMessage()}\n\n";
            
            // Analizar el error específico
            if (strpos($e2->getMessage(), 'invalid_grant') !== false) {
                echo "🔍 DIAGNÓSTICO: invalid_grant\n";
                echo "   📋 POSIBLES CAUSAS:\n";
                echo "   1. Token FCM expirado o inválido\n";
                echo "   2. Problema con las credenciales del servicio\n";
                echo "   3. API de FCM no habilitada correctamente\n";
                echo "   4. Límites de cuota excedidos\n\n";
                
                echo "   🛠️  SOLUCIONES:\n";
                echo "   1. Regenerar token FCM en el frontend\n";
                echo "   2. Verificar que la API esté habilitada\n";
                echo "   3. Probar con un token FCM nuevo\n\n";
            }
        }
        
    } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
        echo "   ❌ Token no encontrado (expirado)\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
        echo "🔍 DIAGNÓSTICO: Token FCM expirado\n";
        echo "   📋 SOLUCIÓN:\n";
        echo "   1. Ir al frontend de la aplicación\n";
        echo "   2. Recargar la página\n";
        echo "   3. Aceptar notificaciones nuevamente\n";
        echo "   4. Esto generará un nuevo token FCM\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
        echo "   Tipo: " . get_class($e) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error crítico: {$e->getMessage()}\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "TEST COMPLETADO\n";
echo str_repeat("=", 40) . "\n";
