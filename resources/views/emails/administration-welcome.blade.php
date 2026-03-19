@extends('emails.layouts.base')

@php($title = 'Bienvenida Administración - Partilot')
@php($heading = '¡Bienvenido a Partilot!')

@section('content')
<p>Hola {{ $administration->name ?? 'Administración' }},</p>
<p>Tu cuenta en Partilot ya está activa.</p>
<div class="info-box">
    <p><strong>Usuario de acceso:</strong> {{ $user->email }}</p>
</div>
<p>Accede al panel y configura tu contraseña desde tu perfil si lo deseas.</p>
@endsection
