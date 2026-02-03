<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Vendedor - Partilot</title>
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
        .icon-success {
            color: #28a745;
        }
        .icon-reject {
            color: #dc3545;
        }
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        .alert {
            margin: 20px 0;
            text-align: left;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
        }
        .text-muted {
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        @if($type === 'accept')
            <div class="mb-4">
                <i class="ri-checkbox-circle-line icon-large icon-success"></i>
            </div>
            <h2 class="text-success">¡Solicitud Aceptada!</h2>
            <p class="lead mb-4">{{ $message }}</p>
            
            @if($seller)
            <div class="alert alert-info">
                <strong>Detalles:</strong><br>
                Email: {{ $seller->email }}<br>
                Tipo: {{ $seller->seller_type === 'partilot' ? 'Vendedor Partilot' : 'Vendedor Externo' }}<br>
                Estado: <span class="badge bg-success">Activo</span>
            </div>
            @endif
            
            <p class="text-muted">
                Tu cuenta de vendedor ha sido activada. Ahora aparecerás en el listado de vendedores 
                y podrás comenzar a gestionar participaciones.
            </p>
        @else
            <div class="mb-4">
                <i class="ri-close-circle-line icon-large icon-reject"></i>
            </div>
            <h2 class="text-danger">Solicitud Rechazada</h2>
            <p class="lead mb-4">{{ $message }}</p>
            <p class="text-muted">
                La solicitud de vendedor ha sido cancelada y no se ha creado ninguna cuenta.
            </p>
        @endif
        
        <div class="mt-4">
            <p class="text-muted small">
                Puedes cerrar esta ventana. El proceso de confirmación ha finalizado.
            </p>
        </div>
    </div>
</body>
</html>
