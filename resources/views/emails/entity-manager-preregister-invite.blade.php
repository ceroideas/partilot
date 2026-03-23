@extends('emails.layouts.base')

@php($title = 'Invitación gestor - Partilot')
@php($heading = 'Invitación como gestor')

@section('content')
<p>Hola,</p>
<p>Has sido invitado/a a ser <strong>gestor responsable</strong> de la entidad <strong>{{ $entity->name }}</strong> en Partilot.</p>
<div class="info-box">
    <p><strong>Aún no tenemos una cuenta registrada con el email {{ $invitedEmail }}.</strong></p>
    <p>Para vincularte automáticamente, <strong>regístrate en la aplicación o en el sitio web</strong> usando <strong>exactamente este mismo correo</strong>: <strong>{{ $invitedEmail }}</strong>.</p>
</div>
<p>Cuando completes el registro, recibirás un correo para <strong>aceptar o rechazar</strong> la invitación y podrás <strong>definir la contraseña de acceso al panel</strong> como gestor.</p>
<p style="font-size: 13px; color:#666;">Web: <a href="{{ $registerHintUrl }}">{{ $registerHintUrl }}</a></p>
<p style="font-size: 13px; color:#666;">Si no esperabas esta invitación, puedes ignorar este mensaje.</p>
@endsection
