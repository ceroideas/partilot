<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participation extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'set_id',
        'design_format_id',
        'participation_number',
        'participation_code',
        'book_number',
        'status',
        'seller_id',
        'sale_date',
        'sale_time',
        'sale_amount',
        'payment_method',
        'buyer_name',
        'buyer_phone',
        'buyer_email',
        'buyer_nif',
        'collected_at',
        'donated_at',
        'return_date',
        'return_time',
        'return_reason',
        'returned_by',
        'cancellation_date',
        'cancellation_reason',
        'cancelled_by',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'sale_time' => 'datetime:H:i',
        'sale_amount' => 'decimal:2',
        'return_date' => 'date',
        'return_time' => 'datetime:H:i',
        'cancellation_date' => 'date',
        'collected_at' => 'datetime',
        'donated_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relaciones
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function designFormat()
    {
        return $this->belongsTo(DesignFormat::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ParticipationActivityLog::class)->orderBy('created_at', 'desc');
    }

    public function gift()
    {
        return $this->hasOne(ParticipationGift::class);
    }

    // Scopes para consultas comunes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'vendida');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'devuelta');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'anulada');
    }

    public function scopeBySet($query, $setId)
    {
        return $query->where('set_id', $setId);
    }

    public function scopeByBook($query, $bookNumber, $setId)
    {
        return $query->where('book_number', $bookNumber)
                    ->where('set_id', $setId);
    }

    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    // Métodos de utilidad
    public function isAvailable()
    {
        return $this->status === 'disponible';
    }

    public function isSold()
    {
        return $this->status === 'vendida';
    }

    public function isReturned()
    {
        return $this->status === 'devuelta';
    }

    public function isCancelled()
    {
        return $this->status === 'anulada';
    }

    public function isReserved()
    {
        return $this->status === 'reservada';
    }

    public function isLost()
    {
        return $this->status === 'perdida';
    }

    // Método para marcar como vendida (payment_method por participación para Tarea 3 QR)
    public function markAsSold($sellerId, $saleAmount = null, $buyerInfo = [], $paymentMethod = null)
    {
        $data = [
            'status' => 'vendida',
            'seller_id' => $sellerId,
            'sale_date' => now()->toDateString(),
            'sale_time' => now()->toTimeString(),
            'sale_amount' => $saleAmount,
            'buyer_name' => $buyerInfo['name'] ?? null,
            'buyer_phone' => $buyerInfo['phone'] ?? null,
            'buyer_email' => $buyerInfo['email'] ?? null,
            'buyer_nif' => $buyerInfo['nif'] ?? null,
        ];
        if ($paymentMethod !== null) {
            $data['payment_method'] = $paymentMethod;
        }
        $this->update($data);
    }

    // Método para marcar como devuelta
    public function markAsReturned($reason = null, $returnedBy = null)
    {
        $this->update([
            'status' => 'devuelta',
            'return_date' => now()->toDateString(),
            'return_time' => now()->toTimeString(),
            'return_reason' => $reason,
            'returned_by' => $returnedBy,
        ]);
    }

    // Método para marcar como anulada
    public function markAsCancelled($reason = null, $cancelledBy = null)
    {
        $this->update([
            'status' => 'anulada',
            'cancellation_date' => now()->toDateString(),
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
        ]);
    }

    // Método para reservar
    public function reserve()
    {
        $this->update(['status' => 'reservada']);
    }

    // Método para liberar reserva
    public function release()
    {
        $this->update(['status' => 'disponible']);
    }

    // Método para marcar como perdida
    public function markAsLost()
    {
        $this->update(['status' => 'perdida']);
    }

    // Método para obtener el estado en español
    public function getStatusTextAttribute()
    {
        $statuses = [
            'disponible' => 'Disponible',
            'reservada' => 'Reservada',
            'vendida' => 'Vendida',
            'devuelta' => 'Devuelta',
            'anulada' => 'Anulada',
            'perdida' => 'Perdida'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Método para obtener el badge de estado
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'disponible' => 'bg-success',
            'reservada' => 'bg-warning',
            'vendida' => 'bg-primary',
            'devuelta' => 'bg-info',
            'anulada' => 'bg-danger',
            'perdida' => 'bg-secondary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    /**
     * Scope para filtrar participaciones accesibles por usuario.
     */
    public function scopeForUser($query, User $user)
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        $entityIds = $user->accessibleEntityIds();
        $sellerIds = $user->accessibleSellerIds();

        if (empty($entityIds) && empty($sellerIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($entityIds, $sellerIds) {
            if (!empty($entityIds)) {
                $q->whereIn('entity_id', $entityIds);
            }

            if (!empty($sellerIds)) {
                $q->orWhereIn('seller_id', $sellerIds);
            }
        });
    }
}
