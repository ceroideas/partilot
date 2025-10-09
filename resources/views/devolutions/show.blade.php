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
                            <a href="{{ route('devolutions.edit', $id) }}" class="btn btn-warning me-2" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold;">
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
                            <span id="devolution-id">{{ $id }}</span>
                        </div>
                        <div class="resumen-item">
                            <span>Entidad:</span>
                            <span id="entity-name">Cargando...</span>
                        </div>
                        <div class="resumen-item">
                            <span>Sorteo:</span>
                            <span id="lottery-name">Cargando...</span>
                        </div>
                        <div class="resumen-item">
                            <span>Vendedor:</span>
                            <span id="seller-name">Cargando...</span>
                        </div>
                        <div class="resumen-item">
                            <span>Fecha de Procesamiento:</span>
                            <span id="processing-date">Cargando...</span>
                        </div>
                        <div class="resumen-item">
                            <span>Total Participaciones:</span>
                            <span id="total-participations">Cargando...</span>
                        </div>
                        <div class="resumen-item">
                            <span>Participaciones Devueltas:</span>
                            <span id="returned-count" class="text-danger">Cargando...</span>
                        </div>
                        <div class="resumen-item">
                            <span>Participaciones Vendidas:</span>
                            <span id="sold-count" class="text-success">Cargando...</span>
                        </div>
                    </div>

                    <!-- Participaciones de la devolución -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Participaciones</h5>
                        </div>
                        <div class="card-body">
                            <div id="loading-participations" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando participaciones...</p>
                            </div>
                            
                            <div id="participations-content" style="display: none;">
                                <div class="grid-participaciones" id="grid-participations">
                                    <!-- Las participaciones se cargarán aquí -->
                                </div>
                            </div>

                            <div id="no-participations" style="display: none;" class="text-center py-4">
                                <div class="empty-tables">
                                    <div>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="" width="80px" style="margin-top: 10px; opacity: 0.3;">
                                    </div>
                                    <h3 class="mb-0">No hay participaciones</h3>
                                    <small class="text-muted">Esta devolución no tiene participaciones asociadas</small>
                                </div>
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
$(document).ready(function() {
    const devolutionId = {{ $id }};
    
    // Cargar información de la devolución
    cargarInformacionDevolucion();
    
    // Cargar participaciones
    cargarParticipaciones();
    
    function cargarInformacionDevolucion() {
        // Por ahora, cargar información básica
        // En una implementación real, harías una llamada AJAX al backend
        $('#entity-name').text('Entidad de Prueba');
        $('#lottery-name').text('Sorteo de Prueba');
        $('#seller-name').text('Vendedor de Prueba');
        $('#processing-date').text(new Date().toLocaleDateString('es-ES'));
        $('#total-participations').text('0');
        $('#returned-count').text('0');
        $('#sold-count').text('0');
    }
    
    function cargarParticipaciones() {
        // Simular carga de participaciones
        // En una implementación real, harías una llamada AJAX al backend
        setTimeout(() => {
            $('#loading-participations').hide();
            
            // Simular que no hay participaciones por ahora
            $('#no-participations').show();
            
            // Si hubiera participaciones, se mostrarían así:
            /*
            const participaciones = [
                {
                    id: 1,
                    participation_code: '1/00001',
                    status: 'devuelto',
                    date: '2024-01-15',
                    time: '14:30:00'
                },
                {
                    id: 2,
                    participation_code: '1/00002',
                    status: 'vendido',
                    date: '2024-01-15',
                    time: '14:31:00'
                }
            ];
            
            mostrarParticipaciones(participaciones);
            */
        }, 1500);
    }
    
    function mostrarParticipaciones(participaciones) {
        if (participaciones.length === 0) {
            $('#no-participations').show();
            return;
        }
        
        $('#participations-content').show();
        
        const gridHtml = participaciones.map(participation => {
            const fecha = new Date(participation.date + 'T' + participation.time);
            const fechaStr = fecha.toLocaleDateString('es-ES');
            const horaStr = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            
            const estadoClass = participation.status === 'devuelto' ? 'estado-devuelto' : 'estado-vendido';
            const estadoText = participation.status === 'devuelto' ? 'Devuelto' : 'Vendido';
            
            return `
                <div class="participacion-item">
                    <div class="d-flex align-items-center">
                        <div class="participacion-icon">
                            <img src="{{url('assets/ticket.svg')}}" alt="" width="20px">
                        </div>
                        <div class="participacion-info">
                            <div class="participacion-numero">${participation.participation_code}</div>
                            <div class="participacion-fecha">
                                <i class="ri-calendar-line"></i>
                                <span>${fechaStr} - ${horaStr}h</span>
                            </div>
                            <span class="participacion-estado ${estadoClass}">${estadoText}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        $('#grid-participations').html(gridHtml);
    }
    
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
        
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>

@endsection
