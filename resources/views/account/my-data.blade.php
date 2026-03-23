@extends('layouts.layout')

@section('title', 'Mis datos')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Panel</a></li>
                        <li class="breadcrumb-item active">Mis datos</li>
                    </ol>
                </div>
                <h4 class="page-title">Mis datos</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Acceso al panel</h4>
                    <p class="text-muted">El usuario de acceso es fijo y no puede modificarse. El correo indicado es el de contacto de su administración (notificaciones).</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Usuario de acceso</label>
                            <input type="text" class="form-control" value="{{ $user->panel_login_username ?? '—' }}" readonly style="border-radius: 30px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly style="border-radius: 30px;">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="header-title mb-3">Cambiar contraseña</h4>
                    <p class="text-muted small">Introduzca su contraseña actual y la nueva dos veces para confirmarla.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('account.update-password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Contraseña actual</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required style="border-radius: 30px;">
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required minlength="8" style="border-radius: 30px;">
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required minlength="8" style="border-radius: 30px;">
                        </div>
                        <button type="submit" class="btn btn-dark" style="border-radius: 30px;">Guardar nueva contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
