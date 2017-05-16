function AjaxQueue() {
    var xhrPool = [];
    this.urls = [];
    this.container = $('.update-production-container');
    this.runRollback = true;


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
                    html: '<i class="zmdi zmdi-refresh"></i> ' + description
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
                    class: 'alert alert--bg alert--glow alert--error alert--xs alert--border mb20',
                    html: '<i class="alert__icon zmdi zmdi-alert-circle"></i> ' + html
                }).appendTo(container);
                return false;
            },
            this.onError = function (key, error) {

                h2 = $('h2.h2-' + key);
                h2.find('i.zmdi').remove();
                h2.prepend('<i class="zmdi zmdi-close"></i>');
                errorContainer = h2.parent();

                if (error.responseJSON === undefined || (error.responseJSON[0] !== undefined && error.responseJSON[0].length === 1)) {
                    this.createErrorContainer(errorContainer, error.responseText);
                } else {
                    if (error.responseJSON.message !== undefined) {
                        this.createErrorContainer(errorContainer, error.responseJSON.message);
                    } else {
                        for (var i = 0; i < error.responseJSON.length; i++) {
                            this.createErrorContainer(errorContainer, error.responseJSON[i]);
                        }
                    }
                    if (error.responseJSON.action !== undefined && error.responseJSON.action === 'break') {
                        this.abort();
                        this.runRollback = false;
                        $.modal.close();
                        swal($.extend({}, APP.swal.cb1Error(), {
                            title: 'Unable to set this version of sandbox as primary instance',
                            text: 'Sandbox version is identical to primary system version.',
                            html: false,
                            showConfirmButton: false,
                            showCancelButton: true,
                            closeOnCancel: true,
                            cancelButtonText: "OK",
                        }));
                        return false;
                    }
                }
                return false;
            },
            this.rollback = function (handler, key) {
                var rollback = $('.production-rollback');
                var self = this;
                if (this.runRollback === false) {
                    return false;
                }
                $.ajax({
                    url: rollback.attr('href'),
                    dataType: 'json',
                    beforeSend: function (element) {
                        self.beforeSend(key, handler, rollback.attr('description'));
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
                APP.swal.close();
                handler = $('.update-production-container');
                modalHandler = $('#Production-Modal');
                APP.modal.init({
                    element: modalHandler,
                    title: modalHandler.attr('title')
                });
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
    $('.sandbox-production').on('click', function (e) {
        e.preventDefault();
        var description = $(this).attr('data-description');
        var title = $(this).attr('data-title');
        var href = $(this).attr('href');
        swal($.extend({}, APP.swal.cb1Warning(), {
            title: title,
            text: description,
            showCancelButton: true,
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }), function (isConfirm) {
            if (isConfirm) {
                var ajax = new AjaxQueue();
                $.ajax({
                    url: href,
                    dataType: 'json',
                    success: function (response) {
                        ajax.init(response);
                        ajax.update(0);
                    },
                    error: function (error) {
                        swal($.extend({}, APP.swal.cb1Error(), {
                            title: 'Error',
                            text: error.responseText,
                            showCancelButton: true
                        }));
                        ajax.onFinish();
                    }
                });
            }
        });
        return false;
    });
});