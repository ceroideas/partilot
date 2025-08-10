<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrutinyEntityResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'administration_lottery_scrutiny_id',
        'entity_id',
        'reserved_numbers',
        'total_reserved',
        'total_issued',
        'total_sold',
        'total_returned',
        'winning_numbers',
        'total_winning',
        'total_prize_amount',
        'prize_per_participation',
        'prize_breakdown'
    ];

    protected $casts = [
        'reserved_numbers' => 'array',
        'winning_numbers' => 'array',
        'prize_breakdown' => 'array',
        'total_prize_amount' => 'decimal:2',
        'prize_per_participation' => 'decimal:2'
    ];

    /**
     * Relación con AdministrationLotteryScrutiny
     */
    public function administrationLotteryScrutiny()
    {
        return $this->belongsTo(AdministrationLotteryScrutiny::class);
    }

    /**
     * Relación con Entity
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Obtener los números reservados como array limpio
     */
    public function getReservedNumbersAttribute($value)
    {
        $numbers = json_decode($value, true);
        return is_array($numbers) ? $numbers : [];
    }

    /**
     * Obtener los números ganadores como array limpio
     */
    public function getWinningNumbersAttribute($value)
    {
        $numbers = json_decode($value, true);
        return is_array($numbers) ? $numbers : [];
    }

    /**
     * Obtener el desglose de premios
     */
    public function getPrizeBreakdownAttribute($value)
    {
        $breakdown = json_decode($value, true);
        
        return [
            'primer_premio' => $breakdown['primer_premio'] ?? ['numbers' => [], 'amount' => 0],
            'segundo_premio' => $breakdown['segundo_premio'] ?? ['numbers' => [], 'amount' => 0],
            'terceros_premios' => $breakdown['terceros_premios'] ?? ['numbers' => [], 'amount' => 0],
            'cuartos_premios' => $breakdown['cuartos_premios'] ?? ['numbers' => [], 'amount' => 0],
            'quintos_premios' => $breakdown['quintos_premios'] ?? ['numbers' => [], 'amount' => 0],
            'reintegros' => $breakdown['reintegros'] ?? ['numbers' => [], 'amount' => 0],
            'otros_premios' => $breakdown['otros_premios'] ?? ['numbers' => [], 'amount' => 0]
        ];
    }

    /**
     * Calcular los premios basándose en los números reservados y los resultados del sorteo
     */
    public function calculatePrizes(LotteryResult $lotteryResult)
    {
        $reservedNumbers = $this->reserved_numbers;
        $winningNumbers = [];
        $prizeBreakdown = [];
        $totalPrizeAmount = 0;

        // Verificar primer premio
        if ($lotteryResult->primer_premio && in_array($lotteryResult->primer_premio['decimo'], $reservedNumbers)) {
            $winningNumbers[] = $lotteryResult->primer_premio['decimo'];
            $prizeBreakdown['primer_premio'] = [
                'numbers' => [$lotteryResult->primer_premio['decimo']],
                'amount' => $lotteryResult->primer_premio['premio'] ?? 0
            ];
            $totalPrizeAmount += $prizeBreakdown['primer_premio']['amount'];
        }

        // Verificar segundo premio
        if ($lotteryResult->segundo_premio && in_array($lotteryResult->segundo_premio['decimo'], $reservedNumbers)) {
            $winningNumbers[] = $lotteryResult->segundo_premio['decimo'];
            $prizeBreakdown['segundo_premio'] = [
                'numbers' => [$lotteryResult->segundo_premio['decimo']],
                'amount' => $lotteryResult->segundo_premio['premio'] ?? 0
            ];
            $totalPrizeAmount += $prizeBreakdown['segundo_premio']['amount'];
        }

        // Verificar terceros premios
        $tercerosPremios = [];
        if ($lotteryResult->terceros_premios) {
            foreach ($lotteryResult->terceros_premios as $premio) {
                if (in_array($premio['decimo'], $reservedNumbers)) {
                    $winningNumbers[] = $premio['decimo'];
                    $tercerosPremios[] = $premio['decimo'];
                    $totalPrizeAmount += $premio['premio'] ?? 0;
                }
            }
            if (!empty($tercerosPremios)) {
                $prizeBreakdown['terceros_premios'] = [
                    'numbers' => $tercerosPremios,
                    'amount' => count($tercerosPremios) * ($lotteryResult->terceros_premios[0]['premio'] ?? 0)
                ];
            }
        }

        // Verificar reintegros
        $reintegros = [];
        if ($lotteryResult->reintegros) {
            foreach ($lotteryResult->reintegros as $reintegro) {
                $lastDigit = $reintegro['decimo'];
                foreach ($reservedNumbers as $number) {
                    if (substr($number, -1) === $lastDigit) {
                        $winningNumbers[] = $number;
                        $reintegros[] = $number;
                        $totalPrizeAmount += $reintegro['premio'] ?? 0;
                    }
                }
            }
            if (!empty($reintegros)) {
                $prizeBreakdown['reintegros'] = [
                    'numbers' => $reintegros,
                    'amount' => count($reintegros) * ($lotteryResult->reintegros[0]['premio'] ?? 0)
                ];
            }
        }

        // Actualizar el modelo
        $this->winning_numbers = $winningNumbers;
        $this->total_winning = count($winningNumbers);
        $this->total_prize_amount = $totalPrizeAmount;
        $this->prize_per_participation = $this->total_sold > 0 ? $totalPrizeAmount / $this->total_sold : 0;
        $this->prize_breakdown = $prizeBreakdown;

        return $this;
    }

    /**
     * Scope para entidades con premios
     */
    public function scopeWithPrizes($query)
    {
        return $query->where('total_winning', '>', 0);
    }

    /**
     * Scope para entidades sin premios
     */
    public function scopeWithoutPrizes($query)
    {
        return $query->where('total_winning', 0);
    }
}
