<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintConfiguration extends Model
{
    protected $fillable = [
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
        'price_design' => 'decimal:4',
        'price_participation' => 'decimal:4',
        'price_back_bw' => 'decimal:4',
        'price_back_color' => 'decimal:4',
        'price_taco_25' => 'decimal:4',
        'price_taco_50' => 'decimal:4',
        'price_taco_100' => 'decimal:4',
    ];
}

