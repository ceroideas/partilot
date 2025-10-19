<?php

/**
 * Verificar la clave privada de Firebase
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "========================================\n";
echo "  VERIFICACIÓN CLAVE PRIVADA FIREBASE\n";
echo "========================================\n\n";

try {
    $credentialsPath = storage_path('firebase-credentials.json');
    $cred = json_decode(file_get_contents($credentialsPath), true);
    
    echo "1️⃣  ANÁLISIS DE LA CLAVE PRIVADA\n";
    echo "   -----------------------------\n";
    
    $privateKey = $cred['private_key'];
    
    // Verificar formato de la clave
    echo "   ✓ Clave comienza con: " . substr($privateKey, 0, 30) . "...\n";
    echo "   ✓ Clave termina con: ..." . substr($privateKey, -30) . "\n";
    echo "   ✓ Contiene BEGIN: " . (strpos($privateKey, 'BEGIN PRIVATE KEY') !== false ? 'SÍ' : 'NO') . "\n";
    echo "   ✓ Contiene END: " . (strpos($privateKey, 'END PRIVATE KEY') !== false ? 'SÍ' : 'NO') . "\n";
    
    // Contar saltos de línea
    $lineBreaks = substr_count($privateKey, "\n");
    echo "   ✓ Saltos de línea: $lineBreaks\n";
    
    // Verificar si tiene \n o \\n
    $hasEscapedNewlines = strpos($privateKey, '\\n') !== false;
    $hasRealNewlines = strpos($privateKey, "\n") !== false;
    
    echo "   ✓ Tiene \\n escapados: " . ($hasEscapedNewlines ? 'SÍ' : 'NO') . "\n";
    echo "   ✓ Tiene saltos reales: " . ($hasRealNewlines ? 'SÍ' : 'NO') . "\n";
    
    if ($hasEscapedNewlines && !$hasRealNewlines) {
        echo "   ⚠️  PROBLEMA DETECTADO: La clave tiene \\n escapados en lugar de saltos reales\n";
        echo "   🔧 SOLUCIÓN: Reemplazar \\n con saltos de línea reales\n\n";
        
        // Corregir automáticamente
        $fixedKey = str_replace('\\n', "\n", $privateKey);
        $cred['private_key'] = $fixedKey;
        
        echo "   🔧 Corrigiendo clave privada...\n";
        file_put_contents($credentialsPath, json_encode($cred, JSON_PRETTY_PRINT));
        echo "   ✅ Clave privada corregida\n\n";
        
    } else {
        echo "   ✅ Formato de clave privada correcto\n\n";
    }
    
    echo "2️⃣  VERIFICACIÓN DE CAMPOS REQUERIDOS\n";
    echo "   -----------------------------------\n";
    
    $requiredFields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id'];
    foreach ($requiredFields as $field) {
        $exists = isset($cred[$field]) && !empty($cred[$field]);
        echo "   " . ($exists ? '✅' : '❌') . " $field: " . ($exists ? 'Presente' : 'Faltante') . "\n";
    }
    
    echo "\n3️⃣  VERIFICACIÓN DE PROYECTO\n";
    echo "   -------------------------\n";
    echo "   ✓ Project ID: {$cred['project_id']}\n";
    echo "   ✓ Client Email: {$cred['client_email']}\n";
    
    // Verificar si el email coincide con el project_id
    $expectedEmail = "firebase-adminsdk-xxxxx@{$cred['project_id']}.iam.gserviceaccount.com";
    $actualEmail = $cred['client_email'];
    
    echo "   ✓ Email formato correcto: " . (strpos($actualEmail, $cred['project_id']) !== false ? 'SÍ' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "VERIFICACIÓN COMPLETADA\n";
echo str_repeat("=", 40) . "\n";
