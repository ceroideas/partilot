@extends('layouts.layout')

@section('title','Gestión Grupos')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Vendedores</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Gestión Grupos</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Gestión Grupos</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Vendedores/Grupos/Editar

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

                    				<img src="{{url('assets/entidad.svg')}}" alt="">

                    				<label>
                    					Dat. Grupo
                    				</label>

                    			</div>
                    			
                    		</div>

                    		<a href="{{route('groups.index')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                    						<i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                    	</div>
                    	<div class="col-md-9">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			
                    			<!-- Tarjeta de entidad seleccionada -->
                    			<div class="mb-3">
                    				<div class="form-card bs p-3">
                    					<div class="d-flex align-items-center">
                    						<img src="{{url('assets/entidad.svg')}}" alt="" width="40px" class="me-3">
                    						<div>
                    							<h5 class="mb-0">{{ $entity->name }}</h5>
                    							<small class="text-muted">{{ $entity->province ?? 'Sin provincia' }}</small>
                    						</div>
                    					</div>
                    				</div>
                    			</div>

                    			<form action="{{ route('groups.update', $group->id) }}" method="POST">
                    				@csrf
                    				@method('PUT')

                    				<div class="row">
                    					<div class="col-md-8">
                    						<div class="form-card bs mb-3">
                    							<h4 class="mb-0 mt-1">
                    								Grupo
                    							</h4>
                    							<small><i>Configura el grupo</i></small>

                    							<div class="row mt-3">
                    								<div class="col-12">
                    									<div class="form-group mt-2 mb-3">
                    										<label class="label-control">Nombre del Grupo</label>
                    										<div class="input-group input-group-merge group-form">
                    											<div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                    												<img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                    											</div>
                    											<input class="form-control" type="text" name="name" placeholder="Nombre del Grupo" style="border-radius: 0 30px 30px 0;" value="{{ old('name', $group->name) }}" required>
                    										</div>
                    										@error('name')
                    											<small class="text-danger">{{ $message }}</small>
                    										@enderror
                    									</div>
                    								</div>
                    							</div>

                    							<div class="row mt-3">
                    								<div class="col-12">
                    									<h5 class="mb-2">Vendedores Disponibles</h5>
                    									<div style="max-height: 400px; overflow-y: auto;">
                    										<table class="table table-striped">
                    											<thead>
                    												<tr>
                    													<th>ID</th>
                    													<th>Nombre</th>
                    													<th>Email</th>
                    													<th>Acción</th>
                    												</tr>
                    											</thead>
                    											<tbody>
                    												@foreach($sellers as $seller)
                    												@php
                    													$isSelected = $group->sellers->contains($seller->id);
                    												@endphp
                    												<tr>
                    													<td>#VE{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }}</td>
                    													<td>{{ $seller->full_name }}</td>
                    													<td>{{ $seller->display_email ?? 'N/A' }}</td>
                    													<td>
                    														@if($isSelected)
                    															<button type="button" class="btn btn-sm btn-light add-seller d-none" data-seller-id="{{ $seller->id }}" data-seller-name="{{ $seller->full_name }}" data-seller-email="{{ $seller->display_email ?? 'N/A' }}" style="border-radius: 30px; background-color: #e78307; color: #333; padding: 2px 12px; font-weight: bolder;">
                    																Añadir
                    															</button>
                    														@else
                    															<button type="button" class="btn btn-sm btn-light add-seller" data-seller-id="{{ $seller->id }}" data-seller-name="{{ $seller->full_name }}" data-seller-email="{{ $seller->display_email ?? 'N/A' }}" style="border-radius: 30px; background-color: #e78307; color: #333; padding: 2px 12px; font-weight: bolder;">
                    																Añadir
                    															</button>
                    														@endif
                    													</td>
                    												</tr>
                    												@endforeach
                    											</tbody>
                    										</table>
                    									</div>
                    								</div>
                    							</div>
                    						</div>
                    					</div>
                    					<div class="col-md-4">
                    						<div class="form-card bs" style="height: 100%;">
                    							<h4 class="mb-0 mt-1">
                    								Vendedores Seleccionados
                    							</h4>
                    							<small><i>Vendedores Integrantes del grupo</i></small>

                    							<div class="{{ $group->sellers->count() > 0 ? 'd-none' : '' }}" id="empty-sellers">
                    								<div class="empty-prizes" style="text-align: center; padding: 40px 20px;">
                    									<div>
                    										<img src="{{url('icons_/vendedores.svg')}}" alt="" width="60px">
                    									</div>
                    									<h5 class="mb-2 mt-3">No hay Vendedores <br> añadidos al grupo</h5>
                    									<small style="line-height: 1.3; display: block;">Añade Vendedores al grupo <br> desde la tabla</small>
                    								</div>
                    							</div>

                    							<div class="{{ $group->sellers->count() > 0 ? '' : 'd-none' }}" id="added-sellers">
                    								<div class="form-group mt-2 mb-3">
                    									<div class="p-2" style="max-height: 500px; overflow-y: auto; border-top: 1px solid silver;" id="sellers-selected">
                    										@foreach($group->sellers as $seller)
                    										<div class="mb-2 p-2 bs" style="border-radius: 8px;" data-seller-id="{{ $seller->id }}">
                    											<div class="d-flex align-items-center justify-content-between">
                    												<div>
                    													<strong>{{ $seller->full_name }}</strong><br>
                    													<small class="text-muted">{{ $seller->display_email ?? 'N/A' }}</small>
                    												</div>
                    												<button type="button" style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-seller" data-seller-id="{{ $seller->id }}"><i class="ri-delete-bin-6-line"></i></button>
                    											</div>
                    										</div>
                    										@endforeach
                    									</div>
                    									
                    									<!-- Campos ocultos para enviar los vendedores seleccionados como array -->
                    									<div id="seller_ids_container">
                    										@foreach($group->sellers as $seller)
                    										<input type="hidden" name="seller_ids[]" value="{{ $seller->id }}">
                    										@endforeach
                    									</div>
                    								</div>
                    							</div>
                    						</div>
                    					</div>
                    				</div>

                    				<div class="row">
                    					<div class="col-12 text-end">
                    						<button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Guardar
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
    let selectedSellers = [];

    // Cargar vendedores seleccionados al cargar la página (no usar .hide(): deja display:none inline y al quitar d-none el botón sigue oculto)
    $(document).ready(function() {
        let sellerIds = [];
        $('#seller_ids_container input[type="hidden"]').each(function() {
            sellerIds.push(parseInt($(this).val()));
        });
        $('.add-seller').each(function() {
            let sellerId = parseInt($(this).data('seller-id'));
            if (sellerIds.includes(sellerId)) {
                selectedSellers.push({
                    id: sellerId,
                    name: $(this).data('seller-name'),
                    email: $(this).data('seller-email')
                });
                // Los botones de seleccionados ya vienen con d-none del servidor; no usar .hide()
            }
        });
    });

    // Función para agregar vendedor
    $(document).on('click', '.add-seller', function(e) {
        e.preventDefault();
        
        let sellerId = $(this).data('seller-id');
        let sellerName = $(this).data('seller-name');
        let sellerEmail = $(this).data('seller-email');
        
        // Verificar si ya está seleccionado
        if (selectedSellers.some(s => s.id === sellerId)) {
            return;
        }
        
        let seller = { id: parseInt(sellerId), name: sellerName, email: sellerEmail };
        selectedSellers.push(seller);
        
        $('#added-sellers').removeClass('d-none');
        $('#empty-sellers').addClass('d-none');

        let html = `<div class="mb-2 p-2 bs" style="border-radius: 8px;" data-seller-id="${sellerId}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>${sellerName}</strong><br>
                                <small class="text-muted">${sellerEmail}</small>
                            </div>
                            <button type="button" style="border-radius: 4px; width:28px; padding: 2px; font-weight: bolder; position: relative;" class="btn btn-sm btn-danger remove-seller" data-seller-id="${sellerId}"><i class="ri-delete-bin-6-line"></i></button>
                        </div>
                    </div>`;

        $('#sellers-selected').append(html);
        
        // Ocultar el botón "Añadir" en la tabla (Bootstrap d-none tiene !important, usar la misma clase)
        $(this).addClass('d-none');
        
        // Actualizar el campo oculto
        updateHiddenField();
    });

    // Función para remover vendedor
    $(document).on('click', '.remove-seller', function() {
        let sellerId = $(this).data('seller-id');
        let index = selectedSellers.findIndex(item => item.id === sellerId);
        if (index > -1) {
            selectedSellers.splice(index, 1);
        }
        
        $(this).closest('div[data-seller-id]').remove();

        // Mostrar de nuevo el botón "Añadir" en la tabla (quitar d-none y limpiar display por si se usó .hide() en carga)
        $('.add-seller[data-seller-id="' + sellerId + '"]').removeClass('d-none').css('display', '');

        if (selectedSellers.length == 0) {
            $('#added-sellers').addClass('d-none');
            $('#empty-sellers').removeClass('d-none');
        }
        
        // Actualizar el campo oculto
        updateHiddenField();
    });

    // Función para actualizar los campos ocultos
    function updateHiddenField() {
        let sellerIds = selectedSellers.map(s => s.id);
        // Limpiar contenedor
        $('#seller_ids_container').empty();
        // Crear inputs múltiples para cada seller_id
        sellerIds.forEach(function(sellerId) {
            $('#seller_ids_container').append('<input type="hidden" name="seller_ids[]" value="' + sellerId + '">');
        });
    }

    // Validación del formulario
    $('form').on('submit', function(e) {
        if (selectedSellers.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un vendedor para el grupo');
            return false;
        }
    });
</script>

@endsection

