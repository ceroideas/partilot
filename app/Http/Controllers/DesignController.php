<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Lottery;
use App\Models\Set;

class DesignController extends Controller
{
    // Paso 1: Seleccionar entidad
    public function selectEntity()
    {
        $entities = Entity::all();
        return view('design.add', compact('entities'));
    }

    // Paso 2: Seleccionar sorteo
    public function selectLottery($entity_id = null)
    {
        if (!$entity_id) {
            $entity_id = session('design_entity_id');
        }
        $entity = Entity::findOrFail($entity_id);
        // Mostrar solo sorteos cuya fecha sea distinta a la actual
        $today = date('Y-m-d');
        $lotteries = \App\Models\Lottery::whereDate('deadline_date', '!=', $today)->get();
        return view('design.add_lottery', compact('entity', 'lotteries'));
    }

    // Paso 3: Seleccionar set
    public function selectSet($entity_id = null, $lottery_id = null)
    {
        // Recuperar entity_id de la sesión si no viene por parámetro
        if (!$entity_id) {
            $entity_id = session('design_entity_id');
        }
        // Recuperar lottery_id del request si no viene por parámetro
        if (!$lottery_id) {
            $lottery_id = request('lottery_id');
        }
        $entity = \App\Models\Entity::findOrFail($entity_id);
        $lottery = \App\Models\Lottery::findOrFail($lottery_id);
        // Buscar la reserva correspondiente
        $reserve = \App\Models\Reserve::where('entity_id', $entity_id)->where('lottery_id', $lottery_id)->first();
        $sets = [];
        if ($reserve) {
            $sets = \App\Models\Set::where('reserve_id', $reserve->id)->get();
        }
        return view('design.add_set', compact('entity', 'lottery', 'sets', 'reserve'));
    }

    // Paso 4: Mostrar formato final
    public function format($entity_id, $lottery_id, $set_id)
    {
        $entity = Entity::findOrFail($entity_id);
        $lottery = Lottery::findOrFail($lottery_id);
        $set = Set::findOrFail($set_id);
        return view('design.format', compact('entity', 'lottery', 'set'));
    }

    // Guardar selección de entidad en sesión y redirigir a selección de sorteo
    public function storeEntity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);
        $entity_id = $request->entity_id;
        session(['design_entity_id' => $entity_id]);
        return redirect()->route('design.selectLottery');
    }
} 