/**
 * Created by andrew on 11/11/18.
 */
$(document).ready(function () {

    $('#court_date').datepicker();
    $('#court_time').timepicker({
        interval: 5,
        beforeShow: function() {
            setTimeout(function () {
                $('.ui-timepicker').css('z-index', 99999999999999);
            }, 0)
        }
    });

    $('.calendar_tooltip').tooltip();

    $('.court_calendar').on('click', function() {
        var id = $(this)[0].id.split('_');
        $('#id_court_date').val(id[2]);

        var splitCourtDate = $('#court_date_' + id[2]).text().split(' ');

        $('#court_date').val('').datepicker('setDate', new Date(splitCourtDate[0]));
        $('#court_time').val('').timepicker('setTime', splitCourtDate[1] + splitCourtDate[2]);
    }).css( 'cursor', 'pointer' );


    $('#submit_date').on('click', function () {
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
            url: '/dashboard/storeCourtDate',
            dataType: 'json',
            data: {
                id: $('#id_court_date').val(),
                courtDate: $('#court_date').val(),
                courtTime: $('#court_time').val()
            },

            success: function (data) {
                console.log(data);
                location.reload();
            },
            error: function (data) {
                console.log(data);
                location.reload();
            }
        });
    });


    $('#eviction_table').DataTable( {
        "pagingType": "simple",
        "aaSorting": []
    }).on('click', '.eviction-remove', function () {
        var id = $(this)[0].id;
        var splitId = id.split('_');
        var conf = confirm('Are you sure you want to Delete ' + splitId[2] + ' ?');

        if (conf == true) {

            var evictionId = splitId[1];
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                },
                type: "POST",
                url: '/dashboard/delete',
                dataType: 'json',
                data: {id: evictionId},

                success: function (data) {
                    console.log(data);
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        } else {

        }
    }).on('click', '.pdf_download_btn_dashboard', function () {
        console.log('this is it' + $(this)[0].id);
        var id = $(this)[0].id;
        var splitId = id.split('_');

        $('#download_id').val(splitId[2]);

    }).on('change', '.status_select', function() {
        var id = $(this)[0].id;
        var splitId = id.split('_');
        var status = $('#status_' + splitId[1]).val();

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
                url: '/dashboard/statusChange',
                dataType: 'json',
                data: {
                    id: splitId[1],
                    status: status
                },

                success: function (data) {
                    console.log(data);
                },
                error: function (data) {
                    console.log(data);
                }
            });
    });
});