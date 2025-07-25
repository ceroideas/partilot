<?php

namespace App\Http\Controllers;

use App\Models\Reserve;
use App\Models\Entity;
use App\Models\Lottery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReserveController extends Controller
{
    /**
     * Mostrar lista de reservas
     */
    public function index()
    {
        $reserves = Reserve::with(['entity', 'lottery'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reserves.index', compact('reserves'));
    }

    /**
     * Mostrar formulario para crear reserva - Paso 1: Seleccionar entidad
     */
    public function create()
    {
        $entities = Entity::with(['administration', 'manager'])->get();
        return view('reserves.add', compact('entities'));
    }

    /**
     * Guardar selección de entidad y mostrar formulario de sorteo - Paso 2
     */
    public function store_entity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $entity = Entity::with(['administration', 'manager'])->find($request->entity_id);
        $request->session()->put('selected_entity', $entity);

        // Obtener sorteos activos
        $lotteries = Lottery::where('status', 1)
            ->with(['lotteryType'])
            ->get();

        return view('reserves.add_lottery', compact('lotteries'));
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
     * Guardar selección de sorteo y mostrar formulario de datos - Paso 3
     */
    public function store_lottery(Request $request)
    {
        $request->validate([
            'lottery_id' => 'required|integer|exists:lotteries,id'
        ]);

        $lottery = Lottery::with(['lotteryType'])->find($request->lottery_id);
        $request->session()->put('selected_lottery', $lottery);

        return view('reserves.add_information');
    }

    /**
     * Guardar selección de sorteo via AJAX
     */
    public function store_lottery_ajax(Request $request)
    {
        $request->validate([
            'lottery_id' => 'required|integer|exists:lotteries,id'
        ]);

        $lottery = Lottery::with(['lotteryType'])->find($request->lottery_id);
        $request->session()->put('selected_lottery', $lottery);

        return response()->json(['success' => true]);
    }

    /**
     * Guardar reserva completa - Paso final
     */
    public function store_information(Request $request)
    {
        $validated = $request->validate([
            'reservation_numbers' => 'required|array|min:1',
            'reservation_numbers.*' => 'required|string|max:10',
            'reservation_amount' => 'required|numeric|min:0',
            'reservation_tickets' => 'required|integer|min:1'
        ]);

        // Obtener datos de sesión
        $entity = $request->session()->get('selected_entity');
        $lottery = $request->session()->get('selected_lottery');

        if (!$entity || !$lottery) {
            return redirect()->route('reserves.create')
                ->with('error', 'Error: No se encontraron los datos de entidad o sorteo');
        }

        // Calcular total
        $totalTickets = count($validated['reservation_numbers']);
        $totalAmount = $totalTickets * $lottery->ticket_price;

        // Crear reserva
        $reserveData = array_merge($validated, [
            'entity_id' => $entity->id,
            'lottery_id' => $lottery->id,
            'total_amount' => $totalAmount,
            'total_tickets' => $totalTickets,
            'status' => 1, // pending
            'reservation_date' => now(),
            'expiration_date' => now()->addDays(7) // 7 días por defecto
        ]);

        Reserve::create($reserveData);

        // Limpiar sesión
        $request->session()->forget(['selected_entity', 'selected_lottery']);

        return redirect()->route('reserves.index')
            ->with('success', 'Reserva creada exitosamente');
    }

    /**
     * Mostrar reserva específica
     */
    public function show(Reserve $reserve)
    {
        $reserve->load(['entity', 'lottery']);
        return view('reserves.show', compact('reserve'));
    }

    /**
     * Mostrar formulario para editar reserva
     */
    public function edit(Reserve $reserve)
    {
        $entities = Entity::all();
        $lotteries = Lottery::all();
        return view('reserves.edit', compact('reserve', 'entities', 'lotteries'));
    }

    /**
     * Actualizar reserva
     */
    public function update(Request $request, Reserve $reserve)
    {
        $validated = $request->validate([
            'reservation_numbers' => 'required|array|min:1',
            'reservation_numbers.*' => 'required|string|max:10',
            'reservation_amount' => 'required|numeric|min:0',
            'reservation_tickets' => 'required|integer|min:1'
        ]);

        $reserve->update($validated);

        return redirect()->route('reserves.show',$reserve->id)
            ->with('success', 'Reserva actualizada exitosamente');
    }

    /**
     * Eliminar reserva
     */
    public function destroy(Reserve $reserve)
    {
        $reserve->delete();

        return redirect()->route('reserves.index')
            ->with('success', 'Reserva eliminada exitosamente');
    }

    /**
     * Cambiar estado de la reserva
     */
    public function changeStatus(Request $request, Reserve $reserve)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3'
        ]);

        $reserve->update(['status' => (int)$request->status]);

        return redirect()->back()
            ->with('success', 'Estado de la reserva actualizado');
    }

    /**
     * Obtener sorteos disponibles para una entidad
     */
    public function getLotteriesByEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $lotteries = Lottery::where('status', 1) // Solo sorteos activos
            ->with(['lotteryType'])
            ->get();

        return response()->json($lotteries);
    }
} 