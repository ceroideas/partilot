<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintOrder extends Model
{
    public const STATUS_PENDING_REVIEW = 'pendiente_revision';
    public const STATUS_IN_PRODUCTION = 'en_produccion';
    public const STATUS_SENT = 'enviada';
    public const STATUS_REJECTED = 'rechazada';

    protected $fillable = [
        'order_code',
        'design_format_id',
        'set_id',
        'entity_id',
        'lottery_id',
        'created_by_user_id',
        'status',
        'payment_provider',
        'payment_intent_id',
        'payment_status',
        'print_size',
        'participations_per_book',
        'back_mode',
        'quoted_amount',
        'quote_breakdown',
        'notes',
        'sent_at',
        'paid_at',
    ];

    protected $casts = [
        'quoted_amount' => 'decimal:2',
        'quote_breakdown' => 'array',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function design()
    {
        return $this->belongsTo(DesignFormat::class, 'design_format_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING_REVIEW => 'Pendiente revisión',
            self::STATUS_IN_PRODUCTION => 'En producción',
            self::STATUS_SENT => 'Enviada',
            self::STATUS_REJECTED => 'Rechazada',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public static function statusBadgeClass(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING_REVIEW => 'bg-warning text-dark',
            self::STATUS_IN_PRODUCTION => 'bg-info text-dark',
            self::STATUS_SENT => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        $transitions = [
            self::STATUS_PENDING_REVIEW => [self::STATUS_IN_PRODUCTION, self::STATUS_REJECTED],
            self::STATUS_IN_PRODUCTION => [self::STATUS_SENT, self::STATUS_REJECTED],
            self::STATUS_REJECTED => [self::STATUS_PENDING_REVIEW],
            self::STATUS_SENT => [],
        ];

        return in_array($targetStatus, $transitions[$this->status] ?? [], true);
    }
}

