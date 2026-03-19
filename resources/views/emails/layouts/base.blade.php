<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notificación - Partilot' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
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
            font-size: 22px;
        }
        .content {
            margin-bottom: 30px;
        }
        .content p {
            margin-bottom: 12px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 14px;
            margin: 18px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        ul {
            padding-left: 18px;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $heading ?? 'Partilot' }}</h1>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Partilot</p>
        </div>
    </div>
</body>
</html>

