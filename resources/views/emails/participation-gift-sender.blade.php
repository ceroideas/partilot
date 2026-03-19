@extends('emails.layouts.base')

@php($title = 'Regalo Enviado - Partilot')
@php($heading = 'Participación enviada con éxito')

@section('content')
<p>Hola {{ $gift->fromUser->name ?? 'Usuario' }},</p>
<p>Has enviado correctamente una participación a <strong>{{ $gift->toUser->email }}</strong>.</p>
<div class="info-box">
    <p><strong>Código:</strong> {{ $gift->participation->display_participation_code ?? $gift->participation->participation_code }}</p>
</div>
@endsection
