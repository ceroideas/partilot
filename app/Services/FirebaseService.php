<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $serverKey;
    protected $fcmEndpoint;

    public function __construct()
    {
        $this->serverKey = config('firebase.server_key');
        $this->fcmEndpoint = config('firebase.fcm_endpoint');
    }

    /**
     * Send push notification to a single device
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToDevice($deviceToken, $title, $body, $data = [])
    {
        if (!$this->serverKey) {
            Log::error('Firebase server key not configured');
            return false;
        }

        $payload = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1
            ],
            'data' => $data
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Send push notification to multiple devices
     *
     * @param array $deviceTokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        if (!$this->serverKey) {
            Log::error('Firebase server key not configured');
            return false;
        }

        if (empty($deviceTokens)) {
            Log::warning('No device tokens provided');
            return false;
        }

        $payload = [
            'registration_ids' => $deviceTokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1
            ],
            'data' => $data
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Send push notification to a topic
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        if (!$this->serverKey) {
            Log::error('Firebase server key not configured');
            return false;
        }

        $payload = [
            'to' => '/topics/' . $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1
            ],
            'data' => $data
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Send notification to all users of an entity
     *
     * @param int $entityId
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToEntity($entityId, $title, $body, $data = [])
    {
        // Get users with FCM tokens for this entity
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
     *
     * @param int $administrationId
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToAdministration($administrationId, $title, $body, $data = [])
    {
        // Get users with FCM tokens for this administration
        $users = \App\Models\User::whereNotNull('fcm_token')->get();
        
        if ($users->isEmpty()) {
            Log::info('No FCM tokens found for administration notifications');
            return false;
        }
        
        $tokens = $users->pluck('fcm_token')->toArray();
        return $this->sendToMultipleDevices($tokens, $title, $body, $data);
    }

    /**
     * Send the actual HTTP request to Firebase
     *
     * @param array $payload
     * @return bool
     */
    protected function sendRequest($payload)
    {
        try {
            // Use legacy FCM endpoint with server key for simplicity
            $legacyEndpoint = 'https://fcm.googleapis.com/fcm/send';
            
            Log::info('Sending Firebase notification', [
                'endpoint' => $legacyEndpoint,
                'payload' => $payload,
                'server_key_length' => strlen($this->serverKey)
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post($legacyEndpoint, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Log successful sends
                if (isset($responseData['success']) && $responseData['success'] > 0) {
                    Log::info('Firebase notification sent successfully', [
                        'success_count' => $responseData['success'],
                        'failure_count' => $responseData['failure'] ?? 0
                    ]);
                }

                // Log failures
                if (isset($responseData['failure']) && $responseData['failure'] > 0) {
                    Log::warning('Some Firebase notifications failed', [
                        'success_count' => $responseData['success'] ?? 0,
                        'failure_count' => $responseData['failure'],
                        'results' => $responseData['results'] ?? []
                    ]);
                }

                return true;
            } else {
                Log::error('Firebase notification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                    'endpoint' => $legacyEndpoint,
                    'payload' => $payload
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Firebase notification exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get Firebase configuration for frontend
     *
     * @return array
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
