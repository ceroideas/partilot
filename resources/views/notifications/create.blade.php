@extends('layouts.layout')

@section('title','Notificaciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Notificaciones</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </div>
                <h4 class="page-title">Notificaciones</h4>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title">
                        Selección
                    </h4>

                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <div class="form-card bs mb-3">

                                <div class="form-wizard-element active">
                                    
                                    <span>
                                        1
                                    </span>

                                    <img src="{{url('assets/entidad.svg')}}" alt="">

                                    <label>
                                        Selección Tipo
                                    </label>

                                </div>

                                <div class="form-wizard-element">
                                    
                                    <span>
                                        2
                                    </span>

                                    <img width="26px" src="{{url('icons/selec_sorteo.svg')}}" alt="">

                                    <label>
                                        Selección Destino
                                    </label>

                                </div>

                                <div class="form-wizard-element">
                                    
                                    <span>
                                        3
                                    </span>

                                    <img src="{{url('assets/entidad.svg')}}" alt="">

                                    <label>
                                        Mensaje
                                    </label>

                                </div>
                                
                            </div>

                            <a href="{{route('notifications.index')}}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span></a>
                        </div>
                        <div class="col-md-9">
                            <div class="form-card bs" style="min-height: 658px;">
                                <form action="{{ route('notifications.store-type') }}" method="POST">
                                    @csrf
                                    <h4 class="mb-0 mt-1">
                                        Administración / Entidad
                                    </h4>
                                    <small><i>Selecciona una Opción</i></small>

                                    <br>
                                    <br>

                                    <!-- Opciones de tipo de notificación -->
                                    <div id="all-options-notification">
                                        <div class="mt-4 text-center">
                                            <div id="notification-type-buttons">
                                                <button type="button" class="btn btn-light btn-xl text-center m-2 bs" id="btn-notificacion-administracion" style="border: 1px solid #f0f0f0; padding: 16px; width: 180px; border-radius: 16px; position: relative;">
                                                    <img class="mt-2 mb-1" src="{{url('assets/vendedor.svg')}}" alt="" width="60%">
                                                    <h4 class="mb-0">Administración</h4>
                                                </button>

                                                <button type="button" class="btn btn-light btn-xl text-center m-2 bs" id="btn-notificacion-entidad" style="border: 1px solid #f0f0f0; padding: 16px; width: 160px; border-radius: 16px;">
                                                    <img class="mt-2 mb-1" src="{{url('assets/vendedor.svg')}}" alt="" width="60%">
                                                    <h4 class="mb-0">Entidad</h4>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden input para el tipo seleccionado -->
                                    <input type="hidden" name="notification_type" id="notification_type" value="">

                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <button type="submit" id="submit-btn" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2" disabled>
                                                Aceptar
                                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-arrow-right-circle-line"></i>
                                            </button>
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
$(document).ready(function() {
    // Manejar clic en botón de administración
    $('#btn-notificacion-administracion').click(function() {
        // Remover clase activa de todos los botones
        $('.btn').removeClass('btn-primary').addClass('btn-light');
        
        // Agregar clase activa al botón seleccionado
        $(this).removeClass('btn-light').addClass('btn-primary');
        
        // Establecer el valor del tipo
        $('#notification_type').val('administration');
        
        // Habilitar el botón de envío
        $('#submit-btn').prop('disabled', false);
    });

    // Manejar clic en botón de entidad
    $('#btn-notificacion-entidad').click(function() {
        // Remover clase activa de todos los botones
        $('.btn').removeClass('btn-primary').addClass('btn-light');
        
        // Agregar clase activa al botón seleccionado
        $(this).removeClass('btn-light').addClass('btn-primary');
        
        // Establecer el valor del tipo
        $('#notification_type').val('entity');
        
        // Habilitar el botón de envío
        $('#submit-btn').prop('disabled', false);
    });

    // Validar antes de enviar
    $('form').submit(function(e) {
        if ($('#notification_type').val() === '') {
            e.preventDefault();
            alert('Por favor selecciona un tipo de notificación');
        }
    });
});
</script>

@endsection
