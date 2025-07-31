@extends('layouts.layout')

@section('title','Editar Resultados del Sorteo')

@section('content')

<style>
	input:disabled, textarea:disabled {
		background-color: #e0e0e0 !important;
	}
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
            	<div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('lotteries.index') }}">Sorteos</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lottery.results') }}">Resultados</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
                <h4 class="page-title">Editar Resultados del Sorteo</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                	<h4 class="header-title">
                    	Edición Resultados - {{ $lottery->name }}
                    </h4>

                    <br>

                    <!-- Botón para obtener resultados desde API -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" id="fetchResultsBtn" class="btn btn-success" style="border-radius: 30px; padding: 10px 20px;">
                                <i class="ri-download-line me-2"></i>
                                Obtener Resultados desde API
                            </button>
                            {{-- <small class="text-muted d-block mt-2">
                                <strong>URL de la API:</strong> {{ $apiUrl }}
                            </small> --}}
                        </div>
                    </div>

                    <div class="row">
                    	
                    	<div class="col-md-12">
                    		<div class="form-card bs" style="min-height: 658px;">
                    			<h4 class="mb-0 mt-1">
                    				Datos del Sorteo
                    			</h4>
                    			<small><i>Revisa que los datos del sorteo sean los correctos</i></small>

                    			<div class="form-group mt-2 mb-3">

                                    <div class="row show-content">

                                    	<div class="col-2">

                                    		<div style="width: calc(100% - 20px); height: 80px; border-radius: 8px; background-color: silver; float: left; margin-right: 20px; background-image: url({{ $lottery->image ? url('uploads/' . $lottery->image) : '' }}); background-size: cover; background-position: center;">
                                            </div>
                                    		
                                    	</div>

                                    	<div class="col-2">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Número de Sorteo</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/16.svg')}}" alt="">
				                                    </div>

				                                    <input class="form-control" value="{{ $lottery->name }}" type="text" readonly style="border-radius: 0 30px 30px 0;">
				                                </div>
			                    			</div>
                    					</div>

                    					<div class="col-4">
                    						<div class="form-group mt-2 mb-3">
                    							<label class="label-control">Nombre del Sorteo</label>

				                    			<div class="input-group input-group-merge group-form">

				                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
				                                        <img src="{{url('assets/form-groups/admin/17.svg')}}" alt="">
				                                    </div>

				                                    <input class="form-control" value="{{ $lottery->description }}" type="text" readonly style="border-radius: 0 30px 30px 0;">
				                                </div>
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

	                                                <input class="form-control" value="{{ $lottery->lotteryType->name ?? 'N/A' }}" type="text" readonly style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Precio décimo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/15.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" value="{{ $lottery->ticket_price }}" type="text" readonly style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-2">
	                                        <div class="form-group mt-2 mb-3">
	                                            <label class="label-control">Fecha Sorteo</label>

	                                            <div class="input-group input-group-merge group-form">

	                                                <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
	                                                    <img src="{{url('assets/form-groups/admin/12.svg')}}" alt="">
	                                                </div>

	                                                <input class="form-control" value="{{ $lottery->draw_date->format('Y-m-d') }}" type="text" readonly style="border-radius: 0 30px 30px 0;">
	                                            </div>
	                                        </div>
	                                    </div>

                                    </div>
                    				
                    			</div>

                                <h4 class="mb-0 mt-1">
                                    Resultados del Sorteo
                                </h4>
                                <small><i>Dejar en blanco los datos que no proceden para este tipo de sorteo. <br> Para extracciones múltiples separar cada número por un guión</i></small>

                                @if($lottery->result)
                                    <!-- Mostrar resultados existentes -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-success">
                                                <i class="ri-check-line me-2"></i>
                                                <strong>Resultados disponibles</strong> - Fecha: {{ $lottery->result->results_date ? $lottery->result->results_date->format('d/m/Y H:i:s') : 'No disponible' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="ri-information-line me-2"></i>
                                                No hay resultados disponibles para este sorteo. Utiliza el botón de arriba para obtener los resultados desde la API.
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div style="/*min-height: 400px; height: 400px; overflow-y: unset; overflow-x: scroll;*/">

                                    <!-- Premio Especial -->
                                    <div class="row">
                                    	<div class="col-2">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Premio Especial</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->premio_especial ? (is_array($lottery->result->premio_especial) ? $lottery->result->premio_especial['numero'] : $lottery->result->premio_especial) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>
                                    </div>

                                    <!-- Primer Premio -->
                                    <div class="row">
                                    	<div class="col-2">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Primer Premio</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->primer_premio ? (is_array($lottery->result->primer_premio) ? $lottery->result->primer_premio['decimo'] : $lottery->result->primer_premio) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-1">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Serie</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->primer_premio && is_array($lottery->result->primer_premio) ? ($lottery->result->primer_premio['serie'] ?? '') : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-1">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Fracción</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->primer_premio && is_array($lottery->result->primer_premio) ? ($lottery->result->primer_premio['fraccion'] ?? '') : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-1">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">Reintegros</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->refunds ? implode('-', $lottery->result->refunds) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-2 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias extracciones <br> de 1 cifra</i></small>
	                                        </div>
	                                    </div>
                                    </div>

                                    <!-- Segundo Premio -->
                                    <div class="row">
                                    	<div class="col-2">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Segundo Premio</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->segundo_premio ? (is_array($lottery->result->segundo_premio) ? $lottery->result->segundo_premio['decimo'] : $lottery->result->segundo_premio) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Una Extracción <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Terceros Premios -->
	                                <div class="row">
                                    	<div class="col-2">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Terceros Premios</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->thirds ? implode('-', $lottery->result->thirds) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Cuartos Premios -->
	                                <div class="row">
                                    	<div class="col-3">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Cuartos Premios</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->fourths ? implode('-', $lottery->result->fourths) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Quintos Premios -->
	                                <div class="row">
                                    	<div class="col-6">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Quintos Premios</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result->fifths ? implode('-', $lottery->result->fifths) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Extracciones 5 Cifras -->
	                                <div class="row">
                                    	<div class="col-6">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 5 Cifras</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result['5figures'] ? implode('-',$lottery->result['5figures']) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 5 cifras</i></small>
	                                        </div>
	                                    </div>

	                                    <div class="col-12">
	                                    	<small><i>Solo en los casos en el que existen <b>VARIOS 3º/4º Premios</b> como en los sorteos del NIÑO, S.ILDEFONSO o VACACIONES</i></small>
	                                    </div>
	                                </div>

	                                <!-- Extracciones 4 Cifras -->
	                                <div class="row">
                                    	<div class="col-3">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 4 Cifras</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result['4figures'] ? implode('-',$lottery->result['4figures']) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 4 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Extracciones 3 Cifras -->
	                                <div class="row">
                                    	<div class="col-6">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 3 Cifras</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result['3figures'] ? implode('-',$lottery->result['3figures']) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 3 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Extracciones 2 Cifras -->
	                                <div class="row">
                                    	<div class="col-3">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Extracciones 2 Cifras</label>
	                                            <div class="input-group input-group-merge group-form">
	                                                <input class="form-control" type="text" value="{{ $lottery->result && $lottery->result['2figures'] ? implode('-',$lottery->result['2figures']) : '' }}" readonly style="border-radius: 30px;">
	                                            </div>
	                                        </div>
	                                    </div>

	                                    <div class="col-4">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">&nbsp;</label> <br>
	                                            <small style="line-height: 1.5; display: block;"><i>Varias Extracciones <br> de 2 cifras</i></small>
	                                        </div>
	                                    </div>
	                                </div>

	                                <!-- Pedrea -->
	                                <div class="row">
                                    	<div class="col-12">
	                                        <div class="form-group mt-1 mb-2">
	                                            <label class="label-control">Pedrea</label>
	                                            <div class="input-group input-group-merge group-form">
	                                            	<textarea class="form-control" readonly style="border-radius: 30px;" rows="6">{{-- {{ $lottery->result && $lottery->result->reintegros ? 'Reintegros: ' . implode(', ', $lottery->result->reintegros) : '' }} --}}</textarea>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>

                                </div>

                                <div class="row">

                                    <div class="col-6 text-start">
                                        <a href="{{ route('lottery.results') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                            <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                                    </div>
                                    
                                    <div class="col-6 text-end">
                                        <a href="{{ route('lottery.show-results', $lottery->id) }}" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">Ver Resultados
                                            <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-eye-line"></i></a>
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
    // Funcionalidad para obtener resultados desde API
    document.getElementById('fetchResultsBtn').addEventListener('click', function() {
        const button = this;
        const originalText = button.innerHTML;
        
        // Cambiar estado del botón
        button.disabled = true;
        button.innerHTML = '<i class="ri-loader-4-line me-2"></i>Obteniendo resultados...';
        
        // Realizar petición AJAX
        fetch('{{ route("lottery.fetch-specific-results") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                lottery_id: {{ $lottery->id }},
                api_url: '{{ $apiUrl }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                alert('¡Éxito! ' + data.message);
                // Recargar la página para mostrar los nuevos resultados
                window.location.reload();
            } else {
                // Mostrar mensaje de error
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al obtener los resultados');
        })
        .finally(() => {
            // Restaurar estado del botón
            button.disabled = false;
            button.innerHTML = originalText;
        });
    });
</script>

@endsection