Vue.config.debug = !0, Vue.config.devtools = !0, function () {
    new Vue({
        el: '.notification-widget-change-type-select',
        data: {
            url: $('.notification-widget-url').val(),
            container: $('.notification-widget-notifications-select'),
            testButton: $('.notification-widget-test-button')
        },
        ready: function () {
            this.bindChangeListener();
        },
        methods: {
            bindChangeListener: function () {

                var self = this, handler = $(this.$el);
                handler.on('change', function (e) {
                    self.lockSelect();
                    e.preventDefault();
                    $.ajax({
                        url: self.url,
                        type: 'POST',
                        data: {type: handler.val()},
                        success: function (response) {
                            self.onSuccess(response);
                        },
                        error: function () {
                            self.onError();
                        }
                    });
                });
            },
            lockSelect: function () {
                this.container.attr('disabled', 'disabled');
                this.container.closest('.grid-stack-item-content').LoadingOverlay('show');
            },
            onSuccess: function (response) {
                this.container.html('');
                options = '';
                for (var i = 0; i < response.length; i++) {
                    options += '<option value="' + response[i].id + '">' + response[i].title + '</option>'
                }
                this.container.html(options);
                this.onComplete();
            },
            onError: function () {
                swal($.extend({}, APP.swal.cb1Error(), {
                    title: "Error",
                    text: "Problem appears while getting notification list",
                    showConfirmButton: false,
                    showCancelButton: true,
                    closeOnCancel: true
                }));
                this.onComplete();
            },
            onComplete: function () {
                this.container.removeAttr('disabled');
                this.container.closest('.grid-stack-item-content').LoadingOverlay('hide');
            }
        },
    });
}.call(this);

$(document).ready(function () {
    $('.notification-widget-test-button').on('click', function (e) {
        e.preventDefault();
        handler = $(this);
        swal($.extend({}, APP.swal.cb1Warning(), {
            title: handler.attr('data-title'),
            text: "",
            showCancelButton: true,
            closeOnConfirm: false
        }), function (isConfirm) {
            if (isConfirm) {
                var form = handler.closest('form');
                $('<input />').attr({
                    type: 'hidden',
                    value: 1,
                    name: 'test'
                }).appendTo(form);
                APP.swal.close();
                return form.submit();
            }
        });
        return false;
    });
    $('.notification-widget-send-button').on('click', function (e) {
        e.preventDefault();
        handler = $(this);
        swal($.extend({}, APP.swal.cb1Warning(), {
            title: handler.attr('data-title'),
            text: "",
            showCancelButton: true,
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }), function (isConfirm) {

            if (isConfirm) {
                var form = handler.closest('form');
                form.find('input[name=test]').remove();
                APP.swal.close();
                return form.submit();
            }

        });
        return false;
    });
});