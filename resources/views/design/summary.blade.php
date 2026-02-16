@extends('layouts.layout')

@section('title', 'Resumen - Diseño guardado')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item active">Resumen</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño guardado</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <p class="text-success mb-4 fs-5">
                        <i class="ri-checkbox-circle-line me-1"></i>
                        La configuración del diseño se ha guardado correctamente.
                    </p>
                    <p class="text-muted mb-4">
                        Puedes descargar los PDF generados o volver al listado de diseños.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                        <a target="_blank" href="{{ route('design.exportParticipationPdf', $design->id) }}" class="btn btn-primary">
                            <i class="ri-file-pdf-line me-1"></i> Descargar PDF participaciones
                        </a>
                        @php
                            $hasCover = !empty($design->cover_html);
                            $hasBack = !empty($design->back_html);
                        @endphp
                        @if($hasCover && $hasBack)
                        <a target="_blank" href="{{ route('design.exportCoverAndBackPdf', $design->id) }}" class="btn btn-outline-primary">
                            <i class="ri-file-pdf-line me-1"></i> Descargar PDF portada y trasera
                        </a>
                        @endif
                    </div>

                    <hr class="my-4">

                    <a href="{{ route('design.index') }}" class="btn btn-dark">
                        <i class="ri-arrow-left-line me-1"></i> Volver a Diseño e impresión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
