<?php

/**
 * Test con token de prueba para verificar credenciales
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "========================================\n";
echo "  TEST CON TOKEN DE PRUEBA\n";
echo "========================================\n\n";

try {
    // 1. Inicializar Firebase
    echo "1️⃣  INICIALIZACIÓN\n";
    echo "   ---------------\n";
    
    $credentialsPath = 'storage/firebase-credentials.json';
    $firebase = (new Factory())->withServiceAccount($credentialsPath);
    $messaging = $firebase->createMessaging();
    echo "   ✅ Firebase inicializado\n\n";
    
    // 2. Crear mensaje con token de prueba
    echo "2️⃣  TOKEN DE PRUEBA\n";
    echo "   ----------------\n";
    
    // Token de prueba (no es real, solo para probar la conexión)
    $testToken = "test-token-12345";
    
    $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $testToken)
        ->withNotification(\Kreait\Firebase\Messaging\Notification::create('Test', 'Prueba'));
    
    echo "   ✅ Mensaje creado con token de prueba\n\n";
    
    // 3. Intentar enviar
    echo "3️⃣  ENVIANDO\n";
    echo "   ---------\n";
    
    try {
        echo "   ⏳ Enviando con token de prueba...\n";
        $result = $messaging->send($message);
        
        echo "   ✅ ¡ÉXITO! (Esto sería muy raro)\n";
        echo "   ✅ Message ID: $result\n\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
        echo "   ✅ Error esperado: Mensaje inválido (token de prueba)\n";
        echo "   ✅ Esto significa que las credenciales SÍ funcionan\n";
        echo "   ✅ El problema es el token FCM del usuario\n\n";
        
        echo "🔍 DIAGNÓSTICO CONFIRMADO:\n";
        echo "   📋 Las credenciales de Firebase están correctas\n";
        echo "   📋 El problema es el token FCM del usuario\n\n";
        
        echo "   🛠️  SOLUCIÓN:\n";
        echo "   1. Regenerar token FCM en el frontend\n";
        echo "   2. O habilitar la API de FCM si no está habilitada\n\n";
        
    } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
        echo "   ❌ ERROR DE AUTENTICACIÓN\n";
        echo "   Error: {$e->getMessage()}\n\n";
        
        echo "🔍 DIAGNÓSTICO: Problema con las credenciales\n";
        echo "   📋 POSIBLES CAUSAS:\n";
        echo "   1. API de FCM no habilitada\n";
        echo "   2. Credenciales incorrectas\n";
        echo "   3. Permisos insuficientes\n\n";
        
        echo "   🛠️  SOLUCIONES:\n";
        echo "   1. Habilitar API: https://console.cloud.google.com/apis/library/firebasemessaging.googleapis.com\n";
        echo "   2. Regenerar credenciales en Firebase Console\n";
        echo "   3. Verificar permisos de la cuenta de servicio\n\n";
        
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
