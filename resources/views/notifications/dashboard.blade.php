@extends('layouts.layout')

@section('title','Dashboard de Notificaciones')

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('notifications.index')}}">Notificaciones</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
                <h4 class="page-title">🔥 Dashboard de Notificaciones Firebase</h4>
            </div>
        </div>
    </div>     

    <!-- Estado de Configuración -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-soft-primary text-primary rounded">
                                    <i class="mdi mdi-firebase font-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">Estado de Firebase</h5>
                            <p class="mb-0 text-muted">
                                <span id="firebase-status" class="badge bg-warning">Verificando...</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-soft-success text-success rounded">
                                    <i class="mdi mdi-account-check font-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">Usuarios con Token</h5>
                            <p class="mb-0">
                                <span id="users-count" class="font-20">{{ $usersWithTokens }}</span>
                                <span class="text-muted">usuarios</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-soft-info text-info rounded">
                                    <i class="mdi mdi-bell-ring font-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">Notificaciones Enviadas</h5>
                            <p class="mb-0">
                                <span class="font-20">{{ $totalNotifications }}</span>
                                <span class="text-muted">total</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-soft-warning text-warning rounded">
                                    <i class="mdi mdi-calendar-today font-24"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mt-0 mb-1">Hoy</h5>
                            <p class="mb-0">
                                <span class="font-20">{{ $notificationsToday }}</span>
                                <span class="text-muted">enviadas</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verificación de Configuración -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-cog-outline me-1"></i>
                        Estado de Configuración
                    </h4>

                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="w-50">
                                        <i class="mdi mdi-circle me-1 text-{{ $config['credentials'] ? 'success' : 'danger' }}"></i>
                                        Credenciales Firebase
                                    </td>
                                    <td>
                                        @if($config['credentials'])
                                            <span class="badge bg-success">Configurado</span>
                                        @else
                                            <span class="badge bg-danger">No configurado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="mdi mdi-circle me-1 text-{{ $config['api_key'] ? 'success' : 'danger' }}"></i>
                                        FIREBASE_API_KEY
                                    </td>
                                    <td>
                                        @if($config['api_key'])
                                            <span class="badge bg-success">Configurado</span>
                                        @else
                                            <span class="badge bg-danger">No configurado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="mdi mdi-circle me-1 text-{{ $config['project_id'] ? 'success' : 'danger' }}"></i>
                                        FIREBASE_PROJECT_ID
                                    </td>
                                    <td>
                                        @if($config['project_id'])
                                            <span class="badge bg-success">{{ $config['project_id'] }}</span>
                                        @else
                                            <span class="badge bg-danger">No configurado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="mdi mdi-circle me-1 text-{{ $config['server_key'] ? 'success' : 'warning' }}"></i>
                                        FIREBASE_SERVER_KEY
                                    </td>
                                    <td>
                                        @if($config['server_key'])
                                            <span class="badge bg-success">Configurado</span>
                                        @else
                                            <span class="badge bg-warning">No configurado (Legacy API)</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="mdi mdi-circle me-1 text-{{ $config['service_worker'] ? 'success' : 'danger' }}"></i>
                                        Service Worker
                                    </td>
                                    <td>
                                        @if($config['service_worker'])
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-danger">No encontrado</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if(!$config['all_configured'])
                    <div class="alert alert-warning mt-3" role="alert">
                        <i class="mdi mdi-alert-outline me-2"></i>
                        Algunos elementos de configuración están pendientes. 
                        <a href="{{ asset('FIREBASE_CONFIG_GUIDE.md') }}" target="_blank" class="alert-link">Ver guía de configuración</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-account-multiple me-1"></i>
                        Usuarios Registrados
                    </h4>

                    @if($users->count() > 0)
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th class="text-center">Token</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td><small>{{ $user->email }}</small></td>
                                    <td class="text-center">
                                        @if($user->fcm_token)
                                            <i class="mdi mdi-check-circle text-success" title="Token registrado"></i>
                                        @else
                                            <i class="mdi mdi-close-circle text-danger" title="Sin token"></i>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-account-off font-48 text-muted"></i>
                        <p class="text-muted mt-2">No hay usuarios con tokens registrados</p>
                        <small class="text-muted">Los usuarios deben permitir las notificaciones en su navegador</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-lightning-bolt me-1"></i>
                        Acciones Rápidas
                    </h4>

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <button onclick="testFirebaseConnection()" class="btn btn-primary btn-block w-100">
                                <i class="mdi mdi-test-tube me-1"></i>
                                Probar Conexión
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            <button onclick="sendTestNotification()" class="btn btn-success btn-block w-100" {{ $usersWithTokens == 0 ? 'disabled' : '' }}>
                                <i class="mdi mdi-send me-1"></i>
                                Enviar Prueba
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="{{route('notifications.create')}}" class="btn btn-info btn-block w-100">
                                <i class="mdi mdi-bell-plus me-1"></i>
                                Nueva Notificación
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Notificaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">
                        <i class="mdi mdi-history me-1"></i>
                        Últimas Notificaciones Enviadas
                    </h4>

                    @if($recentNotifications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Mensaje</th>
                                    <th>Enviado por</th>
                                    <th>Entidad</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentNotifications as $notification)
                                <tr>
                                    <td><strong>{{ $notification->title }}</strong></td>
                                    <td>{{ Str::limit($notification->message, 50) }}</td>
                                    <td>{{ $notification->sender->name ?? 'N/A' }}</td>
                                    <td>{{ $notification->entity->name ?? 'N/A' }}</td>
                                    <td>
                                        <small>{{ $notification->sent_at ? $notification->sent_at->diffForHumans() : $notification->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="mdi mdi-bell-off font-48 text-muted"></i>
                        <p class="text-muted mt-2">No hay notificaciones recientes</p>
                    </div>
                    @endif

                    <div class="text-end mt-3">
                        <a href="{{route('notifications.index')}}" class="btn btn-sm btn-link">
                            Ver todas las notificaciones <i class="mdi mdi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div> <!-- container -->

@endsection

@section('scripts')
<script>
function testFirebaseConnection() {
    // Mostrar loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Probando...';

    // Simular prueba de conexión
    fetch('{{ url("/") }}/notifications/firebase-config')
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Conexión Exitosa';
            btn.className = 'btn btn-success btn-block w-100';
            
            // Mostrar notificación
            showToast('✅ Conexión exitosa con Firebase', 'success');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.className = 'btn btn-primary btn-block w-100';
                btn.disabled = false;
            }, 3000);
        })
        .catch(error => {
            btn.innerHTML = '<i class="mdi mdi-close me-1"></i> Error de Conexión';
            btn.className = 'btn btn-danger btn-block w-100';
            
            showToast('❌ Error al conectar con Firebase', 'error');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.className = 'btn btn-primary btn-block w-100';
                btn.disabled = false;
            }, 3000);
        });
}

function sendTestNotification() {
    if (!confirm('¿Enviar notificación de prueba a todos los usuarios?')) {
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Enviando...';

    // Enviar notificación de prueba
    fetch('{{ url("/") }}/notifications/send-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            title: '🔥 Notificación de Prueba',
            message: 'Firebase está funcionando correctamente'
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Enviado';
        btn.className = 'btn btn-success btn-block w-100';
        
        showToast('✅ Notificación de prueba enviada exitosamente', 'success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.className = 'btn btn-success btn-block w-100';
            btn.disabled = false;
        }, 3000);
    })
    .catch(error => {
        btn.innerHTML = '<i class="mdi mdi-close me-1"></i> Error';
        btn.className = 'btn btn-danger btn-block w-100';
        
        showToast('❌ Error al enviar notificación', 'error');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.className = 'btn btn-success btn-block w-100';
            btn.disabled = false;
        }, 3000);
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Verificar estado de Firebase al cargar
window.addEventListener('DOMContentLoaded', () => {
    if (window.firebaseNotifications) {
        document.getElementById('firebase-status').innerHTML = '<span class="badge bg-success">Activo</span>';
    } else {
        document.getElementById('firebase-status').innerHTML = '<span class="badge bg-danger">Inactivo</span>';
    }
});
</script>
@endsection


