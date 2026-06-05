<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de fecha límite - Partilot</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; background:#f4f4f4; }
        .container { background:#fff; padding: 28px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .header { border-bottom: 2px solid #f59e0b; padding-bottom: 18px; margin-bottom: 18px; }
        .header h1 { margin: 0; color:#b45309; font-size: 20px; }
        .box { background:#fffbeb; border-left: 4px solid #f59e0b; padding: 14px 16px; margin: 14px 0; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; color:#666; font-size: 12px; }
        .btn { display:inline-block; margin-top: 12px; padding: 10px 18px; background:#1f2430; color:#fff !important; text-decoration:none; border-radius: 999px; font-weight:600; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Aviso de fecha límite de devolución</h1>
    </div>

    <p>{{ $messageText }}</p>

    <div class="box">
        <p><strong>Entidad:</strong> {{ $entityName }}</p>
        <p><strong>Sorteo:</strong> {{ $lotteryName }}</p>
        <p><strong>Fecha límite:</strong> {{ $deadlineLabel }}</p>
        <p><strong>Participaciones pendientes:</strong> {{ $pendingCount }}</p>
    </div>

    <p>Accede al módulo de Devoluciones para registrar las devoluciones pendientes antes del cierre.</p>

    <a class="btn" href="{{ $devolutionsUrl }}">Ir a Devoluciones</a>

    <div class="footer">
        <p>Este es un aviso automático de Partilot. No respondas a este correo.</p>
    </div>
</div>
</body>
</html>
