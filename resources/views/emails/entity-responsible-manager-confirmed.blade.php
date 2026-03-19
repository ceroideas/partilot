@extends('emails.layouts.base')

@php($title = 'Gestor Responsable Confirmado - Partilot')
@php($heading = 'Gestor responsable confirmado')

@section('content')
<p>Hola,</p>
<p>Se ha confirmado como gestor responsable de <strong>{{ $entity->name }}</strong> a:</p>
<div class="info-box">
    <p><strong>{{ $responsibleManager->name }} {{ $responsibleManager->last_name }}</strong></p>
    <p>{{ $responsibleManager->email }}</p>
</div>
@endsection
