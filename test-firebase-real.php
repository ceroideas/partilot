<?php

/**
 * Prueba de Firebase API V1 con usuarios reales
 * Este script envía una notificación de prueba a todos los usuarios con tokens FCM
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\FirebaseServiceModern;
use Illuminate\Support\Facades\Log;

echo "========================================\n";
echo "  PRUEBA FIREBASE API V1 CON USUARIOS REALES\n";
echo "========================================\n\n";

try {
    echo "1️⃣  VERIFICANDO USUARIOS CON TOKENS\n";
    echo "   --------------------------------\n";
    
    $users = User::whereNotNull('fcm_token')->get();
    
    if ($users->count() === 0) {
        echo "   ❌ No hay usuarios con tokens FCM registrados\n";
        echo "   💡 Sugerencia: Inicia sesión en la aplicación web y acepta las notificaciones\n";
        exit(1);
    }
    
    echo "   ✅ Encontrados {$users->count()} usuario(s) con tokens FCM:\n";
    foreach ($users as $user) {
        echo "      • {$user->name} ({$user->email})\n";
        echo "        Token: " . substr($user->fcm_token, 0, 50) . "...\n";
    }
    echo "\n";
    
    echo "2️⃣  INICIALIZANDO SERVICIO FIREBASE MODERN\n";
    echo "   ---------------------------------------\n";
    
    $firebaseService = new FirebaseServiceModern();
    echo "   ✅ Servicio Firebase Modern inicializado\n\n";
    
    echo "3️⃣  ENVIANDO NOTIFICACIÓN DE PRUEBA\n";
    echo "   ---------------------------------\n";
    
    $tokens = $users->pluck('fcm_token')->toArray();
    
    $title = "🎉 Prueba de Notificación";
    $body = "Esta es una notificación de prueba enviada con Firebase API V1 a las " . date('H:i:s');
    $data = [
        'type' => 'test',
        'timestamp' => time(),
        'test_id' => uniqid('test_')
    ];
    
    echo "   📱 Título: {$title}\n";
    echo "   📝 Mensaje: {$body}\n";
    echo "   👥 Destinatarios: {$users->count()}\n";
    echo "   ⏳ Enviando...\n\n";
    
    $result = $firebaseService->sendToMultipleDevices($tokens, $title, $body, $data);
    
    if ($result) {
        echo "   ✅ ¡NOTIFICACIÓN ENVIADA EXITOSAMENTE!\n\n";
        
        echo "4️⃣  RESULTADO\n";
        echo "   ----------\n";
        echo "   🎯 Estado: ÉXITO\n";
        echo "   📊 Usuarios notificados: {$users->count()}\n";
        echo "   ⏰ Fecha/Hora: " . date('d/m/Y H:i:s') . "\n\n";
        
        echo "🎉 PRUEBA COMPLETADA CON ÉXITO!\n";
        echo "La API V1 de Firebase está funcionando correctamente.\n";
        echo "Verifica tu dispositivo para confirmar que recibiste la notificación.\n\n";
        
        exit(0);
    } else {
        echo "   ❌ ERROR AL ENVIAR NOTIFICACIÓN\n\n";
        
        echo "4️⃣  POSIBLES CAUSAS\n";
        echo "   ----------------\n";
        echo "   • Permisos de la cuenta de servicio insuficientes\n";
        echo "   • Tokens FCM inválidos o expirados\n";
        echo "   • Problemas de conectividad\n\n";
        
        echo "📋 Revisa los logs para más detalles:\n";
        echo "   tail -f storage/logs/laravel.log\n\n";
        
        exit(1);
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR CRÍTICO\n\n";
    echo "   Mensaje: {$e->getMessage()}\n";
    echo "   Archivo: {$e->getFile()}:{$e->getLine()}\n";
    
    if ($e->getPrevious()) {
        echo "   Causa: {$e->getPrevious()->getMessage()}\n";
    }
    
    echo "\n📋 Stack trace:\n";
    echo $e->getTraceAsString();
    echo "\n\n";
    
    exit(1);
}

