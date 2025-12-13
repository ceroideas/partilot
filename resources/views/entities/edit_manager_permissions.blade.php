@extends('layouts.layout')

@section('title','Editar Permisos Gestor')

@section('content')

<style>
	
	.form-wizard-element, .form-wizard-element label {
		cursor: pointer;
	}
	.form-check-input:checked {
		border-color: #333;
	}
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('entities.index') }}">Entidades</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('entities.show', $entity->id) }}">Entidad</a></li>
                        <li class="breadcrumb-item active">Editar Permisos</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Permisos Gestor</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Permisos Gestor

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
                    					Datos Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('assets/gestor.svg')}}" alt="">

                    				<label>
                    					Permisos Gestor
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<div class="form-card bs">
                    			
                    			<div class="row">
                					<div class="col-4">
                						
	                    				<div class="photo-preview-3">
	                    					@if($manager->user->image)
	                    						<img src="{{ url('manager/' . $manager->user->image) }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
	                    					@else
	                    						<i class="ri-account-circle-fill"></i>
	                    					@endif
	                    				</div>
	                    				
	                    				<div style="clear: both;"></div>
                					</div>

                					<div class="col-8 text-center mt-2">

                						<h4 class="mt-0 mb-0">{{ $manager->user->name ?? '' }} {{ $manager->user->last_name ?? '' }}</h4>

                						<small>{{ $manager->user->email ?? '' }}</small> <br>
                						
                					</div>
                				</div>

                    		</div>

                    		<a href="{{ route('entities.show', $entity->id) }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form action="{{ route('entities.update-manager-permissions', ['entity_id' => $entity->id, 'manager_id' => $manager->id]) }}" method="POST">
                    				@csrf
                    				@method('PUT')
	                    			<h4 class="mb-0 mt-1">
	                    				Permisos Gestor
	                    			</h4>
	                    			<small><i>Puedes modificar los permisos en cualquier momento</i></small>

	                    			<br>

	                    			<div class="row">
	                    				
						    			<div class="text-start col-6">

							    			<div class="form-check form-switch mt-2 mb-2">
												<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="all" {{ $manager->permission_sellers && $manager->permission_design && $manager->permission_statistics && $manager->permission_payments ? 'checked' : '' }}>
												<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="all"><b>
													Todos los permisos
												</b></label>
											</div>

										</div>
	                    			</div>

									<hr>

									<div class="row">
										
										<div class="text-start col-6">

											<div class="form-check form-switch mt-2 mb-2">
												<input class="form-check-input bg-dark permission-checkbox" style="float: right;" type="checkbox" role="switch" name="permission_sellers" id="sellers" value="1" {{ $manager->permission_sellers ? 'checked' : '' }}>
												<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="sellers"><b>
													Administrar Vendedores
												</b></label>
											</div>

											<div class="form-check form-switch mt-2 mb-2">
												<input class="form-check-input bg-dark permission-checkbox" style="float: right;" type="checkbox" role="switch" name="permission_design" id="design" value="1" {{ $manager->permission_design ? 'checked' : '' }}>
												<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="design"><b>
													Diseñar Participaciones
												</b></label>
											</div>

										</div>

										<div class="text-start col-6">

											<div class="form-check form-switch mt-2 mb-2">
												<input class="form-check-input bg-dark permission-checkbox" style="float: right;" type="checkbox" role="switch" name="permission_statistics" id="total" value="1" {{ $manager->permission_statistics ? 'checked' : '' }}>
												<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="total"><b>
													Estadísticas Totales
												</b></label>
											</div>

											<div class="form-check form-switch mt-2 mb-2">
												<input class="form-check-input bg-dark permission-checkbox" style="float: right;" type="checkbox" role="switch" name="permission_payments" id="pay" value="1" {{ $manager->permission_payments ? 'checked' : '' }}>
												<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="pay"><b>
													Pagar Participaciones
												</b></label>
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
	// Sincronizar checkbox "Todos los permisos" con los permisos individuales
	$('#all').change(function() {
		if ($(this).is(':checked')) {
			$('.permission-checkbox').prop('checked', true);
		} else {
			$('.permission-checkbox').prop('checked', false);
		}
	});

	// Actualizar checkbox "Todos los permisos" cuando cambian los permisos individuales
	$('.permission-checkbox').change(function() {
		var allChecked = $('.permission-checkbox:checked').length === $('.permission-checkbox').length;
		$('#all').prop('checked', allChecked);
	});
</script>

@endsection








