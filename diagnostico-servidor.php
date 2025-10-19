<?php

/**
 * Diagnóstico completo para servidor de producción
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
echo "  DIAGNÓSTICO COMPLETO SERVIDOR\n";
echo "========================================\n\n";

try {
    // 1. Información del sistema
    echo "1️⃣  INFORMACIÓN DEL SISTEMA\n";
    echo "   ------------------------\n";
    echo "   ✓ Fecha actual: " . date('Y-m-d H:i:s') . "\n";
    echo "   ✓ Zona horaria: " . date_default_timezone_get() . "\n";
    echo "   ✓ PHP Version: " . PHP_VERSION . "\n";
    echo "   ✓ Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'No disponible') . "\n\n";
    
    // 2. Verificar credenciales
    echo "2️⃣  CREDENCIALES\n";
    echo "   -------------\n";
    
    $credentialsPath = storage_path('firebase-credentials.json');
    if (!file_exists($credentialsPath)) {
        die("❌ No existe: $credentialsPath\n");
    }
    
    $cred = json_decode(file_get_contents($credentialsPath), true);
    echo "   ✓ Project ID: {$cred['project_id']}\n";
    echo "   ✓ Client Email: {$cred['client_email']}\n";
    echo "   ✓ Private Key ID: {$cred['private_key_id']}\n";
    echo "   ✓ Tipo: {$cred['type']}\n\n";
    
    // 3. Verificar permisos del archivo
    echo "3️⃣  PERMISOS DE ARCHIVO\n";
    echo "   --------------------\n";
    echo "   ✓ Archivo existe: " . (file_exists($credentialsPath) ? 'SÍ' : 'NO') . "\n";
    echo "   ✓ Archivo legible: " . (is_readable($credentialsPath) ? 'SÍ' : 'NO') . "\n";
    echo "   ✓ Tamaño: " . filesize($credentialsPath) . " bytes\n\n";
    
    // 4. Inicializar Firebase con más detalles
    echo "4️⃣  INICIALIZACIÓN FIREBASE\n";
    echo "   -------------------------\n";
    
    try {
        $firebase = (new Factory())
            ->withServiceAccount($credentialsPath);
        
        $messaging = $firebase->createMessaging();
        echo "   ✓ Messaging creado exitosamente\n";
        
        // Intentar obtener información del proyecto
        $app = $messaging->getApp();
        echo "   ✓ Project ID verificado: " . $app->getProjectId() . "\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error al inicializar Firebase:\n";
        echo "      Tipo: " . get_class($e) . "\n";
        echo "      Mensaje: {$e->getMessage()}\n";
        echo "      Archivo: {$e->getFile()}:{$e->getLine()}\n\n";
        exit(1);
    }
    
    // 5. Obtener usuario con token
    echo "5️⃣  USUARIO CON TOKEN\n";
    echo "   ------------------\n";
    
    $user = User::whereNotNull('fcm_token')->first();
    
    if (!$user) {
        die("   ❌ No hay usuarios con tokens FCM\n");
    }
    
    echo "   ✓ Usuario: {$user->name}\n";
    echo "   ✓ Email: {$user->email}\n";
    echo "   ✓ Token: " . substr($user->fcm_token, 0, 50) . "...\n";
    echo "   ✓ Token completo válido: " . (strlen($user->fcm_token) > 100 ? 'SÍ' : 'NO') . "\n\n";
    
    // 6. Crear mensaje simple
    echo "6️⃣  CREAR MENSAJE SIMPLE\n";
    echo "   ---------------------\n";
    
    $message = CloudMessage::withTarget('token', $user->fcm_token)
        ->withNotification(Notification::create(
            '🔧 Test Servidor',
            'Prueba desde servidor - ' . date('H:i:s')
        ));
    
    echo "   ✓ Mensaje creado\n\n";
    
    // 7. Intentar enviar
    echo "7️⃣  ENVIANDO NOTIFICACIÓN\n";
    echo "   ----------------------\n";
    
    try {
        echo "   ⏳ Enviando...\n";
        $result = $messaging->send($message);
        
        echo "   ✅ ¡ÉXITO TOTAL!\n";
        echo "   ✓ Message ID: $result\n";
        echo "   ✓ Timestamp: " . date('Y-m-d H:i:s') . "\n\n";
        
        echo "🎉 NOTIFICACIÓN ENVIADA CORRECTAMENTE EN EL SERVIDOR\n";
        echo "La API V1 de Firebase funciona perfectamente.\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
        echo "   ❌ Mensaje inválido\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
        // Intentar con un mensaje más simple
        echo "   🔄 Intentando con mensaje más simple...\n";
        try {
            $simpleMessage = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification(Notification::create('Test', 'Simple test'));
            
            $result = $messaging->send($simpleMessage);
            echo "   ✅ ¡ÉXITO con mensaje simple!\n";
            echo "   ✓ Message ID: $result\n\n";
            
        } catch (\Exception $e2) {
            echo "   ❌ También falló el mensaje simple\n";
            echo "   Error: {$e2->getMessage()}\n\n";
        }
        
    } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
        echo "   ❌ ERROR DE AUTENTICACIÓN\n";
        echo "   Error: {$e->getMessage()}\n\n";
        echo "📋 POSIBLES CAUSAS:\n";
        echo "   1. Reloj del servidor desincronizado\n";
        echo "   2. API de FCM no habilitada\n";
        echo "   3. Permisos insuficientes\n";
        echo "   4. Credenciales incorrectas\n\n";
        
        echo "🔧 SOLUCIONES:\n";
        echo "   • Sincronizar reloj: ntpdate -s time.nist.gov\n";
        echo "   • Verificar API: https://console.cloud.google.com/apis/library\n";
        echo "   • Verificar permisos: https://console.cloud.google.com/iam-admin/iam\n\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Error inesperado\n";
        echo "   Tipo: " . get_class($e) . "\n";
        echo "   Mensaje: {$e->getMessage()}\n";
        echo "   Archivo: {$e->getFile()}:{$e->getLine()}\n\n";
        
        if ($e->getPrevious()) {
            echo "   Causa anterior: " . $e->getPrevious()->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR CRÍTICO:\n";
    echo "   {$e->getMessage()}\n";
    echo "   {$e->getFile()}:{$e->getLine()}\n\n";
    echo $e->getTraceAsString();
    exit(1);
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "DIAGNÓSTICO COMPLETADO\n";
echo str_repeat("=", 40) . "\n";
