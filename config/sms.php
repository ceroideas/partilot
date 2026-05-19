<?php

return [
    /*
    | Verificación OTP por SMS en registro (app y web comprador).
    | Con driver "log" el código aparece en storage/logs/laravel.log (desarrollo).
    | Producción: SMS_DRIVER=twilio y credenciales Twilio.
    */
    'enabled' => (bool) env('SMS_VERIFICATION_ENABLED', false),
    'driver' => env('SMS_DRIVER', 'log'),
    'code_length' => (int) env('SMS_CODE_LENGTH', 6),
    'code_ttl_minutes' => (int) env('SMS_CODE_TTL_MINUTES', 10),
    'resend_cooldown_seconds' => (int) env('SMS_RESEND_COOLDOWN_SECONDS', 60),
    'default_country_code' => env('SMS_DEFAULT_COUNTRY_CODE', '34'),

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'vonage' => [
        'key' => env('VONAGE_API_KEY'),
        'secret' => env('VONAGE_API_SECRET'),
        'from' => env('VONAGE_FROM'),
    ],
];
