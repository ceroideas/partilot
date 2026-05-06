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
                <h4 class="page-title">{{ ($mode ?? 'external') === 'partilot' ? 'Diseño e impresión PARTILOT' : 'Diseño e impresión externo' }}</h4>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex p-2 mb-3" style="align-items: center; justify-content: center;">
                        <div class="form-wizard-element active" style="width: 220px;">
                            <span style="top: -4px; margin-right: 8px;">1</span>
                            <label>Indicaciones <br> / Archivos</label>
                        </div>
                        <div class="form-wizard-element" style="width: 220px;">
                            <span style="top: -4px; margin-right: 8px;">2</span>
                            <label>{{ ($mode ?? 'external') === 'partilot' ? 'Resumen <br> y pago' : 'Invitación' }}</label>
                        </div>
                    </div>

                    <div class="form-card bs">
                        <h4 class="mb-0 mt-1">Configurar salida</h4>
                        <small><i>Configura el formato de salida de las participaciones</i></small>

                        <form action="{{ route('design.external.storeStep1') }}" method="POST" enctype="multipart/form-data" id="external-step1-form" class="mt-4">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Comentarios</label>
                                <p class="text-muted small">Aquí puedes introducir tus sugerencias y las cualidades que deseas que tenga tu diseño.</p>
                                <textarea name="comment" class="form-control" rows="4" placeholder="Añade tu comentario">{{ old('comment', $invitation->comment ?? '') }}</textarea>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Formato de impresión</label>
                                    <select name="print_size" class="form-select">
                                        @php($printSize = old('print_size', $invitation->print_size ?? 'custom'))
                                        <option value="a3_6" {{ $printSize === 'a3_6' ? 'selected' : '' }}>A3 - 6 participaciones</option>
                                        <option value="a3_8" {{ $printSize === 'a3_8' ? 'selected' : '' }}>A3 - 8 participaciones</option>
                                        <option value="custom" {{ $printSize === 'custom' ? 'selected' : '' }}>Personalizado</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Participaciones por taco</label>
                                    <input type="number" name="participations_per_book" min="1" max="1000" class="form-control" value="{{ old('participations_per_book', $invitation->participations_per_book ?? 50) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Trasera</label>
                                    @php($backMode = old('back_mode', $invitation->back_mode ?? 'bw'))
                                    <select name="back_mode" class="form-select">
                                        <option value="bw" {{ $backMode === 'bw' ? 'selected' : '' }}>Blanco y negro</option>
                                        <option value="color" {{ $backMode === 'color' ? 'selected' : '' }}>Color</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Carga de archivos</label>
                                    <p class="text-muted small">PDF, Word, imágenes o ZIP (máx. 50 MB por archivo, hasta 20 archivos).</p>
                                    <div id="external-drop-zone" class="border border-2 border-dashed rounded p-4 text-center bg-light external-upload-zone" style="min-height: 220px; cursor: pointer;">
                                        <i class="ri-cloud-upload-line ri-3x text-muted d-block mb-2"></i>
                                        <input type="file" name="files[]" id="files" multiple class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,.zip,application/pdf,application/zip,image/*">
                                        <button type="button" class="btn btn-outline-primary rounded-pill mb-2" id="external-browse-btn">Buscar archivos</button>
                                        <p class="text-muted small mb-0">Puedes <strong>buscar varias veces</strong> (carpetas distintas) y se irán sumando. También <strong>arrastrar y soltar</strong>.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Archivos que se enviarán <span class="text-muted fw-normal">(nuevos)</span></label>
                                    <div id="external-new-files-list" class="border rounded p-3 bg-light mb-3" style="min-height: 100px;">
                                        <p class="text-muted small mb-0" id="external-new-files-empty">Ningún archivo nuevo seleccionado.</p>
                                        <ul class="list-unstyled mb-0 small d-none" id="external-new-files-ul"></ul>
                                    </div>
                                    <label class="form-label fw-semibold">Ya adjuntos a esta solicitud</label>
                                    <div class="border rounded p-3 bg-white" style="min-height: 100px;">
                                        @if(isset($invitation) && $invitation->files->count())
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach($invitation->files as $f)
                                                    <li class="py-1 border-bottom border-light">
                                                        <i class="ri-attachment-2 text-muted"></i> {{ $f->original_name ?? basename($f->path) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted small mb-0">Aún no hay archivos. Sube archivos y pulsa <strong>Siguiente</strong> para guardarlos.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>

                    <div class="mt-2 d-flex justify-content-between">
                        <a href="{{ route('design.showChooseType') }}" class="btn btn-dark rounded-pill">
                            <i class="ri-arrow-left-line me-1"></i> Atrás
                        </a>
                        <button type="submit" form="external-step1-form" class="btn btn-warning rounded-pill px-4 text-dark">
                            Siguiente <i class="ri-arrow-right-line ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var input = document.getElementById('files');
    var zone = document.getElementById('external-drop-zone');
    var ul = document.getElementById('external-new-files-ul');
    var empty = document.getElementById('external-new-files-empty');
    if (!input || !zone) return;

    /** Lista acumulada: cada “Buscar archivos” solo devuelve la última selección; aquí las vamos sumando. */
    var accumulated = [];

    function fileKey(f) {
        return f.name + '|' + f.size + '|' + f.lastModified;
    }

    function mergeIntoAccumulated(incomingList) {
        var seen = {};
        var i, f;
        for (i = 0; i < accumulated.length; i++) {
            seen[fileKey(accumulated[i])] = true;
        }
        var dt = new DataTransfer();
        for (i = 0; i < accumulated.length; i++) {
            dt.items.add(accumulated[i]);
        }
        var incoming = Array.from(incomingList || []);
        for (i = 0; i < incoming.length; i++) {
            f = incoming[i];
            var k = fileKey(f);
            if (!seen[k]) {
                seen[k] = true;
                dt.items.add(f);
            }
        }
        accumulated = Array.from(dt.files);
        input.files = dt.files;
    }

    function renderList() {
        if (!input.files.length) {
            ul.classList.add('d-none');
            empty.classList.remove('d-none');
            ul.innerHTML = '';
            return;
        }
        empty.classList.add('d-none');
        ul.classList.remove('d-none');
        ul.innerHTML = '';
        for (var i = 0; i < input.files.length; i++) {
            var li = document.createElement('li');
            li.className = 'py-1 border-bottom border-light';
            li.textContent = input.files[i].name + ' (' + Math.round(input.files[i].size / 1024) + ' KB)';
            ul.appendChild(li);
        }
    }

    document.getElementById('external-browse-btn').addEventListener('click', function () { input.click(); });

    zone.addEventListener('click', function (e) {
        if (e.target.closest('button')) return;
        input.click();
    });

    input.addEventListener('change', function () {
        if (!this.files.length) return;
        mergeIntoAccumulated(this.files);
        renderList();
    });

    ['dragenter', 'dragover'].forEach(function (ev) {
        zone.addEventListener(ev, function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('border-primary', 'bg-white');
        });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
        zone.addEventListener(ev, function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('border-primary', 'bg-white');
        });
    });
    zone.addEventListener('drop', function (e) {
        var dropped = e.dataTransfer.files;
        if (!dropped.length) return;
        mergeIntoAccumulated(dropped);
        renderList();
    });
})();
</script>
@endsection
