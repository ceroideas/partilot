@extends('layouts.layout')

@section('title','Gestión Grupos')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Vendedores</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Gestión Grupos</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Gestión Grupos</h4>
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

                    			<div class="form-wizard-element active">
                    				
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

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Dat. Grupo
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<a href="{{url('groups?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form action="{{ route('groups.storeEntity') }}" method="POST">
                    				@csrf
                    				<h4 class="mb-0 mt-1">
                    					Entidad para el grupo
                    				</h4>
                    				<small><i>Selecciona la entidad</i></small>

                    				<br>
                    				<br>

                    				<div style="min-height: 656px;">
                    					<table id="example2" class="table table-striped nowrap w-100">
                    						<thead class="">
                    							<tr>
                    								<th>ID</th>
                    								<th>Nombre Entidad</th>
                    								<th>Provincia</th>
                    								<th>Localidad</th>
                    								<th>Administración</th>
                    								<th>Estado</th>
                    								<th>Seleccionar</th>
                    							</tr>
                    						</thead>
                    						<tbody>
                    							@foreach($entities as $entity)
                    							<tr>
                    								<td>#EN{{str_pad($entity->id, 4, '0', STR_PAD_LEFT)}}</td>
                    								<td>{{$entity->name}}</td>
                    								<td>{{$entity->province ?? 'Sin provincia'}}</td>
                    								<td>{{$entity->city ?? 'Sin localidad'}}</td>
                    								<td>{{$entity->administration ? $entity->administration->name : 'Sin administración'}}</td>
                    								<td><label class="badge bg-success">Activo</label></td>
                    								<td>
                    									<div class="form-check">
                    										<input class="form-check-input" type="radio" name="entity_id" value="{{$entity->id}}" id="entity_{{$entity->id}}" required>
                    										<label class="form-check-label" for="entity_{{$entity->id}}">
                    											Seleccionar
                    										</label>
                    									</div>
                    								</td>
                    							</tr>
                    							@endforeach
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
        fixedHeader: true
  });
  }

  $(document).ready(function() {
    initDatatable();
});

</script>

@endsection

