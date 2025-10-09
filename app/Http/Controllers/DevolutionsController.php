<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participation;
use App\Models\Entity;
use App\Models\Seller;
use App\Models\Lottery;
use App\Models\Reserve;
use App\Models\Set;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DevolutionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('devolutions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('devolutions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'entity_id' => 'required|exists:entities,id',
                'lottery_id' => 'required|exists:lotteries,id',
                'seller_id' => 'required|exists:sellers,id',
                'participations' => 'required|array',
                'participations.*' => 'required|integer|exists:participations,id',
                'return_reason' => 'nullable|string|max:255',
                'liquidacion' => 'required|array',
                'liquidacion.devolver' => 'required|array',
                'liquidacion.vender' => 'required|array'
            ]);

            $now = Carbon::now();
            $userId = auth()->id();

            // Procesar participaciones a devolver
            if (!empty($data['liquidacion']['devolver'])) {
                Participation::whereIn('id', $data['liquidacion']['devolver'])
                    ->update([
                        'return_date' => $now->format('Y-m-d'),
                        'return_time' => $now->format('H:i:s'),
                        'return_reason' => $data['return_reason'] ?? 'Devolución por liquidación',
                        'returned_by' => $userId,
                        'updated_at' => $now
                    ]);
            }

            // Procesar participaciones a vender
            if (!empty($data['liquidacion']['vender'])) {
                Participation::whereIn('id', $data['liquidacion']['vender'])
                    ->update([
                        'sale_date' => $now->format('Y-m-d'),
                        'sale_time' => $now->format('H:i:s'),
                        'updated_at' => $now
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución procesada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Implementar vista de detalle de devolución
        return view('devolutions.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Implementar vista de edición de devolución
        return view('devolutions.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementar actualización de devolución
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            // Aquí implementarías la lógica para eliminar una devolución
            // Por ahora, solo devolvemos éxito

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la devolución: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos para la tabla de devoluciones (DataTable)
     */
    public function data()
    {
        // Obtener participaciones que tienen devoluciones o ventas
        $participations = Participation::select([
                'participations.id',
                'participations.participation_code',
                'participations.sale_date',
                'participations.sale_time',
                'participations.return_date',
                'participations.return_time',
                'participations.status',
                'entities.name as entity_name',
                'lotteries.name as lottery_name',
                'sellers.name as seller_name'
            ])
            ->join('entities', 'participations.entity_id', '=', 'entities.id')
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->join('lotteries', 'reserves.lottery_id', '=', 'lotteries.id')
            ->leftJoin('sellers', 'participations.seller_id', '=', 'sellers.id')
            ->where(function($query) {
                $query->whereNotNull('participations.return_date')
                      ->orWhereNotNull('participations.sale_date')
                      ->orWhereIn('participations.status', ['vendida', 'devuelta']);
            })
            ->get();

        // Agrupar por entidad, sorteo y vendedor
        $grouped = $participations->groupBy(function($participation) {
            return $participation->entity_name . '|' . $participation->lottery_name . '|' . ($participation->seller_name ?? 'Sin vendedor');
        });

        $devolutions = $grouped->map(function($group, $key) {
            $parts = explode('|', $key);
            $entityName = $parts[0];
            $lotteryName = $parts[1];
            $sellerName = $parts[2];

            $devoluciones = $group->whereNotNull('return_date')->count();
            $ventas = $group->whereNotNull('sale_date')->count();
            
            $lastDate = $group->max(function($item) {
                return $item->return_date ?? $item->sale_date;
            });

            return [
                'id' => $group->first()->id,
                'entity_name' => $entityName,
                'lottery_name' => $lotteryName,
                'seller_name' => $sellerName,
                'participations_count' => $devoluciones + $ventas,
                'return_date' => $lastDate ? Carbon::parse($lastDate)->format('d/m/Y') : '-',
                'status' => $devoluciones > 0 ? 'Devuelto' : 'Vendido',
                'actions' => $this->generateActions($group->first()->id)
            ];
        })->values();

        return response()->json([
            'data' => $devolutions
        ]);
    }

    /**
     * Obtener entidades disponibles
     */
    public function getEntities()
    {
        $entities = Entity::select([
                'entities.id', 
                'entities.name', 
                'entities.province', 
                'entities.city',
                'entities.status',
                'administrations.name as administration_name'
            ])
            ->leftJoin('administrations', 'entities.administration_id', '=', 'administrations.id')
            ->where('entities.status', true) // status es boolean, true = activo
            ->get()
            ->map(function($entity) {
                return [
                    'id' => $entity->id,
                    'name' => $entity->name,
                    'province' => $entity->province ?? 'N/A',
                    'city' => $entity->city ?? 'N/A',
                    'administration_name' => $entity->administration_name ?? 'Sin administración',
                    'status' => $entity->status ? 'activo' : 'inactivo'
                ];
            });

        return response()->json([
            'success' => true,
            'entities' => $entities
        ]);
    }

    /**
     * Obtener sorteos de una entidad
     */
    public function getLotteriesByEntity(Request $request)
    {
        $entityId = $request->get('entity_id');
        
        $lotteries = Lottery::select(['lotteries.id', 'lotteries.name', 'lotteries.draw_date'])
            ->join('reserves', 'lotteries.id', '=', 'reserves.lottery_id')
            ->where('reserves.entity_id', $entityId)
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'lotteries' => $lotteries
        ]);
    }

    /**
     * Obtener vendedores de una entidad
     */
    public function getSellersByEntity(Request $request)
    {
        $entityId = $request->get('entity_id');
        
        $sellers = Seller::select(['id', 'name', 'email', 'phone'])
            ->where('entity_id', $entityId)
            ->where('status', true) // status es boolean, true = activo
            ->get();

        return response()->json([
            'success' => true,
            'sellers' => $sellers
        ]);
    }

    /**
     * Obtener participaciones de un vendedor en un sorteo
     */
    public function getParticipationsBySellerAndLottery(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $lotteryId = $request->get('lottery_id');
        
        $participations = Participation::select([
                'participations.id',
                'participations.number',
                'participations.participation_code',
                'participations.sale_date',
                'participations.sale_time',
                'participations.return_date',
                'participations.return_time'
            ])
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->where('participations.seller_id', $sellerId)
            ->where('reserves.lottery_id', $lotteryId)
            ->where(function($query) {
                $query->whereNull('participations.sale_date')
                      ->whereNull('participations.return_date');
            })
            ->get();

        return response()->json([
            'success' => true,
            'participations' => $participations
        ]);
    }

    /**
     * Validar participaciones disponibles
     */
    public function validateParticipations(Request $request)
    {
        $data = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'lottery_id' => 'required|exists:lotteries,id',
            'desde' => 'nullable|integer',
            'hasta' => 'nullable|integer',
            'participation_id' => 'nullable|integer|exists:participations,id'
        ]);

        $query = Participation::select([
                'participations.id',
                'participations.participation_number as number',
                'participations.participation_code'
            ])
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->where('participations.seller_id', $data['seller_id'])
            ->where('reserves.lottery_id', $data['lottery_id'])
            ->where('participations.status', 'disponible'); // Solo participaciones disponibles

        // Filtrar por rango
        if (isset($data['desde']) && isset($data['hasta'])) {
            $query->whereBetween('participations.participation_number', [$data['desde'], $data['hasta']]);
        }

        // Filtrar por participación específica
        if (isset($data['participation_id'])) {
            $query->where('participations.id', $data['participation_id']);
        }

        $participations = $query->get();

        return response()->json([
            'success' => true,
            'participations' => $participations
        ]);
    }

    /**
     * Generar botones de acción para DataTable
     */
    private function generateActions($id)
    {
        return '
            <div class="btn-group" role="group">
                <a href="' . route('devolutions.show', $id) . '" class="btn btn-sm btn-info" title="Ver detalle">
                    <i class="ri-eye-line"></i>
                </a>
                <a href="' . route('devolutions.edit', $id) . '" class="btn btn-sm btn-warning" title="Editar">
                    <i class="ri-edit-line"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger btn-eliminar-devolucion" 
                        data-id="' . $id . '" data-name="Devolución #' . $id . '" title="Eliminar">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        ';
    }
}
