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

                    	Datos Gestor

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
                    					Datos administración
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
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

	                                    <input class="form-control" type="text" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
	                                </div>
                    			</div>
                    		</div>

                    		<a href="{{url('administrations/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs">
                    			<h4 class="mb-0 mt-1">
                    				Datos de contacto
                    			</h4>
                    			<small><i>Todos los campos son obligatorios</i></small>

                    			<div class="form-group mt-2 mb-3 admin-box">

                    				<div class="row">
                    					<div class="col-1">
                    						
		                    				<div class="photo-preview-2">
		                    					
		                    					<i class="ri-account-circle-fill"></i>

		                    				</div>
		                    				
		                    				<div style="clear: both;"></div>
                    					</div>

                    					<div class="col-4 text-center">

                    						<h4 class="mt-0 mb-0">El Buho Lotero</h4>

                    						<small>Jorge Ruíz Ortega</small> <br>

                    						<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> 05716
                    						
                    					</div>

                    					<div class="col-4">

                    						<div class="mt-2">
                    							Provincia: La Rioja <br>
                    							Dirección: Avd. Club Deportivo 28
                    						</div>
                    						
                    					</div>

                    					<div class="col-3">

                    						<div class="mt-2">
                    							Ciudad: Logroño <br>
                    							Tel: 941 900 900
                    						</div>
                    						
                    					</div>
                    				</div>
                    			</div>

                    			
                    			<br>

                    			<form action="{{url('administrations/store')}}" method="POST" enctype="multipart/form-data">

                    				@csrf()

	                    			<div>

	                    				<div class="row">
	                    					
	                    					<div class="col-4">
	                    						<div class="form-group mt-2 mb-3">
	                    							<label class="label-control">Nombre</label>

					                    			<div class="input-group input-group-merge group-form">

					                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
					                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
					                                    </div>

					                                    <input class="form-control" type="text" name="name" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" name="last_name" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" name="last_name2" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="text" name="nif_cif" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="date" name="birthday" placeholder="01/01/1990" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="email" name="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

					                                    <input class="form-control" type="phone" name="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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

				                                    <textarea class="form-control" name="comment" placeholder="Añade tu comentario" id="" rows="6"></textarea>
				                                </div>
			                    			</div>

	                    				</div>

	                    				<div class="col-4 text-end">
	                    					<button style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
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

</script>

@endsection