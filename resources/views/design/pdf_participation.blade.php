<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Participación PDF</title>
    <style>
        @page {
            margin:8mm 10mm;
        }
        {{-- *,
        *::before,
        *::after {
            box-sizing: border-box !important;
        } --}}

        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            margin: 10px 0 !important;
        }
        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            font-family: "Cerebri Sans,sans-serif";
            font-weight: 500;
            line-height: 1.1;
        }
        .h6, h6 {
            font-size: .75rem !important;
        }
        p {
            margin-top: 0;
            margin-bottom: 0;
        }
        [id*="containment-wrapper"] {
            position: relative;
            background-size: cover !important;
            background-repeat: no-repeat !important;
            background-position: center center !important;
        }
        * {font-family: Cerebri sans,sans-serif}
        .elements {
            width: 200px;
            border: 1px solid transparent;
            position: absolute !important;
            z-index: 1000;
        }
        .ck.ck-balloon-panel.ck-balloon-panel_toolbar_west.ck-balloon-panel_visible.ck-toolbar-container {
            z-index: 9999;
        }
        .elements.text:hover,.elements.text:focus {
            /*border: 1px dotted #c8c8c8;*/
        }
        .elements.qr {
            padding: 3px;
            border-radius: 8px;
            background-color: #fff;
        }
        .elements.images {
            height: auto !important;
        }
        .elements.images img {
            max-width: 100% !important;
            max-height: 100% !important;
            height: auto !important;
            width: auto !important;
            display: block;
        }
        a[disabled] {
            color: currentColor;
            cursor: not-allowed;
            opacity: 0.5;
            text-decoration: none;
            pointer-events: none;
        }
        .cke_notifications_area {
            display: none !important;
        }
        /* Optimizaciones para QR codes */
        .qr-code {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: -moz-crisp-edges;
            image-rendering: crisp-edges;
            image-rendering: pixelated;
        }

        .margen-izquierdo,.margen-arriba,.margen-derecho,.margen-abajo,.caja-matriz {
            display: none;
        }
        
        /* Aquí puedes pegar estilos de Bootstrap en el futuro si lo necesitas */
    </style>
</head>
<body>
@foreach($pages as $pageIndex => $page)
    <div class="participation-page" style="@if($pageIndex < count($pages) - 1) page-break-after: always; @endif">
        @for($i = 0; $i < count($page); $i++)
            @php
                $ticket = $page[$i];
                // Procesar HTML base
                $html = $participation_html;
                $html = str_replace(['00000000000000000000', '1/0001'], [$ticket['r'], '1/'.str_pad($ticket['n'], 4,'0',STR_PAD_LEFT)], $html);
                
                // Obtener QR code desde el array pre-generado (ya está en $qrCodes)
                $qrCodeBase64 = $qrCodes[$ticket['r']] ?? '';
                
                // Método simple: Reemplazar el span ui-draggable-handle con el QR code
                $html = str_replace(
                    '<span class="ui-draggable-handle"></span>',
                    '<img src="' . $qrCodeBase64 . '" class="qr-code" style="width: 60px; height: 60px; display: block;" alt="QR Code" />',
                    $html
                );
            @endphp
            <div class="participation-box" style="width: {{ 100/$cols }}%; float: left;">
                {!! $html !!}
            </div>
            @if(($i+1) % $cols == 0)
                <div style="clear: both;"></div>
            @endif
        @endfor
    </div>
@endforeach
</body>
</html> 