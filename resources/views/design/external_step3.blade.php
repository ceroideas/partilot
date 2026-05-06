@extends('layouts.layout')

@section('title', 'Diseño e Impresión - Pago')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('design.external.list') }}">Diseño externo</a></li>
                        <li class="breadcrumb-item active">Pago</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e impresión PARTILOT</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex p-2 mb-3" style="align-items: center; justify-content: center;">
                        <div class="form-wizard-element" style="width: 220px;">
                            <span style="top: -4px; margin-right: 8px;">1</span>
                            <label>Indicaciones <br> / Archivos</label>
                        </div>
                        <div class="form-wizard-element" style="width: 220px;">
                            <span style="top: -4px; margin-right: 8px;">2</span>
                            <label>Resumen</label>
                        </div>
                        <div class="form-wizard-element active" style="width: 220px;">
                            <span style="top: -4px; margin-right: 8px;">3</span>
                            <label>Pago Diseño <br> e Impresión</label>
                        </div>
                    </div>

                    <div class="form-card bs">
                        <h4 class="mb-0 mt-1">Pago Diseño e Impresión</h4>
                        <small><i>Pantalla de pago mock (lista para integrar con Stripe).</i></small>

                        <form action="{{ route('design.external.sendInvitation') }}" method="POST" id="partilotPaymentForm" class="mt-4">
                            @csrf
                            <input type="hidden" name="confirm_simulated_payment" value="1">

                            <div class="payment-mock-wrap">
                                <div class="row g-4">
                                    <div class="col-lg-5">
                                        <div class="payment-info-card">
                                            <div class="payment-amount-box">
                                                <div class="payment-amount-label">Importe</div>
                                                <div class="payment-amount-value">{{ number_format(($quote['total'] ?? 0), 2, ',', '.') }}€</div>
                                            </div>
                                            <div class="payment-info-line"><span>Comercio:</span> <strong>PARTILOT</strong></div>
                                            <div class="payment-info-line"><span>Terminal:</span> <strong>TEST-001</strong></div>
                                            <div class="payment-info-line"><span>Pedido:</span> <strong>{{ $invitation->orden_id ?? ('ORD-' . $set->id) }}</strong></div>
                                            <div class="payment-info-line"><span>Fecha:</span> <strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
                                            <div class="payment-info-line"><span>Descripción:</span> <strong>Diseño e Impresión</strong></div>
                                        </div>
                                    </div>

                                    <div class="col-lg-7">
                                        <div class="payment-card-form">
                                            <h5 class="mb-3">PAGAR CON TARJETA</h5>
                                            <div class="mb-3">
                                                <label class="form-label">Nº Tarjeta</label>
                                                <input type="text" class="form-control" placeholder="4242 4242 4242 4242">
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Mes</label>
                                                    <input type="text" class="form-control" placeholder="12">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Año</label>
                                                    <input type="text" class="form-control" placeholder="29">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">CVC</label>
                                                    <input type="text" class="form-control" placeholder="123">
                                                </div>
                                            </div>
                                            <div class="form-text mt-3">
                                                Integración Stripe pendiente: este formulario es mock visual para flujo operativo.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mt-4">
                        <a href="{{ route('design.external.step2') }}" class="btn btn-dark rounded-pill">
                            <i class="ri-arrow-left-line me-1"></i> Atrás
                        </a>
                        <button type="submit" form="partilotPaymentForm" class="btn btn-warning rounded-pill px-4 text-dark fw-semibold">Pagar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.payment-mock-wrap {
    background: #f2f3f5;
    border: 1px solid #e3e5e8;
    border-radius: 12px;
    padding: 18px;
}
.payment-info-card,
.payment-card-form {
    background: #fff;
    border: 1px solid #e3e5e8;
    border-radius: 10px;
    padding: 16px;
    min-height: 100%;
}
.payment-amount-box {
    background: #18a0db;
    border-radius: 8px;
    padding: 14px;
    color: #fff;
    margin-bottom: 14px;
}
.payment-amount-label {
    font-size: 0.9rem;
    opacity: 0.95;
}
.payment-amount-value {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}
.payment-info-line {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #eef0f2;
    padding: 8px 0;
    font-size: 0.95rem;
}
</style>
@endsection
