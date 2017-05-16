function AjaxQueue() {
    var xhrPool = [];
    this.urls = [];
    this.container = $('.create-sandbox-container');
    this.init = function (urls) {
        this.container.html('');
        this.urls = urls;
        $(document).ajaxSend(function (e, jqXHR, options) {
            xhrPool.push(jqXHR);
        });
        $(document).ajaxComplete(function (e, jqXHR, options) {
            xhrPool = $.grep(xhrPool, function (x) {
                return x != jqXHR
            });
        });
    },
            this.abort = function () {
                $.each(xhrPool, function (idx, jqXHR) {
                    jqXHR.abort();
                });
            },
            this.beforeSend = function (key, handler, description) {
                container = $('<h2/>', {
                    class: 'h2-' + key,
                    html: '<i class="zmdi zmdi-refresh indicator"></i> ' + description
                });
                container.appendTo(handler);
                return false;
            },
            this.onSuccess = function (key) {
                h2 = $('h2.h2-' + key);
                h2.find('i.zmdi').remove();
                h2.prepend('<i class="zmdi zmdi-check"></i>');
                return false;
            },
            this.createErrorContainer = function (container, html) {
                $('<div/>', {
                    class: 'alert alert--bg alert--glow alert--error alert--sm alert--border mb20',
                    html: '<i class="alert__icon zmdi zmdi-alert-circle"></i> ' + html
                }).appendTo(container);
                return false;
            },
            this.onError = function (key, error) {
                h2 = $('h2.h2-' + key);
                h2.find('i.zmdi').remove();
                h2.prepend('<i class="zmdi zmdi-close"></i>');
                errorContainer = h2.parent();
                if (error.responseJSON === undefined || error.responseJSON[0].length === 1) {
                    this.createErrorContainer(errorContainer, error.responseText);
                } else {
                    for (var i = 0; i < error.responseJSON.length; i++) {
                        this.createErrorContainer(errorContainer, error.responseJSON[i]);
                    }
                }
                return false;
            },
            this.rollback = function (handler, key) {
                var rollback = $('.sandbox-rollback');
                var self = this;
                $.ajax({
                    url: rollback.attr('href'),
                    dataType: 'json',
                    beforeSend: function (element) {
                        self.beforeSend(key, handler, rollback.attr('desc'));
                    },
                    success: function (response) {
                        self.onSuccess(key);
                    },
                    error: function (error) {
                        self.onError(key, error);
                    }
                });
                return false;
            },
            this.update = function (key) {
                var self = this;
                var urls = self.urls;
                handler = $('.create-sandbox-container');
                $.when(
                        $.ajax({
                            url: urls[key].url,
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function (element) {
                                self.beforeSend(key, handler, urls[key].description);
                            },
                            success: function (response) {
                                if (response.url !== undefined) {
                                    window.location.href = response.url;
                                }
                                self.onSuccess(key);
                            },
                            error: function (error) {
                                self.abort();
                                self.onError(key, error);
                                self.rollback(handler, key);
                            }
                        })
                        ).then(function (data, textStatus, jqXHR) {
                    if (urls[key + 1] !== undefined) {
                        self.update(key + 1);
                    } else {
                        self.abort();
                        self.onFinish();
                    }
                });
            },
            this.onFinish = function () {
                $('<button/>', {
                    class: 'btn btn-default',
                    text: 'close'
                }).on('click', function (e) {
                    e.preventDefault();
                    $.modal.close();
                    APP.swal.close();
                    return false;
                }).appendTo(this.container);
                return false;
            }
}
;

$(document).ready(function () {
    $('a.create-sandbox').on('click', function (e) {
        handler = $(this);
        e.preventDefault();
        if (handler.hasClass('sandbox-disabled')) {
            swal($.extend({}, APP.swal.cb1Info(), {
                title: handler.data('disabled'),
                text: '',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'OK',
                closeOnConfirm: false,
                closeOnCancel: true
            }));
            return false;
        }
        swal($.extend({}, APP.swal.cb1Warning(), {
            title: handler.data('title'),
            text: handler.data('description'),
            confirmButtonText: 'Yes',
            showCancelButton: true,
            cancelButtonText: 'No',
            closeOnConfirm: false,
            closeOnCancel: true
        }), function (isConfirm) {
            if (isConfirm) {
                APP.swal.close();
                var ajax = new AjaxQueue();
                var indexes = [];
                $('.sandbox-queue li').each(function (index, item) {
                    indexes.push({
                        url: $(item).attr('href'),
                        description: $(item).attr('desc')
                    });
                });
                APP.modal.init({
                    element: $('#Sandbox-Modal'),
                    title: $('#Sandbox-Modal').attr('title')
                });
                ajax.init(indexes);
                ajax.update(0);
            }
            return false;
        });
    });
});

