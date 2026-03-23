@extends('emails.layouts.base')

@php($title = 'Bienvenida Administración - Partilot')
@php($heading = '¡Bienvenido a Partilot!')

@section('content')
<p>Hola {{ $administration->name ?? 'Administración' }},</p>
<p>Tu acceso al panel de Partilot está listo.</p>
<div class="info-box">
    <p><strong>Usuario de acceso al panel:</strong> {{ $user->panel_login_username ?? '—' }}</p>
    @if(!empty($magicLinkUrl))
    <p style="margin-top:12px;"><a href="{{ $magicLinkUrl }}" style="display:inline-block;padding:10px 18px;background:#333;color:#fff;text-decoration:none;border-radius:8px;">Establecer contraseña</a></p>
    <p style="font-size:12px;color:#666;">Si el botón no funciona, copie y pegue este enlace en el navegador:<br>{{ $magicLinkUrl }}</p>
    @endif
</div>
<p>Este enlace es de un solo uso y caduca pasados unos días. Si lo necesita de nuevo, contacte con el administrador de Partilot.</p>
@endsection
