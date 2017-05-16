$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]:first').val()
        }
    });
    $('.notification-select-type').on('change', function (e) {
        e.preventDefault();
        $('body').LoadingOverlay('show');
        window.location.href = $(this).attr('url') + '/' + $(this).val();
        return true;
    });
    function DataProvider() {
        this.getContent = function () {
            return CKEDITOR.instances[$('textarea.richtext:first').attr('name')].getData();
        }
    }
    dataProvider = new DataProvider();

    $('.notification-template-preview').on('click', function (e) {
        e.preventDefault();
        handler = $(this);
        modal = $('#notificationTemplatePreview');
        APP.modal.init({
            element: modal,
            title: modal.attr('title')
        });
        container = $('.template-preview-container');
        container.height(100);
        modalBody = container.parent();
        form = handler.parents('form:first');
        container.html('<h1>' + handler.attr('data-title') + '</h1>');
        $.ajax({
            url: handler.attr('url'),
            data: {title: form.find('.notification-title:first').val(), content: dataProvider.getContent(), type: $('.notification-select-type').val()},
            type: 'POST',
            success: function (response) {
                modalBody.LoadingOverlay('hide');
                container.html(response);
                height = container.find('.preview-response').height();
                targetHeight = height + 50;
                if (targetHeight > 600) {
                    targetHeight = 600;
                }
                container.height(targetHeight);
                var iframe = document.createElement('iframe');
                var frameborder = document.createAttribute("frameborder");
                frameborder.value = 0;
                iframe.setAttributeNode(frameborder);
                var hght = document.createAttribute("height");
                hght.value = '100%';
                iframe.setAttributeNode(hght);
                var wdth = document.createAttribute("width");
                wdth.value = '100%';
                iframe.setAttributeNode(wdth);
                iframe.src = 'data:text/html;charset=utf-8,' + encodeURI(container.find('.preview-response').html());
                container.html(iframe);

            },
            error: function (error) {
                swal($.extend({}, APP.swal.cb1Error(), {
                    title: 'Error appear while generating template preview',
                    text: error.statusText,
                    html: error.statusText,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    closeOnCancel: true
                }));
                modalBody.LoadingOverlay('hide');
            }
        });

        return false;
    });


    $('.send-test-notification').on('click', function (e) {
        e.preventDefault();
        handler = $(this);
        form = $('.send-test-notification').parents('form:first');
        form.LoadingOverlay('show');
        $.ajax({
            url: handler.attr('rel'),
            data: {
                title: form.find('.notification-title:first').val(),
                content: dataProvider.getContent(),
                type: form.find('#type').val()
            },
            type: 'POST',
            success: function (response) {
                form.LoadingOverlay('hide');
                swal($.extend({}, APP.swal.cb1Success(), {
                    title: response,
                    text: '',
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    closeOnCancel: true
                }));
            },
            error: function (error) {
                swal($.extend({}, APP.swal.cb1Error(), {
                    title: error.responseText,
                    text: '',
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    closeOnCancel: true
                }));
                form.LoadingOverlay('hide');

            }
        });

        return false;
    });

});
