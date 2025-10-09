@extends('layouts.layout')

@section('title','Editar Devolución')

@section('content')

<style>
    .form-wizard-element, .form-wizard-element label {
        cursor: pointer;
    }
    .form-check-input:checked {
        border-color: #333;
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
</style>

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('devolutions.index') }}">Devoluciones</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('devolutions.show', $id) }}">Ver Devolución</a></li>
                        <li class="breadcrumb-item active">Editar Devolución</li>
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
                            Editar Devolución
                        </h4>
                        <div>
                            <a href="{{ route('devolutions.show', $id) }}" class="btn btn-info me-2" style="border-radius: 30px;">
                                <i class="ri-eye-line me-2"></i>Ver
                            </a>
                            <a href="{{ route('devolutions.index') }}" class="btn btn-secondary" style="border-radius: 30px;">
                                <i class="ri-arrow-left-line me-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <br>

                    <!-- Formulario de edición -->
                    <form id="edit-devolution-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="resumen-devolucion">
                            <h5>Información de la Devolución</h5>
                            <div class="resumen-item">
                                <span>ID Devolución:</span>
                                <span>{{ $id }}</span>
                            </div>
                            <div class="resumen-item">
                                <span>Estado:</span>
                                <span class="badge bg-warning">En Edición</span>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Configuración</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Motivo de Devolución</label>
                                            <textarea class="form-control" name="return_reason" rows="3" placeholder="Ingresa el motivo de la devolución..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Comentarios Adicionales</label>
                                            <textarea class="form-control" name="comments" rows="3" placeholder="Comentarios adicionales..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Fecha de Devolución</label>
                                            <input type="date" class="form-control" name="return_date" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Hora de Devolución</label>
                                            <input type="time" class="form-control" name="return_time" value="{{ date('H:i') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('devolutions.show', $id) }}" class="btn btn-secondary" style="border-radius: 30px;">
                                <i class="ri-close-line me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success" style="border-radius: 30px; font-weight: bold;">
                                <i class="ri-save-line me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')

<script>
$(document).ready(function() {
    const devolutionId = {{ $id }};
    
    // Cargar información existente de la devolución
    cargarInformacionDevolucion();
    
    // Event listener para el formulario
    $('#edit-devolution-form').on('submit', function(e) {
        e.preventDefault();
        guardarDevolucion();
    });
    
    function cargarInformacionDevolucion() {
        // Por ahora, cargar información básica
        // En una implementación real, harías una llamada AJAX al backend
        console.log('Cargando información de devolución ID:', devolutionId);
        
        // Simular datos existentes
        $('textarea[name="return_reason"]').val('Devolución por liquidación');
        $('input[name="return_date"]').val('{{ date('Y-m-d') }}');
        $('input[name="return_time"]').val('{{ date('H:i') }}');
    }
    
    function guardarDevolucion() {
        const formData = new FormData($('#edit-devolution-form')[0]);
        
        // Mostrar loading
        const submitBtn = $('#edit-devolution-form button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line fa-spin me-2"></i>Guardando...');
        
        // Simular envío
        // En una implementación real, harías una llamada AJAX al backend
        setTimeout(() => {
            mostrarMensaje('Devolución actualizada correctamente', 'success');
            
            // Redirigir a la vista de detalle
            setTimeout(() => {
                window.location.href = "{{ route('devolutions.show', $id) }}";
            }, 1500);
            
        }, 2000);
        
        /*
        // Código real para envío AJAX:
        $.ajax({
            url: "{{ route('devolutions.update', $id) }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    mostrarMensaje('Devolución actualizada correctamente', 'success');
                    setTimeout(() => {
                        window.location.href = "{{ route('devolutions.show', $id) }}";
                    }, 1500);
                } else {
                    mostrarMensaje(response.message || 'Error al actualizar la devolución', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error de conexión al actualizar la devolución', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
        */
    }
    
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
