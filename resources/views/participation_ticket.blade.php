<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participación de Lotería</title>
    <link rel="icon" href="{{ url('/logo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .ticket-container {
            max-width: 420px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
        }
        .ticket-logo {
            display: block;
            margin: 0 auto 1.5rem auto;
            width: 90px;
        }
        .ticket-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1a237e;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .ticket-section {
            margin-bottom: 1.2rem;
        }
        .ticket-label {
            font-weight: 500;
            color: #333;
        }
        .ticket-value {
            font-weight: 400;
            color: #444;
        }
        .ticket-ref {
            font-size: 0.95rem;
            color: #607d8b;
            text-align: right;
        }
        .ticket-footer {
            font-size: 0.9rem;
            color: #888;
            text-align: center;
            margin-top: 2rem;
        }
        .alert {
            margin-bottom: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <img src="{{ url('/logo.svg') }}" alt="Logo" class="ticket-logo">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @endif
        @if(isset($ticket))
            <div class="ticket-title">{{ $ticket['titulo'] ?? 'Participación de Lotería' }}</div>
            <div class="text-center" style="font-size:2.2rem; font-weight:700; color:#1a237e; margin-bottom:1.2rem; letter-spacing:2px;">
                {{ $ticket['numeros'] ?? '-' }}
            </div>
            <div class="ticket-section">
                <span class="ticket-label">Sorteo:</span>
                <span class="ticket-value">{{ $ticket['sorteo'] ?? '-' }}</span>
            </div>
            <div class="ticket-section">
                <span class="ticket-label">Jugado:</span>
                <span class="ticket-value">{{ $ticket['jugado'] ?? '-' }}€</span>
            </div>
            <div class="ticket-section">
                <span class="ticket-label">Nº Serie:</span>
                <span class="ticket-value">{{ $ticket['serie'] ?? '-' }}</span>
            </div>
            <div class="ticket-section ticket-ref">
                <span class="ticket-label">Referencia:</span>
                <span class="ticket-value">{{ $ticket['referencia'] ?? '-' }}</span>
            </div>
        @else
            <div class="alert alert-warning text-center">No se encontraron datos del ticket.</div>
        @endif
        <div class="ticket-footer">
            <div>Atención al cliente: <b>941 203 499</b></div>
            <div style="font-size:0.85em;">© {{ date('Y') }} Partilot</div>
        </div>
    </div>
</body>
</html> 