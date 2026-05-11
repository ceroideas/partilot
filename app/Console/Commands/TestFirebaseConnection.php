<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseServiceModern;
use App\Models\UserFcmToken;

class TestFirebaseConnection extends Command
{
    protected $signature = 'firebase:test {--send-test : Enviar notificación de prueba}';
    protected $description = 'Probar conexión con Firebase con diagnóstico detallado';

    protected $firebaseService;

    public function __construct(FirebaseServiceModern $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    public function handle()
    {
        $this->info('🔥 TEST DE CONEXIÓN FIREBASE - DIAGNÓSTICO PROFUNDO');
        $this->newLine();

        // 1. Verificar credenciales
        $this->info('1️⃣  Verificando credenciales...');
        $credentialsPath = storage_path('firebase-credentials.json');
        
        if (!file_exists($credentialsPath)) {
            $this->error('   ❌ Archivo de credenciales no encontrado');
            return Command::FAILURE;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        $this->info('   ✅ Archivo de credenciales cargado');
        $this->info('   📧 Service Account: ' . $credentials['client_email']);
        $this->info('   🆔 Project ID: ' . $credentials['project_id']);
        
        // Verificar hash del archivo para confirmar que es el mismo
        $fileHash = hash_file('sha256', $credentialsPath);
        $this->info('   🔐 Hash del archivo: ' . substr($fileHash, 0, 16) . '...');
        $this->newLine();

        // 2. Verificar conectividad SSL específica
        $this->info('2️⃣  Verificando conectividad SSL a Google...');
        
        try {
            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            
            curl_close($ch);
            
            if ($error) {
                $this->error('   ❌ Error de cURL: ' . $error);
                $this->line('   Detalles: ' . substr($verboseLog, 0, 500));
            } else {
                $this->info('   ✅ Conexión SSL a Google OAuth: OK');
                $this->info('   HTTP Code: ' . $httpCode);
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Excepción: ' . $e->getMessage());
        }
        $this->newLine();

        // 3. Verificar certificados CA
        $this->info('3️⃣  Verificando certificados CA del sistema...');
        $caInfo = openssl_get_cert_locations();
        $this->info('   Archivo CA por defecto: ' . $caInfo['default_cert_file']);
        $this->info('   Directorio CA: ' . $caInfo['default_cert_dir']);
        
        if (file_exists($caInfo['default_cert_file'])) {
            $this->info('   ✅ Archivo CA existe');
        } else {
            $this->warn('   ⚠️  Archivo CA no encontrado');
        }
        $this->newLine();

        // 4. Test real de Firebase
        $this->info('4️⃣  Probando inicialización de Firebase...');
        
        try {
            // Forzar nueva inicialización
            $factory = (new \Kreait\Firebase\Factory)
                ->withServiceAccount($credentialsPath);
            
            $this->info('   ✅ Factory creado');
            
            $messaging = $factory->createMessaging();
            $this->info('   ✅ Messaging inicializado');
            
        } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
            $this->error('   ❌ Error de autenticación de Firebase');
            $this->error('   Mensaje: ' . $e->getMessage());
            $this->line('   Este error indica que Google rechaza las credenciales');
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
            $this->line('   Clase: ' . get_class($e));
            return Command::FAILURE;
        }
        $this->newLine();

        // 5. Test con dispositivo real
        $device = UserFcmToken::with('user')->first();

        if (! $device) {
            $this->warn('   ⚠️  No hay tokens FCM registrados para probar');

            return Command::SUCCESS;
        }

        $this->info('5️⃣  Dispositivo de prueba encontrado');
        $this->info('   👤 ' . ($device->user->name ?? 'user#' . $device->user_id));
        $this->info('   🔑 Token: ' . substr($device->token, 0, 50) . '...');
        $this->newLine();

        if ($this->option('send-test') || $this->confirm('¿Enviar notificación de prueba?', true)) {
            $this->info('6️⃣  Enviando notificación de prueba...');
            $this->line('   (Esto mostrará el error exacto si falla)');
            $this->newLine();
            
            // Activar logging detallado
            \Log::info('🧪 Iniciando test de envío de notificación...');
            
            try {
                // Intentar crear el mensaje manualmente para ver dónde falla
                $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $device->token)
                    ->withNotification(\Kreait\Firebase\Messaging\Notification::create('🔧 Test de Diagnóstico', 'Prueba desde consola'))
                    ->withData(['test' => 'true']);
                
                $this->info('   ✅ Mensaje creado correctamente');
                
                // Intentar enviar
                $factory = (new \Kreait\Firebase\Factory)
                    ->withServiceAccount($credentialsPath);
                $messaging = $factory->createMessaging();
                
                $this->info('   📤 Enviando mensaje...');
                $result = $messaging->send($message);
                
                $this->info('   ✅ ¡Notificación enviada exitosamente!');
                $this->info('   Message ID: ' . (is_string($result) ? $result : json_encode($result)));
                $this->info('   Verifica si la recibiste en el navegador');
                
            } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
                $this->error('   ❌ Mensaje inválido');
                $this->error('   ' . $e->getMessage());
            } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
                $this->error('   ❌ Token no encontrado o inválido');
                $this->error('   ' . $e->getMessage());
            } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
                $this->error('   ❌ ERROR DE AUTENTICACIÓN');
                $this->error('   Mensaje: ' . $e->getMessage());
                $this->newLine();
                $this->warn('   💡 Esto significa que Google rechaza las credenciales.');
                $this->warn('   Razones posibles:');
                $this->warn('   1. Certificados SSL no configurados correctamente');
                $this->warn('   2. Cuenta de servicio sin permisos');
                $this->warn('   3. API de Firebase no habilitada');
                
                if ($e->getPrevious()) {
                    $this->error('   Error subyacente: ' . $e->getPrevious()->getMessage());
                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $this->error('   ❌ ERROR DE CONEXIÓN HTTP');
                $this->error('   Mensaje: ' . $e->getMessage());
                
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $this->error('   Código HTTP: ' . $response->getStatusCode());
                    $this->error('   Respuesta: ' . $response->getBody()->getContents());
                }
            } catch (\Exception $e) {
                $this->error('   ❌ Excepción capturada:');
                $this->error('   Tipo: ' . get_class($e));
                $this->error('   Mensaje: ' . $e->getMessage());
                $this->error('   Archivo: ' . $e->getFile() . ':' . $e->getLine());
                
                if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
                    $prev = $e->getPrevious();
                    $this->error('   Error previo: ' . get_class($prev) . ': ' . $prev->getMessage());
                }
                
                $this->newLine();
                $this->line('   Stack trace (primeras 10 líneas):');
                $traces = explode("\n", $e->getTraceAsString());
                foreach (array_slice($traces, 0, 10) as $trace) {
                    $this->line('   ' . $trace);
                }
            }
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📋 INFORMACIÓN PARA DEBUG');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('Versión PHP: ' . PHP_VERSION);
        $this->line('Sistema Operativo: ' . PHP_OS);
        $this->line('Kreait Firebase PHP: ' . (class_exists('Kreait\Firebase\Factory') ? 'Instalado' : 'NO instalado'));
        $this->line('OpenSSL: ' . (extension_loaded('openssl') ? OPENSSL_VERSION_TEXT : 'NO disponible'));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return Command::SUCCESS;
    }
}
