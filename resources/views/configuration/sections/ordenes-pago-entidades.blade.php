<div class="form-card bs">
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
    @if($step === 1)
        {{-- Paso 1: Selección Entidad --}}
        <h4 class="mb-0 mt-1">Selección Entidad</h4>
        <small><i>Selecciona la Entidad</i></small>

        <form method="get" action="{{ url('/configuration') }}" id="form-filtros-ope">
            <input type="hidden" name="section" value="ordenes-pago-entidades">
            <input type="hidden" name="step" value="1">
            <div class="row mt-3 mb-3 g-2">
                <div class="col-md-3">
                    <label class="form-label small">Provincia</label>
                    <select name="provincia" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach($provincias as $p)
                            <option value="{{ $p }}" {{ request('provincia') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Localidad</label>
                    <select name="localidad" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach($localidades as $c)
                            <option value="{{ $c }}" {{ request('localidad') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Búsqueda</label>
                    <div class="d-flex gap-1">
                        <input type="search" name="busqueda" class="form-control form-control-sm" placeholder="Busqueda" value="{{ request('busqueda') }}">
                        <button type="submit" class="btn btn-sm btn-light"><i class="fe-search"></i></button>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive" style="min-height: 250px;">
            <table id="tabla-ope-entidades" class="table table-striped table-centered nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>Orden ID</th>
                        <th>Entidad</th>
                        <th>Provincia</th>
                        <th>Localidad</th>
                        <th>Administración</th>
                        <th>Status</th>
                        <th class="d-none">Seleccionar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entities as $ent)
                        <tr class="selectable-row" style="cursor: pointer;" data-entity-id="{{ $ent->id }}">
                            <td>#EN{{ str_pad($ent->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $ent->name }}</td>
                            <td>{{ $ent->province ?? '—' }}</td>
                            <td>{{ $ent->city ?? '—' }}</td>
                            <td>{{ $ent->administration->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $ent->status == 1 ? 'success' : ($ent->status == 0 ? 'danger' : 'secondary') }}">
                                    {{ $ent->status_text }}
                                </span>
                            </td>
                            <td class="d-none">
                                <label class="mb-0">
                                    <input type="radio" name="entity_id_ope" value="{{ $ent->id }}" class="form-check-input"> Seleccionar
                                </label>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No hay entidades que coincidan con el filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-12 text-end">
                <input type="hidden" id="selected-entity-id-ope" value="">
                <button type="button" id="btn-siguiente-ope" style="border-radius: 30px; min-width: 160px; background-color: #e78307; color: #333; padding: 8px 16px; font-weight: bolder;" class="btn btn-md btn-light" disabled>
                    Siguiente <i class="ri-arrow-right-line ms-1"></i>
                </button>
            </div>
        </div>
    @endif

    @if($step === 2 && $entity)
        {{-- Paso 2: Órdenes (cabecera entidad + tabla SEPA) --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="fe-briefcase font-24 text-muted"></i>
            </div>
            <div>
                <h5 class="mb-0">{{ $entity->name }}</h5>
                <small class="text-muted">{{ $entity->province ?? $entity->city ?? '—' }}</small>
            </div>
        </div>
        <h4 class="mb-0 mt-1">Órdenes de pago SEPA</h4>
        <small><i>Selecciona una orden para ver el detalle (beneficiarios) en el paso 3</i></small>

        <div class="table-responsive mt-3">
            <table class="table table-hover table-centered mb-0" id="tabla-ope-sepa">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Orden ID</th>
                        <th>Fecha ejecución</th>
                        <th>Nº transacciones</th>
                        <th>Importe total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sepaOrders as $order)
                        <tr class="selectable-row-ope" style="cursor: pointer;" data-order-id="{{ $order->id }}">
                            <td class="text-center">
                                <input type="radio" name="order_id_ope" value="{{ $order->id }}" class="form-check-input" aria-label="Seleccionar orden">
                            </td>
                            <td>#TR{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $order->execution_date ? $order->execution_date->format('d/m/Y') : '—' }}</td>
                            <td>{{ $order->number_of_transactions ?? 0 }}</td>
                            <td>{{ number_format($order->control_sum ?? 0, 2, ',', '.') }} €</td>
                            <td>
                                @php
                                    $statusLabel = match($order->status ?? '') {
                                        'draft' => 'Borrador',
                                        'descargado', 'generated' => 'Descargado',
                                        'listo' => 'Listo',
                                        default => $order->status ?: 'Borrador',
                                    };
                                    $statusClass = match($order->status ?? '') {
                                        'draft' => 'secondary',
                                        'descargado', 'generated' => 'info',
                                        'listo' => 'success',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                @if($order->beneficiaries->isNotEmpty())
                                    <a href="{{ route('sepa-payments.generate-xml', $order->id) }}" class="btn btn-sm btn-light" title="Descargar XML" onclick="event.stopPropagation();"><i class="fe-download"></i></a>
                                @endif
                                @if(in_array($order->status ?? '', ['descargado', 'generated']))
                                    <form action="{{ route('sepa-payments.mark-ready', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Marcar como Listo (pago realizado)?');" onclick="event.stopPropagation();">
                                        @csrf
                                        <input type="hidden" name="redirect_to" value="configuration">
                                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                                        <button type="submit" class="btn btn-sm btn-success" title="Marcar como Listo (pago realizado)"><i class="fe-check"></i></button>
                                    </form>
                                @endif
                                <form action="{{ route('sepa-payments.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta orden de pago?');" onclick="event.stopPropagation();">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="configuration">
                                    <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Eliminar"><i class="fe-trash-2"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No hay órdenes de pago para esta entidad.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3 align-items-center">
            <a href="{{ url('/configuration?section=ordenes-pago-entidades&step=1') }}" class="btn btn-md btn-dark" style="border-radius: 30px;">
                <i class="ri-arrow-left-line me-1"></i> Atrás
            </a>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('ordenes-pago-entidades.nueva-orden', ['entity_id' => $entity->id]) }}" class="btn btn-md btn-outline-primary" style="border-radius: 30px;">
                    <i class="ri-add-line me-1"></i> Nueva orden SEPA
                </a>
                <a href="{{ url('/configuration?section=ordenes-pago-entidades&step=3&entity_id=' . $entity->id) }}" class="btn btn-md btn-outline-secondary" style="border-radius: 30px;">
                    Pagos pendientes <i class="ri-arrow-right-line ms-1"></i>
                </a>
                <button type="button" id="btn-ver-detalle-ope" disabled style="border-radius: 30px; min-width: 140px; background-color: #e78307; color: #333; padding: 8px 16px; font-weight: bolder;" class="btn btn-md btn-light">
                    Ver detalle <i class="ri-arrow-right-line ms-1"></i>
                </button>
            </div>
        </div>
    @endif

    @if($step === 3 && $entity && isset($sepaOrder) && $sepaOrder)
        {{-- Paso 3: Detalle de la orden SEPA seleccionada (beneficiarios) --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="fe-briefcase font-24 text-muted"></i>
            </div>
            <div>
                <h5 class="mb-0">{{ $entity->name }}</h5>
                <small class="text-muted">Orden #TR{{ str_pad($sepaOrder->id, 4, '0', STR_PAD_LEFT) }}</small>
            </div>
        </div>
        <h4 class="mb-0 mt-1">Detalle de la orden de pago</h4>
        <small><i>Beneficiarios de la orden SEPA seleccionada</i></small>

        <div class="row mt-3 mb-2">
            <div class="col-md-3"><strong>Fecha ejecución:</strong> {{ $sepaOrder->execution_date ? $sepaOrder->execution_date->format('d/m/Y') : '—' }}</div>
            <div class="col-md-3"><strong>Nº transacciones:</strong> {{ $sepaOrder->number_of_transactions ?? 0 }}</div>
            <div class="col-md-3"><strong>Importe total:</strong> {{ number_format($sepaOrder->control_sum ?? 0, 2, ',', '.') }} €</div>
            @php
            $sepaStatusLabel = match($sepaOrder->status ?? '') {
                'draft' => 'Borrador',
                'descargado', 'generated' => 'Descargado',
                'listo' => 'Listo',
                default => $sepaOrder->status ?: 'Borrador',
            };
            $sepaStatusClass = match($sepaOrder->status ?? '') {
                'draft' => 'secondary',
                'descargado', 'generated' => 'info',
                'listo' => 'success',
                default => 'secondary',
            };
        @endphp
            <div class="col-md-3"><strong>Estado:</strong> <span class="badge bg-{{ $sepaStatusClass }}">{{ $sepaStatusLabel }}</span></div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-centered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>End to End ID</th>
                        <th>Nombre</th>
                        <th>NIF/CIF</th>
                        <th>IBAN</th>
                        <th>Importe</th>
                        <th>Moneda</th>
                        <th>Remesa</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sepaOrder->beneficiaries as $beneficiary)
                        <tr>
                            <td>{{ $beneficiary->end_to_end_id }}</td>
                            <td>{{ $beneficiary->creditor_name }}</td>
                            <td>{{ $beneficiary->creditor_nif_cif ?? '—' }}</td>
                            <td>{{ $beneficiary->creditor_iban }}</td>
                            <td>{{ number_format($beneficiary->amount, 2, ',', '.') }} €</td>
                            <td>{{ $beneficiary->currency }}</td>
                            <td>{{ $beneficiary->remittance_info ?? '—' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-light text-danger btn-eliminar-beneficiary"
                                    data-beneficiary-id="{{ $beneficiary->id }}"
                                    data-creditor-name="{{ e($beneficiary->creditor_name) }}"
                                    data-amount="{{ number_format($beneficiary->amount, 2, ',', '.') }}"
                                    data-iban-suffix="{{ strlen($beneficiary->creditor_iban ?? '') >= 4 ? substr($beneficiary->creditor_iban, -4) : '****' }}"
                                    data-has-collection="{{ $beneficiary->participation_collection_id ? '1' : '0' }}"
                                    title="Eliminar de la orden">
                                    <i class="fe-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th>{{ number_format($sepaOrder->control_sum ?? 0, 2, ',', '.') }} €</th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Modal eliminar beneficiario (cuenta vinculada) de la orden --}}
        <div class="modal fade" id="modalEliminarBeneficiary" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Eliminar cuenta de la orden</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2" id="modal-eliminar-beneficiary-texto"></p>
                        <p class="mb-0 small text-muted">
                            Esta operación una vez aceptada <strong>no se puede deshacer</strong>. El importe correspondiente a la participación o participaciones volverá a estar <strong>disponible</strong> para poder ser administrado por el usuario y se le notificará del cambio de estado de sus participaciones.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <form id="form-eliminar-beneficiary" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                            <input type="hidden" name="order_id" value="{{ $sepaOrder->id }}">
                            <button type="submit" class="btn btn-primary" style="background-color: #e78307; border-color: #e78307; color: #333;">Aceptar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ url('/configuration?section=ordenes-pago-entidades&step=2&entity_id=' . $entity->id) }}" class="btn btn-md btn-dark" style="border-radius: 30px;">
                <i class="ri-arrow-left-line me-1"></i> Volver a órdenes
            </a>
            <div class="d-flex gap-2 flex-wrap">
                @if($sepaOrder->beneficiaries->isNotEmpty())
                    <a href="{{ route('sepa-payments.generate-xml', $sepaOrder->id) }}" class="btn btn-md btn-light" style="border-radius: 30px;"><i class="fe-download me-1"></i> Descargar XML</a>
                @endif
                @if(in_array($sepaOrder->status ?? '', ['descargado', 'generated']))
                    <form action="{{ route('sepa-payments.mark-ready', $sepaOrder->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Marcar esta orden como Listo (pago realizado)?');">
                        @csrf
                        <input type="hidden" name="redirect_to" value="configuration">
                        <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                        <input type="hidden" name="order_id" value="{{ $sepaOrder->id }}">
                        <button type="submit" class="btn btn-md btn-success" style="border-radius: 30px;"><i class="fe-check me-1"></i> Marcar como Listo</button>
                    </form>
                @endif
                <a href="{{ url('/configuration?section=ordenes-pago-entidades&step=3&entity_id=' . $entity->id) }}" class="btn btn-md btn-outline-secondary" style="border-radius: 30px;">Pagos pendientes</a>
            </div>
        </div>
    @endif

    @if($step === 3 && $entity && (!isset($sepaOrder) || !$sepaOrder))
        {{-- Paso 3: Listado participation_collections (pagos pendientes) --}}
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                <i class="fe-briefcase font-24 text-muted"></i>
            </div>
            <div>
                <h5 class="mb-0">{{ $entity->name }}</h5>
                <small class="text-muted">{{ $entity->province ?? $entity->city ?? '—' }}</small>
            </div>
        </div>
        <h4 class="mb-0 mt-1">Pagos pendientes</h4>
        <small><i>Peticiones de cobro que aún no están incluidas en ninguna orden SEPA. Crear orden SEPA para generarla con estos items y descargar el XML.</i></small>

        <div class="row mt-3 mb-2 g-2">
            <div class="col-md-3">
                <label class="form-label small">Provincia</label>
                <select class="form-select form-select-sm"><option>Todas</option></select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Localidad</label>
                <select class="form-select form-select-sm"><option>Todas</option></select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Estatus</label>
                <select class="form-select form-select-sm"><option>Todos</option></select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Busqueda</label>
                <input type="search" class="form-control form-control-sm" placeholder="Busqueda">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-centered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario ID</th>
                        <th>Fecha Petición</th>
                        <th>Número de cuenta</th>
                        <th>Importe</th>
                        <th>Petición</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($collections as $col)
                        <tr data-collection-id="{{ $col->id }}" data-collection-amount="{{ number_format($col->importe_total, 2, ',', '') }}" data-collection-date="{{ $col->created_at->format('d/m/Y') }}" data-collection-iban="{{ $col->iban }}">
                            <td>#US{{ str_pad($col->user_id ?? $col->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $col->created_at->format('d/m/Y') }}</td>
                            <td>{{ strlen($col->iban ?? '') >= 8 ? substr(preg_replace('/\s+/', '', $col->iban), 0, 4) . str_repeat('*', strlen(preg_replace('/\s+/', '', $col->iban)) - 8) . substr(preg_replace('/\s+/', '', $col->iban), -4) : ($col->iban ?? '—') }}</td>
                            <td>{{ number_format($col->importe_total, 2, ',', '') }} €</td>
                            <td>WEB - IP: 82.129.80.111</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-light text-danger btn-eliminar-collection" title="Eliminar">
                                    <i class="fe-trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    @if($collections->isEmpty())
                        <tr><td colspan="6" class="text-center text-muted">No hay pagos pendientes para esta entidad (o ya están incluidos en una orden SEPA).</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ url('/configuration?section=ordenes-pago-entidades&step=2&entity_id=' . $entity->id) }}" class="btn btn-md btn-dark" style="border-radius: 30px;">
                <i class="ri-arrow-left-line me-1"></i> Órdenes SEPA
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('ordenes-pago-entidades.nueva-orden', ['entity_id' => $entity->id]) }}" class="btn btn-md btn-outline-primary" style="border-radius: 30px;">
                    <i class="ri-add-line me-1"></i> Nueva orden SEPA
                </a>
                <form action="{{ route('ordenes-pago-entidades.crear-sepa') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                    <button type="submit" class="btn btn-md btn-light" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bolder;" {{ $collections->isEmpty() ? 'disabled' : '' }}>
                        Crear orden SEPA (desde pendientes)
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

{{-- Modal Eliminación de Orden --}}
<div class="modal fade" id="modalEliminarOrden" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminación de Orden</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2" id="modal-eliminar-texto"></p>
                <p class="mb-0 small text-muted">
                    Esta operación una vez aceptada <strong>no se puede deshacer</strong>. El importe correspondiente a la participación o participaciones volverá a estar disponible para poder ser administrado por el usuario y se le notificará del cambio de estado de sus participaciones.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <form id="form-eliminar-collection" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="entity_id" value="{{ $entity->id ?? '' }}">
                    <button type="submit" class="btn btn-primary" style="background-color: #e78307; border-color: #e78307; color: #333;">Aceptar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($step === 3 && $entity && isset($sepaOrder) && $sepaOrder)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalBenef = document.getElementById('modalEliminarBeneficiary');
    var formBenef = document.getElementById('form-eliminar-beneficiary');
    var textoBenef = document.getElementById('modal-eliminar-beneficiary-texto');
    if (modalBenef && formBenef && textoBenef) {
        var modalInstance = new bootstrap.Modal(modalBenef);
        var baseUrl = '{{ url("/configuration/ordenes-pago-entidades/beneficiaries") }}';
        document.querySelectorAll('.btn-eliminar-beneficiary').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.dataset.beneficiaryId;
                var name = this.dataset.creditorName || '';
                var amount = this.dataset.amount || '';
                var ibanSuffix = this.dataset.ibanSuffix || '****';
                textoBenef.textContent = 'Vas a eliminar la cuenta de la orden: ' + name + ' con número de cuenta terminado en ' + ibanSuffix + ' con un importe de ' + amount + ' €. La solicitud de cobro quedará de nuevo disponible para poder volver a cobrarla.';
                formBenef.action = baseUrl + '/' + id;
                modalInstance.show();
            });
        });
    }
});
</script>
@endif

@if($step === 2 && $entity)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var entityId = {{ $entity->id }};
    var baseUrl = '{{ url("/configuration") }}';
    var btnVerDetalle = document.getElementById('btn-ver-detalle-ope');
    var rows = document.querySelectorAll('.selectable-row-ope');

    function setSelectedOrder(orderId) {
        document.querySelectorAll('.selectable-row-ope').forEach(function(r) {
            r.classList.remove('table-active');
            var radio = r.querySelector('input[name="order_id_ope"]');
            if (radio) radio.checked = (parseInt(r.dataset.orderId, 10) === parseInt(orderId, 10));
        });
        var row = document.querySelector('.selectable-row-ope[data-order-id="' + orderId + '"]');
        if (row) row.classList.add('table-active');
        if (btnVerDetalle) btnVerDetalle.disabled = !orderId;
    }

    rows.forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('form')) return;
            var orderId = row.dataset.orderId;
            setSelectedOrder(orderId);
        });
    });
    document.querySelectorAll('input[name="order_id_ope"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            setSelectedOrder(this.value);
        });
    });

    if (btnVerDetalle) {
        btnVerDetalle.addEventListener('click', function() {
            var radio = document.querySelector('input[name="order_id_ope"]:checked');
            if (!radio) return;
            var orderId = radio.value;
            window.location.href = baseUrl + '?section=ordenes-pago-entidades&step=3&entity_id=' + entityId + '&order_id=' + orderId;
        });
    }
});
</script>
@endif

@if($step === 3 && isset($entity))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('modalEliminarOrden');
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);
    var formEliminar = document.getElementById('form-eliminar-collection');
    var modalTexto = document.getElementById('modal-eliminar-texto');
    var entityId = '{{ $entity->id ?? "" }}';

    document.querySelectorAll('.btn-eliminar-collection').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var row = this.closest('tr');
            if (!row || !row.dataset.collectionId) return;
            var id = row.dataset.collectionId;
            var date = row.dataset.collectionDate || '';
            var iban = row.dataset.collectionIban || '';
            var amount = row.dataset.collectionAmount || '';
            var ibanSuffix = iban.length > 4 ? iban.slice(-4) : '****';
            modalTexto.textContent = 'Vas a eliminar la orden de transferencia #US' + String(id).padStart(4, '0') + ' del ' + date + ' con número de cuenta terminado en ' + ibanSuffix + ' con un importe de ' + amount + ' €.';
            formEliminar.action = '{{ url("/configuration/ordenes-pago-entidades/collections") }}/' + id + '?entity_id=' + entityId;
            modal.show();
        });
    });
});
</script>
@endif
