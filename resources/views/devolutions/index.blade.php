@extends('layouts.layout')

@section('title','Devoluciones')

@section('content')

<style>
    .empty-tables {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-tables img {
        opacity: 0.3;
        filter: grayscale(100%);
    }

    .empty-tables h3 {
        color: #333;
        font-weight: 600;
        margin: 20px 0 10px 0;
    }

    .empty-tables small {
        color: #666;
        font-size: 0.9em;
    }
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('devolutions.index') }}">Devoluciones</a></li>
                        <li class="breadcrumb-item active">Lista de Devoluciones</li>
                    </ol>
                </div>
                <h4 class="page-title">Devoluciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Tabla de devoluciones -->
                    <div class="{{ count($devolutions ?? []) > 0 ? '' : 'd-none' }}">
                        <h4 class="header-title">
                            <div class="float-start d-flex align-items-start">
                                <input type="text" class="form-control" style="margin-right: 8px;" placeholder="Entidad">
                                <input type="text" class="form-control" style="margin-right: 8px;" placeholder="Sorteo">
                                <input type="text" class="form-control" style="margin-right: 8px;" placeholder="Participaciones">
                                <input type="text" class="form-control" placeholder="Liquidación">
                            </div>
                            <a href="{{ route('devolutions.create') }}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark float-end">
                                <i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir
                            </a>
                        </h4>

                        <div style="clear: both;"></div>

                        <br>

                        <div class="table-responsive">
                            <table id="tabla-devoluciones" class="table table-striped nowrap w-100">
                                <thead class="filters">
                                    <tr>
                                        <th>ID</th>
                                        <th>Entidad</th>
                                        <th>Sorteo</th>
                                        <th>Total Participaciones</th>
                                        <th>Participaciones Devueltas</th>
                                        <th>Fecha Devolución</th>
                                        <th>Total Liquidación</th>
                                        <th>Total Pagos Registrados</th>
                                        <th class="no-filter">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($devolutions))
                                        @foreach($devolutions as $devolution)
                                            @php
                                                $totalLiquidacion = $devolution->details()
                                                    ->where('action', 'vender')
                                                    ->with('participation.set')
                                                    ->get()
                                                    ->sum(function($detail) {
                                                        return $detail->participation->set->played_amount ?? 0;
                                                    });
                                                $totalPagos = $devolution->payments->sum('amount');
                                            @endphp
                                            <tr>
                                                <td><a href="{{ route('devolutions.show', $devolution->id) }}">#DEV{{ str_pad($devolution->id, 4, '0', STR_PAD_LEFT) }}</a></td>
                                                <td>{{ $devolution->entity->name ?? 'N/A' }}</td>
                                                <td>{{ $devolution->lottery->name ?? 'N/A' }}</td>
                                                <td>{{ $devolution->total_participations }}</td>
                                                <td>{{ $devolution->details()->where('action', 'devolver')->count() }}</td>
                                                <td>{{ \Carbon\Carbon::parse($devolution->devolution_date)->format('d/m/Y') }}</td>
                                                <td><span style="color: green; font-weight: bold;">{{ number_format($totalLiquidacion, 2) }}€</span></td>
                                                <td><span style="color: blue; font-weight: bold;">{{ number_format($totalPagos, 2) }}€</span></td>
                                                <td>
                                                    <a href="{{ route('devolutions.show', $devolution->id) }}" class="btn btn-sm btn-light" title="Ver detalle">
                                                        <img src="{{url('assets/form-groups/eye.svg')}}" alt="" width="12">
                                                    </a>
                                                    <a href="{{ route('devolutions.edit', $devolution->id) }}" class="btn btn-sm btn-light" title="Editar">
                                                        <img src="{{url('assets/form-groups/edit.svg')}}" alt="" width="12">
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-devolucion" 
                                                            data-id="{{ $devolution->id }}" data-name="Devolución #{{ $devolution->id }}" title="Eliminar">
                                                        <i class="ri-delete-bin-6-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <br>
                    </div>

                    <div class="{{ count($devolutions ?? []) == 0 ? '' : 'd-none' }}">
                        <div class="d-flex align-items-center gap-1">

                            
                            <div class="empty-tables">

                                <div style="padding: 44px 12px">
                                    <img src="{{url('icons/participaciones.svg')}}" alt="" width="110px">
                                </div>

                                <h3 class="mb-0">No hay Devoluciones</h3>
                                <small>Añade Devoluciones</small>

                                <br>

                                <a href="{{ route('devolutions.create') }}" style="border-radius: 30px; width: 150px;" class="btn btn-md btn-dark mt-2"><i style="position: relative; top: 2px;" class="ri-add-line"></i> Añadir</a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')

<script>
$(document).ready(function() {
    console.log('Inicializando DataTable de devoluciones');
    
    // Inicializar DataTable con datos ya cargados en el HTML
    let tablaDevoluciones = $('#tabla-devoluciones').DataTable({
        "ordering": false,
        "scrollX": true,
        "scrollCollapse": true,
        orderCellsTop: true,
        fixedHeader: true,
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        "pageLength": 25,
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        "initComplete": function(settings, json) {
            console.log('DataTable inicializado');
            
            // Configurar filtros inline
            var api = this.api();
            
            api.columns().eq(0).each(function (colIdx) {
                var cell = $('.filters th').eq($(api.column(colIdx).header()).index());
                var title = $(cell).text();
                if ($(cell).hasClass('no-filter')) {
                    $(cell).addClass('sorting_disabled').html(title);
                } else {
                    $(cell).addClass('sorting_disabled').html('<input type="text" class="inline-fields" placeholder="' + title + '" />');
                }

                $('input', $('.filters th').eq($(api.column(colIdx).header()).index()))
                    .off('keyup change')
                    .on('keyup change', function (e) {
                        e.stopPropagation();
                        $(this).attr('title', $(this).val());
                        var regexr = '({search})';
                        var cursorPosition = this.selectionStart;
                        
                        api.column(colIdx).search(
                            (this.value != ''
                                ? regexr.replace('{search}', '(((' + this.value + ')))')
                                : ''),
                            this.value != '',
                            this.value == ''
                        ).draw();

                        $(this).focus()[0].setSelectionRange(cursorPosition, cursorPosition);
                    });
            });
        }
    });

    // Event listener para eliminar devolución
    $(document).on('click', '.btn-eliminar-devolucion', function() {
        const devolutionId = $(this).data('id');
        const devolutionName = $(this).data('name');
        
        if (confirm(`¿Estás seguro de que deseas eliminar ${devolutionName}?`)) {
            $.ajax({
                url: `/devolutions/${devolutionId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        mostrarMensaje('Devolución eliminada correctamente', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        mostrarMensaje(response.message || 'Error al eliminar la devolución', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al eliminar:', error);
                    mostrarMensaje('Error al eliminar la devolución', 'error');
                }
            });
        }
    });
    
    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo) {
        const alertClass = tipo === 'success' ? 'alert-success' : 
                          tipo === 'warning' ? 'alert-warning' : 
                          tipo === 'error' ? 'alert-danger' : 'alert-info';
       
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.page-title-box').after(alertHtml);
        
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>

@endsection
