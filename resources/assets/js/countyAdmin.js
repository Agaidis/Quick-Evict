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

    $('#county_table').on('click', '.modal_note_btn', function () {

        let id = $(this)[0].id;
        let splitId = id.split('_');
        let county = splitId[3];
        $('#county').val(county);
        $('#new_note').focus();

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
            url: '/get-notes',
            data: {
                county: county
            },

            success: function (data) {

                if (data !== undefined && data !== '') {
                    let updatedNotes = '';

                    $.each(data, function (key, value) {
                        updatedNotes += '<span>'+value.notes+'</span>';
                    });
                    updatedNotes = $('<span>' + updatedNotes + '</span>');

                    $('#current_notes').empty().append(updatedNotes.html());
                } else {
                    $('#current_notes').empty();
                }

                console.log(data);

            },
            error: function (data) {
            }
        });
    });

    $('#notesModal').on('click', '#add_note', function () {
        let note = $('#new_note').val();
        let county = $('#county').val();

        console.log('county', county);
        console.log('note', note);

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
                $('#new_note').val('');
                $('#current_notes').val()

                if (data !== undefined && data !== '') {
                    let updatedNotes = '';

                    $.each(data, function (key, value) {
                        updatedNotes += '<span>'+value.notes+'</span>';
                    });
                    updatedNotes = $('<span>' + updatedNotes + '</span>');

                    $('#current_notes').empty().append(updatedNotes.html());
                } else {
                    $('#current_notes').empty();
                }

                console.log(data);

            },
            error: function (data) {
            }
        });
    });
});