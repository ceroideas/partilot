<?php

namespace App\Http\Controllers;

use App\Models\Lottery;
use App\Models\LotteryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class LotteryController extends Controller
{
    /**
     * Mostrar lista de sorteos
     */
    public function index()
    {
        $lotteries = Lottery::with(['lotteryType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('lottery.index', compact('lotteries'));
    }

    /**
     * Mostrar formulario para crear sorteo
     */
    public function create()
    {
        $lotteryTypes = LotteryType::where('is_active', true)->get();

        return view('lottery.add', compact('lotteryTypes'));
    }

    /**
     * Guardar nuevo sorteo
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date' => 'required|date|after:today',
            'draw_time' => 'required',
            'deadline_date' => 'nullable|date|after:today',
            'ticket_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lottery_type_id' => 'required|integer',
            // 'total_tickets' => 'required|integer|min:1',
            // 'prize_description' => 'required|string',
            // 'prize_value' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['sold_tickets'] = 0;
        $data['status'] = 1; // 1 = active

        // Manejar la imagen si se subió
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads'), $imageName);
            $data['image'] = $imageName;
        }

        Lottery::create($data);

        return redirect()->route('lotteries.index')
            ->with('success', 'Sorteo creado exitosamente');
    }

    /**
     * Mostrar sorteo específico
     */
    public function show(Lottery $lottery)
    {
        $lottery->load(['lotteryType']);
        
        return view('lottery.show', compact('lottery'));
    }

    /**
     * Mostrar formulario para editar sorteo
     */
    public function edit(Lottery $lottery)
    {
        $lotteryTypes = LotteryType::where('is_active', true)->get();

        return view('lottery.edit', compact('lottery', 'lotteryTypes'));
    }

    /**
     * Actualizar sorteo
     */
    public function update(Request $request, Lottery $lottery)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date' => 'required|date',
            'draw_time' => 'required',
            'deadline_date' => 'nullable|date',
            'ticket_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lottery_type_id' => 'required|integer',
            'status' => 'nullable|integer|in:1,2,3,4', // 1=active, 2=inactive, 3=completed, 4=cancelled
            // 'total_tickets' => 'required|integer|min:1',
            // 'prize_description' => 'required|string',
            // 'prize_value' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Manejar la imagen si se subió
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($lottery->image && File::exists(public_path('uploads/' . $lottery->image))) {
                File::delete(public_path('uploads/' . $lottery->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads'), $imageName);
            $data['image'] = $imageName;
        }

        $lottery->update($data);

        return redirect()->route('lotteries.index')
            ->with('success', 'Sorteo actualizado exitosamente');
    }

    /**
     * Eliminar sorteo
     */
    public function destroy(Lottery $lottery)
    {
        // Eliminar imagen si existe
        if ($lottery->image && File::exists(public_path('uploads/' . $lottery->image))) {
            File::delete(public_path('uploads/' . $lottery->image));
        }

        $lottery->delete();

        return redirect()->route('lotteries.index')
            ->with('success', 'Sorteo eliminado exitosamente');
    }

    /**
     * Cambiar estado del sorteo
     */
    public function changeStatus(Request $request, Lottery $lottery)
    {
        $request->validate([
            'status' => 'required|integer|in:1,2,3,4' // 1=active, 2=inactive, 3=completed, 4=cancelled
        ]);

        $lottery->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Estado del sorteo actualizado');
    }

    /**
     * Eliminar imagen del sorteo
     */
    public function deleteImage(Lottery $lottery)
    {
        if ($lottery->image && File::exists(public_path('uploads/' . $lottery->image))) {
            // Eliminar archivo físico
            File::delete(public_path('uploads/' . $lottery->image));
            
            // Actualizar base de datos
            $lottery->update(['image' => null]);
            
            return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente']);
        }
        
        return response()->json(['success' => false, 'message' => 'No hay imagen para eliminar']);
    }
} 