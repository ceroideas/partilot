<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Reserve;
use Carbon\Carbon;

class DeadlineBeforeLottery implements Rule
{
    protected $reserveId;

    /**
     * Create a new rule instance.
     *
     * @param int $reserveId
     */
    public function __construct($reserveId)
    {
        $this->reserveId = $reserveId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$value) {
            return true; // Si no hay fecha límite, es válido
        }

        // Obtener la reserva con el sorteo
        $reserve = Reserve::with('lottery')->find($this->reserveId);
        
        if (!$reserve || !$reserve->lottery) {
            return false; // Si no hay reserva o sorteo, es inválido
        }

        $deadlineDate = Carbon::parse($value)->startOfDay();
        $lotteryDate = Carbon::parse($reserve->lottery->draw_date)->startOfDay();

        // La fecha límite debe ser estrictamente anterior al día del sorteo (máx. día anterior a las 23:59)
        return $deadlineDate->lt($lotteryDate);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $reserve = Reserve::with('lottery')->find($this->reserveId);
        $lotteryDate = $reserve && $reserve->lottery 
            ? Carbon::parse($reserve->lottery->draw_date)->format('d/m/Y')
            : 'la fecha del sorteo';
            
        return "La fecha límite debe ser como máximo el día anterior al sorteo ({$lotteryDate}).";
    }
}

