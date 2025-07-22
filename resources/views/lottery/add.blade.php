@extends('layouts.layout')

@section('title','Sorteos')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Sorteos</a></li>
                        <li class="breadcrumb-item active">Añadir</li>
                    </ol>
                </div>
                <h4 class="page-title">Sorteos</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

            		<h4 class="header-title">

                    	Configuración de Sorteo

                    </h4>

                    <br>

                    <form action="{{ route('lotteries.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                    	
                    	<div class="col-md-12">

                    		<div class="form-card bs mb-3">

                                <h4 class="mb-0 mt-1">
                                    Datos legales de la entidad
                                </h4>
                                <small><i>Todos los campos son obligatorios</i></small>

                                <div class="form-group mt-2 mb-3">

                    				<div class="photo-preview" style="width: 200px;">
                    					
                    					<i class="ri-image-add-line"></i>

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
                    					<label style="border-radius: 30px; width: 150px; background-color: transparent; color: #333;" class="btn btn-md btn-dark mt-2"><small>Eliminar Imágen</small></label>

                    				</div>
                    				
                    				<div style="clear: both;"></div>
                    			</div>

                    			<div class="row">
                                            
                                    
                                    <div class="col-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Número del Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="text" name="name" placeholder="46/25" style="border-radius: 0 30px 30px 0;" value="{{ old('name') }}" required>
                                            </div>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Nombre del Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="text" name="description" placeholder="Nombre del Sorteo" style="border-radius: 0 30px 30px 0;" value="{{ old('description') }}">
                                            </div>
                                            @error('description')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                            
                                    
                                    <div class="col-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Tipo de Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/14.svg')}}" alt="">
                                                </div>

                                                <select class="form-control" name="lottery_type_id" style="border-radius: 0 30px 30px 0;" required>
                                                	<option value="" disabled selected>Sorteo de Ejemplo</option>
                                                	@foreach($lotteryTypes ?? [] as $lotteryType)
                                                        <option value="{{ $lotteryType->id }}" {{ old('lottery_type_id') == $lotteryType->id ? 'selected' : '' }}>
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

                                    <div class="col-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Precio décimo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="number" step="0.01" name="ticket_price" placeholder="6.00€" style="border-radius: 0 30px 30px 0;" value="{{ old('ticket_price') }}" required>
                                            </div>
                                            @error('ticket_price')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Fecha Sorteo</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="date" name="draw_date" placeholder="15/01/2025" style="border-radius: 0 30px 30px 0;" value="{{ old('draw_date') }}" required>
                                            </div>
                                            @error('draw_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Fecha Límite</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="date" name="deadline_date" placeholder="15/01/2025" style="border-radius: 0 30px 30px 0;" value="{{ old('deadline_date') }}">
                                            </div>
                                            @error('deadline_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Hora Límite</label>

                                            <div class="input-group input-group-merge group-form">

                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                    <img src="{{url('assets/form-groups/admin/18.svg')}}" alt="">
                                                </div>

                                                <input class="form-control" type="time" name="draw_time" placeholder="19:00h" style="border-radius: 0 30px 30px 0;" value="{{ old('draw_time') }}" required>
                                            </div>
                                            @error('draw_time')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <!-- Campos adicionales necesarios para el controlador -->
                                {{-- <div class="row" style="display: none;">
                                    <div class="col-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Total de boletos</label>
                                            <input class="form-control" type="number" name="total_tickets" value="{{ old('total_tickets', 1000) }}" required>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Descripción del premio</label>
                                            <input class="form-control" type="text" name="prize_description" value="{{ old('prize_description', 'Premio principal') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-group mt-2 mb-3">
                                            <label class="label-control">Valor del premio</label>
                                            <input class="form-control" type="number" step="0.01" name="prize_value" value="{{ old('prize_value', 1000.00) }}" required>
                                        </div>
                                    </div>
                                </div> --}}


                            </div>
                    		

                    	</div>

                        <div class="col-12 text-end">
                            <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative; top: calc(100% - 51px);" class="btn btn-md btn-light mt-2">Guardar
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
	        }
	        lector.readAsDataURL(archivo);
	    } else {
	        $('.photo-preview').css('background-image', 'none'); // Limpiar preview si se cancela la selección
	    }
	});

</script>

@endsection