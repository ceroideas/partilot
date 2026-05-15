<?php

return [
    /*
    | Tiempo de reserva de participaciones digitales hasta que el comprador se registre.
    | Usa DIGITAL_SALE_HOLD_HOURS o DIGITAL_SALE_HOLD_DAYS (días tiene prioridad si > 0).
    |
    | Caducidad: columna valid_until. Se comprueba al vender, consultar disponibles,
    | abrir el enlace de registro o registrarse (sin cron ni comando artisan).
    */
    'hold_hours' => (int) env('DIGITAL_SALE_HOLD_HOURS', 72),
    'hold_days' => (int) env('DIGITAL_SALE_HOLD_DAYS', 0),

    /** Ruta web pública de registro (sin dominio). */
    'registration_path' => 'registro-comprador',
];
