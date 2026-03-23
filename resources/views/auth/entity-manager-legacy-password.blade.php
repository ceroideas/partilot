<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Nueva contraseña de gestor | PARTILOT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ url('/') }}/logo.svg">
    <link href="{{ url('default') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('default') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="auth-fluid-pages pb-0">
<div class="container py-5" style="max-width: 480px;">
    <div class="text-center mb-4">
        <img src="{{ url('/') }}/logo.svg" alt="PARTILOT" height="40">
        <h4 class="mt-3">Contraseña provisional</h4>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <p class="text-muted small">Su cuenta de gestor usa la contraseña por defecto. Por seguridad, establezca una nueva contraseña para el panel.</p>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('entity-manager.legacy-password.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password" minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" minlength="8">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-dark" style="border-radius: 30px;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <p class="text-center text-muted small mt-3">
        <form method="post" action="{{ url('logout') }}" class="d-inline">@csrf<button type="submit" class="btn btn-link btn-sm p-0">Cerrar sesión</button></form>
    </p>
</div>
</body>
</html>
