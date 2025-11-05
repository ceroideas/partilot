<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Set;
use App\Models\DesignFormat;
use App\Models\Participation;
use Illuminate\Support\Facades\DB;

class SyncSetOutputConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sets:sync-output-config {--dry-run : Solo mostrar qué se sincronizaría sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar configuraciones de salida con los sets para coincidir número de participaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔄 Sincronizando configuraciones de salida con sets...');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se realizarán cambios reales');
        }
        
        $this->newLine();
        
        // 1. Sincronizar design formats con sets
        $this->syncDesignFormats($dryRun);
        
        // 2. Verificar y corregir participaciones por taco
        $this->syncParticipationsPerBook($dryRun);
        
        // 3. Verificar y corregir totales de participaciones
        $this->syncParticipationTotals($dryRun);
        
        // 4. Verificar y corregir códigos de participación
        $this->syncParticipationCodes($dryRun);
        
        $this->newLine();
        $this->info('✅ Sincronización completada');
    }
    
    /**
     * Sincronizar design formats con sets
     */
    private function syncDesignFormats($dryRun)
    {
        $this->info('🎨 Sincronizando design formats con sets...');
        
        $designFormats = DesignFormat::with('set')->get();
        $syncedCount = 0;
        
        foreach ($designFormats as $designFormat) {
            if (!$designFormat->set) {
                $this->warn("  DesignFormat ID {$designFormat->id}: Sin set asociado");
                continue;
            }
            
            $set = $designFormat->set;
            $output = is_string($designFormat->output) ? json_decode($designFormat->output, true) : $designFormat->output;
            
            if (!$output) {
                $this->warn("  DesignFormat ID {$designFormat->id}: Output vacío o inválido");
                continue;
            }
            
            // Verificar si el output tiene la estructura correcta
            $needsUpdate = false;
            $newOutput = $output;
            
            // Asegurar que participations_per_book esté definido
            if (!isset($output['participations_per_book']) || $output['participations_per_book'] <= 0) {
                $newOutput['participations_per_book'] = 50; // Valor por defecto
                $needsUpdate = true;
            }
            
            // Asegurar que el formato tenga la estructura correcta
            if (!isset($output['format_type'])) {
                $newOutput['format_type'] = 'standard';
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $this->warn("  DesignFormat ID {$designFormat->id}: Actualizando configuración de salida");
                
                if (!$dryRun) {
                    $designFormat->update(['output' => $newOutput]);
                    $syncedCount++;
                }
            }
        }
        
        if ($syncedCount > 0) {
            $this->info("  ✅ Sincronizados {$syncedCount} design formats");
        } else {
            $this->info("  ✅ Design formats ya sincronizados");
        }
    }
    
    /**
     * Sincronizar participaciones por taco
     */
    private function syncParticipationsPerBook($dryRun)
    {
        $this->info('📚 Sincronizando participaciones por taco...');
        
        $sets = Set::with(['designFormats', 'participations'])->get();
        $syncedCount = 0;
        
        foreach ($sets as $set) {
            $designFormat = $set->designFormats->first();
            if (!$designFormat) {
                continue;
            }
            
            $output = is_string($designFormat->output) ? json_decode($designFormat->output, true) : $designFormat->output;
            $participationsPerBook = $output['participations_per_book'] ?? 50;
            
            // Calcular el número real de participaciones por taco basado en las participaciones existentes
            $actualParticipations = $set->participations()->where('status', '!=', 'anulada')->count();
            $actualBooks = ceil($actualParticipations / $participationsPerBook);
            $expectedBooks = ceil($set->total_participations / $participationsPerBook);
            
            if ($actualBooks != $expectedBooks) {
                $this->warn("  Set ID {$set->id}: Libros esperados {$expectedBooks}, reales {$actualBooks}");
                
                // Ajustar participaciones por taco si es necesario
                $newParticipationsPerBook = ceil($actualParticipations / $expectedBooks);
                
                if ($newParticipationsPerBook != $participationsPerBook) {
                    $this->warn("  Set ID {$set->id}: Ajustando participaciones por taco de {$participationsPerBook} a {$newParticipationsPerBook}");
                    
                    if (!$dryRun) {
                        $newOutput = $output;
                        $newOutput['participations_per_book'] = $newParticipationsPerBook;
                        $designFormat->update(['output' => $newOutput]);
                        $syncedCount++;
                    }
                }
            }
        }
        
        if ($syncedCount > 0) {
            $this->info("  ✅ Sincronizados {$syncedCount} sets");
        } else {
            $this->info("  ✅ Participaciones por taco ya sincronizadas");
        }
    }
    
    /**
     * Sincronizar totales de participaciones
     */
    private function syncParticipationTotals($dryRun)
    {
        $this->info('📊 Sincronizando totales de participaciones...');
        
        $sets = Set::with('participations')->get();
        $syncedCount = 0;
        
        foreach ($sets as $set) {
            $actualCount = $set->participations()->where('status', '!=', 'anulada')->count();
            
            if ($set->total_participations != $actualCount) {
                $this->warn("  Set ID {$set->id}: Total declarado {$set->total_participations}, real {$actualCount}");
                
                if (!$dryRun) {
                    $set->update(['total_participations' => $actualCount]);
                    $syncedCount++;
                }
            }
        }
        
        if ($syncedCount > 0) {
            $this->info("  ✅ Sincronizados {$syncedCount} totales de participaciones");
        } else {
            $this->info("  ✅ Totales de participaciones ya sincronizados");
        }
    }
    
    /**
     * Sincronizar códigos de participación
     */
    private function syncParticipationCodes($dryRun)
    {
        $this->info('🏷️  Sincronizando códigos de participación...');
        
        $sets = Set::with('participations')->get();
        $syncedCount = 0;
        
        foreach ($sets as $set) {
            $setNumber = $set->set_number;
            $participations = $set->participations()->where('status', '!=', 'anulada')->orderBy('participation_number')->get();
            
            foreach ($participations as $index => $participation) {
                $expectedCode = sprintf('%d/%05d', $setNumber, $participation->participation_number);
                
                if ($participation->participation_code != $expectedCode) {
                    $this->warn("  Participación ID {$participation->id}: Código actual '{$participation->participation_code}', esperado '{$expectedCode}'");
                    
                    if (!$dryRun) {
                        $participation->update(['participation_code' => $expectedCode]);
                        $syncedCount++;
                    }
                }
            }
        }
        
        if ($syncedCount > 0) {
            $this->info("  ✅ Sincronizados {$syncedCount} códigos de participación");
        } else {
            $this->info("  ✅ Códigos de participación ya sincronizados");
        }
    }
}