@extends('layouts.layout')

@section('title', 'Cartera - Usuario')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.show', $user->id) }}">{{ $user->name }} {{ $user->last_name }}</a></li>
                        <li class="breadcrumb-item active">Cartera</li>
                    </ol>
                </div>
                <h4 class="page-title">Cartera</h4>
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
                                <div class="form-wizard-element active">
                                    <span>2</span>
                                    <img src="{{ url('assets/form-groups/wallet.svg') }}" alt="">
                                    <label>Cartera</label>
                                </div>
                                <a href="{{ route('users.history', $user->id) }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                                    <span>3</span>
                                    <img src="{{ url('assets/form-groups/history.svg') }}" alt="">
                                    <label>Historial</label>
                                </a>
                            </div>
                            <a href="{{ route('users.index') }}" style="border-radius: 30px; width: 200px; background-color: #333; color: #fff; padding: 8px; font-weight: bolder; position: absolute; bottom: 16px;" class="btn btn-md btn-light mt-2">
                                <i style="top: 6px; left: 32%; font-size: 18px; position: absolute;" class="ri-arrow-left-circle-line"></i> <span style="display: block; margin-left: 16px;">Atrás</span>
                            </a>
                        </div>
                        <div class="col-md-9">
                            <div class="form-card bs" style="min-height: 400px;">
                                <h4 class="mb-3 mt-1">Cartera de {{ $user->name }} {{ $user->last_name }}</h4>
                                <p class="text-muted small mb-3">Participaciones en cartera (propias y recibidas como regalo).</p>

                                @if(count($items) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Entidad</th>
                                                <th>Referencia</th>
                                                <th>Nº participación</th>
                                                <th>Fecha sorteo</th>
                                                <th>Importe jugado</th>
                                                <th>Importe total</th>
                                                <th>Estado</th>
                                                <th>Origen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $item)
                                            <tr>
                                                <td>{{ $item['entidad'] ?? '—' }}</td>
                                                <td><code>{{ $item['numeroReferencia'] ?? $item['referencia'] ?? '—' }}</code></td>
                                                <td>{{ $item['numeroParticipacion'] ?? $item['numero'] ?? '—' }}</td>
                                                <td>{{ $item['fechaSorteo'] ?? '—' }}</td>
                                                <td>€{{ number_format($item['importeJugado'] ?? 0, 2, ',', '.') }}</td>
                                                <td>€{{ number_format($item['importeTotal'] ?? 0, 2, ',', '.') }}</td>
                                                <td>
                                                    @php $estado = $item['estado'] ?? 'activa'; @endphp
                                                    <span class="badge bg-{{ $estado === 'cobrada' ? 'secondary' : ($estado === 'donada' ? 'info' : ($estado === 'regalada' ? 'warning' : 'success')) }}">{{ ucfirst($estado) }}</span>
                                                </td>
                                                <td>
                                                    @if(!empty($item['received_from_email']))
                                                        Recibida de {{ $item['received_from_email'] }}
                                                    @else
                                                        Propia
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-light border text-center py-5">
                                    <i class="ri-wallet-3-line display-4 text-muted"></i>
                                    <p class="mb-0 mt-2 text-muted">No hay productos en la cartera.</p>
                                    <small class="text-muted">Este usuario aún no tiene participaciones en su cartera.</small>
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
