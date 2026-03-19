@extends('emails.layouts.base')

@php($title = 'Liquidación Vendedor - Partilot')
@php($heading = $isFullySettled ? 'Liquidación completada' : 'Actualización de liquidación')

@section('content')
<p>Hola {{ $seller->name ?? 'Vendedor' }},</p>
@if($isFullySettled)
<p>Tu liquidación está completada y el saldo pendiente ha quedado en <strong>0,00 €</strong>.</p>
@else
<p>Se ha registrado una actualización de tu liquidación.</p>
@endif
<div class="info-box">
    <p><strong>Importe registrado:</strong> {{ number_format((float)$settlement->paid_amount, 2, ',', '.') }} €</p>
    <p><strong>Pendiente:</strong> {{ number_format((float)$settlement->pending_amount, 2, ',', '.') }} €</p>
</div>
@endsection
