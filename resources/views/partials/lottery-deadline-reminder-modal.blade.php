@if(!empty($lotteryDeadlineModalAlerts))
<div class="modal fade" id="lotteryDeadlineReminderModal" tabindex="-1" aria-labelledby="lotteryDeadlineReminderModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="lotteryDeadlineReminderModalLabel">
                    <i class="mdi mdi-alert-outline text-warning me-1"></i>
                    Aviso de fecha límite de devolución
                </h5>
            </div>
            <div class="modal-body pt-2">
                @foreach($lotteryDeadlineModalAlerts as $alert)
                    <div class="alert alert-warning mb-3 lottery-deadline-alert-item" data-alert-key="{{ $alert['key'] }}">
                        <p class="mb-2">{{ $alert['message'] }}</p>
                        <p class="mb-0 small text-muted">
                            <strong>Sorteo:</strong> {{ $alert['lottery_name'] }}
                            @if(auth()->user()?->isAdministration() || auth()->user()?->isSuperAdmin())
                                · <strong>Entidad:</strong> {{ $alert['entity_name'] }}
                            @endif
                        </p>
                    </div>
                @endforeach
                <p class="mb-0">
                    <a href="{{ route('devolutions.index') }}" class="fw-semibold">Ir al módulo de Devoluciones</a>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-dark rounded-pill px-4" id="lotteryDeadlineReminderDismiss">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>
@endif
