<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Entity;
use App\Models\Participation;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerSettlementPayment;

class ParticipationController extends Controller
{
    /**
     * Mostrar lista de participaciones
     */
    public function index()
    {
        $entities = Entity::with(['administration', 'manager'])
            ->forUser(auth()->user())
            ->orderBy('created_at', 'desc')
            ->get(); // Mostrar solo entidades accesibles
        
        return view('participations.index', compact('entities'));
    }

    /**
     * Mostrar formulario para buscar participaciones - Paso 1: Seleccionar entidad
     */
    public function create()
    {
        // Si no hay entidad seleccionada en sesión, redirigir al index
        $entityId = session('selected_entity_id');

        if (!$entityId || !auth()->user()->canAccessEntity((int) $entityId)) {
            return redirect()->route('participations.index');
        }
        
        $entity = Entity::with(['administration', 'manager'])
            ->forUser(auth()->user())
            ->findOrFail($entityId);
        session(['selected_entity' => $entity]);
        
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
     * Guardar selección de entidad y mostrar formulario de búsqueda - Paso 2
     */
    public function store_entity(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|integer|exists:entities,id'
        ]);

        $entity = Entity::with(['administration', 'manager'])
            ->forUser(auth()->user())
            ->findOrFail($request->entity_id);
        $request->session()->put('selected_entity', $entity);
        $request->session()->put('selected_entity_id', $entity->id);

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
        ])
        ->forUser(auth()->user())
        ->findOrFail($id);
        
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
        ])
        ->forUser(auth()->user())
        ->findOrFail($id);
        
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
        $participation = Participation::forUser(auth()->user())->findOrFail($id);
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
        $set = \App\Models\Set::forUser(auth()->user())->findOrFail($set_id);
        
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

    /**
     * API: Marcar participaciones como vendidas (modo manual)
     * Solo para vendedores autenticados. Las participaciones deben estar asignadas al vendedor.
     */
    public function apiSellManual(Request $request)
    {
        $request->validate([
            'set_id' => 'required|integer|exists:sets,id',
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|min:1',
            'payment_method' => 'nullable|string|in:efectivo,bizum,transferencia,omitir,otro',
        ]);

        $user = $request->user();
        if (!$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        $seller = Seller::where('user_id', $user->id)->where('status', Seller::STATUS_ACTIVE)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Vendedor no encontrado o inactivo.'], 403);
        }

        if ($request->desde > $request->hasta) {
            return response()->json(['success' => false, 'message' => 'El rango desde no puede ser mayor que hasta.'], 422);
        }

        try {
            $participations = Participation::where('set_id', $request->set_id)
                ->whereBetween('participation_number', [$request->desde, $request->hasta])
                ->where('seller_id', $seller->id)
                ->where('status', 'asignada')
                ->get();

            if ($participations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay participaciones asignadas a ti en ese rango o ya están vendidas.'
                ], 422);
            }

            // Verificar que todas las participaciones del rango estén asignadas al vendedor
            $totalEnRango = $request->hasta - $request->desde + 1;
            if ($participations->count() < $totalEnRango) {
                $noAsignadas = $totalEnRango - $participations->count();
                return response()->json([
                    'success' => false,
                    'message' => "Hay {$noAsignadas} participaciones en el rango que no están asignadas a ti o están anuladas."
                ], 422);
            }

            $set = $participations->first()->set()->with('reserve')->first();
            $pricePerParticipation = (float) ($set->played_amount ?? 0);
            $saleAmount = $participations->count() * $pricePerParticipation;

            DB::beginTransaction();
            foreach ($participations as $participation) {
                $participation->markAsSold($seller->id, $pricePerParticipation);
            }
            if ($this->shouldCreateSettlement($request->payment_method)) {
                $this->createSellerSettlementFromSale($seller, $participations, $set, $saleAmount, $request->payment_method, $user->id);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se marcaron {$participations->count()} participaciones como vendidas.",
                'count' => $participations->count()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Solo crear seller_settlement cuando hay método de pago (efectivo, bizum, transferencia).
     * Si es omitir o null, no se registra.
     */
    private function shouldCreateSettlement($paymentMethod): bool
    {
        return in_array($paymentMethod, ['efectivo', 'bizum', 'transferencia'], true);
    }

    /**
     * Crear registro en seller_settlements por venta desde la app.
     * Misma lógica que el backoffice: total_amount, paid_amount, pending_amount, total_participations.
     */
    private function createSellerSettlementFromSale($seller, $participations, $set, $saleAmount, $paymentMethod, $userId)
    {
        $lotteryId = $set->reserve->lottery_id ?? null;
        if (!$lotteryId) {
            return;
        }

        $paymentMethod = in_array($paymentMethod, ['efectivo', 'bizum', 'transferencia'], true) ? $paymentMethod : 'otro';
        $pricePerParticipation = (float) ($set->played_amount ?? 0);
        $now = now();

        // Obtener TODAS las participaciones (asignada+vendida) del vendedor para este sorteo
        $allParticipations = Participation::where('seller_id', $seller->id)
            ->whereHas('set.reserve', fn ($q) => $q->where('lottery_id', $lotteryId))
            ->whereIn('status', ['asignada', 'vendida'])
            ->with('set')
            ->get();

        $totalParticipations = $allParticipations->count();
        $totalAmount = $allParticipations->sum(fn ($p) => (float) ($p->set->played_amount ?? 0));

        // Obtener lo ya pagado en liquidaciones previas
        $previousPaid = SellerSettlement::where('seller_id', $seller->id)
            ->where('lottery_id', $lotteryId)
            ->sum('paid_amount');

        $totalPaidWithNew = $previousPaid + $saleAmount;
        $pendingAmount = $totalAmount - $totalPaidWithNew;
        $calculatedParticipations = $pricePerParticipation > 0 ? round($saleAmount / $pricePerParticipation, 2) : 0;

        $settlement = SellerSettlement::create([
            'seller_id' => $seller->id,
            'lottery_id' => $lotteryId,
            'user_id' => $userId,
            'total_amount' => $totalAmount,
            'paid_amount' => $saleAmount,
            'pending_amount' => $pendingAmount,
            'total_participations' => $totalParticipations,
            'calculated_participations' => $calculatedParticipations,
            'settlement_date' => $now->format('Y-m-d'),
            'settlement_time' => $now->format('H:i:s'),
            'notes' => 'Venta registrada desde app'
        ]);

        SellerSettlementPayment::create([
            'seller_settlement_id' => $settlement->id,
            'amount' => $saleAmount,
            'payment_method' => $paymentMethod,
            'notes' => 'Venta - ' . ucfirst($paymentMethod),
            'payment_date' => $now
        ]);
    }

    /**
     * API: Marcar participación como vendida por escaneo QR
     * La referencia proviene del código QR de la participación física.
     */
    public function apiSellByQr(Request $request)
    {
        $request->validate([
            'referencia' => 'required|string',
            'desde' => 'nullable|integer|min:1',
            'hasta' => 'nullable|integer|min:1',
            'payment_method' => 'nullable|string|in:efectivo,bizum,transferencia,omitir,otro',
        ]);

        $user = $request->user();
        if (!$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
        }

        $seller = Seller::where('user_id', $user->id)->where('status', Seller::STATUS_ACTIVE)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Vendedor no encontrado o inactivo.'], 403);
        }

        // Buscar set y participación por referencia (contenido del QR)
        $set = \App\Models\Set::whereNotNull('tickets')->get()->first(function ($s) use ($request) {
            if (!is_array($s->tickets)) {
                return false;
            }
            foreach ($s->tickets as $ticket) {
                if (isset($ticket['r']) && $ticket['r'] == $request->referencia) {
                    return true;
                }
            }
            return false;
        });

        if (!$set) {
            return response()->json([
                'success' => false,
                'message' => 'Referencia no encontrada. Verifica que el código QR sea correcto.'
            ], 404);
        }

        // Si se proporciona rango desde/hasta, marcar el rango
        if ($request->filled('desde') && $request->filled('hasta')) {
            if ($request->desde > $request->hasta) {
                return response()->json(['success' => false, 'message' => 'El rango desde no puede ser mayor que hasta.'], 422);
            }

            $participations = Participation::where('set_id', $set->id)
                ->whereBetween('participation_number', [$request->desde, $request->hasta])
                ->where('seller_id', $seller->id)
                ->where('status', 'asignada')
                ->get();

            if ($participations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay participaciones asignadas a ti en ese rango.'
                ], 422);
            }

            try {
                $set = $participations->first()->set()->with('reserve')->first();
                $pricePerParticipation = (float) ($set->played_amount ?? 0);
                $saleAmount = $participations->count() * $pricePerParticipation;

                DB::beginTransaction();
                foreach ($participations as $participation) {
                    $participation->markAsSold($seller->id, $pricePerParticipation);
                }
                if ($this->shouldCreateSettlement($request->payment_method)) {
                    $this->createSellerSettlementFromSale($seller, $participations, $set, $saleAmount, $request->payment_method, $user->id);
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Se marcaron {$participations->count()} participaciones como vendidas.",
                    'count' => $participations->count()
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
        }

        // Marcar solo la participación escaneada
        $participationNumber = null;
        foreach ($set->tickets as $ticket) {
            if (isset($ticket['r']) && $ticket['r'] == $request->referencia) {
                $participationNumber = $ticket['n'];
                break;
            }
        }

        if (!$participationNumber) {
            return response()->json(['success' => false, 'message' => 'Referencia no encontrada en el set.'], 404);
        }

        $participation = Participation::where('set_id', $set->id)
            ->where('participation_number', $participationNumber)
            ->where('seller_id', $seller->id)
            ->where('status', 'asignada')
            ->first();

        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'Esta participación no está asignada a ti o ya está vendida.'
            ], 422);
        }

        try {
            $set = $participation->set()->with('reserve')->first();
            $pricePerParticipation = (float) ($set->played_amount ?? 0);
            $saleAmount = $pricePerParticipation;

            DB::beginTransaction();
            $participation->markAsSold($seller->id, $pricePerParticipation);
            if ($this->shouldCreateSettlement($request->payment_method)) {
                $this->createSellerSettlementFromSale($seller, collect([$participation]), $set, $saleAmount, $request->payment_method, $user->id);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Participación marcada como vendida.',
                'participation' => [
                    'id' => $participation->id,
                    'participation_code' => $participation->participation_code,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Historial de ventas del vendedor autenticado (para app móvil).
     * Devuelve las participaciones vendidas por el vendedor en formato listado para el historial.
     */
    public function apiGetMySales(Request $request)
    {
        $user = $request->user();
        if (!$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para esta acción.'], 403);
        }

        $seller = Seller::where('user_id', $user->id)->where('status', Seller::STATUS_ACTIVE)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Vendedor no encontrado.'], 403);
        }

        $participations = Participation::where('seller_id', $seller->id)
            ->where('status', 'vendida')
            ->with(['set.entity', 'set.reserve.lottery'])
            ->orderBy('sale_date', 'desc')
            ->orderBy('sale_time', 'desc')
            ->limit(200)
            ->get();

        $historial = $participations->map(function ($p) {
            $set = $p->set;
            if (!$set) {
                return null;
            }
            $entity = $set->entity ?? null;
            $reserve = $set->reserve ?? null;
            $lottery = $reserve ? $reserve->lottery : null;
            $entidadNombre = $entity ? $entity->name : '—';
            $fechaSorteo = $lottery && $lottery->draw_date
                ? $lottery->draw_date->format('d/m/y')
                : '—';
            $importeJugado = (float) ($set->played_amount ?? 0);
            $donativo = (float) ($set->donation_amount ?? 0);
            $importeTotal = (float) ($p->sale_amount ?? $importeJugado + $donativo);
            $saleDateTime = $p->sale_date
                ? $p->sale_date->format('Y-m-d') . 'T' . ($p->sale_time ? (is_object($p->sale_time) ? $p->sale_time->format('H:i:s') : substr((string) $p->sale_time, 0, 8)) : '00:00:00') . '.000000Z'
                : $p->updated_at->toIso8601String();

            return [
                'id' => $p->id,
                'tipo' => 'venta',
                'fecha' => $saleDateTime,
                'formaPago' => null,
                'descripcion' => 'Participación ' . $entidadNombre,
                'participacion' => [
                    'entidad' => $entidadNombre,
                    'numero' => $p->participation_code ?? (string) $p->participation_number,
                    'fechaSorteo' => $fechaSorteo,
                    'importeJugado' => $importeJugado,
                    'donativo' => $donativo > 0 ? $donativo : null,
                    'importeTotal' => $importeTotal,
                    'numeroParticipacion' => $p->participation_code ?? $p->participation_number . '/' . str_pad($p->participation_number, 4, '0', STR_PAD_LEFT),
                    'numeroReferencia' => $p->participation_code ?? str_pad((string) $p->id, 19, '0', STR_PAD_LEFT),
                    'imagen' => null,
                ],
            ];
        })->filter()->values()->all();

        return response()->json([
            'success' => true,
            'historial' => $historial,
        ]);
    }
}
