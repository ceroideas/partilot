@extends('layouts.layout')

@section('title','Entidades')

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Entidades</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Entidad</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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

                    <form action="{{ route('entities.update', $entity->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                        <div class="col-md-3" style="position: relative;">

                    		<div class="form-card bs mb-3">
                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					&nbsp;&nbsp;
                    				</span>

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Datos Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#datos_contacto">
                    				
                    				<span>
                    					&nbsp;&nbsp;
                    				</span>

                    				<img src="{{url('assets/gestor.svg')}}" alt="">

                    				<label>
                    					Datos Gestor
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<div class="form-card show-content mb-3 bs">
                    			<h4 class="mb-0 mt-1">
                    				Estado Entidad
                    			</h4>
                    			<small><i>Bloquea o desbloquea la entidad</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="">Estado Actual</label> 
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
	                    			<label class="badge badge-lg {{ $statusClass }} float-end">
	                    				{{ $statusText }}
	                    			</label>
	                    			<div style="clear: both;"></div>
	                    			
	                    			<div class="form-group mt-3">
	                    				<label class="form-label">Cambiar Estado</label>
	                    				<select name="status" id="entity_status" class="form-select">
	                    					<option value="-1" {{ ($statusValue === null || $statusValue === -1) ? 'selected' : '' }}>Pendiente</option>
	                    					<option value="1" {{ $statusValue == 1 ? 'selected' : '' }}>Activo</option>
	                    					<option value="0" {{ $statusValue == 0 ? 'selected' : '' }}>Inactivo</option>
	                    				</select>
	                    			</div>
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

                    		<a href="{{ route('entities.show', $entity->id) }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    				
            				<div class="tab-pane fade active show" id="datos_legales">

	                    		<div class="form-card bs" style="min-height: 658px;">
	                    			<h4 class="mb-0 mt-1">
	                    				Datos legales de la entidad
	                    			</h4>
	                    			<small><i>Todos los campos son obligatorios</i></small>

	                    			<div class="form-group mt-2 mb-3">

	                    				<div class="row align-items-center">
	                    					<div class="col-auto">
			                    				<div class="photo-preview-3 logo-round entity-image-preview" @if($entity->image) style="background-image: url('{{ asset('uploads/' . $entity->image) }}');" @endif>
			                    					@if(!$entity->image)
			                    						<i class="ri-image-add-line"></i>
			                    					@endif
			                    				</div>
	                    					</div>
	                    					<div class="col-auto">
	                    						<small><i>Imagen entidad</i></small><br>
	                    						<b>Logotipo</b><br>
	                    						<label style="border-radius: 30px; width: 150px; background-color: #333;" class="btn btn-md btn-dark mt-2">
	                    							<small>Subir imagen</small>
	                    							<input type="file" id="entity-imagen-input" name="image" style="display: none;" accept="image/*">
	                    						</label>
	                    						<button type="button" id="entity-btn-eliminar-imagen" style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2">
	                    							<small>Eliminar imagen</small>
	                    						</button>
	                    						<input type="hidden" name="remove_image" id="entity-remove-image" value="0">
	                    					</div>
	                    					<div class="col-auto mt-3 mt-md-0 text-center">
	                    						<h4 class="mt-0 mb-0">{{ $entity->name ?? '' }}</h4>
	                    						<small>{{ $entity->province ?? '' }}</small>
	                    					</div>
	                    				</div>
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

					                                    <input class="form-control" name="name" value="{{ old('name', $entity->name) }}" type="text" placeholder="Nombre Entidad" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="province" value="{{ old('province', $entity->province) }}" type="text" placeholder="Provincia" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="city" value="{{ old('city', $entity->city) }}" type="text" placeholder="Localidad" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="postal_code" value="{{ old('postal_code', $entity->postal_code) }}" type="text" placeholder="Código Postal" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="address" value="{{ old('address', $entity->address) }}" type="text" placeholder="Dirección" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="nif_cif" id="entity-edit-nif-cif" value="{{ old('nif_cif', $entity->nif_cif) }}" type="text" placeholder="NIF/CIF" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="phone" value="{{ old('phone', $entity->phone) }}" type="text" placeholder="Teléfono" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" name="email" id="entity-edit-email" value="{{ old('email', $entity->email) }}" type="email" placeholder="Email" style="border-radius: 0 30px 30px 0;">
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

				                                    <textarea class="form-control" placeholder="Añade tu comentario" name="comments" id="" rows="6">{{ old('email', $entity->comments) }}</textarea>
				                                </div>
			                    			</div>

	                    				</div>

	                    				<div class="col-4 text-end">
	                    					<button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">
	                    						Guardar
	                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
	                    					</button>
	                    				</div>

	                    			</div>

	                    		</div>

	                    	</div>
                    	</div>

                    </form>

                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>
// Preview y eliminación de imagen de entidad
document.getElementById('entity-imagen-input').addEventListener('change', function(e) {
    const archivo = e.target.files[0];
    const preview = document.querySelector('.entity-image-preview');
    const removeFlag = document.getElementById('entity-remove-image');
    if (removeFlag && archivo) removeFlag.value = '0';
    if (archivo && preview) {
        const lector = new FileReader();
        lector.onload = function(ev) {
            preview.style.backgroundImage = 'url(' + ev.target.result + ')';
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
            const icon = preview.querySelector('i');
            if (icon) icon.style.display = 'none';
        };
        lector.readAsDataURL(archivo);
    }
});
document.getElementById('entity-btn-eliminar-imagen').addEventListener('click', function() {
    const input = document.getElementById('entity-imagen-input');
    const preview = document.querySelector('.entity-image-preview');
    const removeFlag = document.getElementById('entity-remove-image');
    if (input) input.value = '';
    if (removeFlag) removeFlag.value = '1';
    if (preview) {
        preview.style.backgroundImage = 'none';
        const icon = preview.querySelector('i');
        if (icon) {
            icon.style.display = '';
            icon.className = 'ri-image-add-line';
        }
    }
});

// Actualizar el badge de estado cuando se cambie el select
document.addEventListener('DOMContentLoaded', function() {
    // Validación documento entidad: NIF, NIE, TIE o CIF
    initSpanishDocumentValidation('entity-edit-nif-cif', { forEntity: true, showMessage: true });
    // Inicializar validación de email
    initEmailValidation('entity-edit-email', {
        context: 'entity',
        excludeId: {{ $entity->id }},
        showMessage: true
    });
    
    const select = document.getElementById('entity_status');
    
    if (select) {
        // Buscar el badge específico en el contexto del formulario
        const formCard = select.closest('.form-card');
        const badge = formCard ? formCard.querySelector('.badge') : null;
        
        if (badge) {
            select.addEventListener('change', function() {
                const value = this.value;
                if (value === '-1' || value === '') {
                    badge.textContent = 'Pendiente';
                    badge.className = 'badge badge-lg bg-secondary float-end';
                } else if (value === '1') {
                    badge.textContent = 'Activo';
                    badge.className = 'badge badge-lg bg-success float-end';
                } else {
                    badge.textContent = 'Inactivo';
                    badge.className = 'badge badge-lg bg-danger float-end';
                }
            });
        }
    }
});
</script>

@endsection