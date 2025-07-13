<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'draw_date',
        'draw_time',
        'deadline_date',
        'ticket_price',
        'total_tickets',
        'sold_tickets',
        'prize_description',
        'prize_value',
        'image',
        'status',
        'lottery_type_id'
    ];

    protected $casts = [
        'draw_date' => 'date',
        'deadline_date' => 'date',
        'draw_time' => 'datetime',
        'ticket_price' => 'decimal:2',
        'prize_value' => 'decimal:2',
    ];

    // Relación con Tipo de Lotería
    public function lotteryType()
    {
        return $this->belongsTo(LotteryType::class, 'lottery_type_id');
    }

    // Relación con Participaciones
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    // Relación con Reservas
    public function reserves()
    {
        return $this->hasMany(Reserve::class);
    }
}
