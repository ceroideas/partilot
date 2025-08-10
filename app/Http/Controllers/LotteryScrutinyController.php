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
     * Preparar datos para el escrutinio
     */
    private function prepareScrutinyData($lottery, $entitiesWithReserves)
    {
        $scrutinyData = [];
        $lotteryResult = $lottery->result;

        foreach ($entitiesWithReserves as $entity) {
            $allReservedNumbers = [];
            $totalReserved = 0;

            // Consolidar números de todas las reservas de la entidad
            foreach ($entity->reserves as $reserve) {
                $reservedNumbers = $reserve->reservation_numbers ?? [];
                $allReservedNumbers = array_merge($allReservedNumbers, $reservedNumbers);
                $totalReserved += count($reservedNumbers);
            }

            // Eliminar duplicados
            $allReservedNumbers = array_unique($allReservedNumbers);

            // Calcular premios
            $entityResult = new ScrutinyEntityResult([
                'entity_id' => $entity->id,
                'reserved_numbers' => $allReservedNumbers,
                'total_reserved' => count($allReservedNumbers),
                'total_issued' => count($allReservedNumbers), // Por ahora igual
                'total_sold' => count($allReservedNumbers), // Por ahora igual
                'total_returned' => 0 // Por ahora 0
            ]);

            $entityResult->calculatePrizes($lotteryResult);

            $scrutinyData[] = [
                'entity' => $entity,
                'result' => $entityResult
            ];
        }

        return $scrutinyData;
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
            // Consolidar números reservados
            $allReservedNumbers = [];
            foreach ($entity->reserves as $reserve) {
                $reservedNumbers = $reserve->reservation_numbers ?? [];
                $allReservedNumbers = array_merge($allReservedNumbers, $reservedNumbers);
            }
            $allReservedNumbers = array_unique($allReservedNumbers);

            // Crear o actualizar resultado de la entidad
            $entityResult = ScrutinyEntityResult::updateOrCreate([
                'administration_lottery_scrutiny_id' => $scrutiny->id,
                'entity_id' => $entity->id
            ], [
                'reserved_numbers' => $allReservedNumbers,
                'total_reserved' => count($allReservedNumbers),
                'total_issued' => count($allReservedNumbers),
                'total_sold' => count($allReservedNumbers),
                'total_returned' => 0
            ]);

            // Calcular premios
            $entityResult->calculatePrizes($lotteryResult);
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
