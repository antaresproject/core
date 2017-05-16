$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]:first').val()
        }
    });
    $('.mdl-tabs').on('click', 'a.mdl-tabs__tab', function (e) {
        e.preventDefault();
        $('.mdl-tabs a.mdl-tabs__tab').removeClass('is-active');
        $('.tab-content .mdl-tabs__panel').removeClass('is-active');
        handler = $(this);
        handler.addClass('is-active');
        href = handler.attr('href');
        $('.tab-content').find(href).addClass('is-active');
        $('.tab-content').find(href).find('.analyzer-report').css('width', '100%');
        return false;
    });

    function AjaxQueue() {
        var xhrPool = [];
        this.urls = [];
        this.container = $('.analyzer-container');
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

                    shouldBeActive = $('.analyzer-tabs .nav-tabs a.is-active').length === 0;

                    anchorAttributes = {
                        href: '#tab_' + key,
                        class: 'mdl-tabs__tab' + ((shouldBeActive) ? ' is-active' : '')
                    };
                    anchor = $('<a/>').attr(anchorAttributes).html(description);
                    anchor.appendTo($('.analyzer-tabs .nav-tabs'));

                    containerAttributes = {
                        class: "mdl-tabs__panel" + ((shouldBeActive) ? ' is-active' : ''),
                        id: 'tab_' + key
                    };

                    $('<div/>').attr(containerAttributes).appendTo($('.analyzer-tabs .tab-content'));
                    return false;
                },
                this.onSuccess = function (key, response) {
                    container = $('.analyzer-tabs');
                    container.find('.nav-tabs .indicator').remove();
                    container.find('.tab-content').find('.tab-pane.is-active').length == 1 ?
                            $('#tab_' + key).html(response) : $('#tab_' + key).addClass('is-active').html(response);

                    warnings = $(response).find('.alert-warning').length;
                    errors = $(response).find('.alert-error').length;
                    anchor = $('a[href="#tab_' + key + '"]');
                    currentAnchorText = anchor.text();

                    if (warnings > 0) {
                        anchor.html('<span class="mdl-badge mdl-badge--no-background" data-badge="' + warnings + '">' + currentAnchorText + '</span>');
                    }
                    if (errors > 0) {
                        anchor.html('<span class="mdl-badge" data-badge="' + errors + '">' + currentAnchorText + '</span>');
                    }
                    return false;
                },
                this.onError = function (key, error) {
                    swal($.extend({}, APP.swal.cb1Error(), {
                        title: error.statusText,
                        text: '',
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        closeOnCancel: true
                    }));
                    this.abort();
                    return false;
                },
                this.update = function (key) {
                    var self = this;
                    var urls = self.urls;
                    handler = $('.analyzer-container');
                    $.when(
                            $.ajax({
                                url: urls[key].url,
                                type: "POST",
                                beforeSend: function (element) {
                                    self.beforeSend(key, handler, urls[key].description);
                                },
                                success: function (response) {
                                    self.onSuccess(key, response);
                                },
                                error: function (error) {
                                    self.abort();
                                    self.onError(key, error);
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
                    $('.form-report').removeClass('hidden');
                    $('.analyzer-tabs .nav-tabs .indicator').remove();
                    $('.analyze-btn span').removeClass('indicator').removeClass('glyphicon-refresh').addClass('glyphicon-tasks');
                    $('.analyze-btn').removeAttr('disabled');
                    $('body').LoadingOverlay('hide');
                    return false;
                }
    }
    $('.analyze-btn').on('click', function (e) {
        e.preventDefault();
        handler = $(this);
        container = $('.analyzer-container');
        container.html('');
        $('.mdl-tabs .nav-tabs').html('');
        $('.mdl-tabs .tab-content').html('');
        $('body').LoadingOverlay('show', {preloader: true});
        var ajax = new AjaxQueue();

        $.ajax({
            url: handler.attr('href'),
            dataType: 'json',
            type: 'POST',
            success: function (response) {
                $('.analyzer-tabs').removeClass('hidden');
                ajax.init(response);
                ajax.update(0);
            },
            error: function (error) {
                $('<div/>').attr({
                    class: 'alert alert--bg alert--glow alert--error alert--xs alert--border mb20'
                }).html('<i class="alert__icon zmdi zmdi-alert-circle"></i> Error appears while running system analyzer. Please try again.').appendTo(container);
                ajax.onFinish();
                $('.form-report').addClass('hidden');
            }
        });

        return false;
    });
    $('.form-report').on('submit', function (e) {
        handler = $(this);
        src = '';
        $('.analyzer-tabs .tab-content .mdl-tabs__panel').each(function (index, item) {
            src += $(item).html();
        });
        handler.find('input:hidden[name="src"]').val(src);
        return true;

    });



});