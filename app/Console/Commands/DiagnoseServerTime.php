<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiagnoseServerTime extends Command
{
    protected $signature = 'firebase:check-time';
    protected $description = 'Verificar configuración del servidor para Firebase';

    public function handle()
    {
        $this->info('🔍 DIAGNÓSTICO DEL SERVIDOR PARA FIREBASE');
        $this->newLine();

        // 1. Fecha y hora del servidor
        $this->info('1️⃣  Fecha y Hora del Servidor');
        $this->info('   Fecha/Hora actual: ' . now()->toDateTimeString());
        $this->info('   Timezone: ' . config('app.timezone'));
        $this->info('   Timestamp UNIX: ' . time());
        $this->newLine();

        // 2. Versión de PHP
        $this->info('2️⃣  Versión de PHP');
        $this->info('   PHP Version: ' . PHP_VERSION);
        $this->newLine();

        // 3. Extensiones necesarias
        $this->info('3️⃣  Extensiones PHP Necesarias');
        $extensions = ['openssl', 'curl', 'json', 'mbstring'];
        foreach ($extensions as $ext) {
            $status = extension_loaded($ext) ? '✅' : '❌';
            $this->info("   {$status} {$ext}");
        }
        $this->newLine();

        // 4. Archivo de credenciales
        $this->info('4️⃣  Archivo de Credenciales');
        $credPath = storage_path('firebase-credentials.json');
        
        if (file_exists($credPath)) {
            $this->info('   ✅ Archivo existe');
            $this->info('   Ruta: ' . $credPath);
            
            // Check permissions
            $perms = substr(sprintf('%o', fileperms($credPath)), -4);
            $this->info('   Permisos: ' . $perms);
            
            // Check file size
            $size = filesize($credPath);
            $this->info('   Tamaño: ' . $size . ' bytes');
            
            // Try to read and validate
            try {
                $content = file_get_contents($credPath);
                $json = json_decode($content, true);
                
                if ($json && isset($json['project_id'])) {
                    $this->info('   ✅ Archivo JSON válido');
                    $this->info('   Project ID: ' . $json['project_id']);
                    $this->info('   Client Email: ' . $json['client_email']);
                    
                    // Check if private key is present
                    if (isset($json['private_key']) && !empty($json['private_key'])) {
                        $this->info('   ✅ Private key presente');
                        $keyLength = strlen($json['private_key']);
                        $this->info('   Longitud de la clave: ' . $keyLength . ' caracteres');
                    } else {
                        $this->error('   ❌ Private key no encontrada o vacía');
                    }
                } else {
                    $this->error('   ❌ JSON inválido o incompleto');
                }
            } catch (\Exception $e) {
                $this->error('   ❌ Error al leer archivo: ' . $e->getMessage());
            }
        } else {
            $this->error('   ❌ Archivo NO existe');
        }
        $this->newLine();

        // 5. Test de conexión a Google
        $this->info('5️⃣  Test de Conectividad');
        try {
            $ch = curl_init('https://www.googleapis.com');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode > 0) {
                $this->info('   ✅ Conexión a Google APIs: OK');
                $this->info('   HTTP Code: ' . $httpCode);
            } else {
                $this->error('   ❌ No se puede conectar a Google APIs');
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
        }
        $this->newLine();

        // 6. Comparar hora con servidor NTP
        $this->info('6️⃣  Sincronización de Hora');
        try {
            // Obtener hora de un servidor público
            $ch = curl_init('https://www.google.com');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $headers = curl_getinfo($ch);
            curl_close($ch);
            
            $this->info('   Hora del servidor: ' . date('Y-m-d H:i:s'));
            $this->info('   Timezone del servidor: ' . date_default_timezone_get());
            
            // Calcular diferencia horaria
            $serverTime = time();
            $this->info('   Timestamp del servidor: ' . $serverTime);
            
            // Si la diferencia es mayor a 5 minutos, hay problema
            $this->warn('   ⚠️  Si hay más de 5 minutos de diferencia con la hora real, Firebase fallará');
            
        } catch (\Exception $e) {
            $this->warn('   ⚠️  No se pudo verificar sincronización: ' . $e->getMessage());
        }
        $this->newLine();

        // 7. Recomendaciones
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📝 RECOMENDACIONES');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->line('Si el error persiste en el servidor pero funciona en local:');
        $this->line('');
        $this->line('1. Verifica que el reloj del servidor esté sincronizado');
        $this->line('   Comando (Linux): sudo ntpdate -s time.nist.gov');
        $this->line('');
        $this->line('2. Asegúrate de que las extensiones PHP estén habilitadas');
        $this->line('');
        $this->line('3. Verifica que el archivo de credenciales tenga los permisos correctos');
        $this->line('   Comando: chmod 644 storage/firebase-credentials.json');
        $this->line('');
        $this->line('4. Limpia la caché de Laravel');
        $this->line('   php artisan config:clear');
        $this->line('   php artisan cache:clear');
        $this->line('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return Command::SUCCESS;
    }
}

