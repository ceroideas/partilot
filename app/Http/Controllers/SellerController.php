<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use App\Models\Entity;
use App\Models\Reserve;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sellers = Seller::with('entity')->get();
        return view('sellers.index', compact('sellers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entities = Entity::with('administration')->get();
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

        $entity = Entity::with('administration')->findOrFail($request->entity_id);
        session(['selected_entity' => $entity]);

        return redirect()->route('sellers.add-information');
    }

    /**
     * Show the add information form
     */
    public function add_information()
    {
        if (!session('selected_entity')) {
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
            'email' => 'required|email|exists:users,email',
            'entity_id' => 'required|exists:entities,id'
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->first();
            $entity = Entity::findOrFail($request->entity_id);

            // Crear el vendedor con los datos del usuario existente
            $seller = Seller::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'last_name' => $user->last_name,
                'last_name2' => $user->last_name2,
                'nif_cif' => $user->nif_cif,
                'birthday' => $user->birthday,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => 1, // Activo por defecto
                'entity_id' => $entity->id
            ]);

            DB::commit();

            session()->forget('selected_entity');
            return redirect()->route('sellers.index')->with('success', 'Vendedor creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el vendedor: ' . $e->getMessage()]);
        }
    }

    /**
     * Store a seller with new user
     */
    public function store_new_user(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'entity_id' => 'required|exists:entities,id'
        ]);

        try {
            DB::beginTransaction();

            $entity = Entity::findOrFail($request->entity_id);

            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('password123'), // Contraseña temporal
            ]);

            // Crear el vendedor
            $seller = Seller::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'last_name2' => $request->last_name2,
                'nif_cif' => $request->nif_cif,
                'birthday' => $request->birthday,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 1, // Activo por defecto
                'entity_id' => $entity->id
            ]);

            DB::commit();

            session()->forget('selected_entity');
            return redirect()->route('sellers.index')->with('success', 'Vendedor creado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
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
    public function show($id)
    {
        $seller = Seller::with(['entity.administration'])->findOrFail($id);

        $reserves = Reserve::where('entity_id', $seller->entity->id)
            ->where('status', 1) // confirmed
            ->with(['lottery'])
            ->get();

        return view('sellers.show', compact('seller','reserves'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $seller = Seller::findOrFail($id);
        return view('sellers.edit', compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'last_name2' => 'nullable|string|max:255',
            'nif_cif' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'email' => 'required|email|unique:users,email,' . ($seller->user_id ?? 0),
            'phone' => 'nullable|string|max:255'
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
                'phone' => $request->phone
            ]);

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
                        'phone' => $request->phone
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
            $seller = Seller::findOrFail($id);
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

        // Obtener solo sets que tienen participaciones creadas (diseño)
        $sets = Set::where('reserve_id', $request->reserve_id)
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
            // Obtener el set
            $set = Set::findOrFail($request->set_id);
            
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
            $participations = DB::table('participations')
                ->where('set_id', $request->set_id)
                ->whereBetween('participation_number', [$request->desde, $request->hasta])
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
            'participations' => 'required|array|min:1',
            'participations.*.id' => 'required|integer|exists:participations,id',
            'participations.*.number' => 'required|string',
            'participations.*.set_id' => 'required|integer|exists:sets,id',
            'seller_id' => 'required|integer|exists:sellers,id'
        ]);

        try {
            DB::beginTransaction();

            $seller = Seller::findOrFail($request->seller_id);
            $assignedCount = 0;

            foreach ($request->participations as $participationData) {
                // Verificar que la participación esté disponible o ya asignada al vendedor actual
                $participation = DB::table('participations')
                    ->where('id', $participationData['id'])
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
                    DB::table('participations')
                        ->where('id', $participationData['id'])
                        ->update([
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
            $participations = DB::table('participations')
                ->where('seller_id', $request->seller_id)
                ->where('set_id', $request->set_id)
                ->whereIn('status', ['asignada', 'vendida', 'disponible'])
                ->select('id', 'participation_number as number', 'participation_code', 'set_id', 'sale_date', 'sale_time')
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

            // Verificar que la participación pertenece al vendedor
            $participation = DB::table('participations')
                ->where('id', $request->participation_id)
                ->where('seller_id', $request->seller_id)
                ->where('status', 'asignada')
                ->first();

            if (!$participation) {
                return response()->json([
                    'success' => false,
                    'message' => 'La participación no pertenece a este vendedor o no está asignada'
                ]);
            }

            // Restaurar la participación a estado disponible
            DB::table('participations')
                ->where('id', $request->participation_id)
                ->update([
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

            // Obtener información del set
            $set = DB::table('sets')
                ->where('id', $request->set_id)
                ->first();

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
                ->select('id', 'participation_number as number', 'participation_code', 'sale_date', 'sale_time')
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
} 