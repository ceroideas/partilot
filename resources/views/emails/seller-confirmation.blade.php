<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Vendedor - Partilot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .content {
            margin-bottom: 30px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-accept {
            background-color: #28a745;
            color: #ffffff;
        }
        .btn-accept:hover {
            background-color: #218838;
        }
        .btn-reject {
            background-color: #dc3545;
            color: #ffffff;
        }
        .btn-reject:hover {
            background-color: #c82333;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Solicitud de Vendedor - Partilot</h1>
        </div>
        
        <div class="content">
            <p>Hola,</p>
            
            <p>Has recibido una solicitud para convertirte en <strong>vendedor</strong> en la plataforma Partilot.</p>
            
            <div class="info-box">
                <p><strong>Detalles de la solicitud:</strong></p>
                <ul>
                    <li><strong>Email:</strong> {{ $seller->email }}</li>
                    @if($seller->name)
                    <li><strong>Nombre:</strong> {{ $seller->name }} {{ $seller->last_name ?? '' }}</li>
                    @endif
                    <li><strong>Tipo:</strong> {{ $seller->seller_type === 'partilot' ? 'Vendedor Partilot' : 'Vendedor Externo' }}</li>
                </ul>
            </div>
            
            <p>Para completar el proceso, necesitas <strong>aceptar o rechazar</strong> esta solicitud haciendo clic en uno de los botones siguientes:</p>
        </div>
        
        <div class="buttons">
            <a href="{{ $acceptUrl }}" class="btn btn-accept">✅ Aceptar Solicitud</a>
            <a href="{{ $rejectUrl }}" class="btn btn-reject">❌ Rechazar Solicitud</a>
        </div>
        
        <div class="content">
            <p><strong>¿Qué sucede si aceptas?</strong></p>
            <ul>
                <li>Tu cuenta de vendedor se activará</li>
                <li>Aparecerás en el listado de vendedores</li>
                <li>Podrás comenzar a gestionar participaciones</li>
            </ul>
            
            <p><strong>¿Qué sucede si rechazas?</strong></p>
            <ul>
                <li>La solicitud será cancelada</li>
                <li>No se creará ninguna cuenta de vendedor</li>
            </ul>
            
            <p><em>Si no realizaste esta solicitud, puedes ignorar este correo o rechazarla directamente.</em></p>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Partilot. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
