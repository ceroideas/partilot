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
        'total_participations',
        'participation_price',
        'total_amount',
        'played_amount',
        'donation_amount',
        'total_participation_amount',
        'physical_participations',
        'digital_participations',
        'deadline_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_participations' => 'integer',
        'participation_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'played_amount' => 'decimal:2',
        'donation_amount' => 'decimal:2',
        'total_participation_amount' => 'decimal:2',
        'physical_participations' => 'integer',
        'digital_participations' => 'integer',
        'deadline_date' => 'date',
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
}
