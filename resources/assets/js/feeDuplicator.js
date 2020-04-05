/**
 * Created by andrew on 04/04/20.
 */
$(document).ready(function () {
    $('#fee_duplicate_court_select').select2({
        width: 'resolve'
    }).on('change', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "GET",
            url: '/feeDuplicator/getMagistrates',
            data: {
                courtNumber: $('#fee_duplicate_court_select').val()
            },

            success: function (data) {
                let optionValues = '';
               console.log(data);
               $.each(data, function(key, value) {
                   if (key === 0) {
                       $('#duplicated_magistrate').val(value.magistrate_id);
                       $('#first_magistrate').text(value.magistrate_id);
                   } else {
                       optionValues += '<option value="' + value.magistrate_id + '">' + value.magistrate_id + '</option>';
                   }
               });


                $('#fee_duplicate_magistrate_select').empty().append($(optionValues)).prop('disabled', false);
            },
            error: function (data) {
            }
        });
    });

    $('#fee_duplicate_magistrate_select').select2({
        width: 'resolve',
        closeOnSelect: false
    });


});