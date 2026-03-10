@extends('layouts.layout')

@section('title', 'Diseño e Impresión - Indicaciones / Archivos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('design.external.list') }}">Diseño externo</a></li>
                        <li class="breadcrumb-item active">Indicaciones / Archivos</li>
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
                    <h5 class="mb-4">Indicaciones y archivos</h5>

                    {{-- Stepper: 1 activo, 2 inactivo --}}
                    <div class="d-flex gap-2 mb-4">
                        <span class="badge bg-dark rounded-pill px-3 py-2">1 Indicaciones / Archivos</span>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">2 Invitación</span>
                    </div>

                    <form action="{{ route('design.external.storeStep1') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Comentarios</label>
                            <p class="text-muted small">Aquí puedes introducir tus sugerencias y las cualidades que deseas que tenga tu diseño.</p>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Añade tu comentario">{{ old('comment', $invitation->comment ?? '') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Carga de Archivos</label>
                                <div class="border rounded p-5 text-center bg-light" style="min-height: 220px;">
                                    <i class="ri-cloud-upload-line ri-3x text-muted d-block mb-2"></i>
                                    <input type="file" name="files[]" id="files" multiple class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                    <label for="files" class="btn btn-outline-primary rounded-pill mb-2 cursor-pointer">Buscar Archivos</label>
                                    <p class="text-muted small mb-0">Arrastre y suelta archivos aquí</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lista de Archivos cargados</label>
                                <div class="border rounded p-3 bg-light" style="min-height: 220px;">
                                    @if(isset($invitation) && $invitation->files->count())
                                        <ul class="list-unstyled mb-0">
                                            @foreach($invitation->files as $f)
                                                <li class="small text-muted">{{ $f->original_name ?? basename($f->path) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted small mb-0">No hay archivos cargados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="{{ route('design.showChooseType') }}" class="btn btn-dark rounded-pill">
                                <i class="ri-arrow-left-line me-1"></i> Atrás
                            </a>
                            <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark">
                                Siguiente <i class="ri-arrow-right-line ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
