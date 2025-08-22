<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
                $designFormat->updateParticipations();
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
        if (!$this->set) {
            return;
        }

        $totalParticipations = $this->set->total_participations ?? 0;
        if ($totalParticipations <= 0) {
            return;
        }

        // Obtener participaciones por taco desde el output
        $output = is_string($this->output) ? json_decode($this->output, true) : $this->output;
        $participationsPerBook = $output['participations_per_book'] ?? 50;

        // Obtener número de set
        $setNumber = $this->getSetNumber();

        $participationsToCreate = [];

        for ($participationNumber = 1; $participationNumber <= $totalParticipations; $participationNumber++) {
            // Calcular a qué taco pertenece esta participación
            $bookNumber = ceil($participationNumber / $participationsPerBook);
            
            // Generar código de participación
            $participationCode = sprintf('%d/%05d', $setNumber, $participationNumber);

            $participationsToCreate[] = [
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

            // Insertar en lotes de 1000 para mejor rendimiento
            if (count($participationsToCreate) >= 1000) {
                Participation::upsert($participationsToCreate, ['set_id', 'participation_number'], [
                    'participation_code', 'book_number', 'status', 'updated_at'
                ]);
                $participationsToCreate = [];
            }
        }

        // Insertar las participaciones restantes
        if (!empty($participationsToCreate)) {
            Participation::upsert($participationsToCreate, ['set_id', 'participation_number'], [
                'participation_code', 'book_number', 'status', 'updated_at'
            ]);
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
        $this->generateParticipations();
    }

    /**
     * Eliminar participaciones asociadas
     */
    public function deleteParticipations()
    {
        Participation::where('design_format_id', $this->id)->delete();
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
