@extends('emails.layouts.base')

@php($title = 'Invitación Gestor - Partilot')
@php($heading = 'Invitación de gestor')

@section('content')
<p>Hola {{ $managerUser->name ?? 'Gestor' }},</p>
<p>Has sido invitado como gestor de la entidad <strong>{{ $entity->name }}</strong> en Partilot.</p>
<div class="info-box">
    <p>Para activar tu acceso al panel debes confirmar esta solicitud:</p>
</div>
<p style="text-align:center; margin: 24px 0;">
    <a href="{{ $acceptUrl }}" style="display:inline-block;padding:10px 18px;background:#198754;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin-right:8px;">Aceptar solicitud</a>
    <a href="{{ $rejectUrl }}" style="display:inline-block;padding:10px 18px;background:#dc3545;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">Rechazar solicitud</a>
</p>
<p style="font-size: 13px; color:#666;">Si no reconoces esta invitación, puedes rechazarla o ignorar este correo.</p>
@endsection
