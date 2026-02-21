@extends('layouts.layout')

@section('title', 'Ajustes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Ajustes</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">Panel</a></li>
                        <li class="breadcrumb-item active">Ajustes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Menú lateral izquierdo (mismo diseño que entities/add) -->
        <div class="col-md-3 col-lg-3 configuration-menu-wrapper">
            <div class="form-card bs mb-3" style="background-color: #fff;">
                @if($section == 'datos-partilot')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Datos PARTILOT</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=datos-partilot') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Datos PARTILOT</label>
                    </a>
                @endif

                <div class="configuration-menu-sep"></div>

                @if($section == 'config-factura-auto')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Config. Factura (Auto)</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=config-factura-auto') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Config. Factura (Auto)</label>
                    </a>
                @endif

                @if($section == 'facturacion-cobros')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Facturación y Cobros</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=facturacion-cobros') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Facturación y Cobros</label>
                    </a>
                @endif

                <div class="configuration-menu-sep"></div>

                @if($section == 'logs-actividad')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Logs de Actividad</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=logs-actividad') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Logs de Actividad</label>
                    </a>
                @endif

                @if($section == 'logs-emails')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Logs Emails</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=logs-emails') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Logs Emails</label>
                    </a>
                @endif

                @if($section == 'logs-notificaciones')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Logs Notificaciones</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=logs-notificaciones') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Logs Notificaciones</label>
                    </a>
                @endif

                <div class="configuration-menu-sep"></div>

                @if($section == 'imprenta')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Imprenta</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=imprenta') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Imprenta</label>
                    </a>
                @endif

                @if($section == 'ordenes-imprenta')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Ordenes Imprenta</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=ordenes-imprenta') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Ordenes Imprenta</label>
                    </a>
                @endif

                <div class="configuration-menu-sep"></div>

                @if($section == 'ordenes-pago-entidades')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Ordenes Pago Entidades</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=ordenes-pago-entidades') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Ordenes Pago Entidades</label>
                    </a>
                @endif

                @if($section == 'codigos-recarga')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Códigos Recarga</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=codigos-recarga') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Códigos Recarga</label>
                    </a>
                @endif

                <div class="configuration-menu-sep"></div>

                @if($section == 'notificaciones')
                    <div class="form-wizard-element active">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Notificaciones</label>
                    </div>
                @else
                    <a href="{{ url('/configuration?section=notificaciones') }}" class="form-wizard-element text-decoration-none" style="color: inherit;">
                        <img src="{{ url('assets/entidad.svg') }}" alt="">
                        <label>Notificaciones</label>
                    </a>
                @endif
            </div>
        </div>

        <!-- Contenido central -->
        <div class="col-md-9 col-lg-9">
                    @if($section == 'datos-partilot')
                        @include('configuration.sections.datos-partilot')
                    @elseif($section == 'config-factura-auto')
                        @include('configuration.sections.config-factura-auto')
                    @elseif($section == 'facturacion-cobros')
                        @include('configuration.sections.facturacion-cobros')
                    @elseif($section == 'logs-actividad')
                        @include('configuration.sections.logs-actividad')
                    @elseif($section == 'logs-emails')
                        @include('configuration.sections.logs-emails')
                    @elseif($section == 'logs-notificaciones')
                        @include('configuration.sections.logs-notificaciones')
                    @elseif($section == 'imprenta')
                        @include('configuration.sections.imprenta')
                    @elseif($section == 'ordenes-imprenta')
                        @include('configuration.sections.ordenes-imprenta')
                    @elseif($section == 'ordenes-pago-entidades')
                        @include('configuration.sections.ordenes-pago-entidades')
                    @elseif($section == 'codigos-recarga')
                        @include('configuration.sections.codigos-recarga')
                    @elseif($section == 'notificaciones')
                        @include('configuration.sections.notificaciones')
                    @else
                        @include('configuration.sections.datos-partilot')
                    @endif
        </div>
    </div>
</div>

<style>
/* Separador entre grupos del menú de configuración */
.configuration-menu-sep {
    height: 1px;
    background-color: #dee2e6;
    margin: 12px 0;
}

/* Ajuste del menú de configuración: selección más oscura y redondeada como en la referencia */
.configuration-menu-wrapper .form-wizard-element {
    padding: 12px 16px !important;
    margin-bottom: 10px !important;
}

.configuration-menu-wrapper .form-wizard-element.active {
    background-color: #495057 !important;
    color: #fff !important;
    filter: none;
    padding: 14px 16px !important;
}

.configuration-menu-wrapper .form-wizard-element.active img {
    filter: brightness(0) invert(1);
}

/* Ocultar el span (número) en el menú de configuración */
.configuration-menu-wrapper .form-wizard-element span {
    display: none;
}

/* Ajustar tamaño del label para compensar la falta del número */
.configuration-menu-wrapper .form-wizard-element label {
    font-size: 14px;
    font-weight: 600;
}
</style>
@endsection

@section('scripts')
@if($section == 'ordenes-pago-entidades' && $step === 1)
<script>
(function() {
    var baseUrl = '{{ url("/configuration") }}';
    document.addEventListener('DOMContentLoaded', function() {
        var table = document.getElementById('tabla-ope-entidades');
        var btnSiguiente = document.getElementById('btn-siguiente-ope');
        var inputSelected = document.getElementById('selected-entity-id-ope');
        if (!table || !btnSiguiente || !inputSelected) return;

        function clearSelection() {
            var rows = table.querySelectorAll('tbody tr.selectable-row');
            rows.forEach(function(r) { r.style.backgroundColor = ''; });
        }
        document.addEventListener('click', function(e) {
            var row = e.target.closest('tr.selectable-row');
            if (!row || !table.contains(row)) return;
            var id = row.getAttribute('data-entity-id');
            if (!id) return;
            e.preventDefault();
            clearSelection();
            row.style.backgroundColor = '#e3f2fd';
            inputSelected.value = id;
            btnSiguiente.disabled = false;
        });
        btnSiguiente.addEventListener('click', function() {
            var id = inputSelected.value;
            if (id) window.location.href = baseUrl + '?section=ordenes-pago-entidades&step=2&entity_id=' + id;
        });
        if (typeof jQuery !== 'undefined' && jQuery.fn.DataTable && table.querySelector('tbody tr.selectable-row')) {
            jQuery(table).DataTable({
                ordering: true,
                searching: true,
                paging: true,
                pageLength: 30,
                lengthMenu: [[10, 25, 30, 50, -1], [10, 25, 30, 50, 'Todos']],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                columnDefs: [{ orderable: false, targets: -1 }]
            });
        }
    });
})();
</script>
@endif
@endsection
