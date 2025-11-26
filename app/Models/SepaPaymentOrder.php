<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SepaPaymentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'administration_id',
        'message_id',
        'creation_date',
        'execution_date',
        'number_of_transactions',
        'control_sum',
        'payment_info_id',
        'batch_booking',
        'charge_bearer',
        'debtor_name',
        'debtor_nif_cif',
        'debtor_iban',
        'debtor_address',
        'xml_filename',
        'status',
        'notes',
    ];

    protected $casts = [
        'creation_date' => 'datetime',
        'execution_date' => 'date',
        'number_of_transactions' => 'integer',
        'control_sum' => 'decimal:2',
        'batch_booking' => 'boolean',
    ];

    /**
     * Relación con Administration
     */
    public function administration(): BelongsTo
    {
        return $this->belongsTo(Administration::class);
    }

    /**
     * Relación con SepaPaymentBeneficiary
     */
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(SepaPaymentBeneficiary::class);
    }

    /**
     * Generar un Message ID único
     */
    public static function generateMessageId(): string
    {
        return date('YmdHis') . strtoupper(substr(uniqid(), -6));
    }
}
