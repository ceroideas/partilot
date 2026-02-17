@extends('layouts.layout')

@section('title', 'Historial - Usuario')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.show', $user->id) }}">{{ $user->name }} {{ $user->last_name }}</a></li>
                        <li class="breadcrumb-item active">Historial</li>
                    </ol>
                </div>
                <h4 class="page-title">Historial</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Datos Usuario</h4>
                    <br>

                    <div class="row">
                        <div class="col-md-3" style="position: relative;">
                            <div class="form-card bs mb-3">
                                <a href="{{ route('users.show', $user->id) }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                                    <span>1</span>
                                    <img src="{{ url('icons_/usuarios.svg') }}" alt="">
                                    <label>Datos Usuario</label>
                                </a>
                                <a href="{{ route('users.wallet', $user->id) }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                                    <span>2</span>
                                    <img src="{{ url('assets/form-groups/wallet.svg') }}" alt="">
                                    <label>Cartera</label>
                                </a>
                                <div class="form-wizard-element active">
                                    <span>3</span>
                                    <img src="{{ url('assets/form-groups/history.svg') }}" alt="">
                                    <label>Historial</label>
                                </div>
                            </div>
                            <a href="{{ route('users.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>
                        <div class="col-md-9">
                            <div class="form-card bs" style="min-height: 400px;">
                                <h4 class="mb-3 mt-1">Historial de {{ $user->name }} {{ $user->last_name }}</h4>
                                <p class="text-muted small mb-3">Digitalizaciones, regalos, cobros y donaciones.</p>

                                @if(count($historial) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Detalle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($historial as $h)
                                            <tr>
                                                <td>{{ isset($h['fecha']) ? \Carbon\Carbon::parse($h['fecha'])->format('d/m/Y H:i') : '—' }}</td>
                                                <td>
                                                    @php
                                                        $tipo = $h['tipo'] ?? '';
                                                        $badge = 'bg-secondary';
                                                        if ($tipo === 'digitalizacion') $badge = 'bg-primary';
                                                        elseif ($tipo === 'regalo') $badge = 'bg-info';
                                                        elseif ($tipo === 'cobro') $badge = 'bg-success';
                                                        elseif ($tipo === 'donacion') $badge = 'bg-warning text-dark';
                                                    @endphp
                                                    <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</span>
                                                    @if(!empty($h['direccion']) && $h['tipo'] === 'regalo')
                                                        <small class="d-block">{{ $h['direccion'] === 'enviado' ? 'Enviado' : 'Recibido' }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $h['descripcion'] ?? '—' }}</td>
                                                <td>
                                                    @if(!empty($h['participacion']))
                                                        {{ $h['participacion']['entidad'] ?? '—' }} · Ref. {{ $h['participacion']['numeroReferencia'] ?? $h['participacion']['referencia'] ?? '—' }} · €{{ number_format($h['participacion']['importeTotal'] ?? 0, 2, ',', '.') }}
                                                    @elseif(!empty($h['participaciones']) && count($h['participaciones']) > 0)
                                                        {{ count($h['participaciones']) }} participación(es) · €{{ number_format($h['importeTotal'] ?? $h['importeDonacion'] ?? 0, 2, ',', '.') }}
                                                    @elseif(!empty($h['destinatario']))
                                                        A: {{ $h['destinatario'] }}
                                                    @elseif(!empty($h['remitente']))
                                                        De: {{ $h['remitente'] }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-light border text-center py-5">
                                    <i class="ri-history-line display-4 text-muted"></i>
                                    <p class="mb-0 mt-2 text-muted">No hay movimientos en el historial.</p>
                                    <small class="text-muted">Este usuario aún no tiene digitalizaciones, regalos, cobros ni donaciones.</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
