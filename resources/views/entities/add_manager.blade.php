@extends('layouts.layout')

@section('title','Entidades')

@section('content')

<style>
	.form-check-input:checked {
		border-color: #333;
	}
</style>

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
            		<h4 class="header-title">Acceso al panel</h4>
                    <p class="text-muted">El inicio de sesión en el panel web usará el <strong>email de contacto de la entidad</strong> indicado en el paso anterior.</p>
                    <br>

                    <div class="row">
                    	<div class="col-md-3" style="position: relative;">
                    		<div class="form-card bs mb-3">
                    			<div class="form-wizard-element">
                    				<span>1</span>
                    				<img src="{{ url('assets/admin.svg') }}" alt="">
                    				<label>Selec. Administración</label>
                    			</div>
                    			<div class="form-wizard-element">
                    				<span>2</span>
                    				<img src="{{ url('assets/entidad.svg') }}" alt="">
                    				<label>Datos Entidad</label>
                    			</div>
                    			<div class="form-wizard-element active">
                    				<span>3</span>
                    				<img src="{{url('assets/gestor.svg')}}" alt="">
                    				<label>Acceso panel</label>
                    			</div>
                    		</div>

                    		<div class="form-card">
                    			<div class="row">
                					<div class="col-4">
                						@php
                							$adminImg = data_get(session('selected_administration'), 'image');
                							$entInfo = session('entity_information');
                							$entImg = is_array($entInfo) ? ($entInfo['image'] ?? null) : null;
                						@endphp
                						@if($entImg ?? null)
                							<div class="photo-preview-3 logo-round" style="background-image: url('{{ asset('uploads/' . $entImg) }}'); background-size: cover;"></div>
                						@else
                							<div class="photo-preview-3"><i class="ri-account-circle-fill"></i></div>
                						@endif
                					</div>
                					<div class="col-8 text-center mt-2">
                						<h4 class="mt-0 mb-0">{{ session('entity_information.name') ?? 'Entidad' }}</h4>
                						<small class="d-block text-muted">Email acceso: {{ session('entity_information.email') ?? '—' }}</small>
                					</div>
                				</div>
                    		</div>

                    		<a href="{{ route('entities.add-information') }}" class="btn btn-md btn-light mt-2" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder;">
                    			<i class="ri-arrow-left-circle-line"></i> Atrás
                    		</a>
                    	</div>

                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 400px;">
                    			<form action="{{ url('entities/store-manager') }}" method="POST">
                    				@csrf
                    				@if ($errors->any())
                    					<div class="alert alert-danger mb-3">
                    						<ul class="mb-0">
                    							@foreach ($errors->all() as $error)
                    								<li>{{ $error }}</li>
                    							@endforeach
                    						</ul>
                    					</div>
                    				@endif

                    				<div class="alert alert-info">
                    					<strong>Usuario del panel:</strong> {{ session('entity_information.email') ?? '—' }}<br>
                    					Defina la contraseña para esta cuenta (mínimo 8 caracteres).
                    				</div>

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

                    				<button type="submit" class="btn btn-md btn-light mt-3" style="border-radius: 30px; background-color: #e78307; color: #333; padding: 10px 24px; font-weight: bolder;">
                    					Crear entidad <i class="ri-save-line ms-1"></i>
                    				</button>
                    			</form>
                    		</div>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
