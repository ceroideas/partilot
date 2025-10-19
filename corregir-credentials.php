<?php

/**
 * Script para corregir el formato de la clave privada en firebase-credentials.json
 */

echo "========================================\n";
echo "  CORRECTOR DE CREDENCIALES FIREBASE\n";
echo "========================================\n\n";

$credentialsPath = 'storage/firebase-credentials.json';

try {
    // 1. Leer el archivo actual
    echo "1️⃣  LEYENDO ARCHIVO ACTUAL\n";
    echo "   -----------------------\n";
    
    if (!file_exists($credentialsPath)) {
        throw new Exception("Archivo de credenciales no encontrado: $credentialsPath");
    }
    
    $content = file_get_contents($credentialsPath);
    $credentials = json_decode($content, true);
    
    if (!$credentials) {
        throw new Exception("Error al decodificar JSON");
    }
    
    echo "   ✅ Archivo leído correctamente\n";
    echo "   ✅ JSON válido\n\n";
    
    // 2. Verificar si tiene \n literales
    echo "2️⃣  VERIFICANDO FORMATO\n";
    echo "   ---------------------\n";
    
    $privateKey = $credentials['private_key'];
    
    if (strpos($privateKey, '\\n') !== false) {
        echo "   ❌ PROBLEMA DETECTADO: Clave privada tiene \\n literales\n";
        echo "   🔧 Corrigiendo formato...\n";
        
        // Corregir el formato
        $credentials['private_key'] = str_replace('\\n', "\n", $privateKey);
        
        echo "   ✅ Formato corregido\n\n";
        
        // 3. Crear backup
        echo "3️⃣  CREANDO BACKUP\n";
        echo "   ---------------\n";
        
        $backupPath = $credentialsPath . '.backup.' . date('Y-m-d-H-i-s');
        file_put_contents($backupPath, $content);
        echo "   ✅ Backup creado: $backupPath\n\n";
        
        // 4. Guardar archivo corregido
        echo "4️⃣  GUARDANDO ARCHIVO CORREGIDO\n";
        echo "   -----------------------------\n";
        
        $correctedContent = json_encode($credentials, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($credentialsPath, $correctedContent);
        
        echo "   ✅ Archivo corregido guardado\n\n";
        
        // 5. Verificar que se corrigió
        echo "5️⃣  VERIFICACIÓN FINAL\n";
        echo "   -------------------\n";
        
        $newContent = file_get_contents($credentialsPath);
        $newCredentials = json_decode($newContent, true);
        $newPrivateKey = $newCredentials['private_key'];
        
        if (strpos($newPrivateKey, '\\n') === false) {
            echo "   ✅ ¡CORRECCIÓN EXITOSA!\n";
            echo "   ✅ La clave privada ahora tiene saltos de línea reales\n\n";
        } else {
            echo "   ❌ Error: La corrección no funcionó\n\n";
        }
        
    } else {
        echo "   ✅ El archivo ya tiene el formato correcto\n";
        echo "   ✅ No se necesitan correcciones\n\n";
    }
    
    // 6. Mostrar resumen
    echo "6️⃣  RESUMEN\n";
    echo "   -------\n";
    echo "   📁 Archivo: $credentialsPath\n";
    if (isset($backupPath)) {
        echo "   📁 Backup: $backupPath\n";
    }
    echo "   ✅ Formato de clave privada: CORRECTO\n\n";
    
    echo "🎯 PRÓXIMO PASO:\n";
    echo "   Ejecuta: php test-firebase-real.php\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo str_repeat("=", 40) . "\n";
echo "CORRECCIÓN COMPLETADA\n";
echo str_repeat("=", 40) . "\n";

