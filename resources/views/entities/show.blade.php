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
                        <li class="breadcrumb-item active">Entidad</li>
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

                    <div class="row">
                    	
                    	<div class="col-md-3" style="position: relative;">

                    		<ul class="form-card bs mb-3 nav">

                    			<li class="nav-item">
	                    			<div class="form-wizard-element active" data-bs-toggle="tab" data-bs-target="#datos_legales">
	                    				
	                    				<span>
	                    					&nbsp;&nbsp;
	                    				</span>

	                    				<img src="{{url('assets/entidad.svg')}}" alt="">

	                    				<label>
	                    					Datos Entidad
	                    				</label>

	                    			</div>
                    			</li>

	                    		<li class="nav-item">
	                    			<div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#datos_contacto">
	                    				
	                    				<span>
	                    					&nbsp;&nbsp;
	                    				</span>

	                    				<img src="{{url('assets/gestor.svg')}}" alt="">

	                    				<label>
	                    					Datos Gestor
	                    				</label>

	                    			</div>

	                    		</li>

	                    		<li class="nav-item">

	                    			<div class="form-wizard-element" data-bs-toggle="tab" data-bs-target="#gestores">
	                    				
	                    				<span>
	                    					&nbsp;&nbsp;
	                    				</span>

	                    				<img src="{{url('assets/gestores.svg')}}" alt="">

	                    				<label>
	                    					Gestores
	                    				</label>

	                    			</div>
	                    		</li>
                    			
                    		</ul>

                    		<div class="form-card mb-3 bs">
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
	                    			<div class="input-group input-group-merge group-form">
	                    				<div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                    					<img src="{{url('assets/form-groups/admin/13.svg')}}" alt="">
	                    				</div>
	                    				<input class="form-control" type="text" value="{{ $statusText }}" id="entity-status-input" style="border-radius: 0 30px 0 0; border-bottom: 1px solid #dee2e6;" readonly>
	                    				<button type="button" class="btn btn-sm btn-outline-secondary" id="entity-toggle-status" title="Cambiar estado" style="border-radius: 0 30px 30px 0; border-left: none;">Cambiar</button>
	                    			</div>
	                    			<span class="badge badge-lg {{ $statusClass }} mt-2" id="entity-status-badge" style="display: none;">{{ $statusText }}</span>
	                    			<div style="clear: both;"></div>
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

                    		<a href="{{url('entities?table=1')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">

                    		<div class="tabbable show-content">
                    			
                    			<div class="tab-content p-0">
                    				
                    				<div class="tab-pane fade active show" id="datos_legales">

			                    		<div class="form-card show-content bs" style="min-height: 658px;">
			                    			<h4 class="mb-0 mt-1">
			                    				Datos legales de la entidad

			                    				<a href="{{url('entities/edit',$entity->id)}}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    				Editar</a>
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>

			                    			<div class="form-group mt-2 mb-3">

			                    				<div class="row">
			                    					<div class="col-1">
			                    						
					                    				<div class="photo-preview-3 logo-round" @if($entity->image) style="background-image: url('{{ asset('uploads/' . $entity->image) }}');" @endif>
					                    					@if(!$entity->image)
					                    						<i class="ri-account-circle-fill"></i>
					                    					@endif
					                    				</div>
					                    				
					                    				<div style="clear: both;"></div>
			                    					</div>

			                    					<div class="col-4 text-center mt-3">

			                    						<h4 class="mt-0 mb-0">{{ $entity->name ?? 'Sin nombre' }}</h4>

			                    						<small>{{ $entity->province ?? 'Sin provincia' }}</small> <br>
			                    						
			                    					</div>
			                    				</div>
			                    				
			                    				<div style="clear: both;"></div>
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

							                                    <input class="form-control" readonly value="{{ $entity->name ?? '' }}" type="text" placeholder="Nombre Entidad" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->province ?? '' }}" type="text" placeholder="Provincia" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->city ?? '' }}" type="text" placeholder="Localidad" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->postal_code ?? '' }}" type="number" placeholder="C.P." style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->address ?? '' }}" type="text" placeholder="Dirección" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->nif_cif ?? '' }}" type="text" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->phone ?? '' }}" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" readonly value="{{ $entity->email ?? '' }}" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

						                                    <textarea readonly="" class="form-control" placeholder="Añade tu comentario" name="" id="" rows="6">{{ $entity->comments ?? '' }}</textarea>
						                                </div>
					                    			</div>

			                    				</div>

			                    				{{-- <div class="col-12 text-end">
			                    					<a href="{{url('entities/add/manager')}}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Siguiente
			                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i></a>
			                    				</div> --}}

			                    			</div>

			                    		</div>

			                    	</div>

			                    	<div class="tab-pane fade" id="datos_contacto">
                    					<div class="form-card bs" style="min-height: 658px;">
			                    			<h4 class="mb-0 mt-1">
			                    				Datos de gestor

			                    				<a href="{{ route('entities.edit-manager', $entity->id) }}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    				Editar</a>
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>
			                    			<div style="clear: both;"></div>

			                    			<div class="form-group mt-2 mb-3 admin-box">

			                    				<div class="row">
			                    					<div class="col-1">
			                    						
					                    				<div class="photo-preview-2 logo-round" @if($entity->image) style="background-image: url('{{ asset('uploads/' . $entity->image) }}');" @endif>
					                    					@if(!$entity->image)
					                    						<i class="ri-account-circle-fill"></i>
					                    					@endif
					                    				</div>
					                    				
					                    				<div style="clear: both;"></div>
			                    					</div>

			                    					<div class="col-4 text-center">

			                    						<h4 class="mt-0 mb-0">{{ $entity->name ?? 'Sin gestor' }}</h4>

			                    						<small>{{ $entity->manager ? $entity->manager->user->name . ' ' . $entity->manager->user->last_name : 'Sin gestor asignado' }}</small> <br>

			                    						@if($entity->manager)
			                    							<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{ $entity->administration->receiving ?? '' }}
			                    						@endif
			                    						
			                    					</div>

			                    					<div class="col-4">

			                    						<div class="mt-2">
			                    							@if($entity->manager)
			                    								Provincia: {{ $entity->province ?? 'No especificada' }} <br>
			                    								Dirección: {{ $entity->address ?? 'No especificada' }}
			                    							@else
			                    								Sin gestor asignado
			                    							@endif
			                    						</div>
			                    						
			                    					</div>

			                    					<div class="col-3">

			                    						<div class="mt-2">
			                    							@if($entity->manager)
			                    								Ciudad: {{ $entity->city ?? 'No especificada' }} <br>
			                    								Tel: {{ $entity->phone ?? 'No especificado' }}
			                    							@else
			                    								Sin gestor asignado
			                    							@endif
			                    						</div>
			                    						
			                    					</div>
			                    				</div>
			                    			</div>

			                    			
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

							                                    <input readonly="" value="{{ $entity->manager->user->name ?? '' }}" class="form-control" type="text" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->user->last_name ?? '' }}" class="form-control" type="text" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->user->last_name2 ?? '' }}" class="form-control" type="text" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->user->nif_cif ?? '' }}" class="form-control" type="text" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->user->birthday->format('Y-m-d') ?? '' }}" class="form-control" type="date" placeholder="01/01/1990" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->user->email ?? '' }}" class="form-control" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->user->phone ?? '' }}" class="form-control" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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

						                                    <textarea readonly="" class="form-control" placeholder="Añade tu comentario" name="" id="" rows="6">{{ $entity->manager->user->comment ?? '' }}</textarea>
						                                </div>
					                    			</div>

			                    				</div>

			                    				<div class="col-4 text-end">
			                    					
			                    				</div>

			                    			</div>

			                    		</div>
                    				</div>

                    				<div class="tab-pane fade" id="gestores">

                    					<div id="all-managers">
	                    					<div class="form-card bs" style="min-height: 658px;">
				                    			<h4 class="mb-0 mt-1">
				                    				Administración de gestores

				                    				<button style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end add-manager"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</button>
				                    			</h4>
				                    			<small><i>Todos los campos son obligatorios</i></small>
				                    			<div style="clear: both;"></div>

				                    			<table id="example2" class="table table-striped nowrap w-100">
						                            <thead class="filters">
							                            <tr>
							                                <th>Order ID</th>
							                                <th>Gestor</th>
							                                <th>Tipo</th>
							                                <th>Acceso</th>
							                                <th>Status</th>
							                                <th></th>
							                            </tr>
							                        </thead>
							                    
							                    
							                        <tbody>
							                            @forelse($entity->managers as $manager)
							                            <tr>
							                                <td>#GE{{ str_pad($manager->id, 4, '0', STR_PAD_LEFT) }}</td>
							                                <td>{{ $manager->user->name ?? '' }} {{ $manager->user->last_name ?? '' }}</td>
							                                <td>
							                                	@if($manager->is_primary)
							                                		Administrador
							                                	@else
							                                		Gestor
							                                	@endif
							                                </td>
							                                <td>
							                                	@if($manager->is_primary)
							                                		Total
							                                	@else
							                                		@php
							                                			$allPermissions = $manager->permission_sellers && 
							                                								$manager->permission_design && 
							                                								$manager->permission_statistics && 
							                                								$manager->permission_payments;
							                                		@endphp
							                                		@if($allPermissions)
							                                			Total
							                                		@else
							                                			Parcial
							                                		@endif
							                                	@endif
							                                </td>
							                                <td>
							                                	@php
							                                		$status = $manager->status;
							                                		if ($status === null || $status == -1) {
							                                			$statusText = 'Pendiente';
							                                			$statusClass = 'bg-secondary';
							                                			$newStatus = 1; // Cambiar a Activo
							                                			$newStatusText = 'Activar';
							                                			$newStatusIcon = 'ri-check-line';
							                                			$newStatusBtnClass = 'btn-success';
							                                		} elseif ($status == 1) {
							                                			$statusText = 'Activo';
							                                			$statusClass = 'bg-success';
							                                			$newStatus = 0; // Cambiar a Inactivo
							                                			$newStatusText = 'Desactivar';
							                                			$newStatusIcon = 'ri-close-line';
							                                			$newStatusBtnClass = 'btn-danger';
							                                		} else {
							                                			$statusText = 'Inactivo';
							                                			$statusClass = 'bg-danger';
							                                			$newStatus = 1; // Cambiar a Activo
							                                			$newStatusText = 'Activar';
							                                			$newStatusIcon = 'ri-check-line';
							                                			$newStatusBtnClass = 'btn-success';
							                                		}
							                                	@endphp
							                                	<label class="badge {{ $statusClass }}">{{ $statusText }}</label>
							                                	@if(!$manager->is_primary)
							                                		<button class="btn btn-sm {{ $newStatusBtnClass }} toggle-manager-status ms-2" 
							                                		        data-manager-id="{{ $manager->id }}" 
							                                		        data-new-status="{{ $newStatus }}"
							                                		        title="{{ $newStatusText }}">
							                                			<i class="{{ $newStatusIcon }}"></i>
							                                		</button>
							                                	@endif
							                                </td>
							                                <td>
							                                	@if(!$manager->is_primary)
							                                		<form action="{{ route('entities.set-primary-manager') }}" method="POST" class="d-inline" onsubmit="return confirm('¿Asignar a este gestor como principal? El actual principal pasará a ser gestor secundario.');">
							                                			@csrf
							                                			<input type="hidden" name="entity_id" value="{{ $entity->id }}">
							                                			<input type="hidden" name="new_primary_manager_id" value="{{ $manager->id }}">
							                                			<button type="submit" class="btn btn-sm btn-outline-primary" title="Hacer gestor principal"><i class="ri-user-star-line"></i> Principal</button>
							                                		</form>
							                                		<a href="{{ route('entities.edit-manager-permissions', ['entity_id' => $entity->id, 'manager_id' => $manager->id]) }}" class="btn btn-sm btn-warning" title="Editar permisos"><i class="ri-settings-3-line"></i></a>
							                                		<a href="#" class="btn btn-sm btn-danger delete-manager" data-manager-id="{{ $manager->id }}" title="Eliminar"><i class="ri-delete-bin-6-line"></i></a>
							                                	@else
							                                		@if($entity->managers->where('is_primary', false)->count() > 0)
							                                			<form action="{{ route('entities.set-primary-manager') }}" method="POST" class="d-inline" id="change-primary-form-{{ $manager->id }}" onsubmit="return validatePrimaryChange(event, {{ $manager->id }});">
							                                				@csrf
							                                				<input type="hidden" name="entity_id" value="{{ $entity->id }}">
							                                				<select name="new_primary_manager_id" class="form-select form-select-sm d-inline-block primary-manager-select" style="width: auto;" required>
							                                					<option value="">-- Seleccione nuevo principal --</option>
							                                					@foreach($entity->managers->where('is_primary', false) as $other)
							                                						<option value="{{ $other->id }}">{{ $other->user->name ?? '' }} {{ $other->user->last_name ?? '' }}</option>
							                                					@endforeach
							                                				</select>
							                                				<button type="submit" class="btn btn-sm btn-outline-secondary" id="change-primary-btn-{{ $manager->id }}" disabled>Cambiar</button>
							                                			</form>
							                                		@else
							                                			<span class="text-muted" title="No hay otros gestores disponibles para asignar como principal">-</span>
							                                		@endif
							                                	@endif
							                                </td>
							                            </tr>
							                            @empty
							                            <tr>
							                                <td colspan="6" class="text-center">No hay gestores asignados</td>
							                            </tr>
							                            @endforelse
							                        </tbody>
						                        </table>
						                    </div>
                    					</div>

                    					<div id="all-options" class="d-none">

                    						<div class="form-card bs" style="min-height: 658px;">

	                    						<h4 class="mb-0 mt-1">
				                    				Invitación/Registro
				                    			</h4>
				                    			<small><i>Elija la manera en la que agregar al gestor</i></small>

				                    			<div class="form-group mt-2 mb-3 admin-box">

				                    				<div class="row">
				                    					<div class="col-1">
				                    						
						                    				<div class="photo-preview-3 logo-round" @if($entity->image) style="background-image: url('{{ asset('uploads/' . $entity->image) }}');" @endif>
						                    					@if(!$entity->image)
						                    						<i class="ri-account-circle-fill"></i>
						                    					@endif
						                    				</div>
						                    				
						                    				<div style="clear: both;"></div>
				                    					</div>

				                    					<div class="col-4 text-center mt-3">

				                    						<h4 class="mt-0 mb-0">{{ $entity->name ?? 'Sin nombre' }}</h4>

				                    						<small>{{ $entity->province ?? 'Sin provincia' }}</small> <br>
				                    						
				                    					</div>

				                    					<div class="col-4">

				                    						<div class="mt-3">
				                    							Provincia: {{ $entity->province ?? 'No especificada' }} <br>
				                    							Dirección: {{ $entity->address ?? 'No especificada' }}
				                    						</div>
				                    						
				                    					</div>

				                    					<div class="col-3">

				                    						<div class="mt-3">
				                    							Ciudad: {{ $entity->city ?? 'No especificada' }} <br>
				                    							Tel: {{ $entity->phone ?? 'No especificado' }}
				                    						</div>
				                    						
				                    					</div>
				                    				</div>
				                    			</div>

			                    				<div class="row">
			                    					
			                    					<div class="col-12">
			                    						
			                    						<div class="mt-4 text-center">

			                    							<div class="" id="manager-buttons">

				                    							<button class="btn btn-light btn-xl text-center m-2 bs" id="invite-manager" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px;">
				                    								<img class="mt-2" src="{{url('assets/invite.svg')}}" alt="">
				                    								<h4 class="mb-0">Invitar <br> GESTOR</h4>
				                    							</button>

				                    							<button class="btn btn-light btn-xl text-center m-2 bs" id="register-manager" style="border: 1px solid #f0f0f0; padding: 16px; width: 150px; border-radius: 16px;">
				                    								<img class="mt-2" src="{{url('assets/register.svg')}}" alt="">
				                    								<h4 class="mb-0">Registrar <br> GESTOR</h4>
				                    							</button>

			                    							</div>

			                    							<form action="{{ route('entities.invite-manager') }}" method="POST" id="invite-form" class="d-none">
			                    								@csrf
			                    								<input type="hidden" name="entity_id" value="{{ $entity->id }}">
			                    								<input type="hidden" name="user_id" id="invite-user-id">
			                    								<div class="row">
			                    									
			                    									<div class="col-7">
					                    								<div class="card bs" style="border-radius: 16px;">
					                    									<div class="card-body">
					                    										<h4 class="mb-0 mt-1">
												                    				Permisos Gestor
												                    			</h4>
												                    			<small><i>Puedes modificar los permisos en cualquier momento</i></small>

												                    			<br>

												                    			<div style="width: 50%; margin: auto;" class="text-start">

												                    			<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="invite-all" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="invite-all"><b>
																						Todos los permisos
																					</b></label>
																				</div>

																				</div>

																				<hr>

																				<div style="width: 50%; margin: auto;" class="text-start">

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_sellers" id="invite-sellers" value="1" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="invite-sellers"><b>
																						Administrar Vendedores
																					</b></label>
																				</div>

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_design" id="invite-design" value="1" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="invite-design"><b>
																						Diseñar Participaciones
																					</b></label>
																				</div>

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_statistics" id="invite-total" value="1" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="invite-total"><b>
																						Estadísticas Totales
																					</b></label>
																				</div>

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_payments" id="invite-pay" value="1" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="invite-pay"><b>
																						Pagar Participaciones
																					</b></label>
																				</div>

																				</div>

					                    									</div>
					                    								</div>
			                    									</div>
			                    									<div class="col-5">
					                    								<div class="card bs" style="border-radius: 16px;">
					                    									
					                    									<div class="card-body">

					                    										<h4 class="mt-0"><b>¡Invitar Usuario!</b></h4>

					                    										<br>

					                    										<div class="input-group input-group-merge group-form" style="border: none;">

												                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
												                                        <img src="{{url('assets/form-groups/admin/9.svg')}}" alt="">
												                                    </div>

												                                    <input class="form-control invite-email" type="email" name="invite_email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
												                                </div>

												                                <button type="submit" disabled style="border-radius: 30px; width: 100%; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-3" id="invite-button">Invitar</button>
					                    										
					                    									</div>

					                    								</div>
			                    									</div>
			                    								</div>
			                    							</form>



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
				                    										<a href="javascript:;" style="border-radius: 30px; width: 100%; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-3 return-list">Aceptar</a>
				                    									</div>
				                    								</div>
			                    								</div>
			                    							</div>
			                    							
			                    						</div>


			                    					</div>

			                    				</div>

			                    			</div>
		                    			</div>

                    					<div id="register-manager-form" class="d-none">

                    						<form action="{{ route('entities.register-manager', $entity->id) }}" method="POST" enctype="multipart/form-data">
                    							@csrf
                    						<div class="form-card bs" style="min-height: 658px;">

                    							<h4 class="mb-0 mt-1">
				                    				Email de Invitación
				                    			</h4>
				                    			<small><i>Asegúrese de que el email sea el correcto</i></small>

				                    			<div class="form-group mt-2 mb-3 admin-box">

				                    				<div class="row">
				                    					<div class="col-1">
				                    						
						                    				<div class="photo-preview-3 logo-round" @if($entity->image) style="background-image: url('{{ asset('uploads/' . $entity->image) }}');" @endif>
						                    					@if(!$entity->image)
						                    						<i class="ri-account-circle-fill"></i>
						                    					@endif
						                    				</div>
						                    				
						                    				<div style="clear: both;"></div>
				                    					</div>

				                    					<div class="col-4 text-center mt-3">

				                    						<h4 class="mt-0 mb-0">{{ $entity->name ?? 'Sin nombre' }}</h4>

				                    						<small>{{ $entity->province ?? 'Sin provincia' }}</small> <br>
				                    						
				                    					</div>

				                    					<div class="col-4">

				                    						<div class="mt-3">
				                    							Provincia: {{ $entity->province ?? 'No especificada' }} <br>
				                    							Dirección: {{ $entity->address ?? 'No especificada' }}
				                    						</div>
				                    						
				                    					</div>

				                    					<div class="col-3">

				                    						<div class="mt-3">
				                    							Ciudad: {{ $entity->city ?? 'No especificada' }} <br>
				                    							Tel: {{ $entity->phone ?? 'No especificado' }}
				                    						</div>
				                    						
				                    					</div>
				                    				</div>
				                    			</div>

			                    				<div class="row">
			                    					
			                    					<div class="col-4">
			                    						<div class="form-group mt-2 mb-3">
			                    							<label class="label-control">Nombre</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/11.svg')}}" alt="">
							                                    </div>

							                                    <input class="form-control" type="text" name="manager_name" placeholder="Nombre" required style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" type="text" name="manager_last_name" placeholder="Primer Apellido" required style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" type="text" name="manager_last_name2" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" type="text" name="manager_nif_cif" id="entity-register-manager-nif-cif" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" type="date" name="manager_birthday" placeholder="01/01/1990" required style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" type="email" name="manager_email" placeholder="ejemplo@cuentaemail.com" required style="border-radius: 0 30px 30px 0;">
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

							                                    <input class="form-control" type="text" name="manager_phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    				</div>

			                    				<div class="row">

			                    					<div class="col-8">
			            								<div class="card bs m-0" style="border-radius: 16px;">
			            									<div class="card-body">
			            										<h4 class="mb-0 mt-1">
								                    				Permisos Gestor
								                    			</h4>
								                    			<small><i>Puedes modificar los permisos en cualquier momento</i></small>

								                    			<br>

								                    			<div class="row">
								                    				
									                    			<div class="text-start col-6">

										                    			<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="all" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="all"><b>
																				Todos los permisos
																			</b></label>
																		</div>

																	</div>
								                    			</div>

																<hr>

																<div class="row">
																	
																	<div class="text-start col-6">

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_sellers" id="register-sellers" value="1" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="register-sellers"><b>
																				Administrar Vendedores
																			</b></label>
																		</div>

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_design" id="register-design" value="1" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="register-design"><b>
																				Diseñar Participaciones
																			</b></label>
																		</div>

																	</div>

																	<div class="text-start col-6">

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_statistics" id="register-total" value="1" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="register-total"><b>
																				Estadísticas Totales
																			</b></label>
																		</div>

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" name="permission_payments" id="register-pay" value="1" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="register-pay"><b>
																				Pagar Participaciones
																			</b></label>
																		</div>
																	</div>

																</div>

			            									</div>
			            								</div>
			    									</div>

				                    				<div class="col-4 text-end">
				                    					<button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
				                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
				                    				</div>

				                    			</div>

				                    		</div>
		                    				
		                    			</div>
					                </div>
					                </form>
					                </form>
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
	
	$('.add-manager').click(function (e) {
		e.preventDefault();

		$('#all-managers').addClass('d-none');
		$('#all-options').removeClass('d-none');
	});

	$('.save-manager').click(function (e) {
		e.preventDefault();

		$('#all-managers').removeClass('d-none');
		$('#register-manager-form').addClass('d-none');
	});

	/**/

$('#invite-manager').click(function (e) {
	e.preventDefault();

	$('#manager-buttons').addClass('d-none');

	$('#invite-form').removeClass('d-none');
});

$('.invite-email').keyup(function(event) {
	var email = $(this).val();
	if (email) {
		$('#invite-button').prop('disabled', false);
		// Verificar si el email existe
		$.ajax({
			url: '{{ route("entities.check-manager-email") }}',
			method: 'POST',
			data: {
				_token: '{{ csrf_token() }}',
				email: email
			},
			success: function(response) {
				if (response.exists) {
					$('#invite-user-id').val(response.user_id);
				} else {
					$('#invite-user-id').val('');
				}
			}
		});
	} else {
		$('#invite-button').prop('disabled', true);
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

	$('#register-manager-form').removeClass('d-none');

});

$('.return-list').click(function (e) {
	e.preventDefault();

	$('#all-options').addClass('d-none');
	$('#invite-form').addClass('d-none');
	$('#accept-invite').addClass('d-none');
	$('#register-manager-form').addClass('d-none');

	$('#manager-buttons').removeClass('d-none');
	$('#all-managers').removeClass('d-none');
});

// Cambiar status del manager
$(document).on('click', '.toggle-manager-status', function (e) {
	e.preventDefault();
	var managerId = $(this).data('manager-id');
	var newStatus = $(this).data('new-status');
	var button = $(this);
	
	$.ajax({
		url: '{{ url("entities/toggle-manager-status") }}',
		type: 'POST',
		data: {
			manager_id: managerId,
			status: newStatus,
			_token: '{{ csrf_token() }}'
		},
		success: function(response) {
			if (response.success) {
				// Recargar la página para actualizar el estado
				location.reload();
			} else {
				alert('Error al cambiar el status del gestor');
			}
		},
		error: function(xhr) {
			alert('Error al cambiar el status del gestor');
		}
	});
});

// Toggle estado entidad (AJAX)
document.getElementById('entity-toggle-status') && document.getElementById('entity-toggle-status').addEventListener('click', function() {
	var btn = this;
	var entityId = {{ $entity->id }};
	btn.disabled = true;
	fetch('{{ route("entities.toggle-status", $entity->id) }}', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
			'Accept': 'application/json'
		},
		body: JSON.stringify({})
	})
	.then(function(r) { return r.json(); })
	.then(function(data) {
		if (data.success) {
			var input = document.getElementById('entity-status-input');
			var badge = document.getElementById('entity-status-badge');
			input.value = data.status_text;
			badge.textContent = data.status_text;
			badge.className = 'badge badge-lg bg-' + data.status_class + ' mt-2';
		} else {
			alert('Error al cambiar el estado');
		}
	})
	.catch(function() { alert('Error al cambiar el estado'); })
	.finally(function() { btn.disabled = false; });
});

// Validación NIF/CIF en formulario Registrar gestor
if (typeof initSpanishDocumentValidation === 'function') {
    initSpanishDocumentValidation('entity-register-manager-nif-cif', { showMessage: true });
}

// Validar cambio de gestor principal
function validatePrimaryChange(event, managerId) {
    const form = document.getElementById('change-primary-form-' + managerId);
    const select = form.querySelector('.primary-manager-select');
    const selectedValue = select.value;
    
    if (!selectedValue || selectedValue === '') {
        event.preventDefault();
        alert('Debe seleccionar un gestor para asignar como principal. No puede quedar la entidad sin gestor principal.');
        return false;
    }
    
    const selectedText = select.options[select.selectedIndex].text;
    const confirmMessage = '¿Está seguro de cambiar el gestor principal?\n\n' +
                          'El gestor actual pasará a ser gestor secundario y podrá tener permisos restringidos.\n' +
                          'El nuevo gestor principal será: ' + selectedText + '\n\n' +
                          'Esta acción no se puede deshacer automáticamente.';
    
    if (!confirm(confirmMessage)) {
        event.preventDefault();
        return false;
    }
    
    return true;
}

// Habilitar/deshabilitar botón de cambiar según selección
document.querySelectorAll('.primary-manager-select').forEach(function(select) {
    const managerId = select.closest('form').id.replace('change-primary-form-', '');
    const btn = document.getElementById('change-primary-btn-' + managerId);
    
    select.addEventListener('change', function() {
        if (this.value && this.value !== '') {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    });
});

// Eliminar gestor
$(document).on('click', '.delete-manager', function(e) {
    e.preventDefault();
    
    const managerId = $(this).data('manager-id');
    const entityId = {{ $entity->id }};
    const managerRow = $(this).closest('tr');
    const managerName = managerRow.find('td').eq(1).text().trim() || 'Gestor';
    const isPrimary = managerRow.find('.badge').text().includes('Principal');
    
    let confirmMessage = '¿Está seguro de eliminar este gestor?\n\n';
    confirmMessage += 'Gestor: ' + managerName + '\n\n';
    
    if (isPrimary) {
        confirmMessage += '⚠️ ADVERTENCIA: Este gestor es el principal.\n';
        confirmMessage += 'Si es el único gestor disponible, no se podrá eliminar.\n\n';
    }
    
    confirmMessage += 'Esta acción eliminará la relación del gestor con la entidad, pero NO eliminará el usuario asociado.';
    
    if (!confirm(confirmMessage)) {
        return false;
    }
    
    // Construir URL usando la ruta de Laravel
    const deleteUrl = '{{ url("entities/destroy/manager") }}/' + entityId + '/' + managerId;
    
    // Crear formulario para enviar DELETE
    const form = $('<form>', {
        'method': 'POST',
        'action': deleteUrl
    });
    
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': '{{ csrf_token() }}'
    }));
    
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': 'DELETE'
    }));
    
    $('body').append(form);
    form.submit();
});

</script>

@endsection