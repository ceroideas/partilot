<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseServiceModern;
use App\Models\User;

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

        // 5. Test con usuario real
        $user = User::whereNotNull('fcm_token')->first();
        
        if (!$user) {
            $this->warn('   ⚠️  No hay usuarios con tokens FCM para probar');
            return Command::SUCCESS;
        }

        $this->info('5️⃣  Usuario de prueba encontrado');
        $this->info('   👤 ' . $user->name);
        $this->info('   🔑 Token: ' . substr($user->fcm_token, 0, 50) . '...');
        $this->newLine();

        if ($this->option('send-test') || $this->confirm('¿Enviar notificación de prueba?', true)) {
            $this->info('6️⃣  Enviando notificación de prueba...');
            $this->line('   (Esto mostrará el error exacto si falla)');
            $this->newLine();
            
            try {
                $success = $this->firebaseService->sendToDevice(
                    $user->fcm_token,
                    '🔧 Test de Diagnóstico',
                    'Prueba de conexión Firebase desde consola',
                    ['test' => 'true']
                );

                if ($success) {
                    $this->info('   ✅ ¡Notificación enviada exitosamente!');
                    $this->info('   Verifica si la recibiste en el navegador');
                } else {
                    $this->error('   ❌ Error al enviar (revisa logs de Laravel)');
                }
            } catch (\Exception $e) {
                $this->error('   ❌ Excepción capturada:');
                $this->error('   Tipo: ' . get_class($e));
                $this->error('   Mensaje: ' . $e->getMessage());
                
                if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
                    $this->error('   Error previo: ' . $e->getPrevious()->getMessage());
                }
                
                $this->newLine();
                $this->line('   Stack trace:');
                $this->line('   ' . $e->getTraceAsString());
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
