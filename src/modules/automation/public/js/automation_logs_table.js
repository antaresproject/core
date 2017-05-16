$(document).ready(function () {
    $('.tbl-c').on('click', '.show-full-log', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var title = $(this).data('title');

        APP.modal.init({
            element: ".show-logs-modal",
            title: title,
            buttons: {
                close: {
                    type: "default",
                    action: function () {
                        $.modal.close();
                    }
                }
            }
        });
        var id = $(this).data('id');
        $('.modal__content').html('');
        $('.modal__content').html($('div[rel=' + id + ']').html());
        return true;
    });
});