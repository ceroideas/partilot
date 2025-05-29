@extends('layouts.layout')

@section('title','Administraciones')

@section('content')

<style>
	
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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Administraciones</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Administración</a></li>
                        <li class="breadcrumb-item active">Configuración API</li>
                    </ol>
                </div>
                <h4 class="page-title">Configuración API</h4>
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

                    		<ul class="form-card bs mb-3">

                    			<div class="form-wizard-element">
                    				
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
                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					3
                    				</span>

                    				<img src="{{url('assets/api.svg')}}" alt="">

                    				<label>
                    					Configuración API
                    				</label>

                    			</div>
                    			
                    		</ul>

                    		<div class="form-card show-content mb-3">
                    			<h4 class="mb-0 mt-1">
                    				Página web
                    			</h4>
                    			<small><i>Este campo no es obligatorio</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="label-control">Web</label>

	                    			<div class="input-group input-group-merge group-form" style="border: none">

	                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                        <img src="{{url('assets/form-groups/admin/0.svg')}}" alt="">
	                                    </div>

	                                    <input readonly="" class="form-control" type="text" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
	                                </div>
                    			</div>
                    		</div>

                    		<div class="form-card show-content bs">
                    			<h4 class="mb-0 mt-1">
                    				Estado Administración
                    			</h4>
                    			<small><i>Bloquea o desbloquea la administración</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="">Estado Actual</label> <label class="badge badge-lg bg-success float-end">Activo</label>
	                    			<div style="clear: both;"></div>
                    			</div>
                    		</div>

                    		<a href="{{url('administrations/view/1')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">

        					<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Datos generales API

                    				<button class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
                    				Editar</button>
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

			                                    <input class="form-control" value="El Búho Lotero" type="text" placeholder="Nombre Integración" style="border-radius: 0 30px 30px 0;">

			                                </div>
			                                <small><i>Ayuda: Un nombre fácil de recordar para identificar esta configuración</i></small>
		                    			</div>
                					</div>
                					<div class="col-5">
                						<div class="form-group mb-3">
                							
                							<div class="form-check form-switch mt-4" style="margin-top: 3rem !important;">
												<input style="float: right;" class="form-check-input bg-dark" type="checkbox" role="switch" id="api_status" checked>
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

			                                    <input class="form-control" value="http://api.cliente.com/v1/recargas" type="text" placeholder="URL Base de la API" style="border-radius: 0 30px 30px 0;">

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

			                                    <select class="form-control" name="" id="method" style="border-radius: 0 30px 30px 0;">
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
                				<div id="basic" class="mt-3 method">
                					
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

				                                    <input class="form-control" value="elbuholotero@partilot.com" type="text" placeholder="Usuario" style="border-radius: 0 30px 30px 0;">

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

				                                    <input class="form-control" value="12345678" type="text" placeholder="Contraseña" style="border-radius: 0 30px 30px 0;">

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

				                                    <select class="form-control" name="" id="">
				                                    	<option value="" disabled selected>Elige una opción</option>
				                                    	<option value="json" selected>JSON</option>
				                                    	<option value="text">Text</option>
				                                    	<option value="xml">XML</option>
				                                    </select>

				                                </div>
			                    			</div>
                    					</div>

                    				</div>

                    				<div class="row">
                    					
                    					<div class="col-12 text-end">
                    						<a href="{{url('administrations/view/1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; /*top: calc(100% - 51px);*/" class="btn btn-md btn-light mt-2">Guardar
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></a>
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