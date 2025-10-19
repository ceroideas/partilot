<?php
require_once 'vendor/autoload.php';

echo "========================================\n";
echo "  PRUEBA FIREBASE PHP 6.0.0\n";
echo "========================================\n\n";

try {
    echo "1️⃣  VERIFICANDO CREDENCIALES\n";
    echo "   ---------------------\n";
    
    $credentialsPath = 'storage/firebase-credentials.json';
    if (!file_exists($credentialsPath)) {
        throw new Exception("❌ Archivo de credenciales no encontrado: $credentialsPath");
    }
    echo "   ✓ Archivo de credenciales encontrado\n";
    
    $credentials = json_decode(file_get_contents($credentialsPath), true);
    if (!$credentials) {
        throw new Exception("❌ Archivo de credenciales inválido");
    }
    echo "   ✓ Archivo de credenciales válido\n";
    echo "   ✓ Project ID: " . $credentials['project_id'] . "\n\n";
    
    echo "2️⃣  INICIALIZANDO FIREBASE\n";
    echo "   ---------------------\n";
    
    // Para la versión 6.0.0, usamos la API correcta
    $firebase = (new \Kreait\Firebase\Factory())
        ->withServiceAccount($credentialsPath);
    
    echo "   ✓ Firebase Factory creado\n";
    
    // Obtener el servicio de messaging
    $messaging = $firebase->createMessaging();
    echo "   ✓ Messaging service creado\n\n";
    
    echo "3️⃣  VERIFICANDO USUARIOS CON TOKENS\n";
    echo "   -------------------------------\n";
    
    require_once 'bootstrap/app.php';
    
    $users = \App\Models\User::whereNotNull('fcm_token')->get();
    echo "   ✓ " . $users->count() . " usuario(s) con tokens:\n";
    foreach ($users as $user) {
        echo "      - {$user->name} ({$user->email}): " . substr($user->fcm_token, 0, 50) . "...\n";
    }
    echo "\n";
    
    echo "4️⃣  TEST DE CONEXIÓN\n";
    echo "   ----------------\n";
    
    if ($users->count() == 0) {
        echo "   ❌ No hay usuarios con tokens para probar\n";
        exit(1);
    }
    
    // Crear mensaje usando la API de la versión 6.0.0
    $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $users->first()->fcm_token)
        ->withNotification(\Kreait\Firebase\Messaging\Notification::create(
            'Prueba API V1',
            'Esta es una prueba de la nueva API de Firebase'
        ));
    
    echo "   ✓ Mensaje creado\n";
    echo "   ✓ Enviando notificación...\n";
    
    $result = $messaging->send($message);
    echo "   ✅ Notificación enviada exitosamente!\n";
    echo "   ✓ Message ID: " . $result . "\n\n";
    
    echo "🎉 PRUEBA COMPLETADA CON ÉXITO!\n";
    echo "La nueva API de Firebase está funcionando correctamente.\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   📍 Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    if ($e->getPrevious()) {
        echo "   🔗 Causa anterior: " . $e->getPrevious()->getMessage() . "\n";
    }
    exit(1);
}
