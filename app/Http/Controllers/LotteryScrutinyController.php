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

class LotteryScrutinyController extends Controller
{
    /**
     * Mostrar el formulario de escrutinio para una administración específica
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

        // Preparar datos para la vista
        $scrutinyData = $this->prepareScrutinyData($lottery, $entitiesWithReserves);

        return view('lottery.scrutiny', compact('lottery', 'administration', 'entitiesWithReserves', 'scrutinyData'));
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

            // Crear o actualizar el escrutinio de la administración
            $scrutiny = AdministrationLotteryScrutiny::updateOrCreate([
                'administration_id' => $administrationId,
                'lottery_id' => $lotteryId
            ], [
                'lottery_result_id' => $lottery->result->id,
                'scrutiny_date' => now(),
                'is_scrutinized' => true,
                'scrutinized_by' => Auth::id(),
                'comments' => $request->input('comments')
            ]);

            // Procesar cada entidad
            $this->processEntityResults($scrutiny, $lottery);

            // Calcular resumen
            $scrutiny->calculateSummary();

            DB::commit();

            return redirect()->route('lottery.show-administration-scrutiny', [$lotteryId, $administrationId])
                ->with('success', 'Escrutinio procesado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
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
            ->with(['entityResults.entity', 'scrutinizedBy'])
            ->firstOrFail();

        if (!$scrutiny->is_scrutinized) {
            return redirect()->route('lottery.scrutiny', $lotteryId)
                ->with('info', 'El escrutinio aún no ha sido completado');
        }

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

        // Calcular totales únicos globales
        $uniqueWinningNumbers = count(array_unique($allWinningNumbers));
        // Los números no premiados son todos los números reservados menos los ganadores
        $uniqueNonWinningNumbers = count(array_diff($allReservedNumbers, array_unique($allWinningNumbers)));

        return [
            'entities' => $scrutinyData,
            'summary' => [
                'unique_winning_numbers' => $uniqueWinningNumbers,
                'unique_non_winning_numbers' => $uniqueNonWinningNumbers,
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
