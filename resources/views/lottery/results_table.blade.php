@extends('layouts.layout')

@section('title', 'Tabla de Resultados de Sorteos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lotteries.index') }}">Sorteos</a></li>
                        <li class="breadcrumb-item active">Tabla de Resultados</li>
                    </ol>
                </div>
                <h4 class="page-title">Tabla de Resultados de Sorteos</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Resultados de Sorteos</h4>
                    <div class="header-title-right">
                        <span class="badge bg-info">Total: {{ $lotteries->count() }} sorteos</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="resultsTable">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>1º Premio</th>
                                    <th>2º Premio</th>
                                    <th>3º Premio</th>
                                    <th>Fracción</th>
                                    <th>Serie</th>
                                    <th>Reintegro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lotteries as $lottery)
                                <tr>
                                    <td>
                                        <strong>{{ $lottery->name }}</strong>
                                        @if($lottery->description)
                                            <br><small class="text-muted">{{ $lottery->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $lottery->draw_date ? $lottery->draw_date->format('d/m/Y') : 'N/A' }}
                                        @if($lottery->draw_time)
                                            <br><small class="text-muted">{{ $lottery->draw_time->format('H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $lottery->lotteryType->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($lottery->result && $lottery->result->primer_premio)
                                            @if(is_array($lottery->result->primer_premio))
                                                @foreach($lottery->result->primer_premio as $index => $premio)
                                                    <div class="mb-1">
                                                        <strong>{{ $premio['decimo'] ?? 'N/A' }}</strong>
                                                        @if(isset($premio['prize']))
                                                            <br><small>{{ number_format($premio['prize'], 2) }} €</small>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lottery->result && $lottery->result->segundo_premio)
                                            @if(is_array($lottery->result->segundo_premio))
                                                @foreach($lottery->result->segundo_premio as $index => $premio)
                                                    <div class="mb-1">
                                                        <strong>{{ $premio['decimo'] ?? 'N/A' }}</strong>
                                                        @if(isset($premio['prize']))
                                                            <br><small>{{ number_format($premio['prize'], 2) }} €</small>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lottery->result && $lottery->result->terceros_premios && count($lottery->result->terceros_premios) > 0)
                                            @foreach($lottery->result->terceros_premios as $index => $premio)
                                                <div class="mb-1">
                                                    <strong>{{ $premio['decimo'] ?? 'N/A' }}</strong>
                                                    @if(isset($premio['prize']))
                                                        <br><small>{{ number_format($premio['prize'], 2) }} €</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lottery->result && $lottery->result->premio_especial)
                                            {{ $lottery->result->premio_especial['fraccion'] ?? '-' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lottery->result && $lottery->result->premio_especial)
                                            {{ $lottery->result->premio_especial['serie'] ?? '-' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lottery->result && $lottery->result->reintegros && count($lottery->result->reintegros) > 0)
                                            @foreach($lottery->result->reintegros as $reintegro)
                                                <span class="badge bg-danger me-1">{{ $reintegro }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lottery->status == 1)
                                            <span class="badge bg-success">Activo</span>
                                        @elseif($lottery->status == 2)
                                            <span class="badge bg-warning">Inactivo</span>
                                        @elseif($lottery->status == 3)
                                            <span class="badge bg-info">Completado</span>
                                        @elseif($lottery->status == 4)
                                            <span class="badge bg-danger">Cancelado</span>
                                        @else
                                            <span class="badge bg-secondary">Desconocido</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('lottery.show-results', $lottery->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver Resultados Detallados">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            @if(!$lottery->result)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success" 
                                                        title="Obtener Resultados desde API"
                                                        onclick="showFetchResultsModal({{ $lottery->id }}, '{{ $lottery->name }}')">
                                                    <i class="ri-download-line"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="ri-information-line me-2"></i>
                                            No hay sorteos disponibles
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para obtener resultados desde API -->
<div class="modal fade" id="fetchResultsModal" tabindex="-1" aria-labelledby="fetchResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fetchResultsModalLabel">Obtener Resultados desde API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fetchResultsForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lottery_id" id="modalLotteryId">
                    <div class="mb-3">
                        <label for="modalLotteryName" class="form-label">Sorteo</label>
                        <input type="text" class="form-control" id="modalLotteryName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modalApiUrl" class="form-label">URL de la API</label>
                        <input type="url" class="form-control" name="api_url" id="modalApiUrl" 
                               placeholder="https://api.ejemplo.com/resultados" required>
                        <div class="form-text">Ingrese la URL de la API para obtener los resultados del sorteo</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-download-line me-1"></i>
                        Obtener Resultados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#resultsTable').DataTable({
        order: [[0, 'desc']], // Ordenar por número de sorteo descendente
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: [10] } // Columna de acciones no ordenable
        ]
    });

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
                        // Cerrar modal y recargar la página
                        $('#fetchResultsModal').modal('hide');
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

// Función para mostrar el modal de obtener resultados
function showFetchResultsModal(lotteryId, lotteryName) {
    $('#modalLotteryId').val(lotteryId);
    $('#modalLotteryName').val(lotteryName);
    $('#modalApiUrl').val('');
    $('#fetchResultsModal').modal('show');
}
</script>
@endsection 