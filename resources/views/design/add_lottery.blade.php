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

                    	Selección Entidad

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

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img width="26px" src="{{url('icons_/selec_sorteo.svg')}}" alt="">

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

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					4
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Diseño Particip.
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<a href="{{url('design/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form action="{{ route('design.storeLottery') }}" method="POST">
                    				@csrf
                    				<h4 class="mb-0 mt-1">
                    					Sorteo en el que generar el diseño
                    				</h4>
                    				<small><i>Selecciona el sorteo</i></small>

                    				<br>
                    				<br>

                    				<div style="min-height: 656px;">
                    					<table id="example2" class="table table-striped nowrap w-100">
                    						<thead class="">
                    							<tr>
                    								<th>ID</th>
                    								<th>Nombre Sorteo</th>
                    								<th>Fecha</th>
                    								<th>Estado</th>
                    								<th class="d-none">Seleccionar</th>
                    							</tr>
                    						</thead>
                    						<tbody>
                    							@if($lotteries->count() > 0)
                    								@foreach($lotteries as $lottery)
                    								<tr class="selectable-row" style="cursor: pointer;">
                    									<td>#LO{{str_pad($lottery->id, 4, '0', STR_PAD_LEFT)}}</td>
                    									<td>{{$lottery->name}}</td>
                    									<td>{{$lottery->draw_date->format('d-m-Y') ?? 'Sin fecha'}}</td>
                    									<td><label class="badge bg-success">Activo</label></td>
                    									<td class="d-none">
                    										<div class="form-check">
                    											<input class="form-check-input" type="radio" name="lottery_id" value="{{$lottery->id}}" id="lottery_{{$lottery->id}}" required>
                    											<label class="form-check-label" for="lottery_{{$lottery->id}}">
                    												Seleccionar
                    											</label>
                    										</div>
                    									</td>
                    								</tr>
                    								@endforeach
                    							@else
                    								<tr>
                    									<td colspan="5" class="text-center">
                    										<div class="alert alert-info">
                    											<i class="ri-information-line me-2"></i>
                    											No hay sorteos disponibles con sets asociados para esta entidad.
                    											<br>
                    											<small>Primero debe crear sets para los sorteos antes de poder diseñar participaciones.</small>
                    										</div>
                    									</td>
                    								</tr>
                    							@endif
                    						</tbody>
                    					</table>
                    				</div>

                    				<input type="hidden" name="entity_id" value="{{ request('entity_id', isset($entity) ? $entity->id : null) }}">

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
        fixedHeader: true
  });
  }

$(document).ready(function() {
    initDatatable();
    
    // Hacer las filas clickeables para seleccionar el radio button
    $(document).on('click', '#example2 tbody tr.selectable-row', function(e) {
      // No activar si se hace clic directamente en el radio button o su label
      if ($(e.target).is('input[type="radio"]') || $(e.target).is('label') || $(e.target).closest('label').length) {
        return;
      }
      
      // Seleccionar el radio button de la fila
      $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });
    
    // Agregar efecto hover visual
    $(document).on('mouseenter', '#example2 tbody tr.selectable-row', function() {
      $(this).css('background-color', '#f8f9fa');
    }).on('mouseleave', '#example2 tbody tr.selectable-row', function() {
      if (!$(this).find('input[type="radio"]').is(':checked')) {
        $(this).css('background-color', '');
      }
    });
    
    // Mantener el color cuando está seleccionado
    $(document).on('change', '#example2 tbody tr.selectable-row input[type="radio"]', function() {
      $('#example2 tbody tr.selectable-row').css('background-color', '');
      if ($(this).is(':checked')) {
        $(this).closest('tr').css('background-color', '#e3f2fd');
      }
    });
});

</script>

@endsection