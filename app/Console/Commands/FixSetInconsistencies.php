<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Set;
use App\Models\Participation;
use App\Models\DesignFormat;
use Illuminate\Support\Facades\DB;

class FixSetInconsistencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sets:fix-inconsistencies {--dry-run : Solo mostrar qué se corregiría sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corregir inconsistencias en números de Set y datos asociados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Analizando inconsistencias en Sets...');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se realizarán cambios reales');
        }
        
        $this->newLine();
        
        // 1. Verificar y corregir numeración de Sets
        $this->fixSetNumbering($dryRun);
        
        // 2. Verificar y corregir participaciones huérfanas
        $this->fixOrphanedParticipations($dryRun);
        
        // 3. Verificar y corregir design formats
        $this->fixDesignFormats($dryRun);
        
        // 4. Verificar y corregir códigos de participación
        $this->fixParticipationCodes($dryRun);
        
        // 5. Verificar y corregir totales de participaciones
        $this->fixParticipationTotals($dryRun);
        
        $this->newLine();
        $this->info('✅ Análisis completado');
    }
    
    /**
     * Corregir numeración de Sets
     */
    private function fixSetNumbering($dryRun)
    {
        $this->info('📊 Verificando numeración de Sets...');
        
        $reserves = DB::table('reserves')
            ->select('id')
            ->get();
            
        $fixedCount = 0;
        
        foreach ($reserves as $reserve) {
            $sets = Set::where('reserve_id', $reserve->id)
                ->orderBy('created_at')
                ->get();

            $physicalNumber = 0;
            foreach ($sets as $set) {
                $isDigitalOnly = $set->digital_participations > 0 && (int) ($set->physical_participations ?? 0) === 0;
                $expectedNumber = $isDigitalOnly ? 1 : ++$physicalNumber;
                if ($set->set_number != $expectedNumber) {
                    $this->warn("  Set ID {$set->id}: Número actual {$set->set_number}, esperado {$expectedNumber}");
                    if (!$dryRun) {
                        $set->update(['set_number' => $expectedNumber]);
                        $fixedCount++;
                    }
                }
            }
        }
        
        if ($fixedCount > 0) {
            $this->info("  ✅ Corregidos {$fixedCount} números de Set");
        } else {
            $this->info("  ✅ Numeración de Sets correcta");
        }
    }
    
    /**
     * Corregir participaciones huérfanas
     */
    private function fixOrphanedParticipations($dryRun)
    {
        $this->info('🔗 Verificando participaciones huérfanas...');
        
        // Participaciones sin set
        $orphanedParticipations = Participation::whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('sets')
                  ->whereColumn('sets.id', 'participations.set_id');
        })->count();
        
        if ($orphanedParticipations > 0) {
            $this->warn("  Encontradas {$orphanedParticipations} participaciones sin set asociado");
            
            if (!$dryRun) {
                // Marcar como anuladas las participaciones huérfanas
                Participation::whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('sets')
                          ->whereColumn('sets.id', 'participations.set_id');
                })->update(['status' => 'anulada']);
                
                $this->info("  ✅ Participaciones huérfanas marcadas como anuladas");
            }
        } else {
            $this->info("  ✅ No hay participaciones huérfanas");
        }
    }
    
    /**
     * Corregir design formats
     */
    private function fixDesignFormats($dryRun)
    {
        $this->info('🎨 Verificando design formats...');
        
        // Design formats sin set
        $orphanedDesignFormats = DesignFormat::whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('sets')
                  ->whereColumn('sets.id', 'design_formats.set_id');
        })->count();
        
        if ($orphanedDesignFormats > 0) {
            $this->warn("  Encontrados {$orphanedDesignFormats} design formats sin set asociado");
            
            if (!$dryRun) {
                // Eliminar design formats huérfanos
                DesignFormat::whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('sets')
                          ->whereColumn('sets.id', 'design_formats.set_id');
                })->delete();
                
                $this->info("  ✅ Design formats huérfanos eliminados");
            }
        } else {
            $this->info("  ✅ No hay design formats huérfanos");
        }
    }
    
    /**
     * Corregir códigos de participación
     */
    private function fixParticipationCodes($dryRun)
    {
        $this->info('🏷️  Verificando códigos de participación...');
        
        $sets = Set::with('participations')->get();
        $fixedCount = 0;
        
        foreach ($sets as $set) {
            $isDigitalOnly = $set->digital_participations > 0 && (int) ($set->physical_participations ?? 0) === 0;
            $participations = $set->participations->sortBy('participation_number')->values();
            foreach ($participations as $index => $participation) {
                if ($isDigitalOnly) {
                    $expectedCode = '1D/' . sprintf('%05d', $index + 1);
                } else {
                    $expectedCode = sprintf('%d/%05d', $set->set_number, $participation->participation_number);
                }
                if ($participation->participation_code != $expectedCode) {
                    $this->warn("  Participación ID {$participation->id}: Código actual '{$participation->participation_code}', esperado '{$expectedCode}'");
                    if (!$dryRun) {
                        $participation->update(['participation_code' => $expectedCode]);
                        $fixedCount++;
                    }
                }
            }
        }
        
        if ($fixedCount > 0) {
            $this->info("  ✅ Corregidos {$fixedCount} códigos de participación");
        } else {
            $this->info("  ✅ Códigos de participación correctos");
        }
    }
    
    /**
     * Corregir totales de participaciones
     */
    private function fixParticipationTotals($dryRun)
    {
        $this->info('📈 Verificando totales de participaciones...');
        
        $sets = Set::all();
        $fixedCount = 0;
        
        foreach ($sets as $set) {
            $actualCount = $set->participations()->where('status', '!=', 'anulada')->count();
            
            if ($set->total_participations != $actualCount) {
                $this->warn("  Set ID {$set->id}: Total declarado {$set->total_participations}, real {$actualCount}");
                
                if (!$dryRun) {
                    $set->update(['total_participations' => $actualCount]);
                    $fixedCount++;
                }
            }
        }
        
        if ($fixedCount > 0) {
            $this->info("  ✅ Corregidos {$fixedCount} totales de participaciones");
        } else {
            $this->info("  ✅ Totales de participaciones correctos");
        }
    }
}