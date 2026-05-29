@if(!empty($configurationEntityScoped))
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Logs Notificaciones</h4>
        <span class="badge bg-light text-dark border">Solo su entidad</span>
    </div>

    <div class="alert alert-info mb-0">
        <i class="fe-info me-2"></i>
        Próximamente podrá consultar aquí las notificaciones enviadas relacionadas con su entidad.
    </div>
@elseif(!empty($configurationAdministrationScoped))
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h4 class="mb-0">Logs Notificaciones</h4>
        @if(($settingsAdministrationEntities ?? collect())->count() > 0)
            <form method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="section" value="logs-notificaciones">
                <label class="small text-muted mb-0">Entidad:</label>
                <select name="entity_id" class="form-select form-select-sm" style="min-width: 220px; border-radius: 20px;" onchange="this.form.submit()">
                    @foreach($settingsAdministrationEntities as $ent)
                        <option value="{{ $ent->id }}" {{ (int) ($settingsLogEntityId ?? 0) === (int) $ent->id ? 'selected' : '' }}>{{ $ent->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!($settingsLogEntityId ?? null))
        <div class="alert alert-info mb-0">
            <i class="fe-info me-2"></i>
            Seleccione una entidad para consultar las notificaciones enviadas.
        </div>
    @else
        <div class="alert alert-info mb-0">
            <i class="fe-info me-2"></i>
            Próximamente podrá consultar aquí las notificaciones enviadas para la entidad seleccionada.
        </div>
    @endif
@else
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Logs Notificaciones</h4>
</div>

<div class="alert alert-info">
    <i class="fe-info me-2"></i>
    Esta sección está en desarrollo. Próximamente podrás ver los logs de notificaciones.
</div>
@endif
