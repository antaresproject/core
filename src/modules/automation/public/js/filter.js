$(document).ready(function () {
    $('.main-content').on('click', '.filter-container .datatables-filter-item', function (e) {
        e.preventDefault();
        var handler = $(this).find('a'),
                id = handler.attr('rel'),
                selected = handler.data('id'),
                overlayed = $('.ddown-multi__submenu'),
                value = $(this).find('.filter-value').val(),
                column = $(this).parents('.filter-container:first').find('.filter-group-column').val(),
                route = $('.filter-group-route').val(),
                classname = $(this).parents('.filter-container:first').find('input.classname').attr('value'),
                table = $(this).parents('.tbl-c').find('[data-table-init]');

        if ($('.datatables-card-filter[rel=' + id + ']').length > 0) {
            return false;
        }

        overlayed.LoadingOverlay('show');
        $.ajax({
            url: $('input.datatables-filter-store').val(),
            data: {
                route: route,
                classname: classname,
                params: {
                    column: column,
                    value: value,
                    selected: selected
                }
            },
            type: 'POST',
            success: function (response) {
                $('.card-filter').append(response);
                overlayed.LoadingOverlay('hide');
                table.dataTable().api().draw();
            },
            error: function (error) {
                overlayed.LoadingOverlay('hide');
            }
        });
        return false;
    });
    bindSelect($('.filter-script-name'));
    var table = $('.tbl-c').find('[data-table-init]');

    function lockMainFilter() {
        if ($('div[column=script_name] span').text().length > 0) {
            $('.filter-container select').attr('disabled', 'disabled');
        } else {
            $('.filter-container select').removeAttr('disabled');
        }
    }


    function bindSelect(handler) {
        handler.select2({
            'placeholder': 'Select script name...'
        });
        lockMainFilter();
        handler.on("change", function (evt) {
            var classname = $('.filter-script-name').parents('.filter-container:first').find('input.classname').attr('value');
            column = $('.filter-script-name').parents('.filter-container:first').find('.filter-group-column').val();
            var values = $(this).val();
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
                    $('.select2-container--open').removeClass('select2-container--open');
                    $('.card-filter div[column=' + column + ']').parent().remove();
                    $('.card-filter').append(response);
                    lockMainFilter();
                    bindSelect($('.card-filter').find('select'));
                    table.dataTable().api().draw();

                },
                complete: function () {
                    $('.card-filter').LoadingOverlay('hide');
                    if ($('div[column=' + column + '] span').text().length <= 0) {
                        $('div[column=' + column + '] i').trigger('click');
                    }
                    $('.filter-container .ddown-multi__submenu').hide();
                    $('.filter-container select option').each(function (index, item) {
                        $(item).removeAttr('selected');
                    });
                    $('.filter-container select').select2({
                        'placeholder': 'Select script name...'
                    });
                }
            });
        });
    }
});
