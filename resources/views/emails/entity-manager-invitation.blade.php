@extends('emails.layouts.base')

@php($title = 'Invitación Gestor - Partilot')
@php($heading = 'Invitación de gestor')

@section('content')
<p>Hola {{ $managerUser->name ?? 'Gestor' }},</p>
<p>Has sido invitado como gestor de la entidad <strong>{{ $entity->name }}</strong> en Partilot.</p>
<div class="info-box">
    <p>Inicia sesión para revisar la solicitud y operar con la entidad.</p>
</div>
@endsection
