<?php

namespace App\Http\Controllers;

use App\Models\Lottery;
use App\Models\LotteryType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

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

    /**
     * Generar sorteos basado en un rango de fechas
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Convertir fechas al formato requerido por la API (YYYYMMDD)
            $dateFrom = date('Ymd', strtotime($request->date_from));
            $dateTo = date('Ymd', strtotime($request->date_to));

            // Construir URL de la API
            $apiUrl = "https://www.loteriasyapuestas.es/servicios/buscadorSorteos?game_id=LNAC&celebrados=false&fechaInicioInclusiva={$dateFrom}&fechaFinInclusiva={$dateTo}";

            // Realizar petición HTTP usando Guzzle
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ])
            ->timeout(30)
            ->get($apiUrl);
            
            if (!$response->successful()) {
                throw new \Exception('Error HTTP: ' . $response->status() . ' - Respuesta: ' . $response->body());
            }
            
            $responseBody = $response->body();

            // Log para debug
            \Log::info('API Request', [
                'url' => $apiUrl,
                'http_code' => $response->status(),
                'response_length' => strlen($responseBody)
            ]);

            $data = json_decode($responseBody, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error al decodificar JSON de la API');
            }

            // Log para debug
            \Log::info('API Response for dates ' . $dateFrom . ' to ' . $dateTo, [
                'data_count' => is_array($data) ? count($data) : 'not array',
                'first_item' => is_array($data) && count($data) > 0 ? $data[0] : 'no items',
                'data_structure' => $data
            ]);

            $createdCount = 0;
            $updatedCount = 0;

            // Procesar cada sorteo del JSON (el JSON es un array directo)
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $sorteo) {
                    // Validar que el sorteo tenga los datos mínimos necesarios
                    if (!isset($sorteo['fecha_sorteo']) || !isset($sorteo['id_sorteo'])) {
                        continue; // Saltar si no tiene fecha o ID de sorteo
                    }

                    // Extraer fecha y hora del sorteo
                    $fechaSorteo = $sorteo['fecha_sorteo'];
                    $horaSorteo = null;
                    
                    // Extraer hora de la fecha_sorteo si contiene hora
                    if (strpos($fechaSorteo, ' ') !== false) {
                        $fechaHora = explode(' ', $fechaSorteo);
                        $fechaSorteo = $fechaHora[0];
                        $horaSorteo = $fechaHora[1];
                    }

                    // Convertir fecha y hora a formato datetime
                    $drawDateTime = null;
                    if ($horaSorteo) {
                        $drawDateTime = $fechaSorteo . ' ' . $horaSorteo;
                    } else {
                        $drawDateTime = $fechaSorteo . ' 00:00:00';
                    }

                    // Preparar datos del sorteo
                    $lotteryData = [
                        'name' => '0' . ($sorteo['num_sorteo'] ?? ''),
                        'description' => $sorteo['nombre'] ?? '', // Usar día de la semana como descripción
                        'draw_date' => $fechaSorteo,
                        'deadline_date' => $fechaSorteo,
                        'draw_time' => $horaSorteo ? $horaSorteo : '00:00:00',
                        'draw_time' => $horaSorteo ? $horaSorteo : '00:00:00',
                        'ticket_price' => $sorteo['precioDecimo'], // Por defecto 0 ya que no veo precio en el JSON
                        'status' => 1,
                        'sold_tickets' => 0,
                    ];

                    // Buscar si ya existe un sorteo para esta fecha o con el mismo nombre
                    $existingLottery = Lottery::where('draw_date', $fechaSorteo)
                        ->orWhere('name', $lotteryData['name'])
                        ->first();

                    // Buscar tipo de sorteo por identificador (usar game_id si está disponible)
                    if (isset($sorteo['game_id'])) {
                        $lotteryType = LotteryType::where('identificador', $sorteo['tipoSorteo'])->first();
                        if ($lotteryType) {
                            $lotteryData['lottery_type_id'] = $lotteryType->id;
                        }else{
                            $lotteryData['lottery_type_id'] = 0;
                        }
                    }

                    if ($existingLottery) {
                        // Actualizar sorteo existente
                        $existingLottery->update($lotteryData);
                        $updatedCount++;
                    } else {
                        // Crear nuevo sorteo
                        Lottery::create($lotteryData);
                        $createdCount++;
                    }
                }
            } else {
                return redirect()->route('lotteries.index')
                    ->with('warning', 'No se encontraron sorteos en el rango de fechas especificado.');
            }

            $message = "Proceso completado. Sorteos creados: {$createdCount}, Sorteos actualizados: {$updatedCount}";
            return redirect()->route('lotteries.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar sorteos: ' . $e->getMessage())
                ->withInput();
        }
    }
} 