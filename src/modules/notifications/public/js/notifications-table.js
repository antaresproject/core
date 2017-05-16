//$(document).ready(function () {
//    $('.notifications-select-category', document).on('change', function (e) {
//        var table = $('.notifications-select-category').closest('.tbl-c').find('[data-table-init]');
//        if (table.length < 0) {
//            return false;
//        }
//        var api = table.dataTable().api();
//        var val = $.fn.dataTable.util.escapeRegex($(this).val());
//        api.column(3).search(val, true, false).draw();
//    });
//    $('.notifications-select-type', document).on('change', function (e) {
//        var table = $('.notifications-select-type').closest('.tbl-c').find('[data-table-init]');
//        if (table.length < 0) {
//            return false;
//        }
//        var api = table.dataTable().api();
//        var val = $.fn.dataTable.util.escapeRegex($(this).val());
//        api.column(4).search(val, true, false).draw();
//    });
//});