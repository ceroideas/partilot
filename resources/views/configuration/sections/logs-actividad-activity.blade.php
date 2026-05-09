{{-- Solo datos de actividad mock (hardcoded). El contexto (quién se consulta) viene del controlador. --}}
@php
    $mockRowsBase = [
        ['fecha' => '01/05/2025', 'hora' => '10:30h', 'usuario' => 'admin@partilot.es', 'accion' => 'Inicio de sesión', 'detalle' => 'Éxito', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '30/04/2025', 'hora' => '20:30h', 'usuario' => 'admin@partilot.es', 'accion' => 'Cierre de sesión', 'detalle' => 'Éxito', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '30/04/2025', 'hora' => '19:30h', 'usuario' => 'admin@partilot.es', 'accion' => 'Visualizó página', 'detalle' => 'Facturación', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '30/04/2025', 'hora' => '19:00h', 'usuario' => 'admin@partilot.es', 'accion' => 'Editó', 'detalle' => 'josele_garcia@gmail.com', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '30/04/2025', 'hora' => '18:50h', 'usuario' => 'admin@partilot.es', 'accion' => 'Visualizó página', 'detalle' => '/vendedores/', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
    ];
    $mockPartilotExtra = [
        ['fecha' => '29/04/2025', 'hora' => '16:15h', 'usuario' => 'admin@partilot.es', 'accion' => 'Creó administración', 'detalle' => 'Administración «Demostración Sur»', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '29/04/2025', 'hora' => '15:40h', 'usuario' => 'admin@partilot.es', 'accion' => 'Alta de entidad', 'detalle' => 'Entidad #EN0999 · Lotero Centro', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '29/04/2025', 'hora' => '14:20h', 'usuario' => 'admin@partilot.es', 'accion' => 'Alta de vendedor', 'detalle' => 'seller.demo@example.es', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '28/04/2025', 'hora' => '11:05h', 'usuario' => 'admin@partilot.es', 'accion' => 'Creó reserva', 'detalle' => 'Reserva RSV-2025-014 · 500 participaciones', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
        ['fecha' => '28/04/2025', 'hora' => '09:30h', 'usuario' => 'admin@partilot.es', 'accion' => 'Configuró sorteo', 'detalle' => 'Sorteo nacional · cierre 30/06/2025', 'ip' => '192.186.10.2.2', 'dispositivo' => 'Chrome/Windows'],
    ];
    $mockRows = ($mockVariant ?? '') === 'partilot'
        ? array_merge($mockPartilotExtra, $mockRowsBase)
        : $mockRowsBase;
@endphp

<div class="form-card bs mt-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
        <div>
            <h5 class="mb-0">{{ $activityTitle ?? 'Actividad' }}</h5>
            <small class="text-muted">{{ $activitySubtitle ?? '' }}</small>
        </div>
        <div class="flex-grow-1" style="max-width: 280px;">
            <input type="search" class="form-control form-control-sm" placeholder="Busqueda" disabled title="Filtro demo">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-centered mb-0 w-100 logs-tabla-actividad-mock" data-order-col="0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                    <th>IP</th>
                    <th>Dispositivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mockRows as $r)
                    <tr>
                        <td>{{ $r['fecha'] }}</td>
                        <td>{{ $r['hora'] }}</td>
                        <td>{{ $r['usuario'] }}</td>
                        <td>{{ $r['accion'] }}</td>
                        <td>{{ $r['detalle'] }}</td>
                        <td>{{ $r['ip'] }}</td>
                        <td>{{ $r['dispositivo'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
