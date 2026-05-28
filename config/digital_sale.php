<?php

return [
    /*
    | Venta digital pendiente (reserva hasta registro / código de vinculación):
    | valid_until = fecha del sorteo + wallet_validity_months_after_draw meses.
    | No caduca a las 72 h: el comprador puede vincular meses después de la venta
    | siempre que no hayan pasado esos 3 meses desde el sorteo.
    |
    | Tras esa fecha, la participación en cartera queda caducada (no cobrar, regalar ni usar).
    */
    'wallet_validity_months_after_draw' => (int) env('DIGITAL_SALE_WALLET_VALIDITY_MONTHS', 3),

    /** @deprecated Ya no define valid_until; se mantiene por compatibilidad en .env */
    'hold_hours' => (int) env('DIGITAL_SALE_HOLD_HOURS', 72),
    'hold_days' => (int) env('DIGITAL_SALE_HOLD_DAYS', 0),

    /** Ruta web pública de registro (sin dominio). */
    'registration_path' => 'registro-comprador',

    /** Longitud del código de vinculación (5–8). Sin 0, o ni O en el alfabeto. */
    'link_code_length' => (int) env('DIGITAL_SALE_LINK_CODE_LENGTH', 6),
];
