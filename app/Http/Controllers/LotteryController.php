<?php

namespace App\Http\Controllers;

use App\Models\Lottery;
use App\Models\LotteryType;
use App\Models\Administration;
use App\Models\LotteryResult;
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
            ->orderBy('id', 'desc')
            ->get();

        // return $lotteries;

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

    /**
     * Mostrar lista de administraciones para selección
     */
    public function showAdministrations()
    {
        $administrations = Administration::where('status', 1)->get();
        return view('lottery.administrations', compact('administrations'));
    }

    /**
     * Procesar selección de administración
     */
    public function selectAdministration(Request $request)
    {
        $request->validate([
            'administration_id' => 'required|integer|exists:administrations,id'
        ]);

        // Guardar la administración seleccionada en la sesión con su manager
        $administration = Administration::with('manager')->find($request->administration_id);
        $request->session()->put('selected_administration', $administration);

        return redirect()->route('lottery.results')
            ->with('success', 'Administración seleccionada: ' . $administration->name);
    }

    /**
     * Obtener y guardar resultados de lotería desde la API
     */
    public function fetchAndSaveResults(Request $request)
    {
        try {
            $request->validate([
                'lottery_id' => 'required|integer|exists:lotteries,id',
                'api_url' => 'required|url'
            ]);

            $lottery = Lottery::findOrFail($request->lottery_id);

            // Realizar petición a la API
            $response = Http::timeout(30)->get($request->api_url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener datos de la API: ' . $response->status()
                ], 400);
            }

            $data = $response->json();

            // Verificar si ya existe un resultado para este sorteo
            $existingResult = LotteryResult::where('lottery_id', $lottery->id)->first();

            if ($existingResult) {
                // Actualizar resultado existente
                $this->updateLotteryResult($existingResult, $data);
                $message = 'Resultados actualizados exitosamente';
            } else {
                // Crear nuevo resultado
                $this->createLotteryResult($lottery, $data);
                $message = 'Resultados guardados exitosamente';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'lottery_id' => $lottery->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resultados específicos de un sorteo desde la API
     */
    public function fetchSpecificResults(Request $request)
    {
        try {
            $request->validate([
                'lottery_id' => 'required|integer|exists:lotteries,id',
                'api_url' => 'required|url'
            ]);

            $lottery = Lottery::findOrFail($request->lottery_id);

            // Realizar petición a la API
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ])
            ->timeout(30)
            ->get(html_entity_decode($request->api_url));

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener datos de la API: ' . $response->status()
                ], 400);
            }

            $data = $response->json();

            // return response()->json($data,422);

            // Filtrar por el número de sorteo específico
            $filteredData = null;
            if (is_array($data)) {
                foreach ($data as $sorteo) {
                    // Comparar el num_sorteo del JSON con el name del sorteo (sin el '0' inicial)
                    $jsonNumSorteo = $sorteo['num_sorteo'] ?? '';
                    $lotteryName = ltrim($lottery->name, '0'); // Remover '0' inicial si existe
                    
                    if ($jsonNumSorteo == $lotteryName) {
                        $filteredData = $sorteo;
                        break;
                    }
                }
            }

            if (!$filteredData || !isset($filteredData['primerPremio']['decimo'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para el sorteo número ' . $lottery->name
                ], 404);
            }

            // Verificar si ya existe un resultado para este sorteo
            $existingResult = LotteryResult::where('lottery_id', $lottery->id)->first();

            if ($existingResult) {
                // Actualizar resultado existente
                $this->updateLotteryResult($existingResult, $filteredData);
                $message = 'Resultados actualizados exitosamente';
            } else {
                // Crear nuevo resultado
                $this->createLotteryResult($lottery, $filteredData);
                $message = 'Resultados guardados exitosamente';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'lottery_id' => $lottery->id,
                'data' => $filteredData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo resultado de lotería
     */
    private function createLotteryResult(Lottery $lottery, array $data)
    {
        $resultData = [
            'lottery_id' => $lottery->id,
            'results_date' => now(),
            'is_published' => true
        ];

        // Procesar premio especial
        if (isset($data['premioEspecial']) && is_array($data['premioEspecial'])) {
            $resultData['premio_especial'] = $data['premioEspecial'];
        }

        // Procesar otros premios
        $resultData['primer_premio'] = $data['primerPremio'] ?? null;
        $resultData['segundo_premio'] = $data['segundoPremio'] ?? null;
        $resultData['terceros_premios'] = $data['tercerosPremios'] ?? [];
        $resultData['cuartos_premios'] = $data['cuartosPremios'] ?? [];
        $resultData['quintos_premios'] = $data['quintosPremios'] ?? [];

        // Procesar extracciones
        $resultData['extracciones_cinco_cifras'] = $data['extraccionesDeCincoCifras'] ?? [];
        $resultData['extracciones_cuatro_cifras'] = $data['extraccionesDeCuatroCifras'] ?? [];
        $resultData['extracciones_tres_cifras'] = $data['extraccionesDeTresCifras'] ?? [];
        $resultData['extracciones_dos_cifras'] = $data['extraccionesDeDosCifras'] ?? [];

        // Procesar reintegros
        $resultData['reintegros'] = $data['reintegros'] ?? [];

        LotteryResult::create($resultData);
    }

    /**
     * Actualizar resultado existente de lotería
     */
    private function updateLotteryResult(LotteryResult $result, array $data)
    {
        $updateData = [
            'results_date' => now()
        ];

        // Procesar premio especial
        if (isset($data['premioEspecial']) && is_array($data['premioEspecial'])) {
            $updateData['premio_especial'] = $data['premioEspecial'];
        }

        // Procesar otros premios
        $updateData['primer_premio'] = $data['primerPremio'] ?? null;
        $updateData['segundo_premio'] = $data['segundoPremio'] ?? null;
        $updateData['terceros_premios'] = $data['tercerosPremios'] ?? [];
        $updateData['cuartos_premios'] = $data['cuartosPremios'] ?? [];
        $updateData['quintos_premios'] = $data['quintosPremios'] ?? [];

        // Procesar extracciones
        $updateData['extracciones_cinco_cifras'] = $data['extraccionesDeCincoCifras'] ?? [];
        $updateData['extracciones_cuatro_cifras'] = $data['extraccionesDeCuatroCifras'] ?? [];
        $updateData['extracciones_tres_cifras'] = $data['extraccionesDeTresCifras'] ?? [];
        $updateData['extracciones_dos_cifras'] = $data['extraccionesDeDosCifras'] ?? [];

        // Procesar reintegros
        $updateData['reintegros'] = $data['reintegros'] ?? [];

        $result->update($updateData);
    }

    /**
     * Mostrar resultados de un sorteo específico
     */
    public function showResults(Lottery $lottery)
    {
        $lottery->load(['result', 'lotteryType']);
        
        return view('lottery.show_results', compact('lottery'));
    }

    /**
     * Mostrar tabla de resultados de todos los sorteos
     */
    public function resultsTable()
    {
        $lotteries = Lottery::with(['result', 'lotteryType'])
            ->orderBy('name', 'desc')
            ->get();
        
        return view('lottery.results_table', compact('lotteries'));
    }

    /**
     * Mostrar vista de resultados de lotería (después de seleccionar administración)
     */
    public function showLotteryResults()
    {
        $lotteries = Lottery::with(['result', 'lotteryType'])
            ->orderBy('name', 'asc')
            ->get();
        
        return view('lottery.lottery_results', compact('lotteries'));
    }

    /**
     * Mostrar formulario para editar resultados de un sorteo específico
     */
    public function editLotteryResults($id)
    {
        $lottery = Lottery::with(['result', 'lotteryType'])->findOrFail($id);
        
        // Construir la URL de la API para este sorteo específico
        $apiUrl = "https://www.loteriasyapuestas.es/servicios/buscadorSorteos?game_id=LNAC&celebrados=false&fechaInicioInclusiva=" . $lottery->draw_date->format('Ymd') . "&fechaFinInclusiva=" . $lottery->draw_date->format('Ymd');

        // return $lottery->result;

        return view('lottery.edit_lottery_results', compact('lottery', 'apiUrl'));
    }

    /**
     * Guardar resultados editados manualmente
     */
    public function saveResults(Request $request)
    {
        try {
            $request->validate([
                'lottery_id' => 'required|integer|exists:lotteries,id',
                'premio_especial' => 'nullable|string',
                'primer_premio' => 'nullable|string',
                'primer_premio_serie' => 'nullable|string',
                'primer_premio_fraccion' => 'nullable|string',
                'segundo_premio' => 'nullable|string',
                'reintegros' => 'nullable|string',
                'terceros_premios' => 'nullable|string',
                'cuartos_premios' => 'nullable|string',
                'quintos_premios' => 'nullable|string',
                'extracciones_5_cifras' => 'nullable|string',
                'extracciones_4_cifras' => 'nullable|string',
                'extracciones_3_cifras' => 'nullable|string',
                'extracciones_2_cifras' => 'nullable|string',
                'pedrea' => 'nullable|string'
            ]);

            $lottery = Lottery::findOrFail($request->lottery_id);
            
            // Verificar si ya existe un resultado para este sorteo
            $existingResult = LotteryResult::where('lottery_id', $lottery->id)->first();

            $resultData = [
                'lottery_id' => $lottery->id,
                'results_date' => now(),
                'is_published' => true
            ];

            // Procesar premio especial
            if (!empty($request->premio_especial)) {
                $resultData['premio_especial'] = ['numero' => $request->premio_especial];
            }

            // Procesar primer premio
            if (!empty($request->primer_premio)) {
                $primerPremio = ['decimo' => $request->primer_premio];
                if (!empty($request->primer_premio_serie)) {
                    $primerPremio['serie'] = $request->primer_premio_serie;
                }
                if (!empty($request->primer_premio_fraccion)) {
                    $primerPremio['fraccion'] = $request->primer_premio_fraccion;
                }
                $resultData['primer_premio'] = $primerPremio;
            }

            // Procesar segundo premio
            if (!empty($request->segundo_premio)) {
                $resultData['segundo_premio'] = ['decimo' => $request->segundo_premio];
            }

            // Procesar reintegros
            if (!empty($request->reintegros)) {
                $reintegros = explode('-', $request->reintegros);
                $resultData['reintegros'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $reintegros);
            }

            // Procesar terceros premios
            if (!empty($request->terceros_premios)) {
                $terceros = explode('-', $request->terceros_premios);
                $resultData['terceros_premios'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $terceros);
            }

            // Procesar cuartos premios
            if (!empty($request->cuartos_premios)) {
                $cuartos = explode('-', $request->cuartos_premios);
                $resultData['cuartos_premios'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $cuartos);
            }

            // Procesar quintos premios
            if (!empty($request->quintos_premios)) {
                $quintos = explode('-', $request->quintos_premios);
                $resultData['quintos_premios'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $quintos);
            }

            // Procesar extracciones 5 cifras
            if (!empty($request->extracciones_5_cifras)) {
                $extracciones5 = explode('-', $request->extracciones_5_cifras);
                $resultData['extracciones_cinco_cifras'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $extracciones5);
            }

            // Procesar extracciones 4 cifras
            if (!empty($request->extracciones_4_cifras)) {
                $extracciones4 = explode('-', $request->extracciones_4_cifras);
                $resultData['extracciones_cuatro_cifras'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $extracciones4);
            }

            // Procesar extracciones 3 cifras
            if (!empty($request->extracciones_3_cifras)) {
                $extracciones3 = explode('-', $request->extracciones_3_cifras);
                $resultData['extracciones_tres_cifras'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $extracciones3);
            }

            // Procesar extracciones 2 cifras
            if (!empty($request->extracciones_2_cifras)) {
                $extracciones2 = explode('-', $request->extracciones_2_cifras);
                $resultData['extracciones_dos_cifras'] = array_map(function($numero) {
                    return ['decimo' => trim($numero)];
                }, $extracciones2);
            }

            if ($existingResult) {
                // Actualizar resultado existente
                $existingResult->update($resultData);
                $message = 'Resultados actualizados exitosamente';
            } else {
                // Crear nuevo resultado
                LotteryResult::create($resultData);
                $message = 'Resultados guardados exitosamente';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results_date' => now()->format('d/m/Y H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
} 