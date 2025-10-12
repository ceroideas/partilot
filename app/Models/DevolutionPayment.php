<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolutionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'devolution_id',
        'amount',
        'payment_method',
        'from_number',
        'notes',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function devolution()
    {
        return $this->belongsTo(Devolution::class);
    }
}