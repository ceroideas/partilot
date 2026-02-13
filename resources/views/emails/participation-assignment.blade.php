<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Participaciones - Partilot</title>
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
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .assignment-item {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .assignment-item h3 {
            color: #007bff;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .assignment-item p {
            margin: 5px 0;
        }
        .highlight {
            font-weight: bold;
            color: #28a745;
            font-size: 1.1em;
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
            <h1>🎫 Asignación de Participaciones - Partilot</h1>
        </div>
        
        <div class="content">
            <p>Hola <strong>{{ $seller->name ?? $seller->email }}</strong>,</p>
            
            <p>Te informamos que se te han asignado <strong class="highlight">{{ array_sum(array_column($assignments, 'count')) }} participación(es)</strong> en total.</p>
            
            <div class="info-box">
                <p><strong>Detalles de las asignaciones:</strong></p>
            </div>
            
            @foreach($assignments as $assignment)
            <div class="assignment-item">
                <h3>📋 Set: {{ $assignment['set']->set_name ?? 'Set #' . $assignment['set']->set_number }}</h3>
                <p><strong>Número de participaciones asignadas:</strong> <span class="highlight">{{ $assignment['count'] }}</span></p>
                @if($assignment['lottery'])
                <p><strong>Sorteo:</strong> {{ $assignment['lottery']->name ?? 'N/A' }}</p>
                @if($assignment['lottery']->draw_date)
                <p><strong>Fecha del sorteo:</strong> {{ \Carbon\Carbon::parse($assignment['lottery']->draw_date)->format('d/m/Y') }}</p>
                @endif
                @endif
                @if($assignment['set']->entity)
                <p><strong>Entidad:</strong> {{ $assignment['set']->entity->name ?? 'N/A' }}</p>
                @endif
            </div>
            @endforeach
            
            <p>Puedes acceder a tu panel de vendedor para gestionar estas participaciones.</p>
            
            <p><em>Si tienes alguna pregunta, no dudes en contactarnos.</em></p>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Partilot. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
