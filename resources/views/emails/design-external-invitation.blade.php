<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación diseño - Partilot</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #007bff; }
        .header h1 { color: #007bff; margin: 0; }
        .content { margin-bottom: 30px; }
        .content p { margin-bottom: 15px; }
        .btn { display: inline-block; padding: 14px 28px; margin: 15px 0; background-color: #ffc107; color: #212529; text-decoration: none; border-radius: 8px; font-weight: bold; }
        .btn:hover { background-color: #e0a800; }
        .info-box { background-color: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Diseño e impresión - Partilot</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Te han invitado a realizar un <strong>diseño de participaciones</strong> en Partilot.</p>
            @if($invitation->comment)
            <div class="info-box">
                <p><strong>Indicaciones del solicitante:</strong></p>
                <p>{{ $invitation->comment }}</p>
            </div>
            @endif
            <p>Haz clic en el botón siguiente para acceder al editor. Si no tienes cuenta, tendrás que registrarte o iniciar sesión en Partilot.</p>
        </div>
        <p style="text-align: center;">
            <a href="{{ $inviteUrl }}" class="btn">Abrir invitación y realizar diseño</a>
        </p>
        <div class="content">
            <p><small>Si el botón no funciona, copia y pega este enlace en tu navegador:</small><br>
            <a href="{{ $inviteUrl }}">{{ $inviteUrl }}</a></p>
        </div>
        <div class="footer">
            <p>Este es un correo automático. &copy; {{ date('Y') }} Partilot.</p>
        </div>
    </div>
</body>
</html>
