<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devolution extends Model
{
    use HasFactory;

    protected $table = 'devolutions';

    protected $fillable = [
        'entity_id',
        'lottery_id',
        'seller_id',
        'user_id',
        'total_participations',
        'return_reason',
        'devolution_date',
        'devolution_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'devolution_date' => 'date',
        'devolution_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Entity
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Relación con Lottery
     */
    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    /**
     * Relación con Seller (opcional)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Relación con User (quien procesa la devolución)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con DevolutionDetail
     */
    public function details(): HasMany
    {
        return $this->hasMany(DevolutionDetail::class);
    }

    /**
     * Relación con DevolutionPayment
     */
    public function payments(): HasMany
    {
        return $this->hasMany(DevolutionPayment::class);
    }

    /**
     * Scope para filtrar por entidad
     */
    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * Scope para filtrar por sorteo
     */
    public function scopeByLottery($query, $lotteryId)
    {
        return $query->where('lottery_id', $lotteryId);
    }

    /**
     * Scope para filtrar por vendedor
     */
    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Accessor para obtener el nombre completo de la devolución
     */
    public function getFullNameAttribute()
    {
        $seller = $this->seller ? " - {$this->seller->name}" : "";
        return "Devolución #{$this->id} - {$this->entity->name}{$seller}";
    }
}
