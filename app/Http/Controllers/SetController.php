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
            ->get()
             ->sortByDesc(function ($reserve) {
                return $reserve->lottery->draw_date ?? now(); // fallback si null
            });;

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
            ->orderBy('lottery.draw_date','desc')
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

        // return view('sets.add_information', compact('entity', 'reserve'));
        return redirect('sets/add/information');
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

        // Calcular importe disponible para la reserva
        $usedAmount = Set::where('reserve_id', $reserve->id)->sum('total_amount');
        $availableAmount = $reserve->reservation_amount - $usedAmount;
        if ($availableAmount < 0) $availableAmount = 0;

        // Cargar las relaciones necesarias si no están cargadas
        if (!$reserve->relationLoaded('lottery')) {
            $reserve->load('lottery');
        }
        if (!$entity->relationLoaded('administration')) {
            $entity->load('administration');
        }

        return view('sets.add_information', compact('entity', 'reserve', 'availableAmount'));
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

        // Calcular importe disponible
        $usedAmount = Set::where('reserve_id', $reserve->id)->sum('total_amount');
        $availableAmount = $reserve->reservation_amount - $usedAmount;
        if ($availableAmount < 0) $availableAmount = 0;
        if ($validated['total_amount'] > $availableAmount) {
            // Volver a la vista con el valor correcto de $availableAmount
            return back()->withInput()->withErrors(['reservation_amount' => 'El importe total supera el disponible para esta reserva (máx: ' . number_format($availableAmount,2) . ' €)'])
                ->with(['availableAmount' => $availableAmount, 'entity' => $entity, 'reserve' => $reserve]);
        }
        $createdAt = now();
        $tickets = \App\Models\Set::generateTickets($entity->id, $reserve->id, $createdAt, $validated['total_participations']);
        $setData = array_merge($validated, [
            'entity_id' => $entity->id,
            'reserve_id' => $reserve->id,
            'status' => 1, // Activo por defecto
            'created_at' => $createdAt,
            'tickets' => $tickets
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
        // Calcular importe disponible para la reserva, sumando el importe del set actual
        $usedAmount = Set::where('reserve_id', $set->reserve_id)
            ->where('id', '!=', $set->id)
            ->sum('total_amount');
        $availableAmount = $set->reserve->reservation_amount - $usedAmount + $set->total_amount;
        if ($availableAmount < 0) $availableAmount = 0;
        return view('sets.edit', compact('set', 'entities', 'reserves', 'availableAmount'));
    }

    /**
     * Actualizar set
     */
    public function update(Request $request, Set $set)
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

        // Calcular importe disponible para la reserva, sumando el importe del set actual
        $usedAmount = Set::where('reserve_id', $set->reserve_id)
            ->where('id', '!=', $set->id)
            ->sum('total_amount');
        $availableAmount = $set->reserve->reservation_amount - $usedAmount + $set->total_amount;
        if ($availableAmount < 0) $availableAmount = 0;
        if ($validated['total_amount'] > $availableAmount) {
            return back()->withInput()->withErrors(['total_amount' => 'El importe total supera el disponible para esta reserva (máx: ' . number_format($availableAmount,2) . ' €)']);
        }

        // Regenerar tickets manteniendo los existentes
        $tickets = \App\Models\Set::generateTickets($set->entity_id, $set->reserve_id, $set->created_at, $validated['total_participations'], $set->tickets ?? []);

        $set->update(array_merge($validated, [
            'tickets' => $tickets
        ]));

        return redirect()->route('sets.show', $set->id)
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
     * Descargar archivo XML con formato parts.xml
     */
    public function downloadXml(Set $set)
    {
        // Cargar las relaciones necesarias
        $set->load(['entity.administration', 'reserve.lottery', 'reserve']);

        // Obtener datos necesarios
        $entity = $set->entity;
        $administration = $entity->administration;
        $reserve = $set->reserve;
        $lottery = $reserve->lottery;

        // Crear el contenido XML
        $xmlContent = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xmlContent .= '<set>' . "\n";
        $xmlContent .= '  <titulo><![CDATA[' . $entity->name . ']]></titulo>' . "\n";
        $xmlContent .= '  <precio>' . number_format($set->played_amount, 2) . '</precio>' . "\n";
        $xmlContent .= '  <donativo>' . number_format($set->donation_amount, 2) . '</donativo>' . "\n";
        $xmlContent .= '  <fechasorteo>' . $lottery->draw_date->format('d/m/Y') . '</fechasorteo>' . "\n";

        // Agregar números de reserva
        if ($reserve->reservation_numbers && count($reserve->reservation_numbers) > 0) {
            $xmlContent .= '  <numeros>';
            foreach ($reserve->reservation_numbers as $number) {
                $xmlContent .= '<numero><![CDATA[' . $number . ']]></numero>';
            }
            $xmlContent .= '<importe>' . number_format($reserve->total_amount, 2) . '</importe></numeros>' . "\n";
        } else {
            $xmlContent .= '  <numeros><importe>' . number_format($reserve->total_amount, 2) . '</importe></numeros>' . "\n";
        }

        $xmlContent .= '  <urlweb><![CDATA[' . ($administration->web ?? '') . ']]></urlweb>' . "\n";
        $xmlContent .= '  <pagoweb>si</pagoweb>' . "\n";
        $xmlContent .= '  <pagowebpage><![CDATA[loteria-empresas-parti.php?ref=]]></pagowebpage>' . "\n";
        $xmlContent .= '  <participaciones>' . "\n";

        // Generar participaciones
        for ($i = 1; $i <= $set->total_participations; $i++) {
            $xmlContent .= '   <p><s>' . $i . '</s><r>REF' . str_pad($i, 6, '0', STR_PAD_LEFT) . '</r></p>' . "\n";
        }

        $xmlContent .= '  </participaciones>' . "\n";
        $xmlContent .= '</set>';

        // Generar nombre del archivo
        $entityName = str_replace(' ', '_', $entity->name);
        $setName = str_replace(' ', '_', $set->set_name);
        $lotteryName = str_replace(' ', '_', $lottery->name);
        $drawDate = str_replace('/', '-', $lottery->draw_date->format('d-m-Y'));
        
        $filename = $entityName . '_' . $setName . '_' . $lotteryName . '_' . $drawDate . '.xml';

        // Retornar respuesta de descarga
        return response($xmlContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
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

    /**
     * Importar participaciones desde un archivo XML y guardarlas en la columna tickets
     */
    public function importXml(Request $request, $id)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
        ]);

        $set = Set::with('reserve.lottery')->findOrFail($id);

        // Leer el archivo XML
        $xml = simplexml_load_file($request->file('xml_file')->getPathname());

        // Extraer los números de la reserva desde el XML
        $numerosReservaXml = [];
        if (isset($xml->numeros->numero)) {
            foreach ($xml->numeros->numero as $numero) {
                $numerosReservaXml[] = (string)$numero;
            }
        }

        // Números de la reserva en la base de datos
        $numerosReservaDB = is_array($set->reserve->reservation_numbers) ? $set->reserve->reservation_numbers : [];

        // Validar que ambos arrays sean iguales (sin importar el orden)
        if (count($numerosReservaXml) !== count($numerosReservaDB) || array_diff($numerosReservaXml, $numerosReservaDB)) {
            return back()->withErrors(['error' => 'Los números de la reserva en el XML no coinciden con los de la base de datos.']);
        }

        // Extraer participaciones del XML
        $participaciones = [];
        if (isset($xml->participaciones)) {
            foreach ($xml->participaciones->p as $p) {
                $participaciones[] = [
                    'n' => (string)($p->s ?? ''),
                    'r' => (string)($p->r ?? ''),
                ];
            }
        }

        // Validar cantidad de participaciones
        if (count($participaciones) != $set->total_participations) {
            return back()->withErrors(['error' => 'La cantidad de participaciones no coincide con el total del set.']);
        }

        // Guardar las participaciones en la columna tickets
        $set->tickets = $participaciones;
        $set->save();

        return redirect()->route('sets.edit', $set->id)->with('success', 'XML importado correctamente.');
    }
}