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
                        <li class="breadcrumb-item"><a href="{{ route('administrations.index') }}">Administraciones</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('administrations.show', $administration->id) }}">Administración</a></li>
                        <li class="breadcrumb-item active">Editar</li>
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

            		<h4 class="header-title">

                    	Datos Administración

                    </h4>

                    <br>

                    <form action="{{ route('administrations.update', $administration->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					1
                    				</span>

                    				<img src="{{url('assets/admin.svg')}}" alt="">

                    				<label>
                    					Datos administración
                    				</label>

                    			</div>

                    			<div class="form-wizard-element">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('assets/gestor.svg')}}" alt="">

                    				<label>
                    					Datos Gestor
                    				</label>

                    			</div>

	                			<div class="form-wizard-element">
	                				
	                				<span>
	                					3
	                				</span>

	                				<img src="{{url('assets/api.svg')}}" alt="">

	                				<label>
	                					Configuración API
	                				</label>

	                			</div>
                    			
                    		</div>

                    		<div class="form-card bs mb-3">
                    			<h4 class="mb-0 mt-1">
                    				Página web
                    			</h4>
                    			<small><i>Este campo no es obligatorio</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="label-control">Web</label>

	                    			<div class="input-group input-group-merge group-form">

	                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                        <img src="{{url('assets/form-groups/admin/0.svg')}}" alt="">
	                                    </div>

	                                    <input class="form-control" type="text" name="web" value="{{ old('web', $administration->web) }}" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
	                                </div>
                    			</div>
                    		</div>

                    		<div class="form-card show-content bs">
                    			<h4 class="mb-0 mt-1">
                    				Estado Administración
                    			</h4>
                    			<small><i>Bloquea o desbloquea la administración</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="">Estado Actual</label> 
                                    <label class="badge badge-lg {{ $administration->status ? 'bg-success' : 'bg-danger' }} float-end">
                                        {{ $administration->status ? 'Activo' : 'Inactivo' }}
                                    </label>
	                    			<div style="clear: both;"></div>
	                    			
	                    			<div class="form-check mt-2">
	                    				<input class="form-check-input" type="checkbox" name="status" value="1" {{ $administration->status ? 'checked' : '' }}>
	                    				<label class="form-check-label">
	                    					Activar administración
	                    				</label>
	                    			</div>
                    			</div>
                    		</div>

                    		<a href="{{ route('administrations.show', $administration->id) }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs">
                    			<h4 class="mb-0 mt-1">
                    				Datos legales de la administración
                    			</h4>
                    			<small><i>Todos los campos son obligatorios</i></small>

                    			<div class="form-group mt-2 mb-3">

                    				<div class="photo-preview" @if($administration->image) style="background-image: url('{{ asset('images/' . $administration->image) }}');" @endif>
                    					@if(!$administration->image)
                    						<i class="ri-image-add-line"></i>
                    					@endif
                    				</div>

                    				<div>
                    					
                    					<small><i>Imágen empresa</i></small>
                    					 <br>
                    					<b>Logotipo</b>
                    					<br>

                    					<label style="border-radius: 30px; width: 150px; background-color: #333;" class="btn btn-md btn-dark mt-2">
                    						<small>Subir Imágen</small>
                    						<input type="file" id="imagenInput" name="image" style="display: none;" accept="image/*">
                    					</label>
                    					@if($administration->image)
                    						<a href="#" style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2">
                    							<small>Eliminar Imágen</small>
                    						</a>
                    					@endif

                    				</div>
                    				
                    				<div style="clear: both;"></div>
                    			</div>

                    			
                    			<br>

                    			<div>
                    				<div class="row">
                    					<div class="col-4">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Nombre comercial</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/1.svg')}}" alt="">
				                                    </div>

				                                    <input value="{{ old('name', $administration->name) }}" class="form-control" type="text" name="name" placeholder="Nombre Administración" style="border-radius: 0 30px 30px 0;" required>
				                                </div>
			                    			</div>
                    					</div>
                    					<div class="col-3">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Nº Receptor</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/2.svg')}}" alt="">
				                                    </div>

				                                    <input value="{{ old('receiving', $administration->receiving) }}" class="form-control" type="number" name="receiving" placeholder="000000" style="border-radius: 0 30px 30px 0;" required>
				                                </div>
			                    			</div>
                    					</div>
                    					<div class="col-5">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Nombre Autónomo / Sociedad</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/3.svg')}}" alt="">
				                                    </div>

				                                    <input value="{{ old('society', $administration->society) }}" class="form-control" type="text" name="society" placeholder="José Andrés / Administración S.L.U." style="border-radius: 0 30px 30px 0;" required>
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

				                                    <input value="{{ old('nif_cif', $administration->nif_cif) }}" class="form-control" type="text" name="nif_cif" placeholder="B26262626" style="border-radius: 0 30px 30px 0;" required>
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

				                                    <input value="{{ old('province', $administration->province) }}" class="form-control" type="text" name="province" placeholder="Provincia" style="border-radius: 0 30px 30px 0;" required>
				                                </div>
			                    			</div>
                    					</div>

                    					<div class="col-4">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Localidad</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/6.svg')}}" alt="">
				                                    </div>

				                                    <input value="{{ old('city', $administration->city) }}" class="form-control" type="text" name="city" placeholder="Localidad" style="border-radius: 0 30px 30px 0;" required>
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

				                                    <input value="{{ old('postal_code', $administration->postal_code) }}" class="form-control" type="number" name="postal_code" placeholder="C.P." style="border-radius: 0 30px 30px 0;" required>
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

				                                    <input value="{{ old('address', $administration->address) }}" class="form-control" type="text" name="address" placeholder="Dirección" style="border-radius: 0 30px 30px 0;" required>
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

				                                    <input value="{{ old('email', $administration->email) }}" class="form-control" type="email" name="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;" required>
				                                </div>
			                    			</div>
                    					</div>

                    					<div class="col-4">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Teléfono</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/10.svg')}}" alt="">
				                                    </div>

				                                    <input value="{{ old('phone', $administration->phone) }}" class="form-control" type="phone" name="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;" required>
				                                </div>
			                    			</div>
                    					</div>

                    				</div>
                    			</div>

                    			<h4 class="mb-0 mt-1">
                    				Datos Pago
                    			</h4>
                    			<small><i>Introduzca los detalles de su cuenta bancaria para procesar los pagos. Asegúrese de que la <br> información es correcta y está actualizada</i></small>

                    			<div class="row">
                    				
                    				<div class="col-8">
                    					
                    					<div class="form-group mt-2">
			                    			<div class="input-group input-group-merge group-account">
			                    				@php
			                    					$accountParts = explode(' ', $administration->account ?? '');
			                    					$accountArray = array_pad($accountParts, 5, '');
			                    				@endphp
			                                    <input class="" type="number" name="account[0]" placeholder="1234" max="9999" min="1000" value="{{ old('account.0', $accountArray[0]) }}" required>

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" name="account[1]" placeholder="1234" max="9999" min="1000" value="{{ old('account.1', $accountArray[1]) }}" required>

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" name="account[2]" placeholder="1234" max="9999" min="1000" value="{{ old('account.2', $accountArray[2]) }}" required>

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" name="account[3]" placeholder="12" max="99" min="10" value="{{ old('account.3', $accountArray[3]) }}" required>

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" name="account[4]" placeholder="1234567890" max="9999999999" min="1000000000" value="{{ old('account.4', $accountArray[4]) }}" required>

			                                </div>
		                    			</div>

                    				</div>

                    				<div class="col-4 text-end">
                    					<button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                    						Guardar
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i>
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

document.getElementById('imagenInput').addEventListener('change', function(event) {
    const archivo = event.target.files[0];
    const photoPreview = document.querySelector('.photo-preview');

    if (archivo) {
        const lector = new FileReader();
        lector.onload = function(e) {
        	$('.photo-preview').css('background-image', 'url('+e.target.result+')');
        	// Ocultar el icono cuando se carga una imagen
        	$('.photo-preview i').hide();
        }
        lector.readAsDataURL(archivo);
    } else {
        $('.photo-preview').css('background-image', 'none'); // Limpiar preview si se cancela la selección
        // Mostrar el icono si no hay imagen
        $('.photo-preview i').show();
    }
});

</script>

@endsection