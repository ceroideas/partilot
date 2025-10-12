<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerSettlement extends Model
{
    use HasFactory;

    protected $table = 'seller_settlements';

    protected $fillable = [
        'seller_id',
        'lottery_id',
        'user_id',
        'total_amount',
        'paid_amount',
        'pending_amount',
        'total_participations',
        'calculated_participations',
        'settlement_date',
        'settlement_time',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'calculated_participations' => 'decimal:2',
        'settlement_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Seller
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Relación con Lottery
     */
    public function lottery(): BelongsTo
    {
        return $this->belongsTo(Lottery::class);
    }

    /**
     * Relación con User (quien procesó la liquidación)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(SellerSettlementPayment::class);
    }
}

