<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;
use App\Models\Set;
use App\Models\Participation;

class DesignFormat extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'set_id',
        'format',
        'page',
        'rows',
        'cols',
        'orientation',
        'margin_up',
        'margin_right',
        'margin_left',
        'margin_top',
        'identation',
        'matrix_box',
        'page_rigth',
        'page_bottom',
        'guide_color',
        'guide_weight',
        'participation_number',
        'participation_from',
        'participation_to',
        'participation_page',
        'guides',
        'generate',
        'documents',
        'blocks',
        'participation_html',
        'vertical_space',
        'horizontal_space',
        'margin_custom',
        'cover_html',
        'back_html',
        'backgrounds',
        'margins',
        'output',
    ];

    protected $casts = [
        'blocks' => 'array',
        'guides' => 'boolean',
        'backgrounds' => 'array',
        'output' => 'array',
        'margins' => 'array',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    /**
     * Boot del modelo para eventos automáticos
     */
    protected static function boot()
    {
        parent::boot();

        // Cuando se crea un DesignFormat, generar participaciones automáticamente
        static::created(function ($designFormat) {
            $designFormat->generateParticipations();
        });

        // Cuando se actualiza un DesignFormat, actualizar participaciones si es necesario
        static::updated(function ($designFormat) {
            if ($designFormat->wasChanged(['output', 'participation_from', 'participation_to'])) {
                return $designFormat->updateParticipations();
            }
        });

        // Cuando se elimina un DesignFormat, eliminar participaciones asociadas
        static::deleted(function ($designFormat) {
            $designFormat->deleteParticipations();
        });
    }

    /**
     * Generar participaciones automáticamente
     */
    public function generateParticipations()
    {
        // Primero eliminar participaciones existentes para evitar duplicados
        $this->deleteParticipations();
        
        try {
            \Log::info('Iniciando generación de participaciones para DesignFormat ID: ' . $this->id);
            
            // Validar relaciones antes de proceder
            $validationErrors = $this->validateRelationships();
            if (!empty($validationErrors)) {
                \Log::error('Errores de validación: ' . implode(', ', $validationErrors));
                return false;
            }
            
            if (!$this->set) {
                \Log::warning('No se encontró el set asociado al DesignFormat ID: ' . $this->id);
                return false;
            }

            $totalParticipations = $this->set->total_participations ?? 0;
            \Log::info('Total de participaciones a generar: ' . $totalParticipations);
            
            if ($totalParticipations <= 0) {
                \Log::warning('El total de participaciones es 0 o menor para Set ID: ' . $this->set_id);
                return false;
            }

            // Obtener participaciones por taco desde el output
            $output = is_string($this->output) ? json_decode($this->output, true) : $this->output;
            $participationsPerBook = $output['participations_per_book'] ?? 50;
            
            \Log::info('Participaciones por taco: ' . $participationsPerBook);

            // Obtener número de set
            $setNumber = $this->getSetNumber();
            \Log::info('Número de set calculado: ' . $setNumber);

            // Verificar que los IDs necesarios existan
            if (!$this->entity_id || !$this->set_id || !$this->id) {
                \Log::error('Faltan IDs requeridos - entity_id: ' . $this->entity_id . ', set_id: ' . $this->set_id . ', design_format_id: ' . $this->id);
                return false;
            }

            $participationsToCreate = [];
            $totalCreated = 0;

            for ($participationNumber = 1; $participationNumber <= $totalParticipations; $participationNumber++) {
                // Calcular a qué taco pertenece esta participación
                $bookNumber = ceil($participationNumber / $participationsPerBook);
                
                // Generar código de participación
                $participationCode = sprintf('%d/%05d', $setNumber, $participationNumber);

                $participationData = [
                    'entity_id' => $this->entity_id,
                    'set_id' => $this->set_id,
                    'design_format_id' => $this->id,
                    'participation_number' => $participationNumber,
                    'participation_code' => $participationCode,
                    'book_number' => $bookNumber,
                    'status' => 'disponible',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $participationsToCreate[] = $participationData;

                // Insertar en lotes de 100 para mejor rendimiento (más pequeño para debugging)
                if (count($participationsToCreate) >= 100) {
                    try {
                        // Verificar si hay códigos duplicados antes de insertar
                        $codesToInsert = array_column($participationsToCreate, 'participation_code');
                        $existingCodes = Participation::whereIn('participation_code', $codesToInsert)
                            ->where('design_format_id', $this->id)
                            ->pluck('participation_code')->toArray();
                        
                        if (!empty($existingCodes)) {
                            \Log::warning('Códigos de participación ya existen para este design format: ' . implode(', ', $existingCodes));
                            // Eliminar solo las participaciones existentes de este design format con estos códigos
                            Participation::whereIn('participation_code', $existingCodes)
                                ->where('design_format_id', $this->id)
                                ->delete();
                            \Log::info('Eliminadas participaciones duplicadas de este design format');
                        }
                        
                        // Usar insert en lugar de upsert para evitar conflictos de duplicados
                        $result = Participation::insert($participationsToCreate);
                        $totalCreated += count($participationsToCreate);
                        \Log::info('Insertado lote de ' . count($participationsToCreate) . ' participaciones. Total creadas: ' . $totalCreated);
                        $participationsToCreate = [];
                    } catch (\Exception $e) {
                        \Log::error('Error al insertar lote de participaciones: ' . $e->getMessage());
                        \Log::error('Datos del lote: ' . json_encode($participationsToCreate));
                        throw $e;
                    }
                }
            }

            // Insertar las participaciones restantes
            if (!empty($participationsToCreate)) {
                try {
                    // Verificar si hay códigos duplicados antes de insertar
                    $codesToInsert = array_column($participationsToCreate, 'participation_code');
                    $existingCodes = Participation::whereIn('participation_code', $codesToInsert)
                        ->where('design_format_id', $this->id)
                        ->pluck('participation_code')->toArray();
                    
                    if (!empty($existingCodes)) {
                        \Log::warning('Códigos de participación ya existen en lote final para este design format: ' . implode(', ', $existingCodes));
                        // Eliminar solo las participaciones existentes de este design format con estos códigos
                        Participation::whereIn('participation_code', $existingCodes)
                            ->where('design_format_id', $this->id)
                            ->delete();
                        \Log::info('Eliminadas participaciones duplicadas del lote final de este design format');
                    }
                    
                    // Usar insert en lugar de upsert para evitar conflictos de duplicados
                    $result = Participation::insert($participationsToCreate);
                    $totalCreated += count($participationsToCreate);
                    \Log::info('Insertado lote final de ' . count($participationsToCreate) . ' participaciones. Total creadas: ' . $totalCreated);
                } catch (\Exception $e) {
                    \Log::error('Error al insertar lote final de participaciones: ' . $e->getMessage());
                    \Log::error('Datos del lote final: ' . json_encode($participationsToCreate));
                    throw $e;
                }
            }

            \Log::info('Generación de participaciones completada. Total creadas: ' . $totalCreated);
            
            // Verificar que las participaciones se crearon correctamente
            $actualCount = Participation::where('design_format_id', $this->id)->count();
            \Log::info('Verificación: participaciones en BD para DesignFormat ID ' . $this->id . ': ' . $actualCount);
            
            if ($actualCount !== $totalCreated) {
                \Log::warning('Discrepancia: se esperaban ' . $totalCreated . ' participaciones pero se encontraron ' . $actualCount . ' en la BD');
            }
            
            return $totalCreated;

        } catch (\Exception $e) {
            \Log::error('Error en generateParticipations: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Actualizar participaciones existentes
     */
    public function updateParticipations()
    {
        // Eliminar participaciones existentes
        $this->deleteParticipations();
        
        // Generar nuevas participaciones
        return $this->generateParticipations();
    }

    /**
     * Eliminar participaciones asociadas
     */
    public function deleteParticipations()
    {
        try {
            // Eliminar participaciones del design format actual
            $deleted = Participation::where('design_format_id', $this->id)->delete();
            \Log::info('Eliminadas ' . $deleted . ' participaciones existentes para DesignFormat ID: ' . $this->id);
            
            return $deleted;
        } catch (\Exception $e) {
            \Log::error('Error al eliminar participaciones: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Método de prueba para crear una sola participación
     */
    public function testCreateSingleParticipation()
    {
        try {
            $setNumber = $this->getSetNumber();
            $participationCode = sprintf('%d/%05d', $setNumber, 1);
            
            $participationData = [
                'entity_id' => $this->entity_id,
                'set_id' => $this->set_id,
                'design_format_id' => $this->id,
                'participation_number' => 1,
                'participation_code' => $participationCode,
                'book_number' => 1,
                'status' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            \Log::info('Intentando crear participación de prueba: ' . json_encode($participationData));
            
            $result = Participation::create($participationData);
            \Log::info('Participación de prueba creada exitosamente con ID: ' . $result->id);
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('Error al crear participación de prueba: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar que las relaciones existan antes de crear participaciones
     */
    public function validateRelationships()
    {
        $errors = [];
        
        // Verificar que la entidad existe
        if (!$this->entity_id) {
            $errors[] = 'entity_id no está definido';
        } elseif (!Entity::find($this->entity_id)) {
            $errors[] = 'La entidad con ID ' . $this->entity_id . ' no existe';
        }
        
        // Verificar que el set existe
        if (!$this->set_id) {
            $errors[] = 'set_id no está definido';
        } elseif (!Set::find($this->set_id)) {
            $errors[] = 'El set con ID ' . $this->set_id . ' no existe';
        }
        
        // Verificar que el design format existe
        if (!$this->id) {
            $errors[] = 'design_format_id no está definido';
        }
        
        return $errors;
    }

    /**
     * Obtener el número de set basado en la fecha de creación
     */
    private function getSetNumber()
    {
        if (!$this->set) {
            return 1;
        }

        // Contar cuántos sets hay para la misma reserva, ordenados por fecha de creación
        $setNumber = Set::where('reserve_id', $this->set->reserve_id)
            ->where('created_at', '<=', $this->set->created_at)
            ->count();
        
        return $setNumber;
    }
}
