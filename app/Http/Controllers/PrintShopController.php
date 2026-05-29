<?php

namespace App\Http\Controllers;

use App\Models\DesignFormat;
use App\Models\PrintOrder;
use App\Services\PrintOrderPaymentReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintShopController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePrintShopAccess($request);

        $statusFilter = (string) $request->query('status', 'all');
        $query = PrintOrder::query()
            ->with(['entity', 'set', 'lottery', 'design'])
            ->orderByDesc('id');

        if ($statusFilter !== 'all' && in_array($statusFilter, [
            PrintOrder::STATUS_PENDING_REVIEW,
            PrintOrder::STATUS_IN_PRODUCTION,
            PrintOrder::STATUS_SENT,
            PrintOrder::STATUS_REJECTED,
        ], true)) {
            $query->where('status', $statusFilter);
        }

        $printOrders = $query->limit(300)->get();

        $reconciliationService = app(PrintOrderPaymentReconciliationService::class);
        $printOrderIssuesById = [];
        foreach ($printOrders as $order) {
            $issue = $reconciliationService->detectIssue($order);
            if ($issue) {
                $printOrderIssuesById[$order->id] = $issue;
            }
        }

        $orderIds = $printOrders->pluck('id')->all();
        $printOrderAuditsByOrderId = $this->loadOrderAudits($orderIds);

        $counts = PrintOrder::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return view('print-shop.index', compact(
            'printOrders',
            'printOrderAuditsByOrderId',
            'printOrderIssuesById',
            'statusFilter',
            'counts'
        ));
    }

    public function show(Request $request, PrintOrder $printOrder)
    {
        $this->authorizePrintShopAccess($request);

        $printOrder->load(['entity', 'set.reserve.lottery', 'lottery', 'design']);
        $audits = DB::table('print_order_status_audits')
            ->where('print_order_id', $printOrder->id)
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $userIds = $audits->pluck('user_id')->filter()->unique()->values()->all();
        $usersById = empty($userIds)
            ? collect()
            : DB::table('users')->whereIn('id', $userIds)->pluck('name', 'id');

        $audits = $audits->map(function ($row) use ($usersById) {
            $row->user_name = $row->user_id ? ($usersById[$row->user_id] ?? ('Usuario #'.$row->user_id)) : 'Sistema';

            return $row;
        });

        $paymentIssue = app(PrintOrderPaymentReconciliationService::class)->detectIssue($printOrder);

        return view('print-shop.show', compact('printOrder', 'audits', 'paymentIssue'));
    }

    public function updateStatus(Request $request, PrintOrder $printOrder)
    {
        $this->authorizePrintShopAccess($request);

        if (! $this->canOperatePrintOrders($request->user())) {
            return redirect()->back()->with('error', 'No tienes permisos para cambiar el estado de órdenes.');
        }

        $data = $request->validate([
            'target_status' => 'required|string|in:pendiente_revision,en_produccion,enviada,rechazada',
        ]);

        $target = $data['target_status'];
        if (! $printOrder->canTransitionTo($target)) {
            $reason = $printOrder->paymentTransitionBlockReason();

            return redirect()->back()->with('error', $reason ?? 'Transición de estado no permitida.');
        }

        $from = (string) $printOrder->status;
        $printOrder->status = $target;
        if ($target === PrintOrder::STATUS_SENT && ! $printOrder->sent_at) {
            $printOrder->sent_at = now();
        }
        $printOrder->save();

        DB::table('print_order_status_audits')->insert([
            'print_order_id' => $printOrder->id,
            'entity_id' => $printOrder->entity_id,
            'set_id' => $printOrder->set_id,
            'design_format_id' => $printOrder->design_format_id,
            'user_id' => auth()->id(),
            'action' => 'status_change',
            'from_status' => $from,
            'to_status' => $target,
            'message' => 'Cambio de estado (panel imprenta)',
            'created_at' => now(),
        ]);

        return redirect()
            ->route('print-shop.orders.show', $printOrder->id)
            ->with('success', 'Estado actualizado a: '.PrintOrder::statusLabel($target).'.');
    }

    private function authorizePrintShopAccess(Request $request): void
    {
        $user = $request->user();
        if (! $user || (! $user->isPrintShop() && ! $user->isSuperAdmin())) {
            abort(403, 'No tienes acceso al panel de imprenta.');
        }
    }

    private function canOperatePrintOrders($user): bool
    {
        return $user && ($user->isSuperAdmin() || $user->isPrintShop());
    }

    /**
     * @param  list<int>  $orderIds
     */
    private function loadOrderAudits(array $orderIds): \Illuminate\Support\Collection
    {
        if (empty($orderIds)) {
            return collect();
        }

        $audits = DB::table('print_order_status_audits')
            ->whereIn('print_order_id', $orderIds)
            ->orderByDesc('id')
            ->get();

        $userIds = $audits->pluck('user_id')->filter()->unique()->values()->all();
        $usersById = empty($userIds)
            ? collect()
            : DB::table('users')->whereIn('id', $userIds)->pluck('name', 'id');

        return $audits
            ->map(function ($row) use ($usersById) {
                $row->user_name = $row->user_id ? ($usersById[$row->user_id] ?? ('Usuario #'.$row->user_id)) : 'Sistema';

                return $row;
            })
            ->groupBy('print_order_id');
    }
}
