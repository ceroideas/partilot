<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Set;
use App\Models\DesignFormat;
use App\Models\Participation;
use Illuminate\Support\Facades\DB;

class GenerateParticipations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'participations:generate {--set-id= : ID específico del set} {--force : Forzar regeneración}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar participaciones automáticamente desde los datos existentes en sets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando generación de participaciones...');

        $setId = $this->option('set-id');
        $force = $this->option('force');

        // Si se especifica un set específico
        if ($setId) {
            $set = Set::find($setId);
            if (!$set) {
                $this->error("Set con ID {$setId} no encontrado.");
                return 1;
            }
            $sets = collect([$set]);
        } else {
            // Obtener todos los sets que tienen design_formats
            $sets = Set::whereHas('designFormats')->get();
        }

        if ($sets->isEmpty()) {
            $this->warn('No se encontraron sets con design_formats.');
            return 0;
        }

        $this->info("Procesando {$sets->count()} sets...");

        $totalParticipations = 0;
        $createdParticipations = 0;

        foreach ($sets as $set) {
            $this->info("Procesando Set ID: {$set->id} - {$set->set_name}");

            // Obtener el designFormat asociado
            $designFormat = $set->designFormats->first();
            if (!$designFormat) {
                $this->warn("  No se encontró design_format para el set {$set->id}");
                continue;
            }

            // Verificar si ya existen participaciones para este set
            $existingCount = Participation::where('set_id', $set->id)->count();
            if ($existingCount > 0 && !$force) {
                $this->info("  Ya existen {$existingCount} participaciones para el set {$set->id}. Se actualizarán si es necesario.");
            }

            // Si force está activado, eliminar participaciones existentes
            if ($force && $existingCount > 0) {
                Participation::where('set_id', $set->id)->delete();
                $this->info("  Eliminadas {$existingCount} participaciones existentes.");
            }

            // Calcular información del set
            $setNumber = $this->getSetNumber($set);
            $totalParticipationsSet = $set->total_participations ?? 0;

            if ($totalParticipationsSet <= 0) {
                $this->warn("  El set {$set->id} no tiene participaciones totales definidas.");
                continue;
            }

            // Obtener participaciones por taco desde el designFormat
            $output = is_string($designFormat->output) ? json_decode($designFormat->output, true) : $designFormat->output;
            $participationsPerBook = $output['participations_per_book'] ?? 50;

            $this->info("  Total participaciones: {$totalParticipationsSet}");
            $this->info("  Participaciones por taco: {$participationsPerBook}");
            $this->info("  Número de set: {$setNumber}");

            // Calcular cuántos tacos necesitamos
            $totalBooks = ceil($totalParticipationsSet / $participationsPerBook);

            $participationsToCreate = [];

            // Obtener el rango de números de participación global para este set
            $participationRange = $set->getParticipationNumberRange();
            $globalStartNumber = $participationRange['start'];

            for ($participationNumber = 1; $participationNumber <= $totalParticipationsSet; $participationNumber++) {
                // Calcular a qué taco pertenece esta participación
                $bookNumber = ceil($participationNumber / $participationsPerBook);
                
                // Calcular el número global de participación
                $globalParticipationNumber = $globalStartNumber + $participationNumber - 1;
                
                // Generar código de participación con numeración global
                $participationCode = sprintf('%d/%05d', $setNumber, $globalParticipationNumber);

                $participationsToCreate[] = [
                    'entity_id' => $set->entity_id,
                    'set_id' => $set->id,
                    'design_format_id' => $designFormat->id,
                    'participation_number' => $globalParticipationNumber, // Usar número global
                    'participation_code' => $participationCode,
                    'book_number' => $bookNumber,
                    'status' => 'disponible',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insertar en lotes de 1000 para mejor rendimiento
                if (count($participationsToCreate) >= 1000) {
                    Participation::upsert($participationsToCreate, ['set_id', 'participation_number'], [
                        'participation_code', 'book_number', 'status', 'updated_at'
                    ]);
                    $createdParticipations += count($participationsToCreate);
                    $participationsToCreate = [];
                    $this->info("  Creadas " . $createdParticipations . " participaciones...");
                }
            }

            // Insertar las participaciones restantes
            if (!empty($participationsToCreate)) {
                Participation::upsert($participationsToCreate, ['set_id', 'participation_number'], [
                    'participation_code', 'book_number', 'status', 'updated_at'
                ]);
                $createdParticipations += count($participationsToCreate);
            }

            $totalParticipations += $totalParticipationsSet;
            $this->info("  ✅ Set {$set->id} completado. Creadas {$totalParticipationsSet} participaciones.");
        }

        $this->info("\n🎉 Generación completada!");
        $this->info("Total de participaciones procesadas: {$createdParticipations}");
        $this->info("Total de sets procesados: {$sets->count()}");

        return 0;
    }

    /**
     * Obtener el número de set basado en la fecha de creación
     */
    private function getSetNumber($set)
    {
        // Contar cuántos sets hay para la misma reserva, ordenados por fecha de creación
        $setNumber = Set::where('reserve_id', $set->reserve_id)
            ->where('created_at', '<=', $set->created_at)
            ->count();
        
        return $setNumber;
    }
}
