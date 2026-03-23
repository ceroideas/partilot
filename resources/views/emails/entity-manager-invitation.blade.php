@extends('emails.layouts.base')

@php($title = 'Invitación Gestor - Partilot')
@php($heading = 'Invitación de gestor')

@section('content')
<p>Hola {{ $managerUser->name ?? 'Gestor' }},</p>
@if($manager->is_primary)
<p>Has sido designado/a como <strong>gestor responsable</strong> de la entidad <strong>{{ $entity->name }}</strong> en Partilot.</p>
@else
<p>Has sido invitado como gestor de la entidad <strong>{{ $entity->name }}</strong> en Partilot.</p>
@endif
<div class="info-box">
    <p>Para activar tu acceso al panel, abre el enlace <strong>Aceptar</strong>, confirma la solicitud y <strong>define tu contraseña</strong> de acceso al panel como gestor.</p>
</div>
<p style="text-align:center; margin: 24px 0;">
    <a href="{{ $acceptUrl }}" style="display:inline-block;padding:10px 18px;background:#198754;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin-right:8px;">Aceptar y definir contraseña</a>
    <a href="{{ $rejectUrl }}" style="display:inline-block;padding:10px 18px;background:#dc3545;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">Rechazar solicitud</a>
</p>
<p style="font-size: 13px; color:#666;">Si no reconoces esta invitación, puedes rechazarla o ignorar este correo.</p>
@endsection
