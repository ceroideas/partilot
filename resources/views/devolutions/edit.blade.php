@extends('layouts.layout')

@section('title', 'Editar Devolución')

@section('content')
<div class="main-content">
    <div class="page-content">
<div class="container-fluid">
            <!-- start page title -->
    <div class="row">
        <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Editar Devolución</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('devolutions.index') }}">Devoluciones</a></li>
                                <li class="breadcrumb-item active">Editar</li>
                    </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                            <!-- Información de la Devolución -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Información de la Devolución</h6>
                                            <p><strong>ID:</strong> <span id="devolution-id">{{ $devolution->id }}</span></p>
                                            <p><strong>Entidad:</strong> <span id="devolution-entity">{{ $devolution->entity->name }}</span></p>
                                            <p><strong>Sorteo:</strong> <span id="devolution-lottery">{{ $devolution->lottery->name }}</span></p>
                                            <p><strong>Fecha:</strong> <span id="devolution-date">{{ $devolution->devolution_date->format('d/m/Y') }}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Resumen de Participaciones</h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h4 class="text-primary" id="total-participations">{{ $devolution->total_participations }}</h4>
                                                        <small>Total Participaciones</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h4 class="text-warning" id="returned-participations">{{ $devolution->details()->where('action', 'devolver')->count() }}</h4>
                                                        <small>Devueltas</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h4 class="text-success" id="sold-participations">{{ $devolution->details()->where('action', 'vender')->count() }}</h4>
                                                        <small>Vendidas</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h4 class="text-info" id="total-liquidation">{{ number_format($devolution->details()->where('action', 'vender')->with('participation.set')->get()->sum(function($detail) { return $detail->participation->set->played_amount ?? 0; }), 2) }}€</h4>
                                                        <small>Total Liquidación</small>
                                                    </div>
                        </div>
                    </div>
                            </div>
                            </div>
                        </div>
                            </div>

                            <!-- Resumen Devolución -->
                            <div class="card mb-3">
                            <div class="card-body">
                                    <h5 class="card-title">Resumen Devolución</h5>
                                    <small class="text-muted">Resumen Devolución Entidad</small>
                                    
                                    <div class="text-center my-3">
                                        <img src="{{url('assets/ticket.svg')}}" alt="" width="60px">
                                        <div class="mt-2">
                                            <strong id="liquidacion-ticket-number">Devolución #{{ $devolution->id }}</strong>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <strong>Total Participaciones:</strong>
                                                <span id="liquidacion-total-participaciones">{{ $devolution->total_participations }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Ventas registradas:</strong>
                                                <span id="liquidacion-ventas-registradas">{{ $devolution->details()->where('action', 'vender')->count() }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <strong>Participaciones Devueltas:</strong>
                                                <span id="liquidacion-participaciones-devueltas">{{ $devolution->details()->where('action', 'devolver')->count() }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Disponibles:</strong>
                                                <span id="liquidacion-disponibles">0</span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                
                            <!-- Liquidación Actual -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Liquidación Actual</h5>
                                <div class="row">
                                        <div class="col-4">
                                            <div class="mb-2">
                                                <strong>Total Liquidación:</strong>
                                                <span id="liquidacion-total-liquidacion">{{ number_format($devolution->details()->where('action', 'vender')->with('participation.set')->get()->sum(function($detail) { return $detail->participation->set->played_amount ?? 0; }), 2) }}€</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="mb-2">
                                                <strong>Pagos Registrados:</strong>
                                                <span id="liquidacion-pagos-registrados">{{ number_format($devolution->payments()->sum('amount'), 2) }}€</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="mb-2">
                                                <strong>Total a Pagar:</strong>
                                                <span id="liquidacion-total-pagar">{{ number_format($devolution->details()->where('action', 'vender')->with('participation.set')->get()->sum(function($detail) { return $detail->participation->set->played_amount ?? 0; }) - $devolution->payments()->sum('amount'), 2) }}€</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formas de Pago -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Formas de Pago</h5>
                                    <small class="text-muted">Puedes registrar múltiples formas de pago</small>
                                    
                                    <div class="row mt-3">
                                        <div class="col-8">
                                            <!-- Pago en Efectivo -->
                                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                                <div class="me-3">
                                                    <i class="ri-wallet-line text-success" style="font-size: 24px;"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Pago en Efectivo</strong>
                                                </div>
                                                <div class="col-3">
                                                    <input type="number" step="0.01" class="form-control payment-input" placeholder="0.00" id="pago-efectivo-monto">
                                                </div>
                                            </div>

                                            <!-- Pago por Bizum -->
                                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                                <div class="me-3">
                                                    <i class="ri-percent-line text-info" style="font-size: 24px;"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Pago por Bizum</strong>
                                                </div>
                                                <div class="col-3">
                                                    <input type="number" step="0.01" class="form-control payment-input" placeholder="0.00" id="pago-bizum-monto">
                                                </div>
                                            </div>

                                            <!-- Pago por Transferencia -->
                                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                                <div class="me-3">
                                                    <i class="ri-building-line text-primary" style="font-size: 24px;"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong>Pago por Transferencia</strong>
                                                </div>
                                                <div class="col-3">
                                                    <input type="number" step="0.01" class="form-control payment-input" placeholder="0.00" id="pago-transferencia-monto">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="text-center">
                                                <div class="border rounded p-3 mb-3 bg-light">
                                                    <small class="text-muted">Total a Pagar</small>
                                                    <div class="text-danger h4" id="liquidacion-importe-total">{{ number_format($devolution->details()->where('action', 'vender')->with('participation.set')->get()->sum(function($detail) { return $detail->participation->set->played_amount ?? 0; }) - $devolution->payments()->sum('amount'), 2) }}€</div>
                                                </div>
                                                <div class="border rounded p-3 mb-3 bg-success bg-opacity-10">
                                                    <small class="text-muted">Total a Pagar Ahora</small>
                                                    <div class="text-success h4" id="total-pagar-ahora">0,00€</div>
                                                </div>
                                                <div class="border rounded p-3 mb-3" id="pendiente-container">
                                                    <small class="text-muted">Quedará Pendiente</small>
                                                    <div class="h5" id="total-pendiente">{{ number_format($devolution->details()->where('action', 'vender')->with('participation.set')->get()->sum(function($detail) { return $detail->participation->set->played_amount ?? 0; }) - $devolution->payments()->sum('amount'), 2) }}€</div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Botones de Acción -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('devolutions.index') }}" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line"></i> Volver al Listado
                                </a>
                                <button type="button" class="btn btn-warning" id="btn-aceptar-liquidacion" style="border-radius: 30px; padding: 10px 30px;">
                                    <i class="ri-add-line"></i> Agregar Pagos
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
jQuery(document).ready(function($) {
    console.log('Iniciando script de edición con jQuery');
    
    const devolutionId = {{ $devolution->id }};
    console.log('Devolución ID:', devolutionId);

    // Función para actualizar el total a pagar ahora
    function actualizarTotalPagarAhora() {
        console.log('Actualizando totales...');
        const efectivoMonto = parseFloat($('#pago-efectivo-monto').val()) || 0;
        const bizumMonto = parseFloat($('#pago-bizum-monto').val()) || 0;
        const transferenciaMonto = parseFloat($('#pago-transferencia-monto').val()) || 0;
        
        console.log('Montos:', { efectivo: efectivoMonto, bizum: bizumMonto, transferencia: transferenciaMonto });
        
        const totalPagarAhora = efectivoMonto + bizumMonto + transferenciaMonto;
        $('#total-pagar-ahora').text(totalPagarAhora.toFixed(2) + '€');
        
        // Calcular pendiente
        const totalAPagarText = $('#liquidacion-importe-total').text();
        const totalAPagar = parseFloat(totalAPagarText.replace('€', '').replace(',', '.').trim()) || 0;
        const pendiente = totalAPagar - totalPagarAhora;
        
        console.log('Total a pagar:', totalAPagar, 'Pendiente:', pendiente);
        
        $('#total-pendiente').text(pendiente.toFixed(2) + '€');
        
        // Cambiar color según si está completo o no
        if (pendiente <= 0 && totalPagarAhora > 0) {
            $('#total-pendiente').removeClass('text-warning').addClass('text-success');
        } else if (totalPagarAhora > 0) {
            $('#total-pendiente').removeClass('text-success').addClass('text-warning');
        } else {
            $('#total-pendiente').removeClass('text-success text-warning');
        }
    }
    
    // Event listeners para actualizar total al cambiar montos
    $('#pago-efectivo-monto, #pago-bizum-monto, #pago-transferencia-monto').on('input keyup change', function() {
        console.log('Input detectado en:', $(this).attr('id'));
        actualizarTotalPagarAhora();
    });

    // Event listener para aceptar liquidación
    $('#btn-aceptar-liquidacion').click(function() {
        // Recopilar todos los pagos
        const pagos = [];
        
        // Pago en efectivo
        const efectivoMonto = parseFloat($('#pago-efectivo-monto').val()) || 0;
        if (efectivoMonto > 0) {
            pagos.push({
                payment_method: 'efectivo',
                amount: efectivoMonto
            });
        }
        
        // Pago por Bizum
        const bizumMonto = parseFloat($('#pago-bizum-monto').val()) || 0;
        if (bizumMonto > 0) {
            pagos.push({
                payment_method: 'bizum',
                amount: bizumMonto
            });
        }
        
        // Pago por transferencia
        const transferenciaMonto = parseFloat($('#pago-transferencia-monto').val()) || 0;
        if (transferenciaMonto > 0) {
            pagos.push({
                payment_method: 'transferencia',
                amount: transferenciaMonto
            });
        }

        if (pagos.length === 0) {
            mostrarMensaje('Debes ingresar al menos un monto de pago', 'warning');
            return;
        }

        // Preparar datos para los pagos (ahora es un array)
        const paymentData = {
            pagos: pagos,
            notes: 'Pago agregado desde edición',
            _token: '{{ csrf_token() }}'
        };

        $(this).prop('disabled', true).text('Procesando...');

        $.ajax({
            url: `/devolutions/${devolutionId}/payments`,
            method: 'POST',
            data: paymentData,
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('Pago agregado correctamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    mostrarMensaje(response.message || 'Error al agregar el pago', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar pago:', error);
                mostrarMensaje('Error al guardar el pago', 'error');
            },
            complete: function() {
                $('#btn-aceptar-liquidacion').prop('disabled', false).text('Agregar Pagos');
            }
        });
    });
    
    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo) {
        const alertClass = tipo === 'success' ? 'alert-success' : 
                          tipo === 'warning' ? 'alert-warning' : 
                          tipo === 'error' ? 'alert-danger' : 'alert-info';
       
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.page-title-box').after(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    
    console.log('Script de edición cargado completamente');
});
</script>
@endsection