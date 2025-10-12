<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participation;
use App\Models\Entity;
use App\Models\Seller;
use App\Models\Lottery;
use App\Models\Reserve;
use App\Models\Set;
use App\Models\Devolution;
use App\Models\DevolutionDetail;
use App\Models\DevolutionPayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DevolutionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devolutions = Devolution::with(['entity', 'lottery', 'seller', 'user', 'payments'])
            ->orderBy('devolution_date', 'desc')
            ->orderBy('devolution_time', 'desc')
            ->get();
        
        return view('devolutions.index', compact('devolutions'));
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
     * Actualizado para soportar devoluciones con o sin vendedor
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'entity_id' => 'required|exists:entities,id',
                'lottery_id' => 'required|exists:lotteries,id',
                'seller_id' => 'nullable|exists:sellers,id', // Ahora es opcional
                'set_id' => 'nullable|exists:sets,id', // Para liquidar set completo sin devoluciones
                'participations' => 'nullable|array', // Cambiado a nullable para permitir devoluciones sin participaciones
                'participations.*' => 'nullable|integer|exists:participations,id',
                'return_reason' => 'nullable|string|max:255',
                'liquidacion' => 'required|array',
                'liquidacion.devolver' => 'nullable|array', // Cambiado a nullable para permitir devoluciones sin devolver
                'liquidacion.vender' => 'nullable|array', // Cambiado a nullable
                'liquidacion.pagos' => 'nullable|array', // Array de pagos múltiples
                'liquidacion.pagos.*.payment_method' => 'required|string',
                'liquidacion.pagos.*.amount' => 'required|numeric'
            ]);

            // Obtener las participaciones seleccionadas para devolver (puede estar vacío)
            $participationsToReturn = $data['liquidacion']['devolver'] ?? [];
            
            // Calcular participaciones a vender
            $participationsToSell = [];
            
            // Caso 1: No hay participaciones a devolver pero hay set_id (liquidar set completo)
            if (empty($participationsToReturn) && isset($data['set_id'])) {
                $setId = $data['set_id'];
                // Todas las participaciones del set se venden
                $participationsToSell = Participation::where('set_id', $setId)
                    ->whereIn('status', ['disponible', 'asignada', 'vendida'])
                    ->pluck('id')
                    ->toArray();
            }
            // Caso 2: Hay participaciones a devolver
            elseif (!empty($participationsToReturn)) {
                // Obtener los sets únicos de las participaciones seleccionadas
                $participations = Participation::whereIn('id', $participationsToReturn)->get();
                $setIds = $participations->pluck('set_id')->unique()->toArray();
                
                // Para cada set, obtener todas las participaciones disponibles y calcular cuáles vender
                foreach ($setIds as $setId) {
                    // Participaciones del set que se van a devolver
                    $returnedInSet = $participations->where('set_id', $setId)->pluck('id')->toArray();
                    
                    // Todas las participaciones disponibles del set
                    $allInSet = Participation::where('set_id', $setId)
                        ->whereIn('status', ['disponible', 'asignada'])
                        ->pluck('id')
                        ->toArray();
                    
                    // Las que no se devuelven, se venden
                    $toSellInSet = array_diff($allInSet, $returnedInSet);
                    $participationsToSell = array_merge($participationsToSell, $toSellInSet);
                }
            }

            $now = Carbon::now();
            $userId = auth()->id();

            // Crear registro de devolución
            $totalParticipations = count($participationsToReturn) + count($participationsToSell);
            $devolution = Devolution::create([
                'entity_id' => $data['entity_id'],
                'lottery_id' => $data['lottery_id'],
                'seller_id' => $data['seller_id'] ?? null,
                'user_id' => $userId,
                'total_participations' => $totalParticipations,
                'return_reason' => $data['return_reason'] ?? 'Devolución de entidad a administración',
                'devolution_date' => $now->format('Y-m-d'),
                'devolution_time' => $now->format('H:i:s'),
                'status' => 'procesada',
                'notes' => 'Devolución procesada automáticamente'
            ]);

            // Procesar participaciones a devolver
            if (!empty($participationsToReturn)) {
                Participation::whereIn('id', $participationsToReturn)
                    ->update([
                        'status' => 'devuelta',
                        'return_date' => $now->format('Y-m-d'),
                        'return_time' => $now->format('H:i:s'),
                        'return_reason' => $data['return_reason'] ?? 'Devolución de entidad a administración',
                        'returned_by' => $userId,
                        'updated_at' => $now
                    ]);

                // Crear detalles de devolución
                foreach ($participationsToReturn as $participationId) {
                    DevolutionDetail::create([
                        'devolution_id' => $devolution->id,
                        'participation_id' => $participationId,
                        'action' => 'devolver'
                    ]);
                }
            }

            // Procesar participaciones a vender
            if (!empty($participationsToSell)) {
                Participation::whereIn('id', $participationsToSell)
                    ->update([
                        'status' => 'vendida',
                        'sale_date' => $now->format('Y-m-d'),
                        'sale_time' => $now->format('H:i:s'),
                        'sale_amount' => DB::raw('(SELECT played_amount FROM sets WHERE sets.id = participations.set_id)'),
                        'buyer_name' => 'Liquidación automática',
                        'buyer_phone' => '',
                        'buyer_email' => '',
                        'buyer_nif' => '',
                        'notes' => 'Liquidación automática',
                        'updated_at' => $now
                    ]);

                // Crear detalles de venta (sin información de pago)
                foreach ($participationsToSell as $participationId) {
                    DevolutionDetail::create([
                        'devolution_id' => $devolution->id,
                        'participation_id' => $participationId,
                        'action' => 'vender'
                    ]);
                }
            }

            // Crear registros de pago múltiples si se proporcionaron
            if (isset($data['liquidacion']['pagos']) && !empty($data['liquidacion']['pagos'])) {
                foreach ($data['liquidacion']['pagos'] as $pago) {
                    DevolutionPayment::create([
                        'devolution_id' => $devolution->id,
                        'amount' => $pago['amount'],
                        'payment_method' => $pago['payment_method'],
                        'from_number' => null,
                        'notes' => 'Pago de liquidación - ' . ucfirst($pago['payment_method']),
                        'payment_date' => $now
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Devolución procesada correctamente',
                'devolution_id' => $devolution->id
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
        $devolution = Devolution::with([
            'entity', 
            'lottery', 
            'seller', 
            'user', 
            'details.participation.set',
            'payments'
        ])->findOrFail($id);
        
        return view('devolutions.show', compact('devolution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $devolution = Devolution::with(['entity', 'lottery', 'seller', 'user', 'details.participation.set', 'payments'])
            ->findOrFail($id);
        
        return view('devolutions.edit', compact('devolution'));
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

            $devolution = Devolution::with('details')->findOrFail($id);

            // Obtener IDs de participaciones afectadas
            $participationIds = $devolution->details->pluck('participation_id')->toArray();

            // Revertir estado de participaciones
            Participation::whereIn('id', $participationIds)
                ->update([
                    'status' => 'disponible',
                    'return_date' => null,
                    'return_time' => null,
                    'return_reason' => null,
                    'returned_by' => null,
                    'sale_date' => null,
                    'sale_time' => null,
                    'sale_amount' => null,
                    'buyer_name' => null,
                    'buyer_phone' => null,
                    'buyer_email' => null,
                    'buyer_nif' => null,
                    'notes' => null,
                    'updated_at' => now()
                ]);

            // Eliminar detalles de devolución
            $devolution->details()->delete();

            // Eliminar la devolución
            $devolution->delete();

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
        $devolutions = Devolution::with(['entity', 'lottery', 'seller', 'user', 'details', 'payments'])
            ->select([
                'devolutions.id',
                'devolutions.entity_id',
                'devolutions.lottery_id',
                'devolutions.seller_id',
                'devolutions.user_id',
                'devolutions.total_participations',
                'devolutions.return_reason',
                'devolutions.devolution_date',
                'devolutions.devolution_time',
                'devolutions.status',
                'devolutions.notes',
                'devolutions.created_at',
                'devolutions.updated_at'
            ])
            ->orderBy('devolutions.created_at', 'desc')
            ->get()
            ->map(function($devolution) {
                // Contar participaciones devueltas vs vendidas
                $returnedCount = $devolution->details()->where('action', 'devolver')->count();
                $soldCount = $devolution->details()->where('action', 'vender')->count();
                
                // Calcular total de liquidación (participaciones vendidas * precio promedio)
                $totalLiquidation = 0;
                $totalPayments = 0;
                
                if ($soldCount > 0) {
                    // Obtener el precio de las participaciones vendidas
                    $soldParticipations = $devolution->details()->where('action', 'vender')->with('participation.set')->get();
                    foreach ($soldParticipations as $detail) {
                        if ($detail->participation && $detail->participation->set) {
                            $price = $detail->participation->set->played_amount ?? 0;
                            $totalLiquidation += $price;
                        }
                    }
                }
                
                // Calcular pagos registrados (suma de amount en pagos de la devolución)
                $totalPayments = $devolution->payments()->sum('amount') ?? 0;

                return [
                    'id' => $devolution->id,
                    'entity_name' => $devolution->entity->name ?? 'N/A',
                    'lottery_name' => $devolution->lottery->name ?? 'N/A',
                    'total_participations' => $devolution->total_participations,
                    'returned_participations' => $returnedCount,
                    'return_date' => $devolution->devolution_date ? Carbon::parse($devolution->devolution_date)->format('d/m/Y') : '-',
                    'total_liquidation' => number_format($totalLiquidation, 2),
                    'total_payments' => number_format($totalPayments, 2),
                    'user_name' => $devolution->user->name ?? 'N/A',
                    'actions' => $this->generateActions($devolution->id)
                ];
            });

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
            // ->where('entities.status', true) // status es boolean, true = activo
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
     * Obtener sets por vendedor y sorteo (legacy - para devoluciones de vendedores)
     */
    public function getSetsBySellerAndLottery(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $lotteryId = $request->get('lottery_id');

        $sets = Set::with(['reserve'])
            ->whereHas('reserve', function($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId);
            })
            ->whereHas('participations', function($query) use ($sellerId) {
                $query->where('seller_id', $sellerId)
                      ->where('status', 'asignada');
            })
            ->select([
                'sets.id',
                'sets.set_name',
                'sets.set_number',
                'sets.reserve_id'
            ])
            ->orderBy('sets.set_number')
            ->get();

        return response()->json($sets);
    }

    /**
     * Obtener sets por entidad y sorteo (para devoluciones de entidad)
     * Solo muestra sets que tienen participaciones sin devolver
     */
    public function getSetsByEntityAndLottery(Request $request)
    {
        $entityId = $request->get('entity_id');
        $lotteryId = $request->get('lottery_id');

        // Obtener sets que tienen participaciones disponibles o asignadas (no devueltas completamente)
        $sets = Set::with(['reserve'])
            ->where('entity_id', $entityId)
            ->whereHas('reserve', function($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId);
            })
            ->whereHas('participations', function($query) {
                // Solo sets que tienen participaciones disponibles o asignadas
                $query->whereIn('status', ['disponible', 'asignada']);
            })
            ->select([
                'sets.id',
                'sets.set_name',
                'sets.set_number',
                'sets.reserve_id'
            ])
            ->orderBy('sets.set_number')
            ->get();

        return response()->json([
            'success' => true,
            'sets' => $sets
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
     * Actualizado para soportar devoluciones sin vendedor específico
     */
    public function validateParticipations(Request $request)
    {
        $data = $request->validate([
            'seller_id' => 'nullable|exists:sellers,id', // Ahora es opcional
            'entity_id' => 'nullable|exists:entities,id', // Para devoluciones de entidad
            'lottery_id' => 'required|exists:lotteries,id',
            'set_id' => 'required|exists:sets,id',
            'desde' => 'nullable|integer',
            'hasta' => 'nullable|integer',
            'participation_id' => 'nullable|integer' // Número de participación, no ID de base de datos
        ]);

        $query = Participation::select([
                'participations.id',
                'participations.participation_number as number',
                'participations.participation_code'
            ])
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->where('reserves.lottery_id', $data['lottery_id'])
            ->where('participations.set_id', $data['set_id']);

        // Si hay seller_id, filtrar por vendedor (devolución de vendedor)
        if (isset($data['seller_id'])) {
            $query->where('participations.seller_id', $data['seller_id'])
                  ->whereIn('participations.status', ['asignada', 'vendida','disponible']);
        } else {
            // Si no hay seller_id, mostrar participaciones asignadas o vendidas (devolución de entidad)
            $query->whereIn('participations.status', ['asignada', 'vendida','disponible']);
        }

        // Filtrar por rango
        if (isset($data['desde']) && isset($data['hasta'])) {
            $query->whereBetween('participations.participation_number', [$data['desde'], $data['hasta']]);
        }

        // Filtrar por participación específica (por número de participación, no por ID)
        if (isset($data['participation_id'])) {
            $query->where('participations.participation_number', $data['participation_id']);
        }

        $participations = $query->get();

        return response()->json([
            'success' => true,
            'participations' => $participations
        ]);
    }

    /**
     * Obtener resumen de liquidación para los sets seleccionados
     */
    public function getLiquidationSummary(Request $request)
    {
        $entityId = $request->get('entity_id');
        $lotteryId = $request->get('lottery_id');
        $setId = $request->get('set_id');
        $selectedParticipations = $request->get('participations', []);

        \Log::info('=== LIQUIDATION SUMMARY REQUEST ===');
        \Log::info('Entity ID:', [$entityId]);
        \Log::info('Lottery ID:', [$lotteryId]);
        \Log::info('Set ID:', [$setId]);
        \Log::info('Selected Participations:', $selectedParticipations);

        // Si no hay participaciones pero hay set_id, calcular liquidación del set completo
        if (empty($selectedParticipations) && $setId) {
            \Log::info('No participations selected but set provided, calculating full set liquidation');
            
            $set = Set::find($setId);
            if (!$set) {
                \Log::warning("Set not found: $setId");
                return response()->json([
                    'success' => false,
                    'message' => 'Set no encontrado'
                ], 404);
            }

            // Contar todas las participaciones del set que están disponibles o asignadas (vendibles)
            $allParticipations = Participation::where('set_id', $setId)
                ->whereIn('status', ['disponible', 'asignada', 'vendida'])
                ->count();

            $pricePerParticipation = $set->played_amount ?? 0;
            $totalLiquidation = $allParticipations * $pricePerParticipation;

            \Log::info("Full set liquidation:", [
                'set_id' => $setId,
                'total_participations' => $allParticipations,
                'price_per_participation' => $pricePerParticipation,
                'total_liquidation' => $totalLiquidation
            ]);

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_participations' => $allParticipations,
                    'sold_participations' => $allParticipations, // Todas se venden
                    'returned_participations' => 0, // No se devuelve ninguna
                    'available_participations' => 0,
                    'total_liquidation' => $totalLiquidation,
                    'registered_payments' => 0,
                    'total_to_pay' => $totalLiquidation,
                    'sets_info' => [[
                        'set_id' => $setId,
                        'set_name' => $set->set_name,
                        'total_participations' => $allParticipations,
                        'sold_participations' => $allParticipations,
                        'returned_participations' => 0,
                        'price_per_participation' => $pricePerParticipation,
                        'liquidation' => $totalLiquidation
                    ]]
                ]
            ]);
        }

        if (empty($selectedParticipations)) {
            \Log::info('No participations and no set selected, returning empty summary');
            return response()->json([
                'success' => true,
                'summary' => [
                    'total_participations' => 0,
                    'sold_participations' => 0,
                    'returned_participations' => 0,
                    'available_participations' => 0,
                    'total_liquidation' => 0,
                    'registered_payments' => 0,
                    'total_to_pay' => 0,
                    'sets_info' => []
                ]
            ]);
        }

        // Obtener las participaciones seleccionadas
        $participations = Participation::whereIn('id', $selectedParticipations)->get();
        \Log::info('Found participations:', $participations->toArray());
        
        $setIds = $participations->pluck('set_id')->unique()->toArray();
        \Log::info('Unique set IDs:', $setIds);

        $setsInfo = [];
        $totalParticipations = 0;
        $totalSold = 0;
        $totalReturned = 0;
        $totalLiquidation = 0;

        foreach ($setIds as $setId) {
            $set = Set::find($setId);
            if (!$set) {
                \Log::warning("Set not found: $setId");
                continue;
            }

            \Log::info("Processing set: $setId - " . $set->set_name);

            // Participaciones del set que se van a devolver
            $returnedInSet = $participations->where('set_id', $setId)->count();
            
            // Todas las participaciones del set
            $allInSet = Participation::where('set_id', $setId)
                ->whereIn('status', ['disponible', 'asignada'])
                ->count();
            
            // Las que se van a vender (total - devueltas)
            $soldInSet = $allInSet - $returnedInSet;
            
            // Precio por participación del set
            $pricePerParticipation = $set->played_amount ?? 0;
            
            // Liquidación del set
            $setLiquidation = $soldInSet * $pricePerParticipation;

            \Log::info("Set $setId stats:", [
                'returned' => $returnedInSet,
                'total' => $allInSet,
                'sold' => $soldInSet,
                'price' => $pricePerParticipation,
                'liquidation' => $setLiquidation
            ]);

            $setsInfo[] = [
                'set_id' => $setId,
                'set_name' => $set->set_name,
                'total_participations' => $allInSet,
                'sold_participations' => $soldInSet,
                'returned_participations' => $returnedInSet,
                'available_participations' => 0, // Ya no hay disponibles después de la liquidación
                'price_per_participation' => $pricePerParticipation,
                'total_liquidation' => $setLiquidation
            ];

            $totalParticipations += $allInSet;
            $totalSold += $soldInSet;
            $totalReturned += $returnedInSet;
            $totalLiquidation += $setLiquidation;
        }

        $summary = [
            'total_participations' => $totalParticipations,
            'sold_participations' => $totalSold,
            'returned_participations' => $totalReturned,
            'available_participations' => 0,
            'total_liquidation' => $totalLiquidation,
            'registered_payments' => 0, // Por ahora siempre 0
            'total_to_pay' => $totalLiquidation,
            'sets_info' => $setsInfo
        ];

        \Log::info('Final summary:', $summary);

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    /**
     * Generar botones de acción para DataTable
     */
    private function generateActions($id)
    {
        return '
            <a href="' . route('devolutions.show', $id) . '" class="btn btn-sm btn-light" title="Ver detalle">
                <img src="' . url('assets/form-groups/eye.svg') . '" alt="" width="12">
                </a>
            <a href="' . route('devolutions.edit', $id) . '" class="btn btn-sm btn-light" title="Editar">
                <img src="' . url('assets/form-groups/edit.svg') . '" alt="" width="12">
                </a>
                <button type="button" class="btn btn-sm btn-danger btn-eliminar-devolucion" 
                        data-id="' . $id . '" data-name="Devolución #' . $id . '" title="Eliminar">
                <i class="ri-delete-bin-6-line"></i>
                </button>
        ';
    }

    /**
     * Obtener pagos de una devolución
     */
    public function getPayments($id)
    {
        $devolution = Devolution::findOrFail($id);
        $payments = $devolution->payments()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }

    /**
     * Agregar nuevos pagos a una devolución (puede ser múltiple)
     */
    public function addPayment(Request $request, $id)
    {
        try {
            $devolution = Devolution::findOrFail($id);

            $data = $request->validate([
                'pagos' => 'required|array',
                'pagos.*.amount' => 'required|numeric|min:0.01',
                'pagos.*.payment_method' => 'required|in:efectivo,bizum,transferencia,otro',
                'notes' => 'nullable|string|max:1000'
            ]);

            $paymentsCreated = [];

            foreach ($data['pagos'] as $pago) {
                $payment = DevolutionPayment::create([
                    'devolution_id' => $devolution->id,
                    'amount' => $pago['amount'],
                    'payment_method' => $pago['payment_method'],
                    'from_number' => null,
                    'notes' => $data['notes'] ?? 'Pago agregado - ' . ucfirst($pago['payment_method']),
                    'payment_date' => now()
                ]);

                $paymentsCreated[] = $payment;
            }

            return response()->json([
                'success' => true,
                'message' => 'Pagos agregados correctamente',
                'payments' => $paymentsCreated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar los pagos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un pago existente
     */
    public function updatePayment(Request $request, $devolutionId, $paymentId)
    {
        try {
            $payment = DevolutionPayment::where('devolution_id', $devolutionId)
                ->where('id', $paymentId)
                ->firstOrFail();

            $data = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:efectivo,bizum,transferencia,otro',
                'from_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000'
            ]);

            $payment->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Pago actualizado correctamente',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un pago
     */
    public function deletePayment($devolutionId, $paymentId)
    {
        try {
            $payment = DevolutionPayment::where('devolution_id', $devolutionId)
                ->where('id', $paymentId)
                ->firstOrFail();

            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pago eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el pago: ' . $e->getMessage()
            ], 500);
        }
    }
}
