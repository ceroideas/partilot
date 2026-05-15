@php
    $use_prebuilt_cells = $use_prebuilt_cells ?? false;
    $pdfDocumentTitle = $pdfDocumentTitle ?? 'Participación PDF';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $pdfDocumentTitle }}</title>
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

        .format-box {
            padding: 0 !important;
            margin: 0 !important;
        }

        [id*="containment-wrapper"] {
            width: unset !important;
        }	

        .margen-izquierdo,.margen-arriba,.margen-derecho,.margen-abajo,.caja-matriz, button {
            display: none;
        }
        /* @if($use_prebuilt_cells)
        .participation-box .format-box,
        .participation-box [id*="containment-wrapper"] {
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .participation-box .h1, .participation-box .h2, .participation-box .h3,
        .participation-box .h4, .participation-box .h5, .participation-box .h6,
        .participation-box h1, .participation-box h2, .participation-box h3,
        .participation-box h4, .participation-box h5, .participation-box h6 {
            margin: 0 !important;
        }
        @endif */
        
        /* Aquí puedes pegar estilos de Bootstrap en el futuro si lo necesitas */
    </style>
</head>
<body>
@if(!empty($pdfHtmlPreviewBanner))
<div style="position:sticky;top:0;z-index:99999;background:#fef3c7;color:#78350f;padding:8px 12px;font-size:13px;text-align:center;border-bottom:1px solid #fcd34d;font-family:system-ui,sans-serif;">
    Vista previa HTML (DESIGN_PDF_HTML_PREVIEW=true). Es el mismo markup que recibe DomPDF; no es el archivo PDF.
</div>
@endif
@foreach($pages as $pageIndex => $page)
    <div class="participation-page" style="@if($pageIndex < count($pages) - 1) page-break-after: always; @endif">
        @for($i = 0; $i < count($page); $i++)
            @if($use_prebuilt_cells)
                @php $html = $page[$i]; @endphp
            @else
                @php
                    $ticket = $page[$i];
                    $html = $participation_html;
                    $html = str_replace(['00000000000000000000', '1/0001'], [$ticket['r'], '1/'.str_pad($ticket['n'], 4,'0',STR_PAD_LEFT)], $html);
                    $qrCodeBase64 = $qrCodes[$ticket['r']] ?? '';
                    $html = str_replace(
                        '<span class="ui-draggable-handle"></span>',
                        '<img src="' . $qrCodeBase64 . '" class="qr-code" style="width: 60px; height: 60px; display: block;" alt="QR Code" />',
                        $html
                    );
                @endphp
            @endif
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