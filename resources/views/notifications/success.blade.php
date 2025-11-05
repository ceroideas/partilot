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

                                <div class="form-wizard-element">
                                    
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

                                    <img width="26px" src="{{url('icons_/selec_sorteo.svg')}}" alt="">

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
                                
                                <!-- Modal de éxito -->
                                <div class="modal fade show" id="successModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title">Enviado</h5>
                                            </div>
                                            <div class="modal-body text-center">
                                                <div class="mb-3">
                                                    <i class="ri-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                                </div>
                                                <p class="mb-2"><strong>Tu mensaje ha sido enviado con éxito</strong></p>
                                                <p class="mb-2">✉️ Notificaciones guardadas: <strong>{{ $successCount }}</strong> destinatario(s)</p>
                                                
                                                @if(session('firebase_tokens_count'))
                                                    @if(session('firebase_success'))
                                                        <p class="mb-0 text-success">
                                                            📱 Notificaciones push enviadas: <strong>{{ session('firebase_tokens_count') }}</strong> dispositivo(s)
                                                        </p>
                                                    @else
                                                        <p class="mb-0 text-warning">
                                                            ⚠️ Notificaciones push: Error al enviar a {{ session('firebase_tokens_count') }} dispositivo(s)
                                                            <br><small>Revisa los logs para más detalles</small>
                                                        </p>
                                                    @endif
                                                @else
                                                    <p class="mb-0 text-muted">
                                                        <small>No hay dispositivos con notificaciones push activadas</small>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="modal-footer border-0 justify-content-center">
                                                <a href="{{route('notifications.index')}}" class="btn btn-primary" style="background-color: #e78307; border-color: #e78307;">
                                                    Aceptar
                                                </a>
                                            </div>
                                        </div>
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
$(document).ready(function() {
    // Auto-redirect después de 3 segundos
    setTimeout(function() {
        window.location.href = "{{ route('notifications.index') }}";
    }, 3000);
});
</script>

@endsection
