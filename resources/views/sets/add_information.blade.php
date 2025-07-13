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

                    	Configurar Set

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

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('icons/reservas.svg')}}" alt="" width="18px" style="margin: 0 12px;">

                    				<label>
                    					Selec. Reserva
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
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

                						<h3 class="mt-2 mb-0">{{$entity->name ?? 'Entidad'}}</h3>

                						<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{$entity->province ?? 'Sin provincia'}}
                						
                					</div>
                				</div>

                    		</div>

                    		<a href="{{url('sets/add/reserve')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs">
                    			<form action="{{url('sets/store-information')}}" method="POST">
                    				@csrf
                    			<div style="min-height: 658px;">
                    				
	                    			<h4 class="mb-0 mt-1">
	                    				Reserva en la que generar el Set
	                    			</h4>
	                    			<small><i>Revisa que los datos de la reserva sean los correctos</i></small>

	                    			<br>

	                    			<div class="row show-content">
	                                    
	                                    <div class="col-3 offset-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Número del Sorteo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" readonly type="text" value="{{$reserve->lottery ? $reserve->lottery->lottery_number : 'Sin número'}}" placeholder="46/25" style="border-radius: 0 30px 30px 0;">
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

	                                                <input class="form-control" readonly type="text" value="{{$reserve->lottery ? $reserve->lottery->description : 'Sin nombre'}}" placeholder="Nombre del Sorteo" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>

	                                <div class="row show-content">
	                                            
	                                    
	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Fecha Sorteo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" readonly type="text" value="{{$reserve->lottery ? $reserve->lottery->draw_date->format('d-m-Y') : 'Sin fecha'}}" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-5">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Números</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" readonly type="text" value="{{implode(' - ', $reserve->reservation_numbers ?? [])}}" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Décimos TOTALES</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <input class="form-control" readonly type="number" value="{{$reserve->reservation_tickets}}" style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Importe TOTAL</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" readonly type="number" step="0.01" value="{{$reserve->reservation_amount}}" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>

	                    			<h4 class="mb-0 mt-1">
	                    				Configuración del Set
	                    			</h4>
	                    			<small><i>Todos los campos son obligatorios</i></small>

	                    			<br>

	                    			<div class="row">
	                    				<div class="col-6">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Nombre del Set</label>

	                                            <div class="input-group input-group-merge group-form">

	                                            	<div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/19.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="set_name" type="text" placeholder="Set de ejemplo" style="border-radius: 0 30px 30px 0;" required>
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Importe Jugado (Número)</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="played_amount" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Importe Donativo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="donation_amount" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Importe Total Participación</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="total_participation_amount" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Participaciones Totales</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/20.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="total_participations" type="number" placeholder="0" style="border-radius: 0 30px 30px 0;" required>
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Importe TOTAL</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="total_amount" type="number" step="0.01" placeholder="6.00€" style="border-radius: 0 30px 30px 0;" required>
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Fecha Límite</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="deadline_date" type="date" value="2025/07/06" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>
	                    			</div>

	                    			<h4 class="mb-0 mt-1">
	                    				Tipo Participaciones
	                    			</h4>
	                    			<small><i>Elige la cantidad de participaciones fisicas o digitales a realizar</i></small>

	                    			<br>

	                    			<div class="row">
	                    				<div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Participaciones Físicas</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/20.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="physical_participations" type="number" placeholder="600" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-3">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Participaciones Digitales</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/2.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" name="digital_participations" type="number" placeholder="150" style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>
	                    			</div>
                    			</div>

                    			<div class="row">

                    				<div class="col-12 text-end">
                    					<button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Guardar
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
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