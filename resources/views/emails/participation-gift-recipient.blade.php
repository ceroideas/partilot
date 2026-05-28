@extends('emails.layouts.base')

@php($title = 'Participación regalada - Partilot')
@php($heading = 'Te han regalado una participación')
@php($fromName = $gift->fromUser->name ?? $gift->fromUser->email ?? 'Un usuario')
@php($registerUrl = $gift->registrationUrl())
@php($isRegistered = (bool) $gift->to_user_id)

@section('content')
<p>Hola{{ $gift->toUser?->name ? ' '.$gift->toUser->name : '' }},</p>
<p><strong>{{ $fromName }}</strong> te ha regalado una participación de lotería.</p>

@if($gift->message)
<div class="info-box" style="margin: 16px 0;">
    <p style="margin:0;"><strong>Mensaje:</strong></p>
    <p style="margin:8px 0 0; white-space: pre-wrap;">{{ $gift->message }}</p>
</div>
@endif

<div class="info-box">
    <p><strong>Código participación:</strong> {{ $gift->participation->display_participation_code ?? $gift->participation->participation_code }}</p>
</div>

@if($isRegistered)
<p>Entra en la app Partilot con tu cuenta (<strong>{{ $gift->recipientEmail() }}</strong>) y acepta el regalo desde tu cartera.</p>
<p style="text-align:center; margin: 24px 0;">
    <a href="{{ config('app.url') }}" style="display:inline-block;padding:12px 22px;background:#F59200;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">
        Abrir Partilot
    </a>
</p>
@else
<p>Para recibirla debes <strong>registrarte con este mismo correo</strong> (<strong>{{ $gift->recipientEmail() }}</strong>). Tras registrarte, la verás en tu cartera para aceptarla.</p>
@if($registerUrl)
<p style="text-align:center; margin: 24px 0;">
    <a href="{{ $registerUrl }}" style="display:inline-block;padding:12px 22px;background:#F59200;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;">
        Registrarme y ver el regalo
    </a>
</p>
@endif
@endif

<p style="font-size: 13px; color:#666;">
    Si no aceptas el regalo antes del día del sorteo, volverá al usuario que te lo envió.
</p>
@endsection
