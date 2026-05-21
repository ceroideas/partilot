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
                    @if(session('success'))
                        <div class="alert alert-success text-start">{{ session('success') }}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning text-start">{{ session('warning') }}</div>
                    @endif
                    <p class="text-success mb-4 fs-5">
                        <i class="ri-checkbox-circle-line me-1"></i>
                        La configuración del diseño se ha guardado correctamente.
                    </p>
                    <p class="text-muted mb-4">
                        Puedes descargar los PDF generados o volver al listado de diseños.
                    </p>

                    @php
                        $isDigitalSet = $design->set
                            && ($design->set->digital_participations ?? 0) > 0
                            && (int) ($design->set->physical_participations ?? 0) === 0;
                        $hasCover = !empty($design->cover_html);
                        $hasBack = !empty($design->back_html);
                    @endphp
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                        @if($isDigitalSet)
                            <a target="_blank" href="{{ route('design.digitalParticipationImage', $design->id) }}" class="btn btn-primary">
                                <i class="ri-image-line me-1"></i> Descargar imagen participación digital
                            </a>
                        @else
                        <button type="button"
                            class="btn btn-primary js-design-pdf-async"
                            data-async-url="{{ route('design.exportParticipationPdfAsync', $design->id) }}"
                            data-pdf-dialog="participation"
                            data-total-participations="{{ $design->set ? (int)$design->set->total_participations : 0 }}"
                            data-title="Participaciones">
                            <i class="ri-file-pdf-line me-1"></i> Descargar PDF participaciones
                        </button>
                        @endif
                        @if(!$isDigitalSet && $hasCover)
                        <button type="button"
                            class="btn btn-outline-primary js-design-pdf-async"
                            data-async-url="{{ route('design.exportCoverPdfAsync', $design->id) }}"
                            data-title="Portadas">
                            <i class="ri-file-pdf-line me-1"></i> PDF portadas (tacos)
                        </button>
                        @endif
                        @if(!$isDigitalSet && $hasBack)
                        <button type="button"
                            class="btn btn-outline-secondary js-design-pdf-async"
                            data-async-url="{{ route('design.exportBackPdfAsync', $design->id) }}"
                            data-pdf-dialog="backs"
                            data-total-participations="{{ $design->set ? (int)$design->set->total_participations : 0 }}"
                            data-title="Traseras">
                            <i class="ri-file-pdf-line me-1"></i> PDF traseras
                        </button>
                        @endif
                        @if(!$isDigitalSet)
                            @if(!empty($printOrderLock['locked']))
                                <button type="button" class="btn btn-outline-warning text-dark" disabled title="{{ $printOrderLock['message'] ?? '' }}">
                                    <i class="ri-send-plane-line me-1"></i> Enviar a imprenta
                                </button>
                            @else
                                <a href="{{ route('design.sendToPrint', $design->id) }}" class="btn btn-warning text-dark">
                                    <i class="ri-send-plane-line me-1"></i> Enviar a imprenta
                                </a>
                            @endif
                        @endif
                    </div>

                    @if(!empty($latestPrintOrder))
                        <div class="alert alert-light border text-start mx-auto" style="max-width: 540px;">
                            <div class="d-flex justify-content-between">
                                <span>Última orden</span>
                                <strong>{{ $latestPrintOrder->order_code }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Estado</span>
                                <strong>{{ \App\Models\PrintOrder::statusLabel((string) $latestPrintOrder->status) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Importe</span>
                                <strong>{{ number_format((float) $latestPrintOrder->quoted_amount, 2, ',', '.') }}€</strong>
                            </div>
                        </div>
                    @endif

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

@section('scripts')
    @include('design.partials.async_design_pdf')
@endsection
