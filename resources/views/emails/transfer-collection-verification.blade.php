@extends('emails.layouts.base')

@php($title = 'Confirma tu cobro - Partilot')
@php($heading = 'Confirma tu solicitud de cobro')

@section('content')
<p>Hola {{ $collection->user->name ?? 'Usuario' }},</p>
<p>Has solicitado un cobro por transferencia bancaria. Para completar la solicitud, confirma que los datos son correctos haciendo clic en el botón inferior.</p>
<div class="info-box">
    <p><strong>Importe total:</strong> {{ number_format((float)$collection->importe_total, 2, ',', '.') }} €</p>
    <p><strong>IBAN:</strong> {{ $collection->iban }}</p>
    <p><strong>Titular:</strong> {{ $collection->nombre }} {{ $collection->apellidos }}</p>
</div>
<p style="text-align: center; margin: 24px 0;">
    <a href="{{ $confirmUrl }}" style="display: inline-block; background-color: #e78307; color: #333; padding: 12px 24px; border-radius: 30px; text-decoration: none; font-weight: bold;">Confirmar solicitud</a>
</p>
<p style="font-size: 13px; color: #666;">
    Si no has solicitado este cobro, puedes <a href="{{ $cancelUrl }}">cancelar la solicitud</a>.
    El enlace expira en {{ \App\Models\ParticipationCollection::verificationExpiryHours() }} horas.
</p>
@endsection
