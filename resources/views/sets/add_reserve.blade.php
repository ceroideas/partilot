@extends('layouts.layout')

@section('title','Set Participaciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Set Participaciones</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Set Participaciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Selección Reserva

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					1
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="" width="26px">

                    				<label>
                    					Selec. Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('icons/reservas.svg')}}" alt="" width="18px" style="margin: 0 12px;">

                    				<label>
                    					Selec. Reserva
                    				</label>

                    			</div>

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					3
                    				</span>

                    				<img src="{{url('icons/sets.svg')}}" alt="" width="26px">

                    				<label>
                    					Config. Set
                    				</label>

                    			</div>
                    			
                    		</div>

                            <div class="form-card">
                                
                                <div class="row">
                                    <div class="col-4">
                                        
                                        <div class="photo-preview-3">
                                            
                                            <i class="ri-account-circle-fill"></i>

                                        </div>
                                        
                                        <div style="clear: both;"></div>
                                    </div>

                                    <div class="col-8 text-center mt-2">

                                        <h3 class="mt-2 mb-0">{{session('selected_entity')->name ?? 'Entidad'}}</h3>

                                        <i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{session('selected_entity')->province ?? 'Sin provincia'}}
                                        
                                    </div>
                                </div>

                            </div>

                    		<a href="{{url('sets/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form action="{{url('sets/store-reserve')}}" method="POST">
                    				@csrf
                    			<h4 class="mb-0 mt-1">
                    				Reservas disponibles en la entidad
                    			</h4>
                    			<small><i>Selecciona la reserva para crear el set</i></small>

                    			<br>
                    			<br>

                    			<div style="min-height: 656px;">

	                    			<table id="example2" class="table table-striped nowrap w-100">
			                            <thead class="">
			                                <tr>
			                                    <th>ID Reserva</th>
			                                    <th>Sorteo</th>
			                                    <th>Fecha Sorteo</th>
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
			                                    <td>{{$reserve->lottery ? $reserve->lottery->draw_date : 'Sin fecha'}}</td>
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
			                                <tr>
			                                    <td colspan="7" class="text-center">No hay reservas confirmadas disponibles para esta entidad</td>
			                                </tr>
			                                @endforelse
			                            </tbody>
			                        </table>

		                        </div>


                    			<div class="row">

                    				<div class="col-12 text-end">
                    					<button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Siguiente
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></button>
                    				</div>

                    			</div>
                    			</form>

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

  initDatatable();

  setTimeout(()=>{
    $('.filters .inline-fields:first').trigger('keyup');
  },100);

</script>

@endsection