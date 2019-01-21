/**
 * Created by andrewgaidis on 1/17/19.
 */

$(document).ready(function () {

    $('.role_select').on('change', function() {
        var id = $(this)[0].id.split('_');
        var selectedRole = $(this).find(":selected").text();

        console.log(id[2]);
        console.log($(this).find(":selected").text());

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
            url: '/userManagement/changeRole',
            dataType: 'json',
            data: {
                id: id[2],
                role: selectedRole
            },

            success: function (data) {
                console.log(data);
            },
            error: function (data) {
            }
        });
    });


    $('.user_remove').on('click', function() {
        var id = $(this)[0].id.split('_');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var conf = confirm('Are you sure you want to Delete ' + id[2] + ' ?');

        if (conf == true) {

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                },
                type: "POST",
                url: '/userManagement/deleteUser',
                dataType: 'json',
                data: {
                    id: id[1]
                },

                success: function (data) {
                    console.log(data);

                },
                error: function (data) {
                }

            });
            location.reload();
        }
    }).css( 'cursor', 'pointer' );



});