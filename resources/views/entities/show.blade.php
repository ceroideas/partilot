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

                    		<div class="form-card show-content mb-3 bs">
                    			<h4 class="mb-0 mt-1">
                    				Estado Entidad
                    			</h4>
                    			<small><i>Bloquea o desbloquea la entidad</i></small>

                    			<div class="form-group mt-2">
	                    			<label class="">Estado Actual</label> <label class="badge badge-lg bg-success float-end">Activo</label>
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

			                    				<a href="{{url('entities/edit/1')}}" class="btn btn-light float-end" style="border: 1px solid silver; border-radius: 30px;"> 
			                    				<img src="{{url('assets/form-groups/edit.svg')}}" alt="">
			                    				Editar</a>
			                    			</h4>
			                    			<small><i>Todos los campos son obligatorios</i></small>

			                    			<div class="form-group mt-2 mb-3">

			                    				<div class="row">
			                    					<div class="col-1">
			                    						
					                    				<div class="photo-preview-3">
					                    					
					                    					@if($entity->image)
					                    						<img src="{{ url('uploads/' . $entity->image) }}" alt="Imagen de la entidad" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
					                    					@else
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
			                    						
					                    				<div class="photo-preview-2">
					                    					
					                    					@if($entity->manager && $entity->manager->image)
					                    						<img src="{{ url('manager/' . $entity->manager->image) }}" alt="Imagen del gestor" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
					                    					@else
					                    						<i class="ri-account-circle-fill"></i>
					                    					@endif

					                    				</div>
					                    				
					                    				<div style="clear: both;"></div>
			                    					</div>

			                    					<div class="col-4 text-center">

			                    						<h4 class="mt-0 mb-0">{{ $entity->name ?? 'Sin gestor' }}</h4>

			                    						<small>{{ $entity->manager ? $entity->manager->name . ' ' . $entity->manager->last_name : 'Sin gestor asignado' }}</small> <br>

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

							                                    <input readonly="" value="{{ $entity->manager->name ?? '' }}" class="form-control" type="text" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->last_name ?? '' }}" class="form-control" type="text" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->last_name2 ?? '' }}" class="form-control" type="text" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->nif_cif ?? '' }}" class="form-control" type="text" placeholder="B26262626" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->birthday ?? '' }}" class="form-control" type="date" placeholder="01/01/1990" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->email ?? '' }}" class="form-control" type="email" placeholder="ejemplo@cuentaemail.com" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $entity->manager->phone ?? '' }}" class="form-control" type="phone" placeholder="940 200 200" style="border-radius: 0 30px 30px 0;">
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

						                                    <textarea readonly="" class="form-control" placeholder="Añade tu comentario" name="" id="" rows="6">{{ $entity->manager->comment ?? '' }}</textarea>
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
							                            <tr>
							                                <td>#GE9801</td>
							                                <td>Alberto García Montes</td>
							                                <td>Administrador</td>
							                                <td>Total</td>
							                                <td><label class="badge bg-success">Activo</label></td>
							                                <td>
							                                	<a class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></a>
							                                </td>
							                            </tr>
							                            <tr>
							                                <td>#GE9802</td>
							                                <td>María Molina Jimenez</td>
							                                <td>Gestor</td>
							                                <td>Total</td>
							                                <td><label class="badge bg-danger">Bloqueado</label></td>
							                                <td>
							                                	<a class="btn btn-sm btn-danger"><i class="ri-delete-bin-6-line"></i></a>
							                                </td>
							                            </tr>
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
				                    						
						                    				<div class="photo-preview-3">
						                    					
						                    					<i class="ri-account-circle-fill"></i>

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

			                    							<div class="d-none" id="invite-form">

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
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="all" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="all"><b>
																						Todos los permisos
																					</b></label>
																				</div>

																				</div>

																				<hr>

																				<div style="width: 50%; margin: auto;" class="text-start">

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="sellers" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="sellers"><b>
																						Administrar Vendedores
																					</b></label>
																				</div>

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="design" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="design"><b>
																						Diseñar Participaciones
																					</b></label>
																				</div>

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="total" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="total"><b>
																						Estadísticas Totales
																					</b></label>
																				</div>

																				<div class="form-check form-switch mt-2 mb-2">
																					<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="pay" checked>
																					<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="pay"><b>
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

                    						<div class="form-card bs" style="min-height: 658px;">

                    							<h4 class="mb-0 mt-1">
				                    				Email de Invitación
				                    			</h4>
				                    			<small><i>Asegúrese de que el email sea el correcto</i></small>

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
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="sellers" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="sellers"><b>
																				Administrar Vendedores
																			</b></label>
																		</div>

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="design" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="design"><b>
																				Diseñar Participaciones
																			</b></label>
																		</div>

																	</div>

																	<div class="text-start col-6">

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="total" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="total"><b>
																				Estadísticas Totales
																			</b></label>
																		</div>

																		<div class="form-check form-switch mt-2 mb-2">
																			<input class="form-check-input bg-dark" style="float: right;" type="checkbox" role="switch" id="pay" checked>
																			<label class="form-check-label" style="float: right; margin-right: 50px; width: 100%; padding-left: 16px;" for="pay"><b>
																				Pagar Participaciones
																			</b></label>
																		</div>
																	</div>

																</div>

			            									</div>
			            								</div>
			    									</div>

				                    				<div class="col-4 text-end">
				                    					<button style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2 save-manager">Guardar
				                    						<i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
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

</script>

@endsection