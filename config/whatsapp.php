<?php

return [
    /*
    | DEPRECADO: ya no se usa. Fallback manual = wa.me en la app.
    | Envío WhatsApp (Twilio) al comprador en ventas digitales pendientes.
    | El vendedor no ve el código en la app; el mensaje se construye y envía en servidor.
    */
    'enabled' => (bool) env('WHATSAPP_ENABLED', false),

    'default_country_code' => env('WHATSAPP_DEFAULT_COUNTRY_CODE', env('SMS_DEFAULT_COUNTRY_CODE', '34')),

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
        // Plantilla aprobada (opcional). Si WHATSAPP_USE_TEMPLATE=true, se usa contentSid.
        'content_sid' => env('TWILIO_WHATSAPP_CONTENT_SID'),
    ],

    'use_template' => (bool) env('WHATSAPP_USE_TEMPLATE', false),
];
