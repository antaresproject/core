$(document).ready(function () {
    $('.main-content').on('click', '.datatable-disable-selected-filter', function (e) {
        e.preventDefault();
        var handler = $(this).parent(), column = handler.closest('.card-filter').find('.datatables-card-filter').attr('column'), select = $('.filter-container select[name=' + column + ']');
        if (select.length > 0) {
            select.val(null).trigger("change");
        }
    });
    ready('.add-select-filter-button', function (element) {
        bindButton(element);
    });
    ready('.card-filter select', function (element) {
        var column = $(element).closest('.card-filter').find('div[column]').attr('column');
        $(element).on("change", function (evt) {
            $('.filter-container select[name=' + column + ']').val($(this).val()).trigger("change");
        });
        bindSelect($(element));
    });
    function bindButton(button) {
        $(button).on('click', function (e) {
            var element = $(this);
            e.preventDefault();
            var overlay = $(this).closest('.grid-stack-item-content'), table = null;
            if (overlay.length <= 0) {
                overlay = $(this).closest('.tbl-c');
                var table = overlay.find('[data-table-init]');
            }

            var handler = $(this), filterContainer = null, classname = null, column = null, select = $(this).closest('.filter-container').find('select');
            if (!select.length) {
                select = $(this).closest('.card-filter').find('select');
            }
            var values = select.val();

            if ($(this).closest('.card-filter').length > 0) {
                classname = handler.closest('.card-filter').find('.datatables-card-filter').data('classname');
                column = handler.closest('.card-filter').find('.datatables-card-filter').attr('column');
            } else {
                filterContainer = element.last().closest('.filter-container');
                classname = filterContainer.find('input.classname').attr('value');
                column = filterContainer.find('.filter-group-column').val();
            }
            overlay.LoadingOverlay('show');
            if (!$('#filter-save-url').length) {
                return false;
            }
            $.ajax({
                url: $('#filter-save-url').data('url'),
                type: 'POST',
                data: {
                    classname: classname,
                    params: {
                        column: column,
                        value: values
                    }
                },
                success: function (response) {
                    var logs = element.closest('.card--logs');
                    $('.card-filter div[column=' + column + ']').parent().remove();
                    $('.card-filter').append(response);
                    bindSelect($('.card-filter div[column=' + column + ']').parent().find('select'));
                    if ($('div[column=' + column + '] span').text().length <= 0) {
                        $('div[column=' + column + '] i').trigger('click');
                    }
                    if (table !== null) {
                        table.dataTable().api().draw();
                    }
                    overlay.LoadingOverlay('hide');
                    if (logs.length) {
                        logs.LoadingOverlay('show');
                        var url = logs.find('.card-ctrls').data('url');

                        $.ajax({
                            url: url,
                            success: function (response) {
                                var childrens = $(response).closest('.widget-ajax-response').children().length;
                                if (childrens > 1) {
                                    element.closest('.grid-stack-item-content').find('.card__content').html($(response).closest('.widget-ajax-response').html());
                                } else {
                                    var classname = $(response).attr('class').split(' ')[0], container = logs.closest('.' + classname);
                                    if (container.length > 0) {
                                        container.html($(response).html());
                                    }
                                }

                                logs.LoadingOverlay('hide');
                                bindSelect($('.filter-container select[name=' + column + ']'));
                            }
                        })
                    }
                },
            });

            return false;
        });
    }

    function bindSelect(element) {
        if (element.length > 0) {
            element.select2();
        }
    }
});
ready('.filter-multiple-select', function (element) {
    $(element).select2();
});