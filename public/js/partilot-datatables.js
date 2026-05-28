/**
 * Listados del panel: un solo scroll horizontal (contenedor .dataTables_wrapper).
 * Evita doble barra por scrollX + overflow en card-body/table-responsive.
 */
(function ($) {
    if (typeof $.fn.dataTable !== 'function') {
        return;
    }

    function normalizeListTableOptions(opts) {
        if (!opts || typeof opts !== 'object') {
            return opts;
        }
        var o = $.extend({}, opts);
        delete o.scrollX;
        delete o.scrollCollapse;
        delete o.scrollY;
        if (o.autoWidth === undefined) {
            o.autoWidth = false;
        }
        return o;
    }

    var originalDataTable = $.fn.DataTable;

    $.fn.DataTable = function (opts) {
        if (typeof opts === 'object' && opts !== null && !(opts instanceof $)) {
            arguments[0] = normalizeListTableOptions(opts);
        }
        return originalDataTable.apply(this, arguments);
    };

    $.extend($.fn.DataTable, originalDataTable);
    $.fn.DataTable.Api = originalDataTable.Api;
    $.fn.dataTable = $.fn.DataTable;

    $(window).on('resize', function () {
        $('.dataTable').each(function () {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().columns.adjust();
            }
        });
    });
})();
