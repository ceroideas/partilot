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
        $lotteryCategories = config('lotteryCategories');
        
        $prizeCategories = collect($lotteryCategories)->map(function($category) {
            return [
                'nombre' => $category['nombre_categoria'],
                'key' => $category['key_categoria']
            ];
        })->values()->toArray();

        return view('lottery_types.add', compact('prizeCategories'));
    }

    /**
     * Guardar nuevo tipo de lotería
     */
    public function store(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'identificador' => 'required|string|max:2',
            'ticket_price' => 'required|numeric|min:0',
            'prize_categories' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = true;

        // Convertir prize_categories de JSON string a array
        if (isset($data['prize_categories'])) {
            $data['prize_categories'] = json_decode($data['prize_categories'], true);
        }

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
        $lotteryCategories = config('lotteryCategories');
        
        $prizeCategories = collect($lotteryCategories)->map(function($category) {
            return [
                'nombre' => $category['nombre_categoria'],
                'key' => $category['key_categoria']
            ];
        })->values()->toArray();

        return view('lottery_types.edit', compact('lotteryType', 'prizeCategories'));
    }

    /**
     * Actualizar tipo de lotería
     */
    public function update(Request $request, LotteryType $lotteryType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'identificador' => 'required|string|max:2',
            'ticket_price' => 'required|numeric|min:0',
            'prize_categories' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Convertir prize_categories de JSON string a array
        if (isset($data['prize_categories'])) {
            $data['prize_categories'] = json_decode($data['prize_categories'], true);
        }

        $lotteryType->update($data);

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
     * Obtener categorías de premios disponibles desde configuración
     */
    public function getAvailablePrizeCategories()
    {
        $lotteryCategories = config('lotteryCategories');
        
        $categories = collect($lotteryCategories)->map(function($category) {
            return [
                'nombre' => $category['nombre_categoria'],
                'key' => $category['key_categoria'],
                'descripcion' => $this->getCategoryDescription($category)
            ];
        })->values()->toArray();

        return response()->json($categories);
    }

    /**
     * Obtener tipos de sorteo disponibles
     */
    public function getAvailableLotteryTypes()
    {
        $lotteryTypes = config('lotteryTypes');
        
        $types = collect($lotteryTypes)->map(function($type, $key) {
            return [
                'identifier' => $key,
                'nombre' => $type['nombre'],
                'precio_decimo' => $type['precio_decimo'],
                'codigo_sorteo' => $type['codigo_sorteo'],
                'descripcion' => $type['descripcion'],
                'es_especial' => $type['es_especial'] ?? false
            ];
        })->values()->toArray();

        return response()->json($types);
    }

    /**
     * Obtener categorías de premios para un tipo específico de sorteo
     */
    public function getPrizeCategoriesForLotteryType($lotteryTypeIdentifier)
    {
        $lotteryCategories = config('lotteryCategories');
        
        $applicableCategories = collect($lotteryCategories)->filter(function($category) use ($lotteryTypeIdentifier) {
            $prizeAmount = $category['importe_por_tipo'][$lotteryTypeIdentifier] ?? 0;
            $prizeCount = is_array($category['cantidad_premios']) 
                ? ($category['cantidad_premios'][$lotteryTypeIdentifier] ?? 0)
                : $category['cantidad_premios'];
            
            return $prizeAmount > 0 && $prizeCount > 0;
        })->map(function($category) use ($lotteryTypeIdentifier) {
            $prizeAmount = $category['importe_por_tipo'][$lotteryTypeIdentifier] ?? 0;
            $prizeCount = is_array($category['cantidad_premios']) 
                ? ($category['cantidad_premios'][$lotteryTypeIdentifier] ?? 0)
                : $category['cantidad_premios'];
            
            return [
                'nombre' => $category['nombre_categoria'],
                'key' => $category['key_categoria'],
                'importe' => $prizeAmount,
                'cantidad' => $prizeCount
            ];
        })->values()->toArray();

        return response()->json($applicableCategories);
    }

    /**
     * Obtener descripción de una categoría
     */
    private function getCategoryDescription($category)
    {
        $tipos = array_filter($category['importe_por_tipo'], function($importe) {
            return $importe > 0;
        });
        
        $tiposTexto = implode(', ', array_keys($tipos));
        
        return "Aplica a: " . $tiposTexto;
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