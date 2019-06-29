$(document).ready(function () {
    $('#step_1_btn').on('click', function(e) {
        let fileType = $('#file_type_select').val();
        let county = $('#county_select').val();
        let isFileValid = false;
        let isCountyValid = false;

        if (fileType === 'none') {
            isFileValid = false;
            $('#file_type_error').text('File Type cannot be Blank. Please select one of the options.');
        } else {
            $('#file_type_error').text('');
            isFileValid = true;

        }
        if (county === 'none') {
            isCountyValid = false;
            $('#county_error').text('County cannot be Blank. Please select one of the options.');
        } else {
            $('#county_error').text('');
            isCountyValid = true;
        }

        if (isFileValid === true && isCountyValid === true) {
            $('#new_file_form').submit();
        }
    });
});