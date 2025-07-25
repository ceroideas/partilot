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
        /* Aquí puedes pegar estilos de Bootstrap en el futuro si lo necesitas */
    </style>
</head>
<body>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div style="clear: both;"></div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div style="clear: both;"></div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div style="clear: both;"></div><div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div style="clear: both;"></div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div style="clear: both;"></div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div class="participation-box" style="width: 50%; float: left">
        {!! $participation_html !!}
    </div>
    <div style="clear: both;"></div>
</body>
</html> 