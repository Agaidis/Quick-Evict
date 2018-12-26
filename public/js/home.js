/**
 * Created by andrew on 11/11/18.
 */
$(document).ready(function () {

    $('#eviction_table').DataTable( {
        "pagingType": "simple",
        "aaSorting": []
    }).on('click', '.eviction-remove', function () {
        var id = $(this)[0].id;
        var splitId = id.split('_');
        var conf = confirm('Are you sure you want to Delete ' + splitId[2] + ' ?');

        if (conf == true) {
            console.log(splitId[1]);

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