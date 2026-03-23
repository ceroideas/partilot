<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Enlace no válido | PARTILOT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ url('/') }}/logo.svg">
    <link href="{{ url('default') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="auth-fluid-pages pb-0">
<div class="container py-5" style="max-width: 480px;">
    <div class="text-center mb-4">
        <img src="{{ url('/') }}/logo.svg" alt="PARTILOT" height="40">
    </div>
    <div class="alert alert-warning">
        Este enlace no es válido, ya se ha utilizado o ha caducado. Solicite un nuevo acceso desde su administrador.
    </div>
    <p class="text-center"><a href="{{ route('login') }}" class="btn btn-outline-dark" style="border-radius: 30px;">Ir al inicio de sesión</a></p>
</div>
</body>
</html>
