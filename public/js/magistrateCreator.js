/**
 * Created by andrew on 10/8/18.
 */
$(document).ready(function () {
    $('#submit_magistrate').on('click', function () {
        var data = $('#magistrate_form').serialize();
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
            url: '/magistrateCreator',
            dataType: 'json',
            data: data,

            success: function (data) {
                console.log(data);
                location.reload();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
    $('.magistrate-remove').on('click', function () {
        var id = $(this)[0].id;
        var splitId = id.split('_');

        confirm('Are you sure you want to Delete ' + splitId[2]);
    });
});