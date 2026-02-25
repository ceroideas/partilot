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

    /* Estilos para el resumen de devolución */
    .resumen-devolucion {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .resumen-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .resumen-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.1em;
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
                            <a href="{{ route('devolutions.edit', $devolution->id) }}" class="btn btn-warning me-2" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold;">
                                <i class="ri-edit-line me-2"></i>Editar
                            </a>
                            <a href="{{ route('devolutions.index') }}" class="btn btn-secondary" style="border-radius: 30px;">
                                <i class="ri-arrow-left-line me-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <br>

                    <!-- Información de la devolución -->
                    <div class="resumen-devolucion">
                        <h5>Información General</h5>
                        <div class="resumen-item">
                            <span>ID Devolución:</span>
                            <span>{{ $devolution->id }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Entidad:</span>
                            <span>{{ $devolution->entity->name ?? 'N/A' }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Sorteo:</span>
                            <span>{{ $devolution->lottery->name ?? 'N/A' }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Vendedor:</span>
                            <span>{{ $devolution->seller->name ?? 'Sin vendedor' }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Fecha de Procesamiento:</span>
                            <span>{{ \Carbon\Carbon::parse($devolution->devolution_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Total Participaciones:</span>
                            <span>{{ $devolution->total_participations }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Participaciones Devueltas:</span>
                            <span class="text-danger">{{ $devolution->details()->where('action', 'devolver')->count() }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Ventas registradas:</span>
                            <span class="text-success">{{ $devolution->total_participations - $devolution->details()->where('action', 'devolver')->count() }}</span>
                        </div>
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
