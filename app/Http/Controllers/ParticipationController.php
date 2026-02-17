<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Entity;
use App\Models\Participation;
use App\Models\Seller;
use App\Models\SellerSettlement;
use App\Models\SellerSettlementPayment;
use App\Models\ParticipationGift;
use App\Models\ParticipationCollection;
use App\Models\ParticipationCollectionItem;
use App\Models\ParticipationDonation;
use App\Models\ParticipationDonationItem;
use App\Models\User;
use App\Http\Controllers\ApiController;

class ParticipationController extends Controller
{
    /**
     * Calcula resumen de estados para un set.
     * Garantiza coherencia: vendidas + devueltas + anuladas + disponibles = total_configurado.
     * Cualquier estado no contemplado se considera "disponible" a efectos de suma.
     */
    private function getSetStatusSummary(int $setId, int $totalConfigured): array
    {
        $counts = \App\Models\Participation::where('set_id', $setId)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        $sold = (int) ($counts['vendida'] ?? 0);
        $returned = (int) ($counts['devuelta'] ?? 0);
        $cancelled = (int) ($counts['anulada'] ?? 0);

        $knownSum = $sold + $returned + $cancelled;
        // Si por datos inconsistentes knownSum supera el configurado, ampliamos el total para no dar negativos.
        $total = max($totalConfigured, $knownSum);
        $available = max(0, $total - $knownSum);

        return [
            'total' => $total,
            'sold' => $sold,
            'returned' => $returned,
            'cancelled' => $cancelled,
            'available' => $available,
        ];
    }
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
            ->with(['set.reserve.lottery', 'set.reserve.lottery.lotteryType', 'set.entity:id,name,image'])
            ->get();
        
        // Procesar cada designFormat para calcular los tacos
        foreach ($designFormats as $designFormat) {
            $this->calculateBooks($designFormat);
            if ($designFormat->set) {
                $totalConfigured = (int) ($designFormat->set->total_participations ?? 0);
                $designFormat->set_stats = $this->getSetStatusSummary((int) $designFormat->set->id, $totalConfigured);
            } else {
                $designFormat->set_stats = ['total' => 0, 'sold' => 0, 'returned' => 0, 'cancelled' => 0, 'available' => 0];
            }
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

        // Obtener los design_formats de la entidad seleccionada (con entity e image para mostrar imagen en listado)
        $designFormats = \App\Models\DesignFormat::where('entity_id', $entity->id)
            ->with(['set.reserve.lottery', 'set.reserve.lottery.lotteryType', 'set.entity:id,name,image'])
            ->get();

        // Procesar cada designFormat para calcular los tacos
        foreach ($designFormats as $designFormat) {
            $this->calculateBooks($designFormat);
            if ($designFormat->set) {
                $totalConfigured = (int) ($designFormat->set->total_participations ?? 0);
                $designFormat->set_stats = $this->getSetStatusSummary((int) $designFormat->set->id, $totalConfigured);
            } else {
                $designFormat->set_stats = ['total' => 0, 'sold' => 0, 'returned' => 0, 'cancelled' => 0, 'available' => 0];
            }
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
            $expectedTotalInBook = (int) min(
                $participationsPerBook,
                max(0, $totalParticipations - (($i - 1) * $participationsPerBook))
            );

            // Estadísticas del taco por book_number (participation_number es global en BD)
            $stats = \App\Models\Participation::where('set_id', $designFormat->set->id)
                ->where('book_number', $i)
                ->selectRaw('COUNT(*) as total_db')
                ->selectRaw("SUM(CASE WHEN status = 'vendida' THEN 1 ELSE 0 END) as sold")
                ->selectRaw("SUM(CASE WHEN status = 'devuelta' THEN 1 ELSE 0 END) as returned")
                ->selectRaw("SUM(CASE WHEN status = 'anulada' THEN 1 ELSE 0 END) as cancelled")
                ->selectRaw('MIN(participation_number) as min_number')
                ->selectRaw('MAX(participation_number) as max_number')
                ->first();

            $salesRegistered = (int) ($stats->sold ?? 0);
            $returnedParticipations = (int) ($stats->returned ?? 0);
            $cancelledParticipations = (int) ($stats->cancelled ?? 0);
            $knownSum = $salesRegistered + $returnedParticipations + $cancelledParticipations;
            $availableParticipations = max(0, $expectedTotalInBook - $knownSum);
            
            // Determinar el estado del taco
            $status = 'Disponible';
            if ($returnedParticipations > 0) {
                $status = 'Con Devoluciones';
            } elseif ($salesRegistered > 0 && $availableParticipations == 0) {
                $status = 'Vendido';
            } elseif ($salesRegistered > 0 && $availableParticipations > 0) {
                $status = 'Parcial';
            }

            // Obtener el vendedor principal (el que más ha vendido en este taco)
            $mainSeller = \App\Models\Participation::where('set_id', $designFormat->set->id)
                ->where('book_number', $i)
                ->where('status', 'vendida')
                ->whereNotNull('seller_id')
                ->select('seller_id', DB::raw('COUNT(*) as c'))
                ->groupBy('seller_id')
                ->orderByDesc('c')
                ->value('seller_id');

            $sellerName = 'Sin asignar';
            if ($mainSeller) {
                $seller = \App\Models\Seller::with('user')->find($mainSeller);
                $sellerName = $seller ? $seller->user->name : 'Sin asignar';
            }

            $minNum = (int) ($stats->min_number ?? 0);
            $maxNum = (int) ($stats->max_number ?? 0);
            $rangeText = ($minNum > 0 && $maxNum > 0)
                ? sprintf('%d/%05d - %d/%05d', $setNumber, $minNum, $setNumber, $maxNum)
                : '-';

            $books[] = [
                'book_number' => $i,
                'set_number' => $setNumber,
                'start_participation' => $minNum ?: null,
                'end_participation' => $maxNum ?: null,
                'total_participations' => $expectedTotalInBook,
                'participations_range' => $rangeText,
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
        
        // Obtener las participaciones del taco por book_number (participation_number es global)
        $participations = \App\Models\Participation::where('set_id', $set_id)
            ->where('book_number', $book_number)
            ->with(['seller.user'])
            ->orderBy('participation_number')
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
     * API: Digitalizar participación escaneando QR (solo obtener información, no vender)
     * La referencia proviene del código QR de la participación física.
     */
    public function apiDigitalize(Request $request)
    {
        $request->validate([
            'referencia' => 'required|string',
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

        // Obtener número de participación desde el ticket
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

        // Buscar la participación asignada al vendedor
        $participation = Participation::where('set_id', $set->id)
            ->where('participation_number', $participationNumber)
            ->where('seller_id', $seller->id)
            ->where('status', 'asignada')
            ->with(['set.reserve.lottery.lotteryType', 'set.entity', 'set.designFormats'])
            ->first();

        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'Esta participación no está asignada a ti o ya está vendida.'
            ], 422);
        }

        // Obtener información de la participación
        $set = $participation->set;
        $reserve = $set->reserve ?? null;
        $lottery = $reserve ? $reserve->lottery : null;
        $entity = $set->entity ?? null;
        $designFormat = $set->designFormats->first();

        // Obtener snapshot_path del design format
        $snapshotPath = null;
        if ($designFormat && $designFormat->snapshot_path) {
            $snapshotPath = asset('storage/' . $designFormat->snapshot_path);
        }

        // Obtener número reservado de la lotería
        $numeroReservado = '—';
        if ($reserve && $reserve->reservation_numbers) {
            $reservationNumbers = is_array($reserve->reservation_numbers) 
                ? $reserve->reservation_numbers 
                : json_decode($reserve->reservation_numbers, true);
            if (is_array($reservationNumbers) && count($reservationNumbers) > 0) {
                if (count($reservationNumbers) === 1) {
                    $numeroReservado = (string) $reservationNumbers[0];
                } else {
                    $index = $participation->participation_number - 1;
                    if (isset($reservationNumbers[$index])) {
                        $numeroReservado = (string) $reservationNumbers[$index];
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Participación digitalizada correctamente.',
            'participation' => [
                'id' => $participation->id,
                'participation_code' => $participation->participation_code,
                'numero' => $participation->participation_number,
                'referencia' => $request->referencia,
                'entity_name' => $entity ? $entity->name : '—',
                'entidad' => $entity ? $entity->name : '—',
                'draw_date' => $lottery && $lottery->draw_date ? $lottery->draw_date->format('Y-m-d') : null,
                'fechaSorteo' => $lottery && $lottery->draw_date ? $lottery->draw_date->format('d/m/y') : '—',
                'played_amount' => (float) ($set->played_amount ?? 0),
                'importeJugado' => (float) ($set->played_amount ?? 0),
                'donation_amount' => (float) ($set->donation_amount ?? 0),
                'donativo' => (float) ($set->donation_amount ?? 0),
                'amount' => (float) (($set->played_amount ?? 0) + ($set->donation_amount ?? 0)),
                'importeTotal' => (float) (($set->played_amount ?? 0) + ($set->donation_amount ?? 0)),
                'numeroReservado' => $numeroReservado,
                'image' => $snapshotPath,
                'snapshot_path' => $snapshotPath,
                'set' => [
                    'id' => $set->id,
                    'reserve' => $reserve ? [
                        'entity' => $entity ? [
                            'name' => $entity->name
                        ] : null
                    ] : null
                ]
            ]
        ]);
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
            ->with(['set.entity', 'set.reserve.lottery', 'set.designFormats'])
            ->orderBy('sale_date', 'desc')
            ->orderBy('sale_time', 'desc')
            ->limit(200)
            ->get();

        $historial = $participations->map(function ($p) use ($seller) {
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
            $importeTotal = round($importeJugado + $donativo, 2);
            $saleDateTime = $p->sale_date
                ? $p->sale_date->format('Y-m-d') . 'T' . ($p->sale_time ? (is_object($p->sale_time) ? $p->sale_time->format('H:i:s') : substr((string) $p->sale_time, 0, 8)) : '00:00:00') . '.000000Z'
                : $p->updated_at->toIso8601String();

            // Obtener snapshot_path del design format
            $snapshotPath = null;
            $designFormat = $set->designFormats->first();
            if ($designFormat && $designFormat->snapshot_path) {
                $snapshotPath = asset('storage/' . $designFormat->snapshot_path);
            }

            // Obtener número reservado de la lotería
            $numeroReservado = '—';
            if ($reserve && $reserve->reservation_numbers) {
                $reservationNumbers = is_array($reserve->reservation_numbers) 
                    ? $reserve->reservation_numbers 
                    : json_decode($reserve->reservation_numbers, true);
                if (is_array($reservationNumbers)) {
                    // Si solo hay un número reservado, todas las participaciones del set tienen ese número
                    if (count($reservationNumbers) === 1) {
                        $numeroReservado = (string) $reservationNumbers[0];
                    } else {
                        // Si hay múltiples números, usar el índice correspondiente
                        $index = $p->participation_number - 1;
                        if (isset($reservationNumbers[$index])) {
                            $numeroReservado = (string) $reservationNumbers[$index];
                        }
                    }
                }
            }

            // Obtener número de referencia desde set.tickets
            $numeroReferencia = null;
            if ($set->tickets) {
                $tickets = is_array($set->tickets) ? $set->tickets : json_decode($set->tickets, true);
                if (is_array($tickets)) {
                    foreach ($tickets as $ticket) {
                        if (isset($ticket['n']) && $ticket['n'] == $p->participation_number) {
                            $numeroReferencia = $ticket['r'] ?? null;
                            break;
                        }
                    }
                }
            }

            // Obtener método de pago desde settlement (si existe)
            $formaPago = 'efectivo'; // Por defecto
            $lotteryId = $lottery ? $lottery->id : null;
            if ($lotteryId) {
                $settlement = \App\Models\SellerSettlement::where('seller_id', $seller->id)
                    ->where('lottery_id', $lotteryId)
                    ->whereDate('settlement_date', $p->sale_date ?? now())
                    ->with('payments')
                    ->first();
                if ($settlement && $settlement->payments->isNotEmpty()) {
                    $formaPago = $settlement->payments->first()->payment_method ?? 'efectivo';
                }
            }

            return [
                'id' => $p->id,
                'tipo' => 'venta',
                'fecha' => $saleDateTime,
                'formaPago' => $formaPago,
                'descripcion' => 'Participación ' . $entidadNombre,
                'participacion' => [
                    'entidad' => $entidadNombre,
                    'numero' => $numeroReservado,
                    'fechaSorteo' => $fechaSorteo,
                    'importeJugado' => $importeJugado,
                    'donativo' => $donativo > 0 ? $donativo : null,
                    'importeTotal' => $importeTotal,
                    'numeroParticipacion' => $p->participation_code ?? $p->participation_number . '/' . str_pad($p->participation_number, 4, '0', STR_PAD_LEFT),
                    'numeroReferencia' => $numeroReferencia ?? str_pad((string) $p->id, 19, '0', STR_PAD_LEFT),
                    'snapshotPath' => $snapshotPath,
                ],
            ];
        })->filter()->values()->all();

        return response()->json([
            'success' => true,
            'historial' => $historial,
        ]);
    }

    /**
     * Buscar set y número de participación por referencia (campo 'r' del ticket).
     */
    private function findSetAndParticipationNumberByReference(string $referencia): ?array
    {
        $set = \App\Models\Set::whereNotNull('tickets')->get()->first(function ($s) use ($referencia) {
            if (!is_array($s->tickets)) {
                return false;
            }
            foreach ($s->tickets as $ticket) {
                if (isset($ticket['r']) && $ticket['r'] == $referencia) {
                    return true;
                }
            }
            return false;
        });
        if (!$set) {
            return null;
        }
        $participationNumber = null;
        foreach ($set->tickets as $ticket) {
            if (isset($ticket['r']) && $ticket['r'] == $referencia) {
                $participationNumber = $ticket['n'];
                break;
            }
        }
        return $participationNumber !== null ? ['set' => $set, 'participation_number' => $participationNumber] : null;
    }

    /**
     * Formatear participación para respuesta de cartera/detalle (entidad, fecha, importes, nº referencia).
     */
    private function formatParticipationForWallet(Participation $participation, string $referencia): array
    {
        $set = $participation->set()->with('reserve.lottery', 'entity', 'designFormats')->first();
        $reserve = $set->reserve ?? null;
        $lottery = $reserve ? $reserve->lottery : null;
        $entity = $set->entity ?? null;
        $designFormat = $set->designFormats->first();
        $snapshotPath = null;
        if ($designFormat && $designFormat->snapshot_path) {
            $snapshotPath = asset('storage/' . $designFormat->snapshot_path);
        }
        $numeroReservado = '—';
        if ($reserve && $reserve->reservation_numbers) {
            $nums = is_array($reserve->reservation_numbers) ? $reserve->reservation_numbers : json_decode($reserve->reservation_numbers, true);
            if (is_array($nums) && count($nums) > 0) {
                $numeroReservado = count($nums) === 1 ? (string) $nums[0] : (string) ($nums[$participation->participation_number - 1] ?? $nums[0]);
            }
        }
        $numeroReferencia = $referencia;
        if ($set->tickets) {
            $tickets = is_array($set->tickets) ? $set->tickets : json_decode($set->tickets, true);
            if (is_array($tickets)) {
                foreach ($tickets as $ticket) {
                    if (isset($ticket['n']) && $ticket['n'] == $participation->participation_number) {
                        $numeroReferencia = $ticket['r'] ?? $referencia;
                        break;
                    }
                }
            }
        }
        $importeJugado = (float) ($set->played_amount ?? 0);
        $donativo = (float) ($set->donation_amount ?? 0);
        $importeTotal = $importeJugado + $donativo;
        return [
            'id' => $participation->id,
            'referencia' => $referencia,
            'entidad' => $entity ? $entity->name : '—',
            'numero' => $participation->participation_number,
            'numeroReservado' => $numeroReservado,
            'fechaSorteo' => $lottery && $lottery->draw_date ? $lottery->draw_date->format('d/m/y') : '—',
            'importeJugado' => $importeJugado,
            'donativo' => $donativo,
            'importeTotal' => $importeTotal,
            'numeroParticipacion' => $participation->participation_code ?? ($participation->participation_number . '/0001'),
            'numeroReferencia' => $numeroReferencia,
            'snapshot_path' => $snapshotPath,
        ];
    }

    /**
     * API: Listar participaciones en la cartera del usuario (propias + recibidas como regalo).
     */
    public function apiGetWalletParticipations(Request $request)
    {
        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'Solo los usuarios pueden ver su cartera.'], 403);
        }
        $userId = (string) $user->id;
        $items = [];

        // Propias (buyer_name = user)
        $participations = Participation::where('buyer_name', $userId)
            ->with(['set.reserve.lottery', 'set.entity', 'set.designFormats', 'gift.toUser'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $apiController = app(ApiController::class);
        foreach ($participations as $p) {
            $ref = $this->getReferenceFromParticipation($p);
            $item = $this->formatParticipationForWallet($p, $ref);
            $item['estado'] = 'activa';
            $item['gifted_to_email'] = null;
            if ($p->collected_at) {
                $item['estado'] = 'cobrada';
            } elseif ($p->donated_at) {
                $item['estado'] = 'donada';
            } elseif ($p->relationLoaded('gift') && $p->gift) {
                $item['estado'] = 'regalada';
                $item['gifted_to_email'] = $p->gift->toUser->email ?? null;
            }
            $prizeInfo = $apiController->getPrizeInfoForReference($ref);
            $item['premio'] = $prizeInfo['has_won'] ? $prizeInfo['prize_amount'] : null;
            $items[] = $item;
        }

        // Recibidas como regalo (to_user_id = user)
        $giftsReceived = ParticipationGift::where('to_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftsReceived as $gift) {
            $p = $gift->participation;
            if (!$p) continue;
            $ref = $this->getReferenceFromParticipation($p);
            $item = $this->formatParticipationForWallet($p, $ref);
            if ($p->collected_at) {
                $item['estado'] = 'cobrada';
            } elseif ($p->donated_at) {
                $item['estado'] = 'donada';
            } else {
                $item['estado'] = 'recibida';
            }
            $item['received_from_email'] = $gift->fromUser->email ?? null;
            $item['gifted_to_email'] = null;
            $prizeInfo = $apiController->getPrizeInfoForReference($ref);
            $item['premio'] = $prizeInfo['has_won'] ? $prizeInfo['prize_amount'] : null;
            $items[] = $item;
        }

        usort($items, function ($a, $b) {
            return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
        });

        return response()->json(['success' => true, 'participations' => $items]);
    }

    /**
     * API: Participaciones cobrables/donables (tienen premio, no cobradas ni donadas).
     * Incluye propias del usuario (no regaladas) y las recibidas como regalo.
     */
    public function apiGetCobrables(Request $request)
    {
        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'Solo los usuarios pueden acceder.'], 403);
        }
        $userId = (string) $user->id;
        $apiController = app(ApiController::class);
        $items = [];
        $addedIds = [];

        // 1) Propias (buyer_name = user), no regaladas, no cobradas, no donadas, con premio
        $participations = Participation::where('buyer_name', $userId)
            ->whereNull('collected_at')
            ->whereNull('donated_at')
            ->with(['set.reserve.lottery', 'set.entity', 'set.designFormats', 'gift'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($participations as $p) {
            if ($p->relationLoaded('gift') && $p->gift) {
                continue; // regalada, no cobrable ni donable por el que la regaló
            }
            $ref = $this->getReferenceFromParticipation($p);
            $prizeInfo = $apiController->getPrizeInfoForReference($ref);
            if (!($prizeInfo['has_won'] && $prizeInfo['prize_amount'] > 0)) {
                continue; // sin premio
            }
            $item = $this->formatParticipationForWallet($p, $ref);
            $item['premio'] = $prizeInfo['prize_amount'];
            $items[] = $item;
            $addedIds[$p->id] = true;
        }

        // 2) Recibidas como regalo (to_user_id = user), no cobradas, no donadas, con premio
        $giftsReceived = ParticipationGift::where('to_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats'])
            ->get();

        foreach ($giftsReceived as $gift) {
            $p = $gift->participation;
            if (!$p || isset($addedIds[$p->id])) {
                continue;
            }
            if ($p->collected_at || $p->donated_at) {
                continue;
            }
            $ref = $this->getReferenceFromParticipation($p);
            $prizeInfo = $apiController->getPrizeInfoForReference($ref);
            if (!($prizeInfo['has_won'] && $prizeInfo['prize_amount'] > 0)) {
                continue;
            }
            $item = $this->formatParticipationForWallet($p, $ref);
            $item['premio'] = $prizeInfo['prize_amount'];
            $item['recibida_regalo'] = true;
            $items[] = $item;
            $addedIds[$p->id] = true;
        }

        return response()->json(['success' => true, 'participations' => $items]);
    }

    /**
     * API: Registrar cobro (marca participaciones como cobradas).
     * Valida nombre, apellidos, NIF e IBAN (formato español).
     */
    public function apiRegistrarCobro(Request $request)
    {
        $request->validate([
            'participation_ids' => 'required|array',
            'participation_ids.*' => 'integer|exists:participations,id',
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'nif' => ['required', 'string', 'max:20', new \App\Rules\SpanishDocument],
            'iban' => ['required', 'string', new \App\Rules\SpanishIban],
            'importe_total' => 'required|numeric|min:0',
        ]);

        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }
        $userId = (string) $user->id;

        $allowedIds = $this->getParticipationIdsOwnedOrReceivedByUser($user);
        $participations = Participation::whereIn('id', $request->participation_ids)
            ->whereIn('id', $allowedIds)
            ->whereNull('collected_at')
            ->get();

        if ($participations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Ninguna participación válida para cobrar.'], 422);
        }

        // Usar el importe total enviado desde el frontend
        $importeTotal = (float) $request->importe_total;

        // Crear registro de cobro
        $collection = ParticipationCollection::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'nif' => $request->nif,
            'iban' => $request->iban,
            'importe_total' => $importeTotal,
            'collected_at' => now(),
        ]);

        // Marcar participaciones como cobradas en la tabla participations
        $participationIds = $participations->pluck('id')->toArray();
        Participation::whereIn('id', $participationIds)->update(['collected_at' => now()]);

        // Asociar cada participación al registro de cobro
        foreach ($participationIds as $pid) {
            ParticipationCollectionItem::create([
                'collection_id' => $collection->id,
                'participation_id' => $pid,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cobro registrado correctamente. La entidad se encargará del pago.',
            'collected_count' => count($participationIds),
        ]);
    }

    /**
     * API: Registrar donación (marca participaciones como donadas y genera código de recarga si aplica).
     * Valida participation_ids, importe_donacion, importe_codigo, y datos personales opcionales.
     */
    public function apiRegistrarDonacion(Request $request)
    {
        $request->validate([
            'participation_ids' => 'required|array',
            'participation_ids.*' => 'integer|exists:participations,id',
            'importe_donacion' => 'required|numeric|min:0',
            'importe_codigo' => 'required|numeric|min:0',
            'nombre' => 'nullable|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'nif' => ['nullable', 'string', 'max:20', new \App\Rules\SpanishDocument],
        ]);

        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }
        $userId = (string) $user->id;

        $allowedIds = $this->getParticipationIdsOwnedOrReceivedByUser($user);
        $participations = Participation::whereIn('id', $request->participation_ids)
            ->whereIn('id', $allowedIds)
            ->whereNull('collected_at')
            ->whereNull('donated_at')
            ->get();

        if ($participations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Ninguna participación válida para donar.'], 422);
        }

        $importeDonacion = (float) $request->importe_donacion;
        $importeCodigo = (float) $request->importe_codigo;
        $importeTotal = $importeDonacion + $importeCodigo;

        // Validar que la suma coincida con el total de las participaciones (Participation usa sale_amount)
        $totalParticipaciones = $participations->sum(function ($p) {
            return (float) ($p->sale_amount ?? 0);
        });

        if (abs($importeTotal - $totalParticipaciones) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'La suma de donación y código no coincide con el importe total de las participaciones.'
            ], 422);
        }

        // Generar código de recarga si hay importe para código
        $codigoRecarga = null;
        if ($importeCodigo > 0) {
            $codigoRecarga = $this->generarCodigoRecargaUnico();
        }

        // Determinar si es anónima (sin datos personales)
        $anonima = empty($request->nombre) || empty($request->apellidos) || empty($request->nif);

        // Crear registro de donación
        $donation = ParticipationDonation::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'nif' => $request->nif,
            'importe_donacion' => $importeDonacion,
            'importe_codigo' => $importeCodigo,
            'codigo_recarga' => $codigoRecarga,
            'anonima' => $anonima,
            'donated_at' => now(),
        ]);

        // Marcar participaciones como donadas en la tabla participations
        $participationIds = $participations->pluck('id')->toArray();
        Participation::whereIn('id', $participationIds)->update(['donated_at' => now()]);

        // Asociar cada participación al registro de donación
        foreach ($participationIds as $pid) {
            ParticipationDonationItem::create([
                'donation_id' => $donation->id,
                'participation_id' => $pid,
            ]);
        }

        // TODO: Enviar email con el código de recarga si existe

        return response()->json([
            'success' => true,
            'message' => 'Donación registrada correctamente.',
            'donation_id' => $donation->id,
            'codigo_recarga' => $codigoRecarga,
            'importe_donacion' => $importeDonacion,
            'importe_codigo' => $importeCodigo,
        ]);
    }

    /**
     * Generar código de recarga único (10 caracteres alfanuméricos)
     */
    private function generarCodigoRecargaUnico(): string
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $intentos = 0;
        do {
            $codigo = '';
            for ($i = 0; $i < 10; $i++) {
                $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
            $intentos++;
        } while (ParticipationDonation::where('codigo_recarga', $codigo)->exists() && $intentos < 100);

        if ($intentos >= 100) {
            throw new \Exception('No se pudo generar un código único después de 100 intentos.');
        }

        return $codigo;
    }

    /**
     * API: Historial del usuario (digitalizaciones, regalos enviados; cobros pendiente).
     * Solo clientes. Ordenado por fecha descendente.
     */
    public function apiGetUserHistorial(Request $request)
    {
        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'Solo los usuarios pueden ver su historial.'], 403);
        }
        $userId = (string) $user->id;
        $historial = [];

        // 1. Digitalizaciones: participaciones vinculadas a la cartera (buyer_name = user)
        $participations = Participation::where('buyer_name', $userId)
            ->with(['set.reserve.lottery', 'set.entity', 'set.designFormats'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($participations as $p) {
            $ref = $this->getReferenceFromParticipation($p);
            $participacion = $this->formatParticipationForWallet($p, $ref);
            $historial[] = [
                'id' => 'd-' . $p->id,
                'tipo' => 'digitalizacion',
                'fecha' => $p->updated_at->toIso8601String(),
                'participacion' => $participacion,
                'descripcion' => 'Participación ' . ($participacion['entidad'] ?? 'digitalizada'),
            ];
        }

        // 2. Regalos enviados (participation_gifts donde from_user_id = user)
        $giftsSent = ParticipationGift::where('from_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftsSent as $gift) {
            $p = $gift->participation;
            if (!$p) continue;
            $ref = $this->getReferenceFromParticipation($p);
            $participacion = $this->formatParticipationForWallet($p, $ref);
            $historial[] = [
                'id' => 'r-env-' . $gift->id,
                'tipo' => 'regalo',
                'fecha' => $gift->created_at->toIso8601String(),
                'participacion' => $participacion,
                'emailDestinatario' => $gift->toUser->email ?? null,
                'destinatario' => $gift->toUser->email ?? null,
                'direccion' => 'enviado',
                'descripcion' => 'Participación regalada a ' . ($gift->toUser->email ?? '—'),
            ];
        }

        // 2b. Regalos recibidos (participation_gifts donde to_user_id = user)
        $giftsReceived = ParticipationGift::where('to_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftsReceived as $gift) {
            $p = $gift->participation;
            if (!$p) continue;
            $ref = $this->getReferenceFromParticipation($p);
            $participacion = $this->formatParticipationForWallet($p, $ref);
            $historial[] = [
                'id' => 'r-rec-' . $gift->id,
                'tipo' => 'regalo',
                'fecha' => $gift->created_at->toIso8601String(),
                'participacion' => $participacion,
                'emailRemitente' => $gift->fromUser->email ?? null,
                'remitente' => $gift->fromUser->email ?? null,
                'direccion' => 'recibido',
                'descripcion' => 'Participación recibida de ' . ($gift->fromUser->email ?? '—'),
            ];
        }

        // 3. Cobros: participaciones cobradas por el usuario
        $collections = ParticipationCollection::where('user_id', $user->id)
            ->with(['items.participation.set.reserve.lottery', 'items.participation.set.entity', 'items.participation.set.designFormats'])
            ->orderBy('collected_at', 'desc')
            ->get();

        foreach ($collections as $collection) {
            $participaciones = [];
            foreach ($collection->items as $item) {
                $p = $item->participation;
                if (!$p) continue;
                $ref = $this->getReferenceFromParticipation($p);
                $participaciones[] = $this->formatParticipationForWallet($p, $ref);
            }
            
            if (!empty($participaciones)) {
                $historial[] = [
                    'id' => 'c-' . $collection->id,
                    'tipo' => 'cobro',
                    'fecha' => $collection->collected_at->toIso8601String(),
                    'participaciones' => $participaciones,
                    'importeTotal' => (float) $collection->importe_total,
                    'datosPersonales' => [
                        'nombre' => $collection->nombre,
                        'apellidos' => $collection->apellidos,
                        'nif' => $collection->nif,
                    ],
                    'iban' => $collection->iban,
                    'descripcion' => 'Cobro de ' . count($participaciones) . ' participación(es) - €' . number_format($collection->importe_total, 2, ',', '.'),
                ];
            }
        }

        // 4. Donaciones: participaciones donadas por el usuario
        $donations = ParticipationDonation::where('user_id', $user->id)
            ->with(['items.participation.set.reserve.lottery', 'items.participation.set.entity', 'items.participation.set.designFormats'])
            ->orderByRaw('COALESCE(donated_at, created_at) DESC')
            ->get();

        foreach ($donations as $donation) {
            $participaciones = [];
            if ($donation->items && $donation->items->count() > 0) {
                foreach ($donation->items as $item) {
                    if ($item->participation) {
                        $p = $item->participation;
                        $ref = $this->getReferenceFromParticipation($p);
                        $participaciones[] = $this->formatParticipationForWallet($p, $ref);
                    }
                }
            }
            
            // Añadir entrada de donación siempre (incluso si no tiene participaciones asociadas)
            $fechaDonacion = $donation->donated_at ? $donation->donated_at->toIso8601String() : ($donation->created_at ? $donation->created_at->toIso8601String() : now()->toIso8601String());
            
            $historial[] = [
                'id' => 'don-' . $donation->id,
                'tipo' => 'donacion',
                'fecha' => $fechaDonacion,
                'participaciones' => $participaciones,
                'importeDonacion' => (float) $donation->importe_donacion,
                'importeCodigo' => (float) $donation->importe_codigo,
                'codigoRecarga' => $donation->codigo_recarga ?? null,
                'datosPersonales' => $donation->anonima ? null : [
                    'nombre' => $donation->nombre,
                    'apellidos' => $donation->apellidos,
                    'nif' => $donation->nif,
                ],
                'anonima' => $donation->anonima,
                'descripcion' => 'Donación' . 
                    (count($participaciones) > 0 ? ' de ' . count($participaciones) . ' participación(es)' : '') .
                    ($donation->importe_donacion > 0 ? ' - €' . number_format($donation->importe_donacion, 2, ',', '.') : '') .
                    ($donation->codigo_recarga ? ' - Código: ' . $donation->codigo_recarga : ''),
            ];
        }

        // Ordenar por fecha descendente
        usort($historial, function ($a, $b) {
            $fechaA = $a['fecha'] ?? '';
            $fechaB = $b['fecha'] ?? '';
            return strcmp($fechaB, $fechaA);
        });

        return response()->json(['success' => true, 'historial' => $historial]);
    }

    /**
     * Datos de cartera para un usuario (uso web admin). Misma lógica que apiGetWalletParticipations pero para User $user.
     */
    public function getWalletDataForUser(User $user): array
    {
        $userId = (string) $user->id;
        $items = [];

        $participations = Participation::where('buyer_name', $userId)
            ->with(['set.reserve.lottery', 'set.entity', 'set.designFormats', 'gift.toUser'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($participations as $p) {
            $ref = $this->getReferenceFromParticipation($p);
            $item = $this->formatParticipationForWallet($p, $ref);
            $item['estado'] = 'activa';
            $item['gifted_to_email'] = null;
            if ($p->collected_at) {
                $item['estado'] = 'cobrada';
            } elseif ($p->donated_at) {
                $item['estado'] = 'donada';
            } elseif ($p->relationLoaded('gift') && $p->gift) {
                $item['estado'] = 'regalada';
                $item['gifted_to_email'] = $p->gift->toUser->email ?? null;
            }
            $item['premio'] = null;
            $items[] = $item;
        }

        $giftsReceived = ParticipationGift::where('to_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftsReceived as $gift) {
            $p = $gift->participation;
            if (!$p) continue;
            $ref = $this->getReferenceFromParticipation($p);
            $item = $this->formatParticipationForWallet($p, $ref);
            $item['estado'] = $p->collected_at ? 'cobrada' : ($p->donated_at ? 'donada' : 'recibida');
            $item['received_from_email'] = $gift->fromUser->email ?? null;
            $item['gifted_to_email'] = null;
            $item['premio'] = null;
            $items[] = $item;
        }

        usort($items, function ($a, $b) {
            return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
        });

        return $items;
    }

    /**
     * Datos de historial para un usuario (uso web admin). Misma lógica que apiGetUserHistorial pero para User $user.
     */
    public function getHistorialDataForUser(User $user): array
    {
        $userId = (string) $user->id;
        $historial = [];

        $participations = Participation::where('buyer_name', $userId)
            ->with(['set.reserve.lottery', 'set.entity', 'set.designFormats'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($participations as $p) {
            $ref = $this->getReferenceFromParticipation($p);
            $participacion = $this->formatParticipationForWallet($p, $ref);
            $historial[] = [
                'id' => 'd-' . $p->id,
                'tipo' => 'digitalizacion',
                'fecha' => $p->updated_at->toIso8601String(),
                'participacion' => $participacion,
                'descripcion' => 'Participación ' . ($participacion['entidad'] ?? 'digitalizada'),
            ];
        }

        $giftsSent = ParticipationGift::where('from_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftsSent as $gift) {
            $p = $gift->participation;
            if (!$p) continue;
            $ref = $this->getReferenceFromParticipation($p);
            $participacion = $this->formatParticipationForWallet($p, $ref);
            $historial[] = [
                'id' => 'r-env-' . $gift->id,
                'tipo' => 'regalo',
                'fecha' => $gift->created_at->toIso8601String(),
                'participacion' => $participacion,
                'destinatario' => $gift->toUser->email ?? '—',
                'direccion' => 'enviado',
                'descripcion' => 'Participación regalada a ' . ($gift->toUser->email ?? '—'),
            ];
        }

        $giftsReceived = ParticipationGift::where('to_user_id', $user->id)
            ->with(['participation.set.reserve.lottery', 'participation.set.entity', 'participation.set.designFormats', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftsReceived as $gift) {
            $p = $gift->participation;
            if (!$p) continue;
            $ref = $this->getReferenceFromParticipation($p);
            $participacion = $this->formatParticipationForWallet($p, $ref);
            $historial[] = [
                'id' => 'r-rec-' . $gift->id,
                'tipo' => 'regalo',
                'fecha' => $gift->created_at->toIso8601String(),
                'participacion' => $participacion,
                'remitente' => $gift->fromUser->email ?? '—',
                'direccion' => 'recibido',
                'descripcion' => 'Participación recibida de ' . ($gift->fromUser->email ?? '—'),
            ];
        }

        $collections = ParticipationCollection::where('user_id', $user->id)
            ->with(['items.participation.set.reserve.lottery', 'items.participation.set.entity', 'items.participation.set.designFormats'])
            ->orderBy('collected_at', 'desc')
            ->get();

        foreach ($collections as $collection) {
            $participaciones = [];
            foreach ($collection->items as $item) {
                $p = $item->participation;
                if (!$p) continue;
                $ref = $this->getReferenceFromParticipation($p);
                $participaciones[] = $this->formatParticipationForWallet($p, $ref);
            }
            if (!empty($participaciones)) {
                $historial[] = [
                    'id' => 'c-' . $collection->id,
                    'tipo' => 'cobro',
                    'fecha' => $collection->collected_at->toIso8601String(),
                    'participaciones' => $participaciones,
                    'importeTotal' => (float) $collection->importe_total,
                    'descripcion' => 'Cobro de ' . count($participaciones) . ' participación(es) - €' . number_format($collection->importe_total, 2, ',', '.'),
                ];
            }
        }

        $donations = ParticipationDonation::where('user_id', $user->id)
            ->with(['items.participation.set.reserve.lottery', 'items.participation.set.entity', 'items.participation.set.designFormats'])
            ->orderByRaw('COALESCE(donated_at, created_at) DESC')
            ->get();

        foreach ($donations as $donation) {
            $participaciones = [];
            if ($donation->items && $donation->items->count() > 0) {
                foreach ($donation->items as $item) {
                    if ($item->participation) {
                        $ref = $this->getReferenceFromParticipation($item->participation);
                        $participaciones[] = $this->formatParticipationForWallet($item->participation, $ref);
                    }
                }
            }
            $fechaDonacion = $donation->donated_at ? $donation->donated_at->toIso8601String() : ($donation->created_at ? $donation->created_at->toIso8601String() : now()->toIso8601String());
            $historial[] = [
                'id' => 'don-' . $donation->id,
                'tipo' => 'donacion',
                'fecha' => $fechaDonacion,
                'participaciones' => $participaciones,
                'importeDonacion' => (float) $donation->importe_donacion,
                'descripcion' => 'Donación' . (count($participaciones) > 0 ? ' de ' . count($participaciones) . ' participación(es)' : '') . ($donation->importe_donacion > 0 ? ' - €' . number_format($donation->importe_donacion, 2, ',', '.') : ''),
            ];
        }

        usort($historial, function ($a, $b) {
            return strcmp($b['fecha'] ?? '', $a['fecha'] ?? '');
        });

        return $historial;
    }

    private function getReferenceFromParticipation(Participation $p): string
    {
        if (!$p->set || !is_array($p->set->tickets)) {
            return '';
        }
        foreach ($p->set->tickets as $ticket) {
            if (isset($ticket['n']) && $ticket['n'] == $p->participation_number) {
                return $ticket['r'] ?? '';
            }
        }
        return '';
    }

    /**
     * IDs de participaciones que el usuario puede cobrar/donar: propias (buyer_name) o recibidas como regalo.
     */
    private function getParticipationIdsOwnedOrReceivedByUser(User $user): \Illuminate\Support\Collection
    {
        $userId = (string) $user->id;
        $ownedIds = Participation::where('buyer_name', $userId)->pluck('id');
        $receivedIds = ParticipationGift::where('to_user_id', $user->id)->pluck('participation_id');
        return $ownedIds->merge($receivedIds)->unique()->values();
    }

    /**
     * API: Consultar participación por referencia (para usuario, antes de vincular).
     * Devuelve: can_link + datos, o status: already_mine | already_other | not_found.
     */
    public function apiCheckByReference(Request $request)
    {
        $request->validate(['referencia' => 'required|string']);
        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }
        $userId = (string) $user->id;
        $found = $this->findSetAndParticipationNumberByReference($request->referencia);
        if (!$found) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'No se encuentra la participación. Comprueba la referencia o el código QR.',
            ], 404);
        }
        $participation = Participation::where('set_id', $found['set']->id)
            ->where('participation_number', $found['participation_number'])
            ->with(['set.reserve.lottery', 'set.entity', 'set.designFormats'])
            ->first();
        if (!$participation) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'message' => 'No se encuentra la participación.',
            ], 404);
        }
        $currentBuyer = $participation->buyer_name;
        if ($currentBuyer !== null && $currentBuyer !== '') {
            if ($currentBuyer === $userId) {
                return response()->json([
                    'success' => true,
                    'status' => 'already_mine',
                    'message' => 'Ya la posees en tu cartera.',
                    'participation' => $this->formatParticipationForWallet($participation, $request->referencia),
                ]);
            }
            return response()->json([
                'success' => false,
                'status' => 'already_other',
                'message' => 'La participación no se puede vincular porque ya se encuentra leída por otro usuario.',
            ], 422);
        }
        return response()->json([
            'success' => true,
            'status' => 'can_link',
            'participation' => $this->formatParticipationForWallet($participation, $request->referencia),
        ]);
    }

    /**
     * API: Vincular participación a la cartera del usuario (guardar user id en buyer_name).
     */
    public function apiLinkToWallet(Request $request)
    {
        $request->validate(['referencia' => 'required|string']);
        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }
        $userId = (string) $user->id;
        $found = $this->findSetAndParticipationNumberByReference($request->referencia);
        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'No se encuentra la participación.',
            ], 404);
        }
        $participation = Participation::where('set_id', $found['set']->id)
            ->where('participation_number', $found['participation_number'])
            ->first();
        if (!$participation) {
            return response()->json(['success' => false, 'message' => 'No se encuentra la participación.'], 404);
        }
        if ($participation->buyer_name !== null && $participation->buyer_name !== '') {
            if ($participation->buyer_name === $userId) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ya la tienes en tu cartera.',
                    'participation' => $this->formatParticipationForWallet($participation->load('set'), $request->referencia),
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'La participación no se puede vincular porque ya se encuentra leída por otro usuario.',
            ], 422);
        }
        $participation->buyer_name = $userId;
        $participation->save();
        return response()->json([
            'success' => true,
            'message' => 'Participación añadida a tu cartera.',
            'participation' => $this->formatParticipationForWallet($participation->load(['set.reserve.lottery', 'set.entity', 'set.designFormats']), $request->referencia),
        ]);
    }

    /**
     * API: Regalar participación a otro usuario por email.
     * La participación sigue en la cartera del que regala con estado regalada; el destinatario la ve en la suya.
     */
    public function apiGiftToUser(Request $request)
    {
        $request->validate([
            'participation_id' => 'required|integer|exists:participations,id',
            'email' => 'required|email',
        ]);

        $user = $request->user();
        // Permitir tanto usuarios (client) como vendedores (seller) cuando acceden como usuarios normales
        if (!$user->isClient() && !$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $userId = (string) $user->id;
        $participation = Participation::find($request->participation_id);
        if (!$participation || $participation->buyer_name !== $userId) {
            return response()->json(['success' => false, 'message' => 'La participación no está en tu cartera.'], 404);
        }

        if (ParticipationGift::where('participation_id', $participation->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Esta participación ya ha sido regalada.'], 422);
        }
        if ($participation->collected_at) {
            return response()->json(['success' => false, 'message' => 'No se puede regalar una participación ya cobrada.'], 422);
        }
        if ($participation->donated_at) {
            return response()->json(['success' => false, 'message' => 'No se puede regalar una participación ya donada.'], 422);
        }

        $destinatario = User::where('email', $request->email)->first();
        if (!$destinatario) {
            return response()->json([
                'success' => false,
                'message' => 'No existe ningún usuario con ese correo. El destinatario debe estar registrado como usuario.',
            ], 422);
        }
        if (!$destinatario->isClient()) {
            return response()->json([
                'success' => false,
                'message' => 'El correo no corresponde a un usuario. Solo se puede regalar a usuarios con perfil de usuario.',
            ], 422);
        }
        if ((string) $destinatario->id === $userId) {
            return response()->json(['success' => false, 'message' => 'No puedes regalarte la participación a ti mismo.'], 422);
        }

        ParticipationGift::create([
            'participation_id' => $participation->id,
            'from_user_id' => $user->id,
            'to_user_id' => $destinatario->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Participación enviada con éxito.',
            'gifted_to_email' => $destinatario->email,
        ]);
    }
}
