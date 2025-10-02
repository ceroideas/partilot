<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Optimización de PDFs
    |--------------------------------------------------------------------------
    |
    | Configuraciones para optimizar el rendimiento de generación de PDFs
    | con muchas participaciones.
    |
    */

    // Límites para procesamiento síncrono vs asíncrono
    'sync_limit' => 500,        // Hasta 500 participaciones se procesan síncronamente
    'async_limit' => 1000,      // Más de 1000 participaciones se procesan asíncronamente
    
    // Tamaño de chunks para procesamiento por lotes
    'chunk_size' => 100,        // Procesar de 100 en 100 participaciones
    'job_chunk_size' => 50,     // Para jobs asíncronos, chunks más pequeños
    
    // Configuración de memoria y tiempo
    'memory_limit' => '2048M',  // Límite de memoria para PDFs grandes
    'max_execution_time' => 300, // 5 minutos para PDFs síncronos
    'job_timeout' => 0,         // Sin límite de tiempo para jobs
    
    // Cache
    'cache_ttl' => 3600,        // TTL del cache en segundos (1 hora)
    'cache_prefix' => 'pdf_',   // Prefijo para las claves de cache
    
    // Configuración de archivos temporales
    'temp_path' => 'temp_pdfs/',
    'generated_path' => 'generated_pdfs/',
    'cleanup_temp' => true,     // Limpiar archivos temporales automáticamente
    
    // Configuración de DomPDF
    'dompdf_options' => [
        'defaultFont' => 'Arial',
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
    ],
];
