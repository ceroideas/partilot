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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('design.submitPrintOrder', $design->id) }}" id="sendToPrintForm">
        @csrf
        <input type="hidden" name="stripe_payment_intent_id" id="stripe_payment_intent_id" value="">
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
                                <input type="number" min="1" max="1000" name="participations_per_book" class="form-control" value="{{ old('participations_per_book', $defaults['participations_per_book'] ?? 50) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Trasera</label>
                                <select name="back_mode" class="form-select">
                                    <option value="bw" {{ old('back_mode', $defaults['back_mode'] ?? 'bw') === 'bw' ? 'selected' : '' }}>Blanco y negro</option>
                                    <option value="color" {{ old('back_mode', $defaults['back_mode'] ?? '') === 'color' ? 'selected' : '' }}>Color</option>
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
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-semibold">TOTAL</span>
                            <strong class="fs-5" id="quote-total-display">{{ number_format(($quote['total'] ?? 0), 2, ',', '.') }}€</strong>
                        </div>

                        @if(!($stripePaymentEnabled ?? false))
                            <div class="alert alert-warning small mb-3">
                                Stripe no está configurado. Añade las claves en <strong>Configuración → Imprenta</strong> para poder cobrar el envío a imprenta.
                            </div>
                        @else
                            <div class="payment-card-form border rounded p-3 mb-3">
                                <h6 class="mb-2">Pago con tarjeta</h6>
                                <div id="stripe-card-element" class="form-control" style="padding-top: 12px; min-height: 46px;"></div>
                                <div id="stripe-card-errors" class="text-danger small mt-2 d-none"></div>
                                <p class="form-text small mb-0 mt-2">El importe se recalcula al pulsar «Pagar y enviar» según la configuración del formulario.</p>
                            </div>
                        @endif

                        <div class="mt-auto d-flex justify-content-between">
                            <a href="{{ route('design.summary', $design->id) }}" class="btn btn-dark">
                                <i class="ri-arrow-left-line me-1"></i> Volver
                            </a>
                            @if($stripePaymentEnabled ?? false)
                                <button type="button" id="btn-stripe-pay" class="btn btn-warning text-dark fw-semibold">
                                    <i class="ri-bank-card-line me-1"></i> Pagar y enviar a imprenta
                                </button>
                            @else
                                <button type="button" class="btn btn-warning text-dark fw-semibold" disabled title="Configura Stripe en Imprenta">
                                    <i class="ri-send-plane-line me-1"></i> Enviar a imprenta
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@if($stripePaymentEnabled ?? false)
@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
(() => {
    const payBtn = document.getElementById('btn-stripe-pay');
    const errorBox = document.getElementById('stripe-card-errors');
    const paymentIntentInput = document.getElementById('stripe_payment_intent_id');
    const form = document.getElementById('sendToPrintForm');
    const cardContainer = document.getElementById('stripe-card-element');
    if (!payBtn || !errorBox || !paymentIntentInput || !form || !cardContainer) return;

    let stripe = null;
    let card = null;
    let publishableKey = @json($stripePublishableKey ?? '');

    function showError(msg) {
        errorBox.textContent = msg || 'Error procesando el pago.';
        errorBox.classList.remove('d-none');
    }

    function clearError() {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }

    function mountCard() {
        if (!publishableKey) {
            throw new Error('Stripe no configurado.');
        }
        stripe = Stripe(publishableKey);
        const elements = stripe.elements();
        if (card) {
            try { card.unmount(); } catch (e) {}
        }
        card = elements.create('card', { hidePostalCode: true });
        card.mount('#stripe-card-element');
    }

    async function createPaymentIntent() {
        const formData = new FormData(form);
        const res = await fetch(@json(route('design.createPrintOrderPaymentIntent', $design->id)), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                'Accept': 'application/json',
            },
            body: formData,
        });
        const data = await res.json();
        if (!res.ok || !data.ok) {
            throw new Error(data.message || 'No se pudo iniciar el pago.');
        }
        return data;
    }

    payBtn.addEventListener('click', async () => {
        try {
            clearError();
            payBtn.disabled = true;

            if (!stripe || !card) {
                mountCard();
            }

            const intentData = await createPaymentIntent();
            if (intentData.publishable_key) {
                publishableKey = intentData.publishable_key;
            }

            const result = await stripe.confirmCardPayment(intentData.client_secret, {
                payment_method: { card },
            });

            if (result.error) {
                showError(result.error.message || 'Pago rechazado.');
                payBtn.disabled = false;
                return;
            }

            if (!result.paymentIntent || result.paymentIntent.status !== 'succeeded') {
                showError('El pago no se confirmó correctamente.');
                payBtn.disabled = false;
                return;
            }

            paymentIntentInput.value = result.paymentIntent.id;
            form.submit();
        } catch (e) {
            showError(e.message || 'Error al procesar el pago.');
            payBtn.disabled = false;
        }
    });

    mountCard().catch((e) => showError(e.message || 'No se pudo inicializar Stripe.'));
})();
</script>
@endsection
@endif
