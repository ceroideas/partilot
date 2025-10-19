<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

/**
 * Firebase Cloud Messaging Service - Modern API V1
 * 
 * Usa la librería oficial de Firebase PHP para enviar notificaciones
 * a través de la API V1 (más moderna y segura).
 */
class FirebaseServiceModern
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = storage_path('firebase-credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error('❌ Archivo de credenciales no encontrado: ' . $credentialsPath);
                Log::error('   Descarga el archivo JSON de Firebase Console → Project Settings → Service Accounts');
                return;
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withProjectId(config('firebase.project_id'));

            $this->messaging = $factory->createMessaging();
            
            Log::info('✅ Firebase Service Modern inicializado correctamente');
            
        } catch (\Exception $e) {
            Log::error('❌ Error al inicializar Firebase Service Modern: ' . $e->getMessage());
        }
    }

    /**
     * Send push notification to a single device
     */
    public function sendToDevice($deviceToken, $title, $body, $data = [])
    {
        if (!$this->messaging) {
            Log::error('❌ Firebase Messaging no inicializado');
            return false;
        }

        try {
            Log::info('📤 Enviando notificación a dispositivo individual', [
                'token' => substr($deviceToken, 0, 30) . '...',
                'titulo' => $title
            ]);

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body))
                ->withData($data);

            $result = $this->messaging->send($message);
            
            Log::info('✅ Notificación enviada exitosamente', [
                'message_id' => $result
            ]);
            
            return true;

        } catch (\Exception $e) {
            Log::error('❌ Error al enviar notificación: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification to multiple devices
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        if (!$this->messaging) {
            Log::error('❌ Firebase Messaging no inicializado');
            return false;
        }

        if (empty($deviceTokens)) {
            Log::warning('⚠️ No hay tokens para enviar notificaciones');
            return false;
        }

        try {
            Log::info('📤 Enviando notificación a múltiples dispositivos', [
                'cantidad_tokens' => count($deviceTokens),
                'titulo' => $title
            ]);

            $messages = [];
            foreach ($deviceTokens as $token) {
                $messages[] = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData($data);
            }

            $results = $this->messaging->sendAll($messages);
            
            $successCount = $results->successes()->count();
            $failureCount = $results->failures()->count();
            
            Log::info('✅ Notificaciones enviadas', [
                'exitosas' => $successCount,
                'fallidas' => $failureCount
            ]);

            // Log errores específicos
            foreach ($results->failures() as $failure) {
                Log::warning('⚠️ Notificación falló', [
                    'error' => $failure->error()->getMessage(),
                    'token' => substr($failure->target()->value(), 0, 30) . '...'
                ]);
            }

            return $successCount > 0;

        } catch (\Kreait\Firebase\Exception\Messaging\AuthenticationError $e) {
            Log::error('❌ Error de autenticación/permisos en Firebase', [
                'error' => $e->getMessage(),
                'suggestion' => 'Verificar permisos de la cuenta de servicio en Firebase Console'
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('❌ Error al enviar notificaciones múltiples: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all users of an entity
     */
    public function sendToEntity($entityId, $title, $body, $data = [])
    {
        $users = \App\Models\User::whereNotNull('fcm_token')->get();
        
        if ($users->isEmpty()) {
            Log::info('No FCM tokens found for entity notifications');
            return false;
        }
        
        $tokens = $users->pluck('fcm_token')->toArray();
        return $this->sendToMultipleDevices($tokens, $title, $body, $data);
    }

    /**
     * Send notification to all users of an administration
     */
    public function sendToAdministration($administrationId, $title, $body, $data = [])
    {
        $users = \App\Models\User::whereNotNull('fcm_token')->get();
        
        if ($users->isEmpty()) {
            Log::info('No FCM tokens found for administration notifications');
            return false;
        }
        
        $tokens = $users->pluck('fcm_token')->toArray();
        return $this->sendToMultipleDevices($tokens, $title, $body, $data);
    }

    /**
     * Get Firebase configuration for frontend
     */
    public function getConfig()
    {
        return [
            'apiKey' => config('firebase.api_key'),
            'authDomain' => config('firebase.auth_domain'),
            'databaseURL' => config('firebase.database_url'),
            'projectId' => config('firebase.project_id'),
            'storageBucket' => config('firebase.storage_bucket'),
            'messagingSenderId' => config('firebase.messaging_sender_id'),
            'appId' => config('firebase.app_id')
        ];
    }

    /**
     * Test the connection to Firebase
     */
    public function testConnection()
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            // Intenta obtener información del proyecto
            $app = $this->messaging->getApp();
            Log::info('✅ Conexión a Firebase exitosa', [
                'project_id' => $app->getProjectId()
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Error de conexión a Firebase: ' . $e->getMessage());
            return false;
        }
    }
}
