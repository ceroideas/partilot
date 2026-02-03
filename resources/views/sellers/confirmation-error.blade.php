<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Confirmación - Partilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .confirmation-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .icon-large {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .icon-warning {
            color: #ffc107;
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        .alert {
            margin: 20px 0;
            text-align: left;
        }
        .text-muted {
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="mb-4">
            <i class="ri-error-warning-line icon-large icon-warning"></i>
        </div>
        <h2 class="text-warning">Enlace Inválido</h2>
        <p class="lead mb-4">{{ $message }}</p>
        
        <div class="alert alert-warning">
            <strong>Posibles razones:</strong>
            <ul class="text-start mb-0">
                <li>El enlace ya ha sido utilizado</li>
                <li>El enlace ha expirado</li>
                <li>La solicitud ya fue procesada anteriormente</li>
            </ul>
        </div>
        
        <p class="text-muted">
            Si crees que esto es un error, por favor contacta con el administrador del sistema.
        </p>
        
        <div class="mt-4">
            <p class="text-muted small">
                Puedes cerrar esta ventana. Si necesitas ayuda, contacta con el administrador del sistema.
            </p>
        </div>
    </div>
</body>
</html>
