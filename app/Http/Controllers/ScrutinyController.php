<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lottery;
use App\Models\LotteryResult;
use App\Models\LotteryType;

class ScrutinyController extends Controller
{
    /**
     * Mostrar la vista principal de escrutinio
     */
    public function index()
    {
        $lotteries = Lottery::with(['lotteryType', 'result'])
            ->whereHas('result')
            ->orderBy('draw_date', 'desc')
            ->get();

        $lotteryTypes = LotteryType::orderBy('name')->get();

        return view('scrutiny.index', compact('lotteries', 'lotteryTypes'));
    }

    /**
     * Generar escrutinio completo para un sorteo específico
     */
    public function generateScrutiny(Request $request)
    {
        // Aumentar límite de tiempo de ejecución
        set_time_limit(300); // 5 minutos
        
        $request->validate([
            'lottery_id' => 'required|integer|exists:lotteries,id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:1000',
            'start_range' => 'nullable|integer|min:0|max:99999',
            'end_range' => 'nullable|integer|min:0|max:99999',
            'sort_order' => 'nullable|in:asc,desc'
        ]);

        $lottery = Lottery::with(['lotteryType', 'result'])->findOrFail($request->lottery_id);
        
        if (!$lottery->result) {
            return response()->json([
                'success' => false,
                'message' => 'No hay resultados disponibles para este sorteo'
            ], 404);
        }

        // Parámetros
        $startRange = $request->get('start_range', 0);
        $endRange = $request->get('end_range', 99999);
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Calcular resultados (sin caché)
        if ($startRange > 0 || $endRange < 99999) {
            $scrutinyResults = $this->calculateNumbersInRange($lottery, $startRange, $endRange);
        } else {
            $scrutinyResults = $this->calculateAllNumbers($lottery);
        }
        
        // Calcular total de premios
        $totalPrizes = 0;
        foreach ($scrutinyResults as $result) {
            $totalPrizes += count($result['prizes']);
        }
        
        // Ordenamiento
        if ($sortOrder === 'asc') {
            usort($scrutinyResults, function($a, $b) {
                return $a['total_prize'] <=> $b['total_prize'];
            });
        } else {
            usort($scrutinyResults, function($a, $b) {
                return $b['total_prize'] <=> $a['total_prize'];
            });
        }
        
        // NO guardar en caché temporalmente
        
        // Paginación
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 100);
        $total = count($scrutinyResults);
        $offset = ($page - 1) * $perPage;
        $paginatedResults = array_slice($scrutinyResults, $offset, $perPage);
        
        \Log::info("=== TOTAL PREMIOS CALCULADO: {$totalPrizes} ===");
        
        return response()->json([
            'success' => true,
            'lottery' => $lottery,
            'total_numbers_with_prizes' => $total,
            'total_prizes' => $totalPrizes,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage),
            'search_range' => [
                'start' => $startRange,
                'end' => $endRange,
                'is_full_range' => $startRange == 0 && $endRange == 99999
            ],
            'results' => $paginatedResults
        ]);
    }


    /**
     * Exportar resultados del escrutinio a CSV
     */
    public function exportScrutiny(Request $request)
    {
        $request->validate([
            'lottery_id' => 'required|integer|exists:lotteries,id'
        ]);

        $lottery = Lottery::with(['lotteryType', 'result'])->findOrFail($request->lottery_id);
        
        if (!$lottery->result) {
            return response()->json([
                'success' => false,
                'message' => 'No hay resultados disponibles para este sorteo'
            ], 404);
        }

        $scrutinyResults = $this->calculateAllNumbers($lottery);
        
        // Generar CSV
        $filename = 'escrutinio_' . $lottery->name . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($scrutinyResults) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($file, ['Número', 'Premio Total (€)', 'Categorías', 'Detalle Premios']);
            
            // Datos
            foreach ($scrutinyResults as $result) {
                $categories = implode(', ', array_column($result['prizes'], 'category'));
                $prizeDetails = '';
                foreach ($result['prizes'] as $prize) {
                    $prizeDetails .= $prize['category'] . ': ' . number_format($prize['amount'], 2) . '€; ';
                }
                $prizeDetails = rtrim($prizeDetails, '; ');
                
                fputcsv($file, [
                    $result['number'],
                    number_format($result['total_prize'], 2),
                    $categories,
                    $prizeDetails
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    /**
     * Calcular premios para un rango específico de números
     */
    private function calculateNumbersInRange(Lottery $lottery, $startRange, $endRange)
    {
        $lotteryResult = $lottery->result;
        $typeIdentifier = $lottery->getLotteryTypeIdentifier();
        $categories = config('lotteryCategories');
        
        $winningNumbers = [];
        
        // Iterar solo en el rango especificado
        for ($i = $startRange; $i <= $endRange; $i++) {
            $number = str_pad($i, 5, '0', STR_PAD_LEFT);
            $prizeInfo = $this->calculateNumberPrizes($number, $lotteryResult, $typeIdentifier, $categories);
            
            if ($prizeInfo['total_prize'] > 0) {
                $winningNumbers[] = [
                    'number' => $number,
                    'total_prize' => $prizeInfo['total_prize'],
                    'prizes' => $prizeInfo['prizes']
                ];
            }
        }
        
        // Ordenar por premio total descendente
        usort($winningNumbers, function($a, $b) {
            return $b['total_prize'] <=> $a['total_prize'];
        });
        
        return $winningNumbers;
    }

    /**
     * Calcular premios para todos los números del 00000 al 99999 (optimizado)
     */
    private function calculateAllNumbers(Lottery $lottery)
    {
        $lotteryResult = $lottery->result;
        $typeIdentifier = $lottery->getLotteryTypeIdentifier();
        $categories = config('lotteryCategories');
        
        $results = [];
        $categoryCounts = []; // Contador por categoría
        
        // Calcular solo números que pueden tener premios (optimización)
        $winningNumbers = $this->getPotentialWinningNumbers($lotteryResult);
        
        \Log::info("=== CALCULANDO PREMIOS PARA " . count($winningNumbers) . " NÚMEROS POTENCIALES ===");
        
        foreach ($winningNumbers as $numberStr) {
            $prizeInfo = $this->calculateNumberPrizes($numberStr, $lotteryResult, $typeIdentifier, $categories);
            
            // Solo incluir números que tengan premios
            if ($prizeInfo['total_prize'] > 0) {
                $results[] = $prizeInfo;
                
                // Contar premios por categoría
                foreach ($prizeInfo['prizes'] as $prize) {
                    $category = $prize['category'];
                    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                }
            }
        }
        
        // Calcular total de premios
        $totalPrizes = array_sum($categoryCounts);
        
        // Log del resumen por categoría
        \Log::info("=== RESUMEN DE PREMIOS POR CATEGORÍA ===");
        foreach ($categoryCounts as $category => $count) {
            \Log::info("{$category}: {$count} premios");
        }
        \Log::info("=== TOTAL NÚMEROS CON PREMIOS: " . count($results) . " ===");
        \Log::info("=== TOTAL PREMIOS: " . $totalPrizes . " ===");
        
        // Ordenar por premio total descendente
        usort($results, function($a, $b) {
            return $b['total_prize'] <=> $a['total_prize'];
        });
        
        return $results;
    }
    
    /**
     * Obtener números que potencialmente pueden tener premios (optimización)
     */
    private function getPotentialWinningNumbers($lotteryResult)
    {
        $numbers = [];
        
        // 1. Números principales
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $numbers[] = $lotteryResult->primer_premio['decimo'];
        }
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $numbers[] = $lotteryResult->segundo_premio['decimo'];
        }
        
        // 2. Terceros, cuartos, quintos premios
        $additionalPrizes = ['terceros_premios', 'cuartos_premios', 'quintos_premios'];
        foreach ($additionalPrizes as $prizeType) {
            if ($lotteryResult->$prizeType && is_array($lotteryResult->$prizeType)) {
                foreach ($lotteryResult->$prizeType as $premio) {
                    if (isset($premio['decimo'])) {
                        $numbers[] = $premio['decimo'];
                    }
                }
            }
        }
        
        // 3. Extracciones - generar TODOS los números que terminan en cada extracción
        $extractionTypes = [
            'extracciones_cinco_cifras' => 1,    // 1 número por extracción (el número exacto)
            'extracciones_cuatro_cifras' => 10,  // 10 números por extracción
            'extracciones_tres_cifras' => 100,   // 100 números por extracción
            'extracciones_dos_cifras' => 1000    // 1000 números por extracción
        ];
        
        foreach ($extractionTypes as $extractionType => $multiplier) {
            if ($lotteryResult->$extractionType && is_array($lotteryResult->$extractionType)) {
                foreach ($lotteryResult->$extractionType as $extraccion) {
                    if (isset($extraccion['decimo'])) {
                        $extraccionDecimo = $extraccion['decimo'];
                        $cifras = strlen($extraccionDecimo);
                        
                        if ($extractionType === 'extracciones_cinco_cifras') {
                            // Para extracciones de 5 cifras, solo el número exacto
                            $numbers[] = str_pad($extraccionDecimo, 5, '0', STR_PAD_LEFT);
                        } else {
                            // Para otras extracciones, generar todos los números que terminan en esa extracción
                            for ($i = 0; $i < $multiplier; $i++) {
                                $number = str_pad($i, 5 - $cifras, '0', STR_PAD_LEFT) . $extraccionDecimo;
                                if (intval($number) <= 99999) {
                                    $numbers[] = $number;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // 4. Números con terminaciones que pueden ganar premios derivados
        $derivedNumbers = $this->getDerivedNumbers($lotteryResult);
        $numbers = array_merge($numbers, $derivedNumbers);
        
        // Eliminar duplicados después de agregar números derivados
        $numbers = array_unique($numbers);
        
        // 5. Números con reintegros - generar TODOS los números que terminan en cada reintegro
        if ($lotteryResult->reintegros && is_array($lotteryResult->reintegros)) {
            foreach ($lotteryResult->reintegros as $reintegro) {
                if (isset($reintegro['decimo'])) {
                    $reintegroDecimo = $reintegro['decimo'];
                    // Generar todos los números que terminan en esta cifra (10000 números por reintegro)
                    for ($i = 0; $i <= 99999; $i += 10) {
                        $number = $i + intval($reintegroDecimo);
                        if ($number <= 99999) {
                            $numbers[] = str_pad($number, 5, '0', STR_PAD_LEFT);
                        }
                    }
                }
            }
        }
        
        // Eliminar duplicados y ordenar
        $numbers = array_unique($numbers);
        sort($numbers);
        
        \Log::info("Números potenciales generados: " . count($numbers) . " (después de eliminar duplicados)");
        
        return $numbers;
    }
    
    /**
     * Obtener números que pueden ganar premios derivados
     */
    private function getDerivedNumbers($lotteryResult)
    {
        $numbers = [];
        
        // Centenas del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = intval($lotteryResult->primer_premio['decimo']);
            $centenaStart = intval($primerPremio / 100) * 100;
            for ($i = $centenaStart; $i <= $centenaStart + 99; $i++) {
                $numbers[] = str_pad($i, 5, '0', STR_PAD_LEFT);
            }
        }
        
        // Centenas del segundo premio
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $segundoPremio = intval($lotteryResult->segundo_premio['decimo']);
            $centenaStart = intval($segundoPremio / 100) * 100;
            for ($i = $centenaStart; $i <= $centenaStart + 99; $i++) {
                $numbers[] = str_pad($i, 5, '0', STR_PAD_LEFT);
            }
        }
        
        // Centenas de terceros premios
        if ($lotteryResult->terceros_premios && is_array($lotteryResult->terceros_premios)) {
            foreach ($lotteryResult->terceros_premios as $tercerPremio) {
                if (isset($tercerPremio['decimo'])) {
                    $tercerPremioNum = intval($tercerPremio['decimo']);
                    $centenaStart = intval($tercerPremioNum / 100) * 100;
                    for ($i = $centenaStart; $i <= $centenaStart + 99; $i++) {
                        $numbers[] = str_pad($i, 5, '0', STR_PAD_LEFT);
                    }
                }
            }
        }
        
        // Anterior y posterior al primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = intval($lotteryResult->primer_premio['decimo']);
            if ($primerPremio > 0) {
                $numbers[] = str_pad($primerPremio - 1, 5, '0', STR_PAD_LEFT);
            }
            if ($primerPremio < 99999) {
                $numbers[] = str_pad($primerPremio + 1, 5, '0', STR_PAD_LEFT);
            }
        }
        
        // Anterior y posterior al segundo premio
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $segundoPremio = intval($lotteryResult->segundo_premio['decimo']);
            if ($segundoPremio > 0) {
                $numbers[] = str_pad($segundoPremio - 1, 5, '0', STR_PAD_LEFT);
            }
            if ($segundoPremio < 99999) {
                $numbers[] = str_pad($segundoPremio + 1, 5, '0', STR_PAD_LEFT);
            }
        }
        
        // Anterior y posterior a terceros premios
        if ($lotteryResult->terceros_premios && is_array($lotteryResult->terceros_premios)) {
            foreach ($lotteryResult->terceros_premios as $tercerPremio) {
                if (isset($tercerPremio['decimo'])) {
                    $tercerPremioNum = intval($tercerPremio['decimo']);
                    if ($tercerPremioNum > 0) {
                        $numbers[] = str_pad($tercerPremioNum - 1, 5, '0', STR_PAD_LEFT);
                    }
                    if ($tercerPremioNum < 99999) {
                        $numbers[] = str_pad($tercerPremioNum + 1, 5, '0', STR_PAD_LEFT);
                    }
                }
            }
        }
        
        // Anterior y posterior a cuartos premios
        if ($lotteryResult->cuartos_premios && is_array($lotteryResult->cuartos_premios)) {
            foreach ($lotteryResult->cuartos_premios as $cuartoPremio) {
                if (isset($cuartoPremio['decimo'])) {
                    $cuartoPremioNum = intval($cuartoPremio['decimo']);
                    if ($cuartoPremioNum > 0) {
                        $numbers[] = str_pad($cuartoPremioNum - 1, 5, '0', STR_PAD_LEFT);
                    }
                    if ($cuartoPremioNum < 99999) {
                        $numbers[] = str_pad($cuartoPremioNum + 1, 5, '0', STR_PAD_LEFT);
                    }
                }
            }
        }
        
        // Anterior y posterior a quintos premios
        if ($lotteryResult->quintos_premios && is_array($lotteryResult->quintos_premios)) {
            foreach ($lotteryResult->quintos_premios as $quintoPremio) {
                if (isset($quintoPremio['decimo'])) {
                    $quintoPremioNum = intval($quintoPremio['decimo']);
                    if ($quintoPremioNum > 0) {
                        $numbers[] = str_pad($quintoPremioNum - 1, 5, '0', STR_PAD_LEFT);
                    }
                    if ($quintoPremioNum < 99999) {
                        $numbers[] = str_pad($quintoPremioNum + 1, 5, '0', STR_PAD_LEFT);
                    }
                }
            }
        }
        
        // Números con terminaciones del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            $lastDigit = substr($primerPremio, -1);
            $lastTwoDigits = substr($primerPremio, -2);
            $lastThreeDigits = substr($primerPremio, -3);
            
            // Última cifra (10,000 números) - excluir el número principal
            for ($i = 0; $i <= 99999; $i += 10) {
                $number = $i + intval($lastDigit);
                if ($number <= 99999 && $number != intval($primerPremio)) {
                    $numbers[] = str_pad($number, 5, '0', STR_PAD_LEFT);
                }
            }
            
            // 2 últimas cifras (1,000 números) - excluir el número principal
            for ($i = 0; $i <= 99999; $i += 100) {
                $number = $i + intval($lastTwoDigits);
                if ($number <= 99999 && $number != intval($primerPremio)) {
                    $numbers[] = str_pad($number, 5, '0', STR_PAD_LEFT);
                }
            }
            
            // 3 últimas cifras (100 números) - excluir el número principal
            for ($i = 0; $i <= 99999; $i += 1000) {
                $number = $i + intval($lastThreeDigits);
                if ($number <= 99999 && $number != intval($primerPremio)) {
                    $numbers[] = str_pad($number, 5, '0', STR_PAD_LEFT);
                }
            }
        }
        
        return $numbers;
    }

    /**
     * Calcular premios para un número específico
     */
    private function calculateNumberPrizes($number, $lotteryResult, $typeIdentifier, $categories)
    {
        $prizeInfo = [
            'number' => $number,
            'total_prize' => 0,
            'prizes' => []
        ];


        // 1. Verificar premios principales (NO acumulan entre sí)
        $this->checkMainPrizes($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 2. Verificar premios derivados (SÍ acumulan)
        $this->checkDerivedPrizes($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 3. Verificar extracciones
        $this->checkExtractions($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 4. Verificar reintegros
        $this->checkReintegros($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 5. Verificar pedreas (solo para sorteos de Navidad)
        $this->checkPedreas($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);

        return $prizeInfo;
    }

    /**
     * Verificar premios principales (1º, 2º, 3º, 4º, 5º)
     */
    private function checkMainPrizes($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        $mainPrizes = [
            'primer_premio' => $lotteryResult->primer_premio,
            'segundo_premio' => $lotteryResult->segundo_premio,
            'terceros_premios' => $lotteryResult->terceros_premios ?? [],
            'cuartos_premios' => $lotteryResult->cuartos_premios ?? [],
            'quintos_premios' => $lotteryResult->quintos_premios ?? []
        ];

        foreach ($mainPrizes as $prizeType => $prizeData) {
            if (!$prizeData) continue;

            $isArray = is_array($prizeData) && isset($prizeData[0]);
            
            if ($isArray) {
                // Múltiples premios (terceros, cuartos, quintos)
                foreach ($prizeData as $premio) {
                    if (isset($premio['decimo']) && $this->compareNumbers($number, $premio['decimo'])) {
                        $prizeAmount = $this->getPrizeAmount($prizeType, $typeIdentifier, $categories);
                        $prizeInfo['total_prize'] += $prizeAmount;
                        $prizeInfo['prizes'][] = [
                            'category' => $this->getCategoryName($prizeType),
                            'amount' => $prizeAmount,
                            'type' => 'main'
                        ];
                        return; // Solo puede ganar un premio principal
                    }
                }
            } else {
                // Premio único (primer, segundo)
                if (isset($prizeData['decimo']) && $this->compareNumbers($number, $prizeData['decimo'])) {
                    $prizeAmount = $this->getPrizeAmount($prizeType, $typeIdentifier, $categories);
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => $this->getCategoryName($prizeType),
                        'amount' => $prizeAmount,
                        'type' => 'main'
                    ];
                    return; // Solo puede ganar un premio principal
                }
            }
        }
    }

    /**
     * Verificar premios derivados (centenas, anterior/posterior, aproximaciones)
     */
    private function checkDerivedPrizes($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // Los premios derivados SÍ se suman a los premios principales

        // Centenas del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if ($this->isInCentena($number, $primerPremio) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmount('centenasPrimerPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Centenas del Primer Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }

        // Centenas del segundo premio
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $segundoPremio = $lotteryResult->segundo_premio['decimo'];
            if ($this->isInCentena($number, $segundoPremio) && !$this->compareNumbers($number, $segundoPremio)) {
                $prizeAmount = $this->getPrizeAmount('centenasSegundoPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Centenas del Segundo Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }

        // Centenas del tercer premio
        if ($lotteryResult->terceros_premios && is_array($lotteryResult->terceros_premios)) {
            foreach ($lotteryResult->terceros_premios as $tercerPremio) {
                if (isset($tercerPremio['decimo'])) {
                    $tercerPremioNum = $tercerPremio['decimo'];
                    if ($this->isInCentena($number, $tercerPremioNum) && !$this->compareNumbers($number, $tercerPremioNum)) {
                        $prizeAmount = $this->getPrizeAmount('centenasTercerosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) { // Solo aplicar si tiene premio para este tipo de lotería
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Centenas del Tercer Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                        break; // Solo sumar una vez por tercer premio
                    }
                }
            }
        }

        // Anterior y posterior al primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            $primerPremioInt = intval($primerPremio);
            $numberInt = intval($number);
            
            if ($numberInt === $primerPremioInt - 1) {
                $prizeAmount = $this->getPrizeAmount('anteriorPrimerPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Anterior al Primer Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
            
            if ($numberInt === $primerPremioInt + 1) {
                $prizeAmount = $this->getPrizeAmount('posteriorPrimerPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Posterior al Primer Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }

        // Anterior y posterior al segundo premio
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $segundoPremio = $lotteryResult->segundo_premio['decimo'];
            $segundoPremioInt = intval($segundoPremio);
            $numberInt = intval($number);
            
            if ($numberInt === $segundoPremioInt - 1) {
                $prizeAmount = $this->getPrizeAmount('anteriorSegundoPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Anterior al Segundo Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
            
            if ($numberInt === $segundoPremioInt + 1) {
                $prizeAmount = $this->getPrizeAmount('posteriorSegundoPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Posterior al Segundo Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }

        // Anterior y posterior a terceros premios
        if ($lotteryResult->terceros_premios && is_array($lotteryResult->terceros_premios)) {
            foreach ($lotteryResult->terceros_premios as $tercerPremio) {
                if (isset($tercerPremio['decimo'])) {
                    $tercerPremioNum = $tercerPremio['decimo'];
                    $tercerPremioInt = intval($tercerPremioNum);
                    $numberInt = intval($number);
                    
                    if ($numberInt === $tercerPremioInt - 1) {
                        $prizeAmount = $this->getPrizeAmount('anteriorTercerosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Anterior al Tercer Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                    }
                    
                    if ($numberInt === $tercerPremioInt + 1) {
                        $prizeAmount = $this->getPrizeAmount('posteriorTercerosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Posterior al Tercer Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                    }
                }
            }
        }

        // Anterior y posterior a cuartos premios
        if ($lotteryResult->cuartos_premios && is_array($lotteryResult->cuartos_premios)) {
            foreach ($lotteryResult->cuartos_premios as $cuartoPremio) {
                if (isset($cuartoPremio['decimo'])) {
                    $cuartoPremioNum = $cuartoPremio['decimo'];
                    $cuartoPremioInt = intval($cuartoPremioNum);
                    $numberInt = intval($number);
                    
                    if ($numberInt === $cuartoPremioInt - 1) {
                        $prizeAmount = $this->getPrizeAmount('anteriorCuartosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Anterior al Cuarto Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                    }
                    
                    if ($numberInt === $cuartoPremioInt + 1) {
                        $prizeAmount = $this->getPrizeAmount('posteriorCuartosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Posterior al Cuarto Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                    }
                }
            }
        }

        // Anterior y posterior a quintos premios
        if ($lotteryResult->quintos_premios && is_array($lotteryResult->quintos_premios)) {
            foreach ($lotteryResult->quintos_premios as $quintoPremio) {
                if (isset($quintoPremio['decimo'])) {
                    $quintoPremioNum = $quintoPremio['decimo'];
                    $quintoPremioInt = intval($quintoPremioNum);
                    $numberInt = intval($number);
                    
                    if ($numberInt === $quintoPremioInt - 1) {
                        $prizeAmount = $this->getPrizeAmount('anteriorQuintosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Anterior al Quinto Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                    }
                    
                    if ($numberInt === $quintoPremioInt + 1) {
                        $prizeAmount = $this->getPrizeAmount('posteriorQuintosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => 'Posterior al Quinto Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                    }
                }
            }
        }

        // Aproximaciones (2, 3, 4 últimas cifras)
        $this->checkApproximations($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
    }

    /**
     * Verificar aproximaciones (2, 3, 4 últimas cifras)
     */
    private function checkApproximations($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // 2 últimas cifras del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -2) === substr($primerPremio, -2) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmount('dosUltimasCifrasPrimerPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => '2 Últimas Cifras del Primer Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }

        // 3 últimas cifras del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -3) === substr($primerPremio, -3) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmount('tresUltimasCifrasPrimerPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => '3 Últimas Cifras del Primer Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }


        // 1 última cifra del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -1) === substr($primerPremio, -1) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmount('ultimaCifraPrimerPremio', $typeIdentifier, $categories);
                $prizeInfo['total_prize'] += $prizeAmount;
                $prizeInfo['prizes'][] = [
                    'category' => 'Última Cifra del Primer Premio',
                    'amount' => $prizeAmount,
                    'type' => 'derived'
                ];
            }
        }

        // 4 últimas cifras del primer premio (solo para 3€)
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -4) === substr($primerPremio, -4) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmount('cuatroUltimasCifrasPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) { // Solo aplicar si tiene premio para este tipo de lotería
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => '4 Últimas Cifras del Primer Premio',
                        'amount' => $prizeAmount,
                        'type' => 'derived'
                    ];
                }
            }
        }

        // 2 últimas cifras del segundo premio
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $segundoPremio = $lotteryResult->segundo_premio['decimo'];
            if (substr($number, -2) === substr($segundoPremio, -2) && !$this->compareNumbers($number, $segundoPremio)) {
                $prizeAmount = $this->getPrizeAmount('dosUltimasCifrasSegundoPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => '2 Últimas Cifras del Segundo Premio',
                        'amount' => $prizeAmount,
                        'type' => 'derived'
                    ];
                }
            }
        }

        // 2 últimas cifras del tercer premio
        if ($lotteryResult->terceros_premios && is_array($lotteryResult->terceros_premios)) {
            foreach ($lotteryResult->terceros_premios as $tercerPremio) {
                if (isset($tercerPremio['decimo'])) {
                    $tercerPremioNum = $tercerPremio['decimo'];
                    if (substr($number, -2) === substr($tercerPremioNum, -2) && !$this->compareNumbers($number, $tercerPremioNum)) {
                        $prizeAmount = $this->getPrizeAmount('dosUltimasCifrasTercerosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => '2 Últimas Cifras del Tercer Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                        break; // Solo sumar una vez por tercer premio
                    }
                }
            }
        }

        // 2 últimas cifras del cuarto premio
        if ($lotteryResult->cuartos_premios && is_array($lotteryResult->cuartos_premios)) {
            foreach ($lotteryResult->cuartos_premios as $cuartoPremio) {
                if (isset($cuartoPremio['decimo'])) {
                    $cuartoPremioNum = $cuartoPremio['decimo'];
                    if (substr($number, -2) === substr($cuartoPremioNum, -2) && !$this->compareNumbers($number, $cuartoPremioNum)) {
                        $prizeAmount = $this->getPrizeAmount('dosUltimasCifrasCuartosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => '2 Últimas Cifras del Cuarto Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                        break; // Solo sumar una vez por cuarto premio
                    }
                }
            }
        }

        // 2 últimas cifras del quinto premio
        if ($lotteryResult->quintos_premios && is_array($lotteryResult->quintos_premios)) {
            foreach ($lotteryResult->quintos_premios as $quintoPremio) {
                if (isset($quintoPremio['decimo'])) {
                    $quintoPremioNum = $quintoPremio['decimo'];
                    if (substr($number, -2) === substr($quintoPremioNum, -2) && !$this->compareNumbers($number, $quintoPremioNum)) {
                        $prizeAmount = $this->getPrizeAmount('dosUltimasCifrasQuintosPremios', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'category' => '2 Últimas Cifras del Quinto Premio',
                                'amount' => $prizeAmount,
                                'type' => 'derived'
                            ];
                        }
                        break; // Solo sumar una vez por quinto premio
                    }
                }
            }
        }

    }

    /**
     * Verificar extracciones
     */
    private function checkExtractions($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // Extracciones de 5 cifras
        if ($lotteryResult->extracciones_cinco_cifras) {
            foreach ($lotteryResult->extracciones_cinco_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && $this->compareNumbers($extraccion['decimo'], $number)) {
                    $prizeAmount = $this->getPrizeAmount('extraccionesDeCincoCifras', $typeIdentifier, $categories);
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => 'Extracción de 5 Cifras',
                        'amount' => $prizeAmount,
                        'type' => 'extraction'
                    ];
                    break; // Solo sumar una vez por número
                }
            }
        }

        // Extracciones de 4 cifras
        if ($lotteryResult->extracciones_cuatro_cifras) {
            foreach ($lotteryResult->extracciones_cuatro_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && substr($number, -4) === $extraccion['decimo']) {
                    $prizeAmount = $this->getPrizeAmount('extraccionesDeCuatroCifras', $typeIdentifier, $categories);
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => 'Extracción de 4 Cifras',
                        'amount' => $prizeAmount,
                        'type' => 'extraction'
                    ];
                    break; // Solo sumar una vez por número
                }
            }
        }

        // Extracciones de 3 cifras
        if ($lotteryResult->extracciones_tres_cifras) {
            foreach ($lotteryResult->extracciones_tres_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && substr($number, -3) === $extraccion['decimo']) {
                    $prizeAmount = $this->getPrizeAmount('extraccionesDeTresCifras', $typeIdentifier, $categories);
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => 'Extracción de 3 Cifras',
                        'amount' => $prizeAmount,
                        'type' => 'extraction'
                    ];
                    break; // Solo sumar una vez por número
                }
            }
        }

        // Extracciones de 2 cifras
        if ($lotteryResult->extracciones_dos_cifras) {
            foreach ($lotteryResult->extracciones_dos_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && substr($number, -2) === $extraccion['decimo']) {
                    $prizeAmount = $this->getPrizeAmount('extraccionesDeDosCifras', $typeIdentifier, $categories);
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => 'Extracción de 2 Cifras',
                        'amount' => $prizeAmount,
                        'type' => 'extraction'
                    ];
                    break; // Solo sumar una vez por número
                }
            }
        }
    }

    /**
     * Verificar reintegros
     */
    private function checkReintegros($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        if ($lotteryResult->reintegros) {
            $lastDigit = substr($number, -1);
            
            // Verificar si el número es el PRIMER PREMIO (no debe sumar reintegro)
            // Solo el primer premio no suma reintegro, los demás premios SÍ suman reintegro
            $isFirstPrize = $this->isFirstPrizeNumber($number, $lotteryResult);
            
            if (!$isFirstPrize) {
                // Verificar si ya tiene premio de "Última Cifra del Primer Premio"
                $hasUltimaCifra = false;
                foreach ($prizeInfo['prizes'] as $prize) {
                    if ($prize['category'] === 'Última Cifra del Primer Premio') {
                        $hasUltimaCifra = true;
                        break;
                    }
                }
                
                // Solo sumar reintegro si NO tiene ya premio de última cifra
                if (!$hasUltimaCifra) {
                    // Verificar si coincide con ALGÚN reintegro (solo sumar una vez)
                    $hasReintegro = false;
                    foreach ($lotteryResult->reintegros as $reintegro) {
                        if (isset($reintegro['decimo']) && $reintegro['decimo'] === $lastDigit) {
                            $hasReintegro = true;
                            break; // Solo necesitamos saber si coincide, no cuántas veces
                        }
                    }
                    
                    if ($hasReintegro) {
                        $prizeAmount = $this->getPrizeAmount('reintegros', $typeIdentifier, $categories);
                        $prizeInfo['total_prize'] += $prizeAmount;
                        $prizeInfo['prizes'][] = [
                            'category' => 'Reintegro',
                            'amount' => $prizeAmount,
                            'type' => 'reintegro'
                        ];
                    }
                }
            }
        }
    }

    /**
     * Verificar si un número es el PRIMER PREMIO (no debe sumar reintegro)
     * Solo el primer premio no suma reintegro, los demás premios SÍ suman reintegro
     */
    private function isFirstPrizeNumber($number, $lotteryResult)
    {
        // Solo verificar primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            if ($this->compareNumbers($number, $lotteryResult->primer_premio['decimo'])) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Verificar pedreas (solo para sorteos de Navidad)
     */
    private function checkPedreas($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // Solo verificar pedreas si existen en el resultado
        if (!$lotteryResult->pedreas || !is_array($lotteryResult->pedreas)) {
            return;
        }
        
        foreach ($lotteryResult->pedreas as $pedrea) {
            if (isset($pedrea['decimo']) && $this->compareNumbers($number, $pedrea['decimo'])) {
                $prizeAmount = $this->getPrizeAmount('pedrea', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'category' => 'Pedrea',
                        'amount' => $prizeAmount,
                        'type' => 'pedrea'
                    ];
                }
                break; // Solo sumar una vez por número
            }
        }
    }

    /**
     * Verificar si un número está en la centena de otro
     */
    private function isInCentena($number, $referenceNumber)
    {
        $numberInt = intval($number);
        $referenceInt = intval($referenceNumber);
        
        $centenaStart = intval($referenceInt / 100) * 100;
        $centenaEnd = $centenaStart + 99;
        
        return $numberInt >= $centenaStart && $numberInt <= $centenaEnd;
    }

    /**
     * Obtener importe del premio desde la configuración
     */
    private function getPrizeAmount($categoryKey, $typeIdentifier, $categories)
    {
        // Mapear claves de snake_case a camelCase
        $keyMapping = [
            'primer_premio' => 'primerPremio',
            'segundo_premio' => 'segundoPremio',
            'terceros_premios' => 'tercerosPremios',
            'cuartos_premios' => 'cuartosPremios',
            'quintos_premios' => 'quintosPremios',
            'anteriorPrimerPremio' => 'anteriorPrimerPremio',
            'posteriorPrimerPremio' => 'posteriorPrimerPremio',
            'anteriorSegundoPremio' => 'anteriorSegundoPremio',
            'posteriorSegundoPremio' => 'posteriorSegundoPremio',
            'anteriorTercerosPremios' => 'anteriorTercerosPremios',
            'posteriorTercerosPremios' => 'posteriorTercerosPremios',
            'centenasPrimerPremio' => 'centenasPrimerPremio',
            'centenasSegundoPremio' => 'centenasSegundoPremio',
            'centenasTercerosPremios' => 'centenasTercerosPremios',
            'centenasCuartosPremios' => 'centenasCuartosPremios',
            'premioFraccionSeriePrimerPremio' => 'premioFraccionSeriePrimerPremio',
            'cuatroUltimasCifrasPrimerPremio' => 'cuatroUltimasCifrasPrimerPremio',
            'tresUltimasCifrasPrimerPremio' => 'tresUltimasCifrasPrimerPremio',
            'dosUltimasCifrasPrimerPremio' => 'dosUltimasCifrasPrimerPremio',
            'ultimaCifraPrimerPremio' => 'ultimaCifraPrimerPremio',
            'tresUltimasCifrasSegundoPremio' => 'tresUltimasCifrasSegundoPremio',
            'dosUltimasCifrasSegundoPremio' => 'dosUltimasCifrasSegundoPremio',
            'dosUltimasCifrasTercerPremio' => 'dosUltimasCifrasTercerPremio',
            'extraccionesDeCincoCifras' => 'extraccionesDeCincoCifras',
            'extraccionesDeCuatroCifras' => 'extraccionesDeCuatroCifras',
            'extraccionesDeTresCifras' => 'extraccionesDeTresCifras',
            'extraccionesDeDosCifras' => 'extraccionesDeDosCifras',
            'reintegros' => 'reintegros',
            'pedrea' => 'pedrea'
        ];
        
        $mappedKey = $keyMapping[$categoryKey] ?? $categoryKey;
        
        foreach ($categories as $category) {
            if ($category['key_categoria'] === $mappedKey) {
                $amount = $category['importe_por_tipo'][$typeIdentifier] ?? 0;
                
                // Log temporal para debugging (deshabilitado para evitar spam)
                // if ($amount > 0) {
                //     \Log::info("Premio encontrado: {$categoryKey} -> {$mappedKey} -> {$amount} para tipo {$typeIdentifier}");
                // }
                
                return $amount;
            }
        }
        
        // Log temporal para debugging (deshabilitado para evitar spam)
        // \Log::info("Premio NO encontrado: {$categoryKey} -> {$mappedKey} para tipo {$typeIdentifier}");
        
        return 0;
    }

    /**
     * Obtener nombre de la categoría
     */
    private function getCategoryName($categoryKey)
    {
        $names = [
            'primer_premio' => 'Primer Premio',
            'segundo_premio' => 'Segundo Premio',
            'terceros_premios' => 'Tercer Premio',
            'cuartos_premios' => 'Cuarto Premio',
            'quintos_premios' => 'Quinto Premio'
        ];
        
        return $names[$categoryKey] ?? $categoryKey;
    }

    /**
     * Comparar números normalizando formatos (con/sin ceros a la izquierda)
     */
    private function compareNumbers($number1, $number2)
    {
        // Normalizar ambos números a formato de 5 dígitos
        $normalized1 = str_pad($number1, 5, '0', STR_PAD_LEFT);
        $normalized2 = str_pad($number2, 5, '0', STR_PAD_LEFT);
        
        $result = $normalized1 === $normalized2;
        
        return $result;
    }
}
