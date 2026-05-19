@extends('emails.layouts.base')

@php($title = 'Compra Digital Confirmada - Partilot')
@php($heading = 'Compra digital confirmada')

@section('content')
<p>Hola {{ $buyer->name ?? 'Usuario' }},</p>
<p>Hemos registrado tu compra de participaciones digitales. Ya están vinculadas a tu cuenta.</p>
<div class="info-box">
    <p><strong>Total:</strong> {{ number_format($totalAmount, 2, ',', '.') }} €</p>
    <p>Consulta el detalle en la app Partilot, sección <strong>Mi cartera</strong>.</p>
</div>
<p><strong>Detalle:</strong></p>
<ul>
@foreach($items as $it)
<li>{{ $it['code'] ?? '-' }} - {{ $it['entity'] ?? '' }}</li>
@endforeach
</ul>
@endsection
