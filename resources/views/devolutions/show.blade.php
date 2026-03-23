@extends('layouts.layout')

@section('title','Ver Devolución')

@section('content')

<style>
    .form-wizard-element, .form-wizard-element label {
        cursor: pointer;
    }
    .form-check-input:checked {
        border-color: #333;
    }

    /* Estilos para el resumen de devolución (formato compacto como resumen vendedor) */
    .resumen-devolucion {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .resumen-devolucion .resumen-titulo {
        font-weight: bold;
        margin-bottom: 4px;
    }

    .resumen-devolucion .resumen-subtitulo {
        color: #6c757d;
        font-size: 0.95em;
        margin-bottom: 16px;
    }

    .resumen-linea {
        padding: 6px 0;
        border-bottom: 1px solid #e9ecef;
        line-height: 1.5;
    }

    .resumen-linea:last-child {
        border-bottom: none;
    }

    .resumen-linea .resumen-etiqueta {
        font-weight: 500;
        color: #495057;
        margin-right: 6px;
    }

    .resumen-linea .resumen-valor {
        font-weight: 600;
    }

    .resumen-linea .resumen-importe {
        color: #6c757d;
        font-weight: normal;
        margin-left: 4px;
    }

    /* Estilos para las participaciones */
    .participacion-item {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s ease;
    }

    .participacion-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .participacion-icon {
        width: 40px;
        height: 40px;
        background: #333;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .participacion-info {
        flex-grow: 1;
    }

    .participacion-numero {
        font-weight: bold;
        color: #333;
        margin-bottom: 4px;
    }

    .participacion-fecha {
        color: #666;
        font-size: 0.9em;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .participacion-estado {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        font-weight: bold;
        margin-top: 4px;
        display: inline-block;
    }

    .estado-devuelto {
        background: #dc3545;
        color: white;
    }

    .estado-vendido {
        background: #28a745;
        color: white;
    }

    .grid-participaciones {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('devolutions.index') }}">Devoluciones</a></li>
                        <li class="breadcrumb-item active">Ver Devolución</li>
                    </ol>
                </div>
                <h4 class="page-title">Devoluciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">
                            Detalles de la Devolución
                        </h4>
                        <div>
                            @unless(Auth::user()->isEntityPanelReadOnly())
                            <a href="{{ route('devolutions.edit', $devolution->id) }}" class="btn btn-warning me-2" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold;">
                                <i class="ri-edit-line me-2"></i>Editar
                            </a>
                            @endunless
                            <a href="{{ route('devolutions.index') }}" class="btn btn-secondary" style="border-radius: 30px;">
                                <i class="ri-arrow-left-line me-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <br>

                    <!-- Resumen de la devolución (formato compacto como captura resumen vendedor) -->
                    @php
                        $devueltas = $devolution->details()->whereIn('action', ['devolver', 'devolver_vendedor'])->count();
                        $ventasRegistradas = $devolution->details()->where('action', 'vender')->count();
                        $disponibles = $devolution->total_participations - $devueltas - $ventasRegistradas;
                        // Precio por participación = played_amount (ej. 3€) + donation_amount (ej. 1€). Son importes POR participación, no del set completo.
                        $precioPorParticipacion = null;
                        $primerSet = $devolution->details()->with('participation.set')->first()?->participation?->set;
                        if ($primerSet) {
                            $precioPorParticipacion = (float) ($primerSet->played_amount ?? 0) + (float) ($primerSet->donation_amount ?? 0);
                            if ($precioPorParticipacion <= 0) {
                                $precioPorParticipacion = null;
                            }
                        }
                        $fmt = function($n) use ($precioPorParticipacion) {
                            $importe = $precioPorParticipacion !== null ? $n * $precioPorParticipacion : null;
                            return $importe !== null ? $n . ' (' . number_format($importe, 2, ',', '.') . '€)' : (string) $n;
                        };
                    @endphp
                    <div class="resumen-devolucion">
                        <div class="resumen-titulo">Resumen Devolución</div>
                        <div class="resumen-subtitulo">{{ $devolution->seller_id ? 'Resumen Devolución Vendedor' : 'Resumen Devolución Entidad' }}</div>

                        <div class="resumen-linea">
                            <span class="resumen-etiqueta">Total Participaciones:</span>
                            <span class="resumen-valor">{{ $fmt($devolution->total_participations) }}</span>
                        </div>
                        <div class="resumen-linea">
                            <span class="resumen-etiqueta">Participaciones Devueltas:</span>
                            <span class="resumen-valor text-danger">{{ $fmt($devueltas) }}</span>
                        </div>
                        <div class="resumen-linea">
                            <span class="resumen-etiqueta">Ventas registradas:</span>
                            <span class="resumen-valor text-success">{{ $fmt($ventasRegistradas) }}</span>
                        </div>
                        <div class="resumen-linea">
                            <span class="resumen-etiqueta">Disponibles:</span>
                            <span class="resumen-valor text-info">{{ $fmt($disponibles) }}</span>
                        </div>
                    </div>

                    <!-- Datos adicionales en bloque compacto -->
                    <div class="resumen-devolucion mt-3">
                        <div class="resumen-titulo mb-2">Información general</div>
                        <div class="resumen-linea"><span class="resumen-etiqueta">ID Devolución:</span> <span class="resumen-valor">{{ $devolution->id }}</span></div>
                        <div class="resumen-linea"><span class="resumen-etiqueta">Entidad:</span> <span class="resumen-valor">{{ $devolution->entity->name ?? 'N/A' }}</span></div>
                        <div class="resumen-linea"><span class="resumen-etiqueta">Sorteo:</span> <span class="resumen-valor">{{ $devolution->lottery->name ?? 'N/A' }}</span></div>
                        <div class="resumen-linea"><span class="resumen-etiqueta">Vendedor:</span> <span class="resumen-valor">{{ $devolution->seller ? ($devolution->seller->full_name ?: $devolution->seller->name) : 'Sin vendedor' }}</span></div>
                        <div class="resumen-linea"><span class="resumen-etiqueta">Fecha de Procesamiento:</span> <span class="resumen-valor">{{ \Carbon\Carbon::parse($devolution->devolution_date)->format('d/m/Y') }}</span></div>
                    </div>

                    <!-- Resumen de Pagos -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Resumen de Pagos</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalLiquidacion = $devolution->total_liquidation !== null
                                    ? (float) $devolution->total_liquidation
                                    : $devolution->details()
                                        ->where('action', 'vender')
                                        ->with('participation.set')
                                        ->get()
                                        ->sum(function($detail) {
                                            return $detail->participation->set->played_amount ?? 0;
                                        });
                                $totalPagado = $devolution->payments()->sum('amount');
                                $pendiente = $totalLiquidacion - $totalPagado;
                            @endphp
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <small class="text-muted">Total Liquidación</small>
                                        <h4 class="text-primary mb-0">{{ number_format($totalLiquidacion, 2) }}€</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center bg-success bg-opacity-10">
                                        <small class="text-muted">Total Pagado</small>
                                        <h4 class="text-success mb-0">{{ number_format($totalPagado, 2) }}€</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center {{ $pendiente > 0 ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }}">
                                        <small class="text-muted">Pendiente</small>
                                        <h4 class="mb-0 {{ $pendiente > 0 ? 'text-warning' : 'text-success' }}">{{ number_format($pendiente, 2) }}€</h4>
                                    </div>
                                </div>
                            </div>
                            
                            @if($devolution->payments->count() > 0)
                                <h6 class="mb-3">Detalle de Pagos</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Método</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($devolution->payments as $payment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($payment->payment_method == 'efectivo')
                                                            <i class="ri-wallet-line text-success"></i> Efectivo
                                                        @elseif($payment->payment_method == 'bizum')
                                                            <i class="ri-smartphone-line text-info"></i> Bizum
                                                        @elseif($payment->payment_method == 'transferencia')
                                                            <i class="ri-bank-line text-primary"></i> Transferencia
                                                        @else
                                                            {{ ucfirst($payment->payment_method) }}
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ number_format($payment->amount, 2) }}€</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="2">TOTAL PAGADO</th>
                                                <th class="text-end text-success">{{ number_format($totalPagado, 2) }}€</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="empty-tables">
                                        <div>
                                            <i class="ri-money-dollar-circle-line" style="font-size: 48px; opacity: 0.3;"></i>
                                        </div>
                                        <h5 class="mb-0 mt-2">No hay pagos registrados</h5>
                                        <small class="text-muted">Aún no se han registrado pagos para esta devolución</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
