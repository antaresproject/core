ready('.preview-notification-log', function (element) {
    $(element).on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var isNotification = $(this).data('notification') === 1;
        if (isNotification) {
            var targetSidebar = $('aside.sidebar--preview'), sideBarLoader = $('.sidebar-loader').html();
            $('aside').removeClass('sidebar--open');
            targetSidebar.addClass('sidebar--open');
            targetSidebar.find('.sidebar__list').html(sideBarLoader);
            $.getJSON($(this).attr('href'), function (data) {
                targetSidebar.find('.sidebar__list').html(data.content);
            }).fail(function () {
                noty($.extend({}, APP.noti.errorFM("lg", "full"), {text: modal.data('error')}));
            });
        } else {
            var modal = $('.notification-log-preview-modal'), iframe = document.createElement('iframe'), frameborder = document.createAttribute("frameborder"), hght = document.createAttribute("height"), wdth = document.createAttribute("width");

            frameborder.value = 0;
            iframe.setAttributeNode(frameborder);
            hght.value = '100%';
            wdth.value = '100%';
            iframe.setAttributeNode(wdth);
            iframe.setAttributeNode(hght);
            iframe.src = $(this).attr('href');
            APP.modal.init({
                element: modal,
                title: '',
            });
            modal.find('.modal-body').html(iframe);
        }

        return false;
    });
});
$(document).ready(function () {
    $('.sidebar--preview .btn-close').click(function (e) {
        e.preventDefault();
        $('aside').removeClass('sidebar--open');
        return false;
    });
});