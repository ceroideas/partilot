@extends('layouts.layout')

@section('title', 'Push a usuario')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notificaciones</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('notifications.create') }}">Nueva</a></li>
                        <li class="breadcrumb-item active">Push a usuario</li>
                    </ol>
                </div>
                <h4 class="page-title">Enviar push a un usuario</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="text-muted">
                        Solo aparecen usuarios vinculados como gestor o vendedor a entidades a las que tienes acceso
                        @if(auth()->user()->isSuperAdmin())
                            (como super administrador ves todos los usuarios).
                        @endif
                    </p>

                    @if ($users->isEmpty())
                        <div class="alert alert-warning mb-0">
                            No hay usuarios disponibles para esta cuenta. Comprueba que tengas entidades asignadas y que existan gestores o vendedores vinculados.
                        </div>
                    @else
                        <form action="{{ route('notifications.store-user-push') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Usuario</label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>Selecciona…</option>
                                    @foreach ($users as $u)
                                        @php
                                            $n = $u->fcmTokens->count();
                                            $role = $u->role ?? 'usuario';
                                        @endphp
                                        <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} — {{ $u->email }} ({{ $n }} dispositivo{{ $n === 1 ? '' : 's' }}, {{ $role }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" maxlength="255" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje</label>
                                <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" rows="4" required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Enviar push</button>
                            <a href="{{ route('notifications.create') }}" class="btn btn-light ms-1">Volver</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
