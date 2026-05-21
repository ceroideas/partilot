<?php

return [
    /*
    | SMS al comprador en ventas digitales pendientes (httpSMS — móvil Android).
    | Si no está activo, la app usa enlace wa.me manual.
    | Documentación: https://docs.httpsms.com/
    */
    'enabled' => (bool) env('DIGITAL_SALE_SMS_ENABLED', false),

    'driver' => env('DIGITAL_SALE_SMS_DRIVER', 'httpsms'),

    'default_country_code' => env('DIGITAL_SALE_SMS_DEFAULT_COUNTRY_CODE', env('SMS_DEFAULT_COUNTRY_CODE', '34')),
];
