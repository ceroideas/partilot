<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Firebase settings for the application.
    | These settings are used by the Firebase service to send push notifications.
    |
    */

    'api_key' => env('FIREBASE_API_KEY', 'AIzaSyABsAHy3BtYUkcV4z3gjCl3NNU35ye4LFs'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'inicio-de-sesion-94ddc.firebaseapp.com'),
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://inicio-de-sesion-94ddc.firebaseio.com'),
    'project_id' => env('FIREBASE_PROJECT_ID', 'inicio-de-sesion-94ddc'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'inicio-de-sesion-94ddc.firebasestorage.app'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '204683025370'),
    'app_id' => env('FIREBASE_APP_ID', '1:204683025370:web:c424b261eff8d566be7ee3'),
    
    /*
    |--------------------------------------------------------------------------
    | Firebase Server Key
    |--------------------------------------------------------------------------
    |
    | This is the server key from Firebase Console > Project Settings > Cloud Messaging
    | You need to add this to your .env file as FIREBASE_SERVER_KEY
    |
    */
    'server_key' => env('FIREBASE_SERVER_KEY'),
    
    /*
    |--------------------------------------------------------------------------
    | Firebase FCM Endpoint
    |--------------------------------------------------------------------------
    |
    | The Firebase Cloud Messaging endpoint for sending notifications
    |
    */
    'fcm_endpoint' => 'https://fcm.googleapis.com/fcm/send',
];
