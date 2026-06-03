<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrintConfiguration extends Model
{
    public const STATUS_ACTIVE = 1;

    public const STATUS_INACTIVE = 0;

    protected $fillable = [
        'status',
        'company_name',
        'nif_cif',
        'address',
        'postal_code',
        'province',
        'city',
        'phone',
        'email',
        'price_design',
        'price_participation',
        'price_back_bw',
        'price_back_color',
        'price_taco_25',
        'price_taco_50',
        'price_taco_100',
        'bank_account',
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_webhook_secret',
    ];

    protected $casts = [
        'status' => 'integer',
        'price_design' => 'decimal:4',
        'price_participation' => 'decimal:4',
        'price_back_bw' => 'decimal:4',
        'price_back_color' => 'decimal:4',
        'price_taco_25' => 'decimal:4',
        'price_taco_50' => 'decimal:4',
        'price_taco_100' => 'decimal:4',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /** Más antigua primero. */
    public function scopeOrderedOldestFirst($query)
    {
        return $query->orderBy('id');
    }

    public function isActive(): bool
    {
        return (int) $this->status === self::STATUS_ACTIVE;
    }

    public function displayName(): string
    {
        $name = trim((string) $this->company_name);

        return $name !== '' ? $name : ('Imprenta #'.$this->id);
    }

    public static function resolveDefault(): self
    {
        $cfg = static::query()->active()->orderedOldestFirst()->first();
        if ($cfg) {
            return $cfg;
        }

        $any = static::query()->orderedOldestFirst()->first();
        if ($any) {
            if (! $any->isActive()) {
                $any->status = self::STATUS_ACTIVE;
                $any->save();
            }

            return $any;
        }

        return static::create([
            'status' => self::STATUS_ACTIVE,
            'company_name' => 'Imprenta',
        ]);
    }

    public function hasStripeConfigured(): bool
    {
        return $this->stripePublishableKey() !== '' && $this->stripeSecretKey() !== '';
    }

    /** Claves propias de la imprenta (sin fallback global). */
    public function stripePublishableKey(): string
    {
        return trim((string) ($this->stripe_publishable_key ?? ''));
    }

    public function stripeSecretKey(): string
    {
        return trim((string) ($this->stripe_secret_key ?? ''));
    }

    public function stripeWebhookSecret(): string
    {
        return trim((string) ($this->stripe_webhook_secret ?? ''));
    }

    public function printOrders(): HasMany
    {
        return $this->hasMany(PrintOrder::class);
    }
}

