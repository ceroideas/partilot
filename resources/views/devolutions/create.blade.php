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
                            <!-- Pasos del proceso (dinámicos según tipo de devolución) -->
                            <ul class="form-card bs mb-3 nav" id="wizard-steps">
                                <li class="nav-item">
                                    <div class="form-wizard-element active" id="step-1">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('assets/entidad.svg')}}" alt="">
                                        <label>Seleccionar Entidad</label>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <div class="form-wizard-element" id="step-2">
                                        <span>&nbsp;&nbsp;</span>
                                        <img src="{{url('icons/usuarios.svg')}}" alt="">
                                        <label>Selec. Opción</label>
                                    </div>
                                </li>
                                <!-- Los pasos siguientes se cargarán dinámicamente -->
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

                                    <!-- Paso 2: Selección de Opción -->
                                    <div class="tab-pane fade" id="paso-opcion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Seleccionar Tipo de Devolución</h4>
                                                    <small><i>Elige el tipo de devolución a realizar</i></small>
                                                </div>
                                                <div class="d-none" id="back-to-option-buttons">
                                                    <button class="btn btn-sm btn-light" id="back-option-button" style="border-radius: 50%; width: 40px; height: 40px; padding: 0;">
                                                        <i class="ri-arrow-left-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <br>

                                            <!-- Mostrar información de la entidad -->
                                            <div class="form-group mt-2 mb-3 admin-box">
                                                <div class="row">
                                                    <div class="col-1">
                                                        <div class="photo-preview-3">
                                                            <i class="ri-building-line"></i>
                                                        </div>
                                                        <div style="clear: both;"></div>
                                                    </div>
                                                    <div class="col-4 text-center mt-3">
                                                        <h4 class="mt-0 mb-0" id="opcion-entity-name">Entidad</h4>
                                                        <small id="opcion-entity-province">Provincia</small> <br>
                                                        <small id="opcion-entity-admin">Administración</small>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="mt-3">
                                                            <span>Provincia: <span id="opcion-entity-province-2">N/A</span></span> <br>
                                                            <span>Dirección: <span id="opcion-entity-address">N/A</span></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="mt-3">
                                                            <span>Ciudad: <span id="opcion-entity-city">N/A</span></span> <br>
                                                            <span>Tel: <span id="opcion-entity-phone">N/A</span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <!-- Opciones de tipo de devolución -->
                                            <div id="all-options-devolution">
                                                <div class="mt-4 text-center">
                                                    <div id="devolution-type-buttons">
                                                        <button class="btn btn-light btn-xl text-center m-2 bs" id="btn-devolucion-vendedor" style="border: 1px solid #f0f0f0; padding: 16px; width: 160px; border-radius: 16px;">
                                                            <img class="mt-2 mb-1" src="{{url('assets/vendedor.svg')}}" alt="" width="60%">
                                                            <h4 class="mb-0">Devolución <br> Vendedor</h4>
                                                        </button>

                                                        <button class="btn btn-light btn-xl text-center m-2 bs" id="btn-devolucion-administracion" style="border: 1px solid #f0f0f0; padding: 16px; width: 180px; border-radius: 16px; position: relative;">
                                                            <img class="mt-2 mb-1" src="{{url('assets/admin.svg')}}" alt="" width="60%">
                                                            <h4 class="mb-0">Devolución <br> Administración</h4>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 3 (Vendedor): Selección de Vendedor -->
                                    <div class="tab-pane fade" id="paso-vendedor">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Seleccionar Vendedor</h4>
                                                    <small><i>Elige el vendedor para la devolución</i></small>
                                                </div>
                                                <button id="btn-volver-opcion" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Opciones
                                                </button>
                                            </div>

                                            <br>

                                            <div class="table-responsive">
                                                <table id="tabla-vendedores" class="table table-striped nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Nombre</th>
                                                            <th>Email</th>
                                                            <th>Teléfono</th>
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
                                                    <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-vendedor" disabled>
                                                        Siguiente
                                                        <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso: Selección de Sorteo -->
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

                                    <!-- Paso 3: Asignación de Participaciones -->
                                    <div class="tab-pane fade" id="paso-participaciones">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">Indica las participaciones</h4>
                                                    <small><i>Individual o por rango</i></small>
                                                </div>
                                                <button id="btn-volver-sorteo-desde-participaciones" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> Volver a Sorteos
                                                </button>
                                            </div>

                                            <br>

                                            <div class="row">
                                                <!-- Sección: Selección de Set -->
                                                <div class="col-md-12 mb-3">
                                                    <div class="form-card bs">
                                                        <div class="d-flex align-items-center p-3">
                                                            <div class="me-3">
                                                                <img src="{{url('icons/sets.svg')}}" alt="" width="40px">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h4 class="m-0 fw-bold">Seleccionar Set</h4>
                                                                <small class="text-muted">Elige el set de participaciones</small>
                                                                <br>
                                                                <small class="text-info"><i class="ri-information-line"></i> Puedes cambiar de set sin perder las selecciones anteriores</small>
                                                            </div>
                                                            <div style="width: 40%;">
                                                                <label class="form-label small mb-1">Set</label>
                                                                <div class="input-group input-group-merge group-form">
                                                                    <select class="form-select" id="selector-set" style="border-radius: 30px;">
                                                                        <option value="">Seleccionar set...</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

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
                                            <span class="text-muted">|</span>
                                            <span class="fw-bold">Sets:</span>
                                            <div class="form-card bs px-3 py-2">
                                                <span id="total-sets" class="fw-bold fs-4">0</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-warning" id="btn-terminar-asignacion" style="border-radius: 30px; background-color: #e78307; color: #333; font-weight: bold; padding: 10px 30px;">
                                            Siguiente
                                        </button>
                                    </div>
                                                </div>

                                                <!-- Botón para continuar sin participaciones -->
                                                <div id="btn-continuar-sin-participaciones-container" class="text-end mt-3" style="display: none;">
                                                    <button type="button" class="btn btn-info" id="btn-continuar-sin-participaciones" style="border-radius: 30px; font-weight: bold; padding: 10px 30px;">
                                                        <i class="ri-arrow-right-line"></i> Continuar sin participaciones
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso: Liquidación -->
                                    <div class="tab-pane fade" id="paso-liquidacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1" id="liquidacion-titulo">Liquidación</h4>
                                                    <small id="liquidacion-subtitulo"><i>Procesa la liquidación</i></small>
                                                </div>
                                                <button id="btn-volver-participaciones-final" class="btn btn-secondary btn-sm">
                                                    <i class="ri-arrow-left-line"></i> <span id="btn-volver-text">Volver</span>
                                                </button>
                                            </div>

                                            <hr>

                                            <!-- Contenedor para liquidación de VENDEDOR -->
                                            <div id="liquidacion-vendedor-container" style="display: none;">
                                                
                                                <!-- Selector de Sorteo -->
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold">Selecciona un Sorteo</label>
                                                        <select class="form-select" id="vendedor-selector-sorteo-liquidacion">
                                                            <option value="">-- Selecciona un sorteo --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Resumen de Liquidación -->
                                                <div id="vendedor-resumen-liquidacion-container" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-header">Resumen de Participaciones</div>
                                                            <div class="card-body">
                                                                <p><strong>Total Participaciones Asignadas:</strong> <span id="vendedor-settlement-total-participations" class="fw-bold fs-4">0</span></p>
                                                                <p><strong>Precio por Participación:</strong> <span id="vendedor-settlement-price-per-participation">0.00€</span></p>
                                                                <p><strong>Total a Liquidar:</strong> <span id="vendedor-settlement-total-amount" class="text-danger fw-bold">0.00€</span></p>
                                                </div>
                                                </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-header">Liquidación Actual</div>
                                                            <div class="card-body">
                                                                <p><strong>Total Pagado:</strong> <span id="vendedor-settlement-total-paid" class="text-success fw-bold">0.00€</span></p>
                                                                <p><strong>Participaciones Liquidadas:</strong> <span id="vendedor-settlement-liquidated-participations">0</span></p>
                                                                <p><strong>Pendiente por Liquidar:</strong> <span id="vendedor-settlement-pending-amount" class="text-warning fw-bold">0.00€</span></p>
                                                                <p><strong>Participaciones Pendientes:</strong> <span id="vendedor-settlement-pending-participations">0</span></p>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>

                                                <!-- Formas de Pago -->
                                                <div class="card mt-3">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Registrar Pagos</h5>
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
                                                                        <input type="number" step="0.01" class="form-control vendedor-settlement-payment-input" placeholder="0.00" id="vendedor-settlement-pago-efectivo">
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
                                                                        <input type="number" step="0.01" class="form-control vendedor-settlement-payment-input" placeholder="0.00" id="vendedor-settlement-pago-bizum">
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
                                                                        <input type="number" step="0.01" class="form-control vendedor-settlement-payment-input" placeholder="0.00" id="vendedor-settlement-pago-transferencia">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <div class="border rounded p-3 mb-3 bg-light">
                                                                        <small class="text-muted">Pendiente a Pagar</small>
                                                                        <div class="text-danger h4" id="vendedor-settlement-pendiente-display">0,00€</div>
                                                                    </div>
                                                                    <div class="border rounded p-3 mb-3 bg-success bg-opacity-10">
                                                                        <small class="text-muted">A Pagar Ahora</small>
                                                                        <div class="text-success h4" id="vendedor-settlement-pagar-ahora">0,00€</div>
                                                                    </div>
                                                                    <div class="border rounded p-3 mb-3" id="vendedor-settlement-quedara-pendiente-container">
                                                                        <small class="text-muted">Quedará Pendiente</small>
                                                                        <div class="h5" id="vendedor-settlement-quedara-pendiente">0,00€</div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-warning" id="btn-registrar-liquidacion-vendedor" style="border-radius: 30px; width: 100%;">
                                                                        <i class="ri-add-line"></i> Registrar Liquidación
                                                </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>

                                                <!-- Historial de Liquidaciones -->
                                                <div class="card mt-3">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Historial de Liquidaciones</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div id="vendedor-historial-liquidaciones-container">
                                                            <p class="text-muted text-center">Selecciona un sorteo para ver el historial</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div> <!-- Cierre vendedor-resumen-liquidacion-container -->
                                            </div> <!-- Cierre liquidacion-vendedor-container -->

                                            <!-- Contenedor para liquidación de ADMINISTRACIÓN -->
                                            <div id="liquidacion-administracion-container" style="display: none;">
                                                    <!-- Resumen Devolución -->
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            <h5 class="card-title">Resumen Devolución</h5>
                                                            <small class="text-muted">Resumen Devolución Administración</small>
                                                            
                                                            <div class="text-center my-3">
                                                                <img src="{{url('assets/ticket.svg')}}" alt="" width="60px">
                                                                <div class="mt-2">
                                                                    <strong id="liquidacion-ticket-number">-</strong>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="mb-2">
                                                                        <strong>Total Participaciones:</strong>
                                                                        <span id="liquidacion-total-participaciones">0</span>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <strong>Ventas registradas:</strong>
                                                                        <span id="liquidacion-ventas-registradas">0</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="mb-2">
                                                                        <strong>Participaciones Devueltas:</strong>
                                                                        <span id="liquidacion-participaciones-devueltas">0</span>
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
                                                                        <span id="liquidacion-total-liquidacion">0,00€</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="mb-2">
                                                                        <strong>Pagos Registrados:</strong>
                                                                        <span id="liquidacion-pagos-registrados">0,00€</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="mb-2">
                                                                        <strong>Total a Pagar:</strong>
                                                                        <span id="liquidacion-total-pagar">0,00€</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Forma de Pago -->
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
                                                                            <div class="text-danger h4" id="liquidacion-importe-total">0,00€</div>
                                                                        </div>
                                                                        <div class="border rounded p-3 mb-3 bg-success bg-opacity-10">
                                                                            <small class="text-muted">Total Pagado</small>
                                                                            <div class="text-success h4" id="total-pagado">0,00€</div>
                                                                        </div>
                                                                        <div class="border rounded p-3 mb-3" id="pendiente-container" style="display: none;">
                                                                            <small class="text-muted">Pendiente</small>
                                                                            <div class="h5" id="total-pendiente">0,00€</div>
                                                                        </div>
                                                                        <button type="button" class="btn btn-warning" id="btn-aceptar-liquidacion" style="border-radius: 30px; width: 100%;">
                                                                            Aceptar Liquidación
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
    let setSeleccionado = null;
    let participacionesAsignadas = [];
    let tipoDevolucion = null; // 'vendedor' o 'administracion'
    let vendedorSeleccionado = null;
    
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
    
    // Función para construir dinámicamente los pasos del wizard
    function construirWizardPasos() {
        const wizardSteps = $('#wizard-steps');
        
        // Mantener los primeros 2 pasos siempre
        // Limpiar solo desde el paso 3 en adelante
        wizardSteps.find('li:gt(1)').remove();
        
        if (tipoDevolucion === 'vendedor') {
            // Flujo: Entidad -> Opción -> Vendedor -> Liquidación
            wizardSteps.append(`
                <li class="nav-item">
                    <div class="form-wizard-element" id="step-3">
                        <span>&nbsp;&nbsp;</span>
                        <img src="{{url('icons/usuarios.svg')}}" alt="">
                        <label>Selec. Vendedor</label>
                    </div>
                </li>
                <li class="nav-item">
                    <div class="form-wizard-element" id="step-4">
                        <span>&nbsp;&nbsp;</span>
                        <img src="{{url('icons/dinero.svg')}}" alt="">
                        <label>Liquidación</label>
                    </div>
                </li>
            `);
        } else if (tipoDevolucion === 'administracion') {
            // Flujo: Entidad -> Opción -> Sorteo -> Participaciones -> Liquidación
            wizardSteps.append(`
                <li class="nav-item">
                    <div class="form-wizard-element" id="step-3">
                        <span>&nbsp;&nbsp;</span>
                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                        <label>Seleccionar Sorteo</label>
                    </div>
                </li>
                <li class="nav-item">
                    <div class="form-wizard-element" id="step-4">
                        <span>&nbsp;&nbsp;</span>
                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                        <label>Seleccionar Participaciones</label>
                    </div>
                </li>
                <li class="nav-item">
                    <div class="form-wizard-element" id="step-5">
                        <span>&nbsp;&nbsp;</span>
                        <img src="{{url('icons/usuarios.svg')}}" alt="">
                        <label>Liquidación</label>
                    </div>
                </li>
            `);
        }
    }

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

    // Función para actualizar los indicadores de pasos (dinámico según tipo de devolución)
    function actualizarIndicadoresPasos(pasoActual) {
        console.log('=== ACTUALIZANDO INDICADORES ===');
        console.log('Paso actual recibido:', pasoActual);
        console.log('Tipo devolución:', tipoDevolucion);
        
        let pasosOrden = [];
        
        if (tipoDevolucion === 'vendedor') {
            // Flujo con vendedor: Entidad -> Opción -> Vendedor -> Liquidación
            pasosOrden = [
                'paso-entidad',
                'paso-opcion',
                'paso-vendedor',
                'paso-liquidacion'
            ];
        } else if (tipoDevolucion === 'administracion') {
            // Flujo sin vendedor: Entidad -> Opción -> Sorteo -> Participaciones -> Liquidación
            pasosOrden = [
                'paso-entidad',
                'paso-opcion',
                'paso-sorteo',
                'paso-participaciones',
                'paso-liquidacion'
            ];
        } else {
            // Flujo inicial (solo mostrar entidad y opción)
            pasosOrden = [
                'paso-entidad',
                'paso-opcion'
            ];
        }
        
        // Encontrar el índice del paso actual
        const indiceActual = pasosOrden.indexOf(pasoActual);
        console.log('Índice encontrado:', indiceActual);
        
        if (indiceActual === -1) {
            console.error('Paso no encontrado:', pasoActual);
            return;
        }
        
        // Limpiar todas las clases activas primero
        $('.form-wizard-element').removeClass('active');
        
        // Activar SOLO el paso actual
        const stepId = `step-${indiceActual + 1}`;
        const elemento = $('#' + stepId);
        
        console.log('Activando elemento:', stepId, 'Elemento encontrado:', elemento.length > 0);
        
        if (elemento.length > 0) {
            elemento.addClass('active');
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
                actualizarIndicadoresPasos(pasoActualGlobal);
            },
            "drawCallback": function(settings) {
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
                actualizarIndicadoresPasos(pasoActualGlobal);
            },
            "drawCallback": function(settings) {
                actualizarIndicadoresPasos(pasoActualGlobal);
            }
        });
    }

    // Función para inicializar DataTable de vendedores
    function inicializarDataTableVendedores() {
        if (tablaVendedores) {
            tablaVendedores.ajax.reload();
            return;
        }
        
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
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        const nombre = row.user ? `${row.user.name || ''} ${row.user.last_name || ''}`.trim() : 'N/A';
                        return nombre || 'Sin nombre';
                    }
                },
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        return row.user && row.user.email ? row.user.email : 'N/A';
                    }
                },
                { 
                    "data": null,
                    "render": function(data, type, row) {
                        return row.user && row.user.phone ? row.user.phone : 'N/A';
                    }
                },
                { 
                    "data": "status",
                    "render": function(data, type, row) {
                        const badgeClass = data === 'active' ? 'bg-success' : 'bg-danger';
                        const statusText = data === 'active' ? 'Activo' : 'Inactivo';
                        return `<span class="badge ${badgeClass}">${statusText}</span>`;
                    }
                },
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
                actualizarIndicadoresPasos(pasoActualGlobal);
            },
            "drawCallback": function(settings) {
                actualizarIndicadoresPasos(pasoActualGlobal);
            }
        });
    }

    // Event listeners
    $(document).on('change', '.seleccionar-entidad', function() {
        const entityId = $(this).data('entity-id');
        const row = $(this).closest('tr');
        const entityData = {
            id: entityId,
            name: row.find('td:eq(1)').text(),
            province: row.find('td:eq(2)').text(),
            city: row.find('td:eq(3)').text(),
            administration: row.find('td:eq(4)').text(),
            address: 'N/A', // Esto debería venir de los datos
            phone: 'N/A'     // Esto debería venir de los datos
        };
        
        entidadSeleccionada = entityData;
        
        // Mostrar información de la entidad en varias secciones
        $('#entity-name').text(entityData.name);
        $('#entity-location').text(`${entityData.province}, ${entityData.city}`);
        $('#entity-info').show();
        
        // También en la sección de opciones
        $('#opcion-entity-name').text(entityData.name);
        $('#opcion-entity-province').text(entityData.province);
        $('#opcion-entity-admin').text(entityData.administration);
        $('#opcion-entity-province-2').text(entityData.province);
        $('#opcion-entity-city').text(entityData.city);
        $('#opcion-entity-address').text(entityData.address);
        $('#opcion-entity-phone').text(entityData.phone);
        
        $('#btn-siguiente-entidad').prop('disabled', false);
    });

    $(document).on('change', '.seleccionar-vendedor', function() {
        const sellerId = $(this).data('seller-id');
        const row = $(this).closest('tr');
        vendedorSeleccionado = {
            id: sellerId,
            name: row.find('td:eq(1)').text(),
            email: row.find('td:eq(2)').text()
        };
        $('#btn-siguiente-vendedor').prop('disabled', false);
    });

    $(document).on('change', '.seleccionar-sorteo', function() {
        const lotteryId = $(this).data('lottery-id');
        sorteoSeleccionado = { id: lotteryId };
        $('#btn-siguiente-sorteo').prop('disabled', false);
    });

    // Navegación entre pasos
    $('#btn-siguiente-entidad').click(function() {
        if (entidadSeleccionada) {
            mostrarPaso('paso-opcion');
        }
    });

    // Botones de selección de tipo de devolución
    $('#btn-devolucion-vendedor').click(function() {
        tipoDevolucion = 'vendedor';
        construirWizardPasos();
        mostrarPaso('paso-vendedor');
        inicializarDataTableVendedores();
    });

    $('#btn-devolucion-administracion').click(function() {
        tipoDevolucion = 'administracion';
        construirWizardPasos();
        mostrarPaso('paso-sorteo');
        inicializarDataTableSorteos();
    });

    $('#btn-siguiente-vendedor').click(function() {
        if (vendedorSeleccionado) {
            // Para vendedor, ir directo a liquidación
            mostrarPaso('paso-liquidacion');
            configurarLiquidacionPorTipo();
        }
    });

    $('#btn-siguiente-sorteo').click(function() {
        if (sorteoSeleccionado) {
            mostrarPaso('paso-participaciones');
            // Cargar sets según el tipo de devolución
            if (tipoDevolucion === 'vendedor') {
                cargarSetsVendedor();
            } else {
            cargarSetsEntidad();
            }
        }
    });

    // Botones de volver
    $('#btn-volver-entidad').click(function() {
        mostrarPaso('paso-entidad');
    });

    $('#btn-volver-opcion').click(function() {
        mostrarPaso('paso-opcion');
    });

    $('#btn-volver-sorteo-desde-participaciones').click(function() {
        mostrarPaso('paso-sorteo');
    });

    $('#btn-volver-participaciones-final').click(function() {
        if (tipoDevolucion === 'vendedor') {
            mostrarPaso('paso-vendedor');
        } else {
            mostrarPaso('paso-participaciones');
        }
    });

    // Funcionalidad de asignación de participaciones
    function actualizarResumenAsignacion() {
        $('#resumen-asignacion').show();
        $('#estado-vacio-resumen').addClass('d-none');
        
        if (participacionesAsignadas.length === 0) {
            $('#estado-vacio-resumen').removeClass('d-none');
            $('#lista-participaciones-asignadas').hide();
            // Mostrar botón para continuar sin participaciones si hay set seleccionado
            if (setSeleccionado) {
                $('#btn-continuar-sin-participaciones-container').show();
            } else {
                $('#btn-continuar-sin-participaciones-container').hide();
            }
            actualizarResumenLiquidacion();
        } else {
            $('#lista-participaciones-asignadas').show();
            $('#btn-continuar-sin-participaciones-container').hide();
            $('#total-asignadas').text(participacionesAsignadas.length);
            
            // Calcular cuántos sets diferentes se han seleccionado
            const setsUnicos = [...new Set(participacionesAsignadas.map(p => p.set_id))];
            $('#total-sets').text(setsUnicos.length);
            
            // Actualizar resumen de liquidación
            actualizarResumenLiquidacion();
            
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
                                <div class="participacion-fecha">
                                    <i class="ri-folder-line"></i>
                                    <span style="font-size: 0.85em; color: #888;">${participation.set_name || 'Set desconocido'}</span>
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

    // Función para actualizar el resumen de liquidación
    function actualizarResumenLiquidacion() {
        console.log('=== ACTUALIZANDO RESUMEN LIQUIDACIÓN ===');
        console.log('entidadSeleccionada:', entidadSeleccionada);
        console.log('sorteoSeleccionado:', sorteoSeleccionado);
        console.log('participacionesAsignadas:', participacionesAsignadas);
        
        // Validar solo que haya entidad y sorteo seleccionados (las participaciones pueden estar vacías)
        if (!entidadSeleccionada || !sorteoSeleccionado) {
            console.log('Limpiando resumen - no hay entidad o sorteo seleccionado');
            // Limpiar resumen si no hay datos básicos
            $('#liquidacion-total-participaciones').text('0');
            $('#liquidacion-ventas-registradas').text('0');
            $('#liquidacion-participaciones-devueltas').text('0');
            $('#liquidacion-disponibles').text('0');
            $('#liquidacion-total-liquidacion').text('0,00€');
            $('#liquidacion-pagos-registrados').text('0,00€');
            $('#liquidacion-total-pagar').text('0,00€');
            $('#liquidacion-importe-total').text('0,00€');
            return;
        }

        // Permitir continuar sin participaciones (solo para registrar pago)
        const participationIds = participacionesAsignadas.length > 0 ? participacionesAsignadas.map(p => p.id) : [];
        console.log('Enviando IDs de participaciones:', participationIds);
        console.log('Set seleccionado:', setSeleccionado);

        // Preparar datos para el resumen
        const datosResumen = {
            entity_id: entidadSeleccionada.id,
            lottery_id: sorteoSeleccionado.id,
            set_id: setSeleccionado ? setSeleccionado.id : null,
            participations: participationIds
        };

        // Agregar seller_id si es devolución de vendedor
        if (tipoDevolucion === 'vendedor' && vendedorSeleccionado) {
            datosResumen.seller_id = vendedorSeleccionado.id;
        }

        // Obtener resumen del servidor
        $.ajax({
            url: "{{ route('devolutions.liquidation-summary') }}",
            method: 'GET',
            data: datosResumen,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                if (response.success) {
                    const summary = response.summary;
                    console.log('Resumen calculado:', summary);
                    
                    $('#liquidacion-total-participaciones').text(summary.total_participations);
                    $('#liquidacion-ventas-registradas').text(summary.sold_participations);
                    $('#liquidacion-participaciones-devueltas').text(summary.returned_participations);
                    $('#liquidacion-disponibles').text(summary.available_participations);
                    $('#liquidacion-total-liquidacion').text(summary.total_liquidation.toFixed(2) + '€');
                    $('#liquidacion-pagos-registrados').text(summary.registered_payments.toFixed(2) + '€');
                    $('#liquidacion-total-pagar').text(summary.total_to_pay.toFixed(2) + '€');
                    $('#liquidacion-importe-total').text(summary.total_to_pay.toFixed(2) + '€');
                    
                    // Actualizar información del ticket
                    $('#liquidacion-ticket-number').text('#' + summary.total_participations);
                    
                    console.log('Resumen actualizado en la interfaz');
                } else {
                    console.error('Error en respuesta del servidor:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar resumen de liquidación:', error);
                console.error('Detalles del error:', xhr.responseText);
            }
        });
    }

    // Función para cargar sets de la entidad
    function cargarSetsEntidad() {
        if (!entidadSeleccionada || !sorteoSeleccionado) return;
        
        $.ajax({
            url: "{{ route('devolutions.sets-by-entity') }}",
            method: 'GET',
            data: {
                entity_id: entidadSeleccionada.id,
                lottery_id: sorteoSeleccionado.id
            },
            success: function(response) {
                const selector = $('#selector-set');
                selector.empty().append('<option value="">Seleccionar set...</option>');
                
                if (response.success && response.sets && response.sets.length > 0) {
                    response.sets.forEach(set => {
                        // Obtener los números de reserva desde la relación reserve
                        let reservationNumbers = '';
                        if (set.reserve && set.reserve.reservation_numbers && Array.isArray(set.reserve.reservation_numbers) && set.reserve.reservation_numbers.length > 0) {
                            reservationNumbers = set.reserve.reservation_numbers.join(' - ');
                        } else {
                            reservationNumbers = set.reserve_id.toString().padStart(5, '0');
                        }
                        
                        const setNumber = set.set_number.toString().padStart(2, '0');
                        const displayText = `${set.set_name} (${reservationNumbers} - ${setNumber})`;
                        
                        selector.append(`<option value="${set.id}">${displayText}</option>`);
                    });
                } else {
                    mostrarMensaje('No hay sets disponibles para devolver en este sorteo', 'warning');
                }
                
                // NO reiniciar las participaciones asignadas al cargar sets
                actualizarResumenAsignacion();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar sets:', error);
                mostrarMensaje('Error al cargar los sets de la entidad', 'error');
            }
        });
    }

    // Función para cargar sets del vendedor
    function cargarSetsVendedor() {
        if (!entidadSeleccionada || !sorteoSeleccionado || !vendedorSeleccionado) return;
        
        $.ajax({
            url: "{{ route('devolutions.sets-by-entity') }}",
            method: 'GET',
            data: {
                entity_id: entidadSeleccionada.id,
                lottery_id: sorteoSeleccionado.id,
                seller_id: vendedorSeleccionado.id  // Filtrar por vendedor
            },
            success: function(response) {
                const selector = $('#selector-set');
                selector.empty().append('<option value="">Seleccionar set...</option>');
                
                if (response.success && response.sets && response.sets.length > 0) {
                    response.sets.forEach(set => {
                        // Obtener los números de reserva desde la relación reserve
                        let reservationNumbers = '';
                        if (set.reserve && set.reserve.reservation_numbers && Array.isArray(set.reserve.reservation_numbers) && set.reserve.reservation_numbers.length > 0) {
                            reservationNumbers = set.reserve.reservation_numbers.join(' - ');
                        } else {
                            reservationNumbers = set.reserve_id.toString().padStart(5, '0');
                        }
                        
                        const setNumber = set.set_number.toString().padStart(2, '0');
                        const displayText = `${set.set_name} (${reservationNumbers} - ${setNumber})`;
                        
                        selector.append(`<option value="${set.id}">${displayText}</option>`);
                    });
                } else {
                    mostrarMensaje('No hay sets disponibles para devolver de este vendedor', 'warning');
                }
                
                // NO reiniciar las participaciones asignadas al cargar sets
                actualizarResumenAsignacion();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar sets del vendedor:', error);
                mostrarMensaje('Error al cargar los sets del vendedor', 'error');
            }
        });
    }

    // Event listener para selección de set
    $('#selector-set').on('change', function() {
        const setId = $(this).val();
        setSeleccionado = setId ? { id: setId, name: $(this).find('option:selected').text() } : null;
        
        // NO reiniciar las participaciones asignadas para permitir selección de múltiples sets
        actualizarResumenAsignacion();
    });

    // Función para validar participaciones
    function validarParticipacionesDisponibles(desde, hasta, participationId) {
        return new Promise((resolve, reject) => {
            const datosValidacion = {
                    entity_id: entidadSeleccionada.id,
                    lottery_id: sorteoSeleccionado.id,
                    set_id: setSeleccionado.id,
                    desde: desde,
                    hasta: hasta,
                    participation_id: participationId,
                    _token: '{{ csrf_token() }}'
            };

            // Agregar seller_id si es devolución de vendedor
            if (tipoDevolucion === 'vendedor' && vendedorSeleccionado) {
                datosValidacion.seller_id = vendedorSeleccionado.id;
            }

            $.ajax({
                url: "{{ route('devolutions.validate') }}",
                method: 'POST',
                data: datosValidacion,
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
        if (!setSeleccionado) {
            mostrarMensaje('Por favor selecciona un set antes de asignar participaciones', 'warning');
            return;
        }

        const desde = $('#rango-desde').val();
        const hasta = $('#rango-hasta').val();
        const unidad = $('#participacion-unidad').val();

        if (desde && hasta) {
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
                                    set_id: setSeleccionado.id,
                                    set_name: setSeleccionado.name,
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
                                set_id: setSeleccionado.id,
                                set_name: setSeleccionado.name,
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

    // Event listener para terminar asignación (ir directo a liquidación)
    $('#btn-terminar-asignacion').click(function() {
        // Para devolución de vendedor, no se requieren participaciones específicas
        if (tipoDevolucion === 'vendedor') {
            mostrarPaso('paso-liquidacion');
            configurarLiquidacionPorTipo();
            return;
        }

        // Para devolución de administración, permitir continuar sin participaciones
        if (participacionesAsignadas.length === 0) {
            const confirmContinue = confirm('No has seleccionado participaciones para devolver. ¿Deseas continuar solo para registrar una liquidación?');
            if (!confirmContinue) {
                return;
            }
        }

        // Ir directo a liquidación
        mostrarPaso('paso-liquidacion');
        configurarLiquidacionPorTipo();
    });

    // Event listener para continuar sin participaciones (ir directo a liquidación)
    $('#btn-continuar-sin-participaciones').click(function() {
        if (tipoDevolucion === 'vendedor') {
            mostrarPaso('paso-liquidacion');
            configurarLiquidacionPorTipo();
            return;
        }

        const confirmContinue = confirm('¿Deseas continuar sin seleccionar participaciones? Solo podrás registrar un pago de liquidación.');
        if (!confirmContinue) {
            return;
        }

        // Ir directo a liquidación
        mostrarPaso('paso-liquidacion');
        configurarLiquidacionPorTipo();
    });

    // Función para configurar la liquidación según el tipo
    function configurarLiquidacionPorTipo() {
        if (tipoDevolucion === 'vendedor') {
            // Liquidación de vendedor
            $('#liquidacion-titulo').text('Liquidación de Vendedor');
            $('#liquidacion-subtitulo').html('<i>Registra pagos del vendedor</i>');
            $('#btn-volver-text').text('Volver a Vendedor');
            $('#liquidacion-vendedor-container').show();
            $('#liquidacion-administracion-container').hide();
            
            // Cargar sorteos disponibles para el vendedor
            cargarSorteosVendedor();
            
            // Resetear selector y ocultar resumen
            $('#vendedor-selector-sorteo-liquidacion').val('');
            $('#vendedor-resumen-liquidacion-container').hide();
        } else {
            // Liquidación de administración
            $('#liquidacion-titulo').text('Liquidación de Administración');
            $('#liquidacion-subtitulo').html('<i>Procesa la liquidación de participaciones</i>');
            $('#btn-volver-text').text('Volver a Participaciones');
            $('#liquidacion-vendedor-container').hide();
            $('#liquidacion-administracion-container').show();
            cargarParticipacionesParaLiquidacion();
            actualizarResumenLiquidacion();
        }
    }

    // Función para cargar participaciones para liquidación
    function cargarParticipacionesParaLiquidacion() {
        let html = '';
        
        if (participacionesAsignadas.length === 0) {
            html = `
                <div class="alert alert-info" role="alert">
                    <i class="ri-information-line me-2"></i>
                    No hay participaciones seleccionadas. Puedes continuar para registrar solo un pago de liquidación.
                </div>
            `;
        } else {
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
        }
        
        $('#liquidacion-participaciones').html(html);
    }

    // Event listener para procesar liquidación
    $('#btn-procesar-liquidacion').click(function() {
        // Permitir procesar liquidación sin participaciones (solo para registrar pago)
        $('#btn-procesar-liquidacion').prop('disabled', true).text('Procesando...');

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

        // Enviar datos al servidor (sin seller_id)
        $.ajax({
            url: "{{ route('devolutions.store') }}",
            method: 'POST',
            data: {
                entity_id: entidadSeleccionada.id,
                lottery_id: sorteoSeleccionado.id,
                participations: participacionesAsignadas.map(p => p.id),
                return_reason: 'Devolución de entidad a administración',
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

    // Función para actualizar el total pagado
    function actualizarTotalPagado() {
        const efectivoMonto = parseFloat($('#pago-efectivo-monto').val()) || 0;
        const bizumMonto = parseFloat($('#pago-bizum-monto').val()) || 0;
        const transferenciaMonto = parseFloat($('#pago-transferencia-monto').val()) || 0;
        
        const totalPagado = efectivoMonto + bizumMonto + transferenciaMonto;
        $('#total-pagado').text(totalPagado.toFixed(2) + '€');
        
        // Calcular pendiente
        const totalAPagar = parseFloat($('#liquidacion-total-pagar').text().replace('€', '').replace(',', '.')) || 0;
        const pendiente = totalAPagar - totalPagado;
        
        $('#total-pendiente').text(pendiente.toFixed(2) + '€');
        
        if (totalPagado > 0) {
            $('#pendiente-container').show();
            // Cambiar color según si está completo o no
            if (pendiente <= 0) {
                $('#total-pendiente').removeClass('text-warning').addClass('text-success');
            } else {
                $('#total-pendiente').removeClass('text-success').addClass('text-warning');
            }
        } else {
            $('#pendiente-container').hide();
        }
    }
    
    // Event listeners para actualizar total al cambiar montos
    $('.payment-input').on('input', actualizarTotalPagado);

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

        // Validar que al menos haya participaciones o un pago
        if (participacionesAsignadas.length === 0 && pagos.length === 0) {
            mostrarMensaje('Debes seleccionar participaciones o registrar al menos un pago', 'warning');
            return;
        }

        // Preparar datos para la liquidación
        const liquidacionData = {
            entity_id: entidadSeleccionada.id,
            lottery_id: sorteoSeleccionado.id,
            set_id: setSeleccionado ? setSeleccionado.id : null,
            participations: participacionesAsignadas.map(p => p.id),
            return_reason: tipoDevolucion === 'vendedor' ? 'Devolución de vendedor a entidad' : 'Devolución de entidad a administración',
            tipo_devolucion: tipoDevolucion, // Agregar tipo de devolución
            liquidacion: {
                pagos: pagos, // Array de pagos múltiples
                devolver: participacionesAsignadas.map(p => p.id), // Las seleccionadas se devuelven (puede estar vacío)
                vender: [] // Se calculará en el backend
            },
            _token: '{{ csrf_token() }}'
        };

        // Agregar seller_id si es devolución de vendedor
        if (tipoDevolucion === 'vendedor' && vendedorSeleccionado) {
            liquidacionData.seller_id = vendedorSeleccionado.id;
        }

        $(this).prop('disabled', true).text('Procesando...');

        $.ajax({
            url: "{{ route('devolutions.store') }}",
            method: 'POST',
            data: liquidacionData,
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
                console.error('Error en liquidación:', error);
                mostrarMensaje('Error al procesar la liquidación', 'error');
            },
            complete: function() {
                $('#btn-aceptar-liquidacion').prop('disabled', false).text('Aceptar');
            }
        });
    });

    // Event listener para cerrar liquidación
    $('#btn-cerrar-liquidacion').click(function() {
        window.location.href = "{{ route('devolutions.index') }}";
    });

    // ==================== LIQUIDACIÓN DE VENDEDOR ====================
    
    let sorteoSeleccionadoLiquidacionVendedor = null;

    // Función para cargar sorteos disponibles del vendedor
    function cargarSorteosVendedor() {
        if (!vendedorSeleccionado || !entidadSeleccionada) {
            console.error('Falta vendedor o entidad seleccionada');
            return;
        }

        $.ajax({
            url: '{{ route("devolutions.lotteries") }}',
            method: 'GET',
            data: {
                entity_id: entidadSeleccionada.id,
                seller_id: vendedorSeleccionado.id
            },
            success: function(response) {
                if (response.success && response.lotteries) {
                    const selector = $('#vendedor-selector-sorteo-liquidacion');
                    selector.empty().append('<option value="">-- Selecciona un sorteo --</option>');
                    
                    response.lotteries.forEach(lottery => {
                        selector.append(`<option value="${lottery.id}">${lottery.name} - ${lottery.description}</option>`);
                    });
                } else {
                    mostrarMensaje('No hay sorteos disponibles para este vendedor', 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar sorteos:', error);
                mostrarMensaje('Error al cargar sorteos', 'error');
            }
        });
    }

    // Event listener para cambio de sorteo en liquidación de vendedor
    $('#vendedor-selector-sorteo-liquidacion').on('change', function() {
        const lotteryId = $(this).val();
        
        if (lotteryId) {
            sorteoSeleccionadoLiquidacionVendedor = lotteryId;
            cargarResumenLiquidacionVendedor();
            cargarHistorialLiquidacionesVendedor();
            $('#vendedor-resumen-liquidacion-container').show();
        } else {
            sorteoSeleccionadoLiquidacionVendedor = null;
            $('#vendedor-resumen-liquidacion-container').hide();
        }
    });

    // Función para cargar resumen de liquidación de vendedor (COPIA EXACTA DE SELLERS)
    function cargarResumenLiquidacionVendedor() {
        if (!sorteoSeleccionadoLiquidacionVendedor) return;

        $.ajax({
            url: '{{ route("sellers.settlement-summary") }}',
            method: 'GET',
            data: {
                seller_id: vendedorSeleccionado.id,
                lottery_id: sorteoSeleccionadoLiquidacionVendedor
            },
            success: function(response) {
                console.log('=== RESPUESTA SETTLEMENT SUMMARY ===');
                console.log(response);
                
                if (response.success) {
                    const summary = response.summary;
                    console.log('Summary:', summary);
                    
                    // Parsear valores a números
                    const pricePerParticipation = parseFloat(summary.price_per_participation) || 0;
                    const totalAmount = parseFloat(summary.total_amount) || 0;
                    const totalPaid = parseFloat(summary.total_paid) || 0;
                    const pendingAmount = parseFloat(summary.pending_amount) || 0;
                    const liquidatedParticipations = parseFloat(summary.liquidated_participations) || 0;
                    const pendingParticipations = parseFloat(summary.pending_participations) || 0;
                    
                    $('#vendedor-settlement-total-participations').text(summary.total_participations);
                    $('#vendedor-settlement-price-per-participation').text(pricePerParticipation.toFixed(2) + '€');
                    $('#vendedor-settlement-total-amount').text(totalAmount.toFixed(2) + '€');
                    $('#vendedor-settlement-total-paid').text(totalPaid.toFixed(2) + '€');
                    $('#vendedor-settlement-liquidated-participations').text(liquidatedParticipations.toFixed(2));
                    $('#vendedor-settlement-pending-amount').text(pendingAmount.toFixed(2) + '€');
                    $('#vendedor-settlement-pending-participations').text(pendingParticipations.toFixed(2));
                    $('#vendedor-settlement-pendiente-display').text(pendingAmount.toFixed(2) + '€');
                    
                    console.log('Datos actualizados en la vista');
                    
                    // Resetear campos de pago
                    actualizarTotalPagarAhoraSettlementVendedor();
                } else {
                    console.error('Response no exitoso:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar resumen:', error);
                mostrarMensaje('Error al cargar el resumen de liquidación', 'error');
            }
        });
    }

    // Función para actualizar total a pagar ahora (COPIA EXACTA DE SELLERS)
    function actualizarTotalPagarAhoraSettlementVendedor() {
        const efectivo = parseFloat($('#vendedor-settlement-pago-efectivo').val()) || 0;
        const bizum = parseFloat($('#vendedor-settlement-pago-bizum').val()) || 0;
        const transferencia = parseFloat($('#vendedor-settlement-pago-transferencia').val()) || 0;
        
        const totalPagarAhora = efectivo + bizum + transferencia;
        $('#vendedor-settlement-pagar-ahora').text(totalPagarAhora.toFixed(2) + '€');
        
        const pendiente = parseFloat($('#vendedor-settlement-pending-amount').text().replace('€', '').replace(',', '.')) || 0;
        const quedaraPendiente = pendiente - totalPagarAhora;
        
        $('#vendedor-settlement-quedara-pendiente').text(quedaraPendiente.toFixed(2) + '€');
        
        if (quedaraPendiente <= 0 && totalPagarAhora > 0) {
            $('#vendedor-settlement-quedara-pendiente').removeClass('text-warning').addClass('text-success');
        } else if (totalPagarAhora > 0) {
            $('#vendedor-settlement-quedara-pendiente').removeClass('text-success').addClass('text-warning');
        } else {
            $('#vendedor-settlement-quedara-pendiente').removeClass('text-success text-warning');
        }
    }

    // Event listeners para actualizar totales
    $('.vendedor-settlement-payment-input').on('input', actualizarTotalPagarAhoraSettlementVendedor);

    // Botón para registrar liquidación (COPIA EXACTA DE SELLERS)
    $('#btn-registrar-liquidacion-vendedor').on('click', function() {
        if (!sorteoSeleccionadoLiquidacionVendedor) {
            mostrarMensaje('Debes seleccionar un sorteo primero', 'warning');
            return;
        }

        // Recopilar pagos
        const pagos = [];
        
        const efectivo = parseFloat($('#vendedor-settlement-pago-efectivo').val()) || 0;
        if (efectivo > 0) {
            pagos.push({ payment_method: 'efectivo', amount: efectivo });
        }
        
        const bizum = parseFloat($('#vendedor-settlement-pago-bizum').val()) || 0;
        if (bizum > 0) {
            pagos.push({ payment_method: 'bizum', amount: bizum });
        }
        
        const transferencia = parseFloat($('#vendedor-settlement-pago-transferencia').val()) || 0;
        if (transferencia > 0) {
            pagos.push({ payment_method: 'transferencia', amount: transferencia });
        }

        if (pagos.length === 0) {
            mostrarMensaje('Debes ingresar al menos un monto de pago', 'warning');
            return;
        }

        // Deshabilitar botón
        $(this).prop('disabled', true).text('Procesando...');

        $.ajax({
            url: '{{ route("sellers.settlement.store") }}',
            method: 'POST',
            data: {
                seller_id: vendedorSeleccionado.id,
                lottery_id: sorteoSeleccionadoLiquidacionVendedor,
                pagos: pagos,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('Liquidación registrada correctamente', 'success');
                    
                    // Limpiar campos de pago
                    $('#vendedor-settlement-pago-efectivo, #vendedor-settlement-pago-bizum, #vendedor-settlement-pago-transferencia').val('');
                    
                    // Recargar datos
                    setTimeout(() => {
                        cargarResumenLiquidacionVendedor();
                        cargarHistorialLiquidacionesVendedor();
                    }, 1000);
                } else {
                    mostrarMensaje(response.message || 'Error al registrar liquidación', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensaje('Error al registrar la liquidación', 'error');
            },
            complete: function() {
                $('#btn-registrar-liquidacion-vendedor').prop('disabled', false).html('<i class="ri-add-line"></i> Registrar Liquidación');
            }
        });
    });

    // Función para cargar historial de liquidaciones (COPIA EXACTA DE SELLERS)
    function cargarHistorialLiquidacionesVendedor() {
        if (!sorteoSeleccionadoLiquidacionVendedor) return;

        $.ajax({
            url: '{{ route("sellers.settlement-history") }}',
            method: 'GET',
            data: {
                seller_id: vendedorSeleccionado.id,
                lottery_id: sorteoSeleccionadoLiquidacionVendedor
            },
            success: function(response) {
                if (response.success && response.settlements.length > 0) {
                    let html = '<div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-light"><tr><th>Fecha</th><th>Participaciones Liquidadas</th><th>Monto Pagado</th><th>Métodos de Pago</th></tr></thead><tbody>';
                    
                    response.settlements.forEach(settlement => {
                        const fecha = new Date(settlement.settlement_date).toLocaleDateString('es-ES');
                        
                        let metodos = [];
                        settlement.payments.forEach(payment => {
                            let icono = '';
                            if (payment.payment_method == 'efectivo') {
                                icono = '<i class="ri-wallet-line text-success"></i>';
                            } else if (payment.payment_method == 'bizum') {
                                icono = '<i class="ri-smartphone-line text-info"></i>';
                            } else if (payment.payment_method == 'transferencia') {
                                icono = '<i class="ri-bank-line text-primary"></i>';
                            }
                            const paymentAmount = parseFloat(payment.amount) || 0;
                            metodos.push(`${icono} ${paymentAmount.toFixed(2)}€`);
                        });
                        
                        const calculatedParts = parseFloat(settlement.calculated_participations) || 0;
                        const paidAmount = parseFloat(settlement.paid_amount) || 0;
                        
                        html += `
                            <tr>
                                <td>${fecha}</td>
                                <td>${calculatedParts.toFixed(2)}</td>
                                <td class="fw-bold text-success">${paidAmount.toFixed(2)}€</td>
                                <td>${metodos.join(', ')}</td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    $('#vendedor-historial-liquidaciones-container').html(html);
                } else {
                    $('#vendedor-historial-liquidaciones-container').html('<p class="text-muted text-center">No hay liquidaciones registradas para este sorteo</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar historial:', error);
                $('#vendedor-historial-liquidaciones-container').html('<p class="text-danger text-center">Error al cargar el historial</p>');
            }
        });
    }
});
</script>

@endsection

