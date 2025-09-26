<?php

namespace App\Http\Controllers;

use App\Models\Lottery;
use App\Models\LotteryResult;
use App\Models\Administration;
use App\Models\Entity;
use App\Models\Reserve;
use App\Models\AdministrationLotteryScrutiny;
use App\Models\ScrutinyEntityResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Participation;
use App\Models\Set;

class LotteryScrutinyController extends Controller
{
    /**
     * Mostrar el formulario de escrutinio para una administración específica
     * Incluye tanto el escrutinio normal como el escrutinio por categoría
     */
    public function show($lotteryId)
    {
        $lottery = Lottery::with(['lotteryType', 'result'])->findOrFail($lotteryId);
        
        // Verificar que hay una administración seleccionada
        $administrationId = session('selected_administration.id');
        if (!$administrationId) {
            return redirect()->route('lottery.administrations')
                ->with('error', 'Debe seleccionar una administración primero');
        }

        $administration = Administration::findOrFail($administrationId);

        // Verificar que el sorteo tiene resultados
        if (!$lottery->result) {
            return redirect()->route('lottery.results')
                ->with('error', 'Este sorteo aún no tiene resultados publicados');
        }

        // Verificar si ya existe un escrutinio para esta administración
        $existingScrutiny = AdministrationLotteryScrutiny::where('administration_id', $administrationId)
            ->where('lottery_id', $lotteryId)
            ->first();

        if ($existingScrutiny && $existingScrutiny->is_scrutinized) {
            return redirect()->route('lottery.show-administration-scrutiny', [$lotteryId, $administrationId])
                ->with('info', 'Este sorteo ya ha sido escrutado para esta administración');
        }

        // Obtener entidades de la administración que tienen reservas para este sorteo
        $entitiesWithReserves = Entity::where('administration_id', $administrationId)
            ->whereHas('reserves', function ($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId)
                      ->where('status', 1); // Solo reservas confirmadas
            })
            ->with(['reserves' => function ($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId)
                      ->where('status', 1);
            }])
            ->get();

        if ($entitiesWithReserves->isEmpty()) {
            return redirect()->route('lottery.results')
                ->with('warning', 'No hay entidades con reservas confirmadas para este sorteo en la administración seleccionada');
        }

        // Preparar datos para la vista normal
        $scrutinyData = $this->prepareScrutinyData($lottery, $entitiesWithReserves);

        // Obtener números reservados para el escrutinio por categoría
        $reservedNumbers = $this->getReservedNumbersForAdministration($administrationId, $lotteryId);
        
        // Calcular premios para cada número reservado (escrutinio por categoría)
        $scrutinyResults = $this->calculateCategoryScrutiny($lottery, $reservedNumbers);
        
        // Organizar resultados por entidad para mostrar en la tabla
        $scrutinyResultsByEntity = $this->organizeResultsByEntity($scrutinyResults, $entitiesWithReserves, $lotteryId);

        return view('lottery.scrutiny', compact('lottery', 'administration', 'entitiesWithReserves', 'scrutinyData', 'scrutinyResults', 'scrutinyResultsByEntity'));
    }

    /**
     * Procesar y guardar el escrutinio
     */
    public function process(Request $request, $lotteryId)
    {
        $lottery = Lottery::with('result')->findOrFail($lotteryId);
        $administrationId = session('selected_administration.id');

        if (!$administrationId) {
            return redirect()->route('lottery.administrations')
                ->with('error', 'Debe seleccionar una administración primero');
        }

        if (!$lottery->result) {
            return redirect()->route('lottery.results')
                ->with('error', 'Este sorteo aún no tiene resultados');
        }

        try {
            DB::beginTransaction();

            // Obtener entidades con reservas para este sorteo
            $entitiesWithReserves = Entity::where('administration_id', $administrationId)
                ->whereHas('reserves', function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id)
                          ->where('status', 1);
                })
                ->with(['reserves' => function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id)
                          ->where('status', 1);
                }])
                ->get();

            // Preparar datos para el escrutinio
            $scrutinyData = $this->prepareScrutinyData($lottery, $entitiesWithReserves);
            
            // Obtener números reservados para el escrutinio por categoría
            $reservedNumbers = $this->getReservedNumbersForAdministration($administrationId, $lotteryId);
            
            // Calcular premios para cada número reservado (escrutinio por categoría)
            $scrutinyResults = $this->calculateCategoryScrutiny($lottery, $reservedNumbers);
            
            // Organizar resultados por entidad para mostrar en la tabla
            $scrutinyResultsByEntity = $this->organizeResultsByEntity($scrutinyResults, $entitiesWithReserves, $lotteryId);

            // Calcular totales correctos desde los resultados por categoría
            $totalWinning = 0;
            $totalNonWinning = 0;
            $totalPrizeAmount = 0;
            $totalAsignadas = 0;
            
            // Sumar participaciones ganadoras desde los resultados por categoría
            foreach($scrutinyResultsByEntity as $entityId => $entityResults) {
                foreach($entityResults as $categoryResult) {
                    $decimosInfo = $categoryResult['decimos_info'] ?? [];
                    $totalParticipations = $decimosInfo['total_participations'] ?? 0;
                    $totalWinning += $totalParticipations;
                    
                    // Calcular premio total
                    $totalDecimos = $decimosInfo['total_decimos'] ?? 0;
                    $premioPorDecimo = $categoryResult['total_prize'];
                    $premioTotal = $premioPorDecimo * $totalDecimos;
                    $totalPrizeAmount += $premioTotal;
                }
            }
            
            // Obtener total de participaciones asignadas de TODAS las entidades
            $totalAsignadas = \App\Models\Participation::whereHas('set.reserve', function($query) use ($lotteryId) {
                    $query->where('lottery_id', $lotteryId);
                })
                ->whereHas('entity', function($query) use ($administrationId) {
                    $query->where('administration_id', $administrationId);
                })
                ->where('status', 'asignada')
                ->count();
            
            // Participaciones no ganadoras = total asignadas - ganadoras
            $totalNonWinning = $totalAsignadas - $totalWinning;

            // Obtener el total de premios de TODOS los escrutinios guardados para este sorteo
            $totalAllScrutinies = DB::table('scrutiny_detailed_results')
                ->join('administration_lottery_scrutinies', 'scrutiny_detailed_results.scrutiny_id', '=', 'administration_lottery_scrutinies.id')
                ->where('administration_lottery_scrutinies.lottery_id', $lotteryId)
                ->where('administration_lottery_scrutinies.is_saved', true)
                ->sum('scrutiny_detailed_results.premio_total');

            // Crear o actualizar el escrutinio de la administración
            $scrutiny = AdministrationLotteryScrutiny::updateOrCreate([
                'administration_id' => $administrationId,
                'lottery_id' => $lotteryId
            ], [
                'lottery_result_id' => $lottery->result->id,
                'scrutiny_date' => now(),
                'is_scrutinized' => true,
                'scrutinized_by' => Auth::id(),
                'comments' => $request->input('comments'),
                'scrutiny_summary' => [
                    'total_entities' => count($entitiesWithReserves),
                    'total_winning_participations' => $totalWinning,
                    'total_non_winning_participations' => $totalNonWinning,
                    'total_prize_amount' => $totalAllScrutinies + $totalPrizeAmount
                ]
            ]);

            // Guardar resultados detallados por entidad
            $this->saveDetailedScrutinyResults($scrutiny, $scrutinyResultsByEntity, $lottery);

            DB::commit();

            return redirect()->route('lottery.show-administration-scrutiny', [$lotteryId, $administrationId])
                ->with('success', 'Escrutinio procesado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al procesar escrutinio: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Error al procesar el escrutinio: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar los resultados del escrutinio de una administración
     */
    public function showResults($lotteryId, $administrationId)
    {
        $lottery = Lottery::with(['lotteryType', 'result'])->findOrFail($lotteryId);
        $administration = Administration::findOrFail($administrationId);

        $scrutiny = AdministrationLotteryScrutiny::where('administration_id', $administrationId)
            ->where('lottery_id', $lotteryId)
            ->with(['detailedResults.entity', 'detailedResults.set', 'scrutinizedBy', 'savedBy'])
            ->firstOrFail();

        if (!$scrutiny->is_scrutinized) {
            return redirect()->route('lottery.scrutiny', $lotteryId)
                ->with('info', 'El escrutinio aún no ha sido completado');
        }

        // Obtener el total de premios de TODOS los escrutinios guardados para este sorteo
        $totalAllScrutinies = DB::table('scrutiny_detailed_results')
            ->join('administration_lottery_scrutinies', 'scrutiny_detailed_results.scrutiny_id', '=', 'administration_lottery_scrutinies.id')
            ->where('administration_lottery_scrutinies.lottery_id', $lotteryId)
            ->where('administration_lottery_scrutinies.is_saved', true)
            ->sum('scrutiny_detailed_results.premio_total');

        // Actualizar el resumen con el total de todos los escrutinios
        $currentSummary = $scrutiny->scrutiny_summary;
        $currentSummary['total_prize_amount'] = $totalAllScrutinies;
        $scrutiny->scrutiny_summary = $currentSummary;
        $scrutiny->save();

        return view('lottery.scrutiny_results', compact('lottery', 'administration', 'scrutiny'));
    }

    /**
     * Verificar premios de una participación usando las nuevas categorías
     */
    private function checkParticipationPrizes($participation, $lottery, $lotteryResult)
    {
        $typeIdentifier = $lottery->getLotteryTypeIdentifier();
        $categories = config('lotteryCategories');
        $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
        
        $totalPrize = 0;
        $winningCategories = [];
        
        foreach ($reservedNumbers as $number) {
            foreach ($categories as $category) {
                $prizeAmount = $category['importe_por_tipo'][$typeIdentifier] ?? 0;
                
                if ($prizeAmount > 0) {
                    $won = $this->checkCategoryWin($number, $category, $lotteryResult);
                    
                    if ($won) {
                        // Calcular premio proporcional a la participación
                        $participationPrize = $this->calculateParticipationPrize(
                            $prizeAmount, 
                            $participation,
                            $participation->set->total_participations
                        );
                        
                        $totalPrize += $participationPrize;
                        $winningCategories[] = [
                            'categoria' => $category['nombre_categoria'],
                            'key' => $category['key_categoria'],
                            'numero' => $number,
                            'premio_serie' => $prizeAmount,
                            'premio_participacion' => $participationPrize
                        ];
                    }
                }
            }
        }
        
        return [
            'total_prize' => $totalPrize,
            'categories' => $winningCategories,
            'has_won' => $totalPrize > 0
        ];
    }

    /**
     * Verificar si un número gana en una categoría específica
     */
    private function checkCategoryWin($number, $category, $lotteryResult)
    {
        $key = $category['key_categoria'];
        $numberStr = str_pad($number, 5, '0', STR_PAD_LEFT);
        
        switch ($key) {
            case 'primerPremio':
                return isset($lotteryResult->primerPremio['decimo']) && 
                       $lotteryResult->primerPremio['decimo'] == $numberStr;
                       
            case 'segundoPremio':
                return isset($lotteryResult->segundoPremio['decimo']) && 
                       $lotteryResult->segundoPremio['decimo'] == $numberStr;
                       
            case 'tercerosPremios':
                if (isset($lotteryResult->tercerosPremios)) {
                    foreach ($lotteryResult->tercerosPremios as $premio) {
                        if ($premio['decimo'] == $numberStr) return true;
                    }
                }
                return false;
                
            case 'anteriorPrimerPremio':
                if (isset($lotteryResult->primerPremio['decimo'])) {
                    $primerPremio = $lotteryResult->primerPremio['decimo'];
                    $anterior = str_pad((intval($primerPremio) - 1), 5, '0', STR_PAD_LEFT);
                    return $numberStr == $anterior;
                }
                return false;
                
            case 'posteriorPrimerPremio':
                if (isset($lotteryResult->primerPremio['decimo'])) {
                    $primerPremio = $lotteryResult->primerPremio['decimo'];
                    $posterior = str_pad((intval($primerPremio) + 1), 5, '0', STR_PAD_LEFT);
                    return $numberStr == $posterior;
                }
                return false;
                
            case 'extraccionesDeTresCifras':
                $lastThree = substr($numberStr, -3);
                if (isset($lotteryResult->extraccionesDeTresCifras)) {
                    foreach ($lotteryResult->extraccionesDeTresCifras as $extraccion) {
                        if ($extraccion['decimo'] == $lastThree) return true;
                    }
                }
                return false;
                
            case 'extraccionesDeDosCifras':
                $lastTwo = substr($numberStr, -2);
                if (isset($lotteryResult->extraccionesDeDosCifras)) {
                    foreach ($lotteryResult->extraccionesDeDosCifras as $extraccion) {
                        if ($extraccion['decimo'] == $lastTwo) return true;
                    }
                }
                return false;
                
            case 'reintegros':
                $lastOne = substr($numberStr, -1);
                if (isset($lotteryResult->reintegros)) {
                    foreach ($lotteryResult->reintegros as $reintegro) {
                        if ($reintegro['decimo'] == $lastOne) return true;
                    }
                }
                return false;
                
            case 'centenasPrimerPremio':
                if (isset($lotteryResult->primerPremio['decimo'])) {
                    $primerPremio = $lotteryResult->primerPremio['decimo'];
                    $centenaPremio = substr($primerPremio, 0, 3);
                    $centenaNumero = substr($numberStr, 0, 3);
                    return $centenaNumero == $centenaPremio && $numberStr != $primerPremio;
                }
                return false;
                
            // Añadir más casos según sea necesario...
            default:
                return false;
        }
    }

    /**
     * Calcular el premio proporcional para una participación
     */
    private function calculateParticipationPrize($prizeAmountPerSerie, $participation, $totalParticipations)
    {
        // El premio se divide proporcionalmente entre todas las participaciones del set
        return $prizeAmountPerSerie / $totalParticipations;
    }

    /**
     * Preparar datos para el escrutinio
     */
    private function prepareScrutinyData($lottery, $entitiesWithReserves)
    {
        $scrutinyData = [];
        $lotteryResult = $lottery->result;
        
        // Variables para calcular totales globales
        $allWinningNumbers = [];
        $allNonWinningNumbers = [];
        $totalPrizeAmount = 0;
        
        // Obtener TODOS los números únicos de TODAS las reservas de la administración
        $allReservedNumbers = [];
        foreach ($entitiesWithReserves as $entity) {
            $reserves = Reserve::where('entity_id', $entity->id)
                ->where('lottery_id', $lottery->id)
                ->where('status', 1)
                ->get();
                
            foreach ($reserves as $reserve) {
                if ($reserve->reservation_numbers) {
                    $allReservedNumbers = array_merge($allReservedNumbers, $reserve->reservation_numbers);
                }
            }
        }
        $allReservedNumbers = array_unique($allReservedNumbers);

        foreach ($entitiesWithReserves as $entity) {
            // Obtener participaciones asignadas de esta entidad para este sorteo
            $allAssignedParticipations = Participation::where('entity_id', $entity->id)
                ->whereHas('set.reserve', function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id);
                })
                ->where('status', 'asignada')
                ->get();

            // Obtener todas las participaciones disponibles de esta entidad para este sorteo
            $allParticipations = Participation::where('entity_id', $entity->id)
                ->whereHas('set.reserve', function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id);
                })
                ->whereIn('status', ['disponible', 'asignada', 'devuelta'])
                ->get();

            // Obtener participaciones devueltas de esta entidad para este sorteo
            $returnedParticipations = Participation::where('entity_id', $entity->id)
                ->whereHas('set.reserve', function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id);
                })
                ->where('status', 'devuelta')
                ->get();

            // Obtener los números de las participaciones asignadas
            $assignedNumbers = [];
            foreach ($allAssignedParticipations as $participation) {
                if ($participation->set && $participation->set->reserve) {
                    $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
                    if (isset($reservedNumbers[$participation->participation_number - 1])) {
                        $assignedNumbers[] = $reservedNumbers[$participation->participation_number - 1];
                    }
                }
            }

            // Eliminar duplicados
            $assignedNumbers = array_unique($assignedNumbers);

            // Calcular premios para obtener los números ganadores
            $tempEntityResult = new ScrutinyEntityResult([
                'entity_id' => $entity->id,
                'reserved_numbers' => $assignedNumbers,
                'total_reserved' => $allAssignedParticipations->count(),
                'total_issued' => $allParticipations->count(),
                'total_sold' => $allAssignedParticipations->count(),
                'total_returned' => $returnedParticipations->count()
            ]);

            $tempEntityResult->calculatePrizes($lotteryResult, $lottery->lotteryType);
            
            // Filtrar participaciones asignadas: solo aquellas que están en sets con reservas que tienen números ganadores
            $winningParticipations = [];
            $winningParticipationsByNumber = [];
            $totalWinningParticipations = 0;
            
            foreach ($allAssignedParticipations as $participation) {
                if ($participation->set && $participation->set->reserve) {
                    $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
                    $hasWinningNumber = false;
                    
                    // Verificar si esta reserva tiene algún número ganador
                    foreach ($reservedNumbers as $number) {
                        if (in_array($number, $tempEntityResult->winning_numbers)) {
                            $hasWinningNumber = true;
                            break;
                        }
                    }
                    
                    // Solo incluir participaciones de sets con reservas que tienen números ganadores
                    if ($hasWinningNumber) {
                        $winningParticipations[] = $participation;
                        
                        // Calcular participaciones ganadoras por número específico
                        if (isset($reservedNumbers[$participation->participation_number - 1])) {
                            $number = $reservedNumbers[$participation->participation_number - 1];
                            if (in_array($number, $tempEntityResult->winning_numbers)) {
                                $totalWinningParticipations++;
                                $winningParticipationsByNumber[$number] = ($winningParticipationsByNumber[$number] ?? 0) + 1;
                            }
                        }
                    }
                }
            }

            // Obtener los números de las participaciones ganadoras (solo de sets con reservas ganadoras)
            $winningNumbers = [];
            foreach ($winningParticipations as $participation) {
                if ($participation->set && $participation->set->reserve) {
                    $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
                    if (isset($reservedNumbers[$participation->participation_number - 1])) {
                        $winningNumbers[] = $reservedNumbers[$participation->participation_number - 1];
                    }
                }
            }
            $winningNumbers = array_unique($winningNumbers);

            // Calcular participaciones sin premio (sets sin números ganadores)
            $nonWinningParticipations = $allAssignedParticipations->count() - count($winningParticipations);

            // Crear el resultado final con solo las participaciones de sets ganadores
            $entityResult = new ScrutinyEntityResult([
                'entity_id' => $entity->id,
                'reserved_numbers' => $winningNumbers,
                'total_reserved' => count($winningParticipations), // Solo participaciones de sets ganadores
                'total_issued' => $allParticipations->count(), // Total de participaciones emitidas
                'total_sold' => count($winningParticipations), // Solo participaciones de sets ganadores
                //'total_sold' => $allAssignedParticipations->count(), // Solo participaciones de sets ganadores
                'total_returned' => $returnedParticipations->count(),
                'total_non_winning' => $nonWinningParticipations // Participaciones sin premio
            ]);

            $entityResult->winning_participations = $totalWinningParticipations;
            
            // Calcular los premios con las participaciones ganadoras por número
            $entityResult->calculatePrizes($lotteryResult, $lottery->lotteryType, $winningParticipationsByNumber);

            // Acumular números ganadores para el total global
            $allWinningNumbers = array_merge($allWinningNumbers, $entityResult->winning_numbers);
            
            // Solo acumular el premio si la entidad tiene números ganadores
            if ($entityResult->total_winning > 0) {
                $totalPrizeAmount += $entityResult->total_prize_amount;
            }

            $scrutinyData[] = [
                'entity' => $entity,
                'result' => $entityResult
            ];
        }

        // Calcular totales de participaciones (no números únicos)
        $totalWinningParticipations = 0;
        $totalNonWinningParticipations = 0;
        
        foreach ($scrutinyData as $data) {
            $result = $data['result'];
            $totalWinningParticipations += $result->total_winning;
            $totalNonWinningParticipations += ($result->total_issued ?? 0) - ($result->total_winning ?? 0);
        }

        return [
            'entities' => $scrutinyData,
            'summary' => [
                'unique_winning_numbers' => $totalWinningParticipations,
                'unique_non_winning_numbers' => $totalNonWinningParticipations,
                'total_prize_amount' => $totalPrizeAmount
            ]
        ];
    }

    /**
     * Procesar resultados de todas las entidades
     */
    private function processEntityResults($scrutiny, $lottery)
    {
        $lotteryResult = $lottery->result;
        
        // Obtener entidades con reservas para este sorteo
        $entitiesWithReserves = Entity::where('administration_id', $scrutiny->administration_id)
            ->whereHas('reserves', function ($query) use ($lottery) {
                $query->where('lottery_id', $lottery->id)
                      ->where('status', 1);
            })
            ->with(['reserves' => function ($query) use ($lottery) {
                $query->where('lottery_id', $lottery->id)
                      ->where('status', 1);
            }])
            ->get();

        foreach ($entitiesWithReserves as $entity) {
            // Obtener participaciones asignadas de esta entidad para este sorteo
            $allAssignedParticipations = Participation::where('entity_id', $entity->id)
                ->whereHas('set.reserve', function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id);
                })
                ->where('status', 'asignada')
                ->get();

            // Obtener todas las participaciones disponibles de esta entidad para este sorteo
            $allParticipations = Participation::where('entity_id', $entity->id)
                ->whereHas('set.reserve', function ($query) use ($lottery) {
                    $query->where('lottery_id', $lottery->id);
                })
                ->whereIn('status', ['disponible', 'asignada'])
                ->get();

            // Obtener los números de las participaciones asignadas
            $assignedNumbers = [];
            foreach ($allAssignedParticipations as $participation) {
                if ($participation->set && $participation->set->reserve) {
                    $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
                    if (isset($reservedNumbers[$participation->participation_number - 1])) {
                        $assignedNumbers[] = $reservedNumbers[$participation->participation_number - 1];
                    }
                }
            }

            // Eliminar duplicados
            $assignedNumbers = array_unique($assignedNumbers);

            // Calcular premios para obtener los números ganadores
            $tempEntityResult = new ScrutinyEntityResult([
                'entity_id' => $entity->id,
                'reserved_numbers' => $assignedNumbers,
                'total_reserved' => $allAssignedParticipations->count(),
                'total_issued' => $allParticipations->count(),
                'total_sold' => $allAssignedParticipations->count(),
                'total_returned' => 0
            ]);

            $tempEntityResult->calculatePrizes($lotteryResult, $lottery->lotteryType);
            
            // Filtrar participaciones asignadas: solo aquellas que están en sets con reservas que tienen números ganadores
            $winningParticipations = [];
            $winningParticipationsByNumber = [];
            $totalWinningParticipations = 0;
            
            foreach ($allAssignedParticipations as $participation) {
                if ($participation->set && $participation->set->reserve) {
                    $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
                    $hasWinningNumber = false;
                    
                    // Verificar si esta reserva tiene algún número ganador
                    foreach ($reservedNumbers as $number) {
                        if (in_array($number, $tempEntityResult->winning_numbers)) {
                            $hasWinningNumber = true;
                            break;
                        }
                    }
                    
                    // Solo incluir participaciones de sets con reservas que tienen números ganadores
                    if ($hasWinningNumber) {
                        $winningParticipations[] = $participation;
                        
                        // Calcular participaciones ganadoras por número específico
                        if (isset($reservedNumbers[$participation->participation_number - 1])) {
                            $number = $reservedNumbers[$participation->participation_number - 1];
                            if (in_array($number, $tempEntityResult->winning_numbers)) {
                                $totalWinningParticipations++;
                                $winningParticipationsByNumber[$number] = ($winningParticipationsByNumber[$number] ?? 0) + 1;
                            }
                        }
                    }
                }
            }

            // Obtener los números de las participaciones ganadoras (solo de sets con reservas ganadoras)
            $winningNumbers = [];
            foreach ($winningParticipations as $participation) {
                if ($participation->set && $participation->set->reserve) {
                    $reservedNumbers = $participation->set->reserve->reservation_numbers ?? [];
                    if (isset($reservedNumbers[$participation->participation_number - 1])) {
                        $winningNumbers[] = $reservedNumbers[$participation->participation_number - 1];
                    }
                }
            }
            $winningNumbers = array_unique($winningNumbers);

            // Calcular participaciones sin premio (sets sin números ganadores)
            $nonWinningParticipations = $allAssignedParticipations->count() - count($winningParticipations);

            // Crear o actualizar resultado de la entidad con solo las participaciones de sets ganadores
            $entityResult = ScrutinyEntityResult::updateOrCreate([
                'administration_lottery_scrutiny_id' => $scrutiny->id,
                'entity_id' => $entity->id
            ], [
                'reserved_numbers' => $winningNumbers,
                'total_reserved' => count($winningParticipations), // Solo participaciones de sets ganadores
                'total_issued' => $allParticipations->count(), // Total de participaciones emitidas
                'total_sold' => count($winningParticipations), // Solo participaciones de sets ganadores
                'total_returned' => 0,
                'total_non_winning' => $nonWinningParticipations // Participaciones sin premio
            ]);

            $entityResult->winning_participations = $totalWinningParticipations;
            
            // Calcular los premios con las participaciones ganadoras por número
            $entityResult->calculatePrizes($lotteryResult, $lottery->lotteryType, $winningParticipationsByNumber);
            $entityResult->save();
        }
    }


    /**
     * Obtener números reservados para una administración y sorteo
     */
    private function getReservedNumbersForAdministration($administrationId, $lotteryId)
    {
        $reservedNumbers = [];
        
        $entities = Entity::where('administration_id', $administrationId)
            ->whereHas('reserves', function ($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId)
                      ->where('status', 1);
            })
            ->with(['reserves' => function ($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId)
                      ->where('status', 1);
            }])
            ->get();

        foreach ($entities as $entity) {
            foreach ($entity->reserves as $reserve) {
                if ($reserve->reservation_numbers) {
                    $reservedNumbers = array_merge($reservedNumbers, $reserve->reservation_numbers);
                }
            }
        }

        return array_unique($reservedNumbers);
    }

    /**
     * Calcular escrutinio por categoría para números individuales
     */
    private function calculateCategoryScrutiny($lottery, $reservedNumbers)
    {
        $lotteryResult = $lottery->result;
        $typeIdentifier = $lottery->getLotteryTypeIdentifier();
        $categories = config('lotteryCategories');
        
        $results = [];
        
        foreach ($reservedNumbers as $number) {
            $numberStr = str_pad($number, 5, '0', STR_PAD_LEFT);
            
            // Reutilizar la lógica del ScrutinyController pero adaptada para premios por décimo
            $prizeInfo = $this->calculateNumberPrizesForDecimo($numberStr, $lotteryResult, $typeIdentifier, $categories);
            
            if ($prizeInfo['total_prize'] > 0) {
                $results[] = [
                    'number' => $number,
                    'number_str' => $numberStr,
                    'total_prize' => $prizeInfo['total_prize'],
                    'categories' => $prizeInfo['prizes']
                ];
            }
        }
        
        // Ordenar por premio total descendente
        usort($results, function($a, $b) {
            return $b['total_prize'] <=> $a['total_prize'];
        });
        
        return $results;
    }

    /**
     * Calcular premios para un número específico (adaptado del ScrutinyController para premios por décimo)
     */
    private function calculateNumberPrizesForDecimo($number, $lotteryResult, $typeIdentifier, $categories)
    {
        $prizeInfo = [
            'number' => $number,
            'total_prize' => 0,
            'prizes' => []
        ];

        // 1. Verificar premios principales (NO acumulan entre sí)
        $this->checkMainPrizesForDecimo($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 2. Verificar premios derivados (SÍ acumulan)
        $this->checkDerivedPrizesForDecimo($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 3. Verificar extracciones
        $this->checkExtractionsForDecimo($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 4. Verificar reintegros
        $this->checkReintegrosForDecimo($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
        
        // 5. Verificar pedreas (solo para sorteos de Navidad)
        $this->checkPedreasForDecimo($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);

        return $prizeInfo;
    }

    /**
     * Verificar premios principales (adaptado para premios por décimo)
     */
    private function checkMainPrizesForDecimo($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
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
                        $prizeAmount = $this->getPrizeAmountForDecimo($prizeType, $typeIdentifier, $categories);
                        $prizeInfo['total_prize'] += $prizeAmount;
                        $prizeInfo['prizes'][] = [
                            'categoria' => $this->getCategoryName($prizeType),
                            'premio_decimo' => $prizeAmount,
                            'key' => $this->getCategoryKey($prizeType)
                        ];
                        return; // Solo puede ganar un premio principal
                    }
                }
            } else {
                // Premio único (primer, segundo)
                if (isset($prizeData['decimo']) && $this->compareNumbers($number, $prizeData['decimo'])) {
                    $prizeAmount = $this->getPrizeAmountForDecimo($prizeType, $typeIdentifier, $categories);
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => $this->getCategoryName($prizeType),
                        'premio_decimo' => $prizeAmount,
                        'key' => $this->getCategoryKey($prizeType)
                    ];
                    return; // Solo puede ganar un premio principal
                }
            }
        }
    }

    /**
     * Verificar premios derivados (adaptado para premios por décimo)
     */
    private function checkDerivedPrizesForDecimo($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // Centenas del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if ($this->isInCentena($number, $primerPremio) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmountForDecimo('centenasPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Centenas del Primer Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'centenasPrimerPremio'
                    ];
                }
            }
        }

        // Anterior y posterior al primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            $primerPremioInt = intval($primerPremio);
            $numberInt = intval($number);
            
            if ($numberInt === $primerPremioInt - 1) {
                $prizeAmount = $this->getPrizeAmountForDecimo('anteriorPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Anterior al Primer Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'anteriorPrimerPremio'
                    ];
                }
            }
            
            if ($numberInt === $primerPremioInt + 1) {
                $prizeAmount = $this->getPrizeAmountForDecimo('posteriorPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Posterior al Primer Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'posteriorPrimerPremio'
                    ];
                }
            }
        }

        // Anterior y posterior al segundo premio
        if ($lotteryResult->segundo_premio && isset($lotteryResult->segundo_premio['decimo'])) {
            $segundoPremio = $lotteryResult->segundo_premio['decimo'];
            $segundoPremioInt = intval($segundoPremio);
            $numberInt = intval($number);
            
            if ($numberInt === $segundoPremioInt - 1) {
                $prizeAmount = $this->getPrizeAmountForDecimo('anteriorSegundoPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Anterior al Segundo Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'anteriorSegundoPremio'
                    ];
                }
            }
            
            if ($numberInt === $segundoPremioInt + 1) {
                $prizeAmount = $this->getPrizeAmountForDecimo('posteriorSegundoPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Posterior al Segundo Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'posteriorSegundoPremio'
                    ];
                }
            }
        }

        // Aproximaciones (2, 3, 4 últimas cifras)
        $this->checkApproximationsForDecimo($number, $lotteryResult, $typeIdentifier, $categories, $prizeInfo);
    }

    /**
     * Verificar aproximaciones (adaptado para premios por décimo)
     */
    private function checkApproximationsForDecimo($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // 2 últimas cifras del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -2) === substr($primerPremio, -2) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmountForDecimo('dosUltimasCifrasPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => '2 Últimas Cifras del Primer Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'dosUltimasCifrasPrimerPremio'
                    ];
                }
            }
        }

        // 3 últimas cifras del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -3) === substr($primerPremio, -3) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmountForDecimo('tresUltimasCifrasPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => '3 Últimas Cifras del Primer Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'tresUltimasCifrasPrimerPremio'
                    ];
                }
            }
        }

        // 1 última cifra del primer premio
        if ($lotteryResult->primer_premio && isset($lotteryResult->primer_premio['decimo'])) {
            $primerPremio = $lotteryResult->primer_premio['decimo'];
            if (substr($number, -1) === substr($primerPremio, -1) && !$this->compareNumbers($number, $primerPremio)) {
                $prizeAmount = $this->getPrizeAmountForDecimo('ultimaCifraPrimerPremio', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Última Cifra del Primer Premio',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'ultimaCifraPrimerPremio'
                    ];
                }
            }
        }
    }

    /**
     * Verificar extracciones (adaptado para premios por décimo)
     */
    private function checkExtractionsForDecimo($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // Extracciones de 4 cifras
        if ($lotteryResult->extracciones_cuatro_cifras) {
            foreach ($lotteryResult->extracciones_cuatro_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && substr($number, -4) === $extraccion['decimo']) {
                    $prizeAmount = $this->getPrizeAmountForDecimo('extraccionesDeCuatroCifras', $typeIdentifier, $categories);
                    if ($prizeAmount > 0) {
                        $prizeInfo['total_prize'] += $prizeAmount;
                        $prizeInfo['prizes'][] = [
                            'categoria' => 'Extracción de 4 Cifras',
                            'premio_decimo' => $prizeAmount,
                            'key' => 'extraccionesDeCuatroCifras'
                        ];
                    }
                    break; // Solo sumar una vez por número
                }
            }
        }

        // Extracciones de 3 cifras
        if ($lotteryResult->extracciones_tres_cifras) {
            foreach ($lotteryResult->extracciones_tres_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && substr($number, -3) === $extraccion['decimo']) {
                    $prizeAmount = $this->getPrizeAmountForDecimo('extraccionesDeTresCifras', $typeIdentifier, $categories);
                    if ($prizeAmount > 0) {
                        $prizeInfo['total_prize'] += $prizeAmount;
                        $prizeInfo['prizes'][] = [
                            'categoria' => 'Extracción de 3 Cifras',
                            'premio_decimo' => $prizeAmount,
                            'key' => 'extraccionesDeTresCifras'
                        ];
                    }
                    break; // Solo sumar una vez por número
                }
            }
        }

        // Extracciones de 2 cifras
        if ($lotteryResult->extracciones_dos_cifras) {
            foreach ($lotteryResult->extracciones_dos_cifras as $extraccion) {
                if (isset($extraccion['decimo']) && substr($number, -2) === $extraccion['decimo']) {
                    $prizeAmount = $this->getPrizeAmountForDecimo('extraccionesDeDosCifras', $typeIdentifier, $categories);
                    if ($prizeAmount > 0) {
                        $prizeInfo['total_prize'] += $prizeAmount;
                        $prizeInfo['prizes'][] = [
                            'categoria' => 'Extracción de 2 Cifras',
                            'premio_decimo' => $prizeAmount,
                            'key' => 'extraccionesDeDosCifras'
                        ];
                    }
                    break; // Solo sumar una vez por número
                }
            }
        }
    }

    /**
     * Verificar reintegros (adaptado para premios por décimo)
     */
    private function checkReintegrosForDecimo($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        if ($lotteryResult->reintegros) {
            $lastDigit = substr($number, -1);
            
            // Verificar si el número es el PRIMER PREMIO (no debe sumar reintegro)
            $isFirstPrize = $this->isFirstPrizeNumber($number, $lotteryResult);
            
            if (!$isFirstPrize) {
                // Verificar si ya tiene premio de "Última Cifra del Primer Premio"
                $hasUltimaCifra = false;
                foreach ($prizeInfo['prizes'] as $prize) {
                    if ($prize['categoria'] === 'Última Cifra del Primer Premio') {
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
                        $prizeAmount = $this->getPrizeAmountForDecimo('reintegros', $typeIdentifier, $categories);
                        if ($prizeAmount > 0) {
                            $prizeInfo['total_prize'] += $prizeAmount;
                            $prizeInfo['prizes'][] = [
                                'categoria' => 'Reintegro',
                                'premio_decimo' => $prizeAmount,
                                'key' => 'reintegros'
                            ];
                        }
                    }
                }
            }
        }
    }

    /**
     * Verificar pedreas (adaptado para premios por décimo)
     */
    private function checkPedreasForDecimo($number, $lotteryResult, $typeIdentifier, $categories, &$prizeInfo)
    {
        // Solo verificar pedreas si existen en el resultado
        if (!$lotteryResult->pedreas || !is_array($lotteryResult->pedreas)) {
            return;
        }
        
        foreach ($lotteryResult->pedreas as $pedrea) {
            if (isset($pedrea['decimo']) && $this->compareNumbers($number, $pedrea['decimo'])) {
                $prizeAmount = $this->getPrizeAmountForDecimo('pedrea', $typeIdentifier, $categories);
                if ($prizeAmount > 0) {
                    $prizeInfo['total_prize'] += $prizeAmount;
                    $prizeInfo['prizes'][] = [
                        'categoria' => 'Pedrea',
                        'premio_decimo' => $prizeAmount,
                        'key' => 'pedrea'
                    ];
                }
                break; // Solo sumar una vez por número
            }
        }
    }

    /**
     * Obtener importe del premio por décimo desde la configuración
     */
    private function getPrizeAmountForDecimo($categoryKey, $typeIdentifier, $categories)
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
            'centenasPrimerPremio' => 'centenasPrimerPremio',
            'centenasSegundoPremio' => 'centenasSegundoPremio',
            'dosUltimasCifrasPrimerPremio' => 'dosUltimasCifrasPrimerPremio',
            'tresUltimasCifrasPrimerPremio' => 'tresUltimasCifrasPrimerPremio',
            'ultimaCifraPrimerPremio' => 'ultimaCifraPrimerPremio',
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
                
                // Para el escrutinio por categoría, el premio es por décimo (no por serie)
                // Dividir entre 10 porque el premio de la serie se divide entre 10 décimos
                return $amount / 10;
            }
        }
        
        return 0;
    }

    /**
     * Obtener clave de la categoría
     */
    private function getCategoryKey($categoryKey)
    {
        $keys = [
            'primer_premio' => 'primerPremio',
            'segundo_premio' => 'segundoPremio',
            'terceros_premios' => 'tercerosPremios',
            'cuartos_premios' => 'cuartosPremios',
            'quintos_premios' => 'quintosPremios'
        ];
        
        return $keys[$categoryKey] ?? $categoryKey;
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
     * Verificar si un número es el PRIMER PREMIO (no debe sumar reintegro)
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
     * Comparar números normalizando formatos (con/sin ceros a la izquierda)
     */
    private function compareNumbers($number1, $number2)
    {
        // Normalizar ambos números a formato de 5 dígitos
        $normalized1 = str_pad($number1, 5, '0', STR_PAD_LEFT);
        $normalized2 = str_pad($number2, 5, '0', STR_PAD_LEFT);
        
        return $normalized1 === $normalized2;
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
     * Organizar resultados del escrutinio por categoría agrupados por entidad
     */
    private function organizeResultsByEntity($scrutinyResults, $entitiesWithReserves, $lotteryId)
    {
        $resultsByEntity = [];
        
        // Crear un mapa de números por entidad
        $numbersByEntity = [];
        foreach ($entitiesWithReserves as $entity) {
            $entityNumbers = [];
            foreach ($entity->reserves as $reserve) {
                if ($reserve->reservation_numbers) {
                    $entityNumbers = array_merge($entityNumbers, $reserve->reservation_numbers);
                }
            }
            $numbersByEntity[$entity->id] = array_unique($entityNumbers);
        }
        
        // Agrupar resultados por entidad y calcular décimos por set individual
        foreach ($entitiesWithReserves as $entity) {
            $entityResults = [];
            $entityNumbers = $numbersByEntity[$entity->id] ?? [];
            $totalEntityPrize = 0; // Suma total de premios de todos los números de esta entidad
            
            foreach ($scrutinyResults as $result) {
                if (in_array($result['number'], $entityNumbers)) {
                    // Calcular décimos para este número específico por cada set individual
                    $decimosInfo = $this->calculateDecimosForNumberBySets($result['number'], $entity, $lotteryId);
                    
                    // Calcular el premio total para este número
                    $premioPorDecimo = $result['total_prize'];
                    $totalDecimos = $decimosInfo['total_decimos'] ?? 0;
                    $premioTotalNumero = $premioPorDecimo * $totalDecimos;
                    $totalEntityPrize += $premioTotalNumero;
                    
                    $result['decimos_info'] = $decimosInfo;
                    $result['premio_total_numero'] = $premioTotalNumero;
                    $entityResults[] = $result;
                }
            }
            
            if (!empty($entityResults)) {
                // Agregar el premio total de la entidad a cada resultado
                foreach ($entityResults as &$result) {
                    $result['premio_total_entidad'] = $totalEntityPrize;
                }
                $resultsByEntity[$entity->id] = $entityResults;
            }
        }
        
        return $resultsByEntity;
    }

    /**
     * Calcular décimos para un número específico por sets individuales
     */
    private function calculateDecimosForNumberBySets($number, $entity, $lotteryId)
    {
        $lottery = Lottery::find($lotteryId);
        $ticketPrice = $lottery->ticket_price ?? 0; // Precio del décimo del sorteo
        
        $totalParticipations = 0;
        $totalDecimos = 0;
        $setsInfo = [];
        
        \Log::info("=== CALCULANDO DÉCIMOS PARA NÚMERO: {$number} ===");
        \Log::info("Entidad: {$entity->name} (ID: {$entity->id})");
        \Log::info("Precio del décimo del sorteo: {$ticketPrice}");
        \Log::info("Total reservas de la entidad: " . count($entity->reserves));
        
        // Recorrer cada reserva de la entidad
        foreach ($entity->reserves as $reserve) {
            \Log::info("--- Procesando reserva ID: {$reserve->id} ---");
            \Log::info("Números en reserva: " . json_encode($reserve->reservation_numbers));
            
            if ($reserve->reservation_numbers) {
                // Buscar el índice del número en esta reserva
                $numberIndex = array_search($number, $reserve->reservation_numbers);
                \Log::info("Índice del número {$number} en reserva: " . ($numberIndex !== false ? $numberIndex : 'NO ENCONTRADO'));
                
                if ($numberIndex !== false) {
                    // Obtener todos los sets de esta reserva
                    $sets = Set::where('reserve_id', $reserve->id)->get();
                    \Log::info("Sets encontrados en reserva: " . count($sets));
                    
                    foreach ($sets as $set) {
                        \Log::info("--- Procesando Set ID: {$set->id} ---");
                        \Log::info("Precio del set: {$set->price_per_participation}");
                        \Log::info("Donativo del set: {$set->donation_amount}");
                        
                        // Obtener todas las participaciones vendidas (asignadas) para este número específico
                        // Buscar todas las participaciones del set que correspondan a este número
                        $participations = Participation::where('set_id', $set->id)
                            ->where('status', 'asignada')
                            ->get();
                        
                        $participationsVendidas = $participations->count();
                        \Log::info("Participaciones vendidas para número {$number} en set {$set->id}: {$participationsVendidas}");
                        
                        if ($participationsVendidas > 0) {
                            $totalParticipations += $participationsVendidas;
                            
                            // Obtener el precio real de la participación (played_amount del set)
                            $pricePerParticipation = $set->played_amount ?? 0;
                            \Log::info("Precio real del set: {$pricePerParticipation}");
                            $donationAmount = $set->donation_amount ?? 0;
                            $importeJugado = $pricePerParticipation; // No restar el donativo
                            
                            \Log::info("Set {$set->id}: {$participationsVendidas} participaciones vendidas, Precio Real: {$pricePerParticipation}, Donativo: {$donationAmount}, Importe Jugado: {$importeJugado}");
                            
                            if ($importeJugado > 0 && $ticketPrice > 0) {
                                $participacionesPorDecimo = $ticketPrice / $importeJugado;
                                $decimosDeEsteSet = $participationsVendidas / $participacionesPorDecimo; // Usar participaciones del set actual, no total
                                $decimosRedondeados = round($decimosDeEsteSet); // Redondear correctamente
                                $totalDecimos += $decimosRedondeados;
                                
                                $setsInfo[] = [
                                    'set_id' => $set->id,
                                    'participations_vendidas' => $participationsVendidas,
                                    'importe_jugado' => $importeJugado,
                                    'decimos' => $decimosRedondeados // Usar décimos redondeados
                                ];
                                
                                \Log::info("Set {$set->id}: {$participationsVendidas} participaciones, {$participacionesPorDecimo} por décimo, {$decimosDeEsteSet} décimos (redondeados: {$decimosRedondeados})");
                            } else {
                                \Log::info("Set {$set->id}: No se puede calcular - Importe jugado: {$importeJugado}, Precio décimo: {$ticketPrice}");
                            }
                        } else {
                            \Log::info("Set {$set->id}: No hay participaciones vendidas para este número");
                        }
                    }
                } else {
                    \Log::info("Número {$number} no encontrado en esta reserva");
                }
            } else {
                \Log::info("Reserva sin números");
            }
        }
        
        \Log::info("Total décimos calculados: {$totalDecimos} para número {$number}");
        
        return [
            'total_participations' => $totalParticipations,
            'total_decimos' => round($totalDecimos),
            'ticket_price' => $ticketPrice,
            'sets_info' => $setsInfo
        ];
    }

    /**
     * Guardar escrutinio definitivamente
     */
    public function save(Request $request, $lotteryId)
    {
        $administrationId = session('selected_administration.id');

        if (!$administrationId) {
            return redirect()->route('lottery.administrations')
                ->with('error', 'Debe seleccionar una administración primero');
        }

        $scrutiny = AdministrationLotteryScrutiny::where('administration_id', $administrationId)
            ->where('lottery_id', $lotteryId)
            ->first();

        if (!$scrutiny) {
            return redirect()->route('lottery.results')
                ->with('error', 'No se encontró el escrutinio');
        }

        if (!$scrutiny->is_scrutinized) {
            return redirect()->route('lottery.scrutiny', $lotteryId)
                ->with('error', 'Debe completar el escrutinio antes de guardarlo');
        }

        if ($scrutiny->is_saved) {
            return redirect()->route('lottery.show-administration-scrutiny', [$lotteryId, $administrationId])
                ->with('info', 'El escrutinio ya ha sido guardado');
        }

        try {
            $scrutiny->update([
                'is_saved' => true,
                'saved_at' => now(),
                'saved_by' => Auth::id()
            ]);

            return redirect()->route('lottery.show-administration-scrutiny', [$lotteryId, $administrationId])
                ->with('success', 'Escrutinio guardado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al guardar el escrutinio: ' . $e->getMessage());
        }
    }

    /**
     * Guardar resultados detallados del escrutinio
     */
    private function saveDetailedScrutinyResults($scrutiny, $scrutinyResultsByEntity, $lottery)
    {
        foreach ($scrutinyResultsByEntity as $entityId => $entityResults) {
            foreach ($entityResults as $categoryResult) {
                $decimosInfo = $categoryResult['decimos_info'] ?? [];
                $totalDecimos = $decimosInfo['total_decimos'] ?? 0;
                $premioPorDecimo = $categoryResult['total_prize'];
                $premioTotal = $premioPorDecimo * $totalDecimos;
                
                // Iterar por cada set que tenga participaciones
                if (!empty($decimosInfo['sets_info'])) {
                    foreach ($decimosInfo['sets_info'] as $setInfo) {
                        // Calcular premio por participación para este set específico
                        $premioPorParticipacion = 0;
                        $importeJugado = $setInfo['importe_jugado'] ?? 0;
                        $ticketPrice = $decimosInfo['ticket_price'] ?? 0;
                        
                        if ($ticketPrice > 0 && $importeJugado > 0) {
                            $porcentajeParticipacion = $importeJugado / $ticketPrice;
                            $premioPorParticipacion = $premioPorDecimo * $porcentajeParticipacion;
                        }

                        // Debug: Log de información del set
                        \Log::info("Set Info: " . json_encode($setInfo));
                        \Log::info("Decimos from setInfo: " . ($setInfo['decimos'] ?? 'NULL'));
                        \Log::info("Participations vendidas from setInfo: " . ($setInfo['participations_vendidas'] ?? 'NULL'));

                        // Guardar un registro por cada set
                        DB::table('scrutiny_detailed_results')->insert([
                            'scrutiny_id' => $scrutiny->id,
                            'entity_id' => $entityId,
                            'winning_number' => $categoryResult['number'],
                            'set_id' => $setInfo['set_id'],
                            'premio_por_decimo' => $premioPorDecimo,
                            'premio_por_participacion' => $premioPorParticipacion,
                            'total_decimos' => $setInfo['decimos'] ?? 0,
                            'total_participations' => $setInfo['participations_vendidas'] ?? 0,
                            'premio_total' => $premioPorDecimo * ($setInfo['decimos'] ?? 0),
                            'winning_categories' => json_encode($categoryResult['categories']),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                } else {
                    // Si no hay sets_info, guardar con los datos totales
                    \Log::info("No sets_info found, using total data");
                    \Log::info("Total Decimos: " . $totalDecimos);
                    \Log::info("Total Participations: " . ($decimosInfo['total_participations'] ?? 0));
                    
                    // Buscar el primer set de la entidad para obtener el set_id
                    $entity = \App\Models\Entity::find($entityId);
                    $firstSet = null;
                    if ($entity) {
                        foreach ($entity->reserves as $reserve) {
                            $set = \App\Models\Set::where('reserve_id', $reserve->id)->first();
                            if ($set) {
                                $firstSet = $set;
                                break;
                            }
                        }
                    }
                    
                    DB::table('scrutiny_detailed_results')->insert([
                        'scrutiny_id' => $scrutiny->id,
                        'entity_id' => $entityId,
                        'winning_number' => $categoryResult['number'],
                        'set_id' => $firstSet ? $firstSet->id : null,
                        'premio_por_decimo' => $premioPorDecimo,
                        'premio_por_participacion' => $premioPorDecimo, // Usar premio por décimo como fallback
                        'total_decimos' => $totalDecimos,
                        'total_participations' => $decimosInfo['total_participations'] ?? 0,
                        'premio_total' => $premioTotal,
                        'winning_categories' => json_encode($categoryResult['categories']),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    /**
     * Eliminar escrutinio (solo si no está finalizado)
     */
    public function delete($lotteryId, $administrationId)
    {
        $scrutiny = AdministrationLotteryScrutiny::where('administration_id', $administrationId)
            ->where('lottery_id', $lotteryId)
            ->first();

        if (!$scrutiny) {
            return redirect()->route('lottery.results')
                ->with('error', 'No se encontró el escrutinio');
        }

        if ($scrutiny->is_scrutinized) {
            return redirect()->route('lottery.show-administration-scrutiny', [$lotteryId, $administrationId])
                ->with('error', 'No se puede eliminar un escrutinio ya finalizado');
        }

        $scrutiny->delete();

        return redirect()->route('lottery.results')
            ->with('success', 'Escrutinio eliminado exitosamente');
    }
}
