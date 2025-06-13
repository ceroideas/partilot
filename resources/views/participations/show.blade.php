@extends('layouts.layout')

@section('title','Participaciones')

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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Participaciones</a></li>
                        <li class="breadcrumb-item active">Participación</li>
                    </ol>
                </div>
                <h4 class="page-title">Participaciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Datos Participación

                    </h4>

                    <br>

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">

                    		<ul class="form-card bs mb-3 nav">

                    			<li class="nav-item">
	                    			<div class="form-wizard-element active" data-bs-toggle="tab" data-bs-target="#datos_legales">
	                    				
	                    				<span>
	                    					&nbsp;&nbsp;
	                    				</span>

	                    				<img src="{{url('icons/entidades.svg')}}" alt="">

	                    				<label>
	                    					Datos participación
	                    				</label>

	                    			</div>
                    			</li>

	                    		<li class="nav-item">
	                    			<div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#datos_contacto">
	                    				
	                    				<span>
	                    					&nbsp;&nbsp;
	                    				</span>

	                    				<img src="{{url('icons/sorteos.svg')}}" alt="">

	                    				<label>
	                    					Vendedor
	                    				</label>

	                    			</div>

	                    		</li>
                    			
                    		</ul>

                    		<div class="form-card show-content bs mb-3">
                    			<h4 class="mb-0 mt-1">
                    				Estado Participación
                    			</h4>
                    			<small><i>Cambia estado Participación</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="">Estado Actual</label> <label class="badge badge-lg bg-success float-end">Vendido</label>
	                    			<div style="clear: both;"></div>
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

                    		<a href="{{url('participations/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">

                    		<div class="tabbable show-content">
                    			
                    			<div class="tab-content p-0">
                    				
                    				<div class="tab-pane fade active show" id="datos_legales">
                    					<div class="form-card bs" style="min-height: 658px;">
			                    			<h4 class="mb-0 mt-1">
			                    				Participación

			                    				{{-- <a href="{{url('administrations/edit/1')}}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    				Editar</a> --}}
			                    			</h4>
			                    			<small><i>Comprueba que el sorteo es el correcto</i></small>
			                    			<div style="clear: both;"></div>

			                    			
			                    			<br>

			                    			{{-- <div>
			                    				<div class="row">
			                    					
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Nombre comercial</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/1.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="El Búho Lotero" class="form-control" type="text" placeholder="Nombre Administración" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    				</div>
			                    			</div> --}}

			                    		</div>
                    				</div>

                    				<div class="tab-pane fade" id="datos_contacto">
                    					<div class="form-card bs" style="min-height: 658px;">
			                    			<h4 class="mb-0 mt-1">
			                    				Datos de contacto

			                    				<a href="{{url('administrations/edit/manager/1')}}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    				Editar</a>
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>
			                    			<div style="clear: both;"></div>

			                    			
			                    			<br>

			                    			<div>

			                    				<div class="row">
			                    					
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Nombre</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="Jorge" class="form-control" type="text" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Primer Apellido</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="Ruiz" class="form-control" type="text" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Segundo Apellido</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="Ortega" class="form-control" type="text" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    					
			                    					<div class="col-2">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">NIF/CIF</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="16600600A" class="form-control" type="text" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-3">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">F. Nacimiento</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="1975-01-01" class="form-control" type="date" placeholder="01/01/1990" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="administracion@ejemplo.es" class="form-control" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="941 900 900" class="form-control" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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

						                    			<div class="input-group input-group-merge group-form" style="border: none">

						                                    <textarea readonly="" class="form-control" placeholder="Añade tu comentario" name="" id="" rows="6"></textarea>
						                                </div>
					                    			</div>

			                    				</div>

			                    				<div class="col-4 text-end">
			                    					
			                    				</div>

			                    			</div>

			                    		</div>
                    				</div>

                    				<div class="tab-pane fade" id="configuracion_api">
                    					<div class="form-card bs" style="min-height: 658px;">
			                    			<h4 class="mb-0 mt-1">
			                    				Datos generales API

			                    				<a href="{{url('administrations/edit/api/1')}}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    				Editar</a>
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>
			                    			<div style="clear: both;"></div>

			                    			<div class="row">
			                    					
		                    					<div class="col-7">
		                    						<div class="form-group mt-2 mb-3">
		                    							<label class="label-control">Nombre de la integración</label>

						                    			<div class="input-group input-group-merge group-form">

						                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
						                                      	<img src="{{url('assets/form-groups/admin/1.svg')}}" alt="">
						                                    </div>

						                                    <input class="form-control" value="El Búho Lotero" type="text" readonly="" placeholder="Nombre Integración" style="border-radius: 0 30px 30px 0;">

						                                </div>
						                                <small><i>Ayuda: Un nombre fácil de recordar para identificar esta configuración</i></small>
					                    			</div>
		                    					</div>
		                    					<div class="col-5">
		                    						<div class="form-group mb-3">
		                    							
		                    							<div class="form-check form-switch mt-4" style="margin-top: 3rem !important;">
															<input disabled="" style="float: right;" class="form-check-input bg-dark" type="checkbox" role="switch" id="api_status" checked>
															<label style="float: right; margin-right: 50px;" class="form-check-label" for="api_status"><b>Estado de la integración</b></label>
														</div>

						                    			
					                    			</div>
		                    					</div>

		                    				</div>

		                    				<h4 class="mb-0 mt-1">
			                    				Datos generales API
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>

			                    			<div class="row">
			                    					
		                    					<div class="col-7">
		                    						<div class="form-group mt-2 mb-3">
		                    							<label class="label-control">URL Base de la API (Endpoint)</label>

						                    			<div class="input-group input-group-merge group-form">

						                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
						                                      	<img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
						                                    </div>

						                                    <input class="form-control" value="http://api.cliente.com/v1/recargas" type="text" readonly="" placeholder="URL Base de la API" style="border-radius: 0 30px 30px 0;">

						                                </div>
					                    			</div>
		                    					</div>

		                    					<div class="col-5">
		                    						<div class="form-group mt-2 mb-3">
		                    							<label class="label-control">Método de Autenticación</label>

						                    			<div class="input-group input-group-merge group-form">

						                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
						                                      	<img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
						                                    </div>

						                                    <select class="form-control" name="" id="method" disabled>
						                                    	<option value="" disabled>Elige una opción</option>
						                                    	<option value="apikey">Clave API (API Key)</option>
						                                    	<option value="oauth">OAuth 2.0</option>
						                                    	<option value="bearer">Bearer Token (JWT)</option>
						                                    	<option value="basic" selected>Básico (Usuario/Contraseña)</option>
						                                    </select>

						                                </div>
					                    			</div>
		                    					</div>
		                    					

		                    				</div>

		                    				<div id="apikey" class="d-none mt-3 method">
		                    					
		                    					<h4 class="mb-0 mt-1">
				                    				Clave API (API Key)
				                    			</h4>

				                    			<div class="row">
			                    					
			                    					<div class="col-12">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">API Key</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    {{-- <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div> --}}

							                                    <input class="form-control" type="text" placeholder="API Key" style="border-radius: 0 30px 30px 0;">

							                                </div>
						                    			</div>
			                    					</div>
			                    				</div>
		                    				</div>
		                    				<div id="oauth" class="d-none mt-3 method">
		                    					
		                    					<h4 class="mb-0 mt-1">
				                    				OAuth 2.0
				                    			</h4>

				                    			<div class="row">
			                    					
			                    					<div class="col-12">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Token OAuth</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    {{-- <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div> --}}

							                                    <input class="form-control" type="text" placeholder="Token Oauth" style="border-radius: 0 30px 30px 0;">

							                                </div>
						                    			</div>
			                    					</div>
			                    				</div>

		                    				</div>
		                    				<div id="bearer" class="d-none mt-3 method">
		                    					
		                    					<h4 class="mb-0 mt-1">
				                    				Bearer Token (JWT)
				                    			</h4>

				                    			<div class="row">
			                    					
			                    					<div class="col-12">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Bearer Token</label>

							                    			<div class="input-group input-group-merge group-form" {{-- style="border-bottom: none;" --}}>

							                                    {{-- <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div> --}}

							                                    <textarea readonly="" class="form-control" placeholder="Bearer Token" name="" id="" rows="4"></textarea>

							                                </div>
						                    			</div>
			                    					</div>
			                    				</div>
		                    				</div>
		                    				<div id="basic" class="d-none- mt-3 method">
		                    					
		                    					<h4 class="mb-0 mt-1">
				                    				Básico (Usuario/Contraseña)
				                    			</h4>

				                    			<div class="row">
			                    					
			                    					<div class="col-7">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Usuario</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    {{-- <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div> --}}

							                                    <input class="form-control" value="elbuholotero@partilot.com" readonly="" type="text" placeholder="Usuario" style="border-radius: 0 30px 30px 0;">

							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-5">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Contraseña</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    {{-- <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/4.svg')}}" alt="">
							                                    </div> --}}

							                                    <input class="form-control" value="12345678" readonly="" type="text" placeholder="Contraseña" style="border-radius: 0 30px 30px 0;">

							                                </div>
						                    			</div>
			                    					</div>
			                    					
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Formato de Datos de Envío</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                      	<img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
							                                    </div>

							                                    <select class="form-control" name="" id="" disabled>
							                                    	<option value="" disabled selected>Elige una opción</option>
							                                    	<option value="json" selected>JSON</option>
							                                    	<option value="text">Text</option>
							                                    	<option value="xml">XML</option>
							                                    </select>

							                                </div>
						                    			</div>
			                    					</div>

			                    				</div>
		                    				</div>

			                    		</div>
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

	$('.form-wizard-element').click(function(event) {
		event.preventDefault();

		let target = $(this).data('target');

	    let tab = new bootstrap.Tab(document.querySelector(target));
	    console.log(tab);
	    tab.show();

	});

	$('#method').change(function (e) {
		e.preventDefault();

		let value = $(this).val();

		console.log(value);

		$('.method').addClass('d-none');

		$('#'+value).removeClass('d-none');
	});

</script>

@endsection