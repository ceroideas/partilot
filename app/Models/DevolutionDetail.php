<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevolutionDetail extends Model
{
    use HasFactory;

    protected $table = 'devolution_details';

    protected $fillable = [
        'devolution_id',
        'participation_id',
        'action', // 'devolver' o 'vender'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Devolution
     */
    public function devolution(): BelongsTo
    {
        return $this->belongsTo(Devolution::class);
    }

    /**
     * Relación con Participation
     */
    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope para participaciones devueltas
     */
    public function scopeReturned($query)
    {
        return $query->where('action', 'devolver');
    }

    /**
     * Scope para participaciones vendidas
     */
    public function scopeSold($query)
    {
        return $query->where('action', 'vender');
    }
}
