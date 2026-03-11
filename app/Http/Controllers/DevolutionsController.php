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

            // Normalizar solo_devolucion para que la validación boolean acepte "true"/"1" del front
            $request->merge([
                'solo_devolucion' => filter_var($request->input('solo_devolucion', false), FILTER_VALIDATE_BOOLEAN)
            ]);

            $data = $request->validate([
                'entity_id' => 'required|exists:entities,id',
                'lottery_id' => 'required|exists:lotteries,id',
                'seller_id' => 'nullable|exists:sellers,id', // Ahora es opcional
                'set_id' => 'nullable|exists:sets,id', // Para liquidar set completo sin devoluciones
                'solo_devolucion' => 'nullable|boolean', // Solo devolver participaciones, sin liquidar
                'participations' => 'nullable|array',
                'participations.*' => 'nullable|integer|exists:participations,id',
                'return_reason' => 'nullable|string|max:255',
                'liquidacion' => 'required|array',
                'liquidacion.devolver' => 'nullable|array',
                'liquidacion.vender' => 'nullable|array',
                'liquidacion.pagos' => 'nullable|array',
                'liquidacion.pagos.*.payment_method' => 'required_with:liquidacion.pagos.*|string',
                'liquidacion.pagos.*.amount' => 'required_with:liquidacion.pagos.*|numeric'
            ]);

            $soloDevolucion = !empty($data['solo_devolucion']);

            if ($soloDevolucion) {
                $devolver = $data['liquidacion']['devolver'] ?? [];
                if (empty($devolver) || !is_array($devolver)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Selecciona al menos una participación para devolver.'
                    ], 422);
                }
            }

            // Si no es solo devolución: exigir al menos un pago con importe para completar la liquidación
            if (!$soloDevolucion) {
                $pagos = $data['liquidacion']['pagos'] ?? [];
                if (empty($pagos) || !is_array($pagos)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes registrar al menos un pago para completar la liquidación.'
                    ], 422);
                }
                $tieneImporte = collect($pagos)->contains(fn ($p) => (float) ($p['amount'] ?? 0) > 0);
                if (!$tieneImporte) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes registrar al menos un pago con importe.'
                    ], 422);
                }
            }

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
                    ->get()
                    ->map(fn ($p) => $p->display_participation_code)
                    ->toArray();
                
                if (!empty($anuladas)) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pueden procesar participaciones anuladas: ' . implode(', ', $anuladas)
                    ], 400);
                }
            }

            // Participaciones a devolver: solo permitir asignada o disponible (nunca vendida ni pagada)
            $requestedToReturn = $data['liquidacion']['devolver'] ?? [];
            $allowedToReturn = Participation::forUser(auth()->user())
                ->whereIn('id', $requestedToReturn)
                ->whereIn('status', ['asignada', 'disponible'])
                ->pluck('id')
                ->toArray();
            $vendidasOPagadas = array_diff($requestedToReturn, $allowedToReturn);
            if (!empty($vendidasOPagadas)) {
                $codigos = Participation::forUser(auth()->user())
                    ->whereIn('id', $vendidasOPagadas)
                    ->get()
                    ->map(fn ($p) => $p->display_participation_code)
                    ->toArray();
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden devolver participaciones ya vendidas o pagadas: ' . implode(', ', $codigos)
                ], 400);
            }
            $participationsToReturn = $allowedToReturn;
            $tipoDevolucion = $request->input('tipo_devolucion');

            // Calcular participaciones a vender
            $participationsToSell = [];
            $sellerIdForSell = $data['seller_id'] ?? null;
            if ($tipoDevolucion === 'vendedor' && !empty($participationsToReturn) && $sellerIdForSell) {
                // Devolución vendedor: las que quedan asignadas al vendedor (no devueltas) se marcan como vendida
                $participations = Participation::forUser(auth()->user())
                    ->whereIn('id', $participationsToReturn)->get();
                $setIds = $participations->pluck('set_id')->unique()->toArray();
                foreach ($setIds as $setId) {
                    $returnedInSet = $participations->where('set_id', $setId)->pluck('id')->toArray();
                    $allSellerInSet = Participation::forUser(auth()->user())
                        ->where('set_id', $setId)
                        ->where('seller_id', $sellerIdForSell)
                        ->where('status', '!=', 'anulada')
                        ->pluck('id')
                        ->toArray();
                    $toSellInSet = array_values(array_diff($allSellerInSet, $returnedInSet));
                    $participationsToSell = array_merge($participationsToSell, $toSellInSet);
                }
            }
            // Caso 1: No hay participaciones a devolver pero hay set_id (liquidar set completo)
            // Entidad→administración: todo el set. Vendedor→entidad: solo participaciones del vendedor.
            elseif (empty($participationsToReturn) && isset($data['set_id'])) {
                $setId = $data['set_id'];

                Set::forUser(auth()->user())->findOrFail($setId);
                $query = Participation::forUser(auth()->user())
                    ->where('set_id', $setId)
                    ->whereIn('status', ['disponible', 'asignada', 'vendida'])
                    ->where('status', '!=', 'anulada');
                if ($tipoDevolucion === 'vendedor' && !empty($data['seller_id'])) {
                    $query->where('seller_id', $data['seller_id']);
                }
                $participationsToSell = $query->pluck('id')->toArray();
            }
            // Caso 2: Hay participaciones a devolver (solo para administración; vendedor solo desasigna, no marca el resto como vendida)
            elseif (!empty($participationsToReturn) && $tipoDevolucion !== 'vendedor') {
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

            // Solo devolución: no marcar ninguna como vendida ni liquidar
            if ($soloDevolucion) {
                $participationsToSell = [];
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
                    $baseCount = Participation::forUser(auth()->user())
                        ->where('set_id', $setId)
                        ->where('status', '!=', 'anulada');
                    if ($tipoDevolucion === 'vendedor' && !empty($data['seller_id'])) {
                        $baseCount->where('seller_id', $data['seller_id']);
                    }
                    $totalInSet = $baseCount->count();
                    $price = $set->played_amount ?? 0;
                    $totalParticipations = $totalInSet;
                    $totalLiquidation = $totalInSet * $price;
                }
            } elseif (!empty($participationsToReturn)) {
                $participations = Participation::forUser(auth()->user())
                    ->whereIn('id', $participationsToReturn)->get();
                $setIds = $participations->pluck('set_id')->unique()->toArray();
                $sellerIdForCalc = $data['seller_id'] ?? null;
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
                    $price = (float) ($set->played_amount ?? 0);
                    $totalParticipations += $totalInSet;
                    // Devolución vendedor: liquidación = por las que QUEDAN con el vendedor (total asignadas al vendedor en set − devueltas)
                    if ($tipoDevolucion === 'vendedor' && $sellerIdForCalc) {
                        $totalSellerInSet = Participation::forUser(auth()->user())
                            ->where('set_id', $setId)
                            ->where('seller_id', $sellerIdForCalc)
                            ->where('status', '!=', 'anulada')
                            ->count();
                        $remainingWithSeller = max(0, $totalSellerInSet - $returnedInSet);
                        $totalLiquidation += $remainingWithSeller * $price;
                    } else {
                        $totalLiquidation += ($totalInSet - $returnedInSet) * $price;
                    }
                }
                if ($tipoDevolucion === 'vendedor') {
                    // En el registro: total participaciones = las que se liquidan (quedan con vendedor); el detalle de devolución ya tiene las devueltas
                    $totalParticipations = (int) round($totalLiquidation / ($price ?? 1)) ?: count($participationsToReturn);
                    if ($sellerIdForCalc && count($setIds) > 0) {
                        $totalParticipations = 0;
                        foreach ($setIds as $setId) {
                            $totalSellerInSet = Participation::forUser(auth()->user())
                                ->where('set_id', $setId)
                                ->where('seller_id', $sellerIdForCalc)
                                ->where('status', '!=', 'anulada')
                                ->count();
                            $returnedInSet = $participations->where('set_id', $setId)->count();
                            $totalParticipations += max(0, $totalSellerInSet - $returnedInSet);
                        }
                    }
                }
            } else {
                // Sin set ni participaciones a devolver: usar conteo de las procesadas
                $totalParticipations = count($participationsToReturn) + count($participationsToSell);
                $totalLiquidation = 0;
            }

            if ($soloDevolucion) {
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
                    if ($tipoDevolucion === 'vendedor' && !empty($data['seller_id'])) {
                        // Devolución de vendedor a entidad: dejar la participación disponible y sin vendedor
                        $participation->update([
                            'status' => 'disponible',
                            'seller_id' => null,
                            'return_date' => $now->format('Y-m-d'),
                            'return_time' => $now->format('H:i:s'),
                            'return_reason' => $data['return_reason'] ?? 'Devolución de vendedor a entidad',
                            'returned_by' => $userId,
                        ]);

                        DevolutionDetail::create([
                            'devolution_id' => $devolution->id,
                            'participation_id' => $participation->id,
                            'action' => 'devolver_vendedor'
                        ]);
                    } else {
                        // Devolución de entidad a administración (comportamiento existente)
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
            }

            // Procesar participaciones a vender
            if (!empty($participationsToSell)) {
                // USAR MODELO ELOQUENT para disparar el Observer
                $participationsToUpdateSell = Participation::with('set')
                    ->forUser(auth()->user())
                    ->whereIn('id', $participationsToSell)->get();
                
                foreach ($participationsToUpdateSell as $participation) {
                    $saleAmount = $participation->set ? $participation->set->played_amount : 0;
                    // No limpiar buyer_name ni datos del comprador si ya tienen valor (digitalización previa)
                    $participation->update([
                        'status' => 'vendida',
                        'sale_date' => $now->format('Y-m-d'),
                        'sale_time' => $now->format('H:i:s'),
                        'sale_amount' => $saleAmount,
                        'notes' => 'Liquidación automática',
                    ]);

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

            // Revertir cada participación según el tipo de acción y devolution.seller_id (sin nuevos campos)
            // Si la devolución tiene seller_id → participaciones asignadas a ese vendedor (asignada); si no → disponible
            $sellerId = $devolution->seller_id;

            foreach ($devolution->details as $detail) {
                $participation = Participation::forUser(auth()->user())->find($detail->participation_id);
                if (!$participation) {
                    continue;
                }

                $updateData = [
                    'return_date' => null,
                    'return_time' => null,
                    'return_reason' => null,
                    'returned_by' => null,
                ];

                if ($detail->action === 'devolver_vendedor') {
                    // Restaurar: estaba asignada al vendedor de la devolución
                    $updateData['status'] = $sellerId ? 'asignada' : 'disponible';
                    $updateData['seller_id'] = $sellerId;
                } elseif ($detail->action === 'devolver') {
                    // Entidad→administración: quedan disponibles sin vendedor
                    $updateData['status'] = 'disponible';
                    $updateData['seller_id'] = null;
                } else {
                    // action === 'vender': deshacer liquidación; si la devolución es de vendedor, quedan asignadas a él
                    $updateData['status'] = $sellerId ? 'asignada' : 'disponible';
                    $updateData['seller_id'] = $sellerId;
                    $updateData['sale_date'] = null;
                    $updateData['sale_time'] = null;
                    $updateData['sale_amount'] = null;
                    $updateData['notes'] = null;
                    // No tocar buyer_name, buyer_phone, buyer_email, buyer_nif (pueden estar digitalizados)
                }

                $participation->update($updateData);
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
     * API: Listado de devoluciones (JSON)
     */
    public function apiIndex()
    {
        return $this->data();
    }

    /**
     * API: Crear devolución (delega en store; store ya devuelve JSON)
     */
    public function apiStore(Request $request)
    {
        return $this->store($request);
    }

    /**
     * API: Ver una devolución (JSON)
     */
    public function apiShow(string $id)
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

        return response()->json([
            'success' => true,
            'devolution' => $devolution
        ]);
    }

    /**
     * API: Actualizar devolución (JSON)
     */
    public function apiUpdate(Request $request, string $id)
    {
        $this->update($request, $id);
        return response()->json(['success' => true, 'message' => 'Actualizado']);
    }

    /**
     * API: Eliminar devolución (delega en destroy; destroy ya devuelve JSON)
     */
    public function apiDestroy(string $id)
    {
        return $this->destroy($id);
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
     * Obtener entidades disponibles.
     * Si el usuario es gestor (tiene registros en managers), solo devuelve entidades donde es gestor.
     * Así en devoluciones solo puede devolver participaciones de sus entidades gestionadas.
     */
    public function getEntities()
    {
        $user = auth()->user();
        $entityIds = $user->getManagerEntityIds();
        if (empty($entityIds)) {
            $entityIds = $user->accessibleEntityIds();
        }
        if (empty($entityIds)) {
            return response()->json(['success' => true, 'entities' => []]);
        }

        $query = Entity::with('administration')->whereIn('id', $entityIds);
        // En la app (API) solo se devuelven entidades activas; en la web se devuelven todas para mostrarlas y bloquear las inactivas
        if (request()->is('api/*')) {
            $query->where('status', 1);
        }
        $entities = $query->get()
            ->map(function ($entity) {
                return [
                    'id' => $entity->id,
                    'name' => $entity->name,
                    'image' => $entity->image,
                    'province' => $entity->province ?? 'N/A',
                    'city' => $entity->city ?? 'N/A',
                    'administration_name' => $entity->administration->name ?? 'Sin administración',
                    'status' => $entity->status == 1 ? 'activo' : ($entity->status == 0 ? 'inactivo' : 'pendiente'),
                ];
            });

        return response()->json([
            'success' => true,
            'entities' => $entities,
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

        $lotteries = Lottery::select(['lotteries.id', 'lotteries.name', 'lotteries.description', 'lotteries.draw_date', 'lotteries.image'])
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
        
        // Ahora usamos la relación many-to-many. Mostrar Activo, Pendiente y Bloqueado (solo ocultar Inactivo)
        $sellers = Seller::with(['user:id,name,last_name,email,phone,image'])
            ->forUser(auth()->user())
            ->whereHas('entities', function($query) use ($entityId) {
                $query->where('entities.id', $entityId);
            })
            ->whereIn('status', [\App\Models\Seller::STATUS_ACTIVE/*, \App\Models\Seller::STATUS_PENDING, \App\Models\Seller::STATUS_BLOCKED*/])
            ->get()
            ->map(function($seller) {
                $statusMap = [
                    \App\Models\Seller::STATUS_ACTIVE => 'active',
                    \App\Models\Seller::STATUS_PENDING => 'pending',
                    \App\Models\Seller::STATUS_BLOCKED => 'blocked',
                ];
                $status = $statusMap[(int) $seller->status] ?? 'inactive';
                return [
                    'id' => $seller->id,
                    'user_id' => $seller->user_id,
                    'seller_type' => $seller->seller_type,
                    'status' => $status,
                    'user' => [
                        'id' => $seller->user ? $seller->user->id : null,
                        'name' => $seller->display_name,
                        'last_name' => $seller->display_last_name,
                        'email' => $seller->display_email,
                        'phone' => $seller->display_phone,
                        'image' => $seller->user ? $seller->user->image : null,
                    ],
                    'image' => $seller->user ? $seller->user->image : null,
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
                'sets.reserve_id',
                'sets.digital_participations',
                'sets.physical_participations'
            ])
            ->orderBy('sets.set_number')
            ->get();

        return response()->json([
            'success' => true,
            'sets' => $sets
        ]);
    }

    /**
     * Obtener reservas por entidad y sorteo (para asignación/devolución por reserva).
     * Si solo hay una reserva, el front puede preseleccionarla.
     * Opcional: seller_id para devolución vendedor→entidad (solo reservas con participaciones de ese vendedor).
     */
    public function getReservesByEntityAndLottery(Request $request)
    {
        $entityId = $request->get('entity_id');
        $lotteryId = $request->get('lottery_id');
        $sellerId = $request->get('seller_id');

        if (!$entityId || !$lotteryId) {
            return response()->json(['success' => true, 'reserves' => []]);
        }

        if (!auth()->user()->canAccessEntity((int) $entityId)) {
            return response()->json(['success' => true, 'reserves' => []]);
        }

        if ($sellerId && !auth()->user()->canAccessSeller((int) $sellerId)) {
            return response()->json(['success' => true, 'reserves' => []]);
        }

        $reservesQuery = Reserve::forUser(auth()->user())
            ->where('entity_id', $entityId)
            ->where('lottery_id', $lotteryId)
            ->whereHas('sets', function ($q) use ($sellerId) {
                $q->whereHas('participations', function ($p) use ($sellerId) {
                    $p->whereIn('status', ['disponible', 'asignada'])
                      ->where('status', '!=', 'anulada');
                    if ($sellerId) {
                        $p->where('seller_id', $sellerId);
                    }
                });
            })
            ->orderBy('id');

        $reserves = $reservesQuery->get()->map(function ($reserve) {
            $nums = $reserve->reservation_numbers;
            if (is_array($nums) && count($nums) > 0) {
                $label = implode(' - ', $nums);
            } else {
                $label = 'Reserva #' . str_pad((string) $reserve->id, 5, '0', STR_PAD_LEFT);
            }
            return [
                'id' => $reserve->id,
                'reservation_numbers' => $reserve->reservation_numbers,
                'display_label' => $label,
            ];
        });

        return response()->json([
            'success' => true,
            'reserves' => $reserves,
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
        
        $query = Participation::select([
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
            ->where(function($q) {
                $q->whereNull('participations.sale_date')
                  ->whereNull('participations.return_date');
            });

        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $entityIds = $user->accessibleEntityIds();
            $sellerIds = $user->accessibleSellerIds();
            if (empty($entityIds) && empty($sellerIds)) {
                return response()->json(['success' => true, 'participations' => collect()]);
            }
            $query->where(function ($q) use ($entityIds, $sellerIds) {
                if (!empty($entityIds)) {
                    $q->whereIn('participations.entity_id', $entityIds);
                }
                if (!empty($sellerIds)) {
                    $q->orWhereIn('participations.seller_id', $sellerIds);
                }
            });
        }

        $participations = $query->get();

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
            'set_id' => 'nullable|exists:sets,id', // Opcional si se envía reserve_id o referencia
            'reserve_id' => 'nullable|exists:reserves,id', // Alternativa a set_id: participaciones de toda la reserva
            'desde' => 'nullable|integer',
            'hasta' => 'nullable|integer',
            'participation_id' => 'nullable|integer', // Número de participación, no ID de base de datos
            'referencia' => 'nullable|string' // QR: referencia para resolver set + participación
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

        // Si viene referencia (QR), resolver y devolver esa participación si pertenece al sorteo/entidad
        if (!empty($data['referencia'])) {
            return $this->validateByReference(
                $data['referencia'],
                (int) ($data['entity_id'] ?? 0),
                (int) $data['lottery_id']
            );
        }

        if (empty($data['set_id']) && empty($data['reserve_id'])) {
            return response()->json(['success' => false, 'message' => 'Falta set_id, reserve_id o referencia.'], 422);
        }

        if (!empty($data['set_id'])) {
            Set::forUser(auth()->user())->findOrFail($data['set_id']);
        }
        if (!empty($data['reserve_id'])) {
            Reserve::forUser(auth()->user())->findOrFail($data['reserve_id']);
        }

        $query = Participation::select([
                'participations.id',
                'participations.participation_number as number',
                'participations.participation_code',
                'participations.set_id',
                'sets.set_name'
            ])
            ->join('sets', 'participations.set_id', '=', 'sets.id')
            ->join('reserves', 'sets.reserve_id', '=', 'reserves.id')
            ->where('reserves.lottery_id', $data['lottery_id']);

        if (!empty($data['set_id'])) {
            $query->where('participations.set_id', $data['set_id']);
        } else {
            $query->where('reserves.id', $data['reserve_id']);
        }

        // Filtro de acceso (mismo criterio que Participation::forUser, con columnas calificadas para evitar ambigüedad con sets.entity_id)
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $entityIds = $user->accessibleEntityIds();
            $sellerIds = $user->accessibleSellerIds();
            if (empty($entityIds) && empty($sellerIds)) {
                return response()->json(['success' => true, 'participations' => collect()]);
            }
            $query->where(function ($q) use ($entityIds, $sellerIds) {
                if (!empty($entityIds)) {
                    $q->whereIn('participations.entity_id', $entityIds);
                }
                if (!empty($sellerIds)) {
                    $q->orWhereIn('participations.seller_id', $sellerIds);
                }
            });
        }

        // Solo se pueden devolver participaciones asignadas o disponibles (nunca vendidas ni pagadas)
        if (isset($data['seller_id'])) {
            $query->where('participations.seller_id', $data['seller_id'])
                  ->whereIn('participations.status', ['asignada', 'disponible']);
        } else {
            $query->whereIn('participations.status', ['asignada', 'disponible']);
        }

        // EXCLUIR ANULADAS
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
     * Resolver referencia (QR) y validar participación para devolución en contexto entity/lottery.
     */
    private function validateByReference(string $referencia, int $entityId, int $lotteryId)
    {
        $found = $this->findSetAndParticipationByReference($referencia);
        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'No se encuentra ninguna participación con esa referencia.',
                'participations' => []
            ], 404);
        }
        $set = $found['set'];
        $participationNumber = $found['participation_number'];

        if ($entityId && $set->entity_id != $entityId) {
            return response()->json([
                'success' => false,
                'message' => 'La participación no pertenece a la entidad seleccionada.',
                'participations' => []
            ], 422);
        }
        $reserve = $set->reserve ?? $set->reserve()->first();
        if (!$reserve || $reserve->lottery_id != $lotteryId) {
            return response()->json([
                'success' => false,
                'message' => 'La participación no pertenece al sorteo seleccionado.',
                'participations' => []
            ], 422);
        }

        $participation = Participation::forUser(auth()->user())
            ->where('set_id', $set->id)
            ->where('participation_number', $participationNumber)
            ->whereIn('status', ['disponible', 'asignada', 'vendida'])
            ->where('status', '!=', 'anulada')
            ->first();

        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'Esa participación no está disponible para devolución.',
                'participations' => []
            ], 422);
        }

        return response()->json([
            'success' => true,
            'participations' => [[
                'id' => $participation->id,
                'number' => $participation->participation_number,
                'participation_code' => $participation->display_participation_code,
                'set_id' => $set->id,
                'set_name' => $set->set_name ?? $set->name ?? 'Set ' . $set->set_number,
            ]]
        ]);
    }

    private function findSetAndParticipationByReference(string $referencia): ?array
    {
        $set = Set::forUser(auth()->user())->whereNotNull('tickets')->get()->first(function ($s) use ($referencia) {
            if (!is_array($s->tickets)) {
                return false;
            }
            foreach ($s->tickets as $ticket) {
                if (isset($ticket['r']) && $ticket['r'] == $referencia) {
                    return true;
                }
            }
            return false;
        });
        if (!$set) {
            return null;
        }
        $participationNumber = null;
        foreach ($set->tickets as $ticket) {
            if (isset($ticket['r']) && $ticket['r'] == $referencia) {
                $participationNumber = $ticket['n'] ?? null;
                break;
            }
        }
        return $participationNumber !== null ? ['set' => $set, 'participation_number' => $participationNumber] : null;
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
        $tipoDevolucion = $request->get('tipo_devolucion');
        $sellerId = $request->get('seller_id');
        // Si hay seller_id y participaciones seleccionadas, tratar como vendedor para el cálculo (liquidar por las que quedan)
        if ($sellerId && !empty($selectedParticipations) && $tipoDevolucion !== 'vendedor') {
            $tipoDevolucion = 'vendedor';
        }

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

        // Sin participaciones seleccionadas pero con set_id: resumen del set (liquidar sin devolver nada)
        // Entidad→administración: todo el set. Vendedor→entidad: solo participaciones del vendedor.
        if (empty($selectedParticipations) && $setId) {
            \Log::info('No participations selected but set provided, returning set breakdown for liquidar sin devolver');
            
            $set = Set::forUser(auth()->user())->find($setId);
            if (!$set) {
                \Log::warning("Set not found: $setId");
                return response()->json([
                    'success' => false,
                    'message' => 'Set no encontrado'
                ], 404);
            }

            $baseQuery = Participation::forUser(auth()->user())
                ->where('set_id', $setId)
                ->where('status', '!=', 'anulada');
            if ($tipoDevolucion === 'vendedor' && $sellerId) {
                $baseQuery->where('seller_id', $sellerId);
            }

            $totalInSet = (clone $baseQuery)->count();
            $ventasRegistradas = (clone $baseQuery)->where('status', 'vendida')->count();
            $devueltas = (clone $baseQuery)->where('status', 'devuelta')->count();
            $disponibles = (clone $baseQuery)->whereIn('status', ['disponible', 'asignada'])->count();

            $pricePerParticipation = (float) ($set->played_amount ?? 0);
            // Total liquidación = (total del set − ya devueltas) × precio (igual que cuando se seleccionan participaciones a devolver)
            $totalLiquidation = ($totalInSet - $devueltas) * $pricePerParticipation;

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_participations' => $totalInSet,
                    'ventas_registradas' => $ventasRegistradas,
                    'sold_participations' => $ventasRegistradas,
                    'returned_participations' => $devueltas,
                    'available_participations' => $disponibles,
                    'total_liquidation' => $totalLiquidation,
                    'registered_payments' => 0,
                    'total_to_pay' => $totalLiquidation,
                    'sets_info' => [[
                        'set_id' => $setId,
                        'set_name' => $set->set_name,
                        'total_participations' => $totalInSet,
                        'sold_participations' => $ventasRegistradas,
                        'returned_participations' => $devueltas,
                        'available_participations' => $disponibles,
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
        $totalSellerParticipations = 0;   // solo para vendedor: total asignadas al vendedor en el set
        $totalRemainingWithSeller = 0;     // solo para vendedor: las que quedan a liquidar

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

            // Precio por participación del set
            $pricePerParticipation = (float) ($set->played_amount ?? 0);

            $totalSellerInSet = $totalInSet; // por defecto; en vendedor se sobrescribe
            // Devolución vendedor: liquidar por las que QUEDAN asignadas al vendedor (total del vendedor en el set − devueltas)
            if ($tipoDevolucion === 'vendedor' && $sellerId) {
                $totalSellerInSet = Participation::forUser(auth()->user())
                    ->where('set_id', $setId)
                    ->where('seller_id', $sellerId)
                    ->where('status', '!=', 'anulada')
                    ->count();
                $remainingWithSeller = max(0, $totalSellerInSet - $returnedInSet);
                $setLiquidation = $remainingWithSeller * $pricePerParticipation;
                $soldInSet = $remainingWithSeller; // para estadísticas: las que se liquidan (quedan con vendedor)
                $totalSellerParticipations += $totalSellerInSet;
                $totalRemainingWithSeller += $remainingWithSeller;
            } else {
                // Administración: liquidación = (total set - devueltas) × precio (lo que queda como vendido)
                $soldInSet = $allInSet - $returnedInSet;
                $setLiquidation = ($totalInSet - $returnedInSet) * $pricePerParticipation;
            }

            \Log::info("Set $setId stats:", [
                'total_in_set' => $totalInSet,
                'in_pool' => $allInSet,
                'returned' => $returnedInSet,
                'sold' => $soldInSet,
                'price' => $pricePerParticipation,
                'liquidation' => $setLiquidation,
                'tipo_devolucion' => $tipoDevolucion
            ]);

            $setsInfo[] = [
                'set_id' => $setId,
                'set_name' => $set->set_name,
                'total_participations' => $totalSellerInSet,
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

        // Para la UI: administración = ventas que quedarán (Total - Devueltas). Vendedor = participaciones que quedan a liquidar (las que siguen con el vendedor)
        $ventasRegistradasDisplay = $tipoDevolucion === 'vendedor' ? $totalRemainingWithSeller : ($totalParticipations - $totalReturned);

        // Devolución vendedor: mostrar total del vendedor en el set (ej. 100), no solo las devueltas
        $totalParticipationsDisplay = $tipoDevolucion === 'vendedor' ? $totalSellerParticipations : $totalParticipations;

        $summary = [
            'total_participations' => $totalParticipationsDisplay,
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
                $codigosAnulados = $yaAnuladas->map(fn ($p) => $p->display_participation_code)->toArray();
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
