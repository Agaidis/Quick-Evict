/**
 * Created by andrew on 10/8/18.
 */
$('#submit_magistrate').on('click', function() {
    var data = $('#eviction_form').serialize();
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
            location.reload();
        },
        error: function (data) {
            console.log(data);
        }
    });
});