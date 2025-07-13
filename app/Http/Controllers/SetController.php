<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Entity;
use App\Models\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SetController extends Controller
{
    /**
     * Mostrar lista de sets
     */
    public function index()
    {
        $sets = Set::with(['entity', 'reserve'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sets.index', compact('sets'));
    }

    /**
     * Mostrar formulario para crear set - Paso 1: Seleccionar entidad
     */
    public function create()
    {
        $entities = Entity::with(['administration', 'manager'])->get();
        return view('sets.add', compact('entities'));
    }

    /**
     * Guardar selección de entidad y mostrar formulario de reserva - Paso 2
     */
    public function store_entity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $entity = Entity::with(['administration', 'manager'])->find($request->entity_id);
        $request->session()->put('selected_entity', $entity);

        // Obtener reservas activas de la entidad
        $reserves = Reserve::where('entity_id', $entity->id)
            ->where('status', 1) // confirmed
            ->with(['lottery'])
            ->get();

        return view('sets.add_reserve', compact('reserves'));
    }

    /**
     * Guardar selección de entidad via AJAX
     */
    public function store_entity_ajax(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $entity = Entity::with(['administration', 'manager'])->find($request->entity_id);
        $request->session()->put('selected_entity', $entity);

        return response()->json(['success' => true]);
    }

    /**
     * Mostrar formulario para seleccionar reserva - Paso 2
     */
    public function add_reserve()
    {
        $entity = session('selected_entity');
        
        if (!$entity) {
            return redirect()->route('sets.create')
                ->with('error', 'Error: No se encontró la entidad seleccionada');
        }

        // Obtener reservas activas de la entidad
        $reserves = Reserve::where('entity_id', $entity->id)
            ->where('status', 1) // confirmed
            ->with(['lottery'])
            ->get();

        return view('sets.add_reserve', compact('reserves'));
    }

    /**
     * Guardar selección de reserva y mostrar formulario de configuración - Paso 3
     */
    public function store_reserve(Request $request)
    {
        $request->validate([
            'reserve_id' => 'required|integer|exists:reserves,id'
        ]);

        $reserve = Reserve::with(['lottery', 'entity'])->find($request->reserve_id);
        $request->session()->put('selected_reserve', $reserve);

        // Obtener también la entidad de la sesión
        $entity = session('selected_entity');
        
        if (!$entity || !$reserve) {
            return redirect()->route('sets.create')
                ->with('error', 'Error: No se encontraron los datos de entidad o reserva');
        }

        return view('sets.add_information', compact('entity', 'reserve'));
    }

    /**
     * Guardar selección de reserva via AJAX
     */
    public function store_reserve_ajax(Request $request)
    {
        $request->validate([
            'reserve_id' => 'required|integer|exists:reserves,id'
        ]);

        $reserve = Reserve::with(['lottery', 'entity'])->find($request->reserve_id);
        $request->session()->put('selected_reserve', $reserve);

        return response()->json(['success' => true]);
    }

    /**
     * Mostrar formulario para configurar set - Paso 3
     */
    public function add_information()
    {
        $entity = session('selected_entity');
        $reserve = session('selected_reserve');
        
        if (!$entity || !$reserve) {
            return redirect()->route('sets.create')
                ->with('error', 'Error: No se encontraron los datos de entidad o reserva. Por favor, selecciona una entidad y reserva.');
        }

        // Cargar las relaciones necesarias si no están cargadas
        if (!$reserve->relationLoaded('lottery')) {
            $reserve->load('lottery');
        }
        if (!$entity->relationLoaded('administration')) {
            $entity->load('administration');
        }

        return view('sets.add_information', compact('entity', 'reserve'));
    }

    /**
     * Guardar set completo - Paso final
     */
    public function store_information(Request $request)
    {
        $validated = $request->validate([
            'set_name' => 'required|string|max:255',
            'played_amount' => 'nullable|numeric|min:0',
            'donation_amount' => 'nullable|numeric|min:0',
            'total_participation_amount' => 'nullable|numeric|min:0',
            'total_participations' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'physical_participations' => 'nullable|integer|min:0',
            'digital_participations' => 'nullable|integer|min:0',
            'deadline_date' => 'nullable|date'
        ]);

        // Obtener datos de sesión
        $entity = $request->session()->get('selected_entity');
        $reserve = $request->session()->get('selected_reserve');

        if (!$entity || !$reserve) {
            return redirect()->route('sets.create')
                ->with('error', 'Error: No se encontraron los datos de entidad o reserva');
        }

        // Crear set
        $setData = array_merge($validated, [
            'entity_id' => $entity->id,
            'reserve_id' => $reserve->id,
            'status' => 1, // Activo por defecto
            'created_at' => now()
        ]);

        Set::create($setData);

        // Limpiar sesión
        $request->session()->forget(['selected_entity', 'selected_reserve']);

        return redirect()->route('sets.index')
            ->with('success', 'Set creado exitosamente');
    }

    /**
     * Mostrar set específico
     */
    public function show(Set $set)
    {
        $set->load(['entity', 'reserve']);
        return view('sets.show', compact('set'));
    }

    /**
     * Mostrar formulario para editar set
     */
    public function edit(Set $set)
    {
        $entities = Entity::all();
        $reserves = Reserve::all();
        return view('sets.edit', compact('set', 'entities', 'reserves'));
    }

    /**
     * Actualizar set
     */
    public function update(Request $request, Set $set)
    {
        $validated = $request->validate([
            'entity_id' => 'required|integer|exists:entities,id',
            'reserve_id' => 'required|integer|exists:reserves,id',
            'set_name' => 'required|string|max:255',
            'set_description' => 'nullable|string|max:1000',
            'total_participations' => 'required|integer|min:1',
            'participation_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:0,1,2'
        ]);

        $set->update($validated);

        return redirect()->route('sets.index')
            ->with('success', 'Set actualizado exitosamente');
    }

    /**
     * Eliminar set
     */
    public function destroy(Set $set)
    {
        $set->delete();

        return redirect()->route('sets.index')
            ->with('success', 'Set eliminado exitosamente');
    }

    /**
     * Cambiar estado del set
     */
    public function changeStatus(Request $request, Set $set)
    {
        $request->validate([
            'status' => 'required|in:0,1,2'
        ]);

        $set->update(['status' => (int)$request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del set actualizado exitosamente'
        ]);
    }

    /**
     * Obtener reservas por entidad
     */
    public function getReservesByEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $reserves = Reserve::where('entity_id', $request->entity_id)
            ->where('status', 1) // confirmed
            ->with(['lottery'])
            ->get();

        return response()->json($reserves);
    }
} 