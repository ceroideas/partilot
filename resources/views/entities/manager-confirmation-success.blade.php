<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Gestor - Partilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .box { background:#fff; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,.3); max-width:640px; width:100%; padding:40px; text-align:center; }
        .icon-large { font-size:80px; margin-bottom:20px; }
        .ok { color:#28a745; }
        .no { color:#dc3545; }
    </style>
</head>
<body>
    <div class="box">
        @if(($type ?? 'accept') === 'accept')
            <i class="ri-checkbox-circle-line icon-large ok"></i>
            <h2 class="text-success">Invitación aceptada</h2>
        @else
            <i class="ri-close-circle-line icon-large no"></i>
            <h2 class="text-danger">Invitación rechazada</h2>
        @endif
        <p class="lead">{{ $message ?? '' }}</p>
        <p class="text-muted">Puedes cerrar esta ventana.</p>
    </div>
</body>
</html>

