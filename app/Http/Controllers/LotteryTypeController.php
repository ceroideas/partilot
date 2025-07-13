<?php

namespace App\Http\Controllers;

use App\Models\LotteryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LotteryTypeController extends Controller
{
    /**
     * Mostrar lista de tipos de lotería
     */
    public function index()
    {
        $lotteryTypes = LotteryType::orderBy('created_at', 'desc')->paginate(10);
        return view('lottery_types.index', compact('lotteryTypes'));
    }

    /**
     * Mostrar formulario para crear tipo de lotería
     */
    public function create()
    {
        return view('lottery_types.add');
    }

    /**
     * Guardar nuevo tipo de lotería
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ticket_price' => 'required|numeric|min:0',
            /*'prize_categories' => 'required|array|min:1',
            'prize_categories.*' => 'required|string|max:255',*/
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = true;

        LotteryType::create($data);

        return redirect()->route('lottery-types.index')
            ->with('success', 'Tipo de lotería creado exitosamente');
    }

    /**
     * Mostrar tipo de lotería específico
     */
    public function show(LotteryType $lotteryType)
    {
        return view('lottery_types.show', compact('lotteryType'));
    }

    /**
     * Mostrar formulario para editar tipo de lotería
     */
    public function edit(LotteryType $lotteryType)
    {
        return view('lottery_types.edit', compact('lotteryType'));
    }

    /**
     * Actualizar tipo de lotería
     */
    public function update(Request $request, LotteryType $lotteryType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ticket_price' => 'required|numeric|min:0',
            'prize_categories' => 'required|array|min:1',
            'prize_categories.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lotteryType->update($request->all());

        return redirect()->route('lottery-types.index')
            ->with('success', 'Tipo de lotería actualizado exitosamente');
    }

    /**
     * Eliminar tipo de lotería
     */
    public function destroy(LotteryType $lotteryType)
    {
        // Verificar si hay loterías asociadas
        if ($lotteryType->lotteries()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar el tipo de lotería porque tiene sorteos asociados');
        }

        $lotteryType->delete();

        return redirect()->route('lottery-types.index')
            ->with('success', 'Tipo de lotería eliminado exitosamente');
    }

    /**
     * Obtener categorías de premios disponibles (hardcodeadas)
     */
    public function getAvailablePrizeCategories()
    {
        $categories = [
            'Primer Premio',
            'Segundo Premio', 
            'Tercer Premio',
            'Cuarto Premio',
            'Quinto Premio'
        ];

        return response()->json($categories);
    }

    /**
     * Cambiar estado del tipo de lotería
     */
    public function changeStatus(Request $request, LotteryType $lotteryType)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $lotteryType->update(['is_active' => $request->is_active]);

        return redirect()->back()
            ->with('success', 'Estado del tipo de lotería actualizado');
    }
} 