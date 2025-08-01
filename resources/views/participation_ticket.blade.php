<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participación de Lotería</title>
    <link rel="icon" href="{{ url('/logo.svg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .ticket-container {
            max-width: 520px;
            margin: 48px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 3.5rem 2.2rem 2.5rem 2.2rem;
        }
        .ticket-logo {
            display: block;
            margin: 0 auto 2rem auto;
            width: 110px;
        }
        .ticket-title {
            font-size: 2.1rem;
            font-weight: 700;
            color: #1a237e;
            text-align: center;
            margin-bottom: 1.2rem;
        }
        .ticket-numbers {
            font-size: 2.8rem;
            font-weight: 800;
            color: #1a237e;
            margin-bottom: 2rem;
            letter-spacing: 3px;
            text-align: center;
        }
        .ticket-section {
            margin-bottom: 1.7rem;
            font-size: 1.25rem;
        }
        .ticket-label {
            font-weight: 600;
            color: #333;
        }
        .ticket-value {
            font-weight: 400;
            color: #444;
        }
        .ticket-ref {
            font-size: 1.1rem;
            color: #607d8b;
            text-align: right;
        }
        .ticket-footer {
            font-size: 1.1rem;
            color: #888;
            text-align: center;
            margin-top: 2.5rem;
        }
        .alert {
            margin-bottom: 1.7rem;
            font-size: 1.15rem;
        }
        .winner-message {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .loser-message {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }
        .prize-info {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }
        .no-results {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }
        @media (max-width: 600px) {
            .ticket-container {
                max-width: 98vw;
                padding: 1.2rem 0.5rem 1.2rem 0.5rem;
            }
            .ticket-title {
                font-size: 1.3rem;
            }
            .ticket-numbers {
                font-size: 1.7rem;
            }
            .ticket-section {
                font-size: 1rem;
            }
            .ticket-footer {
                font-size: 0.95rem;
            }
            .winner-message,
            .loser-message,
            .prize-info,
            .no-results {
                font-size: 1rem;
                padding: 0.8rem;
            }
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
            <div class="ticket-numbers">
                {{ $ticket['numeros'] ?? '-' }}
            </div>
            
            @if(isset($prizeInfo))
                @if($prizeInfo['message'] === 'Resultados del sorteo no disponibles')
                    <div class="no-results">
                        <i class="ri-information-line me-2"></i>
                        {{ $prizeInfo['message'] }}
                    </div>
                @elseif($prizeInfo['isWinner'])
                    <div class="winner-message">
                        <i class="ri-award-line me-2"></i>
                        {{ $prizeInfo['message'] }}
                    </div>
                    <div class="prize-info">
                        <div><strong>{{ $prizeInfo['category'] }}</strong></div>
                        <div>Premio: {{ $prizeInfo['prizeAmount'] }}</div>
                    </div>
                @else
                    <div class="loser-message">
                        <i class="ri-close-circle-line me-2"></i>
                        {{ $prizeInfo['message'] }}
                    </div>
                @endif
            @endif
            <div class="ticket-section">
                <span class="ticket-label">Sorteo:</span>
                <span class="ticket-value">{{ $ticket['sorteo'] ?? '-' }}</span>
            </div>
            <div class="ticket-section">
                <span class="ticket-label">Fecha Sorteo:</span>
                <span class="ticket-value">{{ $ticket['fecha_sorteo'] ?? '-' }}</span>
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