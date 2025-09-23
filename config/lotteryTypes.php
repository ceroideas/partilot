<?php

return [
    // SORTEOS REGULARES
    '3_J' => [
        'nombre' => 'Sorteo de Jueves',
        'precio_decimo' => 3,
        'codigo_sorteo' => 'J',
        'descripcion' => 'Sorteo Regular de Jueves 3€',
        'es_especial' => false,
        'dia_semana' => 'jueves'
    ],
    
    '6_X' => [
        'nombre' => 'Sorteo de Sábado',
        'precio_decimo' => 6,
        'codigo_sorteo' => 'X',
        'descripcion' => 'Sorteo Regular de Sábado 6€',
        'es_especial' => false,
        'dia_semana' => 'sabado'
    ],
    
    '12_S' => [
        'nombre' => 'Sorteo Extraordinario 12€',
        'precio_decimo' => 12,
        'codigo_sorteo' => 'S',
        'descripcion' => 'Sorteo Extraordinario 12€',
        'es_especial' => false,
        'tipo' => 'extraordinario'
    ],
    
    '15_S' => [
        'nombre' => 'Sorteo Extraordinario 15€',
        'precio_decimo' => 15,
        'codigo_sorteo' => 'S',
        'descripcion' => 'Sorteo Extraordinario 15€',
        'es_especial' => false,
        'tipo' => 'extraordinario'
    ],
    
    // SORTEOS ESPECIALES
    '15_S_ESPECIAL' => [
        'nombre' => 'Sorteo Especial 15€',
        'precio_decimo' => 15,
        'codigo_sorteo' => 'S',
        'descripcion' => 'Sorteo Especial 15€ (130.000€ primer premio)',
        'es_especial' => true,
        'tipo' => 'especial',
        'premio_especial' => 14870000, // Premio a la fracción y serie
        'primer_premio_especial' => 1300000 // 130.000€ en lugar de 150.000€
    ],
    
    '20_N' => [
        'nombre' => 'Sorteo de Navidad',
        'precio_decimo' => 20,
        'codigo_sorteo' => 'N',
        'descripcion' => 'Sorteo Extraordinario de Navidad',
        'es_especial' => true,
        'tipo' => 'navidad',
        'tiene_pedrea' => true
    ],
    
    '20_B' => [
        'nombre' => 'Sorteo del Niño',
        'precio_decimo' => 20,
        'codigo_sorteo' => 'B',
        'descripcion' => 'Sorteo Extraordinario del Niño',
        'es_especial' => true,
        'tipo' => 'nino'
    ],
    
    '20_V' => [
        'nombre' => 'Sorteo de Vacaciones',
        'precio_decimo' => 20,
        'codigo_sorteo' => 'V',
        'descripcion' => 'Sorteo Extraordinario de Vacaciones',
        'es_especial' => true,
        'tipo' => 'vacaciones'
    ],
];
