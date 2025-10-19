<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseServiceModern;
use App\Models\User;

class TestFirebaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:test {--send-test : Enviar notificación de prueba a todos los usuarios con token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la configuración y conexión con Firebase Cloud Messaging';

    protected $firebaseService;

    public function __construct(FirebaseServiceModern $firebaseService)
    {
        parent::__construct();
        $this->firebaseService = $firebaseService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 FIREBASE CONNECTION TEST 🔥');
        $this->newLine();

        // 1. Verificar archivo de credenciales
        $this->info('1️⃣  Verificando archivo de credenciales...');
        $credentialsPath = storage_path('firebase-credentials.json');
        
        if (file_exists($credentialsPath)) {
            $this->info('   ✅ Archivo de credenciales encontrado');
            $credentials = json_decode(file_get_contents($credentialsPath), true);
            $this->info('   📧 Service Account: ' . ($credentials['client_email'] ?? 'N/A'));
            $this->info('   🆔 Project ID: ' . ($credentials['project_id'] ?? 'N/A'));
        } else {
            $this->error('   ❌ Archivo de credenciales NO encontrado en: ' . $credentialsPath);
            $this->warn('   💡 Descarga el archivo JSON desde Firebase Console → Project Settings → Service Accounts');
            return Command::FAILURE;
        }
        $this->newLine();

        // 2. Verificar configuración en .env
        $this->info('2️⃣  Verificando configuración en .env...');
        $configs = [
            'FIREBASE_API_KEY' => config('firebase.api_key'),
            'FIREBASE_PROJECT_ID' => config('firebase.project_id'),
            'FIREBASE_MESSAGING_SENDER_ID' => config('firebase.messaging_sender_id'),
            'FIREBASE_APP_ID' => config('firebase.app_id'),
            'FIREBASE_SERVER_KEY' => config('firebase.server_key'),
        ];

        $allConfigured = true;
        foreach ($configs as $key => $value) {
            if (empty($value)) {
                $this->error("   ❌ {$key} no está configurado");
                $allConfigured = false;
            } else {
                $masked = $key === 'FIREBASE_SERVER_KEY' ? substr($value, 0, 20) . '...' : $value;
                $this->info("   ✅ {$key}: {$masked}");
            }
        }

        if (!$allConfigured) {
            $this->newLine();
            $this->warn('   💡 Agrega las variables faltantes en tu archivo .env');
            return Command::FAILURE;
        }
        $this->newLine();

        // 3. Probar conexión con Firebase
        $this->info('3️⃣  Probando conexión con Firebase...');
        if ($this->firebaseService->testConnection()) {
            $this->info('   ✅ Conexión exitosa con Firebase');
        } else {
            $this->error('   ❌ Error de conexión con Firebase');
            $this->warn('   💡 Revisa los logs en storage/logs/laravel.log para más detalles');
            return Command::FAILURE;
        }
        $this->newLine();

        // 4. Verificar usuarios con tokens FCM
        $this->info('4️⃣  Verificando usuarios con tokens FCM...');
        $usersWithTokens = User::whereNotNull('fcm_token')->count();
        $this->info("   👥 Usuarios con token FCM: {$usersWithTokens}");
        
        if ($usersWithTokens === 0) {
            $this->warn('   ⚠️  No hay usuarios con tokens FCM registrados');
            $this->info('   💡 Los usuarios deben abrir la aplicación web y permitir las notificaciones');
        } else {
            $users = User::whereNotNull('fcm_token')->get(['id', 'name', 'email']);
            $this->table(['ID', 'Nombre', 'Email'], $users->map(fn($u) => [$u->id, $u->name, $u->email]));
        }
        $this->newLine();

        // 5. Enviar notificación de prueba (opcional)
        if ($this->option('send-test') && $usersWithTokens > 0) {
            $this->info('5️⃣  Enviando notificación de prueba...');
            
            if ($this->confirm('¿Deseas enviar una notificación de prueba a todos los usuarios con token?')) {
                $users = User::whereNotNull('fcm_token')->get();
                $tokens = $users->pluck('fcm_token')->toArray();
                
                $success = $this->firebaseService->sendToMultipleDevices(
                    $tokens,
                    '🔥 Prueba de Firebase',
                    '¡Las notificaciones push están funcionando correctamente!',
                    [
                        'type' => 'test',
                        'timestamp' => now()->toIso8601String()
                    ]
                );

                if ($success) {
                    $this->info('   ✅ Notificación de prueba enviada exitosamente');
                } else {
                    $this->error('   ❌ Error al enviar notificación de prueba');
                    $this->warn('   💡 Revisa los logs en storage/logs/laravel.log');
                }
            }
            $this->newLine();
        }

        // Resumen final
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✨ RESUMEN');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Configuración completada correctamente');
        $this->info('✅ Firebase está listo para enviar notificaciones');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        return Command::SUCCESS;
    }
}


