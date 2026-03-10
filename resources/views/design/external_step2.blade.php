@extends('layouts.layout')

@section('title', 'Diseño e Impresión - Invitación')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('design.external.list') }}">Diseño externo</a></li>
                        <li class="breadcrumb-item active">Invitación</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e impresión externo</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-2">Selecciona una Entidad</p>
                    <h5 class="mb-4">Invitación</h5>

                    {{-- Stepper: 1 inactivo, 2 activo --}}
                    <div class="d-flex gap-2 mb-4">
                        <span class="badge bg-secondary rounded-pill px-3 py-2">1 Indicaciones / Archivos</span>
                        <span class="badge bg-dark rounded-pill px-3 py-2">2 Invitación</span>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card border shadow-sm">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-4">¡Invitar Diseño!</h5>
                                    <form action="{{ route('design.external.sendInvitation') }}" method="POST" id="inviteForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Correo electrónico</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                                <input type="email" name="email" class="form-control" placeholder="email@example.com" value="{{ old('email', $invitation->email ?? '') }}" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 text-dark fw-semibold">Invitar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('design.external.step1') }}" class="btn btn-dark rounded-pill">
                            <i class="ri-arrow-left-line me-1"></i> Atrás
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
