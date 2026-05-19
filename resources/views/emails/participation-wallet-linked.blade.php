@extends('emails.layouts.base')

@php($title = 'Participación en cartera - Partilot')
@php($heading = 'Participación vinculada')
@php($participation->loadMissing(['set.entity', 'set.reserve.lottery']))

@section('content')
<p>Hola {{ $user->name ?? 'Usuario' }},</p>
<p>Se ha vinculado una participación a tu cartera en la app Partilot.</p>
<div class="info-box">
    <p><strong>Código:</strong> {{ $participation->display_participation_code }}</p>
    <p><strong>Entidad:</strong> {{ $participation->set?->entity?->name ?? '—' }}</p>
    <p><strong>Sorteo:</strong> {{ $participation->set?->reserve?->lottery?->name ?? '—' }}</p>
</div>
<p>Abre la app y entra en <strong>Mi cartera</strong> para verla.</p>
@endsection
