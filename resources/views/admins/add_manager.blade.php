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

            		<h4 class="header-title">Acceso al panel</h4>

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

                    				<label>Acceso panel</label>

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

                                        <input class="form-control" type="text" id="web-field" value="{{ old('web', session('administration.web', '')) }}" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
	                                </div>
                    			</div>
                    		</div>

                    		<a href="{{url('administrations/add')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs">
                    			<div class="form-group mt-2 mb-3 admin-box">
                    				<div class="row">
                    					<div class="col-1">
		                    				<div class="photo-preview-2" @if(session('administration.image')) style="background-image: url('{{ url('images/' . session('administration.image')) }}'); background-size: cover; background-position: center;" @endif>
		                    					@if(!session('administration.image'))
		                    						<i class="ri-account-circle-fill"></i>
		                    					@endif
		                    				</div>
                    					</div>
                    					<div class="col-8">
                    						<h4 class="mt-0 mb-0">{{ session('administration.name', 'Administración') }}</h4>
                    						<p class="mb-0 text-muted">Usuario del panel: <strong>{{ session('administration.email') }}</strong></p>
                    					</div>
                    				</div>
                    			</div>

                    			<form action="{{url('administrations/store')}}" method="POST">
                    				@csrf()
                    				<input type="hidden" name="web" id="web-hidden" value="{{ old('web', session('administration.web', '')) }}">

                    				@if ($errors->any())
                    					<div class="alert alert-danger mb-3">
                    						<ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    					</div>
                    				@endif

                    				<div class="alert alert-info">Defina la contraseña de acceso al panel (mínimo 8 caracteres).</div>

                    				<div class="row">
                    					<div class="col-md-6">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Contraseña</label>
                    							<input class="form-control" type="password" name="panel_password" required autocomplete="new-password" style="border-radius: 30px;">
                    						</div>
                    					</div>
                    					<div class="col-md-6">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Confirmar contraseña</label>
                    							<input class="form-control" type="password" name="panel_password_confirmation" required autocomplete="new-password" style="border-radius: 30px;">
                    						</div>
                    					</div>
                    				</div>

                    				<button type="submit" class="btn btn-md btn-light mt-2" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder;">
                    					Crear administración <i class="ri-save-line ms-1"></i>
                    				</button>
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
	// Sincronizar el campo web visible con el campo oculto del formulario
	document.getElementById('web-field').addEventListener('input', function() {
	    document.getElementById('web-hidden').value = this.value;
	});

	document.querySelector('form').addEventListener('submit', function() {
	    localStorage.removeItem('administration_form_data');
	});
</script>

@endsection