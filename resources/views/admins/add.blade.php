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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Administraciones</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
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
                    			
                    		</div>

                    		<div class="form-card">
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

	                                    <input class="form-control" type="text" name="web" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
	                                </div>
                    			</div>
                    		</div>

                    		<a href="{{url('administrations')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<form action="{{url('administrations/add/manager')}}" method="POST" enctype="multipart/form-data">

                    				@csrf()
                    				
                                <!-- Web (hidden, sincronizado con el campo visible de la izquierda) -->
                                <input type="hidden" name="web" id="webHidden" value="">
                    				
	                    			<h4 class="mb-0 mt-1">
	                    				Datos legales de la administración
	                    			</h4>
	                    			<small><i>Todos los campos son obligatorios</i></small>

	                    			<div class="form-group mt-2 mb-3">

	                    				<div class="photo-preview">
	                    					
	                    					<i class="ri-image-add-line"></i>

	                    				</div>

	                    				<div>
	                    					
	                    					<small><i>Imágen empresa</i></small>
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
	                    					
	                    					<div class="col-4">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Nombre comercial</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/1.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" required name="name" placeholder="Nombre Administración" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="number" required name="receiving" placeholder="000000" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" required name="society" placeholder="José Andrés / Administración S.L.U." style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" required name="nif_cif" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" required name="province" placeholder="Provincia" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" required name="city" placeholder="Localidad" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="number" required name="postal_code" placeholder="C.P." style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" required name="address" placeholder="Dirección" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="email" required name="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="phone" required name="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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
				                    			<div class="input-group input-group-merge group-form">
				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <span style="font-weight: bold;">ES</span>
				                                    </div>
				                                    <input class="form-control" type="text" name="account" placeholder="1234567890123456789012" maxlength="22" style="border-radius: 0 30px 30px 0;" required>
				                                </div>
			                    			</div>
			                    			<small class="text-muted">Ingrese los 22 dígitos de la cuenta bancaria (sin espacios). El prefijo ES se añadirá automáticamente.</small>

	                    				</div>

	                    				<div class="col-4 text-end">
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

	// Persistir datos del formulario en localStorage
	function saveFormData() {
	    const formData = {
	        web: document.querySelector('input[name="web"]')?.value || '',
	        name: document.querySelector('input[name="name"]')?.value || '',
	        receiving: document.querySelector('input[name="receiving"]')?.value || '',
	        society: document.querySelector('input[name="society"]')?.value || '',
	        nif_cif: document.querySelector('input[name="nif_cif"]')?.value || '',
	        province: document.querySelector('input[name="province"]')?.value || '',
	        city: document.querySelector('input[name="city"]')?.value || '',
	        postal_code: document.querySelector('input[name="postal_code"]')?.value || '',
	        address: document.querySelector('input[name="address"]')?.value || '',
	        email: document.querySelector('input[name="email"]')?.value || '',
	        phone: document.querySelector('input[name="phone"]')?.value || '',
	        account: Array.from(document.querySelectorAll('input[name="account[]"]')).map(input => input.value)
	    };
	    localStorage.setItem('administration_form_data', JSON.stringify(formData));
	}

	// Cargar datos guardados
	function loadFormData() {
	    const savedData = localStorage.getItem('administration_form_data');
	    if (savedData) {
	        const formData = JSON.parse(savedData);
	        Object.keys(formData).forEach(key => {
	            if (key === 'account') {
	                const accountInputs = document.querySelectorAll('input[name="account[]"]');
	                accountInputs.forEach((input, index) => {
	                    if (formData.account[index]) {
	                        input.value = formData.account[index];
	                    }
	                });
	            } else {
	                const input = document.querySelector(`input[name="${key}"]`);
	                if (input && formData[key]) {
	                    input.value = formData[key];
	                }
	            }
	        });
	    }
	}

	// Cargar datos al cargar la página
	loadFormData();

	// Sincronizar el campo web visible con el hidden dentro del formulario
	const webVisible = document.querySelector('input[name="web"]');
	const webHidden = document.getElementById('webHidden');
	if (webVisible && webHidden) {
	    // Inicializar
	    webHidden.value = webVisible.value || '';
	    // Sincronizar en tiempo real
	    webVisible.addEventListener('input', function() {
	        webHidden.value = this.value || '';
	    });
	    webVisible.addEventListener('change', function() {
	        webHidden.value = this.value || '';
	    });
	}

	// Guardar datos al cambiar cualquier campo
	document.querySelectorAll('input').forEach(input => {
	    input.addEventListener('input', saveFormData);
	    input.addEventListener('change', saveFormData);
	});

    // Enviar el campo 'web' (que está fuera del <form>) como hidden dentro del formulario y limpiar storage
    document.querySelector('form').addEventListener('submit', function(e) {
        const form = this;
        const webInputOutside = document.querySelector('input[name="web"]');
        if (webInputOutside) {
            const hiddenWeb = document.createElement('input');
            hiddenWeb.type = 'hidden';
            hiddenWeb.name = 'web';
            hiddenWeb.value = webInputOutside.value || '';
            form.appendChild(hiddenWeb);
        }
        localStorage.removeItem('administration_form_data');
    });

    // Limpiar datos al navegar al paso 2 (manager)
    document.querySelector('button[type="submit"]').addEventListener('click', function() {
        setTimeout(() => {
            localStorage.removeItem('administration_form_data');
        }, 100);
	});

</script>

@endsection