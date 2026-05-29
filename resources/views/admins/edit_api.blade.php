@extends('layouts.layout')

@section('title','Administraciones')

@section('content')

<style>
    .form-check-input:checked {
        border-color: #333;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('administrations.index') }}">Administraciones</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('administrations.show', $administration->id) }}">Administración</a></li>
                        <li class="breadcrumb-item active">Configuración API</li>
                    </ol>
                </div>
                <h4 class="page-title">Configuración API</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Datos Administración</h4>
                    <br>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('administrations.update-api', $administration->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-3" style="position: relative;">
                                <ul class="form-card bs mb-3">
                                    <div class="form-wizard-element">
                                        <span>1</span>
                                        <img src="{{ url('assets/admin.svg') }}" alt="">
                                        <label>Datos administración</label>
                                    </div>
                                    <div class="form-wizard-element">
                                        <span>2</span>
                                        <img src="{{ url('assets/gestor.svg') }}" alt="">
                                        <label>Datos Gestor</label>
                                    </div>
                                    <div class="form-wizard-element active">
                                        <span>3</span>
                                        <img src="{{ url('assets/api.svg') }}" alt="">
                                        <label>Configuración API</label>
                                    </div>
                                </ul>

                                <div class="form-card show-content mb-3">
                                    <h4 class="mb-0 mt-1">Página web</h4>
                                    <small><i>Este campo no es obligatorio</i></small>
                                    <div class="form-group mt-2">
                                        <label class="label-control">Web</label>
                                        <div class="input-group input-group-merge group-form" style="border: none">
                                            <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                <img src="{{ url('assets/form-groups/admin/0.svg') }}" alt="">
                                            </div>
                                            <input readonly class="form-control" type="text" value="{{ $administration->web }}" placeholder="www.administracion.es" style="border-radius: 0 30px 30px 0;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-card show-content bs">
                                    <h4 class="mb-0 mt-1">Estado Administración</h4>
                                    <small><i>Bloquea o desbloquea la administración</i></small>
                                    <div class="form-group mt-2">
                                        <label>Estado Actual</label>
                                        <label class="badge badge-lg bg-{{ $administration->status_class }} float-end">{{ $administration->status_text }}</label>
                                        <div style="clear: both;"></div>
                                    </div>
                                </div>

                                <a href="{{ route('administrations.show', $administration->id) }}#configuracion_api" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                    <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i>
                                    <span style="display: block; margin-left: 16px;">Atrás</span>
                                </a>
                            </div>

                            <div class="col-md-9">
                                <div class="form-card bs" style="min-height: 658px;">
                                    <h4 class="mb-0 mt-1">Datos generales API</h4>
                                    <small><i>Todos los campos son obligatorios</i></small>
                                    <div style="clear: both;"></div>

                                    <div class="row">
                                        <div class="col-7">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Nombre de la integración</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{ url('assets/form-groups/admin/1.svg') }}" alt="">
                                                    </div>
                                                    <input class="form-control" type="text" name="prepago_integration_name" value="{{ old('prepago_integration_name', $administration->prepago_integration_name) }}" placeholder="Nombre Integración" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                                <small><i>Ayuda: Un nombre fácil de recordar para identificar esta configuración</i></small>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group mb-3">
                                                <div class="form-check form-switch mt-4" style="margin-top: 3rem !important;">
                                                    <input style="float: right;" class="form-check-input bg-dark" type="checkbox" role="switch" id="prepago_integration_enabled" name="prepago_integration_enabled" value="1" {{ old('prepago_integration_enabled', $administration->prepago_integration_enabled) ? 'checked' : '' }}>
                                                    <label style="float: right; margin-right: 50px;" class="form-check-label" for="prepago_integration_enabled"><b>Estado de la integración</b></label>
                                                </div>
                                                <small class="text-muted d-block" style="clear: both; padding-top: 8px;">Activa o desactiva el uso de la API propia de esta administración.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="mb-0 mt-1">Datos generales API</h4>
                                    <small><i>Todos los campos son obligatorios</i></small>

                                    <div class="row">
                                        <div class="col-7">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">URL Base de la API (Endpoint)</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                                    </div>
                                                    <input class="form-control" type="url" name="prepago_api_url" value="{{ old('prepago_api_url', $administration->prepago_api_url) }}" placeholder="URL Base de la API" style="border-radius: 0 30px 30px 0;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group mt-2 mb-3">
                                                <label class="label-control">Método de Autenticación</label>
                                                <div class="input-group input-group-merge group-form">
                                                    <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                        <img src="{{ url('assets/form-groups/admin/13.svg') }}" alt="">
                                                    </div>
                                                    <select class="form-control" style="border-radius: 0 30px 30px 0;" disabled>
                                                        <option value="apikey" selected>Clave API (API Key)</option>
                                                    </select>
                                                    <input type="hidden" name="prepago_auth_method" value="apikey">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="apikey" class="mt-3">
                                        <h4 class="mb-0 mt-1">Clave API (API Key)</h4>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">Prefijo del código</label>
                                                    <div class="input-group input-group-merge group-form">
                                                        <div class="input-group-text" style="border-radius: 30px 0 0 30px;">
                                                            <img src="{{ url('assets/form-groups/admin/4.svg') }}" alt="">
                                                        </div>
                                                        <input class="form-control" type="text" name="prepago_api_prefix" value="{{ old('prepago_api_prefix', $administration->prepago_api_prefix) }}" placeholder="c-" style="border-radius: 0 30px 30px 0;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group mt-2 mb-3">
                                                    <label class="label-control">API Key</label>
                                                    <input class="form-control" type="password" name="prepago_api_key" autocomplete="new-password" placeholder="{{ $administration->prepago_api_key ? 'Dejar vacío para no cambiar' : 'API Key' }}" style="border-radius: 30px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="prepago_use_partilot_default" name="prepago_use_partilot_default" value="1" {{ old('prepago_use_partilot_default', $administration->prepago_use_partilot_default) ? 'checked' : '' }} {{ $partilotDefaultAvailable ? '' : 'disabled' }}>
                                        <label class="form-check-label" for="prepago_use_partilot_default">
                                            Usar integración por defecto de PARTILOT (.env) si no hay configuración propia completa
                                        </label>
                                    </div>
                                    <small class="text-muted d-block">
                                        @if($partilotDefaultAvailable)
                                            Si la integración propia está inactiva o incompleta, se usarán URL, prefijo y API key del servidor PARTILOT.
                                        @else
                                            La configuración PARTILOT del servidor (.env) no está completa; este check no tendrá efecto hasta configurarla.
                                        @endif
                                    </small>

                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <button type="submit" style="border-radius: 30px; width: 200px; background-color: #e78307; color: #333; padding: 8px; font-weight: bolder; position: relative;" class="btn btn-md btn-light mt-2">
                                                Guardar
                                                <i style="top: 6px; margin-left: 6px; font-size: 18px; position: absolute;" class="ri-save-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
