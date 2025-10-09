@extends('layouts.layout')

@section('title','Devoluciones')

@section('content')

<style>
    .form-wizard-element, .form-wizard-element label {
        cursor: pointer;
    }
    .form-check-input:checked {
        border-color: #333;
    }

    .devolucion-paso {
        transition: all 0.3s ease;
    }

    .devolucion-paso table {
        margin-top: 20px;
    }

    .devolucion-paso .btn-seleccionar {
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
    }

    .devolucion-paso .btn-volver {
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
    }

    /* Animación para transiciones entre pasos */
    .devolucion-paso.fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Estilos para las participaciones a devolver */
    .participacion-item {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s ease;
    }

    .participacion-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .participacion-icon {
        width: 40px;
        height: 40px;
        background: #333;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .participacion-info {
        flex-grow: 1;
    }

    .participacion-numero {
        font-weight: bold;
        color: #333;
        margin-bottom: 4px;
    }

    .participacion-fecha {
        color: #666;
        font-size: 0.9em;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .participacion-estado {
        background: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        font-weight: bold;
        margin-top: 4px;
        display: inline-block;
    }

    .btn-eliminar-participacion {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-eliminar-participacion:hover {
        background: #c82333;
        transform: scale(1.1);
    }

    .grid-participaciones {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }

    /* Estilos para el resumen de devolución */
    .resumen-devolucion {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .resumen-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .resumen-item:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 1.1em;
    }

    /* Estilos para estado vacío */
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

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">
                            Gestión de Devoluciones
                        </h4>
                    </div>

                    <br>

                    <!-- Estado vacío (se mostrará cuando no hay devoluciones) -->
                    <div id="estado-vacio" class="d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <div class="empty-tables text-center">
                            <div>
                                <img src="{{url('icons/participaciones.svg')}}" alt="" width="120px" style="margin-top: 20px; opacity: 0.3;">
                            </div>
                            <h3 class="mb-0 mt-3">No hay Devoluciones</h3>
                            <small class="text-muted">Gestiona Devoluciones</small>
                            <br>
                            <a href="{{ route('devolutions.create') }}" class="btn btn-dark mt-3" style="border-radius: 30px; width: 150px;">
                                <i class="ri-add-line me-2"></i>Nueva
                            </a>
                        </div>
                    </div>

                    <!-- Tabla de devoluciones (se mostrará cuando haya datos) -->
                    <div id="tabla-contenido" style="display: none;">
                        <div class="table-responsive">
                            <table id="tabla-devoluciones" class="table table-striped nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Entidad</th>
                                        <th>Sorteo</th>
                                        <th>Vendedor</th>
                                        <th>Participaciones</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
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
    // Inicializar DataTable
    let tablaDevoluciones = $('#tabla-devoluciones').DataTable({
        "select": { style: "single" },
        "ordering": true,
        "sorting": true,
        "scrollX": true,
        "scrollCollapse": true,
        "language": {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        "pageLength": 25,
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "columnDefs": [
            {
                "targets": -1, // Última columna (Acciones)
                "orderable": false,
                "searchable": false
            }
        ],
        "ajax": {
            "url": "{{ route('devolutions.data') }}",
            "type": "GET",
            "dataSrc": function(json) {
                // Si no hay datos, mostrar estado vacío
                if (!json.data || json.data.length === 0) {
                    $('#estado-vacio').show();
                    $('#tabla-contenido').hide();
                    return [];
                } else {
                    // Si hay datos, mostrar tabla
                    $('#estado-vacio').hide();
                    $('#tabla-contenido').show();
                    return json.data;
                }
            },
            "error": function(xhr, error, thrown) {
                console.error('Error al cargar devoluciones:', error);
                $('#estado-vacio').show();
                $('#tabla-contenido').hide();
                mostrarMensaje('Error al cargar los datos de devoluciones', 'error');
            }
        },
        "columns": [
            { "data": "id" },
            { "data": "entity_name" },
            { "data": "lottery_name" },
            { "data": "seller_name" },
            { "data": "participations_count" },
            { "data": "return_date" },
            { "data": "status" },
            { "data": "actions", "orderable": false }
        ]
    });

    // Función para recargar la tabla
    window.reloadDevoluciones = function() {
        tablaDevoluciones.ajax.reload(null, false); // false = mantener la página actual
    };

    // Event listener para eliminar devolución
    $(document).on('click', '.btn-eliminar-devolucion', function() {
        const devolutionId = $(this).data('id');
        const devolutionName = $(this).data('name');
        
        if (confirm(`¿Estás seguro de que quieres eliminar la devolución "${devolutionName}"?`)) {
            $.ajax({
                url: `{{ url('devolutions') }}/${devolutionId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        mostrarMensaje('Devolución eliminada correctamente', 'success');
                        tablaDevoluciones.ajax.reload();
                    } else {
                        mostrarMensaje(response.message || 'Error al eliminar la devolución', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    mostrarMensaje('Error de conexión al eliminar la devolución', 'error');
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
        
        // Insertar alerta en la parte superior de la página
        $('.page-title-box').after(alertHtml);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>

@endsection
