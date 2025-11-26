<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SepaPaymentBeneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'sepa_payment_order_id',
        'end_to_end_id',
        'amount',
        'currency',
        'creditor_name',
        'creditor_nif_cif',
        'creditor_iban',
        'purpose_code',
        'remittance_info',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relación con SepaPaymentOrder
     */
    public function paymentOrder(): BelongsTo
    {
        return $this->belongsTo(SepaPaymentOrder::class, 'sepa_payment_order_id');
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
