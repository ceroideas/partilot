<?php
/**
 * Script de prueba para Firebase API V1
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseServiceModern;
use App\Models\User;

echo "\n";
echo "========================================\n";
echo "  PRUEBA FIREBASE API V1\n";
echo "========================================\n\n";

// 1. Verificar archivo de credenciales
echo "1️⃣  VERIFICANDO CREDENCIALES\n";
echo "   ---------------------\n";

$credentialsPath = storage_path('firebase-credentials.json');

if (!file_exists($credentialsPath)) {
    echo "   ❌ Archivo de credenciales no encontrado: {$credentialsPath}\n";
    echo "\n   📋 INSTRUCCIONES:\n";
    echo "   1. Ve a Firebase Console → Project Settings → Service Accounts\n";
    echo "   2. Haz clic en 'Generate new private key'\n";
    echo "   3. Descarga el archivo JSON\n";
    echo "   4. Guárdalo como: storage/firebase-credentials.json\n\n";
    exit(1);
}

echo "   ✓ Archivo de credenciales encontrado\n";

// Verificar que el archivo es válido
$credentials = json_decode(file_get_contents($credentialsPath), true);
if (!isset($credentials['private_key']) || !isset($credentials['client_email'])) {
    echo "   ❌ Archivo de credenciales inválido\n";
    exit(1);
}

echo "   ✓ Archivo de credenciales válido\n";
echo "   ✓ Project ID: " . ($credentials['project_id'] ?? 'No encontrado') . "\n";

// 2. Inicializar servicio
echo "\n2️⃣  INICIALIZANDO SERVICIO FIREBASE\n";
echo "   ------------------------------\n";

try {
    $firebaseService = new FirebaseServiceModern();
    echo "   ✓ Servicio Firebase inicializado\n";
} catch (\Exception $e) {
    echo "   ❌ Error al inicializar servicio: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Verificar usuarios con tokens
echo "\n3️⃣  VERIFICANDO USUARIOS CON TOKENS\n";
echo "   -------------------------------\n";

$users = User::whereNotNull('fcm_token')->get();

if ($users->isEmpty()) {
    echo "   ❌ No hay usuarios con tokens FCM\n";
    echo "   → Abre la aplicación en el navegador para registrar un token\n";
    exit(1);
}

echo "   ✓ {$users->count()} usuario(s) con tokens:\n";
foreach ($users as $user) {
    $tokenPreview = substr($user->fcm_token, 0, 30);
    echo "      - {$user->name} ({$user->email}): {$tokenPreview}...\n";
}

// 4. Test de conexión
echo "\n4️⃣  TEST DE CONEXIÓN\n";
echo "   ----------------\n";

if ($firebaseService->testConnection()) {
    echo "   ✓ Conexión a Firebase exitosa\n";
} else {
    echo "   ❌ Error de conexión a Firebase\n";
    exit(1);
}

// 5. Enviar notificación de prueba
echo "\n5️⃣  ENVIANDO NOTIFICACIÓN DE PRUEBA\n";
echo "   -------------------------------\n";

$testUser = $users->first();

try {
    $result = $firebaseService->sendToDevice(
        $testUser->fcm_token,
        '🧪 Test API V1',
        'Esta es una prueba de la nueva API V1 de Firebase',
        [
            'test' => 'true',
            'api_version' => 'v1',
            'timestamp' => now()->toIso8601String()
        ]
    );

    if ($result) {
        echo "   ✅ ¡ÉXITO! Notificación enviada correctamente\n";
        echo "   → Revisa tu dispositivo/navegador para ver la notificación\n";
        echo "\n   📱 La notificación debería aparecer como:\n";
        echo "      Título: 🧪 Test API V1\n";
        echo "      Mensaje: Esta es una prueba de la nueva API V1 de Firebase\n";
    } else {
        echo "   ❌ Error al enviar la notificación\n";
        echo "   → Revisa storage/logs/laravel.log para más detalles\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Excepción: " . $e->getMessage() . "\n";
}

// 6. Verificar logs
echo "\n6️⃣  VERIFICAR LOGS\n";
echo "   --------------\n";
echo "   Revisa los logs en: storage/logs/laravel.log\n";
echo "   Busca mensajes que empiecen con:\n";
echo "   • ✅ Firebase Service Modern inicializado\n";
echo "   • 📤 Enviando notificación a dispositivo\n";
echo "   • ✅ Notificación enviada exitosamente\n";

echo "\n========================================\n";
echo "  FIN DE LA PRUEBA\n";
echo "========================================\n\n";

echo "💡 PRÓXIMO PASO:\n";
echo "   Si la prueba fue exitosa, puedes enviar notificaciones\n";
echo "   desde la interfaz web de Partilot.\n\n";
