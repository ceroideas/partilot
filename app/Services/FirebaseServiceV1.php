<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Firebase Cloud Messaging Service - API V1
 * 
 * Esta versión usa la API V1 de Firebase que requiere un Service Account JSON
 * en lugar del Server Key legacy.
 * 
 * Para usar este servicio:
 * 1. Descarga el archivo JSON de Service Account de Firebase Console
 * 2. Guárdalo en storage/firebase-credentials.json
 * 3. Actualiza config/firebase.php para usar este servicio
 */
class FirebaseServiceV1
{
    protected $projectId;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
    }

    /**
     * Send push notification to a single device
     */
    public function sendToDevice($deviceToken, $title, $body, $data = [])
    {
        if (!$this->getAccessToken()) {
            Log::error('❌ No se pudo obtener el access token de Firebase');
            return false;
        }

        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],
                'data' => $data,
                'webpush' => [
                    'notification' => [
                        'icon' => url('/favicon.ico'),
                        'badge' => url('/favicon.ico')
                    ]
                ]
            ]
        ];

        return $this->sendRequest($message);
    }

    /**
     * Send push notification to multiple devices
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        if (!$this->getAccessToken()) {
            Log::error('❌ No se pudo obtener el access token de Firebase');
            return false;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($deviceTokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        Log::info("✅ Notificaciones enviadas: {$successCount} exitosas, {$failureCount} fallidas");

        return $successCount > 0;
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
     * Get access token using Service Account JSON
     */
    protected function getAccessToken()
    {
        try {
            $credentialsPath = storage_path('firebase-credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error('❌ Archivo de credenciales no encontrado: ' . $credentialsPath);
                Log::error('   Descarga el archivo JSON de Firebase Console → Project Settings → Service Accounts');
                return false;
            }

            $credentials = json_decode(file_get_contents($credentialsPath), true);

            if (!isset($credentials['private_key']) || !isset($credentials['client_email'])) {
                Log::error('❌ Credenciales de Firebase inválidas');
                return false;
            }

            // Create JWT
            $now = time();
            $payload = [
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600
            ];

            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode($payload));
            
            $signatureInput = $header . '.' . $payload;
            openssl_sign($signatureInput, $signature, $credentials['private_key'], 'SHA256');
            $signature = base64_encode($signature);

            $jwt = $signatureInput . '.' . $signature;

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json()['access_token'];
                return true;
            }

            Log::error('❌ Error al obtener access token de Firebase', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Excepción al obtener access token: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send the actual HTTP request to Firebase API V1
     */
    protected function sendRequest($message)
    {
        try {
            $endpoint = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
            
            Log::info('📤 Enviando notificación Firebase (API V1)', [
                'endpoint' => $endpoint,
                'titulo' => $message['message']['notification']['title'] ?? 'N/A'
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($endpoint, $message);

            if ($response->successful()) {
                Log::info('✅ Notificación Firebase enviada exitosamente (API V1)', [
                    'message_id' => $response->json()['name'] ?? null
                ]);
                return true;
            } else {
                Log::error('❌ Error en la respuesta de Firebase (API V1)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('❌ Excepción al enviar notificación Firebase (API V1)', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
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
}

