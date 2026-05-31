<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SepaPaymentBeneficiary extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_REVERTED = 'reverted';

    protected $fillable = [
        'sepa_payment_order_id',
        'participation_collection_id',
        'end_to_end_id',
        'amount',
        'currency',
        'creditor_name',
        'creditor_nif_cif',
        'creditor_iban',
        'purpose_code',
        'remittance_info',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function isPending(): bool
    {
        return ($this->status ?? self::STATUS_PENDING) === self::STATUS_PENDING;
    }

    public function statusLabel(): string
    {
        return match ($this->status ?? self::STATUS_PENDING) {
            self::STATUS_PAID => 'Pagado',
            self::STATUS_REVERTED => 'Revertido (cobrable)',
            default => 'Pendiente',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status ?? self::STATUS_PENDING) {
            self::STATUS_PAID => 'success',
            self::STATUS_REVERTED => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Relación con SepaPaymentOrder
     */
    public function paymentOrder(): BelongsTo
    {
        return $this->belongsTo(SepaPaymentOrder::class, 'sepa_payment_order_id');
    }

    /**
     * Relación con ParticipationCollection (cuando el beneficiario proviene de una solicitud de cobro)
     */
    public function participationCollection(): BelongsTo
    {
        return $this->belongsTo(ParticipationCollection::class);
    }

    /**
     * Generar un End to End ID único
     */
    public static function generateEndToEndId(string $prefix = ''): string
    {
        $prefix = $prefix ?: date('Ymd');
        return $prefix . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
