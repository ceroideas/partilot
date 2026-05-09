@extends('layouts.layout')

@section('title', 'Enviar a imprenta')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('design.summary', $design->id) }}">Resumen</a></li>
                        <li class="breadcrumb-item active">Enviar a imprenta</li>
                    </ol>
                </div>
                <h4 class="page-title">Enviar a imprenta</h4>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('design.submitPrintOrder', $design->id) }}">
        @csrf
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-2">Configuración del envío</h5>
                        <p class="text-muted small mb-3">Define los parámetros operativos para generar el presupuesto de imprenta.</p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Formato impresión</label>
                                <select name="print_size" class="form-select">
                                    <option value="a3_6" {{ ($defaults['print_size'] ?? '') === 'a3_6' ? 'selected' : '' }}>A3 - 6 participaciones</option>
                                    <option value="a3_8" {{ ($defaults['print_size'] ?? '') === 'a3_8' ? 'selected' : '' }}>A3 - 8 participaciones</option>
                                    <option value="custom" {{ ($defaults['print_size'] ?? '') === 'custom' ? 'selected' : '' }}>Personalizado</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Participaciones por taco</label>
                                <input type="number" min="1" max="1000" name="participations_per_book" class="form-control" value="{{ $defaults['participations_per_book'] ?? 50 }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Trasera</label>
                                <select name="back_mode" class="form-select">
                                    <option value="bw" {{ ($defaults['back_mode'] ?? 'bw') === 'bw' ? 'selected' : '' }}>Blanco y negro</option>
                                    <option value="color" {{ ($defaults['back_mode'] ?? '') === 'color' ? 'selected' : '' }}>Color</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observaciones para imprenta</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Indicaciones de entrega, cortes, empaquetado, etc.">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="mb-3">Resumen de presupuesto</h5>
                        <div class="d-flex justify-content-between small mb-2">
                            <span>Set</span>
                            <strong>{{ $design->set->set_name ?? ('#'.$design->set_id) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span>Participaciones</span>
                            <strong>{{ number_format($quote['total_participations'] ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span>Tacos estimados</span>
                            <strong>{{ $quote['books'] ?? 0 }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between small mb-2 align-items-start">
                            <span>Diseño @if(!empty($quote['design_fee_waived']))<span class="d-block text-muted fw-normal" style="font-size:0.85em;">Sin cargo (realizado en PARTILOT)</span>@endif</span>
                            <strong>{{ number_format(($quote['subtotal']['design'] ?? 0), 2, ',', '.') }}€</strong>
                        </div>
                        <div class="d-flex justify-content-between small mb-2"><span>Participaciones</span><strong>{{ number_format(($quote['subtotal']['participation'] ?? 0), 2, ',', '.') }}€</strong></div>
                        <div class="d-flex justify-content-between small mb-2"><span>Trasera</span><strong>{{ number_format(($quote['subtotal']['back'] ?? 0), 2, ',', '.') }}€</strong></div>
                        <div class="d-flex justify-content-between small mb-2"><span>Tacos</span><strong>{{ number_format(($quote['subtotal']['book'] ?? 0), 2, ',', '.') }}€</strong></div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-semibold">TOTAL</span>
                            <strong class="fs-5">{{ number_format(($quote['total'] ?? 0), 2, ',', '.') }}€</strong>
                        </div>
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="{{ route('design.summary', $design->id) }}" class="btn btn-dark">
                                <i class="ri-arrow-left-line me-1"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-semibold">
                                <i class="ri-send-plane-line me-1"></i> Enviar a imprenta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

