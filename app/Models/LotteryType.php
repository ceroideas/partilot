<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identificador',
        'ticket_price',
        'prize_categories',
        'is_active',
        'series',
        'billetes_serie',
    ];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'prize_categories' => 'array',
        'is_active' => 'boolean',
    ];

    // Relación con Loterías
    public function lotteries()
    {
        return $this->hasMany(Lottery::class);
    }
}
