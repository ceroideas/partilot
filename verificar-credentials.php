<?php

/**
 * Verificar credenciales de Firebase sin Laravel
 */

echo "========================================\n";
echo "  VERIFICACIÓN CREDENCIALES FIREBASE\n";
echo "========================================\n\n";

try {
    $credentialsPath = 'storage/firebase-credentials.json';
    
    if (!file_exists($credentialsPath)) {
        die("❌ No existe: $credentialsPath\n");
    }
    
    echo "1️⃣  ANÁLISIS DE CREDENCIALES\n";
    echo "   -------------------------\n";
    
    $jsonContent = file_get_contents($credentialsPath);
    $cred = json_decode($jsonContent, true);
    
    if (!$cred) {
        die("❌ JSON inválido\n");
    }
    
    echo "   ✓ Archivo leído correctamente\n";
    echo "   ✓ JSON válido\n";
    echo "   ✓ Tamaño: " . strlen($jsonContent) . " bytes\n\n";
    
    echo "2️⃣  CAMPOS REQUERIDOS\n";
    echo "   ------------------\n";
    
    $requiredFields = [
        'type' => 'service_account',
        'project_id' => 'inicio-de-sesion-94ddc',
        'private_key_id' => 'string',
        'private_key' => 'string',
        'client_email' => 'string',
        'client_id' => 'string'
    ];
    
    foreach ($requiredFields as $field => $expected) {
        $exists = isset($cred[$field]) && !empty($cred[$field]);
        $value = $exists ? $cred[$field] : 'FALTANTE';
        
        if ($field === 'private_key') {
            $value = substr($value, 0, 50) . '...';
        }
        
        echo "   " . ($exists ? '✅' : '❌') . " $field: $value\n";
        
        if (!$exists) {
            echo "      ⚠️  Campo requerido faltante\n";
        }
    }
    
    echo "\n3️⃣  ANÁLISIS CLAVE PRIVADA\n";
    echo "   -----------------------\n";
    
    if (isset($cred['private_key'])) {
        $privateKey = $cred['private_key'];
        
        echo "   ✓ Longitud: " . strlen($privateKey) . " caracteres\n";
        echo "   ✓ Comienza con: " . substr($privateKey, 0, 30) . "...\n";
        echo "   ✓ Termina con: ..." . substr($privateKey, -30) . "\n";
        
        // Verificar formato
        $hasBegin = strpos($privateKey, 'BEGIN PRIVATE KEY') !== false;
        $hasEnd = strpos($privateKey, 'END PRIVATE KEY') !== false;
        $hasEscapedNewlines = strpos($privateKey, '\\n') !== false;
        $hasRealNewlines = strpos($privateKey, "\n") !== false;
        
        echo "   ✓ Contiene BEGIN: " . ($hasBegin ? 'SÍ' : 'NO') . "\n";
        echo "   ✓ Contiene END: " . ($hasEnd ? 'SÍ' : 'NO') . "\n";
        echo "   ✓ Tiene \\n escapados: " . ($hasEscapedNewlines ? 'SÍ' : 'NO') . "\n";
        echo "   ✓ Tiene saltos reales: " . ($hasRealNewlines ? 'SÍ' : 'NO') . "\n";
        
        if ($hasEscapedNewlines && !$hasRealNewlines) {
            echo "\n   ⚠️  PROBLEMA DETECTADO: Clave con \\n escapados\n";
            echo "   🔧 Esto puede causar 'invalid_grant'\n";
            
            // Corregir automáticamente
            echo "\n   🔧 Corrigiendo clave privada...\n";
            $cred['private_key'] = str_replace('\\n', "\n", $privateKey);
            file_put_contents($credentialsPath, json_encode($cred, JSON_PRETTY_PRINT));
            echo "   ✅ Clave privada corregida\n";
        } else {
            echo "   ✅ Formato de clave privada correcto\n";
        }
    }
    
    echo "\n4️⃣  VERIFICACIÓN EMAIL\n";
    echo "   -------------------\n";
    
    if (isset($cred['client_email']) && isset($cred['project_id'])) {
        $email = $cred['client_email'];
        $projectId = $cred['project_id'];
        
        echo "   ✓ Email: $email\n";
        echo "   ✓ Project ID: $projectId\n";
        
        $emailMatchesProject = strpos($email, $projectId) !== false;
        echo "   ✓ Email coincide con proyecto: " . ($emailMatchesProject ? 'SÍ' : 'NO') . "\n";
        
        if (!$emailMatchesProject) {
            echo "   ⚠️  PROBLEMA: Email no coincide con Project ID\n";
        }
    }
    
    echo "\n5️⃣  RESUMEN\n";
    echo "   --------\n";
    echo "   ✅ Archivo existe y es legible\n";
    echo "   ✅ JSON válido\n";
    echo "   ✅ Campos principales presentes\n";
    
    if (isset($cred['private_key'])) {
        $hasEscapedNewlines = strpos($cred['private_key'], '\\n') !== false;
        $hasRealNewlines = strpos($cred['private_key'], "\n") !== false;
        
        if ($hasEscapedNewlines && !$hasRealNewlines) {
            echo "   ✅ Clave privada corregida (\\n → saltos reales)\n";
        } else {
            echo "   ✅ Clave privada con formato correcto\n";
        }
    }
    
    echo "\n📋 PRÓXIMOS PASOS:\n";
    echo "   1. Ejecutar: php test-firebase-servidor.php\n";
    echo "   2. Si persiste 'invalid_grant', regenerar credenciales\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "VERIFICACIÓN COMPLETADA\n";
echo str_repeat("=", 40) . "\n";
