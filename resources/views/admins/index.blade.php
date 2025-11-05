@extends('layouts.layout')

@section('title','Administraciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">Administraciones</li>
                    </ol>
                </div>
                <h4 class="page-title">Administraciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    @php
                        $administrations = App\Models\Administration::orderBy('created_at', 'desc')->get();
                    @endphp

                	<div class="{{count($administrations) ? '' : 'd-none'}}">
	                    <h4 class="header-title">

	                    	<div class="float-start d-flex align-items-start">
	                    		<input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Provincia">
	                    		<input type="text" class="form-control" style="margin-right: 8px ;" placeholder="Localidad">
	                    		<input type="text" class="form-control" placeholder="Status">
	                    	</div>

	                    	<a href="{{url('administrations/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>

	                    </h4>

	                    <div style="clear: both;"></div>

	                    <br>

	                    <table id="example2" class="table table-striped nowrap w-100 selectable-rows">
	                        <thead class="filters">
	                            <tr>
	                                <th>Order ID</th>
	                                <th>Administración</th>
	                                <th>Nº Receptor</th>
	                                <th>Provincia</th>
	                                <th>Localidad</th>
	                                <th>Gestor</th>
	                                <th>Teléfono</th>
	                                <th>Email</th>
	                                <th>Status</th>
	                                <th class="no-filter"></th>
	                            </tr>
	                        </thead>
	                    
	                    
	                        <tbody>
                                @foreach ($administrations as $admins)
	                            <tr>
	                                <td><a href="{{url('administrations/view',$admins->id)}}">#AD{{ str_pad($admins->id, 5,0, STR_PAD_LEFT) }}</a></td>
	                                <td>{{$admins->name}}</td>
	                                <td>{{$admins->receiving}}</td>
	                                <td>{{$admins->province}}</td>
	                                <td>{{$admins->city}}</td>
	                                <td>{{$admins->manager->user->name.' '.$admins->manager->user->last_name}}</td>
	                                <td>{{$admins->phone}}</td>
	                                <td>{{$admins->email}}</td>
	                                <td>
                                        {!!$admins->status ? '<label class="badge bg-success">Activo</label>' : '<label class="badge bg-danger">Inactivo</label>' !!}
                                        
                                    </td>
	                                <td>
	                                	<a class="btn btn-sm btn-light"><img src="{{url('icons_/entidades.svg')}}" alt="" width="12"></a>
	                                	<a class="btn btn-sm btn-light"><img src="{{url('icons_/sorteos.svg')}}" alt="" width="12"></a>
	                                	<a class="btn btn-sm btn-light"><img src="{{url('icons_/reservas.svg')}}" alt="" width="12"></a>
	                                	<a class="btn btn-sm btn-light"><img src="{{url('icons_/participaciones.svg')}}" alt="" width="12"></a>
	                                	<a class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></a>
	                                </td>
	                            </tr>
                                @endforeach
	                        </tbody>
	                    </table>

                    </div>

                    <div class="{{count($administrations) ? 'd-none' : ''}}">
                        
                        <div class="d-flex align-items-center gap-1">
                        	
                        	<div class="empty-tables">

                        		<div>
                        			<img src="{{url('icons_/administraciones.svg')}}" alt="" width="80px">
                        		</div>

                        		<h3 class="mb-0">No hay Administraciones</h3>

                        		<small>Añade Administraciones</small>

                        		<br>

                        		<a href="{{url('administrations/add')}}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
                        	</div>

                        </div>
                    </div>
                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>
	// Limpiar datos de formulario de administración al entrar al index
	localStorage.removeItem('administration_form_data');
	
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

</script>

@endsection