@extends('layouts.layout')

@section('title', 'Diseño e Impresión - Invitaciones externas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('design.index') }}">Diseño e Impresión</a></li>
                        <li class="breadcrumb-item active">Diseño externo</li>
                    </ol>
                </div>
                <h4 class="page-title">Diseño e impresión externo</h4>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Orden ID</th>
                                    <th>Email</th>
                                    <th>Fecha Envio</th>
                                    <th>Hora Envio</th>
                                    <th>Status</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invitations as $inv)
                                    <tr>
                                        <td>{{ $inv->orden_id ?? '#EN' . $inv->id }}</td>
                                        <td>{{ $inv->email }}</td>
                                        <td>{{ $inv->sent_at ? $inv->sent_at->format('d/m/Y') : '—' }}</td>
                                        <td>{{ $inv->sent_at ? $inv->sent_at->format('H.i') }}h</td>
                                        <td>
                                            @if($inv->status === \App\Models\DesignExternalInvitation::STATUS_COMPLETED && $inv->design_format_id)
                                                <span class="badge bg-success rounded-pill">Completado</span>
                                            @elseif($inv->status === \App\Models\DesignExternalInvitation::STATUS_IN_PROGRESS)
                                                <span class="badge bg-info rounded-pill">En diseño</span>
                                            @elseif($inv->sent_at)
                                                <span class="badge bg-warning text-dark rounded-pill">En Proceso</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('design.external.destroy', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta invitación?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No hay invitaciones de diseño externo.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('design.index') }}" class="btn btn-dark rounded-pill"><i class="ri-arrow-left-line me-1"></i> Volver a Diseño e impresión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
