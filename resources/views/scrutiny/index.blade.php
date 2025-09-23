@extends('layouts.layout')

@section('title','Escrutinio de Sorteos')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                        <li class="breadcrumb-item active">Escrutinio</li>
                    </ol>
                </div>
                <h4 class="page-title">Escrutinio de Sorteos</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Sorteos Disponibles para Escrutinio</h4>
                    <p class="text-muted mb-0">Selecciona un sorteo para generar el escrutinio completo</p>
                </div>
                <div class="card-body">

                    @if($lotteries->count() > 0)

                        <div class="row">
                            @foreach($lotteries as $lottery)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <strong>{{ $lottery->name }}</strong>
                                            @if($lottery->description)
                                                <br><small class="text-muted">{{ $lottery->description }}</small>
                                            @endif
                                        </h5>
                                        
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <strong>Fecha:</strong> {{ $lottery->draw_date ? \Carbon\Carbon::parse($lottery->draw_date)->format('d/m/Y') : 'N/A' }}<br>
                                                <strong>Precio:</strong> {{ number_format($lottery->ticket_price, 2) }}€<br>
                                                <strong>Tipo:</strong> {{ $lottery->lotteryType->name ?? 'N/A' }}
                                            </small>
                                        </p>
                                        
                                        @if($lottery->result)
                                            <div class="mb-3">
                                                <h6>Premios Principales:</h6>
                                                @if($lottery->result->primer_premio)
                                                    <small class="text-success">
                                                        <strong>1º Premio:</strong> {{ $lottery->result->primer_premio['decimo'] ?? 'N/A' }}
                                                    </small><br>
                                                @endif
                                                @if($lottery->result->segundo_premio)
                                                    <small class="text-success">
                                                        <strong>2º Premio:</strong> {{ $lottery->result->segundo_premio['decimo'] ?? 'N/A' }}
                                                    </small><br>
                                                @endif
                                                @if($lottery->result->reintegros && count($lottery->result->reintegros) > 0)
                                                    <small class="text-info">
                                                        <strong>Reintegros:</strong> 
                                                        @foreach($lottery->result->reintegros as $reintegro)
                                                            {{ $reintegro['decimo'] ?? '' }}
                                                        @endforeach
                                                    </small>
                                                @endif
                                            </div>
                                            
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-success btn-sm generate-scrutiny-btn" 
                                                        data-lottery-id="{{ $lottery->id }}"
                                                        data-lottery-name="{{ $lottery->name }}">
                                                    <i class="ri-calculator-line"></i> Generar Escrutinio
                                                </button>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mb-0">
                                                <small>No hay resultados disponibles para este sorteo</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="ri-inbox-line" style="font-size: 48px; color: #ccc;"></i>
                            </div>
                            <h5 class="text-muted">No hay sorteos disponibles</h5>
                            <p class="text-muted">No se encontraron sorteos con resultados para generar escrutinio.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de configuración de escrutinio -->
<div class="modal fade" id="scrutinyConfigModal" tabindex="-1" aria-labelledby="scrutinyConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrutinyConfigModalLabel">Configurar Escrutinio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label"><strong>Sorteo:</strong> <span id="config-lottery-name"></span></label>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Rango de números a analizar</label>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Desde</label>
                            <input type="number" class="form-control" id="config-start-range" value="0" min="0" max="99999" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Hasta</label>
                            <input type="number" class="form-control" id="config-end-range" value="99999" min="0" max="99999" placeholder="99999">
                        </div>
                    </div>
                    <small class="text-muted">Rango completo: 0 - 99999 (100,000 números)</small>
                    
                    <!-- Botones de rango rápido -->
                    <div class="mt-2">
                        <small class="text-muted">Rangos rápidos:</small>
                        <div class="btn-group btn-group-sm mt-1" role="group">
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickRange(0, 9999)">0-9K</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickRange(10000, 19999)">10-19K</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickRange(20000, 29999)">20-29K</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickRange(26000, 26999)">26K</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="setQuickRange(0, 99999)">Completo</button>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Elementos por página</label>
                    <select class="form-select" id="config-per-page">
                        <option value="50">50 por página</option>
                        <option value="100" selected>100 por página</option>
                        <option value="200">200 por página</option>
                        <option value="500">500 por página</option>
                    </select>
                </div>
                
                <div class="alert alert-info">
                    <small>
                        <strong>Consejos de rendimiento:</strong><br>
                        • Rango pequeño (ej: 26000-26999): Muy rápido<br>
                        • Rango medio (ej: 0-9999): Rápido<br>
                        • Rango completo (0-99999): Más lento pero completo
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="start-scrutiny-btn">
                    <i class="ri-play-line"></i> Iniciar Escrutinio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar resultados del escrutinio -->
<div class="modal fade" id="scrutinyResultsModal" tabindex="-1" aria-labelledby="scrutinyResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrutinyResultsModalLabel">Escrutinio de Sorteo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="scrutiny-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Generando escrutinio completo...</p>
                    <small class="text-muted">Analizando 100.000 números</small>
                </div>
                
                <div id="scrutiny-results" style="display: none;">
                    <!-- Información del Sorteo - Diseño Compacto -->
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="card-title mb-0">
                                        <i class="ri-trophy-line me-2"></i>Resumen del Sorteo
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h5 class="text-primary mb-0" id="total-winning-numbers">0</h5>
                                                <small class="text-muted">Con premios</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h5 class="text-success mb-0" id="numbers-analyzed">100,000</h5>
                                                <small class="text-muted">Analizados</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h5 class="text-info mb-0" id="range-info">0-99999</h5>
                                                <small class="text-muted">Rango</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <p class="mb-0 small"><strong>Sorteo:</strong> <span id="lottery-name" class="text-primary"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white py-2">
                                    <h6 class="card-title mb-0">
                                        <i class="ri-settings-3-line me-2"></i>Controles
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Por página:</label>
                                            <select class="form-select form-select-sm" id="per-page-select">
                                                <option value="50">50</option>
                                                <option value="100" selected>100</option>
                                                <option value="200">200</option>
                                                <option value="500">500</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-1">Orden:</label>
                                            <select class="form-select form-select-sm" id="sort-order-select">
                                                <option value="desc" selected>↓ Mayor</option>
                                                <option value="asc">↑ Menor</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <button class="btn btn-outline-secondary btn-sm" id="prev-page-btn" disabled>
                                                    <i class="ri-arrow-left-line"></i>
                                                </button>
                                                <input type="number" class="form-control form-control-sm text-center mx-2" id="page-input" min="1" value="1" style="width: 50px;">
                                                <button class="btn btn-outline-secondary btn-sm" id="next-page-btn" disabled>
                                                    <i class="ri-arrow-right-line"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted text-center d-block" id="total-pages">de 1</small>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-success btn-sm w-100" id="export-results-btn" style="display: none;">
                                                <i class="ri-download-line me-1"></i> Exportar CSV
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-striped table-hover" id="scrutiny-table">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th>Número</th>
                                    <th>Premio Total</th>
                                    <th>Categorías</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody id="scrutiny-table-body">
                                <!-- Los resultados se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Variables globales para mantener el rango de búsqueda
    let currentStartRange = null;
    let currentEndRange = null;
    
    // Función para cargar página específica
    function loadScrutinyPage(lotteryId, page, perPage, startRange = null, endRange = null, sortOrder = 'desc') {
        const data = {
            lottery_id: lotteryId,
            page: page,
            per_page: perPage,
            sort_order: sortOrder,
            _token: '{{ csrf_token() }}'
        };
        
        // Usar rangos proporcionados o los rangos globales almacenados
        const useStartRange = startRange !== null ? startRange : currentStartRange;
        const useEndRange = endRange !== null ? endRange : currentEndRange;
        
        // Agregar rangos si están disponibles
        if (useStartRange !== null && useEndRange !== null) {
            data.start_range = useStartRange;
            data.end_range = useEndRange;
        }
        
        $.ajax({
            url: '{{ url("scrutiny/generate") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    displayScrutinyResults(response);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.message : 'Error desconocido';
                alert('Error: ' + error);
            },
            complete: function() {
                $('#scrutiny-loading').hide();
            }
        });
    }

    // Función para mostrar resultados
    function displayScrutinyResults(response) {
        console.log('Respuesta completa:', response); // Debug
        const results = response.results;
        const totalWinning = response.total_numbers_with_prizes;
        const currentPage = response.current_page;
        const totalPages = response.total_pages;
        const perPage = response.per_page;
        const searchRange = response.search_range;
        
        $('#total-winning-numbers').text(totalWinning.toLocaleString());
        $('#total-pages').text(`de ${totalPages}`);
        $('#page-input').val(currentPage).attr('max', totalPages);
        $('#per-page-select').val(perPage);
        $('#scrutiny-results').show();
        $('#export-results-btn').show();
        
        // Mostrar total de premios si está disponible
        if (response.total_prizes) {
            $('#total-winning-numbers').parent().append(`<br><small class="text-info">Total premios: ${response.total_prizes.toLocaleString()}</small>`);
        }
        
        // Mostrar información del rango de búsqueda
        if (searchRange && !searchRange.is_full_range) {
            $('#range-info').text(`${searchRange.start} - ${searchRange.end}`);
            $('#numbers-analyzed').text((searchRange.end - searchRange.start + 1).toLocaleString());
        } else {
            $('#range-info').text('Completo (0-99999)');
            $('#numbers-analyzed').text('100,000');
        }
        
        // Habilitar/deshabilitar botones de navegación
        $('#prev-page-btn').prop('disabled', currentPage <= 1);
        $('#next-page-btn').prop('disabled', currentPage >= totalPages);
        
        // Limpiar tabla
        $('#scrutiny-table-body').empty();
        
        // Mostrar resultados de la página actual
        results.forEach(function(result) {
            const row = `
                <tr>
                    <td><strong>${result.number}</strong></td>
                    <td><strong class="text-success">${formatCurrency(result.total_prize)}</strong></td>
                    <td>
                        ${result.prizes.map(prize => 
                            `<span class="badge bg-${getBadgeColor(prize.type)} me-1">${prize.category}</span>`
                        ).join('')}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-info" onclick="showPrizeDetails('${result.number}', ${JSON.stringify(result.prizes).replace(/"/g, '&quot;')})">
                            <i class="ri-eye-line"></i> Ver
                        </button>
                    </td>
                </tr>
            `;
            $('#scrutiny-table-body').append(row);
        });
        
        // Mostrar información de paginación
        const startResult = (currentPage - 1) * perPage + 1;
        const endResult = Math.min(currentPage * perPage, totalWinning);
        
        $('#scrutiny-table-body').append(`
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <em>Mostrando resultados ${startResult.toLocaleString()} - ${endResult.toLocaleString()} de ${totalWinning.toLocaleString()} números con premios</em>
                    ${response.total_prizes ? `<br><strong>Total de premios: ${response.total_prizes.toLocaleString()}</strong>` : ''}
                </td>
            </tr>
        `);
    }
    
    // Función para formatear moneda
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    }
    
    // Función para obtener color del badge
    function getBadgeColor(type) {
        const colors = {
            'main': 'danger',
            'derived': 'warning',
            'extraction': 'info',
            'reintegro': 'success'
        };
        return colors[type] || 'secondary';
    }

    // Variables globales para almacenar configuración
    let currentLotteryId = null;
    let currentLotteryName = null;

    // Generar escrutinio - mostrar modal de configuración
    $('.generate-scrutiny-btn').click(function() {
        currentLotteryId = $(this).data('lottery-id');
        currentLotteryName = $(this).data('lottery-name');
        
        // Mostrar modal de configuración
        $('#config-lottery-name').text(currentLotteryName);
        $('#scrutinyConfigModal').modal('show');
    });
    
    // Iniciar escrutinio desde modal de configuración
    $('#start-scrutiny-btn').click(function() {
        const startRange = parseInt($('#config-start-range').val()) || 0;
        const endRange = parseInt($('#config-end-range').val()) || 99999;
        const perPage = parseInt($('#config-per-page').val()) || 100;
        
        if (startRange > endRange) {
            alert('El rango inicial no puede ser mayor al rango final');
            return;
        }
        
        // Cerrar modal de configuración
        $('#scrutinyConfigModal').modal('hide');
        
        // Mostrar modal de resultados
        $('#scrutinyResultsModalLabel').text('Escrutinio: ' + currentLotteryName);
        $('#lottery-name').text(currentLotteryName);
        $('#scrutinyResultsModal').modal('show');
        
        // Mostrar loading
        $('#scrutiny-loading').show();
        $('#scrutiny-results').hide();
        
        // Almacenar rangos globalmente para usar en paginación
        currentStartRange = startRange;
        currentEndRange = endRange;
        
        // Realizar petición AJAX con configuración
        const sortOrder = $('#sort-order-select').val();
        loadScrutinyPage(currentLotteryId, 1, perPage, startRange, endRange, sortOrder);
    });
    
    // Event listeners para paginación y ordenamiento
    $('#per-page-select').on('change', function() {
        if (!currentLotteryId) return;
        const perPage = $(this).val();
        const sortOrder = $('#sort-order-select').val();
        loadScrutinyPage(currentLotteryId, 1, perPage, currentStartRange, currentEndRange, sortOrder);
    });
    
    $('#sort-order-select').on('change', function() {
        if (!currentLotteryId) return;
        const sortOrder = $(this).val();
        const perPage = $('#per-page-select').val();
        loadScrutinyPage(currentLotteryId, 1, perPage, currentStartRange, currentEndRange, sortOrder);
    });
    
    $('#page-input').on('change', function() {
        if (!currentLotteryId) return;
        const page = $(this).val();
        const perPage = $('#per-page-select').val();
        const sortOrder = $('#sort-order-select').val();
        loadScrutinyPage(currentLotteryId, page, perPage, currentStartRange, currentEndRange, sortOrder);
    });
    
    // Botones de navegación
    $('#prev-page-btn').on('click', function() {
        if (!currentLotteryId) return;
        const currentPage = parseInt($('#page-input').val());
        const perPage = $('#per-page-select').val();
        const sortOrder = $('#sort-order-select').val();
        if (currentPage > 1) {
            loadScrutinyPage(currentLotteryId, currentPage - 1, perPage, currentStartRange, currentEndRange, sortOrder);
        }
    });
    
    $('#next-page-btn').on('click', function() {
        if (!currentLotteryId) return;
        const currentPage = parseInt($('#page-input').val());
        const totalPages = parseInt($('#total-pages').text().split('de ')[1]);
        const perPage = $('#per-page-select').val();
        const sortOrder = $('#sort-order-select').val();
        if (currentPage < totalPages) {
            loadScrutinyPage(currentLotteryId, currentPage + 1, perPage, currentStartRange, currentEndRange, sortOrder);
        }
    });
    
    // Exportar resultados
    $('#export-results-btn').on('click', function() {
        if (!currentLotteryId) return;
        
        // Crear formulario para descarga
        const form = $('<form>', {
            method: 'POST',
            action: '{{ url("scrutiny/export") }}'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'lottery_id',
            value: currentLotteryId
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    });
    
    // Filtros
    $('#min-prize-filter, #max-prize-filter').on('input', function() {
        // Implementar filtros si es necesario
    });
});

// Función global para mostrar detalles de premios
function showPrizeDetails(number, prizes) {
    let details = `<h6>Número: ${number}</h6><ul class="list-unstyled">`;
    prizes.forEach(prize => {
        details += `<li><strong>${prize.category}:</strong> ${formatCurrency(prize.amount)}</li>`;
    });
    details += '</ul>';
    
    // Mostrar en un modal o alert
    alert(details);
}

// Función para formatear moneda (global)
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Función para establecer rangos rápidos
function setQuickRange(start, end) {
    $('#config-start-range').val(start);
    $('#config-end-range').val(end);
}
</script>
@endsection