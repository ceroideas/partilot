@extends('layouts.layout_external_design')

@section('title', 'Diseño guardado')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <span class="fe-check-circle text-success" style="font-size: 4rem;"></span>
                </div>
                <h2 class="h4 mb-2">Diseño guardado correctamente</h2>
                <p class="text-muted mb-0">Gracias por completar tu diseño. Los datos han sido registrados.</p>
            </div>
        </div>
    </div>
</div>
@endsection
