@if(!empty($configurationEntityScoped))
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Logs Emails</h4>
        <span class="badge bg-light text-dark border">Solo su entidad</span>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Destinatario</th>
                    <th>Plantilla</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entityEmailLogs ?? [] as $log)
                    <tr>
                        <td>{{ $log->sent_at?->format('d/m/Y H:i') ?? $log->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ $log->recipient_email ?? '—' }}</td>
                        <td><code>{{ $log->template_key ?? '—' }}</code></td>
                        <td><span class="badge bg-secondary">{{ $log->status ?? '—' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No hay emails registrados para su entidad.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Logs Emails</h4>
</div>

<div class="alert alert-info">
    <i class="fe-info me-2"></i>
    Esta sección está en desarrollo. Próximamente podrás ver los logs de emails.
</div>
@endif
