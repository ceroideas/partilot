<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolución a entidad - Partilot</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; background:#f4f4f4; }
        .container { background:#fff; padding: 28px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .header { border-bottom: 2px solid #007bff; padding-bottom: 18px; margin-bottom: 18px; }
        .header h1 { margin: 0; color:#007bff; font-size: 20px; }
        .box { background:#f8f9fa; border-left: 4px solid #007bff; padding: 14px 16px; margin: 14px 0; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; color:#666; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #eee; padding: 8px 0; text-align:left; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Devolución procesada - Partilot</h1>
    </div>

    @php
        $managerName = trim(($entityManagerUser?->name ?? '') . ' ' . ($entityManagerUser?->last_name ?? ''));
        $managerName = $managerName !== '' ? $managerName : ($entityManagerUser?->email ?? 'Gestor');
        $entityName = $devolution->entity?->name ?? '-';
        $lottery = $devolution->lottery;
        $returnedCodes = $returnedParticipations->map(fn($p) => $p->display_participation_code)->filter()->values()->all();
    @endphp

    <p>Hola <strong>{{ $managerName }}</strong>,</p>

    <p>Te informamos de que se ha procesado una <strong>devolución</strong> para la entidad <strong>{{ $entityName }}</strong>.</p>

    <div class="box">
        <p><strong>Sorteo:</strong> {{ $lottery?->name ?? 'N/A' }}</p>
        <p><strong>Fecha de devolución:</strong> {{ $devolution->devolution_date ? \Carbon\Carbon::parse($devolution->devolution_date)->format('d/m/Y') : '-' }}</p>
        <p><strong>Motivo:</strong> {{ $devolution->return_reason ?? '-' }}</p>
        <p><strong>Participaciones devueltas:</strong> {{ count($returnedCodes) }}</p>
    </div>

    @if(count($returnedCodes))
        <p><strong>Listado de participaciones devueltas:</strong></p>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returnedCodes as $code)
                    <tr>
                        <td>{{ $code }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No se registraron participaciones con acción de devolución (entidad -> administración) para este proceso.</p>
    @endif

    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>&copy; {{ date('Y') }} Partilot</p>
    </div>
</div>
</body>
</html>

