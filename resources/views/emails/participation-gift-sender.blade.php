@extends('emails.layouts.base')

@php($title = 'Regalo Enviado - Partilot')
@php($heading = 'Participación enviada')
@php($toLabel = $gift->toUser?->email ?? $gift->to_email ?? '—')

@section('content')
<p>Hola {{ $gift->fromUser->name ?? 'Usuario' }},</p>
<p>Has enviado una participación a <strong>{{ $toLabel }}</strong>.</p>
<p>El destinatario debe <strong>aceptar el regalo</strong> en la app para que pase a su cartera. Si no lo acepta a tiempo, la participación volverá a ti.</p>
<div class="info-box">
    <p><strong>Código:</strong> {{ $gift->participation->display_participation_code ?? $gift->participation->participation_code }}</p>
</div>
@endsection
