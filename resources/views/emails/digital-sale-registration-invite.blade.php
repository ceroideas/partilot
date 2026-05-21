@extends('emails.layouts.base')

@php($title = 'Participaciones digitales - Partilot')
@php($heading = 'Tienes participaciones esperándote')

@section('content')
<p>Hola,</p>
<p>
    Se han reservado <strong>{{ $pending->quantity }}</strong>
    {{ $pending->quantity === 1 ? 'participación digital' : 'participaciones digitales' }}
    a tu nombre para el sorteo <strong>{{ $pending->lottery->name ?? '—' }}</strong>
    ({{ $pending->entity->name ?? '—' }}).
</p>

<div class="info-box">
    <p>
        Tienes <strong>{{ $pending->quantity }}</strong>
        {{ $pending->quantity === 1 ? 'participación digital pendiente' : 'participaciones digitales pendientes' }}
        de asignar a tu cuenta.
    </p>
    <p>
        Regístrate con este mismo correo (<strong>{{ $pending->email }}</strong>)
        antes del <strong>{{ $pending->valid_until?->format('d/m/Y H:i') }}</strong>
        usando el enlace de abajo. Al completar el formulario, las participaciones se vincularán automáticamente.
    </p>
</div>

<p style="text-align:center; margin: 24px 0;">
    <a href="{{ $pending->registrationUrlForShare() }}" style="display:inline-block;padding:12px 22px;background:#F59200;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">
        Registrarme y activar participaciones
    </a>
</p>

<p style="font-size: 13px; color:#666;">
    Si ya tienes cuenta en Partilot, inicia sesión en la app con <strong>{{ $pending->email }}</strong>
    para ver tus participaciones.
</p>
<p style="font-size: 13px; color:#666;">
    Si no completas el registro a tiempo, las participaciones volverán a estar disponibles para la venta.
</p>
@endsection
