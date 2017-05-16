function Swaller(handler) {
    this.handler = handler;

    this.swal = function (callback) {
        swal($.extend({}, APP.swal.cb1Warning(), {
            title: handler.attr('data-title'),
            text: handler.attr('data-description'),
            html: false,
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: true
        }), function (isConfirm) {
            if (isConfirm) {
                callback();
            }
        });
    }
}
;

$(document).ready(function () {
    $('.backup-disabled', document).click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var handler = $(this);
        swal($.extend({}, APP.swal.cb1Info(), {
            title: handler.attr('data-disabled'),
            text: '',
            html: false,
            cancelButtonText: 'OK',
            showCancelButton: true,
            showConfirmButton: false,
            closeOnCancel: true,
            closeOnCancel: true
        }));
        return false;
    });
    function Backup() {
        this.h2 = null;
        this.buttonContainer = null;
        this.p = null;
        this.alert = null;
        this.init = function () {
            alert = $('.sweet-alert');
            this.alert = alert;
            h2 = alert.find('h2');
            p = alert.find('p');
            h2.html('Restoring...');
            h2.LoadingOverlay('show');
            this.h2 = h2;
            p.html('this process make take a while, please be patient...');
            this.p = p;
            buttonContainer = alert.find('.sa-button-container');
            buttonContainer.html('');
            this.buttonContainer = buttonContainer;

        },
                this.createCloseButton = function () {
                    self = this;
                    $('<button/>').attr({
                        class: 'cancel',
                        style: 'display: inline-block; box-shadow: none;'
                    }).text("Close").on('click', function (e) {
                        self.alert.LoadingOverlay('show');
                        e.preventDefault();
                        location.reload();
                    }).appendTo(buttonContainer);
                    this.h2.LoadingOverlay('hide');
                    return false;
                },
                this.onError = function (error) {
                    this.h2.html("Restoration failed...");
                    this.p.html('');
                    $('<div/>').attr({
                        'class': 'alert alert-danger'
                    }).html(error.responseText).appendTo(this.p);
                    this.createCloseButton();
                },
                this.onSuccess = function (response) {
                    this.h2.html("Restoration completed...");
                    this.p.html('');
                    this.createCloseButton();
                }
    }
    ;
    $(".dataTable").on("click", ".backup", function (e) {
        handler = $(this);
        swaller = new Swaller(handler);
        swaller.swal(function () {
            var backup = new Backup();
            backup.init();
            $.ajax({
                url: handler.attr('href'),
                dataType: 'json',
                success: function (response) {
                    backup.onSuccess(response);
                },
                error: function (error) {
                    backup.onError(error);
                }
            });
            return false;
        });
        return false;
    });

});

