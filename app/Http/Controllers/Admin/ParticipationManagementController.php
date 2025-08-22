<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;
use App\Models\DesignFormat;
use App\Models\Participation;
use Illuminate\Support\Facades\DB;

class ParticipationManagementController extends Controller
{
    /**
     * Mostrar lista de sets con sus participaciones
     */
    public function index()
    {
        $sets = Set::with(['entity', 'reserve.lottery', 'designFormats', 'participations'])
            ->whereHas('designFormats')
            ->get();

        return view('admin.participations.index', compact('sets'));
    }

    /**
     * Mostrar detalles de un set específico
     */
    public function show($setId)
    {
        $set = Set::with(['entity', 'reserve.lottery', 'designFormats.participations'])
            ->findOrFail($setId);

        $designFormat = $set->designFormats->first();
        
        if ($designFormat) {
            // Calcular estadísticas
            $totalParticipations = $set->total_participations ?? 0;
            $createdParticipations = $designFormat->participations->count();
            $availableParticipations = $designFormat->participations->where('status', 'disponible')->count();
            $soldParticipations = $designFormat->participations->where('status', 'vendida')->count();
            $returnedParticipations = $designFormat->participations->where('status', 'devuelta')->count();

            $stats = [
                'total' => $totalParticipations,
                'created' => $createdParticipations,
                'available' => $availableParticipations,
                'sold' => $soldParticipations,
                'returned' => $returnedParticipations,
                'missing' => $totalParticipations - $createdParticipations
            ];
        } else {
            $stats = [
                'total' => 0,
                'created' => 0,
                'available' => 0,
                'sold' => 0,
                'returned' => 0,
                'missing' => 0
            ];
        }

        return view('admin.participations.show', compact('set', 'designFormat', 'stats'));
    }

    /**
     * Generar participaciones para un set específico
     */
    public function generate(Request $request, $setId)
    {
        $set = Set::findOrFail($setId);
        $designFormat = $set->designFormats->first();

        if (!$designFormat) {
            return back()->with('error', 'No se encontró un diseño para este set.');
        }

        try {
            DB::beginTransaction();

            // Eliminar participaciones existentes si se solicita
            if ($request->has('force') && $request->force) {
                $designFormat->deleteParticipations();
            }

            // Generar nuevas participaciones
            $designFormat->generateParticipations();

            DB::commit();

            $message = $request->has('force') && $request->force 
                ? 'Participaciones regeneradas exitosamente.' 
                : 'Participaciones generadas exitosamente.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar participaciones: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar participaciones de un set
     */
    public function delete(Request $request, $setId)
    {
        $set = Set::findOrFail($setId);
        $designFormat = $set->designFormats->first();

        if (!$designFormat) {
            return back()->with('error', 'No se encontró un diseño para este set.');
        }

        try {
            $count = $designFormat->participations->count();
            $designFormat->deleteParticipations();

            return back()->with('success', "Se eliminaron {$count} participaciones exitosamente.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar participaciones: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar estadísticas generales
     */
    public function stats()
    {
        $stats = [
            'total_sets' => Set::whereHas('designFormats')->count(),
            'total_participations' => Participation::count(),
            'available_participations' => Participation::where('status', 'disponible')->count(),
            'sold_participations' => Participation::where('status', 'vendida')->count(),
            'returned_participations' => Participation::where('status', 'devuelta')->count(),
            'cancelled_participations' => Participation::where('status', 'anulada')->count(),
        ];

        // Estadísticas por entidad
        $entityStats = DB::table('participations')
            ->join('entities', 'participations.entity_id', '=', 'entities.id')
            ->select(
                'entities.name as entity_name',
                DB::raw('COUNT(*) as total_participations'),
                DB::raw('SUM(CASE WHEN status = "disponible" THEN 1 ELSE 0 END) as available'),
                DB::raw('SUM(CASE WHEN status = "vendida" THEN 1 ELSE 0 END) as sold'),
                DB::raw('SUM(CASE WHEN status = "devuelta" THEN 1 ELSE 0 END) as returned')
            )
            ->groupBy('entities.id', 'entities.name')
            ->get();

        return view('admin.participations.stats', compact('stats', 'entityStats'));
    }

    /**
     * Buscar participación por código
     */
    public function search(Request $request)
    {
        $request->validate([
            'participation_code' => 'required|string'
        ]);

        $participation = Participation::with(['entity', 'set', 'designFormat', 'seller.user'])
            ->where('participation_code', $request->participation_code)
            ->first();

        if (!$participation) {
            return back()->with('error', 'Participación no encontrada.');
        }

        return view('admin.participations.search', compact('participation'));
    }

    /**
     * Exportar participaciones de un set
     */
    public function export($setId)
    {
        $set = Set::with(['entity', 'reserve.lottery', 'designFormats.participations.seller.user'])
            ->findOrFail($setId);

        $designFormat = $set->designFormats->first();
        
        if (!$designFormat) {
            return back()->with('error', 'No se encontró un diseño para este set.');
        }

        $participations = $designFormat->participations;

        // Aquí podrías generar un CSV o Excel
        // Por ahora retornamos una vista con los datos
        return view('admin.participations.export', compact('set', 'designFormat', 'participations'));
    }
}
