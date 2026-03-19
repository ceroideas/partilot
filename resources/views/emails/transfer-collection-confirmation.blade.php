@extends('emails.layouts.base')

@php($title = 'Cobro por Transferencia - Partilot')
@php($heading = 'Cobro por transferencia registrado')

@section('content')
<p>Hola {{ $collection->user->name ?? 'Usuario' }},</p>
<p>Tu solicitud de cobro por transferencia ha sido registrada correctamente.</p>
<div class="info-box">
    <p><strong>Importe total:</strong> {{ number_format((float)$collection->importe_total, 2, ',', '.') }} €</p>
    <p><strong>IBAN:</strong> {{ $collection->iban }}</p>
</div>
@endsection
