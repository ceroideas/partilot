@extends('layouts.layout')

@section('title','Entidades')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Entidades</li>
                    </ol>
                </div>
                <h4 class="page-title">Entidades</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    @if($entities->count() > 0)
                        <h4 class="header-title">

                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
                                <input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
                                <input type="text" class="form-control" placeholder="Status">
                            </div>

                            <a href="{{url('entities/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <table id="example2" class="table table-striped nowrap w-100">
                            <thead class="filters">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Entidad</th>
                                    <th>Provincia</th>
                                    <th>Localidad</th>
                                    <th>Gestor</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Administración</th>
                                    <th>Status</th>
                                    <th class="no-filter"></th>
                                </tr>
                            </thead>
                        
                        
                            <tbody>
                                @foreach($entities as $entity)
                                <tr>
                                    <td><a href="{{url('entities/view', $entity->id)}}">#EN{{str_pad($entity->id, 4, '0', STR_PAD_LEFT)}}</a></td>
                                    <td>{{$entity->name ?? 'Sin nombre'}}</td>
                                    <td>{{$entity->province ?? 'Sin provincia'}}</td>
                                    <td>{{$entity->city ?? 'Sin localidad'}}</td>
                                    <td>{{$entity->manager ? $entity->manager->user->name . ' ' . $entity->manager->user->last_name : 'Sin gestor'}}</td>
                                    <td>{{$entity->phone ?? 'Sin teléfono'}}</td>
                                    <td>{{$entity->email ?? 'Sin email'}}</td>
                                    <td>{{$entity->administration ? $entity->administration->name : 'Sin administración'}}</td>
                                    <td>
                                        @php
                                            $statusValue = $entity->status;
                                            if ($statusValue === null || $statusValue === -1) {
                                                $statusText = 'Pendiente';
                                                $statusClass = 'bg-secondary';
                                            } elseif ($statusValue == 1) {
                                                $statusText = 'Activo';
                                                $statusClass = 'bg-success';
                                            } else {
                                                $statusText = 'Inactivo';
                                                $statusClass = 'bg-danger';
                                            }
                                        @endphp
                                        <label class="badge {{ $statusClass }}">{{ $statusText }}</label>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-light"><img src="{{url('icons_/persons.svg')}}" alt="" width="12"></a>
                                        <a class="btn btn-sm btn-light"><img src="{{url('icons_/design.svg')}}" alt="" width="12"></a>
                                        <a class="btn btn-sm btn-light"><img src="{{url('icons_/participations.svg')}}" alt="" width="12"></a>
                                        <a class="btn btn-sm btn-light"><img src="{{url('icons_/returns.svg')}}" alt="" width="12"></a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{$entity->id}}" data-name="{{$entity->name}}"><i class="ri-delete-bin-6-line"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="d-flex align-items-center gap-1">
                            
                            <div class="empty-tables">

                                <div>
                                    <img src="{{url('icons_/entidades.svg')}}" alt="" width="80px">
                                </div>

                                <h3 class="mb-0">No hay Entidades</h3>

                                <small>Añade Entidades</small>

                                <br>

                                <a href="{{url('entities/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
                            </div>

                        </div>
                    @endif
                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>
    
  function initDatatable() 
  {
    $("#example2").DataTable({

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
                            //     console.log(val)
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

  initDatatable();

  setTimeout(()=>{
    $('.filters .inline-fields:first').trigger('keyup');
  },100);

  // Eliminar entidad
  $('.delete-btn').on('click', function() {
    const id = $(this).data('id');
    const name = $(this).data('name');
    
    if (confirm('¿Estás seguro de que quieres eliminar la entidad "' + name + '"?')) {
      window.location.href = '{{url("entities/delete")}}/' + id;
    }
  });

</script>

@endsection