@extends('layouts.layout')

@section('title','Comunicaciones')

@section('content')


<style>
    .empty-tables {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-tables img {
        opacity: 0.3;
        filter: grayscale(100%);
    }

    .empty-tables h3 {
        color: #333;
        font-weight: 600;
        margin: 20px 0 10px 0;
    }

    .empty-tables small {
        color: #666;
        font-size: 0.9em;
    }
</style>    
<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Comunicaciones (Emails)</h4>
            </div>
        </div>
    </div>     


    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="{{ count($logs) ? '' : 'd-none' }}">
                        <table id="communications-table" class="table table-striped nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Tipo</th>
                                    <th>Enviado por</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Status</th>
                                    <th>Fecha</th>
                                    <th class="no-filter"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    @php
                                        $effectiveDate = $log->displayEffectiveDate();
                                        $dateText = $effectiveDate ? $effectiveDate->format('d/m/Y H:i') : 'N/A';
                                    @endphp
                                    <tr id="email-log-{{ $log->id }}">
                                        <td><a href="#">#NO{{ str_pad($log->id,5,'0',STR_PAD_LEFT) }}</a></td>
                                        <td>{{ $log->message_type ?? ($log->template_key ?? '-') }}</td>
                                        <td>{{ $log->sender_type }}</td>
                                        <td>{{ $log->recipient_email }}</td>
                                        <td>{{ $log->recipient_role ?? '-' }}</td>
                                        <td><span class="badge {{ $log->displayStatusBadgeClass() }}">{{ $log->displayStatus() }}</span></td>
                                        <td>{{ $dateText }}</td>
                                        <td style="white-space:nowrap;">
                                            <form method="POST" action="{{ route('communications.resend', $log->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-dark" {{ empty($log->mail_class) ? 'disabled' : '' }}>
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                            </form>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger ms-1"
                                                onclick="deleteEmailLog({{ $log->id }})"
                                            >
                                                <i class="ri-delete-bin-6-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="{{ count($logs) ? 'd-none' : '' }}">
                        <div class="empty-tables">
                            <div>
                                <img src="{{url('icons_/comunicados.svg')}}" alt="" width="80px">
                            </div>
                            <h3 class="mb-0">No hay emails registrados</h3>
                            <small>A medida que se envíen comunicaciones, aparecerán aquí.</small>
                        </div>
                    </div>
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div><!-- col -->
    </div><!-- row -->
</div> <!-- container -->

@endsection

@section('scripts')
<script>
    function deleteEmailLog(id) {
        if (!confirm('¿Eliminar de forma permanente este registro de comunicación?')) return;

        fetch('{{ url("/") }}/communications/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            window.location.reload();
        }).catch(e => {
            alert('No se pudo eliminar: ' + e.message);
        });
    }

    $(document).ready(function() {
        if (!$('#communications-table').length) return;
        $('#communications-table').DataTable({
            ordering: false,
            searching: true,
            pageLength: 25
        });
    });
</script>
@endsection