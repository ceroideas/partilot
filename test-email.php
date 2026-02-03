<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "\n=== TEST DE ENVÍO DE CORREO SMTP ===\n\n";

$mailConfig = config('mail');
echo "Configuración SMTP:\n";
echo "  - Host: " . ($mailConfig['mailers']['smtp']['host'] ?? 'N/A') . "\n";
echo "  - Puerto: " . ($mailConfig['mailers']['smtp']['port'] ?? 'N/A') . "\n";
echo "  - Encriptación: " . ($mailConfig['mailers']['smtp']['encryption'] ?? 'N/A') . "\n";
echo "  - Usuario: " . ($mailConfig['mailers']['smtp']['username'] ?? 'N/A') . "\n";
echo "  - Desde: " . ($mailConfig['from']['address'] ?? 'N/A') . "\n\n";

echo "Enviando correo de prueba a jorgesolano92@gmail.com...\n";

try {
    Mail::raw('Este es un correo de prueba desde Partilot.

La configuración SMTP está funcionando correctamente.

Detalles de la prueba:
- Fecha: ' . date('Y-m-d H:i:s') . '
- Servidor: ' . ($mailConfig['mailers']['smtp']['host'] ?? 'N/A') . '
- Puerto: ' . ($mailConfig['mailers']['smtp']['port'] ?? 'N/A') . '
- Encriptación: ' . ($mailConfig['mailers']['smtp']['encryption'] ?? 'N/A') . '

Si recibes este correo, significa que la configuración SMTP está correcta.

Saludos,
Sistema Partilot', function($message) {
        $message->to('jorgesolano92@gmail.com')
                ->subject('✅ Prueba de Correo SMTP - Partilot');
    });
    
    echo "✅ ¡Correo enviado exitosamente a jorgesolano92@gmail.com!\n";
    echo "Revisa tu bandeja de entrada (y spam si no lo encuentras).\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error al enviar el correo:\n";
    echo $e->getMessage() . "\n\n";
    echo "Detalles:\n";
    echo "  - Tipo: " . get_class($e) . "\n";
    echo "  - Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
