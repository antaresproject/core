function UpdateModule() {
    this.container = null;
    this.handler = null;
    this.init = function (handler) {
        APP.swal.close();
        this.container = handler.parents('.container-fluid:first');
        this.handler = handler;
        this.container.LoadingOverlay('show');
        this.handler.find('i').removeClass('zmdi-long-arrow-up')
                .addClass('zmdi-refresh')
                .addClass('spin')
                .addClass('disabled')
                .attr('disabled', 'disabled');
        return false;
    },
            this.noty = function (message, type) {

                attrs = {
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
                notifierInstance = (type === 'success') ? APP.noti.successFM("lg", "full") : APP.noti.errorFM("lg", "full");
                noty($.extend({}, notifierInstance, attrs));

                this.container.LoadingOverlay('hide');
                this.handler.find('i')
                        .removeClass('zmdi-refresh')
                        .addClass('zmdi-long-arrow-up')
                        .removeClass('spin')
                        .removeClass('disabled')
                        .removeAttr('disabled');
            },
            this.onSuccess = function (response) {
                this.noty(response.message, 'success');
                setTimeout(function () {
                    location.reload();
                }, 1000);
                return false;
            },
            this.onError = function (message) {
                swal($.extend({}, APP.swal.cb1Error(), {
                    title: 'Update error',
                    html: message.statusText,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    showConfirmButton: false,
                    closeOnCancel: true
                }), function (isConfirm) {
                    if (isConfirm) {
                        callback();
                    }
                });
                return false;
            }
}
;
function Queue() {
    this.indexes = [];
    this.updateModule = null;
    this.init = function (urls) {
        this.indexes = urls;
        this.updateModule = new UpdateModule();
    },
            this.onBefore = function (key) {
                this.updateModule.init(this.indexes[key].item);
                return false;
            },
            this.onSuccess = function (key, response) {
                this.updateModule.onSuccess(response);
                return false;
            },
            this.onError = function (key, error) {
                this.updateModule.onError(error);
                return false;
            },
            this.update = function (key) {
                var self = this;
                var indexes = self.indexes;

                $.when(
                        $.ajax({
                            url: indexes[key].item.attr('href'),
                            type: "POST",
                            dataType: 'json',
                            beforeSend: function () {
                                self.onBefore(key);
                            },
                            success: function (response) {
                                self.onSuccess(key, response);
                            },
                            error: function (error) {
                                self.onError(key, error);
                            }
                        })
                        ).then(function (data, textStatus, jqXHR) {
                    if (self.indexes[key + 1] !== undefined) {
                        self.update(key + 1);
                    } else {
                        location.reload();
                    }
                });
            }
}
;
$(document).ready(function () {
    function Swaller(handler) {
        this.handler = handler;

        this.swal = function (callback) {
            swal($.extend({}, APP.swal.cb1Warning(), {
                title: handler.attr('data-title'),
                text: handler.attr('data-description'),
                html: false,
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
                closeOnCancel: true
            }), function (isConfirm) {
                if (isConfirm) {
                    callback();
                }
            });

        }
    }
    /**
     * updating system
     */
    function UpdateCore() {
        this.modalContainer = $('#SandBox-Modal');
        this.modalBody = $('#SandBox-Modal .update-container');
        this.handler = null;
        this.xhrPool = [];
        this.indexes = [];
        this.rollbacks = [];
        this.version = '';
        this.init = function (handler) {
            this.handler = handler;
            this.version = this.handler.attr('version');
            APP.swal.close();
            this.modalBody.html('');
            APP.modal.init({
                element: this.modalContainer,
                title: this.modalContainer.attr('title')
            });
            self = this;
            $(document).ajaxSend(function (e, jqXHR, options) {
                self.xhrPool.push(jqXHR);
            });
            $(document).ajaxComplete(function (e, jqXHR, options) {
                self.xhrPool = $.grep(self.xhrPool, function (x) {
                    return x != jqXHR
                });
            });

            $('.sandbox-queue li').each(function (index, item) {
                self.indexes.push({
                    url: $(item).attr('href'),
                    desc: $(item).attr('desc')
                });
            });
            $('.sandbox-rollback-queue li').each(function (index, item) {
                self.rollbacks.push({
                    url: $(item).attr('href'),
                    desc: $(item).attr('desc')
                });
            });
        },
                this.abort = function () {
                    $.each(this.xhrPool, function (idx, jqXHR) {
                        jqXHR.abort();
                    });
                },
                this.onBeforeSend = function (key, desc) {
                    container = $('<h2/>', {
                        class: 'h2-' + key,
                        html: '<i class="zmdi zmdi-refresh"></i>' + desc
                    });
                    container.appendTo(this.modalBody);
                    container.LoadingOverlay('show');
                    return container;
                },
                this.onSuccess = function (key, response, container) {
                    $('h2.h2-' + key).each(function (index, item) {
                        $(item).find('.zmdi').removeClass('zmdi-refresh').addClass('zmdi-check').LoadingOverlay('hide');
                        $(item).LoadingOverlay('hide');
                    });
                    if (response.url !== undefined) {
                        var win = window.open(response.url, '_blank');
                        win.focus();
                        this.abort();
                        $.modal.close();
                        swal($.extend({}, APP.swal.cb1Success(), {
                            title: 'Sandbox instance has been created sucessfully',
                            text: 'New system version has been opened in next browser tab.',
                            'showConfirmButton': false,
                            'showCancelButton': true,
                            'cancelButtonText': 'Close',
                            'closeOnCancel': true
                        }));
                    }
                },
                this.createAlert = function (html, container) {
                    if (html.length > 0) {
                        $('<div/>', {
                            class: 'alert alert--bg alert--glow alert--error alert--xs alert--border mb20',
                            html: '<i class="alert__icon zmdi zmdi-alert-circle"></i> ' + html
                        }).appendTo(container);
                    }
                },
                this.onError = function (key, error, container) {
                    var errorContainer = null;
                    if (container !== undefined) {
                        container.find('.zmdi').removeClass('zmdi-check').removeClass('zmdi-refresh').addClass('zmdi-close');
                        container.LoadingOverlay('hide');
                        errorContainer = container;
                    } else {
                        $('h2.h2-' + key).each(function (index, item) {
                            $(item).find('.zmdi').removeClass('zmdi-check').removeClass('zmdi-refresh').addClass('zmdi-close');
                            $(item).LoadingOverlay('hide');
                            errorContainer = $(item);
                        });
                    }
                    if (error.responseText.length > 255) {
                        this.createAlert(error.responseText, errorContainer);
                    } else if (error.responseJSON === undefined || error.responseJSON.length === 0 || error.responseJSON[0].length === 1) {
                        this.createAlert(error.responseText, errorContainer);
                    } else {
                        for (var i = 0; i < error.responseJSON.length; i++) {
                            this.createAlert(error.responseJSON[i], errorContainer);
                        }
                    }
                    this.modalBody.LoadingOverlay('hide');
                },
                this.update = function (key) {
                    var self = this;
                    $.when(
                            $.ajax({
                                url: self.indexes[key].url,
                                type: "POST",
                                data: {version: self.version},
                                dataType: 'json',
                                beforeSend: function (element) {
                                    self.onBeforeSend(key, self.indexes[key].desc);
                                },
                                success: function (response) {
                                    self.onSuccess(key, response);
                                },
                                error: function (error) {
                                    self.onError(key, error);
                                    self.rollback(0);
                                }
                            })
                            ).then(function (data, textStatus, jqXHR) {
                        if (self.indexes[key + 1] !== undefined) {
                            self.update(key + 1);
                        } else {
                            self.abort();
                        }
                    }
                    );
                },
                this.rollback = function (key) {
                    this.rollbackContainer = null;
                    self = this;
                    $.when(
                            $.ajax({
                                url: self.rollbacks[key].url,
                                type: "POST",
                                data: {version: self.version},
                                dataType: 'json',
                                beforeSend: function (element) {
                                    self.rollbackContainer = self.onBeforeSend(key, self.rollbacks[key].desc);
                                },
                                success: function (response) {
                                    self.onSuccess(key, response, self.rollbackContainer);
                                },
                                error: function (error) {
                                    self.onError(key, error, self.rollbackContainer);
                                }
                            })
                            ).then(function (data, textStatus, jqXHR) {
                        if (self.rollbacks[key + 1] !== undefined) {
                            self.rollback(key + 1);
                        } else {
                            self.abort();
                        }
                    }
                    );
                }
    }
    ;

    /**
     * mass update module
     */
    $('a.update-mass-confirm').on('click', function (e) {
        var handler = $(this);
        e.preventDefault();

        swaller = new Swaller(handler);
        swaller.swal(function () {
            var ajax = new Queue();
            var indexes = [];
            $('a.update-confirm').each(function (index, item) {
                indexes.push({
                    item: $(item)
                });
            });
            ajax.init(indexes);
            ajax.update(0);
        });
        return false;
    });

    /**
     * update module
     */
    $('a.update-confirm').on('click', function (e) {
        var handler = $(this);
        e.preventDefault();
        swaller = new Swaller(handler);
        swaller.swal(function () {
            var updateModule = new UpdateModule();
            updateModule.init(handler);
            $.ajax({
                url: handler.attr('href'),
                dataType: 'json',
                success: function (response) {
                    updateModule.onSuccess(response)
                },
                error: function (error) {
                    updateModule.onError(error);
                }
            });
        });
        return false;
    });
    $('a.begin-update').on('click', function (e) {
        handler = $(this);
        e.preventDefault();
        swaller = new Swaller(handler);
        swaller.swal(function () {
            var updateCore = new UpdateCore();
            updateCore.init(handler);
            updateCore.update(0);
            return false;
        });
    });
});