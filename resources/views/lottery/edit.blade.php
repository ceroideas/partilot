@extends('layouts.layout')

@section('title','Editar Sorteo')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Sorteo</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Editar Sorteo

                    </h4>

                    <br>

                    <form action="{{ route('lotteries.update', $lottery->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                    	
                    	<div class="col-md-12">

                    		<div class="form-card bs mb-3">

                                <h4 class="mb-0 mt-1">
                                    Datos del Sorteo
                                </h4>
                                <small><i>Todos los campos son obligatorios</i></small>

                                <div class="form-group mt-2 mb-3">

                    				<div class="photo-preview" style="width: 200px; @if($lottery->image) background-image: url('{{ asset('storage/lotteries/' . $lottery->image) }}'); @endif">
                    					@if(!$lottery->image)
                    						<i class="ri-image-add-line"></i>
                    					@endif
                    				</div>

                    				<div>
                    					
                    					<small><i>Imágen</i></small>
                    					 <br>
                    					<b>Décimo</b>
                    					<br>

                    					<label style="border-radius: 30px; width: 150px; background-color: #333;" class="btn btn-md btn-dark mt-2">
                    						<small>Subir Imágen</small>
                    						<input type="file" id="imagenInput" name="image" style="display: none;" accept="image/*">
                    					</label>
                    					@if($lottery->image)
                    						<button type="button" id="deleteImageBtn" style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2"><small>Eliminar Imágen</small></button>
                    					@else
                    						<button type="button" id="deleteImageBtn" style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2" style="display: none;"><small>Eliminar Imágen</small></button>
                    					@endif

                    				</div>
                    				
                    				<div style="clear: both;"></div>
                    			</div>

                    			<div class="row">
                                            
                                    
                                    <div class="col-6">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Nombre del Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="text" name="name" placeholder="Nombre del Sorteo" style="border-radius: 0 30px 30px 0;" value="{{ old('name', $lottery->name) }}" required>
                                            </div>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Administración</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                </div>

                                                <select class="form-control" name="administration_id" style="border-radius: 0 30px 30px 0;" required>
                                                    <option value="" disabled>Seleccionar Administración</option>
                                                    @foreach($administrations ?? [] as $administration)
                                                        <option value="{{ $administration->id }}" {{ old('administration_id', $lottery->administration_id) == $administration->id ? 'selected' : '' }}>
                                                            {{ $administration->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('administration_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Tipo de Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                </div>

                                                <select class="form-control" name="lottery_type_id" style="border-radius: 0 30px 30px 0;" required>
                                                    <option value="" disabled>Seleccionar Tipo</option>
                                                    @foreach($lotteryTypes ?? [] as $lotteryType)
                                                        <option value="{{ $lotteryType->id }}" {{ old('lottery_type_id', $lottery->lottery_type_id) == $lotteryType->id ? 'selected' : '' }}>
                                                            {{ $lotteryType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('lottery_type_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                            
                                    
                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Precio del décimo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="number" step="0.01" name="ticket_price" placeholder="6.00€" style="border-radius: 0 30px 30px 0;" value="{{ old('ticket_price', $lottery->ticket_price) }}" required>
                                            </div>
                                            @error('ticket_price')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Total de boletos</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="number" name="total_tickets" placeholder="1000" style="border-radius: 0 30px 30px 0;" value="{{ old('total_tickets', $lottery->total_tickets) }}" required>
                                            </div>
                                            @error('total_tickets')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Fecha del sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="date" name="draw_date" placeholder="15/01/2025" style="border-radius: 0 30px 30px 0;" value="{{ old('draw_date', $lottery->draw_date) }}" required>
                                            </div>
                                            @error('draw_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Hora del sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/18.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="time" name="draw_time" placeholder="19:00h" style="border-radius: 0 30px 30px 0;" value="{{ old('draw_time', $lottery->draw_time) }}" required>
                                            </div>
                                            @error('draw_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Descripción del premio</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="text" name="prize_description" placeholder="Descripción del premio" style="border-radius: 0 30px 30px 0;" value="{{ old('prize_description', $lottery->prize_description) }}" required>
                                            </div>
                                            @error('prize_description')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Valor del premio</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="number" step="0.01" name="prize_value" placeholder="1000.00€" style="border-radius: 0 30px 30px 0;" value="{{ old('prize_value', $lottery->prize_value) }}" required>
                                            </div>
                                            @error('prize_value')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Estado</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                </div>

                                                <select class="form-control" name="status" style="border-radius: 0 30px 30px 0;" required>
                                                    <option value="1" {{ old('status', $lottery->status) == 1 ? 'selected' : '' }}>Activo</option>
                                                    <option value="2" {{ old('status', $lottery->status) == 2 ? 'selected' : '' }}>Inactivo</option>
                                                    <option value="3" {{ old('status', $lottery->status) == 3 ? 'selected' : '' }}>Completado</option>
                                                    <option value="4" {{ old('status', $lottery->status) == 4 ? 'selected' : '' }}>Cancelado</option>
                                                </select>
                                            </div>
                                            @error('status')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Descripción del sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
                                                </div>

                                                <textarea class="form-control" name="description" placeholder="Descripción del sorteo" style="border-radius: 0 30px 30px 0;" rows="3">{{ old('description', $lottery->description) }}</textarea>
                                            </div>
                                            @error('description')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                    		

                    	</div>

                        <div class="col-12 text-end">
                            <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Actualizar
                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i></button>
                        </div>

                    </div>
                    </form>

                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

</div> <!-- container -->

@endsection

@section('scripts')

<script>

	document.getElementById('imagenInput').addEventListener('change', function(event) {
	    const archivo = event.target.files[0];

	    if (archivo) {
	        const lector = new FileReader();
	        lector.onload = function(e) {
	        	$('.photo-preview').css('background-image', 'url('+e.target.result+')');
	        	$('#deleteImageBtn').show(); // Mostrar botón de eliminar
	        }
	        lector.readAsDataURL(archivo);
	    } else {
	        $('.photo-preview').css('background-image', 'none'); // Limpiar preview si se cancela la selección
	        $('#deleteImageBtn').hide(); // Ocultar botón de eliminar
	    }
	});

	// Función para eliminar imagen
	$('#deleteImageBtn').click(function() {
	    if (confirm('¿Estás seguro de que quieres eliminar la imagen?')) {
	        // Limpiar el input file
	        $('#imagenInput').val('');
	        
	        // Limpiar la vista previa
	        $('.photo-preview').css('background-image', 'none');
	        
	        // Ocultar el botón de eliminar
	        $(this).hide();
	        
	        // Hacer petición AJAX para eliminar la imagen del servidor
	        $.ajax({
	            url: '{{ route("lotteries.delete-image", $lottery->id) }}',
	            method: 'DELETE',
	            headers: {
	                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            success: function(response) {
	                if (response.success) {
	                    console.log('Imagen eliminada correctamente');
	                }
	            },
	            error: function() {
	                    console.error('Error al eliminar la imagen');
	            }
	        });
	    }
	});

</script>

@endsection