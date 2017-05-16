DateRangeFilter = function () {}, DateRangeFilter.elements = DateRangeFilter.elements || {};

DateRangeFilter.prototype.init = function () {
    var self = this;
    !function () {
        self.dateRangeBinder.bindDateRangePicker($('.daterangepicker-filter'));
        self.buttonsBinder.bindButton($('.add-daterange-button'));
    }();
};
DateRangeFilter.prototype.dateRangeBinder = {
    bindDateRangePicker: function (dateselector) {
        if (dateselector.length <= 0) {
            return false;
        }
        dateselector.daterangepicker({
            datepickerOptions: {
                numberOfMonths: 3,
                mirrorOnCollision: false,
                maxDate: null
            },
            dateFormat: 'yy-mm-dd'
        });
        var start = dateselector.data('start');
        var end = dateselector.data('end');
        if (start.length > 0 && end.length > 0) {
            dateselector.daterangepicker("setRange", {start: moment(start, "YYYY-MM-DD").toDate(), 'end': moment(end, "YYYY-MM-DD").toDate()});
        }

        var dateRangePicker = $('.card-filter').find('input.daterangepicker-filter');
        if (dateRangePicker.length > 0) {
            dateRangePicker.daterangepicker({
                change: function (event, data) {
                    var values = $.parseJSON($(this).val());
                    $('.filter-container').find('input.daterangepicker-filter').daterangepicker("setRange", {start: moment(values.start, "YYYY-MM-DD").toDate(), end: moment(values.end, "YYYY-MM-DD").toDate()});
                }
            });
        }
    }
};
DateRangeFilter.prototype.validator = {
    valid: function (value) {
        return value.length > 0;
    }
};
DateRangeFilter.prototype.buttonsBinder = {
    bindButton: function (element) {
        element.on('click', function (e) {
            var tableContainer = $(this).closest('.tbl-c');
            var table = tableContainer.find('[data-table-init]');

            e.preventDefault();

            var handler = $(this), input = handler.closest('.ddown__sgl--range').find('input.daterangepicker-filter'), value = input.val();

            if (!DateRangeFilter.validator.valid(value)) {
                return false;
            }

            var filterContainer = null, classname = null, column = null;

            if ($(this).closest('.card-filter').length > 0) {
                classname = handler.closest('.card-filter').find('.datatables-card-filter').data('classname');
                column = handler.closest('.card-filter').find('.datatables-card-filter').attr('column');
            } else {
                filterContainer = $(this).closest('.filter-container');
                classname = filterContainer.find('input.classname').attr('value');
                column = filterContainer.find('.filter-group-column').val();
            }
            if (classname === undefined || column === undefined) {
                return false;
            }
            tableContainer.LoadingOverlay('show');
            $.ajax({
                url: $('#filter-save-url').data('url'),
                type: 'POST',
                data: {
                    classname: classname,
                    params: {
                        column: column,
                        value: value
                    }
                },
                success: function (response) {
                    $('.card-filter div[column=' + column + ']').parent().remove();
                    $('.card-filter').append(response);
                    table.dataTable().api().draw();
                    var container = $('.card-filter div[column=' + column + ']').parent();
                    DateRangeFilter.dateRangeBinder.bindDateRangePicker(container.find('input:text'));
                    DateRangeFilter.buttonsBinder.bindButton(container.find('a.add-daterange-button'));
                    tableContainer.LoadingOverlay('hide');
                }
            });


            return false;
        });
    }

};
$(function () {
    window.DateRangeFilter = new DateRangeFilter(), DateRangeFilter.init();
});
