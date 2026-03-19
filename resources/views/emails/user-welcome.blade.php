@extends('emails.layouts.base')

@php($title = 'Bienvenida Usuario - Partilot')
@php($heading = '¡Bienvenido a Partilot!')

@section('content')
<p>Hola {{ $user->name ?? 'Usuario' }},</p>
<p>Tu cuenta ya está creada y puedes iniciar sesión.</p>
<div class="info-box">
    <p>Ya puedes comprar participaciones digitales y gestionar tus movimientos desde tu cuenta.</p>
</div>
@endsection
