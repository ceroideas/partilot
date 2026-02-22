<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipationCollection extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'apellidos',
        'nif',
        'iban',
        'importe_total',
        'collected_at',
        'sepa_payment_order_id',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'importe_total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ParticipationCollectionItem::class, 'collection_id');
    }

    public function participations()
    {
        return $this->belongsToMany(Participation::class, 'participation_collection_items', 'collection_id', 'participation_id');
    }

    public function sepaPaymentOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SepaPaymentOrder::class);
    }

    public function scopePending($query)
    {
        return $query->whereNull('sepa_payment_order_id');
    }

    /**
     * Al borrar la solicitud de cobro, poner collected_at en null en las participaciones vinculadas.
     */
    protected static function booted(): void
    {
        static::deleting(function (ParticipationCollection $collection) {
            $participationIds = $collection->items()->pluck('participation_id')->unique()->filter()->values()->all();
            if ($participationIds !== [] && \Illuminate\Support\Facades\Schema::hasColumn('participations', 'collected_at')) {
                Participation::whereIn('id', $participationIds)->update(['collected_at' => null]);
            }
        });
    }
}
