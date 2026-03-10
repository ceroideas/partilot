@extends('layouts.layout')

@section('title', 'Diseño e Impresión')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item active">Elegir tipo</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e Impresión</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">¿Cómo deseas continuar?</h4>
                    <p class="text-muted">Elige si vas a diseñar tú mismo o si quieres delegar el diseño e impresión a otra persona (enlace por correo).</p>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-body text-center py-4">
                                    <i class="ri-palette-line ri-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Diseño</h5>
                                    <p class="card-text text-muted small">Diseñar participación, portada y trasera tú mismo.</p>
                                    <form action="{{ route('design.format') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="set_id" value="{{ $set->id }}">
                                        <input type="hidden" name="new_design" value="1">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                            Ir a diseñar <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </form>
                                    <div class="mt-2">
                                        <a href="{{ route('design.listFormats', ['set_id' => $set->id]) }}" class="btn btn-outline-primary btn-sm">Usar diseño existente de la entidad</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-body text-center py-4">
                                    <i class="ri-mail-send-line ri-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Diseño e impresión externo</h5>
                                    <p class="card-text text-muted small">Enviar invitación por correo para que otra persona diseñe e imprima.</p>
                                    <a href="{{ route('design.external.step1') }}" class="btn btn-warning rounded-pill px-4 text-dark">
                                        Invitar a diseñar <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('design.selectSet') }}" class="btn btn-dark rounded-pill">
                            <i class="ri-arrow-left-line me-1"></i> Atrás
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
