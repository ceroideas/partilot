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
        'total_non_winning',
        'winning_numbers',
        'total_winning',
        'winning_participations',
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
            'primer_premio' => $breakdown['primer_premio'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'segundo_premio' => $breakdown['segundo_premio'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'terceros_premios' => $breakdown['terceros_premios'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'cuartos_premios' => $breakdown['cuartos_premios'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'quintos_premios' => $breakdown['quintos_premios'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_cinco_cifras' => $breakdown['extracciones_cinco_cifras'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_cuatro_cifras' => $breakdown['extracciones_cuatro_cifras'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_tres_cifras' => $breakdown['extracciones_tres_cifras'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_dos_cifras' => $breakdown['extracciones_dos_cifras'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'reintegros' => $breakdown['reintegros'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'otros_premios' => $breakdown['otros_premios'] ?? ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0]
        ];
    }

    /**
     * Calcular los premios basándose en los números reservados y los resultados del sorteo
     */
    public function calculatePrizes(LotteryResult $lotteryResult, $lotteryType = null, $winningParticipations = null)
    {
        $reservedNumbers = $this->reserved_numbers;
        $winningNumbers = [];
        $prizeBreakdown = [
            'primer_premio' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'segundo_premio' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'terceros_premios' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'cuartos_premios' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'quintos_premios' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_cinco_cifras' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_cuatro_cifras' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_tres_cifras' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'extracciones_dos_cifras' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'reintegros' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0],
            'otros_premios' => ['numbers' => [], 'prize_per_ticket' => 0, 'total_tickets' => 0, 'total_amount' => 0]
        ];
        $totalPrizeAmount = 0;

        // Verificar premio especial
        /*if ($lotteryResult->premio_especial && in_array($lotteryResult->premio_especial['decimo'], $reservedNumbers)) {
            $winningNumbers[] = $lotteryResult->premio_especial['decimo'];
            $prizeBreakdown['otros_premios']['numbers'][] = $lotteryResult->premio_especial['decimo'];
            $prizeAmount = ($lotteryResult->premio_especial['prize'] ?? 0) / 100;
            $prizeBreakdown['otros_premios']['amount'] += $prizeAmount;
            $totalPrizeAmount += $prizeAmount;
        }*/

        // Verificar primer premio
        if ($lotteryResult->primer_premio && in_array($lotteryResult->primer_premio['decimo'], $reservedNumbers)) {
            $winningNumbers[] = $lotteryResult->primer_premio['decimo'];
            $prizeBreakdown['primer_premio']['numbers'][] = $lotteryResult->primer_premio['decimo'];
            // El premio por décimo es 100€
            $prizePerTicket = 100;
            $prizeBreakdown['primer_premio']['prize_per_ticket'] = $prizePerTicket;
            
            // El premio total del sorteo (convertir de céntimos a euros)
            $totalPrizeFromLottery = ($lotteryResult->primer_premio['prize'] ?? 0) / 100;
            
            // Calcular cuántos décimos hay: premio total ÷ premio por décimo
            $winningTickets = $totalPrizeFromLottery / $prizePerTicket;
            
            $prizeBreakdown['primer_premio']['total_tickets'] = $winningTickets;
            $prizeBreakdown['primer_premio']['total_amount'] = $totalPrizeFromLottery;
        }

        // Verificar segundo premio
        if ($lotteryResult->segundo_premio && in_array($lotteryResult->segundo_premio['decimo'], $reservedNumbers)) {
            $winningNumbers[] = $lotteryResult->segundo_premio['decimo'];
            $prizeBreakdown['segundo_premio']['numbers'][] = $lotteryResult->segundo_premio['decimo'];
            $prizeAmount = ($lotteryResult->segundo_premio['prize'] ?? 0) / 100;
            $prizeBreakdown['segundo_premio']['amount'] += $prizeAmount;
            $totalPrizeAmount += $prizeAmount;
        }

        // Verificar terceros premios
        if ($lotteryResult->terceros_premios) {
            foreach ($lotteryResult->terceros_premios as $premio) {
                if (in_array($premio['decimo'], $reservedNumbers)) {
                    $winningNumbers[] = $premio['decimo'];
                    $prizeBreakdown['terceros_premios']['numbers'][] = $premio['decimo'];
                    $prizeAmount = ($premio['prize'] ?? 0) / 100;
                    $prizeBreakdown['terceros_premios']['amount'] += $prizeAmount;
                    $totalPrizeAmount += $prizeAmount;
                }
            }
        }

        // Verificar cuartos premios
        if ($lotteryResult->cuartos_premios) {
            foreach ($lotteryResult->cuartos_premios as $premio) {
                if (in_array($premio['decimo'], $reservedNumbers)) {
                    $winningNumbers[] = $premio['decimo'];
                    $prizeBreakdown['cuartos_premios']['numbers'][] = $premio['decimo'];
                    $prizeAmount = ($premio['prize'] ?? 0) / 100;
                    $prizeBreakdown['cuartos_premios']['amount'] += $prizeAmount;
                    $totalPrizeAmount += $prizeAmount;
                }
            }
        }

        // Verificar quintos premios
        if ($lotteryResult->quintos_premios) {
            foreach ($lotteryResult->quintos_premios as $premio) {
                if (in_array($premio['decimo'], $reservedNumbers)) {
                    $winningNumbers[] = $premio['decimo'];
                    $prizeBreakdown['quintos_premios']['numbers'][] = $premio['decimo'];
                    $prizeAmount = ($premio['prize'] ?? 0) / 100;
                    $prizeBreakdown['quintos_premios']['amount'] += $prizeAmount;
                    $totalPrizeAmount += $prizeAmount;
                }
            }
        }

        // Verificar extracciones de cinco cifras
        if ($lotteryResult->extracciones_cinco_cifras) {
            foreach ($lotteryResult->extracciones_cinco_cifras as $premio) {
                foreach ($reservedNumbers as $number) {
                    if (substr($number, 0, 5) === $premio['decimo']) {
                        $winningNumbers[] = $number;
                        $prizeBreakdown['extracciones_cinco_cifras']['numbers'][] = $number;
                        $prizeAmount = ($premio['prize'] ?? 0) / 100;
                        $prizeBreakdown['extracciones_cinco_cifras']['amount'] += $prizeAmount;
                        $totalPrizeAmount += $prizeAmount;
                    }
                }
            }
        }

        // Verificar extracciones de cuatro cifras
        if ($lotteryResult->extracciones_cuatro_cifras) {
            foreach ($lotteryResult->extracciones_cuatro_cifras as $premio) {
                foreach ($reservedNumbers as $number) {
                    if (substr($number, 0, 4) === $premio['decimo']) {
                        $winningNumbers[] = $number;
                        $prizeBreakdown['extracciones_cuatro_cifras']['numbers'][] = $number;
                        $prizeAmount = ($premio['prize'] ?? 0) / 100;
                        $prizeBreakdown['extracciones_cuatro_cifras']['amount'] += $prizeAmount;
                        $totalPrizeAmount += $prizeAmount;
                    }
                }
            }
        }

        // Verificar extracciones de tres cifras
        if ($lotteryResult->extracciones_tres_cifras) {
            foreach ($lotteryResult->extracciones_tres_cifras as $premio) {
                foreach ($reservedNumbers as $number) {
                    if (substr($number, 0, 3) === $premio['decimo']) {
                        $winningNumbers[] = $number;
                        $prizeBreakdown['extracciones_tres_cifras']['numbers'][] = $number;
                        $prizeAmount = ($premio['prize'] ?? 0) / 100;
                        $prizeBreakdown['extracciones_tres_cifras']['amount'] += $prizeAmount;
                        $totalPrizeAmount += $prizeAmount;
                    }
                }
            }
        }

        // Verificar extracciones de dos cifras
        if ($lotteryResult->extracciones_dos_cifras) {
            foreach ($lotteryResult->extracciones_dos_cifras as $premio) {
                foreach ($reservedNumbers as $number) {
                    if (substr($number, 0, 2) === $premio['decimo']) {
                        $winningNumbers[] = $number;
                        $prizeBreakdown['extracciones_dos_cifras']['numbers'][] = $number;
                        $prizeAmount = ($premio['prize'] ?? 0) / 100;
                        $prizeBreakdown['extracciones_dos_cifras']['amount'] += $prizeAmount;
                        $totalPrizeAmount += $prizeAmount;
                    }
                }
            }
        }

        // Verificar reintegros (última cifra)
        if ($lotteryResult->reintegros) {
            foreach ($lotteryResult->reintegros as $reintegro) {
                $lastDigit = $reintegro['decimo'];
                foreach ($reservedNumbers as $number) {
                    if (substr($number, -1) === $lastDigit) {
                        $winningNumbers[] = $number;
                        $prizeBreakdown['reintegros']['numbers'][] = $number;
                        $prizeAmount = ($reintegro['prize'] ?? 0) / 100;
                        $prizeBreakdown['reintegros']['amount'] += $prizeAmount;
                        $totalPrizeAmount += $prizeAmount;
                    }
                }
            }
        }

        // Eliminar duplicados de números ganadores (un número puede ganar múltiples premios)
        $winningNumbers = array_unique($winningNumbers);

        // Calcular el premio total basado en el precio del décimo del tipo de sorteo
        // El premio total es: precio del décimo × número de participaciones asignadas
        $ticketPrice = $lotteryType ? $lotteryType->ticket_price : 20; // Precio por defecto de 20€
        $totalPrizeAmount = $ticketPrice * $this->total_reserved; // total_reserved = participaciones asignadas
        // $totalPrizeAmount = $ticketPrice * $this->total_sold; // total_reserved = participaciones asignadas

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

    /**
     * Validar que los números de lotería sean válidos (5 dígitos)
     */
    public function validateLotteryNumbers($numbers)
    {
        $validNumbers = [];
        $invalidNumbers = [];

        foreach ($numbers as $number) {
            if (is_numeric($number) && strlen($number) === 5) {
                $validNumbers[] = $number;
            } else {
                $invalidNumbers[] = $number;
            }
        }

        return [
            'valid' => $validNumbers,
            'invalid' => $invalidNumbers
        ];
    }

    /**
     * Calcular estadísticas adicionales del escrutinio
     */
    public function getScrutinyStatistics()
    {
        $totalNumbers = count($this->reserved_numbers);
        $winningNumbers = count($this->winning_numbers);
        $nonWinningNumbers = $totalNumbers - $winningNumbers;

        return [
            'total_numbers' => $totalNumbers,
            'winning_numbers' => $winningNumbers,
            'non_winning_numbers' => $nonWinningNumbers,
            'winning_percentage' => $totalNumbers > 0 ? ($winningNumbers / $totalNumbers) * 100 : 0,
            'average_prize_per_winning' => $winningNumbers > 0 ? $this->total_prize_amount / $winningNumbers : 0,
            'prize_efficiency' => $totalNumbers > 0 ? $this->total_prize_amount / ($totalNumbers * 20) : 0 // Asumiendo precio de 20€ por décimo
        ];
    }

    /**
     * Obtener un resumen de premios en formato legible
     */
    public function getPrizeSummary()
    {
        $summary = [];
        $breakdown = $this->prize_breakdown;

        if (!empty($breakdown['primer_premio']['numbers'])) {
            $summary[] = "1º Premio: " . count($breakdown['primer_premio']['numbers']) . " número(s) - " . number_format($breakdown['primer_premio']['amount'], 2) . "€";
        }

        if (!empty($breakdown['segundo_premio']['numbers'])) {
            $summary[] = "2º Premio: " . count($breakdown['segundo_premio']['numbers']) . " número(s) - " . number_format($breakdown['segundo_premio']['amount'], 2) . "€";
        }

        if (!empty($breakdown['terceros_premios']['numbers'])) {
            $summary[] = "3º Premios: " . count($breakdown['terceros_premios']['numbers']) . " número(s) - " . number_format($breakdown['terceros_premios']['amount'], 2) . "€";
        }

        if (!empty($breakdown['cuartos_premios']['numbers'])) {
            $summary[] = "4º Premios: " . count($breakdown['cuartos_premios']['numbers']) . " número(s) - " . number_format($breakdown['cuartos_premios']['amount'], 2) . "€";
        }

        if (!empty($breakdown['quintos_premios']['numbers'])) {
            $summary[] = "5º Premios: " . count($breakdown['quintos_premios']['numbers']) . " número(s) - " . number_format($breakdown['quintos_premios']['amount'], 2) . "€";
        }

        if (!empty($breakdown['reintegros']['numbers'])) {
            $summary[] = "Reintegros: " . count($breakdown['reintegros']['numbers']) . " número(s) - " . number_format($breakdown['reintegros']['amount'], 2) . "€";
        }

        return $summary;
    }
}
