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
            ->forUser(auth()->user())
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
        // Verificar si es una anulación ANTES de iniciar transacción
        if ($request->input('tipo_devolucion') === 'anulacion') {
            return $this->procesarAnulacion($request);
        }
        
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

            if (!auth()->user()->canAccessEntity((int) $data['entity_id'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para gestionar devoluciones de esta entidad'
                ], 403);
            }

            // VALIDAR: Si hay seller_id, verificar que pertenezca a la entidad
            if (!empty($data['seller_id'])) {
                if (!auth()->user()->canAccessSeller((int) $data['seller_id'])) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para gestionar devoluciones de este vendedor'
                    ], 403);
                }

                $seller = Seller::with('entities')->find($data['seller_id']);
                
                if (!$seller || !$seller->belongsToEntity($data['entity_id'])) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'El vendedor seleccionado no pertenece a la entidad especificada'
                    ], 400);
                }
            }

            // VALIDAR: Rechazar participaciones anuladas
            $allParticipationIds = array_merge(
                $data['liquidacion']['devolver'] ?? [],
                $data['liquidacion']['vender'] ?? []
            );
            
            if (!empty($allParticipationIds)) {
                $anuladas = Participation::forUser(auth()->user())
                    ->whereIn('id', $allParticipationIds)
                    ->where('status', 'anulada')
                    ->pluck('participation_code')
                    ->toArray();
                
                if (!empty($anuladas)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pueden procesar participaciones anuladas: ' . implode(', ', $anuladas)
                    ], 400);
                }
            }

            // Obtener las participaciones seleccionadas para devolver (puede estar vacío)
            $participationsToReturn = $data['liquidacion']['devolver'] ?? [];
            
            // Calcular participaciones a vender
            $participationsToSell = [];
            
            // Caso 1: No hay participaciones a devolver pero hay set_id (liquidar set completo)
            if (empty($participationsToReturn) && isset($data['set_id'])) {
                $setId = $data['set_id'];

                Set::forUser(auth()->user())->findOrFail($setId);
                // Todas las participaciones del set se venden (EXCLUIR ANULADAS)
                $participationsToSell = Participation::forUser(auth()->user())
                    ->where('set_id', $setId)
                    ->whereIn('status', ['disponible', 'asignada', 'vendida'])
                    ->where('status', '!=', 'anulada')
                    ->pluck('id')
                    ->toArray();
            }
            // Caso 2: Hay participaciones a devolver
            elseif (!empty($participationsToReturn)) {
                // Obtener los sets únicos de las participaciones seleccionadas
                $participations = Participation::forUser(auth()->user())
                    ->whereIn('id', $participationsToReturn)->get();
                $setIds = $participations->pluck('set_id')->unique()->toArray();
                
                // Para cada set, obtener todas las participaciones disponibles y calcular cuáles vender
                foreach ($setIds as $setId) {
                    // Participaciones del set que se van a devolver
                    $returnedInSet = $participations->where('set_id', $setId)->pluck('id')->toArray();
                    
                    // Todas las participaciones disponibles del set (EXCLUIR ANULADAS)
                    $allInSet = Participation::forUser(auth()->user())
                        ->where('set_id', $setId)
                        ->whereIn('status', ['disponible', 'asignada'])
                        ->where('status', '!=', 'anulada')
                        ->pluck('id')
                        ->toArray();
                    
                    // Las que no se devuelven, se venden
                    $toSellInSet = array_diff($allInSet, $returnedInSet);
                    $participationsToSell = array_merge($participationsToSell, $toSellInSet);
                }
            }

            $now = Carbon::now();
            $userId = auth()->id();

            // Calcular total real del set/s y liquidación (misma lógica que getLiquidationSummary)
            $totalParticipations = 0;
            $totalLiquidation = 0.0;

            if (empty($participationsToReturn) && isset($data['set_id'])) {
                $setId = (int) $data['set_id'];
                $set = Set::forUser(auth()->user())->find($setId);
                if ($set) {
                    $totalInSet = Participation::forUser(auth()->user())
                        ->where('set_id', $setId)
                        ->where('status', '!=', 'anulada')
                        ->count();
                    $price = $set->played_amount ?? 0;
                    $totalParticipations = $totalInSet;
                    $totalLiquidation = $totalInSet * $price;
                }
            } elseif (!empty($participationsToReturn)) {
                $participations = Participation::forUser(auth()->user())
                    ->whereIn('id', $participationsToReturn)->get();
                $setIds = $participations->pluck('set_id')->unique()->toArray();
                foreach ($setIds as $setId) {
                    $set = Set::forUser(auth()->user())->find($setId);
                    if (!$set) {
                        continue;
                    }
                    $totalInSet = Participation::forUser(auth()->user())
                        ->where('set_id', $setId)
                        ->where('status', '!=', 'anulada')
                        ->count();
                    $returnedInSet = $participations->where('set_id', $setId)->count();
                    $price = $set->played_amount ?? 0;
                    $totalParticipations += $totalInSet;
                    $totalLiquidation += ($totalInSet - $returnedInSet) * $price;
                }
            } else {
                // Sin set ni participaciones a devolver: usar conteo de las procesadas
                $totalParticipations = count($participationsToReturn) + count($participationsToSell);
                $totalLiquidation = 0;
            }

            // Crear registro de devolución
            $devolution = Devolution::create([
                'entity_id' => $data['entity_id'],
                'lottery_id' => $data['lottery_id'],
                'seller_id' => $data['seller_id'] ?? null,
                'user_id' => $userId,
                'total_participations' => $totalParticipations,
                'total_liquidation' => $totalLiquidation,
                'return_reason' => $data['return_reason'] ?? 'Devolución de entidad a administración',
                'devolution_date' => $now->format('Y-m-d'),
                'devolution_time' => $now->format('H:i:s'),
                'status' => 'procesada',
                'notes' => 'Devolución procesada automáticamente'
            ]);

            // Procesar participaciones a devolver
            if (!empty($participationsToReturn)) {
                // USAR MODELO ELOQUENT para disparar el Observer
                $participationsToUpdateReturn = Participation::forUser(auth()->user())
                    ->whereIn('id', $participationsToReturn)->get();
                
                foreach ($participationsToUpdateReturn as $participation) {
                    $participation->update([
                        'status' => 'devuelta',
                        'return_date' => $now->format('Y-m-d'),
                        'return_time' => $now->format('H:i:s'),
                        'return_reason' => $data['return_reason'] ?? 'Devolución de entidad a administración',
                        'returned_by' => $userId,
                    ]);

                    // Crear detalle de devolución
                    DevolutionDetail::create([
                        'devolution_id' => $devolution->id,
                        'participation_id' => $participation->id,
                        'action' => 'devolver'
                    ]);
                }
            }

            // Procesar participaciones a vender
            if (!empty($participationsToSell)) {
                // USAR MODELO ELOQUENT para disparar el Observer
                $participationsToUpdateSell = Participation::with('set')
                    ->forUser(auth()->user())
                    ->whereIn('id', $participationsToSell)->get();
                
                foreach ($participationsToUpdateSell as $participation) {
                    // Obtener el precio del set
                    $saleAmount = $participation->set ? $participation->set->played_amount : 0;
                    
                    $participation->update([
                        'status' => 'vendida',
                        'sale_date' => $now->format('Y-m-d'),
                        'sale_time' => $now->format('H:i:s'),
                        'sale_amount' => $saleAmount,
                        'buyer_name' => null,
                        'buyer_phone' => null,
                        'buyer_email' => null,
                        'buyer_nif' => null,
                        'notes' => 'Liquidación automática',
                    ]);

                    // Crear detalle de venta
                    DevolutionDetail::create([
                        'devolution_id' => $devolution->id,
                        'participation_id' => $participation->id,
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
        ])
        ->forUser(auth()->user())
        ->findOrFail($id);
        
        return view('devolutions.show', compact('devolution'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $devolution = Devolution::with(['entity', 'lottery', 'seller', 'user', 'details.participation.set', 'payments'])
            ->forUser(auth()->user())
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

            $devolution = Devolution::with('details')
                ->forUser(auth()->user())
                ->findOrFail($id);

            // Obtener IDs de participaciones afectadas
            $participationIds = $devolution->details->pluck('participation_id')->toArray();

            // Revertir estado de participaciones
            // USAR MODELO ELOQUENT para disparar el Observer
            $participationsToRevert = Participation::forUser(auth()->user())
                ->whereIn('id', $participationIds)->get();
            
            foreach ($participationsToRevert as $participation) {
                $participation->update([
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
                ]);
            }

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
            ->forUser(auth()->user())
            ->select([
                'devolutions.id',
                'devolutions.entity_id',
                'devolutions.lottery_id',
                'devolutions.seller_id',
                'devolutions.user_id',
                'devolutions.total_participations',
                'devolutions.total_liquidation',
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
                
                // Total liquidación: usar valor guardado si existe, si no calcular desde detalles
                $totalLiquidation = $devolution->total_liquidation !== null
                    ? (float) $devolution->total_liquidation
                    : 0;
                if ($totalLiquidation == 0 && $soldCount > 0) {
                    $soldParticipations = $devolution->details()->where('action', 'vender')->with('participation.set')->get();
                    foreach ($soldParticipations as $detail) {
                        if ($detail->participation && $detail->participation->set) {
                            $totalLiquidation += $detail->participation->set->played_amount ?? 0;
                        }
                    }
                }
                $totalPayments = 0;
                
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
        $entities = Entity::with('administration')
            ->forUser(auth()->user())
            ->get()
            ->map(function($entity) {
                return [
                    'id' => $entity->id,
                    'name' => $entity->name,
                    'province' => $entity->province ?? 'N/A',
                    'city' => $entity->city ?? 'N/A',
                    'administration_name' => $entity->administration->name ?? 'Sin administración',
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
        if (!auth()->user()->canAccessEntity((int) $entityId)) {
            return response()->json([
                'success' => true,
                'lotteries' => collect()
            ]);
        }

        $lotteries = Lottery::select(['lotteries.id', 'lotteries.name', 'lotteries.description', 'lotteries.draw_date'])
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
        if (!auth()->user()->canAccessEntity((int) $entityId)) {
            return response()->json([
                'success' => true,
                'sellers' => collect()
            ]);
        }
        
        // Ahora usamos la relación many-to-many
        $sellers = Seller::with(['user:id,name,last_name,email,phone'])
            ->forUser(auth()->user())
            ->whereHas('entities', function($query) use ($entityId) {
                $query->where('entities.id', $entityId);
            })
            ->where('status', true) // status es boolean
            ->get()
            ->map(function($seller) {
                return [
                    'id' => $seller->id,
                    'user_id' => $seller->user_id,
                    'seller_type' => $seller->seller_type,
                    'status' => $seller->status ? 'active' : 'inactive',
                    'user' => [
                        'id' => $seller->user ? $seller->user->id : null,
                        'name' => $seller->display_name,
                        'last_name' => $seller->display_last_name,
                        'email' => $seller->display_email,
                        'phone' => $seller->display_phone
                    ]
                ];
            });

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

        if (!auth()->user()->canAccessSeller((int) $sellerId)) {
            return response()->json([]);
        }

        $sets = Set::with(['reserve'])
            ->forUser(auth()->user())
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

        if (!auth()->user()->canAccessEntity((int) $entityId)) {
            return response()->json([
                'success' => true,
                'sets' => collect()
            ]);
        }

        // Obtener sets que tienen participaciones disponibles o asignadas (no devueltas completamente)
        $sets = Set::with(['reserve'])
            ->forUser(auth()->user())
            ->where('entity_id', $entityId)
            ->whereHas('reserve', function($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId);
            })
            ->whereHas('participations', function($query) {
                // Solo sets que tienen participaciones disponibles o asignadas (EXCLUIR ANULADAS)
                $query->whereIn('status', ['disponible', 'asignada'])
                      ->where('status', '!=', 'anulada');
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

        if (!auth()->user()->canAccessSeller((int) $sellerId)) {
            return response()->json([
                'success' => true,
                'participations' => collect()
            ]);
        }
        
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
            ->forUser(auth()->user())
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

        if (!empty($data['seller_id']) && !auth()->user()->canAccessSeller((int) $data['seller_id'])) {
            return response()->json([
                'success' => true,
                'participations' => collect()
            ]);
        }

        if (!empty($data['entity_id']) && !auth()->user()->canAccessEntity((int) $data['entity_id'])) {
            return response()->json([
                'success' => true,
                'participations' => collect()
            ]);
        }

        Set::forUser(auth()->user())->findOrFail($data['set_id']);

        $query = Participation::select([
                'participations.id',
                'participations.participation_number as number',
                'participations.participation_code'
            ])
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->forUser(auth()->user())
            ->where('reserves.lottery_id', $data['lottery_id'])
            ->where('participations.set_id', $data['set_id']);

        // Si hay seller_id, filtrar por vendedor (devolución de vendedor) - EXCLUIR ANULADAS
        if (isset($data['seller_id'])) {
            $query->where('participations.seller_id', $data['seller_id'])
                  ->whereIn('participations.status', ['asignada', 'vendida','disponible']);
        } else {
            // Si no hay seller_id, mostrar participaciones asignadas o vendidas (devolución de entidad) - EXCLUIR ANULADAS
            $query->whereIn('participations.status', ['asignada', 'vendida','disponible']);
        }
        
        // EXCLUIR EXPLÍCITAMENTE LAS PARTICIPACIONES ANULADAS
        $query->where('participations.status', '!=', 'anulada');

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

        if ($entityId && !auth()->user()->canAccessEntity((int) $entityId)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para consultar esta entidad'
            ], 403);
        }

        \Log::info('=== LIQUIDATION SUMMARY REQUEST ===');
        \Log::info('Entity ID:', [$entityId]);
        \Log::info('Lottery ID:', [$lotteryId]);
        \Log::info('Set ID:', [$setId]);
        \Log::info('Selected Participations:', $selectedParticipations);

        // Si no hay participaciones pero hay set_id, calcular liquidación del set completo
        if (empty($selectedParticipations) && $setId) {
            \Log::info('No participations selected but set provided, calculating full set liquidation');
            
            $set = Set::forUser(auth()->user())->find($setId);
            if (!$set) {
                \Log::warning("Set not found: $setId");
                return response()->json([
                    'success' => false,
                    'message' => 'Set no encontrado'
                ], 404);
            }

            // Contar todas las participaciones del set que están disponibles o asignadas (vendibles) - EXCLUIR ANULADAS
            $allParticipations = Participation::forUser(auth()->user())
                ->where('set_id', $setId)
                ->whereIn('status', ['disponible', 'asignada', 'vendida'])
                ->where('status', '!=', 'anulada')
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

        // Obtener las participaciones seleccionadas (EXCLUIR ANULADAS)
        $participations = Participation::forUser(auth()->user())
            ->whereIn('id', $selectedParticipations)
            ->where('status', '!=', 'anulada')
            ->get();
        \Log::info('Found participations (excluding cancelled):', $participations->toArray());
        
        $setIds = $participations->pluck('set_id')->unique()->toArray();
        \Log::info('Unique set IDs:', $setIds);

        $setsInfo = [];
        $totalParticipations = 0;
        $totalSold = 0;
        $totalReturned = 0;
        $totalLiquidation = 0;

        foreach ($setIds as $setId) {
            $set = Set::forUser(auth()->user())->find($setId);
            if (!$set) {
                \Log::warning("Set not found: $setId");
                continue;
            }

            \Log::info("Processing set: $setId - " . $set->set_name);

            // Participaciones del set que se van a devolver (las seleccionadas por el usuario)
            $returnedInSet = $participations->where('set_id', $setId)->count();

            // Total real del set: todas las participaciones del set (cualquier estado salvo anulada)
            $totalInSet = Participation::forUser(auth()->user())
                ->where('set_id', $setId)
                ->where('status', '!=', 'anulada')
                ->count();

            // Participaciones en pool para esta liquidación: solo disponible + asignada (las que se pueden devolver o liquidar)
            $allInSet = Participation::forUser(auth()->user())
                ->where('set_id', $setId)
                ->whereIn('status', ['disponible', 'asignada'])
                ->where('status', '!=', 'anulada')
                ->count();

            // No devolver más de las que hay en el pool (evitar ventas registradas negativas)
            $returnedInSet = min($returnedInSet, $allInSet);

            // Las que se van a liquidar como vendidas en el pool (para estadísticas)
            $soldInSet = $allInSet - $returnedInSet;

            // Precio por participación del set
            $pricePerParticipation = $set->played_amount ?? 0;

            // Importe a liquidar: (Total del set - Devueltas) × precio (no solo el pool)
            $setLiquidation = ($totalInSet - $returnedInSet) * $pricePerParticipation;

            \Log::info("Set $setId stats:", [
                'total_in_set' => $totalInSet,
                'in_pool' => $allInSet,
                'returned' => $returnedInSet,
                'sold' => $soldInSet,
                'price' => $pricePerParticipation,
                'liquidation' => $setLiquidation
            ]);

            $setsInfo[] = [
                'set_id' => $setId,
                'set_name' => $set->set_name,
                'total_participations' => $totalInSet,
                'sold_participations' => $soldInSet,
                'returned_participations' => $returnedInSet,
                'available_participations' => 0,
                'price_per_participation' => $pricePerParticipation,
                'total_liquidation' => $setLiquidation
            ];

            $totalParticipations += $totalInSet;
            $totalSold += $soldInSet;
            $totalReturned += $returnedInSet;
            $totalLiquidation += $setLiquidation;
        }

        // Para la UI: "Ventas registradas" = cuántas quedarán como vendidas tras la devolución (Total - Devueltas)
        $ventasRegistradasDisplay = $totalParticipations - $totalReturned;

        $summary = [
            'total_participations' => $totalParticipations,
            'sold_participations' => $totalSold,
            'ventas_registradas' => $ventasRegistradasDisplay,
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
        $devolution = Devolution::forUser(auth()->user())->findOrFail($id);
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
            $devolution = Devolution::forUser(auth()->user())->findOrFail($id);

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
            $devolution = Devolution::forUser(auth()->user())->findOrFail($devolutionId);

            $payment = DevolutionPayment::where('devolution_id', $devolution->id)
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
            $devolution = Devolution::forUser(auth()->user())->findOrFail($devolutionId);

            $payment = DevolutionPayment::where('devolution_id', $devolution->id)
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

    /**
     * Procesar anulación de participaciones
     */
    private function procesarAnulacion(Request $request)
    {
        \Log::info("=== INICIANDO PROCESAMIENTO DE ANULACIÓN ===");
        
        try {
            DB::beginTransaction();
            \Log::info("Transacción de anulación iniciada");

            $data = $request->validate([
                'entity_id' => 'required|exists:entities,id',
                'lottery_id' => 'required|exists:lotteries,id',
                'set_id' => 'nullable|exists:sets,id',
                'participations' => 'required|array|min:1',
                'participations.*' => 'integer|exists:participations,id',
                'motivo' => 'required|string|max:500'
            ]);

            if (!auth()->user()->canAccessEntity((int) $data['entity_id'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para gestionar anulaciones de esta entidad'
                ], 403);
            }

            if (!empty($data['set_id'])) {
                Set::forUser(auth()->user())->findOrFail($data['set_id']);
            }

            $now = Carbon::now();
            $userId = auth()->id();

            // Obtener las participaciones a anular
            $participations = Participation::forUser(auth()->user())
                ->whereIn('id', $data['participations'])->get();
            
            if ($participations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron participaciones para anular'
                ], 400);
            }

            // VALIDAR: Rechazar participaciones que ya están anuladas
            $yaAnuladas = $participations->where('status', 'anulada');
            if ($yaAnuladas->count() > 0) {
                $codigosAnulados = $yaAnuladas->pluck('participation_code')->toArray();
                return response()->json([
                    'success' => false,
                    'message' => 'Las siguientes participaciones ya están anuladas: ' . implode(', ', $codigosAnulados)
                ], 400);
            }

            // Anular las participaciones
            foreach ($participations as $participation) {
                try {
                    // Anular la participación (usar update para disparar observer)
                    $participation->update([
                        'status' => 'anulada',
                        'cancellation_reason' => $data['motivo'],
                        'cancelled_by' => $userId,
                        'cancellation_date' => $now->format('Y-m-d'),
                    ]);
                } catch (\Exception $observerError) {
                    // Si el observer falla, continuar sin rollback
                    \Log::warning("Observer falló para participación {$participation->id}", [
                        'error' => $observerError->getMessage()
                    ]);
                    
                    // Actualizar directamente sin observer
                    \DB::table('participations')
                        ->where('id', $participation->id)
                        ->update([
                            'status' => 'anulada',
                            'cancellation_reason' => $data['motivo'],
                            'cancelled_by' => $userId,
                            'cancellation_date' => $now->format('Y-m-d'),
                            'updated_at' => $now,
                        ]);
                }
            }

            // Crear registro de devolución para auditoría
            \Log::info("Creando registro de devolución para anulación", [
                'entity_id' => $data['entity_id'],
                'lottery_id' => $data['lottery_id'],
                'participations_count' => $participations->count()
            ]);
            
            $devolution = Devolution::create([
                'entity_id' => $data['entity_id'],
                'lottery_id' => $data['lottery_id'],
                'seller_id' => null, // Las anulaciones no tienen vendedor
                'user_id' => $userId,
                'total_participations' => $participations->count(),
                'return_reason' => "Anulación: {$data['motivo']}",
                'devolution_date' => $now->format('Y-m-d'),
                'devolution_time' => $now->format('H:i:s'),
                'status' => 'procesada', // Status normal como otras devoluciones
                'notes' => "Anulación de {$participations->count()} participaciones"
            ]);
            
            \Log::info("Devolución creada exitosamente", [
                'devolution_id' => $devolution->id
            ]);

            // Crear detalles de anulación
            \Log::info("Creando detalles de anulación", [
                'devolution_id' => $devolution->id,
                'participations_count' => $participations->count()
            ]);
            
            foreach ($participations as $participation) {
                \Log::info("Creando detalle para participación", [
                    'devolution_id' => $devolution->id,
                    'participation_id' => $participation->id
                ]);
                
                DevolutionDetail::create([
                    'devolution_id' => $devolution->id,
                    'participation_id' => $participation->id,
                    'action' => 'anular'
                ]);
            }
            
            \Log::info("Detalles de anulación creados exitosamente");

            DB::commit();
            
            \Log::info("=== TRANSACCIÓN DE ANULACIÓN CONFIRMADA EXITOSAMENTE ===", [
                'devolution_id' => $devolution->id,
                'participaciones_anuladas' => $participations->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Anulación procesada correctamente. Se anularon {$participations->count()} participaciones.",
                'data' => [
                    'devolution_id' => $devolution->id,
                    'participaciones_anuladas' => $participations->count()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error en anulación de participaciones', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la anulación: ' . $e->getMessage()
            ], 500);
        }
    }
}
