/**
 * Created by andrew on 11/11/18.
 */
$(document).ready(function () {

    $('#eviction_table').DataTable( {
        "pagingType": "simple"
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
                url: '/home/delete',
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

        console.log(splitId[2]);
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
            url: '/home/download',
            dataType: 'json',
            data: {id: splitId[2]},

            success: function (data) {
                console.log(data);
                //location.reload();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
});