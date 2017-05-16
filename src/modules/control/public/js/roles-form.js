(function () {
    ready('ins.iCheck-helper', function (element) {
        $('input:checkbox.role-selector').on('ifChecked', function (e) {
            e.preventDefault();
            $('.roles-select-container').removeClass('hidden');
        });
        $('input:checkbox.role-selector').on('ifUnchecked', function (e) {
            e.preventDefault();
            $('.roles-select-container').addClass('hidden');
        });
    });

}).call(this);
