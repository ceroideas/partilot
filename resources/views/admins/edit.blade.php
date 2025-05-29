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
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Administración</a></li>
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

	                                    <input class="form-control" type="text" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
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
                    		<div class="form-card bs">
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

                    					<label style="border-radius: 30px; width: 150px; background-color: #333;" class="btn btn-md btn-dark mt-2"><small>Subir Imágen</small></label>
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

				                                    <input value="El Búho Lotero" class="form-control" type="text" placeholder="Nombre Administración" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="05716" class="form-control" type="number" placeholder="000000" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="Jorge Ruiz Ortega" class="form-control" type="text" placeholder="José Andrés / Administración S.L.U." style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="16600600A" class="form-control" type="text" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="La Rioja" class="form-control" type="text" placeholder="Provincia" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="Logroño" class="form-control" type="text" placeholder="Localidad" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="26007" class="form-control" type="number" placeholder="C.P." style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="Avd. Club Deportivo 28" class="form-control" type="text" placeholder="Dirección" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="administracion@ejemplo.es" class="form-control" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

				                                    <input value="941 900 900" class="form-control" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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
			                                    <input class="" type="number" placeholder="1234" max="9999" min="1000">

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" placeholder="1234" max="9999" min="1000">

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" placeholder="1234" max="9999" min="1000">

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" placeholder="12" max="99" min="10">

			                                    <label>
			                                    	-
			                                    </label>

			                                    <input class="" type="number" placeholder="1234567890" max="9999999999" min="1000000000">

			                                </div>
		                    			</div>

                    				</div>

                    				<div class="col-4 text-end">
                    					<a href="{{url('administrations/view/1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; /*top: calc(100% - 51px);*/" class="btn btn-md btn-light mt-2">Guardar
                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></a>
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

</script>

@endsection