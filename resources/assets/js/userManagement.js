/**
 * Created by andrewgaidis on 1/17/19.
 */

$(document).ready(function () {

    $('#user_table').DataTable({
        "pagingType": "simple",
        "pageLength": 50
    }).on('change', '.pay_type', function() {
        let id = $(this)[0].id.split('_');
        let payType = $(this)[0].value;
        let userId = id[2];

        console.log('userId', userId);
        console.log('payType', payType);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));},
            type: "POST",
            url: '/userManagement/pay-type',
            dataType: 'json',
            data: {
                userId: userId,
                payType: payType
            },

            success: function (data) {
                console.log(data);
            },

            error: function (data) {
            }
        });
    });


    $('.role_select').on('change', function() {
        var id = $(this)[0].id.split('_');
        var selectedRole = $(this).find(":selected").text();

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
            data: {
                id: id[2],
                role: selectedRole
            },

            success: function (data) {
                if (data['role'] == 'Court') {
                    $('#user_court_' + data['id']).prop('disabled', false);
                } else {
                    $('#user_court_' + data['id']).prop('disabled', true);
                }
            },
            error: function (data) {
            }
        });
    });

    $('.court_select').on('change', function() {
        var id = $(this)[0].id.split('_');
        var selectedCourt = $(this).find(":selected").text();

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
            url: '/userManagement/changeCourt',
            dataType: 'json',
            data: {
                id: id[2],
                court: selectedCourt
            },

            success: function (data) {
                console.log(data);
            },
            error: function (data) {
            }
        });
    });




    $('.user_remove').on('click', function() {
        let id = $(this)[0].id.split('_');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var conf = confirm('Are you sure you want to Delete ' + id[2] + ' ?');

        if (conf === true) {

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