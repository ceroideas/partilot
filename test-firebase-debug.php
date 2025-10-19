<?php

/**
 * Script de depuración detallada de Firebase API V1
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
echo "  DEBUG FIREBASE API V1\n";
echo "========================================\n\n";

try {
    // 1. Verificar credenciales
    echo "1️⃣  CREDENCIALES\n";
    echo "   -------------\n";
    
    $credentialsPath = storage_path('firebase-credentials.json');
    if (!file_exists($credentialsPath)) {
        die("❌ No existe: $credentialsPath\n");
    }
    
    $cred = json_decode(file_get_contents($credentialsPath), true);
    echo "   ✓ Project ID: {$cred['project_id']}\n";
    echo "   ✓ Client Email: {$cred['client_email']}\n\n";
    
    // 2. Inicializar Firebase
    echo "2️⃣  INICIALIZACIÓN\n";
    echo "   ---------------\n";
    
    $firebase = (new Factory())
        ->withServiceAccount($credentialsPath);
    
    $messaging = $firebase->createMessaging();
    echo "   ✓ Messaging creado\n\n";
    
    // 3. Obtener usuario con token
    echo "3️⃣  USUARIO CON TOKEN\n";
    echo "   ------------------\n";
    
    $user = User::whereNotNull('fcm_token')->first();
    
    if (!$user) {
        die("   ❌ No hay usuarios con tokens\n");
    }
    
    echo "   ✓ Usuario: {$user->name}\n";
    echo "   ✓ Email: {$user->email}\n";
    echo "   ✓ Token: " . substr($user->fcm_token, 0, 50) . "...\n\n";
    
    // 4. Crear mensaje
    echo "4️⃣  CREAR MENSAJE\n";
    echo "   --------------\n";
    
    $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create(
            '🎯 Debug Test',
            'Mensaje de prueba detallado - ' . date('H:i:s')
        ))
        ->withData([
            'test_id' => uniqid('debug_'),
            'timestamp' => time()
        ]);
    
    echo "   ✓ Mensaje creado\n\n";
    
    // 5. Enviar
    echo "5️⃣  ENVIANDO...\n";
    echo "   -----------\n";
    
    try {
        $result = $messaging->send($message);
        echo "   ✅ ¡ÉXITO!\n";
        echo "   ✓ Message ID: $result\n\n";
        
        echo "🎉 LA NOTIFICACIÓN SE ENVIÓ CORRECTAMENTE\n";
        echo "Verifica tu dispositivo para confirmar la recepción.\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
        echo "   ❌ Mensaje inválido\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
        echo "   ❌ Token no encontrado (posiblemente expirado)\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
        echo "   ❌ ERROR DE PERMISOS\n";
        echo "   Error: {$e->getMessage()}\n\n";
        echo "📋 SOLUCIÓN:\n";
        echo "   1. Ve a: https://console.cloud.google.com/\n";
        echo "   2. Proyecto: {$cred['project_id']}\n";
        echo "   3. IAM & Admin → IAM\n";
        echo "   4. Busca: {$cred['client_email']}\n";
        echo "   5. Agrega roles:\n";
        echo "      - Firebase Cloud Messaging Admin\n";
        echo "      - Firebase Admin\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error inesperado\n";
        echo "   Tipo: " . get_class($e) . "\n";
        echo "   Mensaje: {$e->getMessage()}\n";
        echo "   Archivo: {$e->getFile()}:{$e->getLine()}\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR CRÍTICO:\n";
    echo "   {$e->getMessage()}\n";
    echo "   {$e->getFile()}:{$e->getLine()}\n\n";
    echo $e->getTraceAsString();
    exit(1);
}

