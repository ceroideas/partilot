<?php

return [
    /*
    | Bloquear creación/edición de reservas, sets, diseños, etc. cuando draw_date ya pasó.
    | false = modo depuración (permite operar con sorteos pasados).
    | true  = modo real (recomendado en producción).
    */
    'enforce_draw_date_rules' => filter_var(
        env('LOTTERY_ENFORCE_DRAW_DATE_RULES', true),
        FILTER_VALIDATE_BOOLEAN
    ),

    /** Mensaje genérico si no hay fecha de sorteo en el modelo. */
    'draw_date_passed_message' => 'No se puede continuar: la fecha del sorteo ya ha pasado.',
];
