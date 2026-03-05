<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
     * API externa para generar códigos prepago (recarga) usables en la web de loterías.
     */
    'prepago_codigos' => [
        'url' => env('PREPAGO_CODIGOS_URL', 'https://www.test.elbuholotero.es/css/plugins/include/crea_codigos_prepago.php'),
        'apikey' => env('PREPAGO_CODIGOS_APIKEY'),
        'prefijo' => env('PREPAGO_CODIGOS_PREFIJO', 'c-'),
        'n_codigos' => 1,
        'tamano_cadena' => 8,
        'accion' => 'generarCodigosRnd',
    ],

];
