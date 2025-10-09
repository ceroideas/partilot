@extends('layouts.layout')

@section('title','Nueva Devolución')

@section('content')

<style>
    .form-wizard-element, .form-wizard-element label {
        cursor: pointer;
    }
    
    /* Asegurar que las clases active sean visibles */
    .form-wizard-element.active {
        background-color: #cccccc !important;
        color: #111 !important;
        filter: invert(1) !important;
    }
    .form-check-input:checked {
        border-color: #333;
    }

    .devolucion-paso {
        transition: all 0.3s ease;
    }

    .devolucion-paso table {
        margin-top: 20px;
    }

    .devolucion-paso .btn-seleccionar {
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
    }

    .devolucion-paso .btn-volver {
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
    }

    /* Animación para transiciones entre pasos */
    .devolucion-paso.fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Estilos para las participaciones a devolver */
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
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        font-weight: bold;
        margin-top: 4px;
        display: inline-block;
    }

    .btn-eliminar-participacion {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-eliminar-participacion:hover {
        background: #c82333;
        transform: scale(1.1);
    }

    .grid-participaciones {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
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

    /* Estilos para liquidación */
    .liquidacion-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .liquidacion-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .liquidacion-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .liquidacion-icon {
        width: 50px;
        height: 50px;
        background: #f8f9fa;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .liquidacion-info h5 {
        margin: 0;
        color: #333;
    }

    .liquidacion-info small {
        color: #666;
    }

    .liquidacion-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .stat-item {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .stat-number {
        font-size: 1.5em;
        font-weight: bold;
        color: #333;
    }

    .stat-label {
        font-size: 0.8em;
        color: #666;
        margin-top: 5px;
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
                        <li class="breadcrumb-item active">Nueva Devolución</li>
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

                    <h4 class="header-title">
                        Nueva Devolución de Entidad
                    </h4>

                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <!-- Pasos del proceso -->
                            <ul class="form-card bs mb-3 nav">
                                <li class="nav-item">
                                    <div class="form-wizard-element active" id="step-1">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/entidad.svg')}}" alt="">
                                        <label>Seleccionar Entidad</label>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="form-wizard-element" id="step-2">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>Seleccionar Sorteo</label>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="form-wizard-element" id="step-3">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/vendedores.svg')}}" alt="">
                                        <label>Seleccionar Vendedor</label>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="form-wizard-element" id="step-4">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>Asignar Participaciones</label>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="form-wizard-element" id="step-5">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>Resumen Devolución</label>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="form-wizard-element" id="step-6">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>Liquidación</label>
                                    </div>
                                </li>
                            </ul>

                            <!-- Información de la entidad seleccionada -->
                            <div class="form-card bs mb-3" id="entity-info" style="display: none;">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="photo-preview-3">
                                            <i class="ri-building-line"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 text-center mt-2">
                                        <h3 class="mt-2 mb-0" id="entity-name">Entidad</h3>
                                        <i class="ri-map-pin-line"></i> <span id="entity-location">Ubicación</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del vendedor seleccionado -->
                            <div class="form-card bs mb-3" id="seller-info" style="display: none;">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="photo-preview-3">
                                            <i class="ri-user-line"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 text-center mt-2">
                                        <h3 class="mt-2 mb-0" id="seller-name">Vendedor</h3>
                                        <i class="ri-mail-line"></i> <span id="seller-email">Email</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('devolutions.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> 
                                <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>

                        <div class="col-md-9">
                            <div class="tabbable">
                                <div class="tab-content p-0">
                                    
                                    <!-- Paso 1: Selección de Entidad -->
                                    <div class="tab-pane fade active show" id="paso-entidad">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Seleccionar Entidad</h4>
                                                    <small><i>Elige la entidad para la devolución</i></small>
                                                </div>
                                            </div>

                                            <br>

                                            <div class="table-responsive">
                                                <table id="tabla-entidades" class="table table-striped nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Entidad</th>
                                                            <th>Provincia</th>
                                                            <th>Localidad</th>
                                                            <th>Administración</th>
                                                            <th>Estado</th>
                                                            <th>Seleccionar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Los datos se cargarán dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 text-end">
                                                    <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-entidad" disabled>
                                                        Siguiente
                                                        <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 2: Selección de Sorteo -->
                                    <div class="tab-pane fade" id="paso-sorteo">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Seleccionar Sorteo</h4>
                                                    <small><i>Elige el sorteo para la devolución</i></small>
                                                </div>
                                                <button id="btn-volver-entidad" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Entidades
                                                </button>
                                            </div>

                                            <br>

                                            <div class="table-responsive">
                                                <table id="tabla-sorteos" class="table table-striped nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Nombre Sorteo</th>
                                                            <th>Fecha Sorteo</th>
                                                            <th>Descripción</th>
                                                            <th>Seleccionar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Los datos se cargarán dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 text-end">
                                                    <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-sorteo" disabled>
                                                        Siguiente
                                                        <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 3: Selección de Vendedor -->
                                    <div class="tab-pane fade" id="paso-vendedor">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Seleccionar Vendedor</h4>
                                                    <small><i>Elige el vendedor para la devolución</i></small>
                                                </div>
                                                <button id="btn-volver-sorteo" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Sorteos
                                                </button>
                                            </div>

                                            <br>

                                            <div class="table-responsive">
                                                <table id="tabla-vendedores" class="table table-striped nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Vendedor</th>
                                                            <th>Email</th>
                                                            <th>Teléfono</th>
                                                            <th>Seleccionar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Los datos se cargarán dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 text-end">
                                                    <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-vendedor" disabled>
                                                        Siguiente
                                                        <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 4: Asignación de Participaciones -->
                                    <div class="tab-pane fade" id="paso-participaciones">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Asignar Participaciones</h4>
                                                    <small><i>Individual o por rango</i></small>
                                                </div>
                                                <button id="btn-volver-vendedor" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Vendedores
                                                </button>
                                            </div>

                                            <br>

                                            <div class="row">
                                                <!-- Sección: Participaciones Por Rango -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="form-card bs">
                                                        <div class="d-flex align-items-center p-3">
                                                            <div class="me-3">
                                                                <img src="{{url('icons/participaciones.svg')}}" alt="" width="40px">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h4 class="m-0 fw-bold">Participaciones</h4>
                                                                <small class="text-muted">Por Rango</small>
                                                            </div>
                                                            <div class="d-flex gap-2" style="width: 70%;">
                                                                <div class="flex-fill">
                                                                    <label class="form-label small mb-1">Desde</label>
                                                                    <div class="input-group input-group-merge group-form">
                                                                        <input type="number" class="form-control" id="rango-desde" placeholder="Número inicial" style="border-radius: 30px;">
                                                                    </div>
                                                                </div>
                                                                <div class="flex-fill">
                                                                    <label class="form-label small mb-1">Hasta</label>
                                                                    <div class="input-group input-group-merge group-form">
                                                                        <input type="number" class="form-control" id="rango-hasta" placeholder="Número final" style="border-radius: 30px;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center p-3">
                                                            <div class="me-3">
                                                                <img src="{{url('icons/participaciones.svg')}}" alt="" width="40px">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h4 class="m-0 fw-bold">Participación</h4>
                                                                <small class="text-muted">Participación Unidad</small>
                                                            </div>
                                                            <div class="d-flex gap-2 align-items-end" style="width: 70%;">
                                                                <div style="width: 50%;">
                                                                    <label class="form-label small mb-1">Participación</label>
                                                                    <div class="input-group input-group-merge group-form">
                                                                        <input type="number" class="form-control" id="participacion-unidad" placeholder="Número de participación" style="border-radius: 30px;">
                                                                    </div>
                                                                </div>
                                                                <div style="width: 50%;">
                                                                    <button type="button" class="btn btn-warning w-100" id="btn-asignar-participacion" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold;">
                                                                        Asignar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="resumen-asignacion" style="display: block;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 mt-1">Resumen Asignación</h4>
                                                        <small><i>comprueba que la asignación sea la correcta</i></small>
                                                    </div>
                                                </div>
                                                
                                                <br>
                                                
                                                <!-- Estado vacío -->
                                                <div id="estado-vacio-resumen" class="d-flex align-items-center gap-1">
                                                    <div class="empty-tables">
                                                        <div>
                                                            <img src="{{url('icons/participaciones.svg')}}" alt="" width="80px" style="margin-top: 10px;">
                                                        </div>
                                                        <h3 class="mb-0">No hay Participaciones</h3>
                                                        <small>Asigna Participaciones</small>
                                                    </div>
                                                </div>

                                                <!-- Lista de participaciones asignadas -->
                                                <div id="lista-participaciones-asignadas" style="display: none;">
                                                    <div class="form-card bs" style="max-height: 400px; overflow-y: auto;">
                                                        <div class="grid-participaciones" id="grid-participaciones">
                                                            <!-- Las participaciones se cargarán dinámicamente aquí -->
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="fw-bold">Total Asignadas:</span>
                                                            <div class="form-card bs px-3 py-2">
                                                                <span id="total-asignadas" class="fw-bold fs-4">0</span>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-warning" id="btn-terminar-asignacion" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold; padding: 10px 30px;">
                                                            Siguiente
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 5: Resumen de Devolución -->
                                    <div class="tab-pane fade" id="paso-resumen">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Resumen de Devolución</h4>
                                                    <small><i>Verifica los datos antes de continuar</i></small>
                                                </div>
                                                <button id="btn-volver-participaciones" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Participaciones
                                                </button>
                                            </div>

                                            <br>

                                            <div class="resumen-devolucion">
                                                <h5>Datos de la Devolución</h5>
                                                <div class="resumen-item">
                                                    <span>Entidad:</span>
                                                    <span id="resumen-entidad">-</span>
                                                </div>
                                                <div class="resumen-item">
                                                    <span>Sorteo:</span>
                                                    <span id="resumen-sorteo">-</span>
                                                </div>
                                                <div class="resumen-item">
                                                    <span>Vendedor:</span>
                                                    <span id="resumen-vendedor">-</span>
                                                </div>
                                                <div class="resumen-item">
                                                    <span>Total Participaciones:</span>
                                                    <span id="resumen-total">0</span>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <button type="button" class="btn btn-secondary" id="btn-volver-resumen" style="border-radius: 30px;">
                                                    <i class="ri-arrow-left-line me-2"></i>Volver
                                                </button>
                                                <button type="button" class="btn btn-warning" id="btn-continuar-liquidacion" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold;">
                                                    Continuar a Liquidación<i class="ri-arrow-right-line ms-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 6: Liquidación -->
                                    <div class="tab-pane fade" id="paso-liquidacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Liquidación</h4>
                                                    <small><i>Marca las participaciones como devueltas o vendidas</i></small>
                                                </div>
                                                <button id="btn-volver-resumen-final" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Resumen
                                                </button>
                                            </div>

                                            <br>

                                            <div id="liquidacion-participaciones">
                                                <!-- Las participaciones para liquidar se cargarán aquí -->
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <button type="button" class="btn btn-secondary" id="btn-cancelar-liquidacion" style="border-radius: 30px;">
                                                    <i class="ri-close-line me-2"></i>Cancelar
                                                </button>
                                                <button type="button" class="btn btn-success" id="btn-procesar-liquidacion" style="border-radius: 30px; font-weight: bold;">
                                                    <i class="ri-check-line me-2"></i>Procesar Liquidación
                                                </button>
                                            </div>
                                        </div>
                                    </div>

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
    // Variables globales
    let entidadSeleccionada = null;
    let sorteoSeleccionado = null;
    let vendedorSeleccionado = null;
    let participacionesAsignadas = [];
    
    // DataTables
    let tablaEntidades = null;
    let tablaSorteos = null;
    let tablaVendedores = null;

    // Asegurar que el primer paso esté activo al cargar
    $('#step-1').addClass('active');
    
    // Inicializar DataTable de entidades al cargar la página
    inicializarDataTableEntidades();

    // Variable global para rastrear el paso actual
    let pasoActualGlobal = 'paso-entidad';

    // Función para mostrar un paso específico
    function mostrarPaso(pasoId) {
        console.log('=== MOSTRAR PASO ===');
        console.log('Paso solicitado:', pasoId);
        console.log('Paso anterior:', pasoActualGlobal);
        
        $('.tab-pane').removeClass('active show');
        $('#' + pasoId).addClass('active show');
        
        // Actualizar la variable global del paso actual
        pasoActualGlobal = pasoId;
        console.log('pasoActualGlobal actualizado a:', pasoActualGlobal);
        
        // Actualizar indicadores de pasos con lógica de progreso
        actualizarIndicadoresPasos(pasoId);
        
        // Inicializar DataTables según el paso
        if (pasoId === 'paso-entidad' && !tablaEntidades) {
            inicializarDataTableEntidades();
        }
        
        console.log('=== FIN MOSTRAR PASO ===');
    }

    // Función para actualizar los indicadores de pasos
    function actualizarIndicadoresPasos(pasoActual) {
        console.log('=== ACTUALIZANDO INDICADORES ===');
        console.log('Paso actual recibido:', pasoActual);
        console.log('pasoActualGlobal:', pasoActualGlobal);
        
        // Definir el orden de los pasos
        const pasosOrden = [
            'paso-entidad',
            'paso-sorteo', 
            'paso-vendedor',
            'paso-participaciones',
            'paso-resumen',
            'paso-liquidacion'
        ];
        
        // Encontrar el índice del paso actual
        const indiceActual = pasosOrden.indexOf(pasoActual);
        console.log('Índice encontrado:', indiceActual);
        
        if (indiceActual === -1) {
            console.error('Paso no encontrado:', pasoActual);
            return;
        }
        
        // Mostrar estado antes de cambiar
        console.log('Estado ANTES de cambiar:');
        for (let i = 0; i < pasosOrden.length; i++) {
            const stepId = `step-${i + 1}`; // Corregir: usar step-1, step-2, etc.
            const elemento = $('#' + stepId);
            console.log(`  ${stepId}: ${elemento.hasClass('active')}`);
        }
        
        // Limpiar todas las clases activas primero
        $('.form-wizard-element').removeClass('active');
        
        // Activar SOLO el paso actual
        const stepId = `step-${indiceActual + 1}`; // Corregir: usar step-1, step-2, etc.
        const elemento = $('#' + stepId);
        
        console.log('Activando elemento:', stepId, 'Elemento encontrado:', elemento.length > 0);
        
        if (elemento.length > 0) {
            elemento.addClass('active');
        }
        
        // Mostrar estado después de cambiar
        console.log('Estado DESPUÉS de cambiar:');
        for (let i = 0; i < pasosOrden.length; i++) {
            const stepId = `step-${i + 1}`; // Corregir: usar step-1, step-2, etc.
            const elemento = $('#' + stepId);
            console.log(`  ${stepId}: ${elemento.hasClass('active')}`);
        }
        console.log('=== FIN ACTUALIZACIÓN ===');
    }


    // Función para inicializar DataTable de entidades
    function inicializarDataTableEntidades() {
        if (tablaEntidades) return;
        
        tablaEntidades = $('#tabla-entidades').DataTable({
            "select": { style: "single" },
            "ordering": true,
            "sorting": true,
            "scrollX": true,
            "scrollCollapse": true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            "ajax": {
                "url": "{{ route('devolutions.entities') }}",
                "type": "GET",
                "dataSrc": "entities"
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "province" },
                { "data": "city" },
                { "data": "administration_name", "defaultContent": "N/A" },
                { 
                    "data": "status",
                    "render": function(data, type, row) {
                        const badgeClass = data === 'activo' ? 'bg-success' : 'bg-danger';
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="form-check">
                                <input class="form-check-input seleccionar-entidad" type="radio" name="entity_id" value="${row.id}" id="entity_${row.id}" data-entity-id="${row.id}">
                                <label class="form-check-label" for="entity_${row.id}">Seleccionar</label>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "initComplete": function(settings, json) {
                // Restaurar los indicadores después de que se inicialice el DataTable
                actualizarIndicadoresPasos(pasoActualGlobal);
            },
            "drawCallback": function(settings) {
                // Restaurar los indicadores después de cada redibujado
                actualizarIndicadoresPasos(pasoActualGlobal);
            }
        });
    }

    // Función para inicializar DataTable de sorteos
    function inicializarDataTableSorteos() {
        if (tablaSorteos) return;
        
        tablaSorteos = $('#tabla-sorteos').DataTable({
            "select": { style: "single" },
            "ordering": true,
            "sorting": true,
            "scrollX": true,
            "scrollCollapse": true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            "ajax": {
                "url": "{{ route('devolutions.lotteries') }}",
                "type": "GET",
                "data": function(d) {
                    d.entity_id = entidadSeleccionada.id;
                },
                "dataSrc": "lotteries"
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { 
                    "data": "draw_date",
                    "render": function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString('es-ES') : 'N/A';
                    }
                },
                { "data": "description", "defaultContent": "N/A" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="form-check">
                                <input class="form-check-input seleccionar-sorteo" type="radio" name="lottery_id" value="${row.id}" id="lottery_${row.id}" data-lottery-id="${row.id}">
                                <label class="form-check-label" for="lottery_${row.id}">Seleccionar</label>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "initComplete": function(settings, json) {
                // Restaurar los indicadores después de que se inicialice el DataTable
                actualizarIndicadoresPasos(pasoActualGlobal);
            },
            "drawCallback": function(settings) {
                // Restaurar los indicadores después de cada redibujado
                actualizarIndicadoresPasos(pasoActualGlobal);
            }
        });
    }

    // Función para inicializar DataTable de vendedores
    function inicializarDataTableVendedores() {
        if (tablaVendedores) return;
        
        tablaVendedores = $('#tabla-vendedores').DataTable({
            "select": { style: "single" },
            "ordering": true,
            "sorting": true,
            "scrollX": true,
            "scrollCollapse": true,
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            "ajax": {
                "url": "{{ route('devolutions.sellers') }}",
                "type": "GET",
                "data": function(d) {
                    d.entity_id = entidadSeleccionada.id;
                },
                "dataSrc": "sellers"
            },
            "columns": [
                { "data": "id" },
                { "data": "name" },
                { "data": "email" },
                { "data": "phone", "defaultContent": "N/A" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                            <div class="form-check">
                                <input class="form-check-input seleccionar-vendedor" type="radio" name="seller_id" value="${row.id}" id="seller_${row.id}" data-seller-id="${row.id}">
                                <label class="form-check-label" for="seller_${row.id}">Seleccionar</label>
                            </div>
                        `;
                    },
                    "orderable": false
                }
            ],
            "initComplete": function(settings, json) {
                // Restaurar los indicadores después de que se inicialice el DataTable
                actualizarIndicadoresPasos(pasoActualGlobal);
            },
            "drawCallback": function(settings) {
                // Restaurar los indicadores después de cada redibujado
                actualizarIndicadoresPasos(pasoActualGlobal);
            }
        });
    }

    // Event listeners
    $(document).on('change', '.seleccionar-entidad', function() {
        const entityId = $(this).data('entity-id');
        entidadSeleccionada = { id: entityId };
        
        // Mostrar información de la entidad
        const row = $(this).closest('tr');
        const entityName = row.find('td:eq(1)').text();
        const entityLocation = row.find('td:eq(2)').text() + ', ' + row.find('td:eq(3)').text();
        
        $('#entity-name').text(entityName);
        $('#entity-location').text(entityLocation);
        $('#entity-info').show();
        
        $('#btn-siguiente-entidad').prop('disabled', false);
    });

    $(document).on('change', '.seleccionar-sorteo', function() {
        const lotteryId = $(this).data('lottery-id');
        sorteoSeleccionado = { id: lotteryId };
        $('#btn-siguiente-sorteo').prop('disabled', false);
    });

    $(document).on('change', '.seleccionar-vendedor', function() {
        const sellerId = $(this).data('seller-id');
        vendedorSeleccionado = { id: sellerId };
        
        // Mostrar información del vendedor
        const row = $(this).closest('tr');
        const sellerName = row.find('td:eq(1)').text();
        const sellerEmail = row.find('td:eq(2)').text();
        
        $('#seller-name').text(sellerName);
        $('#seller-email').text(sellerEmail);
        $('#seller-info').show();
        
        $('#btn-siguiente-vendedor').prop('disabled', false);
    });

    // Navegación entre pasos
    $('#btn-siguiente-entidad').click(function() {
        if (entidadSeleccionada) {
            mostrarPaso('paso-sorteo');
            inicializarDataTableSorteos();
        }
    });

    $('#btn-siguiente-sorteo').click(function() {
        if (sorteoSeleccionado) {
            mostrarPaso('paso-vendedor');
            inicializarDataTableVendedores();
        }
    });

    $('#btn-siguiente-vendedor').click(function() {
        if (vendedorSeleccionado) {
            mostrarPaso('paso-participaciones');
        }
    });

    // Botones de volver
    $('#btn-volver-entidad').click(function() {
        mostrarPaso('paso-entidad');
    });

    $('#btn-volver-sorteo').click(function() {
        mostrarPaso('paso-sorteo');
    });

    $('#btn-volver-vendedor').click(function() {
        mostrarPaso('paso-vendedor');
    });

    $('#btn-volver-participaciones').click(function() {
        mostrarPaso('paso-participaciones');
    });

    $('#btn-volver-resumen-final').click(function() {
        mostrarPaso('paso-resumen');
    });

    // Funcionalidad de asignación de participaciones (similar a sellers/show.blade.php)
    function actualizarResumenAsignacion() {
        $('#resumen-asignacion').show();
        $('#estado-vacio-resumen').addClass('d-none');
        
        if (participacionesAsignadas.length === 0) {
            $('#estado-vacio-resumen').removeClass('d-none');
            $('#lista-participaciones-asignadas').hide();
        } else {
            $('#lista-participaciones-asignadas').show();
            $('#total-asignadas').text(participacionesAsignadas.length);
            
            // Generar grid de participaciones
            const gridHtml = participacionesAsignadas.map(participation => {
                const fecha = new Date(participation.assigned_at);
                const fechaStr = fecha.toLocaleDateString('es-ES');
                const horaStr = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                
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
                                <span class="participacion-estado">Asignada</span>
                            </div>
                        </div>
                        <button class="btn-eliminar-participacion" onclick="eliminarParticipacion('${participation.participation_code}')">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;
            }).join('');

            $('#grid-participaciones').html(gridHtml);
        }
    }

    // Función para eliminar participación
    window.eliminarParticipacion = function(codigo) {
        participacionesAsignadas = participacionesAsignadas.filter(p => p.participation_code !== codigo);
        actualizarResumenAsignacion();
    };

    // Función para validar participaciones
    function validarParticipacionesDisponibles(desde, hasta, participationId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "{{ route('devolutions.validate') }}",
                method: 'POST',
                data: {
                    seller_id: vendedorSeleccionado.id,
                    lottery_id: sorteoSeleccionado.id,
                    desde: desde,
                    hasta: hasta,
                    participation_id: participationId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        resolve(response);
                    } else {
                        reject(response.message || 'Error al validar participaciones');
                    }
                },
                error: function(xhr, status, error) {
                    reject('Error de conexión: ' + error);
                }
            });
        });
    }

    // Event listener para asignar participaciones
    $('#btn-asignar-participacion').click(function() {
        const desde = $('#rango-desde').val();
        const hasta = $('#rango-hasta').val();
        const unidad = $('#participacion-unidad').val();

        if (desde && hasta) {
            // Asignación por rango
            $('#btn-asignar-participacion').prop('disabled', true).text('Validando...');
            
            validarParticipacionesDisponibles(desde, hasta, null)
                .then(response => {
                    if (response.participations && response.participations.length > 0) {
                        response.participations.forEach(participation => {
                            const participacionExistente = participacionesAsignadas.find(p => p.id === participation.id);
                            if (!participacionExistente) {
                                participacionesAsignadas.push({
                                    id: participation.id,
                                    number: participation.number,
                                    participation_code: participation.participation_code,
                                    assigned_at: new Date().toISOString()
                                });
                            }
                        });
                        actualizarResumenAsignacion();
                        mostrarMensaje('Participaciones asignadas correctamente', 'success');
                    } else {
                        mostrarMensaje('No hay participaciones disponibles en ese rango', 'warning');
                    }
                })
                .catch(error => {
                    mostrarMensaje(error, 'error');
                })
                .finally(() => {
                    $('#btn-asignar-participacion').prop('disabled', false).text('Asignar');
                    $('#rango-desde').val('');
                    $('#rango-hasta').val('');
                });
        } else if (unidad) {
            // Asignación individual
            $('#btn-asignar-participacion').prop('disabled', true).text('Validando...');
            
            validarParticipacionesDisponibles(null, null, unidad)
                .then(response => {
                    if (response.participations && response.participations.length > 0) {
                        const participation = response.participations[0];
                        const participacionExistente = participacionesAsignadas.find(p => p.id === participation.id);
                        
                        if (!participacionExistente) {
                            participacionesAsignadas.push({
                                id: participation.id,
                                number: participation.number,
                                participation_code: participation.participation_code,
                                assigned_at: new Date().toISOString()
                            });
                            actualizarResumenAsignacion();
                            mostrarMensaje('Participación asignada correctamente', 'success');
                        } else {
                            mostrarMensaje('Esta participación ya está asignada', 'warning');
                        }
                    } else {
                        mostrarMensaje('La participación no está disponible', 'warning');
                    }
                })
                .catch(error => {
                    mostrarMensaje(error, 'error');
                })
                .finally(() => {
                    $('#btn-asignar-participacion').prop('disabled', false).text('Asignar');
                    $('#participacion-unidad').val('');
                });
        } else {
            alert('Debes especificar un rango o una participación individual');
        }
    });

    // Event listener para terminar asignación
    $('#btn-terminar-asignacion').click(function() {
        if (participacionesAsignadas.length === 0) {
            alert('No hay participaciones para continuar');
            return;
        }

        // Actualizar resumen
        $('#resumen-entidad').text($('#entity-name').text());
        $('#resumen-sorteo').text('Sorteo #' + sorteoSeleccionado.id);
        $('#resumen-vendedor').text($('#seller-name').text());
        $('#resumen-total').text(participacionesAsignadas.length);

        mostrarPaso('paso-resumen');
    });

    // Event listener para continuar a liquidación
    $('#btn-continuar-liquidacion').click(function() {
        mostrarPaso('paso-liquidacion');
        cargarParticipacionesParaLiquidacion();
    });

    // Función para cargar participaciones para liquidación
    function cargarParticipacionesParaLiquidacion() {
        let html = '';
        
        participacionesAsignadas.forEach(participation => {
            html += `
                <div class="liquidacion-card">
                    <div class="liquidacion-header">
                        <div class="liquidacion-icon">
                            <img src="{{url('assets/ticket.svg')}}" alt="" width="25px">
                        </div>
                        <div class="liquidacion-info">
                            <h5>${participation.participation_code}</h5>
                            <small>Participación #${participation.number}</small>
                        </div>
                    </div>
                    <div class="liquidacion-stats">
                        <div class="stat-item">
                            <div class="stat-number">5€</div>
                            <div class="stat-label">Valor</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="form-check">
                            <input class="form-check-input liquidacion-option" type="radio" name="liquidacion_${participation.id}" value="devolver" id="devolver_${participation.id}" checked>
                            <label class="form-check-label" for="devolver_${participation.id}">
                                <i class="ri-arrow-go-back-line me-1"></i>Devolver
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input liquidacion-option" type="radio" name="liquidacion_${participation.id}" value="vender" id="vender_${participation.id}">
                            <label class="form-check-label" for="vender_${participation.id}">
                                <i class="ri-money-dollar-circle-line me-1"></i>Vender
                            </label>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#liquidacion-participaciones').html(html);
    }

    // Event listener para procesar liquidación
    $('#btn-procesar-liquidacion').click(function() {
        if (participacionesAsignadas.length === 0) {
            alert('No hay participaciones para procesar');
            return;
        }

        // Mostrar loading
        $('#btn-procesar-liquidacion').prop('disabled', true).text('Procesando...');

        // Recopilar datos de liquidación
        const liquidacion = {
            devolver: [],
            vender: []
        };

        participacionesAsignadas.forEach(participation => {
            const opcion = $(`input[name="liquidacion_${participation.id}"]:checked`).val();
            if (opcion === 'devolver') {
                liquidacion.devolver.push(participation.id);
            } else if (opcion === 'vender') {
                liquidacion.vender.push(participation.id);
            }
        });

        // Enviar datos al servidor
        $.ajax({
            url: "{{ route('devolutions.store') }}",
            method: 'POST',
            data: {
                entity_id: entidadSeleccionada.id,
                lottery_id: sorteoSeleccionado.id,
                seller_id: vendedorSeleccionado.id,
                participations: participacionesAsignadas.map(p => p.id),
                return_reason: 'Devolución por liquidación',
                liquidacion: liquidacion,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('Liquidación procesada correctamente', 'success');
                    setTimeout(() => {
                        window.location.href = "{{ route('devolutions.index') }}";
                    }, 2000);
                } else {
                    mostrarMensaje(response.message || 'Error al procesar la liquidación', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error de conexión al procesar la liquidación', 'error');
            },
            complete: function() {
                $('#btn-procesar-liquidacion').prop('disabled', false).text('Procesar Liquidación');
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
        
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Event listeners para inputs
    $('#rango-desde, #rango-hasta').on('input', function() {
        const desde = $('#rango-desde').val();
        const hasta = $('#rango-hasta').val();
        
        if (desde && hasta) {
            $('#participacion-unidad').val('').prop('disabled', true);
        } else {
            $('#participacion-unidad').prop('disabled', false);
        }
    });

    $('#participacion-unidad').on('input', function() {
        const unidad = $(this).val();
        
        if (unidad) {
            $('#rango-desde, #rango-hasta').val('').prop('disabled', true);
        } else {
            $('#rango-desde, #rango-hasta').prop('disabled', false);
        }
    });
});
</script>

@endsection
