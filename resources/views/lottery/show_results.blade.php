@extends('layouts.layout')

@section('title', 'Resultados del Sorteo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lotteries.index') }}">Sorteos</a></li>
                        <li class="breadcrumb-item active">Resultados</li>
                    </ol>
                </div>
                <h4 class="page-title">Resultados del Sorteo: {{ $lottery->name }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Información del Sorteo</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $lottery->name }}</p>
                            <p><strong>Descripción:</strong> {{ $lottery->description }}</p>
                            <p><strong>Tipo:</strong> {{ $lottery->lotteryType->name ?? 'N/A' }}</p>
                            <p><strong>Fecha del Sorteo:</strong> {{ $lottery->draw_date ? $lottery->draw_date->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Hora del Sorteo:</strong> {{ $lottery->draw_time ? $lottery->draw_time->format('H:i') : 'N/A' }}</p>
                            <p><strong>Precio del Décimo:</strong> {{ $lottery->ticket_price ? number_format($lottery->ticket_price, 2) . ' €' : 'N/A' }}</p>
                            <p><strong>Estado:</strong> 
                                @if($lottery->status)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($lottery->result)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Resultados del Sorteo</h4>
                        <div class="header-title-right">
                            <span class="badge bg-info">Publicado: {{ $lottery->result->results_date ? $lottery->result->results_date->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Premio Especial -->
                        @if($lottery->result->premio_especial)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">🏆 Premio Especial</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Número:</strong> {{ $lottery->result->premio_especial['numero'] ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Fracción:</strong> {{ $lottery->result->premio_especial['fraccion'] ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Serie:</strong> {{ $lottery->result->premio_especial['serie'] ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Premio:</strong> {{ isset($lottery->result->premio_especial['premio']) ? number_format($lottery->result->premio_especial['premio'], 2) . ' €' : 'N/A' }}
                                    </div>
                                </div>
                                @if(isset($lottery->result->premio_especial['literalPremio']))
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <strong>Literal:</strong> {{ $lottery->result->premio_especial['literalPremio'] }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Primer Premio -->
                        @if($lottery->result->primer_premio)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-success">🥇 Primer Premio</h5>
                                <div class="row">
                                    @foreach($lottery->result->primer_premio as $index => $premio)
                                    <div class="col-md-4 mb-2">
                                        <div class="border rounded p-2">
                                            <strong>Premio {{ $index + 1 }}:</strong><br>
                                            Número: {{ $premio['numero'] ?? 'N/A' }}<br>
                                            @if(isset($premio['fraccion']))
                                                Fracción: {{ $premio['fraccion'] }}<br>
                                            @endif
                                            @if(isset($premio['premio']))
                                                Premio: {{ number_format($premio['premio'], 2) }} €<br>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Segundo Premio -->
                        @if($lottery->result->segundo_premio)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-warning">🥈 Segundo Premio</h5>
                                <div class="row">
                                    @foreach($lottery->result->segundo_premio as $index => $premio)
                                    <div class="col-md-4 mb-2">
                                        <div class="border rounded p-2">
                                            <strong>Premio {{ $index + 1 }}:</strong><br>
                                            Número: {{ $premio['numero'] ?? 'N/A' }}<br>
                                            @if(isset($premio['fraccion']))
                                                Fracción: {{ $premio['fraccion'] }}<br>
                                            @endif
                                            @if(isset($premio['premio']))
                                                Premio: {{ number_format($premio['premio'], 2) }} €<br>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Terceros Premios -->
                        @if($lottery->result->terceros_premios && count($lottery->result->terceros_premios) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-info">🥉 Terceros Premios</h5>
                                <div class="row">
                                    @foreach($lottery->result->terceros_premios as $index => $premio)
                                    <div class="col-md-3 mb-2">
                                        <div class="border rounded p-2">
                                            <strong>Premio {{ $index + 1 }}:</strong><br>
                                            Número: {{ $premio['numero'] ?? 'N/A' }}<br>
                                            @if(isset($premio['fraccion']))
                                                Fracción: {{ $premio['fraccion'] }}<br>
                                            @endif
                                            @if(isset($premio['premio']))
                                                Premio: {{ number_format($premio['premio'], 2) }} €<br>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Cuartos Premios -->
                        @if($lottery->result->cuartos_premios && count($lottery->result->cuartos_premios) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-secondary">🏅 Cuartos Premios</h5>
                                <div class="row">
                                    @foreach($lottery->result->cuartos_premios as $index => $premio)
                                    <div class="col-md-3 mb-2">
                                        <div class="border rounded p-2">
                                            <strong>Premio {{ $index + 1 }}:</strong><br>
                                            Número: {{ $premio['numero'] ?? 'N/A' }}<br>
                                            @if(isset($premio['fraccion']))
                                                Fracción: {{ $premio['fraccion'] }}<br>
                                            @endif
                                            @if(isset($premio['premio']))
                                                Premio: {{ number_format($premio['premio'], 2) }} €<br>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Quintos Premios -->
                        @if($lottery->result->quintos_premios && count($lottery->result->quintos_premios) > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-dark">🏆 Quintos Premios</h5>
                                <div class="row">
                                    @foreach($lottery->result->quintos_premios as $index => $premio)
                                    <div class="col-md-3 mb-2">
                                        <div class="border rounded p-2">
                                            <strong>Premio {{ $index + 1 }}:</strong><br>
                                            Número: {{ $premio['numero'] ?? 'N/A' }}<br>
                                            @if(isset($premio['fraccion']))
                                                Fracción: {{ $premio['fraccion'] }}<br>
                                            @endif
                                            @if(isset($premio['premio']))
                                                Premio: {{ number_format($premio['premio'], 2) }} €<br>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Extracciones -->
                        <div class="row">
                            <div class="col-md-6">
                                @if($lottery->result->extracciones_cinco_cifras && count($lottery->result->extracciones_cinco_cifras) > 0)
                                <div class="mb-3">
                                    <h6 class="text-primary">Extracciones de 5 Cifras</h6>
                                    <div class="row">
                                        @foreach($lottery->result->extracciones_cinco_cifras as $extraccion)
                                        <div class="col-md-6 mb-1">
                                            <span class="badge bg-light text-dark">{{ $extraccion }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($lottery->result->extracciones_cuatro_cifras && count($lottery->result->extracciones_cuatro_cifras) > 0)
                                <div class="mb-3">
                                    <h6 class="text-success">Extracciones de 4 Cifras</h6>
                                    <div class="row">
                                        @foreach($lottery->result->extracciones_cuatro_cifras as $extraccion)
                                        <div class="col-md-6 mb-1">
                                            <span class="badge bg-light text-dark">{{ $extraccion }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                @if($lottery->result->extracciones_tres_cifras && count($lottery->result->extracciones_tres_cifras) > 0)
                                <div class="mb-3">
                                    <h6 class="text-warning">Extracciones de 3 Cifras</h6>
                                    <div class="row">
                                        @foreach($lottery->result->extracciones_tres_cifras as $extraccion)
                                        <div class="col-md-6 mb-1">
                                            <span class="badge bg-light text-dark">{{ $extraccion }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($lottery->result->extracciones_dos_cifras && count($lottery->result->extracciones_dos_cifras) > 0)
                                <div class="mb-3">
                                    <h6 class="text-info">Extracciones de 2 Cifras</h6>
                                    <div class="row">
                                        @foreach($lottery->result->extracciones_dos_cifras as $extraccion)
                                        <div class="col-md-6 mb-1">
                                            <span class="badge bg-light text-dark">{{ $extraccion }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Reintegros -->
                        @if($lottery->result->reintegros && count($lottery->result->reintegros) > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-danger">🔄 Reintegros</h6>
                                <div class="row">
                                    @foreach($lottery->result->reintegros as $reintegro)
                                    <div class="col-md-2 mb-1">
                                        <span class="badge bg-danger">{{ $reintegro }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="alert alert-warning" role="alert">
                            <i class="ri-alert-line me-2"></i>
                            <strong>No hay resultados disponibles</strong><br>
                            Los resultados de este sorteo aún no han sido publicados o no se han obtenido desde la API.
                        </div>
                        
                        <!-- Formulario para obtener resultados desde API -->
                        <div class="mt-3">
                            <h6>Obtener Resultados desde API</h6>
                            <form id="fetchResultsForm" class="row g-3">
                                @csrf
                                <input type="hidden" name="lottery_id" value="{{ $lottery->id }}">
                                <div class="col-md-8">
                                    <input type="url" class="form-control" name="api_url" 
                                           placeholder="URL de la API para obtener resultados" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-download-line me-1"></i>
                                        Obtener Resultados
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Manejar envío del formulario para obtener resultados
    $('#fetchResultsForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Cambiar texto del botón
        submitBtn.html('<i class="ri-loader-4-line me-1"></i> Obteniendo...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("lottery.fetch-results") }}',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Recargar la página para mostrar los resultados
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                let message = 'Error al obtener los resultados';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Restaurar botón
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection 