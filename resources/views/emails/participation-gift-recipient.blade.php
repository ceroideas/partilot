@extends('emails.layouts.base')

@php($title = 'Participación Regalada - Partilot')
@php($heading = 'Te han regalado una participación')

@section('content')
<p>Hola {{ $gift->toUser->name ?? 'Usuario' }},</p>
<p>{{ $gift->fromUser->name ?? 'Un usuario' }} te ha regalado una participación.</p>
<div class="info-box">
    <p><strong>Código:</strong> {{ $gift->participation->display_participation_code ?? $gift->participation->participation_code }}</p>
</div>
@endsection
