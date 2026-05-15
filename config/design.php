<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vista HTML en lugar de PDF (depuración)
    |--------------------------------------------------------------------------
    |
    | Si es true, las rutas web síncronas de exportación de diseño
    | (participación, portadas, traseras) devuelven el HTML que DomPDF
    | renderizaría, para inspeccionar en el navegador el mismo markup
    | que genera el PDF. Los jobs en cola y saveGridPdfFacadeToPath
    | siguen generando PDF real.
    |
    | .env: DESIGN_PDF_HTML_PREVIEW=true
    |
    */
    'pdf_html_preview' => filter_var(env('DESIGN_PDF_HTML_PREVIEW', false), FILTER_VALIDATE_BOOLEAN),

];
