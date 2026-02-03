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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Entidades</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
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

            		<h4 class="header-title">

                    	Datos Entidad

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					1
                    				</span>

                    				<img src="{{url('assets/admin.svg')}}" alt="">

                    				<label>
                    					Selec. Administración
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Datos Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					3
                    				</span>

                    				<img src="{{url('assets/gestor.svg')}}" alt="">

                    				<label>
                    					Datos Gestor
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<div class="form-card mb-3 bs">
                    			
                    			<div class="form-check form-switch mt-2 mb-2">
									<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="fin" checked>
									<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="fin"><b>Entidad sin fin lucrativo</b></label>
								</div>

								<div class="form-check form-switch mt-2 mb-2">
									<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="coste" checked>
									<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="coste"><b>Coste gestión</b></label>
								</div>

                    		</div>

                    		<div class="form-card bs">
                    			
                    			<div class="row">
                					<div class="col-4">
                						
	                    				<div class="photo-preview-3">
	                    					
	                    					<i class="ri-account-circle-fill"></i>

	                    				</div>
	                    				
	                    				<div style="clear: both;"></div>
                					</div>

                					<div class="col-8 text-center mt-2">

                						<h4 class="mt-0 mb-0">{{session('selected_administration')->name ?? 'Administración'}}</h4>

                						<small>
                							@php
                								$admin = session('selected_administration');
                								$managerName = 'Gestor';
                								if ($admin && $admin->manager && $admin->manager->user) {
                									$managerName = trim(($admin->manager->user->name ?? '') . ' ' . ($admin->manager->user->last_name ?? ''));
                									if (empty($managerName)) {
                										$managerName = 'Gestor';
                									}
                								}
                							@endphp
                							{{ $managerName }}
                						</small> <br>

                						<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{session('selected_administration')->province ?? 'Provincia'}}
                						
                					</div>
                				</div>

                    		</div>

                    		<a href="{{url('entities/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form action="{{url('entities/store-information')}}" method="POST" enctype="multipart/form-data">
                    				@csrf()
	                    			<h4 class="mb-0 mt-1">
	                    				Datos legales de la entidad
	                    			</h4>
	                    			<small><i>Todos los campos son obligatorios</i></small>

	                    			@if ($errors->any())
	                    				<div class="alert alert-danger mt-3">
	                    					<ul class="mb-0">
	                    						@foreach ($errors->all() as $error)
	                    							<li>{{ $error }}</li>
	                    						@endforeach
	                    					</ul>
	                    				</div>
	                    			@endif

	                    			<div class="form-group mt-2 mb-3">

	                    				<div class="photo-preview">
	                    					
	                    					<i class="ri-image-add-line"></i>

	                    				</div>

	                    				<div>
	                    					
	                    					<small><i>Imágen entidad</i></small>
	                    					 <br>
	                    					<b>Logotipo</b>
	                    					<br>

	                    					<label style="border-radius: 30px; width: 150px; background-color: #333;" class="btn btn-md btn-dark mt-2"><small>Subir Imágen</small>
	                    						<input type="file" id="imagenInput" name="image" style="display: none;" accept="image/*">
	                    					</label>
	                    					<label style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2"><small>Eliminar Imágen</small></label>

	                    				</div>
	                    				
	                    				<div style="clear: both;"></div>
	                    			</div>

	                    			<br>

	                    			<div>
	                    				<div class="row">
	                    					
	                    					<div class="col-6">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Nombre comercial</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/1.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="name" placeholder="Nombre Entidad" value="{{ old('name') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('name')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-3">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Provincia</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/5.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="province" placeholder="Provincia" value="{{ old('province') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('province')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-3">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Localidad</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/6.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="city" placeholder="Localidad" value="{{ old('city') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('city')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-2">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Código Postal</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/7.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="postal_code" placeholder="C.P." value="{{ old('postal_code') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('postal_code')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-4">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Dirección</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/8.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="address" placeholder="Dirección" value="{{ old('address') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('address')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-3">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">NIF/CIF</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="nif_cif" id="entity-nif-cif" placeholder="B26262626" value="{{ old('nif_cif') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('nif_cif')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-3">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Teléfono</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/10.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="phone" placeholder="940 200 200" value="{{ old('phone') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('phone')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    					<div class="col-4">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Email</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/9.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="email" name="email" placeholder="ejemplo@cuentaemail.com" value="{{ old('email') }}" required style="border-radius: 0 30px 30px 0;">
					                                    @error('email')
					                                        <div class="text-danger small mt-1">{{ $message }}</div>
					                                    @enderror
					                                </div>
				                    			</div>
	                    					</div>

	                    				</div>
	                    			</div>


	                    			<h4 class="mb-0 mt-1">
	                    				Comentarios
	                    			</h4>
	                    			<small><i>Puedes añadir un comentario si necesitas añadir información adicional <br> sobre la entidad. Puedes añadir comentarios mas tarde.</i></small>

	                    			<div class="row">
	                    				
	                    				<div class="col-8">
	                    					
	                    					<div class="form-group mt-2">
				                    			<label class="label-control">Comentario</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <textarea class="form-control" placeholder="Añade tu comentario" name="comments" id="" rows="6">{{ old('comments') }}</textarea>
				                                    @error('comments')
				                                        <div class="text-danger small mt-1">{{ $message }}</div>
				                                    @enderror
				                                </div>
			                    			</div>

	                    				</div>

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

	document.getElementById('imagenInput').addEventListener('change', function(event) {
	    const archivo = event.target.files[0];

	    if (archivo) {
	        const lector = new FileReader();
	        lector.onload = function(e) {
	        	$('.photo-preview').css('background-image', 'url('+e.target.result+')');
	        	// Guardar en localStorage para persistencia
	        	localStorage.setItem('image_entity_create', e.target.result);
	        }
	        lector.readAsDataURL(archivo);
	    } else {
	        $('.photo-preview').css('background-image', 'none');
	        localStorage.removeItem('image_entity_create');
	    }
	});

	// Restaurar imagen si hay error de validación
	document.addEventListener('DOMContentLoaded', function() {
	    const savedImage = localStorage.getItem('image_entity_create');
	    if (savedImage) {
	        $('.photo-preview').css('background-image', 'url('+savedImage+')');
	    }

	    // Inicializar validación de documento español
	    initSpanishDocumentValidation('entity-nif-cif', {
	        showMessage: true
	    });
	});

	// Limpiar localStorage al enviar exitosamente
	$('form[action*="store-information"]').on('submit', function() {
	    setTimeout(() => {
	        localStorage.removeItem('image_entity_create');
	    }, 1000);
	});

</script>

@endsection