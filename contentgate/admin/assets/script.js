jQuery(document).ready(function ($) {

    function toggleContentGateFields() {
        var condition = $('#contentgate_condition').val();
        $('.contentgate-field').hide();

        if (condition === 'user_role') {
            $('.contentgate-roles').show();
        }
        if (condition === 'day_of_week') {
            $('.contentgate-days').show();
        }
    }

    toggleContentGateFields();
    $('#contentgate_condition').on('change', toggleContentGateFields);
});
