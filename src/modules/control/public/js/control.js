$(document).ready(function () {
    /**
     * on expand all acl containers
     */
    $('.acl-expand-all').on('change', function (e) {
        e.preventDefault();
        panels = $(this).parents('.parent').find('.panel-collapse');
        if (this.checked) {
            panels.addClass('in').removeAttr('style');
        } else {
            panels.removeClass('in').attr('style', 'height:0px;');
        }
        return true;
    });
    /**
     * on resource action click
     */
    $('.acl-container').on('click', 'a.action-properties', function (e) {
        e.preventDefault();
        container = $('.properties-container');
        container.LoadingOverlay('show');
        handler = $(this);
        $.ajax({
            url: handler.attr('href'),
            success: function (response) {
                container.html(response);
                $('input:checkbox').iCheck({
                    checkboxClass: 'icheckbox_billevo'
                });
                container.LoadingOverlay('hide');


            },
            error: function (error) {
                container.LoadingOverlay('hide');
            }
        });
    });
    $('div.panel-group').on('submit', 'form.resource-actions', function (e) {
        e.preventDefault();
        handler = $(this);
        handler.LoadingOverlay('show');
        var message = null;
        var hasError = false;
        $.ajax({
            url: handler.attr('action'),
            data: handler.serialize(),
            type: 'POST',
            success: function (response) {
                handler.LoadingOverlay('hide');
                message = response.message;
            },
            error: function (error) {
                handler.LoadingOverlay('hide');
                message = error;
                hasError = true;
            }
        }).done(function () {
            attributes = {
                text: message,
                dismissQueue: true,
                layout: 'bottomRight',
                maxVisible: 10,
                timeout: 3000,
                animation: {
                    open: 'animated bounceInRight',
                    close: 'animated bounceOutRight',
                    easing: 'swing',
                    speed: 500
                }
            };
            notifierInstance = (hasError) ? APP.noti.errorFM("lg", "full") : APP.noti.successFM("lg", "full");
            noty($.extend({}, notifierInstance, attributes));
        });
        return false;
    });

    $('div.properties-container').on('submit', 'form.action-forms', function (e) {
        e.preventDefault();
        handler = $(this);
        handler.LoadingOverlay('show');
        var message = null;
        var hasError = false;
        $.ajax({
            url: handler.attr('action'),
            data: {_token: handler.find('input[name=_token]:first').val(), elements: handler.serialize()},
            type: 'POST',
            success: function (response) {
                handler.LoadingOverlay('hide');
                message = response.message;
            },
            error: function (error) {
                handler.LoadingOverlay('hide');
                message = error.message;
                hasError = true;
            }
        }).done(function () {
            noty({
                text: message,
                type: hasError === true ? 'error' : 'success',
                dismissQueue: true,
                layout: 'bottomRight',
                closeWith: ['click'],
                theme: 'relax',
                maxVisible: 10,
                timeout: 3000,
                animation: {
                    open: 'animated bounceInRight',
                    close: 'animated bounceOutRight',
                    easing: 'swing',
                    speed: 500
                }
            });
        });
        return false;
    });


});

