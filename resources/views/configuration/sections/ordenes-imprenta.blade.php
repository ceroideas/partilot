<div class="form-card bs">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Órdenes Imprenta</h4>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($printOrders->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="fe-info me-2"></i>
            No hay órdenes de imprenta registradas todavía.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Orden</th>
                        <th>Entidad</th>
                        <th>Set</th>
                        <th>Sorteo</th>
                        <th>Estado</th>
                        <th>Importe</th>
                        <th>Fecha envío</th>
                        <th>Historial</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($printOrders as $order)
                    @php
                        $canOperate = auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdministration());
                        $orderAudits = $printOrderAuditsByOrderId[$order->id] ?? collect();
                    @endphp
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->entity->name ?? '—' }}</td>
                        <td>{{ $order->set->set_name ?? ('#'.$order->set_id) }}</td>
                        <td>{{ $order->lottery->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ \App\Models\PrintOrder::statusBadgeClass((string) $order->status) }} rounded-pill">
                                {{ \App\Models\PrintOrder::statusLabel((string) $order->status) }}
                            </span>
                        </td>
                        <td>{{ number_format((float) $order->quoted_amount, 2, ',', '.') }}€</td>
                        <td>{{ $order->sent_at ? $order->sent_at->format('d/m/Y H:i') : '—' }}</td>
                        <td>
                            @if($orderAudits->isNotEmpty())
                                <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#order-audit-modal-{{ $order->id }}">
                                    <i class="ri-time-line me-1"></i> Ver ({{ $orderAudits->count() }})
                                </button>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                @if($order->canTransitionTo(\App\Models\PrintOrder::STATUS_IN_PRODUCTION) && $canOperate)
                                    <form method="POST" action="{{ route('configuration.print-orders.status', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="target_status" value="{{ \App\Models\PrintOrder::STATUS_IN_PRODUCTION }}">
                                        <button type="submit" class="btn btn-sm btn-info text-dark" title="Marcar en producción">
                                            <i class="ri-hammer-line"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($order->canTransitionTo(\App\Models\PrintOrder::STATUS_SENT) && $canOperate)
                                    <form method="POST" action="{{ route('configuration.print-orders.status', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="target_status" value="{{ \App\Models\PrintOrder::STATUS_SENT }}">
                                        <button type="submit" class="btn btn-sm btn-success" title="Marcar enviada">
                                            <i class="ri-truck-line"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($order->canTransitionTo(\App\Models\PrintOrder::STATUS_REJECTED) && $canOperate)
                                    <form method="POST" action="{{ route('configuration.print-orders.status', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="target_status" value="{{ \App\Models\PrintOrder::STATUS_REJECTED }}">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rechazar orden">
                                            <i class="ri-close-circle-line"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($order->canTransitionTo(\App\Models\PrintOrder::STATUS_PENDING_REVIEW) && $canOperate)
                                    <form method="POST" action="{{ route('configuration.print-orders.status', $order->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="target_status" value="{{ \App\Models\PrintOrder::STATUS_PENDING_REVIEW }}">
                                        <button type="submit" class="btn btn-sm btn-warning text-dark" title="Reabrir en revisión">
                                            <i class="ri-restart-line"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(!$canOperate)
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Solo administración o super admin puede cambiar estados.">
                                        <i class="ri-lock-line"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @foreach($printOrders as $order)
            @php($orderAudits = $printOrderAuditsByOrderId[$order->id] ?? collect())
            @if($orderAudits->isNotEmpty())
                <div class="modal fade" id="order-audit-modal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Historial de estados - {{ $order->order_code }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>De</th>
                                                <th>A</th>
                                                <th>Detalle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($orderAudits->take(25) as $audit)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($audit->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>{{ $audit->user_name ?? 'Sistema' }}</td>
                                                <td>{{ $audit->from_status ? \App\Models\PrintOrder::statusLabel((string) $audit->from_status) : '—' }}</td>
                                                <td>{{ $audit->to_status ? \App\Models\PrintOrder::statusLabel((string) $audit->to_status) : '—' }}</td>
                                                <td>{{ $audit->message ?? 'Cambio de estado' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>
