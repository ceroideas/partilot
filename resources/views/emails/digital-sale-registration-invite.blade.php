@extends('emails.layouts.base')

@php($title = 'Participaciones digitales - Partilot')
@php($heading = 'Tienes participaciones esperándote')
@php($pending = $pending->ensureLinkCode())

@section('content')
<p>Hola,</p>
<p>
    Se han reservado <strong>{{ $pending->quantity }}</strong>
    {{ $pending->quantity === 1 ? 'participación digital' : 'participaciones digitales' }}
    a tu nombre para el sorteo <strong>{{ $pending->lottery->name ?? '—' }}</strong>
    ({{ $pending->entity->name ?? '—' }}).
</p>

<div style="background:#fff8f0;border:2px solid #F59200;border-radius:12px;padding:20px;margin:24px 0;text-align:center;">
    <p style="margin:0 0 8px;font-size:14px;color:#666;text-transform:uppercase;letter-spacing:1px;">
        Código para reclamar tus participaciones
    </p>
    <p style="margin:0;font-size:32px;letter-spacing:8px;font-weight:bold;color:#212529;font-family:monospace;">
        {{ $pending->link_code }}
    </p>
    <p style="margin:14px 0 0;font-size:13px;color:#555;">
        Si no estás registrado, completa el registro con el botón de abajo.<br>
        En la app Partilot también puedes usar <strong>Mi cartera → Vincular con código</strong>.
    </p>
</div>

<div class="info-box">
    <p>
        Regístrate con este mismo correo (<strong>{{ $pending->email }}</strong>)
        antes del <strong>{{ $pending->valid_until?->format('d/m/Y H:i') }}</strong>
        para activar las participaciones en tu cartera.
    </p>
</div>

<p style="text-align:center; margin: 24px 0;">
    <a href="{{ $pending->registrationUrl() }}" style="display:inline-block;padding:12px 22px;background:#F59200;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">
        Registrarme y activar participaciones
    </a>
</p>

<p style="font-size: 13px; color:#666;">
    Si el correo no llega o hay un error en la dirección, conserva el código <strong>{{ $pending->link_code }}</strong>
    y vincúlalo al registrarte en la web o en la app.
</p>
<p style="font-size: 13px; color:#666;">
    Si no completas el registro a tiempo, las participaciones volverán a estar disponibles para la venta.
</p>
@endsection
