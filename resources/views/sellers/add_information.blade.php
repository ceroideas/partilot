@extends('layouts.layout')

@section('title','Vendedores/Asignación')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Vendedores/Asignación</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Vendedores/Asignación</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Invitación Vendedor

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
                    					Selec. Entidad
                    				</label>

                    			</div>

                    			<div class="form-wizard-element active">
                    				
                    				<span>
                    					2
                    				</span>

                    				<img src="{{url('icons/vendedores.svg')}}" alt="">

                    				<label>
                    					Dat. Vendedor
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<a href="{{url('sellers')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Invitación / Registro
                    			</h4>
                    			<small><i>Elige la manera en la que agregar al Vendedor</i></small>

                    			<div class="form-group mt-2 mb-3 admin-box">

                    				<div class="row">
                    					<div class="col-1">
                    						
		                    				<div class="photo-preview-3">
		                    					
		                    					<i class="ri-account-circle-fill"></i>

		                    				</div>
		                    				
		                    				<div style="clear: both;"></div>
                    					</div>

                    					<div class="col-4 text-center mt-3">

                    						<h4 class="mt-0 mb-0">FADEMUR</h4>

                    						<small>La Rioja</small> <br>
                    						
                    					</div>

                    					<div class="col-4">

                    						<div class="mt-3">
                    							Provincia: La Rioja <br>
                    							Dirección: Avd. Club Deportivo 28
                    						</div>
                    						
                    					</div>

                    					<div class="col-3">

                    						<div class="mt-3">
                    							Ciudad: Logroño <br>
                    							Tel: 941 900 900
                    						</div>
                    						
                    					</div>
                    				</div>
                    			</div>

                    			<br>

                    			<div id="all-options">
                    				<div class="row">
                    					
                    					<div class="col-12">
                    						
                    						<div class="mt-4 text-center">

                    							<div class="" id="manager-buttons">

	                    							<button class="btn btn-light btn-xl text-center m-2 bs" id="invite-manager" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px;">
	                    								<img class="mt-2 mb-1" src="{{url('assets/vendedor.svg')}}" alt="" width="60%">
	                    								<h4 class="mb-0">Vendedor <br> PARTILOT</h4>
	                    							</button>

	                    							<button class="btn btn-light btn-xl text-center m-2 bs" id="register-manager" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px; position: relative;">
	                    								<img class="mt-2 mb-1" src="{{url('assets/vendedor.svg')}}" alt="" width="60%">
	                    								<img class="mt-2 mb-1" src="{{url('assets/deni.svg')}}" alt="" width="35%" style="position: absolute; margin: auto; left: 0; right: 0; top: 8px;">
	                    								<h4 class="mb-0">Vendedor <br> EXTERNO</h4>
	                    							</button>

                    							</div>

                    							<div class="d-none" id="invite-form">

                    								<div class="row">
                    									
                    									<div class="col-4 offset-4">
		                    								<div class="card bs" style="border-radius: 16px;">
		                    									
		                    									<div class="card-body">

		                    										<h4 class="mt-0"><b>¡Invitar Usuario!</b></h4>

		                    										<br>

		                    										<div class="input-group input-group-merge group-form" style="border: none;">

									                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
									                                        <img src="{{url('assets/form-groups/admin/9.svg')}}" alt="">
									                                    </div>

									                                    <input class="form-control invite-email" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
									                                </div>

									                                <button disabled style="border-radius: 30px; width: 100%; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-3" id="invite-button">Invitar</button>
		                    										
		                    									</div>

		                    								</div>
                    									</div>
                    								</div>



                    							</div>

                    							<div class="d-none" id="accept-invite">

                    								<div style="width: 400px; margin: auto;">

                    									<div class="d-none" id="no-coincidence">
		                    								<h2>¡Hay 0 coincidencias!</h2>

		                    								<p>
		                    									No hemos encontrado un <b>usuario registrado con el email “tomasgarciamontes@example.com”</b>. Si haces clic en <b>Aceptar</b>, se le enviará una invitación para <b>unirse a tu entidad una vez se que registre.</b>
		                    								</p>
                    									</div>

                    									<div class="d-none" id="coincidence">
		                    								<h2>¡Hay 1 coincidencia!</h2>

		                    								<p>
		                    									Hemos encontrado un <b>usuario registrado con el email “tomasgarciamontes@example.com”</b>. Si haces clic en <b>Aceptar</b>, se le enviará una invitación para <b>unirse a tu entidad.</b>
		                    								</p>
                    									</div>

	                    								<div class="row">
	                    									<div class="col-6">
	                    										<button style="border-radius: 30px; width: 100%; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-3" id="cancel-invite">Cancelar</button>
	                    									</div>

	                    									<div class="col-6">
	                    										<a href="{{url('entities?table=1')}}" style="border-radius: 30px; width: 100%; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-3">Aceptar</a>
	                    									</div>
	                    								</div>
                    								</div>
                    							</div>
                    							
                    						</div>


                    					</div>

                    				</div>
                    			</div>

                    			<div id="register-manager-selected" class="d-none">

                    				<div style="min-height: 340px;">
                    					
                    				<div class="row">
                    					
                    					<div class="col-4">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Nombre</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
				                                    </div>

				                                    <input class="form-control" type="text" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
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

				                                    <input class="form-control" type="text" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
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

				                                    <input class="form-control" type="text" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
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

				                                    <input class="form-control" type="text" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

				                                    <input class="form-control" type="date" placeholder="01/01/1990" style="border-radius: 0 30px 30px 0;">
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

				                                    <input class="form-control" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

				                                    <input class="form-control" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
				                                </div>
			                    			</div>
                    					</div>

                    				</div>

                    				</div>

                    				<div class="row">

                    					<div class="col-8">
            								
    									</div>

	                    				<div class="col-4 text-end">
	                    					<a href="{{url('sellers?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
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

$('#invite-manager').click(function (e) {
	e.preventDefault();

	$('#manager-buttons').addClass('d-none');

	$('#invite-form').removeClass('d-none');
});

$('.invite-email').keyup(function(event) {
	
	if ($(this).val()) {
		$('#invite-button').prop('disabled',false);
	}else{
		$('#invite-button').prop('disabled',true);
	}
});

$('#invite-button').click(function (e) {
	e.preventDefault();

	$('#invite-form').addClass('d-none');

	$('#accept-invite').removeClass('d-none');

	if ($('.invite-email').val() == 'admin@partilot.com') {
		$('#coincidence').removeClass('d-none');
		$('#no-coincidence').addClass('d-none');
	}else{
		$('#coincidence').addClass('d-none');
		$('#no-coincidence').removeClass('d-none');
	}
});

$('#cancel-invite').click(function (e) {
	e.preventDefault();

	$('#accept-invite').addClass('d-none');
	
	$('#invite-form').removeClass('d-none');

});

$('#register-manager').click(function (e) {
	e.preventDefault();

	$('#all-options').addClass('d-none');

	$('#register-manager-selected').removeClass('d-none');

});

</script>

@endsection