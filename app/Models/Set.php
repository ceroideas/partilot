<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'reserve_id',
        'set_name',
        'set_description',
        'set_number',
        'total_participations',
        'participation_price',
        'total_amount',
        'played_amount',
        'donation_amount',
        'total_participation_amount',
        'physical_participations',
        'digital_participations',
        'deadline_date',
        'tickets',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'set_number' => 'integer',
        'total_participations' => 'integer',
        'participation_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'played_amount' => 'decimal:2',
        'donation_amount' => 'decimal:2',
        'total_participation_amount' => 'decimal:2',
        'physical_participations' => 'integer',
        'digital_participations' => 'integer',
        'deadline_date' => 'date',
        'tickets' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con la entidad
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Relación con la reserva
     */
    public function reserve()
    {
        return $this->belongsTo(Reserve::class);
    }

    /**
     * Relación con las participaciones
     */
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    /**
     * Relación con los design formats
     */
    public function designFormats()
    {
        return $this->hasMany(DesignFormat::class);
    }

    /**
     * Scope para sets activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope para sets inactivos
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope para sets pausados
     */
    public function scopePaused($query)
    {
        return $query->where('status', 2);
    }

    /**
     * Obtener el texto del status
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            0 => 'Inactivo',
            1 => 'Activo',
            2 => 'Pausado',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener la clase CSS del status
     */
    public function getStatusClassAttribute()
    {
        return match($this->status) {
            0 => 'bg-danger',
            1 => 'bg-success',
            2 => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Genera el array de tickets con referencias únicas
     *
     * @param int $entityId
     * @param int $reserveId
     * @param \DateTime|string $createdAt
     * @param int $totalParticipations
     * @param array $oldTickets (opcional)
     * @return array
     */
    public static function generateTickets($entityId, $reserveId, $createdAt, $totalParticipations, $oldTickets = [])
    {
        $tickets = [];
        $created = is_string($createdAt) ? strtotime($createdAt) : ($createdAt instanceof \DateTime ? $createdAt->getTimestamp() : $createdAt);
        $dateStr = $created; // Usar timestamp directamente

        $input = (str_pad($entityId, 4, "0", STR_PAD_LEFT));
        $entityId = $input;

        $input = (str_pad($reserveId, 4, "0", STR_PAD_LEFT));
        $reserveId = $input;

        for ($i = 1; $i <= $totalParticipations; $i++) {
            $referencia = ("{$entityId}{$reserveId}{$dateStr}".str_pad($i, 3, '0', STR_PAD_LEFT));
            $tickets[] = [
                'n' => $i,
                'r' => $referencia
            ];
        }
        return $tickets;
    }

    /**
     * Boot del modelo para eventos automáticos
     */
    protected static function boot()
    {
        parent::boot();

        // Asignar set_number automáticamente al crear
        static::creating(function ($set) {
            if (empty($set->set_number)) {
                $set->set_number = static::getNextSetNumber($set->reserve_id);
            }
        });

        // Renumerar sets cuando se elimina uno
        static::deleted(function ($deletedSet) {
            self::renumberSetsInReserve($deletedSet->reserve_id);
        });
    }

    /**
     * Obtener el siguiente número de set para una reserva
     */
    private static function getNextSetNumber($reserveId)
    {
        $lastSet = self::where('reserve_id', $reserveId)
            ->orderBy('set_number', 'desc')
            ->first();
        
        return ($lastSet ? $lastSet->set_number : 0) + 1;
    }

    /**
     * Renumerar sets en una reserva después de eliminar uno
     */
    private static function renumberSetsInReserve($reserveId)
    {
        // Obtener todos los sets de la reserva ordenados por ID (orden de creación)
        $sets = self::where('reserve_id', $reserveId)
            ->orderBy('id')
            ->get();
        
        // Renumerar secuencialmente
        foreach ($sets as $index => $set) {
            $newNumber = $index + 1;
            if ($set->set_number != $newNumber) {
                $set->update(['set_number' => $newNumber]);
            }
        }
    }

    /**
     * Obtener el siguiente número de participación para una reserva específica
     * Este método calcula el siguiente número basado en las participaciones existentes
     * de la misma reserva, incluyendo las anuladas, para mantener la numeración secuencial por reserva
     */
    public static function getNextParticipationNumberForReserve($reserveId)
    {
        // Obtener el mayor participation_number de las participaciones de esta reserva
        $maxParticipationNumber = \App\Models\Participation::whereHas('set', function($query) use ($reserveId) {
            $query->where('reserve_id', $reserveId);
        })->max('participation_number') ?? 0;
        
        return $maxParticipationNumber + 1;
    }

    /**
     * Obtener el rango de números de participación para un set
     * Basado en el número de participaciones del set y el número de inicio por reserva
     */
    public function getParticipationNumberRange()
    {
        // Obtener el número de inicio basado en participaciones existentes de la misma reserva
        $startNumber = static::getNextParticipationNumberForReserve($this->reserve_id);
        $endNumber = $startNumber + $this->total_participations - 1;
        
        return [
            'start' => $startNumber,
            'end' => $endNumber,
            'count' => $this->total_participations
        ];
    }

    /**
     * Obtener el total de participaciones restando las anuladas
     */
    public function getTotalParticipationsAttribute()
    {
        $cancelledCount = $this->participations()->where('status', 'anulada')->count();
        return $this->attributes['total_participations'] - $cancelledCount;
    }

    /**
     * Obtener el importe total restando las participaciones anuladas
     */
    public function getTotalAmountAttribute()
    {
        $cancelledCount = $this->participations()->where('status', 'anulada')->count();
        $cancelledAmount = $cancelledCount * ($this->played_amount ?? 0);
        return $this->attributes['total_amount'] - $cancelledAmount;
    }
}
