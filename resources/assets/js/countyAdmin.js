/**
 * Created by andrew on 04/04/20.
 */
$(document).ready(function () {
    $('.in_person_complaint_toggle').on('change', function() {

        let id = $(this)[0].id;
        let splitId = id.split('_');
        let county = splitId[4];

        console.log('county', county);

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
                county: county
            },

            success: function (data) {
                console.log(data);
                },
            error: function (data) {
            }
        });
    });
});