<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use App\Models\Entity;
use App\Models\Reserve;
use App\Models\Set;
use App\Models\Participation;
use App\Models\Lottery;
use App\Models\SellerSettlement;
use App\Models\SellerSettlementPayment;
use App\Services\SellerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::with(['entities', 'groups'])
            ->forUser(auth()->user())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('sellers.index', compact('sellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entities = Entity::with('administration')
            ->forUser(auth()->user())
            ->get();
        return view('sellers.add', compact('entities'));
    }

    /**
     * Store the selected entity in session
     */
    public function store_entity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,id'
        ]);

        $entity = Entity::with('administration')
            ->forUser(auth()->user())
            ->findOrFail($request->entity_id);
        session(['selected_entity' => $entity]);

        return redirect()->route('sellers.add-information');
    }

    /**
     * Show the add information form
     */
    public function add_information()
    {
        $entity = session('selected_entity');

        if (!$entity || !auth()->user()->canAccessEntity($entity->id)) {
            return redirect()->route('sellers.create');
        }

        return view('sellers.add_information');
    }

    /**
     * Store a seller with existing user
     */
    public function store_existing_user(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'entity_id' => 'required|exists:entities,id',
            'name' => 'nullable|string|max:255', // No requerido, puede estar vacío
            'last_name' => 'nullable|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string|max:255',
            'comment' => 'nullable|string'
        ]);

        if (!auth()->user()->canAccessEntity((int) $request->entity_id)) {
            abort(403, 'No tienes permisos para gestionar vendedores de esta entidad.');
        }

        try {
            $sellerService = new SellerService();
            
            // Verificar si el seller ya existe antes de crearlo
            $existingSeller = \App\Models\Seller::where('email', $request->email)->first();
            $wasExisting = $existingSeller !== null;
            
            $seller = $sellerService->createSeller($request->all(), $request->entity_id, 'partilot');

            session()->forget('selected_entity');
            
            // Determinar el mensaje
            if ($wasExisting) {
                $message = 'Vendedor existente agregado a la entidad seleccionada';
            } else {
                $message = $seller->isLinkedToUser() 
                    ? 'Vendedor PARTILOT creado y vinculado exitosamente'
                    : 'Vendedor PARTILOT creado pendiente de vinculación';
            }
                
            return redirect()->route('sellers.index')->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a seller with new user
     */
    public function store_new_user(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255', // No requerido
            'last_name' => 'nullable|string|max:255', // No requerido
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:255',
            'entity_id' => 'required|exists:entities,id'
        ]);

        if (!auth()->user()->canAccessEntity((int) $request->entity_id)) {
            abort(403, 'No tienes permisos para gestionar vendedores de esta entidad.');
        }

        try {
            $sellerService = new SellerService();
            
            // Verificar si el seller ya existe antes de crearlo
            $existingSeller = \App\Models\Seller::where('email', $request->email)->first();
            $wasExisting = $existingSeller !== null;
            
            $seller = $sellerService->createSeller($request->all(), $request->entity_id, 'externo');

            session()->forget('selected_entity');
            
            $message = $wasExisting 
                ? 'Vendedor existente agregado a la entidad seleccionada'
                : 'Vendedor EXTERNO creado exitosamente';
            
            return redirect()->route('sellers.index')->with('success', $message);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if user email exists
     */
    public function check_user_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $seller = Seller::with(['entities.administration'])
            ->forUser(auth()->user())
            ->findOrFail($id);

        $accessibleEntityIds = auth()->user()->accessibleEntityIds();
        if (!empty($accessibleEntityIds)) {
            $filteredEntities = $seller->entities->whereIn('id', $accessibleEntityIds)->values();
        } else {
            $filteredEntities = collect();
        }

        if ($filteredEntities->isEmpty()) {
            abort(403, 'No tienes permisos para ver las entidades de este vendedor.');
        }

        $seller->setRelation('entities', $filteredEntities);

        // Verificar que el vendedor tenga al menos una entidad
        if ($seller->entities->isEmpty()) {
            return back()->withErrors(['error' => 'El vendedor no tiene entidades asignadas']);
        }

        // Determinar la entidad actual
        // 1. Si viene entity_id por parámetro, usarla (y validar que pertenezca al seller)
        // 2. Si no, usar la primera entidad
        $entityId = $request->query('entity_id');
        
        if ($entityId && $seller->belongsToEntity($entityId)) {
            $currentEntity = $seller->entities->where('id', $entityId)->first();
        } else {
            $currentEntity = $seller->getPrimaryEntity();
        }
        
        // Obtener reservas de la entidad actual
        $reserves = collect();
        if ($currentEntity) {
            $reserves = Reserve::where('entity_id', $currentEntity->id)
                ->where('status', 1) // confirmed
                ->with(['lottery'])
                ->get();
        }

        return view('sellers.show', compact('seller', 'currentEntity', 'reserves'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $seller = Seller::with('entities')
            ->forUser(auth()->user())
            ->findOrFail($id);
        $entities = Entity::forUser(auth()->user())->get();
        
        // Obtener grupos de la nueva tabla groups
        $groups = \App\Models\Group::with('entity')
            ->forUser(auth()->user())
            ->orderBy('name', 'asc')
            ->get();
            
        return view('sellers.edit', compact('seller', 'entities', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $seller = Seller::forUser(auth()->user())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'email' => 'required|email|unique:users,email,' . ($seller->user_id ?? 0),
            'phone' => 'nullable|string|max:255',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el vendedor
            $seller->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                // Nuevo FIX: status guarda el valor real, no sólo su existencia
                'status' => $request->input('status', 0),
            ]);

            // Actualizar la relación con grupos (many-to-many)
            if ($request->has('group_id') && !empty($request->group_id)) {
                $seller->groups()->sync([$request->group_id]);
            } else {
                // Si no se selecciona grupo, eliminar todas las relaciones
                $seller->groups()->detach();
            }

            // Actualizar el usuario si existe
            if ($seller->user_id) {
                $user = User::find($seller->user_id);
                if ($user) {
                    $user->update([
                        'name' => $request->name,
                        'last_name' => $request->last_name,
                        'last_name2' => $request->last_name2,
                        'nif_cif' => $request->nif_cif,
                        'birthday' => $request->birthday,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'role' => User::ROLE_SELLER
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sellers.index')->with('success', 'Vendedor actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $seller = Seller::forUser(auth()->user())->findOrFail($id);
            $seller->delete();

            return redirect()->route('sellers.index')->with('success', 'Vendedor eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener sets por reserva
     */
    public function getSetsByReserve(Request $request)
    {
        $request->validate([
            'reserve_id' => 'required|integer|exists:reserves,id'
        ]);

        $reserve = Reserve::forUser(auth()->user())->findOrFail($request->reserve_id);

        // Obtener solo sets que tienen participaciones creadas (diseño)
        $sets = Set::forUser(auth()->user())
            ->where('reserve_id', $reserve->id)
            ->where('status', 1) // activos
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('participations')
                      ->whereRaw('participations.set_id = sets.id');
            })
            ->get();

        return response()->json(['sets' => $sets]);
    }

    /**
     * Validar participaciones disponibles para asignación
     */
    public function validateParticipations(Request $request)
    {
        $request->validate([
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|min:1',
            'set_id' => 'required|integer|exists:sets,id',
            'seller_id' => 'required|integer|exists:sellers,id'
        ]);

        try {
            if (!auth()->user()->canAccessSeller((int) $request->seller_id)) {
                abort(403, 'No tienes permisos para gestionar este vendedor.');
            }

            // Obtener el set
            $set = Set::forUser(auth()->user())->findOrFail($request->set_id);
            
            // Verificar que el set tiene participaciones creadas
            $totalParticipationsInSet = DB::table('participations')
                ->where('set_id', $request->set_id)
                ->count();
                
            if ($totalParticipationsInSet === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este set no tiene participaciones creadas (diseño)'
                ]);
            }
            
            // Verificar que el rango solicitado existe en este set
            $minParticipation = DB::table('participations')
                ->where('set_id', $request->set_id)
                ->min('participation_number');
                
            $maxParticipation = DB::table('participations')
                ->where('set_id', $request->set_id)
                ->max('participation_number');
                
            if ($request->desde < $minParticipation || $request->hasta > $maxParticipation) {
                return response()->json([
                    'success' => false,
                    'message' => "El rango debe estar entre {$minParticipation} y {$maxParticipation} para este set"
                ]);
            }
            
            // Obtener todas las participaciones del set en el rango especificado (para debug)
            $allParticipationsInRange = DB::table('participations')
                ->where('set_id', $request->set_id)
                ->whereBetween('participation_number', [$request->desde, $request->hasta])
                ->select('id', 'participation_number as number', 'status', 'seller_id')
                ->get();

            // Debug: Mostrar todas las participaciones en el rango
            \Log::info('Debug participaciones en rango:', [
                'set_id' => $request->set_id,
                'range' => "{$request->desde} - {$request->hasta}",
                'participations' => $allParticipationsInRange->toArray()
            ]);

            // Obtener las participaciones disponibles del set en el rango especificado
            // EXCLUIR explícitamente las participaciones anuladas
            $participations = DB::table('participations')
                ->where('set_id', $request->set_id)
                ->whereBetween('participation_number', [$request->desde, $request->hasta])
                ->where('status', '!=', 'anulada') // Excluir participaciones anuladas
                ->where(function($query) use ($request) {
                    $query->where('status', 'disponible')
                          ->whereNull('seller_id') // No asignadas a ningún vendedor
                          ->orWhere(function($subQuery) use ($request) {
                              $subQuery->where('status', 'asignada')
                                      ->where('seller_id', $request->seller_id); // Asignadas al vendedor actual
                          });
                })
                ->select('id', 'participation_number as number', 'participation_code', 'status')
                ->get();

            // Verificar que todas las participaciones del rango estén disponibles o asignadas al vendedor actual
            $totalParticipations = $request->hasta - $request->desde + 1;
            $availableParticipations = $participations->count();

            if ($availableParticipations < $totalParticipations) {
                $assignedCount = $totalParticipations - $availableParticipations;
                
                // Obtener información detallada de las participaciones asignadas
                $assignedParticipations = $allParticipationsInRange->where('seller_id', '!=', null);
                $assignedToOthers = $assignedParticipations->where('seller_id', '!=', $request->seller_id);
                $assignedToThisSeller = $assignedParticipations->where('seller_id', $request->seller_id);
                
                $message = "Hay {$assignedCount} participaciones en este rango que no están disponibles";
                if ($assignedToOthers->count() > 0) {
                    $message .= " (asignadas a otros vendedores)";
                } else {
                    $message .= " (ya asignadas a este vendedor)";
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'debug' => [
                        'set_id' => $request->set_id,
                        'total_in_range' => $totalParticipations,
                        'available' => $availableParticipations,
                        'assigned_to_others' => $assignedToOthers->count(),
                        'assigned_to_this_seller' => $assignedToThisSeller->count(),
                        'range_requested' => "{$request->desde} - {$request->hasta}",
                        'set_range' => "{$minParticipation} - {$maxParticipation}"
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'participations' => $participations,
                'debug' => [
                    'set_id' => $request->set_id,
                    'range' => "{$request->desde} - {$request->hasta}",
                    'total_in_range' => $totalParticipations,
                    'available' => $availableParticipations,
                    'all_participations_in_range' => $allParticipationsInRange->toArray()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar participaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Guardar asignaciones de participaciones
     */
    public function saveAssignments(Request $request)
    {
        $request->validate([
            'participations_json' => 'required|string',
            'seller_id' => 'required|integer|exists:sellers,id'
        ]);

        // Decodificar el JSON de participaciones
        $participations = json_decode($request->participations_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los datos de participaciones: ' . json_last_error_msg()
            ]);
        }

        // Validar que participations sea un array y tenga al menos un elemento
        if (!is_array($participations) || empty($participations)) {
            return response()->json([
                'success' => false,
                'message' => 'Debe proporcionar al menos una participación'
            ]);
        }

        // Validar cada participación
        foreach ($participations as $participation) {
            if (!isset($participation['id']) || !isset($participation['number']) || !isset($participation['set_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de participación incompletos'
                ]);
            }
        }

        try {
            DB::beginTransaction();

            if (!auth()->user()->canAccessSeller((int) $request->seller_id)) {
                abort(403, 'No tienes permisos para gestionar este vendedor.');
            }

            $seller = Seller::forUser(auth()->user())->findOrFail($request->seller_id);
            $assignedCount = 0;

            foreach ($participations as $participationData) {
                // Verificar que la participación esté disponible o ya asignada al vendedor actual
                // USAR MODELO ELOQUENT para que se dispare el Observer
                $participation = Participation::where('id', $participationData['id'])
                    ->where('set_id', $participationData['set_id'])
                    ->where(function($query) use ($seller) {
                        $query->where('status', 'disponible')
                              ->whereNull('seller_id')
                              ->orWhere(function($subQuery) use ($seller) {
                                  $subQuery->where('status', 'asignada')
                                          ->where('seller_id', $seller->id);
                              });
                    })
                    ->first();

                if ($participation) {
                    // Asignar la participación al vendedor
                    // USAR update() del modelo para disparar el Observer
                    $participation->update([
                        'seller_id' => $seller->id,
                        'sale_date' => now()->toDateString(),
                        'sale_time' => now()->toTimeString(),
                        'status' => 'asignada'
                    ]);

                    $assignedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se asignaron {$assignedCount} participaciones correctamente",
                'assigned_count' => $assignedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar las asignaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener participaciones asignadas del vendedor por set
     */
    public function getAssignedParticipations(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|integer|exists:sellers,id',
            'set_id' => 'required|integer|exists:sets,id'
        ]);

        try {
            if (!auth()->user()->canAccessSeller((int) $request->seller_id)) {
                abort(403, 'No tienes permisos para consultar este vendedor.');
            }

            Set::forUser(auth()->user())->findOrFail($request->set_id);

            $participations = DB::table('participations')
                ->where('seller_id', $request->seller_id)
                ->where('set_id', $request->set_id)
                ->whereIn('status', ['asignada', 'vendida', 'disponible'])
                ->select('id', 'participation_number as number', 'participation_code', 'set_id', 'sale_date', 'sale_time', 'updated_at', 'created_at')
                ->orderBy('participation_number')
                ->get();

            return response()->json([
                'success' => true,
                'participations' => $participations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener participaciones: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar asignación de participación
     */
    public function removeAssignment(Request $request)
    {
        $request->validate([
            'participation_id' => 'required|integer|exists:participations,id',
            'seller_id' => 'required|integer|exists:sellers,id'
        ]);

        try {
            DB::beginTransaction();

            if (!auth()->user()->canAccessSeller((int) $request->seller_id)) {
                abort(403, 'No tienes permisos para gestionar este vendedor.');
            }

            // Verificar que la participación pertenece al vendedor
            // USAR MODELO ELOQUENT para que se dispare el Observer
            $participation = Participation::where('id', $request->participation_id)
                ->where('seller_id', $request->seller_id)
                ->whereIn('status', ['asignada', 'disponible'])
                ->first();

            if (!$participation) {
                return response()->json([
                    'success' => false,
                    'message' => 'La participación no pertenece a este vendedor o no está asignada'
                ]);
            }

            // Restaurar la participación a estado disponible
            // USAR update() del modelo para disparar el Observer
            $participation->update([
                'seller_id' => null,
                'sale_date' => null,
                'sale_time' => null,
                'status' => 'disponible'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asignación eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la asignación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener participaciones por taco (book)
     */
    public function getParticipationsByBook(Request $request)
    {
        try {
            $request->validate([
                'seller_id' => 'required|integer',
                'set_id' => 'required|integer',
                'book_number' => 'required|integer'
            ]);

            if (!auth()->user()->canAccessSeller((int) $request->seller_id)) {
                abort(403, 'No tienes permisos para consultar este vendedor.');
            }

            // Obtener información del set
            $set = Set::forUser(auth()->user())->findOrFail($request->set_id);

            if (!$set) {
                return response()->json([
                    'success' => false,
                    'message' => 'Set no encontrado'
                ]);
            }

            // Obtener el formato de diseño para saber cuántas participaciones por taco
            $designFormat = DB::table('design_formats')
                ->where('set_id', $request->set_id)
                ->first();

            if (!$designFormat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de diseño no encontrado'
                ]);
            }

            $participationsPerBook = $designFormat->output['participations_per_book'] ?? 50;
            $startParticipation = ($request->book_number - 1) * $participationsPerBook + 1;
            $endParticipation = $request->book_number * $participationsPerBook;

            // Obtener participaciones del vendedor en este taco
            $participations = DB::table('participations')
                ->where('seller_id', $request->seller_id)
                ->where('set_id', $request->set_id)
                ->where('participation_number', '>=', $startParticipation)
                ->where('participation_number', '<=', $endParticipation)
                ->where('status', 'asignada')
                ->select('id', 'participation_number as number', 'participation_code', 'sale_date', 'sale_time', 'updated_at', 'created_at')
                ->orderBy('participation_number')
                ->get();

            return response()->json([
                'success' => true,
                'participations' => $participations,
                'book_info' => [
                    'book_number' => $request->book_number,
                    'start_participation' => $startParticipation,
                    'end_participation' => $endParticipation,
                    'participations_per_book' => $participationsPerBook,
                    'total_assigned' => $participations->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener participaciones del taco: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener resumen de liquidación para un vendedor y sorteo
     */
    public function getSettlementSummary(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $lotteryId = $request->get('lottery_id');

        if (!auth()->user()->canAccessSeller((int) $sellerId)) {
            abort(403, 'No tienes permisos para consultar este vendedor.');
        }

        if (!auth()->user()->canAccessSeller((int) $sellerId)) {
            abort(403, 'No tienes permisos para consultar este vendedor.');
        }

        \Log::info('=== SELLER SETTLEMENT SUMMARY ===');
        \Log::info('Seller ID:', [$sellerId]);
        \Log::info('Lottery ID:', [$lotteryId]);

        // Obtener todas las participaciones asignadas al vendedor para este sorteo
        $participations = Participation::where('seller_id', $sellerId)
            ->whereHas('set.reserve', function($query) use ($lotteryId) {
                $query->where('lottery_id', $lotteryId);
            })
            ->whereIn('status', ['asignada', 'vendida'])
            ->with('set')
            ->get();

        \Log::info('Participaciones asignadas encontradas:', [$participations->count()]);

        $totalParticipations = $participations->count();
        
        // Calcular el total a liquidar (suma del precio de cada participación)
        $totalAmount = $participations->sum(function($participation) {
            return $participation->set->played_amount ?? 0;
        });

        // Obtener liquidaciones previas para este vendedor y sorteo
        $previousSettlements = SellerSettlement::where('seller_id', $sellerId)
            ->where('lottery_id', $lotteryId)
            ->with('payments')
            ->get();

        $totalPaid = $previousSettlements->sum('paid_amount');
        $pendingAmount = $totalAmount - $totalPaid;

        // Calcular participaciones liquidadas (pagos / precio por participación)
        $pricePerParticipation = $participations->first()->set->played_amount ?? 1;
        $liquidatedParticipations = $pricePerParticipation > 0 ? ($totalPaid / $pricePerParticipation) : 0;

        \Log::info('Resumen calculado:', [
            'total_participations' => $totalParticipations,
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'pending_amount' => $pendingAmount,
            'liquidated_participations' => $liquidatedParticipations
        ]);

        return response()->json([
            'success' => true,
            'summary' => [
                'total_participations' => $totalParticipations,
                'price_per_participation' => $pricePerParticipation,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'pending_amount' => $pendingAmount,
                'liquidated_participations' => round($liquidatedParticipations, 2),
                'pending_participations' => $totalParticipations - round($liquidatedParticipations, 2)
            ]
        ]);
    }

    /**
     * Guardar nueva liquidación de vendedor
     */
    public function storeSettlement(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validate([
                'seller_id' => 'required|exists:sellers,id',
                'lottery_id' => 'required|exists:lotteries,id',
                'pagos' => 'required|array',
                'pagos.*.payment_method' => 'required|string',
                'pagos.*.amount' => 'required|numeric|min:0.01'
            ]);

            if (!auth()->user()->canAccessSeller((int) $data['seller_id'])) {
                abort(403, 'No tienes permisos para gestionar este vendedor.');
            }

            // Calcular totales
            $totalPagoNuevo = collect($data['pagos'])->sum('amount');

            // Obtener participaciones asignadas al vendedor para este sorteo
            $participations = Participation::where('seller_id', $data['seller_id'])
                ->whereHas('set.reserve', function($query) use ($data) {
                    $query->where('lottery_id', $data['lottery_id']);
                })
                ->whereIn('status', ['asignada', 'vendida'])
                ->with('set')
                ->get();

            $totalParticipations = $participations->count();
            $pricePerParticipation = $participations->first()->set->played_amount ?? 0;
            $totalAmount = $participations->sum(function($participation) {
                return $participation->set->played_amount ?? 0;
            });

            // Obtener liquidaciones previas
            $previousSettlements = SellerSettlement::where('seller_id', $data['seller_id'])
                ->where('lottery_id', $data['lottery_id'])
                ->sum('paid_amount');

            $totalPaidWithNew = $previousSettlements + $totalPagoNuevo;
            $pendingAmount = $totalAmount - $totalPaidWithNew;
            $calculatedParticipations = $pricePerParticipation > 0 ? ($totalPagoNuevo / $pricePerParticipation) : 0;

            $now = Carbon::now();

            // Crear registro de liquidación
            $settlement = SellerSettlement::create([
                'seller_id' => $data['seller_id'],
                'lottery_id' => $data['lottery_id'],
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'paid_amount' => $totalPagoNuevo,
                'pending_amount' => $pendingAmount,
                'total_participations' => $totalParticipations,
                'calculated_participations' => round($calculatedParticipations, 2),
                'settlement_date' => $now->format('Y-m-d'),
                'settlement_time' => $now->format('H:i:s'),
                'notes' => 'Liquidación de vendedor'
            ]);

            // Crear registros de pago
            foreach ($data['pagos'] as $pago) {
                SellerSettlementPayment::create([
                    'seller_settlement_id' => $settlement->id,
                    'amount' => $pago['amount'],
                    'payment_method' => $pago['payment_method'],
                    'notes' => 'Pago de liquidación - ' . ucfirst($pago['payment_method']),
                    'payment_date' => $now
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Liquidación registrada correctamente',
                'settlement_id' => $settlement->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la liquidación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de liquidaciones de un vendedor
     */
    public function getSettlementHistory(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $lotteryId = $request->get('lottery_id');

        $settlements = SellerSettlement::where('seller_id', $sellerId)
            ->where('lottery_id', $lotteryId)
            ->with(['payments', 'user'])
            ->orderBy('settlement_date', 'desc')
            ->orderBy('settlement_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'settlements' => $settlements
        ]);
    }

    /**
     * Actualizar grupo de vendedor
     */
    public function updateGroup(Request $request, $id)
    {
        $request->validate([
            'group_name' => 'nullable|string|max:255',
            'group_color' => 'nullable|string|max:7',
            'group_priority' => 'nullable|integer|min:0'
        ]);

        try {
            $seller = Seller::forUser(auth()->user())->findOrFail($id);
            $seller->update([
                'group_name' => $request->group_name,
                'group_color' => $request->group_color,
                'group_priority' => $request->group_priority ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Grupo actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el grupo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener vendedores por grupo
     */
    public function getByGroup(Request $request)
    {
        $groupName = $request->get('group');
        
        if ($groupName) {
            $sellers = Seller::with('entities')
                ->byGroup($groupName)
                ->forUser(auth()->user())
                ->orderByGroup()
                ->get();
        } else {
            $sellers = Seller::with('entities')
                ->forUser(auth()->user())
                ->orderByGroup()
                ->get();
        }

        return response()->json([
            'success' => true,
            'sellers' => $sellers
        ]);
    }

    /**
     * Obtener estadísticas de grupos
     */
    public function getGroupStats()
    {
        $query = Seller::select('group_name', 'group_color')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('group_name')
            ->where('group_name', '!=', '')
            ->groupBy('group_name', 'group_color')
            ->orderBy('count', 'desc');

        if (!auth()->user()->isSuperAdmin()) {
            $sellerIds = auth()->user()->accessibleSellerIds();

            if (empty($sellerIds)) {
                return response()->json([
                    'success' => true,
                    'stats' => collect()
                ]);
            }

            $query->whereIn('id', $sellerIds);
        }

        $stats = $query->get();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
} 