<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseServiceModern;
use App\Models\User;

class DiagnoseFirebase extends Command
{
    protected $signature = 'firebase:diagnose';
    protected $description = 'Diagnóstico avanzado de problemas con Firebase';

    public function handle()
    {
        $this->info('🔍 DIAGNÓSTICO DE FIREBASE');
        $this->newLine();

        // 1. Verificar archivo de credenciales
        $this->info('1️⃣  Verificando credenciales...');
        $credentialsPath = storage_path('firebase-credentials.json');
        
        if (!file_exists($credentialsPath)) {
            $this->error('   ❌ Archivo de credenciales no encontrado');
            return Command::FAILURE;
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        
        // Verificar campos importantes
        $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email'];
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (empty($credentials[$field])) {
                $missing[] = $field;
            }
        }

        if (count($missing) > 0) {
            $this->error('   ❌ Campos faltantes en credenciales: ' . implode(', ', $missing));
            return Command::FAILURE;
        }

        $this->info('   ✅ Archivo de credenciales válido');
        $this->info('   📧 Service Account: ' . $credentials['client_email']);
        $this->info('   🆔 Project ID: ' . $credentials['project_id']);
        $this->info('   🔑 Private Key ID: ' . substr($credentials['private_key_id'], 0, 20) . '...');
        $this->newLine();

        // 2. Verificar formato de private key
        $this->info('2️⃣  Verificando formato de Private Key...');
        $privateKey = $credentials['private_key'];
        
        if (!str_contains($privateKey, '-----BEGIN PRIVATE KEY-----')) {
            $this->error('   ❌ Private Key no tiene el formato correcto');
            $this->warn('   💡 Debe comenzar con "-----BEGIN PRIVATE KEY-----"');
            return Command::FAILURE;
        }

        if (!str_contains($privateKey, '-----END PRIVATE KEY-----')) {
            $this->error('   ❌ Private Key incompleta');
            return Command::FAILURE;
        }

        $this->info('   ✅ Private Key tiene formato correcto');
        $this->newLine();

        // 3. Verificar permisos necesarios
        $this->info('3️⃣  Verificando configuración del proyecto...');
        
        if ($credentials['project_id'] !== config('firebase.project_id')) {
            $this->warn('   ⚠️  Project ID en credenciales no coincide con .env');
            $this->warn('   Credenciales: ' . $credentials['project_id']);
            $this->warn('   .env: ' . config('firebase.project_id'));
        } else {
            $this->info('   ✅ Project ID coincide');
        }
        $this->newLine();

        // 4. Verificar que la API esté habilitada
        $this->info('4️⃣  Instrucciones para habilitar Firebase Cloud Messaging API:');
        $this->line('   1. Ve a: https://console.cloud.google.com/apis/library/fcm.googleapis.com?project=' . $credentials['project_id']);
        $this->line('   2. Haz clic en "ENABLE" si no está habilitado');
        $this->newLine();

        // 5. Verificar permisos de la cuenta de servicio
        $this->info('5️⃣  Verificar permisos de la cuenta de servicio:');
        $this->line('   1. Ve a: https://console.cloud.google.com/iam-admin/iam?project=' . $credentials['project_id']);
        $this->line('   2. Busca: ' . $credentials['client_email']);
        $this->line('   3. Debe tener uno de estos roles:');
        $this->line('      - Firebase Cloud Messaging Admin');
        $this->line('      - Firebase Admin');
        $this->line('      - Editor (mínimo)');
        $this->newLine();

        // 6. Probar envío con un token real
        $user = User::whereNotNull('fcm_token')->first();
        
        if ($user) {
            $this->info('6️⃣  Usuario de prueba encontrado:');
            $this->info('   👤 ' . $user->name);
            $this->info('   🔑 Token: ' . substr($user->fcm_token, 0, 50) . '...');
            $this->newLine();

            if ($this->confirm('¿Deseas intentar enviar una notificación de prueba ahora?')) {
                $this->info('   📤 Enviando notificación...');
                
                try {
                    $firebaseService = app(FirebaseServiceModern::class);
                    $success = $firebaseService->sendToDevice(
                        $user->fcm_token,
                        '🔧 Diagnóstico Firebase',
                        'Probando envío directo de notificación',
                        ['test' => 'true']
                    );

                    if ($success) {
                        $this->info('   ✅ ¡Notificación enviada exitosamente!');
                    } else {
                        $this->error('   ❌ Error al enviar notificación');
                        $this->warn('   💡 Revisa storage/logs/laravel.log para más detalles');
                    }
                } catch (\Exception $e) {
                    $this->error('   ❌ Excepción: ' . $e->getMessage());
                    $this->newLine();
                    
                    if (str_contains($e->getMessage(), 'invalid_grant')) {
                        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                        $this->warn('🔴 SOLUCIÓN PARA "invalid_grant"');
                        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                        $this->warn('');
                        $this->warn('Este error significa que las credenciales no son válidas.');
                        $this->warn('Para solucionarlo:');
                        $this->warn('');
                        $this->warn('1. Ve a Firebase Console:');
                        $this->warn('   https://console.firebase.google.com/project/' . $credentials['project_id'] . '/settings/serviceaccounts/adminsdk');
                        $this->warn('');
                        $this->warn('2. Haz clic en "Generate new private key"');
                        $this->warn('');
                        $this->warn('3. Descarga el nuevo archivo JSON');
                        $this->warn('');
                        $this->warn('4. Reemplaza el archivo actual en:');
                        $this->warn('   ' . $credentialsPath);
                        $this->warn('');
                        $this->warn('5. Vuelve a ejecutar: php artisan firebase:diagnose');
                        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                    }
                }
            }
        } else {
            $this->warn('   ⚠️  No hay usuarios con tokens FCM para probar');
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📝 RESUMEN DE ACCIONES NECESARIAS');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->line('1. ✅ Verifica que Cloud Messaging API esté habilitada');
        $this->line('2. ✅ Verifica permisos de la cuenta de servicio');
        $this->line('3. ⚠️  Si el error persiste: genera nuevas credenciales');
        $this->line('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return Command::SUCCESS;
    }
}

