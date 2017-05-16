$(document).ready(function () {
    $('.main-content').on('click', '.delete-group-denied', function (e) {
        e.preventDefault();
        var title = $(this).data('title'), text = $(this).data('description');
        swal($.extend({}, APP.swal.cb1Info(), {
            title: title,
            text: text,
            showConfirmButton: true,
            confirmButtonText: 'Ok',
            showCancelButton: false,
            closeOnConfirm: true
        }), function (confirmed) {
            $('.context-menu-list,#context-menu-layer').hide();
        });
        return false;
    });
});


