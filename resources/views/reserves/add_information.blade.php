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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Reservas</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Reservas</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Datos de la Reserva

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					1
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Selec. Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('icons/sorteos.svg')}}" alt="">

                    				<label>
                    					Selec. Sorteo
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					3
                    				</span>

                    				<img src="{{url('icons/reservas.svg')}}" alt="">

                    				<label>
                    					Datos Reserva
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

                						<h3 class="mt-2 mb-0">Fademur</h3>

                						<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> La Rioja
                						
                					</div>
                				</div>

                    		</div>

                    		<a href="{{url('reserves/add/lottery')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Datos del Sorteo
                    			</h4>
                    			<small><i>Comprueba que el sorteo es el correcto</i></small>

                    			<br>

                    			<div class="row show-content">
                                    
                                    <div class="col-3 offset-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Número del Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" readonly type="text" value="46/25" placeholder="46/25" style="border-radius: 0 30px 30px 0;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-7">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Nombre del Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" readonly type="text" value="Sorteo Extraordinario Asociación Española Contra El Cáncer" placeholder="Nombre del Sorteo" style="border-radius: 0 30px 30px 0;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row show-content">
                                            
                                    
                                    <div class="col-4 offset-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Tipo de Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                </div>

                                                <select disabled class="form-control" name="" id="" style="border-radius: 0 30px 30px 0;">
                                                	<option value="" disabled>Sorteo de Ejemplo</option>
                                                	<option value="sabado" selected>Sorteo Extraordinario de 15€</option>
                                                	<option value="sabado">Sorteo Sábado</option>
                                                	<option value="jueves">Sorteo Jueves</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Precio décimo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" readonly type="number" value="15.00" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Fecha Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" readonly type="date" value="2025-06-07" placeholder="15/01/2025" style="border-radius: 0 30px 30px 0;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                    			<h4 class="mb-0 mt-1">
                    				Configuración de la Reserva
                    			</h4>
                    			<small><i>Introduce los datos de la reserva</i></small>

                    			<br>
                    			<br>

                    			<div style="min-height: 256px;">

                    				<div class="row">
                    					<div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Número</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <input class="form-control" type="number" placeholder="" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>
                    				</div>

                    				<div class="row">
                    					<div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Importe a Reservar</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <input class="form-control" type="number" step="0.01" placeholder="" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Cantidad de décimos</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <input class="form-control" type="number" placeholder="" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>
                    				</div>
	                    			

		                        </div>


                    			<div class="row">

                    				<div class="col-12 text-end">
                    					<a href="{{url('reserves?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
	                    					<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></a>
                    				</div>

                    			</div>

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