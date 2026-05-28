<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Cuenta creada | PARTILOT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ url('default') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="auth-fluid-pages pb-0">
<div class="container py-5 text-center" style="max-width: 520px;">
    <img src="{{ url('/') }}/logo.svg" alt="PARTILOT" height="40" class="mb-4">
    <h4>Cuenta creada</h4>
    <p class="text-muted">Abre la app Partilot, inicia sesión con <strong>{{ $user->email }}</strong> y acepta la participación regalada desde tu cartera.</p>
</div>
</body>
</html>
