@extends('layouts.layout')

@section('title','Vendedores/Asignación')

@section('content')

<style>
    
    .form-wizard-element, .form-wizard-element label {
        cursor: pointer;
    }
    .form-check-input:checked {
        border-color: #333;
    }

    .part-information {
        transition: all 500ms;
    }

    /* Estilos para la funcionalidad de asignación */
    .asignacion-paso {
        transition: all 0.3s ease;
    }

    .asignacion-paso table {
        margin-top: 20px;
    }

    .asignacion-paso .btn-seleccionar {
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
    }

    .asignacion-paso .btn-volver {
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
    }

    /* Animación para transiciones entre pasos */
    .asignacion-paso.fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Estilos para las participaciones asignadas */
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
</style>


<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('sellers.index') }}">Vendedores/Asignación</a></li>
                        <li class="breadcrumb-item active">Ver Vendedor</li>
                    </ol>
                </div>
                <h4 class="page-title">Vendedores/Asignación</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">
                        Detalles del Vendedor
                    </h4>

                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <ul class="form-card bs mb-3 nav">

                                <li class="nav-item">

                                    <div class="form-wizard-element active" data-bs-toggle="tab" data-bs-target="#datos_vendedor">
                                        <span>
                                            &nbsp;&nbsp;
                                        </span>
                                        <img src="{{url('icons/vendedores.svg')}}" alt="">
                                        <label>
                                            Dat. Vendedor
                                        </label>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#asignacion">
                                        
                                        <span>
                                            &nbsp;&nbsp;
                                        </span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>
                                            Asignación
                                        </label>

                                    </div>

                                </li>

                                <li class="nav-item">
                                    <div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#participaciones">
                                        
                                        <span>
                                            &nbsp;&nbsp;
                                        </span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>
                                            Participaciones
                                        </label>

                                    </div>

                                </li>

                                <li class="nav-item">
                                    <div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#liquidacion">
                                        
                                        <span>
                                            &nbsp;&nbsp;
                                        </span>
                                        <img src="{{url('icons/participaciones.svg')}}" alt="">
                                        <label>
                                            Liquidación
                                        </label>

                                    </div>

                                </li>
                            </ul>

                                                         <!-- Información del Vendedor -->
                             <div class="form-card bs mb-3">
                                 <div class="row">
                                     <div class="col-4">
                                         <div class="photo-preview-3">
                                             <i class="ri-account-circle-fill"></i>
                                         </div>
                                         <div style="clear: both;"></div>
                                     </div>
                                     <div class="col-8 text-center mt-2">
                                         <h3 class="mt-2 mb-0">{{ $seller->name ?? 'N/A' }}</h3>
                                         <i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-mail-line"></i> {{ $seller->email ?? 'N/A' }}
                                     </div>
                                 </div>
                             </div>

                             <!-- Información de la Entidad -->
                             <div class="form-card bs mb-3">
                                 <div class="row">
                                     <div class="col-4">
                                         <div class="photo-preview-3">
                                             <i class="ri-account-circle-fill"></i>
                                         </div>
                                         <div style="clear: both;"></div>
                                     </div>
                                     <div class="col-8 text-center mt-2">
                                         <h3 class="mt-2 mb-0">{{ $seller->entity->name ?? 'Entidad' }}</h3>
                                         <i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{ $seller->entity->province ?? 'Provincia' }}
                                     </div>
                                 </div>
                             </div>

                            <a href="{{ route('sellers.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>

                        <div class="col-md-9">

                            <div class="tabbable">
                                
                                <div class="tab-content p-0">
                                    
                                    <div class="tab-pane fade active show" id="datos_vendedor">


                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center show-content">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Información del Vendedor
                                                    </h4>
                                                    <small><i>Detalles completos del vendedor</i></small>
                                                </div>
                                                <div>
                                                    <a href="{{ route('sellers.edit', $seller->id) }}" class="btn btn-light" style="border: 1px solid silver; border-radius: 30px;"> 
                                                        <img src="{{url('assets/form-groups/edit.svg')}}" alt="">
                                                        Editar
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="form-group mt-2 mb-3 admin-box">
                                                <div class="row">
                                                    <div class="col-1">
                                                        <div class="photo-preview-3">
                                                            <i class="ri-account-circle-fill"></i>
                                                        </div>
                                                        <div style="clear: both;"></div>
                                                    </div>

                                                    <div class="col-4 text-center mt-3">
                                                        <h4 class="mt-0 mb-0">{{ $seller->entity->name ?? 'Entidad' }}</h4>
                                                        <small>{{ $seller->entity->province ?? 'Provincia' }}</small> <br>
                                                        <small>{{ $seller->entity->administration->name ?? 'Administración' }}</small>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mt-3">
                                                            Provincia: {{ $seller->entity->province ?? 'N/A' }} <br>
                                                            Dirección: {{ $seller->entity->address ?? 'N/A' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="mt-3">
                                                            Ciudad: {{ $seller->entity->city ?? 'N/A' }} <br>
                                                            Tel: {{ $seller->entity->phone ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <div class="row show-content">
                                                <div class="col-md-4">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Nombre</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->name ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Primer Apellido</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->last_name ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Segundo Apellido</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->last_name2 ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row show-content">
                                                <div class="col-md-3">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">NIF/CIF</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->nif_cif ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">F. Nacimiento</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->birthday ? \Carbon\Carbon::parse($seller->birthday)->format('d/m/Y') : 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Email</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/9.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="email" value="{{ $seller->email ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Teléfono</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/10.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->phone ?? 'N/A' }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                {{-- <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Order ID</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="#VN{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Estado</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->status_text }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>

                                            {{-- @if($seller->comment)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group mt-2 mb-3">
                                                        <label class="label-control">Comentarios</label>
                                                        <div class="input-group input-group-merge group-form">
                                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                                <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
                                                            </div>
                                                            <input class="form-control" type="text" value="{{ $seller->comment }}" style="border-radius: 0 30px 30px 0;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif --}}

                                        </div>

                                    </div>



                                    <div class="tab-pane fade" id="asignacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                                                                         <!-- Paso 1: Selección de Reservas -->
                                             <div id="paso-reservas" class="asignacion-paso" style="display: none;">
                                                 <div class="d-flex justify-content-between align-items-center">
                                                     <div>
                                                         <h4 class="mb-0 mt-1">
                                                             Participaciones
                                                         </h4>
                                                         <small><i>Selecciona una reserva para continuar</i></small>
                                                     </div>
                                                 </div>

                                                 <br>

                                                                                                   <div style="min-height: 656px;">
                                                      <table id="tabla-reservas" class="table table-striped nowrap w-100">
                                                          <thead class="">
                                                              <tr>
                                                                  <th>Orden ID</th>
                                                                  <th>N.Sorteo</th>
                                                                  <th>Fecha Sorteo</th>
                                                                  <th>Nombre Sorteo</th>
                                                                  <th>Numero/s</th>
                                                                  <th>Importe <br> (Número)</th>
                                                                  <th>Décimos <br> (Número)</th>
                                                                  <th>Importe <br> TOTAL</th>
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
                                                          <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-reservas" disabled>
                                                              Siguiente
                                                              <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                          </button>
                                                      </div>
                                                  </div>
                                             </div>

                                                                                         <!-- Paso 2: Selección de Sets -->
                                             <div id="paso-sets" class="asignacion-paso" style="display: none;">
                                                 <div class="d-flex justify-content-between align-items-center">
                                                     <div>
                                                         <h4 class="mb-0 mt-1">
                                                             Set en el que Asignar participaciones
                                                         </h4>
                                                         <small><i>Selecciona un set para continuar</i></small>
                                                     </div>
                                                     <button id="btn-volver-reservas" class="btn btn-secondary btn-sm">
                                                         <i class="ri-arrow-left-line"></i> Volver a Reservas
                                                     </button>
                                                 </div>

                                                 <br>

                                                                                                   <div style="min-height: 656px;">
                                                      <table id="tabla-sets" class="table table-striped nowrap w-100">
                                                          <thead class="">
                                                              <tr>
                                                                  <th>Orden ID</th>
                                                                  <th>Nombre Set</th>
                                                                  <th>Importe Jugado <br> (por Número)</th>
                                                                  <th>Importe Donativo</th>
                                                                  <th>Importe TOTAL</th>
                                                                  <th>Participaciones <br> Físicas</th>
                                                                  <th>Participaciones <br> Disponibles</th>
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
                                                          <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-sets" disabled>
                                                              Siguiente
                                                              <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                          </button>
                                                      </div>
                                                  </div>
                                             </div>

                                            <!-- Paso 3: Asignación de Participaciones -->
                                            <div id="paso-asignacion" class="asignacion-paso" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 mt-1">
                                                            Asigna las participaciones
                                                        </h4>
                                                        <small><i>Individual o por rango</i></small>
                                                    </div>
                                                    <button id="btn-volver-sets" class="btn btn-secondary btn-sm">
                                                        <i class="ri-arrow-left-line"></i> Volver a Sets
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
                                                            <h4 class="mb-0 mt-1">
                                                                Resumen Asignación
                                                            </h4>
                                                            <small><i>comprueba que la asignación sea la correcta </i></small>
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
                                                            <div class="row" id="grid-participaciones">
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
                                                                Terminar
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <!-- Estado inicial: Mostrar botón para comenzar -->
                                            <div id="estado-inicial" class="asignacion-paso">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 mt-1">
                                                            Asignación de Participaciones
                                                        </h4>
                                                        <small><i>Comienza seleccionando una reserva</i></small>
                                                    </div>
                                                </div>

                                                <br>
                                                <br>

                                                <div class="d-flex align-items-center gap-1">
                                                    <div class="empty-tables">
                                                        <div>
                                                            <img src="{{url('icons/participaciones.svg')}}" alt="" width="80px" style="margin-top: 10px;">
                                                        </div>
                                        
                                                        <h3 class="mb-0">Participaciones del vendedor</h3>
                                        
                                                        <small>Asigna Participaciones</small>
                                        
                                                        <br>
                                        
                                                        <button id="btn-iniciar-asignacion" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2">
                                                            <i style="position: relative; top: 2px;" class="ri-add-line"></i> Asignar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="participaciones">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <!-- Paso 1: Selección de Reservas -->
                                            <div id="paso-reservas-participaciones" class="participaciones-paso">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 mt-1">
                                                            Reserva en la que Asignar participaciones
                                                        </h4>
                                                        <small><i>Selecciona una reserva para continuar</i></small>
                                                    </div>
                                                </div>

                                                <br>

                                                <div style="min-height: 656px;">
                                                    <table id="tabla-reservas-participaciones" class="table table-striped nowrap w-100">
                                                        <thead class="">
                                                            <tr>
                                                                <th>Orden ID</th>
                                                                <th>N.Sorteo</th>
                                                                <th>Fecha Sorteo</th>
                                                                <th>Nombre Sorteo</th>
                                                                <th>Numero/s</th>
                                                                <th>Importe <br> (Número)</th>
                                                                <th>Décimos <br> (Número)</th>
                                                                <th>Importe <br> TOTAL</th>
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
                                                        <button type="button" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="btn-siguiente-reservas-participaciones" disabled>
                                                            Siguiente
                                                            <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Paso 2: Mostrar Tacos del Vendedor -->
                                            <div id="paso-tacos-participaciones" class="participaciones-paso" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 mt-1">
                                                            Tacos del Vendedor
                                                        </h4>
                                                        <small><i>Participaciones asignadas por taco</i></small>
                                                    </div>
                                                    <button id="btn-volver-reservas-participaciones" class="btn btn-secondary btn-sm">
                                                        <i class="ri-arrow-left-line"></i> Volver a Reservas
                                                    </button>
                                                </div>

                                                <br>

                                                <div style="min-height: 656px;" id="contenedor-tacos">
                                                    <!-- Los tacos se cargarán dinámicamente aquí -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="liquidacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Liquidación
                                                    </h4>
                                                    <small><i>Resumen Liquidación total</i></small>
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

function initDatatable() 
  {
    $("#example2").DataTable({

      "select":{style:"single"},

      "ordering": false,
      "sorting": false,

      "scrollX": true, "scrollCollapse": true,
        orderCellsTop: true,
        fixedHeader: true,
        initComplete: function () {
            var api = this.api();
 
            // For each column
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    // Set the header cell to contain the input element
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                    var title = $(cell).text();
                    if ($(cell).hasClass('no-filter')) {
                      $(cell).addClass('sorting_disabled').html(title);
                    }else{
                      $(cell).addClass('sorting_disabled').html('<input type="text" class="inline-fields" placeholder="' + title + '" />');
                    }
 
                    // On every keypress in this input
                    $(
                        'input',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('keyup change')
                        .on('keyup change', function (e) {
                            e.stopPropagation();
 
                            // Get the search value
                            $(this).attr('title', $(this).val());
                            var regexr = '({search})'; //$(this).parents('th').find('select').val();
 
                            var cursorPosition = this.selectionStart;
                            // Search the column for that value

                            // console.log(val.replace(/<select[\s\S]*?<\/select>/,''));
                            let wSelect = false;
                            $.each(api.column(colIdx).data(), function(index, val) {
                               if (val.indexOf('<select') == -1) {
                                wSelect = false;
                               }else{
                                wSelect = true;
                               }
                            });

                            // $.each(api
                            //     .column(colIdx).data(), function(index, val) {
                            //         console.log(val)
                            // });

                            api
                                .column(colIdx)
                                .search(

                                  (wSelect ?
                                      (this.value != ''
                                        ? regexr.replace('{search}', '(((selected' + this.value + ')))')
                                        : '')
                                    :
                                      (this.value != ''
                                        ? regexr.replace('{search}', '(((' + this.value + ')))')
                                        : '')),

                                    this.value != '',
                                    this.value == ''
                                ).draw()
 
                            $(this)
                                .focus()[0]
                                .setSelectionRange(cursorPosition, cursorPosition);
                        });
                });
        }
    });
  }

  var init1 = false;

  setTimeout(()=>{
    $('.filters .inline-fields:first').trigger('keyup');
  },100);

  {{-- $('[data-bs-target="#participaciones"]').click(function (e) {
    if (!init1) {
      initDatatable();
      init1 =true;
    }
  }); --}}

  $('.show-details').click(function (e) {
      e.preventDefault();

      if ($(this).parents('.form-card').find('.part-information').css('height') == '0px') {
        $(this).parents('.form-card').find('.part-information').css('height', '250px');
      }else{
        $(this).parents('.form-card').find('.part-information').css('height', '0px');
        {{-- setTimeout(()=>{
            $(this).parents('.form-card').find('#details-participations').addClass('d-none');
            $(this).parents('.form-card').find('#list-participations').removeClass('d-none');
        },500); --}}
      }

  });

  {{-- $('.show-details').click(function(event) {
      $(this).parents('.form-card').find('#details-participations').removeClass('d-none');
      $(this).parents('.form-card').find('#list-participations').addClass('d-none');
  }); --}}



  // Funcionalidad para asignación de participaciones
  $(document).ready(function() {
    // Variables globales para almacenar datos
    let reservaSeleccionada = null;
    let setSeleccionado = null;
    
    // Inicializar DataTables para las tablas de reservas y sets
    let tablaReservas = null;
    let tablaSets = null;
    
    // Función para inicializar DataTable de reservas
    function inicializarDataTableReservas() {
      if (tablaReservas) {
        return; // Ya está inicializada
      }
      
      tablaReservas = $('#tabla-reservas').DataTable({
        "select": { style: "single" },
        "ordering": false,
        "sorting": false,
        "scrollX": true,
        "scrollCollapse": true,
        "language": {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "columnDefs": [
          {
            "targets": -1, // Última columna (Seleccionar)
            "orderable": false,
            "searchable": false
          }
        ]
      });
    }
    
    // Función para inicializar DataTable de sets
    function inicializarDataTableSets() {
      if (tablaSets) {
        return; // Ya está inicializada
      }
      
      tablaSets = $('#tabla-sets').DataTable({
        "select": { style: "single" },
        "ordering": false,
        "sorting": false,
        "scrollX": true,
        "scrollCollapse": true,
        "language": {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "columnDefs": [
          {
            "targets": -1, // Última columna (Seleccionar)
            "orderable": false,
            "searchable": false
          }
        ]
      });
    }
    
    // Función para mostrar un paso específico
    function mostrarPaso(pasoId) {
      $('.asignacion-paso').hide();
      $('#' + pasoId).show();
      
      // Inicializar DataTables según el paso
      if (pasoId === 'paso-reservas' && !tablaReservas) {
        inicializarDataTableReservas();
      } else if (pasoId === 'paso-sets' && !tablaSets) {
        inicializarDataTableSets();
      }
      
      // Verificar que el botón existe
      if (pasoId === 'paso-reservas') {
        console.log('Botón siguiente existe:', $('#btn-siguiente-reservas').length > 0);
        console.log('Botón siguiente disabled:', $('#btn-siguiente-reservas').prop('disabled'));
      }
    }
    
    // Función para cargar datos de reservas desde la base de datos
    function cargarReservas() {
      // Inicializar DataTable si no existe
      if (!tablaReservas) {
        inicializarDataTableReservas();
      }
      
      // Limpiar datos existentes
      tablaReservas.clear();
      
      // Preparar los datos para agregar
      let datosReservas = [];
      
      // Los datos de reservas están disponibles en la variable PHP $reserves
      @if(isset($reserves) && $reserves->count() > 0)
        @foreach($reserves as $reserve)
          datosReservas.push([
            '#RS{{ str_pad($reserve->id, 4, '0', STR_PAD_LEFT) }}',
            '{{ $reserve->lottery ? $reserve->lottery->name : "Sin sorteo" }}',
            '{{ $reserve->lottery ? $reserve->lottery->draw_date->format('d-m-Y') : "Sin fecha" }}',
            '{{ $reserve->lottery ? $reserve->lottery->description : "N/A" }}',
            '{{ is_array($reserve->reservation_numbers) ? implode(' - ', $reserve->reservation_numbers) : ($reserve->reservation_numbers ?? 'Sin números') }}',
            '{{ number_format($reserve->reservation_amount ?? 0, 2) }}€',
            '{{ $reserve->reservation_tickets ?? 0 }}',
            '<b>{{ number_format($reserve->total_amount ?? 0, 2) }}€</b>',
            `<div class="form-check">
               <input class="form-check-input seleccionar-reserva" type="radio" name="reserve_id" value="{{ $reserve->id }}" id="reserve_{{ $reserve->id }}" data-reserva-id="{{ $reserve->id }}">
               <label class="form-check-label" for="reserve_{{ $reserve->id }}">
                 Seleccionar
               </label>
             </div>`
          ]);
        @endforeach
      @endif
      
      // Agregar los datos a la tabla
      if (datosReservas.length > 0) {
        tablaReservas.rows.add(datosReservas).draw();
      } else {
        tablaReservas.rows.add([['No hay reservas disponibles para esta entidad', '', '', '', '', '', '', '', '']]).draw();
      }
      
      console.log('Reservas cargadas correctamente con DataTable');
    }
    
    // Función para cargar sets de una reserva desde la base de datos
    function cargarSets(reservaId) {
      // Inicializar DataTable si no existe
      if (!tablaSets) {
        inicializarDataTableSets();
      }
      
      // Limpiar datos existentes
      tablaSets.clear();
      
      // Hacer llamada AJAX para obtener los sets de la reserva
      $.ajax({
        url: '{{ route("sellers.get-sets-by-reserve") }}',
        method: 'POST',
        data: {
          reserve_id: reservaId,
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          // Limpiar datos existentes
          tablaSets.clear();
          
          if (response.sets && response.sets.length > 0) {
            let datosSets = [];
            
            response.sets.forEach(set => {
              datosSets.push([
                `#SP${String(set.id).padStart(4, '0')}`,
                set.set_name,
                `${parseFloat(set.played_amount || 0).toFixed(2)}€`,
                `${parseFloat(set.donation_amount || 0).toFixed(2)}€`,
                `<b>${parseFloat(set.total_amount || 0).toFixed(2)}€</b>`,
                set.physical_participations || 0,
                set.total_participations || 0,
                `<div class="form-check">
                   <input class="form-check-input seleccionar-set" type="radio" name="set_id" value="${set.id}" id="set_${set.id}" data-set-id="${set.id}">
                   <label class="form-check-label" for="set_${set.id}">
                     Seleccionar
                   </label>
                 </div>`
              ]);
            });
            
            tablaSets.rows.add(datosSets).draw();
          } else {
            tablaSets.rows.add([['No hay sets disponibles para esta reserva', '', '', '', '', '', '', '']]).draw();
          }
          
          console.log('Sets cargados correctamente con DataTable');
        },
        error: function(xhr, status, error) {
          console.error('Error al cargar sets:', error);
          
          // Limpiar datos y mostrar error
          tablaSets.clear();
          tablaSets.rows.add([['Error al cargar los sets', '', '', '', '', '', '', '']]).draw();
        }
      });
    }
    
    // Event listeners
      
             // Cargar participaciones existentes al cargar la página
       $(document).ready(function() {
         // No cargar participaciones al inicio, solo cuando se seleccione un set
         participacionesAsignadas = [];
         actualizarResumenAsignacion();
       });
    
    // Botón para iniciar asignación
    $(document).on('click', '#btn-iniciar-asignacion', function() {
      cargarReservas();
      mostrarPaso('paso-reservas');
    });
    
    // Seleccionar reserva
    $(document).on('change', '.seleccionar-reserva', function() {
      console.log('Radio button seleccionado (change)');
      const reservaId = $(this).data('reserva-id');
      reservaSeleccionada = { id: reservaId };
      console.log('Reserva seleccionada ID:', reservaId);
      // Habilitar el botón siguiente
      $('#btn-siguiente-reservas').prop('disabled', false);
      console.log('Botón habilitado');
    });
    
    // Botón siguiente para ir a sets
    $(document).on('click', '#btn-siguiente-reservas', function() {
      console.log('Botón siguiente clickeado');
      if (reservaSeleccionada) {
        cargarSets(reservaSeleccionada.id);
        mostrarPaso('paso-sets');
      }
    });
    
    // Seleccionar set
    $(document).on('change', '.seleccionar-set', function() {
      console.log('Set seleccionado (change)');
      const setId = $(this).data('set-id');
      setSeleccionado = { id: setId };
      console.log('Set seleccionado ID:', setId);
      // Habilitar el botón siguiente
      $('#btn-siguiente-sets').prop('disabled', false);
      console.log('Botón siguiente sets habilitado');
    });
    
    // Botón siguiente para ir a asignación
    $(document).on('click', '#btn-siguiente-sets', function() {
      console.log('Botón siguiente sets clickeado');
      if (setSeleccionado) {
         // Limpiar participaciones asignadas al cambiar de set
         participacionesAsignadas = [];
         // Cargar participaciones del set seleccionado
         cargarParticipacionesExistentes();
        mostrarPaso('paso-asignacion');
      }
    });
    
    // Botón volver a reservas
    $(document).on('click', '#btn-volver-reservas', function() {
      mostrarPaso('paso-reservas');
    });
    
    // Botón volver a sets
    $(document).on('click', '#btn-volver-sets', function() {
      mostrarPaso('paso-sets');
    });

           // Variables para manejar las participaciones asignadas
      let participacionesAsignadas = [];
      let participacionesDisponibles = [];

      // Función para cargar participaciones asignadas existentes
      function cargarParticipacionesExistentes() {
        // Solo cargar si hay un set seleccionado
        if (!setSeleccionado) {
          participacionesAsignadas = [];
          actualizarResumenAsignacion();
          return;
        }

        $.ajax({
          url: '{{ route("sellers.get-assigned-participations") }}',
          method: 'POST',
          data: {
            seller_id: {{ $seller->id }},
            set_id: setSeleccionado.id,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success && response.participations) {
              // Convertir las participaciones existentes al formato que usa la aplicación
              participacionesAsignadas = response.participations.map(participation => ({
                id: participation.id,
                number: participation.number,
                participation_code: participation.participation_code,
                set_id: participation.set_id,
                assigned_at: participation.sale_date + 'T' + participation.sale_time
              }));
              
              // Actualizar el resumen con las participaciones existentes
              actualizarResumenAsignacion();
            }
          },
          error: function(xhr, status, error) {
            console.error('Error al cargar participaciones existentes:', error);
          }
        });
      }

      // Función para validar y obtener participaciones disponibles
     function validarParticipacionesDisponibles(desde, hasta, setId) {
       return new Promise((resolve, reject) => {
         $.ajax({
           url: '{{ route("sellers.validate-participations") }}',
           method: 'POST',
           data: {
             desde: desde,
             hasta: hasta,
             set_id: setId,
             seller_id: {{ $seller->id }},
             _token: '{{ csrf_token() }}'
           },
           success: function(response) {
             if (response.success) {
               participacionesDisponibles = response.participations || [];
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

     // Función para asignar participaciones por rango
     function asignarParticipacionesPorRango(desde, hasta) {
       if (!setSeleccionado) {
         alert('Debes seleccionar un set primero');
         return;
       }

       if (!desde || !hasta) {
         alert('Debes especificar el rango desde y hasta');
         return;
       }

       if (parseInt(desde) > parseInt(hasta)) {
         alert('El número "Desde" no puede ser mayor que "Hasta"');
         return;
       }

       // Mostrar loading
       $('#btn-asignar-participacion').prop('disabled', true).text('Validando...');

       validarParticipacionesDisponibles(desde, hasta, setSeleccionado.id)
         .then(response => {
           if (response.participations && response.participations.length > 0) {
             // Agregar las participaciones válidas al resumen
             response.participations.forEach(participation => {
               const participacionExistente = participacionesAsignadas.find(p => p.number === participation.number);
               if (!participacionExistente) {
                 participacionesAsignadas.push({
                   id: participation.id,
                   number: participation.number,
                   participation_code: participation.participation_code,
                   set_id: setSeleccionado.id,
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
     }

     // Función para asignar participación individual
     function asignarParticipacionIndividual(numero) {
       if (!setSeleccionado) {
         alert('Debes seleccionar un set primero');
         return;
       }

       if (!numero) {
         alert('Debes especificar el número de participación');
         return;
       }

       // Mostrar loading
       $('#btn-asignar-participacion').prop('disabled', true).text('Validando...');

       validarParticipacionesDisponibles(numero, numero, setSeleccionado.id)
         .then(response => {
           if (response.participations && response.participations.length > 0) {
             const participation = response.participations[0];
             const participacionExistente = participacionesAsignadas.find(p => p.number === participation.number);
             
             if (!participacionExistente) {
               participacionesAsignadas.push({
                 id: participation.id,
                 number: participation.number,
                 participation_code: participation.participation_code,
                 set_id: setSeleccionado.id,
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
     }

           // Función para actualizar el resumen de asignación
      function actualizarResumenAsignacion() {
        // Siempre mostrar el resumen, pero controlar qué contenido mostrar
        $('#resumen-asignacion').show();
        
        // Ocultar el estado vacío por defecto
        $('#estado-vacio-resumen').addClass('d-none');
        
        if (participacionesAsignadas.length === 0) {
          // Si no hay participaciones, mostrar estado vacío
          $('#estado-vacio-resumen').removeClass('d-none');
          $('#lista-participaciones-asignadas').hide();
        } else {
          // Si hay participaciones, mostrar lista
          $('#lista-participaciones-asignadas').show();
          
          // Actualizar contador
          $('#total-asignadas').text(participacionesAsignadas.length);

          // Generar grid de participaciones
          const gridHtml = participacionesAsignadas.map(participation => {
            const fecha = new Date(participation.assigned_at);
            const fechaStr = fecha.toLocaleDateString('es-ES');
            const horaStr = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            
            return `
              <div class="col-12 mb-2">
                <table class="table table-striped table-condensed table nowrap w-100 mb-0">
                  <thead>
                    <tr style="font-size: 10px;">
                      <th rowspan="2" style="border-color: transparent; width: 80px;">
                        <div style="background-color: #333; padding: 10px 5px; border-radius: 12px; text-align: center;">
                          <img src="{{url('assets/ticket.svg')}}" alt="" width="30px">
                        </div>
                      </th>
                      <th>Nº Participación</th>
                      <th>Estado</th>
                      <th>Vendedor</th>
                      <th>Fecha Venta</th>
                      <th>Hora Venta</th>
                      <th></th>
                    </tr>
                    <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                      <td>${participation.participation_code}</td>
                      <td><label class="badge bg-success">Asignada</label></td>
                      <td>{{ $seller->name ?? 'N/A' }}</td>
                      <td>${fechaStr}</td>
                      <td>${horaStr}h</td>
                      <td>
                        <div class="d-flex gap-2">
                          <button class="btn btn-sm btn-light" onclick="verDetalleParticipacion('${participation.participation_code}', ${participation.id})" title="Ver detalle">
                            <img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12">
                          </button>
                          <button class="btn btn-sm btn-danger" onclick="eliminarParticipacion('${participation.participation_code}')" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </thead>
                </table>
              </div>
            `;
          }).join('');

          $('#grid-participaciones').html(gridHtml);
          
          // Asegurar que el estado vacío esté oculto cuando hay elementos en el grid
          $('#estado-vacio-resumen').addClass('d-none');
        }
      }

           // Función para eliminar participación del resumen
      window.eliminarParticipacion = function(codigo) {
        // Encontrar la participación a eliminar
        const participacionAEliminar = participacionesAsignadas.find(p => p.participation_code === codigo);
        
        if (participacionAEliminar) {
          // Eliminar de la base de datos
          $.ajax({
            url: '{{ route("sellers.remove-assignment") }}',
            method: 'POST',
            data: {
              participation_id: participacionAEliminar.id,
              seller_id: {{ $seller->id }},
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                // Eliminar del array local
                participacionesAsignadas = participacionesAsignadas.filter(p => p.participation_code !== codigo);
                actualizarResumenAsignacion();
                mostrarMensaje('Participación eliminada correctamente', 'success');
              } else {
                mostrarMensaje(response.message || 'Error al eliminar la participación', 'error');
              }
            },
            error: function(xhr, status, error) {
              mostrarMensaje('Error de conexión al eliminar la participación', 'error');
            }
          });
        }
      };

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
        
        // Insertar alerta en la parte superior de la página, fuera del contenido de asignación
        $('.page-title-box').after(alertHtml);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
          $('.alert').fadeOut();
        }, 5000);
      }

     // Event listeners para asignación
     $(document).on('click', '#btn-asignar-participacion', function() {
       const desde = $('#rango-desde').val();
       const hasta = $('#rango-hasta').val();
       const unidad = $('#participacion-unidad').val();

       if (desde && hasta) {
         // Asignación por rango
         asignarParticipacionesPorRango(desde, hasta);
       } else if (unidad) {
         // Asignación individual
         asignarParticipacionIndividual(unidad);
       } else {
         alert('Debes especificar un rango o una participación individual');
       }
     });

           // Event listener para terminar asignación
      $(document).on('click', '#btn-terminar-asignacion', function() {
        if (participacionesAsignadas.length === 0) {
          alert('No hay participaciones para guardar');
          return;
        }

        // Mostrar loading
        $('#btn-terminar-asignacion').prop('disabled', true).text('Guardando...');

        // Guardar asignaciones en la base de datos
        $.ajax({
          url: '{{ route("sellers.save-assignments") }}',
          method: 'POST',
          data: {
            participations: participacionesAsignadas,
            seller_id: {{ $seller->id }},
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              mostrarMensaje('Asignaciones guardadas correctamente', 'success');
              
              // Limpiar el array temporal y recargar las participaciones existentes
              participacionesAsignadas = [];
              
              // Recargar las participaciones existentes desde la base de datos
              cargarParticipacionesExistentes();
              
              // Limpiar los campos de entrada
              $('#rango-desde').val('');
              $('#rango-hasta').val('');
              $('#participacion-unidad').val('');
              
              // Habilitar todos los campos
              $('#rango-desde, #rango-hasta, #participacion-unidad').prop('disabled', false);
              
            } else {
              mostrarMensaje(response.message || 'Error al guardar las asignaciones', 'error');
            }
          },
                     error: function(xhr, status, error) {
             mostrarMensaje('Error de conexión al guardar las asignaciones', 'error');
             $('#btn-terminar-asignacion').prop('disabled', false).text('Terminar');
           },
           complete: function() {
             $('#btn-terminar-asignacion').prop('disabled', false).text('Terminar');
           }
        });
      });

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

     // Variables para el tab de participaciones
     let tablaReservasParticipaciones = null;
     let reservaSeleccionadaParticipaciones = null;

     // Función para inicializar DataTable de reservas para participaciones
     function inicializarDataTableReservasParticipaciones() {
       if (tablaReservasParticipaciones) {
         return; // Ya está inicializada
       }
       
       tablaReservasParticipaciones = $('#tabla-reservas-participaciones').DataTable({
         "select": { style: "single" },
         "ordering": false,
         "sorting": false,
         "scrollX": true,
         "scrollCollapse": true,
         "language": {
           url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
         },
         "pageLength": 10,
         "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
         "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
         "columnDefs": [
           {
             "targets": -1, // Última columna (Seleccionar)
             "orderable": false,
             "searchable": false
           }
         ]
       });
     }

     // Función para cargar reservas en el tab de participaciones
     function cargarReservasParticipaciones() {
       if (!tablaReservasParticipaciones) {
         inicializarDataTableReservasParticipaciones();
       }
       
       tablaReservasParticipaciones.clear();
       
       let datosReservas = [];
       
       @if(isset($reserves) && $reserves->count() > 0)
         @foreach($reserves as $reserve)
           datosReservas.push([
             '#RS{{ str_pad($reserve->id, 4, '0', STR_PAD_LEFT) }}',
             '{{ $reserve->lottery ? $reserve->lottery->name : "Sin sorteo" }}',
             '{{ $reserve->lottery ? $reserve->lottery->draw_date->format('d-m-Y') : "Sin fecha" }}',
             '{{ $reserve->lottery ? $reserve->lottery->description : "N/A" }}',
             '{{ is_array($reserve->reservation_numbers) ? implode(' - ', $reserve->reservation_numbers) : ($reserve->reservation_numbers ?? 'Sin números') }}',
             '{{ number_format($reserve->reservation_amount ?? 0, 2) }}€',
             '{{ $reserve->reservation_tickets ?? 0 }}',
             '<b>{{ number_format($reserve->total_amount ?? 0, 2) }}€</b>',
             `<div class="form-check">
                <input class="form-check-input seleccionar-reserva-participaciones" type="radio" name="reserve_id_participaciones" value="{{ $reserve->id }}" id="reserve_participaciones_{{ $reserve->id }}" data-reserva-id="{{ $reserve->id }}">
                <label class="form-check-label" for="reserve_participaciones_{{ $reserve->id }}">
                  Seleccionar
                </label>
              </div>`
           ]);
         @endforeach
       @endif
       
       if (datosReservas.length > 0) {
         tablaReservasParticipaciones.rows.add(datosReservas).draw();
       } else {
         tablaReservasParticipaciones.rows.add([['No hay reservas disponibles para esta entidad', '', '', '', '', '', '', '', '']]).draw();
       }
     }

           // Función para calcular y mostrar tacos del vendedor
      window.cargarTacosVendedor = function(reservaId) {
        // Mostrar loading
        $('#contenedor-tacos').html('<div class="text-center"><i class="ri-loader-4-line fa-spin"></i> Cargando tacos...</div>');

        // Obtener los sets de la reserva
        $.ajax({
          url: '{{ route("sellers.get-sets-by-reserve") }}',
          method: 'POST',
          data: {
            reserve_id: reservaId,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.sets && response.sets.length > 0) {
              let html = '';
              let setsProcessed = 0;
              
              response.sets.forEach(set => {
                // Obtener las participaciones asignadas del vendedor para este set
                $.ajax({
                  url: '{{ route("sellers.get-assigned-participations") }}',
                  method: 'POST',
                  data: {
                    seller_id: {{ $seller->id }},
                    set_id: set.id,
                    _token: '{{ csrf_token() }}'
                  },
                  success: function(participationsResponse) {
                    setsProcessed++;
                    
                    if (participationsResponse.success && participationsResponse.participations.length > 0) {
                      // Calcular tacos basándose en las participaciones asignadas
                      const tacos = calcularTacos(participationsResponse.participations, set);
                      
                      if (tacos.length > 0) {
                        html += generarHTMLTacos(tacos, set);
                      }
                    }
                    
                    // Si todos los sets han sido procesados, mostrar el resultado
                    if (setsProcessed === response.sets.length) {
                      if (html) {
                        $('#contenedor-tacos').html(html);
                      } else {
                        $('#contenedor-tacos').html('<div class="alert alert-info">No hay participaciones asignadas en ningún set de esta reserva</div>');
                      }
                    }
                  },
                  error: function() {
                    setsProcessed++;
                    if (setsProcessed === response.sets.length) {
                      $('#contenedor-tacos').html('<div class="alert alert-danger">Error al cargar las participaciones</div>');
                    }
                  }
                });
              });
            } else {
              $('#contenedor-tacos').html('<div class="alert alert-info">No hay sets disponibles para esta reserva</div>');
            }
          },
          error: function() {
            $('#contenedor-tacos').html('<div class="alert alert-danger">Error al cargar los sets</div>');
          }
        });
      };

     // Función para calcular los tacos basándose en las participaciones
     function calcularTacos(participations, set) {
       const tacos = new Map();
       
       // Obtener el número de participaciones por taco desde el set
       // Por defecto 50 si no está definido
       const participationsPerBook = set.participations_per_book || 50;
       
       participations.forEach(participation => {
         const participationNumber = parseInt(participation.number);
         const bookNumber = Math.ceil(participationNumber / participationsPerBook);
         
         if (!tacos.has(bookNumber)) {
           tacos.set(bookNumber, {
             bookNumber: bookNumber,
             participations: [],
             totalParticipations: 0,
             salesRegistered: 0,
             returnedParticipations: 0,
             availableParticipations: 0
           });
         }
         
         const taco = tacos.get(bookNumber);
         taco.participations.push(participation);
         taco.totalParticipations++;
         
         // Aquí puedes agregar lógica para calcular ventas registradas, devoluciones, etc.
         if (participation.status === 'asignada') {
           taco.salesRegistered++;
         }
       });
       
       return Array.from(tacos.values());
     }

           // Función para generar HTML de los tacos
      function generarHTMLTacos(tacos, set) {
        let html = '';
        
        // Obtener el número de participaciones por taco desde el set
        const participationsPerBook = set.participations_per_book || 50;
        
        tacos.forEach(taco => {
          const startParticipation = (taco.bookNumber - 1) * participationsPerBook + 1;
          const endParticipation = Math.min(taco.bookNumber * participationsPerBook, set.total_participations || 1000);
          
          // Generar el formato de participation_code para el rango
          const startCode = `${set.set_number || 1}/${String(startParticipation).padStart(5, '0')}`;
          const endCode = `${set.set_number || 1}/${String(endParticipation).padStart(5, '0')}`;
          const participationsRange = `${startCode} - ${endCode}`;
          
          html += `
            <div class="form-card bs mb-2" style="margin: 5px;">
              <table class="table table-striped table-condensed table nowrap w-100 mb-0">
                <thead>
                  <tr style="font-size: 10px;">
                    
                    <th>Nº Taco</th>
                    <th>Participaciones</th>
                    <th>Nº Participaciones</th>
                    <th>Ventas Registradas</th>
                    <th>Participaciones Devueltas</th>
                    <th>Participaciones Disponibles</th>
                    <th></th>
                  </tr>
                  <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                    <td>${set.set_number || set.id}/${String(taco.bookNumber).padStart(4, '0')}</td>
                    <td>${taco.totalParticipations}</td>
                    <td>${participationsRange}</td>
                    <td>${taco.salesRegistered}</td>
                    <td>${taco.returnedParticipations}</td>
                    <td>${taco.availableParticipations}</td>
                    <td>
                      <a class="btn btn-sm btn-light show-details-taco" data-set-id="${set.id}" data-book-number="${taco.bookNumber}" data-participations='${JSON.stringify(taco.participations)}'><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                    </td>
                  </tr>
                </thead>
              </table>
              
                             <!-- Sección desplegable para mostrar participaciones -->
               <div class="part-information" style="height: 0px; overflow: hidden; transition: height 0.5s ease;">
                 <div style="height: 250px; overflow: auto;" id="list-participations-taco-${set.id}-${taco.bookNumber}" class="">
                   <table class="table table-striped table-condensed table nowrap w-100 mb-0">
                     <thead>
                       <tr style="font-size: 10px;">
                         <th rowspan="2" style="border-color: transparent; width: 80px;">
                           <div style="background-color: #333; padding: 10px 5px; border-radius: 12px; text-align: center; display:none">
                             <img src="{{url('assets/ticket.svg')}}" alt="" width="30px">
                           </div>
                         </th>
                         <th>Nº Participación</th>
                         <th>Estado</th>
                         <th>Vendedor</th>
                         <th>Fecha Venta</th>
                         <th>Hora Venta</th>
                         <th></th>
                       </tr>
                       <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                         <td></td>
                       </tr>
                     </thead>
                     <tbody id="participations-body-${set.id}-${taco.bookNumber}">
                       <!-- Las participaciones se cargarán dinámicamente aquí -->
                     </tbody>
                   </table>
                 </div>
               </div>
            </div>
          `;
        });
        
        return html;
      }

     // Event listeners para el tab de participaciones
     $(document).on('change', '.seleccionar-reserva-participaciones', function() {
       const reservaId = $(this).data('reserva-id');
       reservaSeleccionadaParticipaciones = { id: reservaId };
       $('#btn-siguiente-reservas-participaciones').prop('disabled', false);
     });

     $(document).on('click', '#btn-siguiente-reservas-participaciones', function() {
       if (reservaSeleccionadaParticipaciones) {
         cargarTacosVendedor(reservaSeleccionadaParticipaciones.id);
         $('#paso-reservas-participaciones').hide();
         $('#paso-tacos-participaciones').show();
       }
     });

     $(document).on('click', '#btn-volver-reservas-participaciones', function() {
       $('#paso-tacos-participaciones').hide();
       $('#paso-reservas-participaciones').show();
     });

           // Inicializar el tab de participaciones cuando se active
      $('[data-bs-target="#participaciones"]').click(function() {
        if (!tablaReservasParticipaciones) {
          cargarReservasParticipaciones();
        }
      });

      // Event listener para mostrar/ocultar participaciones de tacos
      $(document).on('click', '.show-details-taco', function(e) {
        e.preventDefault();
        
        const setId = $(this).data('set-id');
        const bookNumber = $(this).data('book-number');
        const participations = $(this).data('participations');
        const partInformation = $(this).closest('.form-card').find('.part-information');
        
        if (partInformation.css('height') == '0px') {
          // Abrir el desplegable
          partInformation.css('height', '250px');
          
          // Cargar las participaciones en la tabla
          const tbody = $(`#participations-body-${setId}-${bookNumber}`);
          tbody.empty();
          
          if (participations && participations.length > 0) {
            participations.forEach(participation => {
              const saleDate = participation.sale_date ? new Date(participation.sale_date).toLocaleDateString('es-ES') : 'N/A';
              const saleTime = participation.sale_time ? participation.sale_time : 'N/A';
              
              tbody.append(`
                <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                  <td style="width: 80px;">
                    <div style="background-color: #333; padding: 10px 5px; border-radius: 12px; text-align: center;">
                      <img src="{{url('assets/ticket.svg')}}" alt="" width="30px">
                    </div>
                  </td>
                  <td>${participation.participation_code}</td>
                  <td><label class="badge bg-success">Asignada</label></td>
                  <td>{{ $seller->name ?? 'N/A' }}</td>
                  <td>${saleDate}</td>
                  <td>${saleTime}</td>
                  <td>
                    <div class="d-flex gap-2">
                      <button class="btn btn-sm btn-light" onclick="verDetalleParticipacionTaco('${participation.participation_code}', ${participation.id})" title="Ver detalle">
                        <img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12">
                      </button>
                      <button class="btn btn-sm btn-danger" onclick="eliminarParticipacionTaco('${participation.participation_code}', ${participation.id})">
                        <i class="ri-delete-bin-line"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              `);
            });
                     } else {
             tbody.append(`
               <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                 <td colspan="7" class="text-center">No hay participaciones en este taco</td>
               </tr>
             `);
           }
        } else {
          // Cerrar el desplegable
          partInformation.css('height', '0px');
        }
      });

     

           // Función para ver detalle de participación desde el grid
      window.verDetalleParticipacion = function(codigo, participationId) {
        // Aquí puedes implementar la lógica para mostrar el detalle de la participación
        // Por ejemplo, abrir un modal con la información detallada
        alert(`Detalle de participación: ${codigo}\nID: ${participationId}`);
        // TODO: Implementar modal o vista detallada de la participación
      };

      // Función para ver detalle de participación desde el taco
      window.verDetalleParticipacionTaco = function(codigo, participationId) {
        // Aquí puedes implementar la lógica para mostrar el detalle de la participación
        // Por ejemplo, abrir un modal con la información detallada
        alert(`Detalle de participación: ${codigo}\nID: ${participationId}`);
        // TODO: Implementar modal o vista detallada de la participación
      };

      // Función para eliminar participación desde el taco
      window.eliminarParticipacionTaco = function(codigo, participationId) {
        if (confirm('¿Estás seguro de que quieres eliminar esta participación?')) {
          // Eliminar de la base de datos
          $.ajax({
            url: '{{ route("sellers.remove-assignment") }}',
            method: 'POST',
            data: {
              participation_id: participationId,
              seller_id: {{ $seller->id }},
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                // Mostrar mensaje de éxito
                mostrarMensaje('Participación eliminada correctamente', 'success');
                
                // Recargar los tacos para actualizar la información
                if (reservaSeleccionadaParticipaciones) {
                  cargarTacosVendedor(reservaSeleccionadaParticipaciones.id);
                }
              } else {
                mostrarMensaje(response.message || 'Error al eliminar la participación', 'error');
              }
            },
            error: function(xhr, status, error) {
              mostrarMensaje('Error de conexión al eliminar la participación', 'error');
            }
          });
        }
      };
  });

</script>

@endsection