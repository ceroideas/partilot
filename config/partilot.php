<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Magic link acceso panel administración (días de caducidad)
    |--------------------------------------------------------------------------
    */
    'panel_magic_link_ttl_days' => (int) env('PARTILOT_PANEL_MAGIC_LINK_TTL_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Búsqueda global de participaciones
    |--------------------------------------------------------------------------
    | Mínimo de caracteres para ejecutar la búsqueda por número de referencia.
    */
    'search_min_chars' => (int) env('PARTILOT_SEARCH_MIN_CHARS', 16),
];
