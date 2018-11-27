/**
 * Created by andrew on 11/11/18.
 */
$(document).ready(function () {

    $('#eviction_table').DataTable( {
        "pagingType": "simple"
    });

    $('.pdf_download_btn_dashboard').on('click', function () {
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
            url: '/online-eviction/pdf-data',
            dataType: 'json',
            data: data,

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