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

                            <a href="{{ route('sellers.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>

                        <div class="col-md-9">

                            <div class="tabbable show-content">
                                
                                <div class="tab-content p-0">
                                    
                                    <div class="tab-pane fade active show" id="datos_vendedor">


                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
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

                                    <div class="tab-pane fade" id="participaciones">
                                        <div class="form-card bs" id="participations_select" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Reserva en la que Asignar participaciones
                                                    </h4>
                                                    <small><i>Selecciona una Reserva</i></small>
                                                </div>
                                            </div>

                                            <br>
                                            <br>

                                            <div style="min-height: 656px;">

                                                <table id="example2" class="table table-striped nowrap w-100">
                                                    <thead class="">
                                                        <tr>
                                                            <th>ID Reserva</th>
                                                            <th>Sorteo</th>
                                                            <th>Fecha Sorteo</th>
                                                            <th>Nombre Sorteo</th>
                                                            <th>Números</th>
                                                            <th>Importe Total</th>
                                                            <th>Estado</th>
                                                            <th>Seleccionar</th>
                                                        </tr>
                                                    </thead>
                                                
                                                    <tbody>
                                                        @forelse($reserves as $reserve)
                                                        <tr>
                                                            <td>#RS{{str_pad($reserve->id, 4, '0', STR_PAD_LEFT)}}</td>
                                                            <td>{{$reserve->lottery ? $reserve->lottery->name : 'Sin sorteo'}}</td>
                                                            <td>{{$reserve->lottery ? $reserve->lottery->draw_date->format('d-m-Y') : 'Sin fecha'}}</td>
                                                            <td>{{$reserve->lottery ? $reserve->lottery->description : 'N/A'}}</td>
                                                            <td>{{implode(' - ', $reserve->reservation_numbers ?? [])}}</td>
                                                            <td>{{number_format($reserve->total_amount, 2)}} €</td>
                                                            <td><label class="badge bg-success">{{ucfirst($reserve->status ? 'Activo' : '')}}</label></td>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="reserve_id" value="{{$reserve->id}}" id="reserve_{{$reserve->id}}" required>
                                                                    <label class="form-check-label" for="reserve_{{$reserve->id}}">
                                                                        Seleccionar
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        {{-- <tr>
                                                            <td colspan="7" class="text-center">No hay reservas confirmadas disponibles para esta entidad</td>
                                                        </tr> --}}
                                                        @endforelse
                                                    </tbody>
                                                </table>

                                            </div>


                                            <div class="row">

                                                <div class="col-12 text-end">
                                                    <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" id="load_participations">Siguiente
                                                        <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></button>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-card bs d-none" id="participations_load" style="min-height: 658px;">

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Asignación
                                                    </h4>
                                                    <small><i>Resumen asignación total</i></small>
                                                </div>
                                            </div>

                                            <br>
                                            <br>

                                            <div style="height: 656px; overflow: auto;" id="list-participations" class="">

                                                @for ($i = 0; $i < 4; $i++)
                                                <div class="form-card bs mb-2" style="margin: 5px;">
                                                
                                                    <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                        <thead>
                                                            <tr style="font-size: 10px;">
                                                                <th rowspan="2" style="border-color: transparent;">
                                                                    <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                        <img src="{{url('assets/rectangulo.svg')}}" alt="" width="50px">
                                                                    </div>
                                                                </th>
                                                                <th>Nº Taco</th>
                                                                <th>Participaciones</th>
                                                                <th>Nº Participaciones</th>
                                                                <th>Ventas Registradas</th>
                                                                <th>Participaciones Devueltas</th>
                                                                <th>Participaciones Disponibles</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr style="font-size: 12px; font-weight: bolder; border-color: transparent;">
                                                                <td>1/0001</td>
                                                                <td>50</td>
                                                                <td>1/00001 - 1/00050</td>
                                                                <td>10</td>
                                                                <td>5</td>
                                                                <td>35</td>
                                                                <td>
                                                                    <a class="btn btn-sm btn-light show-details"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        
                                                    </table>

                                                    <div style="height: 0px; overflow-y: auto; overflow-x: hidden;" class="part-information">

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                                    <thead>
                                                                        <tr style="font-size: 10px;">
                                                                            <th rowspan="2" style="border-color: transparent;">
                                                                                <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                                    <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
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
                                                                            <td>1/0001</td>
                                                                            <td><label class="badge bg-success">Vendida</label></td>
                                                                            <td>Jorge Ruiz Ortega</td>
                                                                            <td>20/10/2025</td>
                                                                            <td>21:00h</td>
                                                                            <td>
                                                                                <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                            </td>
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                </table>
                                                                <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                                    <thead>
                                                                        <tr style="font-size: 10px;">
                                                                            <th rowspan="2" style="border-color: transparent;">
                                                                                <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                                    <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
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
                                                                            <td>1/0001</td>
                                                                            <td><label class="badge bg-success">Vendida</label></td>
                                                                            <td>Jorge Ruiz Ortega</td>
                                                                            <td>20/10/2025</td>
                                                                            <td>21:00h</td>
                                                                            <td>
                                                                                <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                            </td>
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                </table>
                                                                <table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

                                                                    <thead>
                                                                        <tr style="font-size: 10px;">
                                                                            <th rowspan="2" style="border-color: transparent;">
                                                                                <div style="background-color: #333; padding: 20px 10px; border-radius: 12px; text-align: center;">
                                                                                    <img src="{{url('assets/ticket.svg')}}" alt="" width="50px">
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
                                                                            <td>1/0001</td>
                                                                            <td><label class="badge bg-success">Vendida</label></td>
                                                                            <td>Jorge Ruiz Ortega</td>
                                                                            <td>20/10/2025</td>
                                                                            <td>21:00h</td>
                                                                            <td>
                                                                                <a href="{{url('participations/view',1)}}" class="btn btn-sm btn-light"><img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12"></a>
                                                                            </td>
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endfor
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="asignacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Asigna las participaciones
                                                    </h4>
                                                    <small><i>Individual o por rango</i></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="liquidacion">
                                        <div class="form-card bs" style="min-height: 658px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h4 class="mb-0 mt-1">
                                                        Asignación
                                                    </h4>
                                                    <small><i>Resumen Asignación total</i></small>
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

  $('[data-bs-target="#participaciones"]').click(function (e) {
    if (!init1) {
      initDatatable();
      init1 =true;
    }
  });

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

  $('#load_participations').click(function (e) {
      e.preventDefault();
      $('#participations_load').removeClass('d-none');
      $('#participations_select').addClass('d-none');
  });

</script>

@endsection