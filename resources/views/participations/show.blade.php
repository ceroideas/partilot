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
	                    			<label class="">Estado Actual</label> 
	                    			@if($participation->status == 'vendida')
	                    				<label class="badge badge-lg bg-success float-end">Vendida</label>
	                    			@elseif($participation->status == 'disponible')
	                    				<label class="badge badge-lg bg-info float-end">Disponible</label>
	                    			@elseif($participation->status == 'devuelta')
	                    				<label class="badge badge-lg bg-danger float-end">Devuelta</label>
	                    			@else
	                    				<label class="badge badge-lg bg-warning float-end">{{ ucfirst($participation->status ?? 'Sin estado') }}</label>
	                    			@endif
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

                						<h3 class="mt-2 mb-0">{{ $participation->set && $participation->set->reserve && $participation->set->reserve->entity ? $participation->set->reserve->entity->name : 'Sin entidad' }}</h3>

                						<i style="position: relative; top: 3px; font-size: 16px; color: #333" class="ri-computer-line"></i> {{ $participation->set && $participation->set->reserve && $participation->set->reserve->entity ? ($participation->set->reserve->entity->province ?? 'Sin provincia') : 'Sin provincia' }}
                						
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
			                    			
			                    			<br>

			                    			<div>
			                    				<div class="row">

			                    					<div class="col-2">

			                    						<img src="{{url('assets/participacion.png')}}" alt="" width="150px">
			                    						
			                    					</div>
			                    					
			                    					<div class="col-2">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Número de Sorteo</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->set && $participation->set->reserve && $participation->set->reserve->lottery ? $participation->set->reserve->lottery->name : 'N/A' }}" class="form-control" type="text" placeholder="46/25" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-2">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Fecha Sorteo</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->set && $participation->set->reserve && $participation->set->reserve->lottery && $participation->set->reserve->lottery->draw_date ? \Carbon\Carbon::parse($participation->set->reserve->lottery->draw_date)->format('Y-m-d') : 'N/A' }}" class="form-control" type="date" placeholder="01/01/2025" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-6">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Número/s Jugado/s</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->set && $participation->set->reserve && $participation->set->reserve->reservation_numbers ? implode(' - ', $participation->set->reserve->reservation_numbers) : 'N/A' }}" class="form-control" type="text" placeholder="0000" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-2">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Importe Jugado (por Número)</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->set && $participation->set->reserve && $participation->set->reserve->lottery ? number_format($participation->set->reserve->lottery->ticket_price, 2) . '€' : 'N/A' }}" class="form-control" type="text" placeholder="6,00€" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-2">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Importe <br> Donativo</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->set ? number_format($participation->set->donation_amount, 2) . '€' : 'N/A' }}" class="form-control" type="text" placeholder="6,00€" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-2">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Importe Total Participación</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->set ? number_format($participation->set->total_amount, 2) . '€' : 'N/A' }}" class="form-control" type="text" placeholder="6,00€" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-2">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Número <br> Participación</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/22.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $participation->participation_code ?? 'N/A' }}" class="form-control" type="text" placeholder="1/0001" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>

			                    					<div class="col-4">
			                    						<div class="form-group mt-3 mb-3">
			                    							<label class="label-control">Número Seguridad <br> Participación</label>

							                    			<div class="input-group input-group-merge group-form">

							                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
							                                        <img src="{{url('assets/form-groups/admin/21.svg')}}" alt="">
							                                    </div>

							                                    <input readonly="" value="{{ $ticketReference ?? 'N/A' }}" class="form-control" type="text" placeholder="12345678901234567890" style="border-radius: 0 30px 30px 0;">
							                                </div>
						                    			</div>
			                    					</div>
			                    				</div>
			                    			</div>

			                    			<h4 class="mb-0 mt-1">
			                    				Historial Participación
			                    			</h4>
			                    			<small><i>Historial</i></small>

			                    			<table id="" class="table table-striped table-condensed table nowrap w-100 mb-0">

			                    				<thead>
			                    					<tr>
			                    						<th>Fecha Movimiento</th>
			                    						<th>Hora Movimiento</th>
			                    						<th>Concepto Movimiento</th>
			                    						<th>Usuario</th>
			                    						<th>Rol</th>
			                    						<th>Status</th>
			                    					</tr>
			                    				</thead>

			                    				<tbody>
			                    					<tr>
			                    						<td>{{ $participation->created_at ? \Carbon\Carbon::parse($participation->created_at)->format('d/m/Y') : 'N/A' }}</td>
			                    						<td>{{ $participation->created_at ? \Carbon\Carbon::parse($participation->created_at)->format('H:i') . 'h' : 'N/A' }}</td>
			                    						<td>Participación Creada</td>
			                    						<td>Sistema</td>
			                    						<td>Sistema</td>
			                    						<td><label class="badge bg-primary">Creada</label></td>
			                    					</tr>

			                    					@if($participation->seller && $participation->seller->user)
			                    					<tr>
			                    						<td>{{ $participation->assigned_at ? \Carbon\Carbon::parse($participation->assigned_at)->format('d/m/Y') : ($participation->sale_date ? \Carbon\Carbon::parse($participation->sale_date)->format('d/m/Y') : 'N/A') }}</td>
			                    						<td>{{ $participation->assigned_at ? \Carbon\Carbon::parse($participation->assigned_at)->format('H:i') . 'h' : ($participation->sale_time ?? 'N/A') }}</td>
			                    						<td>Asignada al Vendedor</td>
			                    						<td>{{ $participation->seller->user->name ?? 'N/A' }}</td>
			                    						<td>Vendedor</td>
			                    						<td><label class="badge bg-warning">Asignada</label></td>
			                    					</tr>
			                    					@endif

			                    					@if($participation->status == 'vendida')
			                    					<tr>
			                    						<td>{{ $participation->sale_date ? \Carbon\Carbon::parse($participation->sale_date)->format('d/m/Y') : 'N/A' }}</td>
			                    						<td>{{ $participation->sale_time ?? 'N/A' }}</td>
			                    						<td>Venta Registrada</td>
			                    						<td>{{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'N/A' }}</td>
			                    						<td>Vendedor</td>
			                    						<td><label class="badge bg-success">Vendida</label></td>
			                    					</tr>
			                    					@endif

			                    					@if($participation->status == 'devuelta')
			                    					<tr>
			                    						<td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('d/m/Y') : 'N/A' }}</td>
			                    						<td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('H:i') . 'h' : 'N/A' }}</td>
			                    						<td>Participación Devuelta</td>
			                    						<td>{{ $participation->seller && $participation->seller->user ? $participation->seller->user->name : 'N/A' }}</td>
			                    						<td>Vendedor</td>
			                    						<td><label class="badge bg-danger">Devuelta</label></td>
			                    					</tr>
			                    					@endif

			                    					@if($participation->status == 'disponible')
			                    					<tr>
			                    						<td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('d/m/Y') : 'N/A' }}</td>
			                    						<td>{{ $participation->updated_at ? \Carbon\Carbon::parse($participation->updated_at)->format('H:i') . 'h' : 'N/A' }}</td>
			                    						<td>Disponible para Venta</td>
			                    						<td>Sistema</td>
			                    						<td>Sistema</td>
			                    						<td><label class="badge bg-info">Disponible</label></td>
			                    					</tr>
			                    					@endif
			                    				</tbody>
			                    				
			                    			</table>

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

							                                    <input readonly="" value="{{ $participation->seller && $participation->seller->user ? explode(' ', $participation->seller->user->name)[0] ?? 'N/A' : 'Sin asignar' }}" class="form-control" type="text" placeholder="Nombre" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $participation->seller && $participation->seller->user ? (explode(' ', $participation->seller->user->name)[1] ?? 'N/A') : 'Sin asignar' }}" class="form-control" type="text" placeholder="Primer Apellido" style="border-radius: 0 30px 30px 0;">
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

							                                    <input readonly="" value="{{ $participation->seller && $participation->seller->user ? (explode(' ', $participation->seller->user->name)[2] ?? 'N/A') : 'Sin asignar' }}" class="form-control" type="text" placeholder="Segundo Apellido" style="border-radius: 0 30px 30px 0;">
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