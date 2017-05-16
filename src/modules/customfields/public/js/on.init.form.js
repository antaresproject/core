$(document).ready(function () {

    // The maximum number of options
    var MAX_OPTIONS = 5;
    $('#surveyForm').on('click', '.addButton', function () {
        var $template = $('#optionTemplate');
        var $clone = $template.clone().removeClass('hide').removeAttr('id').insertBefore($template);
        var $option = $clone.find('[name="option[]"]');
    }).on('click', '.removeButton', function () {
        var $row = $(this).parents('.group-options'), $option = $row.find('[name="option[]"]');
        $row.remove();
    }).on('added.field.fv', function (e, data) {

        if (data.field === 'option[]') {
            if ($('#surveyForm').find(':visible[name="option[]"]').length >= MAX_OPTIONS) {
                $('#surveyForm').find('.addButton').attr('disabled', 'disabled');
            }
        }
    }).on('removed.field.fv', function (e, data) {
        if (data.field === 'option[]') {
            if ($('#surveyForm').find(':visible[name="option[]"]').length < MAX_OPTIONS) {
                $('#surveyForm').find('.addButton').removeAttr('disabled');
            }
        }
    });

    $('#fieldset').select2({
        tags: true,
        tokenSeparators: [','],
        width: '100%',
        dropdownAutoWidth: true,
        theme: "selectAR",
        allowClear: true,
        minimumResultsForSearch: Infinity,
    })

    $('#FieldCategorySelector').on("change", function (e) {
        action = $('#surveyForm').find('[name="path"]').val();
        categoryId = $('#FieldCategorySelector').val();
        window.location.href = action + '/category/' + categoryId;
    });
    $('#FieldGroupSelector').on("change", function (e) {
        action = $('#surveyForm').find('[name="path"]').val();
        categoryId = $('#FieldCategorySelector').val();
        groupId = $('#FieldGroupSelector').val();
        window.location.href = action + '/category/' + categoryId + '/group/' + groupId;
    });
    $('#FieldTypeSelector').on("change", function (e) {
        action = $('#surveyForm ').find('[name="path"]').val();
        categoryId = $('#FieldCategorySelector').val();
        groupId = $('#FieldGroupSelector').val();
        typeId = $('#FieldTypeSelector').val();
        window.location.href = action + '/category/' + categoryId + '/group/' + groupId + '/type/' + typeId;
    });
});