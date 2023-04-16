/**
 * Created by andrew on 04/04/20.
 */
$(document).ready(function () {
    $('.in_person_complaint_toggle').on('change', function() {

        let id = $(this)[0].id;
        let splitId = id.split('_');
        let county = splitId[4];
        let isChecked = $('#in_person_complaint_toggle_' + county)[0].checked;
        console.log('isChecked', isChecked);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "POST",
            url: '/countyAdmin',
            data: {
                county: county,
                isChecked: isChecked
            },

            success: function (data) {
                console.log(data);
                },
            error: function (data) {
            }
        });
    });

    $('#add_note').on('click', function () {
        let county = $('#county').val();
        let note = $('#new_note').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "POST",
            url: '/add-note',
            data: {
                county: county,
                note: note
            },

            success: function (data) {
                console.log(data);
            },
            error: function (data) {
            }
        });
    });
});