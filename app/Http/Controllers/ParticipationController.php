<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Participation;

class ParticipationController extends Controller
{
    /**
     * Mostrar lista de participaciones
     */
    public function index()
    {
        $entities = Entity::with(['administration', 'manager'])
            ->get(); // Mostrar todas las entidades independientemente del status
        
        return view('participations.index', compact('entities'));
    }

    /**
     * Mostrar formulario para buscar participaciones - Paso 1: Seleccionar entidad
     */
    public function create()
    {
        // Si no hay entidad seleccionada en sesión, redirigir al index
        if (!session('selected_entity')) {
            return redirect()->route('participations.index');
        }
        
        $entity = session('selected_entity');
        
        // Obtener los design_formats de la entidad seleccionada
        $designFormats = \App\Models\DesignFormat::where('entity_id', $entity->id)
            ->with(['set.reserve.lottery', 'set.reserve.lottery.lotteryType'])
            ->get();
        
        // Procesar cada designFormat para calcular los tacos
        foreach ($designFormats as $designFormat) {
            $this->calculateBooks($designFormat);
        }
        
        return view('participations.add', compact('designFormats'));
    }

    /**
     * Guardar selección de entidad y mostrar formulario de búsqueda - Paso 2
     */
    public function store_entity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $entity = Entity::with(['administration', 'manager'])->find($request->entity_id);
        $request->session()->put('selected_entity', $entity);

        // Obtener los design_formats de la entidad seleccionada
        $designFormats = \App\Models\DesignFormat::where('entity_id', $entity->id)
            ->with(['set.reserve.lottery', 'set.reserve.lottery.lotteryType'])
            ->get();

        // Procesar cada designFormat para calcular los tacos
        foreach ($designFormats as $designFormat) {
            $this->calculateBooks($designFormat);
        }

        return view('participations.add', compact('entity', 'designFormats'));
    }

    /**
     * Mostrar participación específica por ID con todos los datos relacionados
     */
    public function view($id)
    {
        $participation = Participation::with([
            'set.reserve.lottery.lotteryType',
            'set.reserve.entity.administration',
            'seller.user',
            'designFormat'
        ])->findOrFail($id);
        
        return view('participations.view', compact('participation'));
    }

    /**
     * Mostrar participación específica
     */
    public function show($id)
    {
        $participation = Participation::with([
            'set.reserve.lottery.lotteryType',
            'set.reserve.entity.administration',
            'seller.user',
            'designFormat'
        ])->findOrFail($id);
        
        // Buscar la referencia del ticket en el set
        $ticketReference = null;
        if ($participation->set && $participation->set->tickets) {
            $tickets = is_string($participation->set->tickets) ? json_decode($participation->set->tickets, true) : $participation->set->tickets;
            
            if (is_array($tickets)) {
                foreach ($tickets as $ticket) {
                    if (isset($ticket['n']) && $ticket['n'] == $participation->participation_number) {
                        $ticketReference = $ticket['r'] ?? null;
                        break;
                    }
                }
            }
        }
        
        return view('participations.show', compact('participation', 'ticketReference'));
    }

    /**
     * Mostrar participación para vendedor
     */
    public function show_seller($id)
    {
        $participation = Participation::findOrFail($id);
        return view('participations.show_seller', compact('participation'));
    }

    /**
     * Calcular los tacos (books) para un designFormat
     */
    private function calculateBooks($designFormat)
    {
        if (!$designFormat->set) {
            $designFormat->books = [];
            return;
        }

        // Obtener el número de participaciones por taco desde el JSON
        $output = is_string($designFormat->output) ? json_decode($designFormat->output, true) : $designFormat->output;
        $participationsPerBook = $output['participations_per_book'] ?? 50;
        
        // Obtener el total de participaciones del set
        $totalParticipations = $designFormat->set->total_participations ?? 0;
        
        // Calcular cuántos tacos necesitamos
        $totalBooks = ceil($totalParticipations / $participationsPerBook);
        
        // Obtener el número de set (por fecha de creación)
        $setNumber = $this->getSetNumber($designFormat->set);
        
        $books = [];
        for ($i = 1; $i <= $totalBooks; $i++) {
            $startParticipation = (($i - 1) * $participationsPerBook) + 1;
            $endParticipation = min($i * $participationsPerBook, $totalParticipations);
            
            // Calcular estadísticas reales del taco
            $bookParticipations = \App\Models\Participation::where('set_id', $designFormat->set->id)
                ->whereBetween('participation_number', [$startParticipation, $endParticipation])
                ->get();

            $salesRegistered = $bookParticipations->where('status', 'vendida')->count();
            $returnedParticipations = $bookParticipations->where('status', 'devuelta')->count();
            $availableParticipations = $bookParticipations->where('status', 'disponible')->count();
            
            // Determinar el estado del taco
            $status = 'Disponible';
            if ($salesRegistered > 0 && $availableParticipations == 0) {
                $status = 'Vendido';
            } elseif ($salesRegistered > 0 && $availableParticipations > 0) {
                $status = 'Parcial';
            } elseif ($returnedParticipations > 0) {
                $status = 'Con Devoluciones';
            }

            // Obtener el vendedor principal (el que más ha vendido en este taco)
            $mainSeller = $bookParticipations->where('status', 'vendida')
                ->groupBy('seller_id')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first();

            $sellerName = 'Sin asignar';
            if ($mainSeller) {
                $seller = \App\Models\Seller::with('user')->find($mainSeller);
                $sellerName = $seller ? $seller->user->name : 'Sin asignar';
            }

            $books[] = [
                'book_number' => $i,
                'set_number' => $setNumber,
                'start_participation' => $startParticipation,
                'end_participation' => $endParticipation,
                'total_participations' => $endParticipation - $startParticipation + 1,
                'participations_range' => sprintf('%d/%05d - %d/%05d', $setNumber, $startParticipation, $setNumber, $endParticipation),
                'sales_registered' => $salesRegistered,
                'returned_participations' => $returnedParticipations,
                'available_participations' => $availableParticipations,
                'status' => $status,
                'seller' => $sellerName,
            ];
        }
        
        $designFormat->books = $books;
    }

    /**
     * Obtener el número de set basado en la fecha de creación
     */
    private function getSetNumber($set)
    {
        // Contar cuántos sets hay para la misma reserva, ordenados por fecha de creación
        $setNumber = \App\Models\Set::where('reserve_id', $set->reserve_id)
            ->where('created_at', '<=', $set->created_at)
            ->count();
        
        return $setNumber;
    }

    /**
     * Obtener las participaciones de un taco específico
     */
    public function getBookParticipations($set_id, $book_number)
    {
        $set = \App\Models\Set::findOrFail($set_id);
        
        // Obtener el designFormat asociado
        $designFormat = \App\Models\DesignFormat::where('set_id', $set_id)->first();
        
        if (!$designFormat) {
            return response()->json(['error' => 'Diseño no encontrado'], 404);
        }
        
        // Calcular los tacos para obtener el rango de participaciones
        $this->calculateBooks($designFormat);
        
        // Encontrar el taco específico
        $book = null;
        foreach ($designFormat->books as $b) {
            if ($b['book_number'] == $book_number) {
                $book = $b;
                break;
            }
        }
        
        if (!$book) {
            return response()->json(['error' => 'Taco no encontrado'], 404);
        }
        
        // Obtener las participaciones del rango específico
        $participations = \App\Models\Participation::where('set_id', $set_id)
            ->whereBetween('participation_number', [$book['start_participation'], $book['end_participation']])
            ->with(['seller.user'])
            ->get();
        
                 // Formatear las participaciones para la vista
         $formattedParticipations = [];
         foreach ($participations as $participation) {
             $formattedParticipations[] = [
                 'id' => $participation->id,
                 'participation_number' => $participation->participation_code,
                 'status' => $participation->status_text,
                 'seller' => $participation->seller ? $participation->seller->user->name : 'Sin asignar',
                 'sale_date' => $participation->sale_date ? $participation->sale_date->format('d/m/Y') : '-',
                 'sale_time' => $participation->sale_time ? $participation->sale_time->format('H:i') . 'h' : '-',
             ];
         }
        
        return response()->json([
            'book' => $book,
            'participations' => $formattedParticipations
        ]);
    }
}
