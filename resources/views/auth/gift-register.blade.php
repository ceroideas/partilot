<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Registro regalo | PARTILOT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ url('/') }}/logo.svg">
    <link href="{{ url('default') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('default') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <style>
        .group-login { border: 2px solid silver; padding: 5px 0; border-radius: 30px; background: #fff; display: flex; align-items: center; }
        .group-login input { border: none !important; box-shadow: none !important; background: transparent !important; }
        .group-login input[readonly] { color: #6c757d; }
        .pending-summary { background: #fff8f0; border: 2px solid #F59200; border-radius: 12px; padding: 16px 20px; margin-bottom: 8px; }
    </style>
</head>
<body class="auth-fluid-pages pb-0">
<div class="container py-4" style="max-width: 520px;">
    <div class="text-center mb-4">
        <img src="{{ url('/') }}/logo.svg" alt="PARTILOT" height="40">
        <h4 class="mt-3">Te han regalado una participación</h4>
        <div class="pending-summary text-start">
            <p class="mb-2 fw-semibold">{{ $gift->fromUser->name ?? 'Un usuario' }} te ha regalado una participación.</p>
            @if($gift->message)
                <p class="mb-2 small"><strong>Mensaje:</strong> {{ $gift->message }}</p>
            @endif
            <p class="mb-1 small text-muted"><strong>Entidad:</strong> {{ $gift->participation->set->entity->name ?? '—' }}</p>
            <p class="mb-0 small text-muted"><strong>Sorteo:</strong> {{ $gift->participation->set->reserve->lottery->name ?? '—' }}</p>
        </div>
        <p class="text-muted small mb-0">Regístrate con el correo indicado. Después podrás aceptar el regalo en la app.</p>
    </div>
    <div class="card">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="post" action="{{ route('gift-recipient.register.store', ['token' => $token]) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="group-login"><input type="email" class="form-control" value="{{ $gift->to_email }}" readonly></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre *</label>
                        <div class="group-login"><input type="text" name="name" class="form-control" required value="{{ old('name') }}"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Primer apellido</label>
                        <div class="group-login"><input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono móvil <span class="text-muted fw-normal">(opcional)</span></label>
                    <div class="group-login"><input type="tel" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                    @if(config('sms.enabled'))
                        <div class="input-group mt-2">
                            <input type="text" name="sms_code" class="form-control" placeholder="Código SMS" maxlength="{{ config('sms.code_length', 6) }}" value="{{ old('sms_code') }}">
                            <button type="button" class="btn btn-outline-secondary" id="btn-send-sms">Enviar código</button>
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de nacimiento *</label>
                    <div class="group-login"><input type="date" name="birthday" class="form-control" required value="{{ old('birthday') }}"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña *</label>
                    <div class="group-login"><input type="password" name="password" class="form-control" required minlength="6"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Repetir contraseña *</label>
                    <div class="group-login"><input type="password" name="password_confirmation" class="form-control" required minlength="6"></div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="aceptar_condiciones" value="1" id="cond" required>
                    <label class="form-check-label" for="cond">Acepto las condiciones de uso</label>
                </div>
                <button type="submit" class="btn w-100 text-white" style="background:#F59200;border-radius:30px;font-weight:bold;">Crear cuenta</button>
            </form>
        </div>
    </div>
</div>
@if(config('sms.enabled'))
<script>
(function () {
    const btn = document.getElementById('btn-send-sms');
    btn?.addEventListener('click', function () {
        const phone = document.querySelector('input[name=phone]')?.value;
        if (!phone) { alert('Introduce el teléfono primero.'); return; }
        fetch('{{ route('gift-recipient.sms-code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ phone })
        }).then(r => r.json()).then(d => alert(d.message || (d.success ? 'Código enviado.' : 'Error')));
    });
})();
</script>
@endif
</body>
</html>
