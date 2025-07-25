<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diseño PDF</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
        }
        .pdf-content {
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
        }
        /* Puedes agregar aquí estilos globales para impresión */
    </style>
</head>
<body>
    <div class="pdf-content">
        {!! $html !!}
    </div>
</body>
</html> 