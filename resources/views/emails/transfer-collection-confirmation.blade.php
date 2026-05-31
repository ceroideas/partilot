@extends('emails.layouts.base')

@php($title = 'Cobro por Transferencia - Partilot')
@php($heading = 'Cobro por transferencia confirmado')

@section('content')
<p>Hola {{ $collection->user->name ?? 'Usuario' }},</p>
<p>Tu solicitud de cobro por transferencia ha sido <strong>confirmada</strong>. La entidad emisora gestionará el pago.</p>
<div class="info-box">
    <p><strong>Importe total:</strong> {{ number_format((float)$collection->importe_total, 2, ',', '.') }} €</p>
    <p><strong>IBAN:</strong> {{ $collection->iban }}</p>
</div>
<p>Normalmente recibirás el importe en tu cuenta en un plazo de 2 a 3 días hábiles.</p>
@endsection
