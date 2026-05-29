@if(!empty($configurationEntityScoped))
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Logs Notificaciones</h4>
        <span class="badge bg-light text-dark border">Solo su entidad</span>
    </div>

    <div class="alert alert-info mb-0">
        <i class="fe-info me-2"></i>
        Próximamente podrá consultar aquí las notificaciones enviadas relacionadas con su entidad.
    </div>
@else
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Logs Notificaciones</h4>
</div>

<div class="alert alert-info">
    <i class="fe-info me-2"></i>
    Esta sección está en desarrollo. Próximamente podrás ver los logs de notificaciones.
</div>
@endif
