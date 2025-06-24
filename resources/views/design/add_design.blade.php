@extends('layouts.layout')

@section('title','Diseño e Impresión')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e Impresión</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Selección

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

                    				<img width="26px" src="{{url('icons/selec_sorteo.svg')}}" alt="">

                    				<label>
                    					Selec. Sorteo
                    				</label>

                    			</div>

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					3
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Selec. Set
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					4
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Diseño Particip.
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<a href="{{url('design/add/set')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Set seleccionado
                    			</h4>
                    			<small><i>Comprueba que todos los datos del set sean los correctos</i></small>

                    			<br>
                    			<br>

                    			<div style="min-height: 656px;">

                    				<table class="table" style="font-size: 10px;">
                    					<thead>
                    						<tr>
                    							<th>Nombre Set</th>
                    							<th>N.Sorteo</th>
                    							<th>Número/s</th>
                    							<th>Importe Jugado <br> (Número)</th>
                    							<th>Importe por Participación</th>
                    							<th>Importe Donativo</th>
				                                <th>Participaciones <br> TOTAL</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    						<tr>
                    							<td>Set de Pruebas</td>
                    							<td>46/25</td>
                    							<td>05716 - 52468 <br> 51235 - 69584</td>
                    							<td>2,00€</td>
				                                <td>2,00€</td>
				                                <td>10,00€</td>
				                                <td>750</td>
                    						</tr>
                    					</tbody>
                    				</table>

                    				<br>

                    				<table class="table" style="font-size: 10px;">
                    					<thead>
                    						<tr>
                    							<th>Participaciones <br> Fisicas</th>
                    							<th>Participaciones <br> Digitales</th>
                    							<th>Importe <br> Lotería TOTAL</th>
                    							<th>Importe <br> Donativo TOTAL</th>
                    							<th>Lotería + <br> Donativo TOTAL</th>
                    						</tr>
                    					</thead>
                    					<tbody>
                    						<tr>
				                                <td>600</td>
				                                <td>150</td>
				                                <td>6.000,00€</td>
				                                <td>1.500,00€</td>
				                                <td>7.500,00€</td>
                    						</tr>
                    					</tbody>
                    				</table>

                    				<h4 class="mb-0 mt-1">
	                    				Seleccione una opción
	                    			</h4>
	                    			<small><i>Selecciona la opción que mas se adapte </i></small>

	                    			<br>
	                    			<br>

	                    			<div class="text-center" id="manager-buttons">

            							<button class="btn btn-light btn-xl text-center m-2 bs" id="select-template" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px;">
            								<br>
            								<br>
            								<br>
            								<h4 class="mb-0">Diseño <br> Plantillas</h4>
            							</button>

            							<a href="{{url('design/add/format')}}" class="btn btn-light btn-xl text-center m-2 bs" id="free-design" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px;">
            								<br>
            								<br>
            								<br>
            								<h4 class="mb-0">Diseño <br> Libre</h4>
            							</a>

            							<button class="btn btn-light btn-xl text-center m-2 bs" id="extern-design" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px;">
            								<br>
            								<br>
            								<br>
            								<h4 class="mb-0">Diseño <br> Externo</h4>
            							</button>

        							</div>

		                        </div>


                    			<div class="row">

                    				{{-- <div class="col-12 text-end">
                    					<a href="{{url('design/add/design')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Siguiente
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></a>
                    				</div> --}}

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