/**
 * Created by andrew on 10/8/18.
 */

    $(document).ready(function () {

        $('#magistrate_table').DataTable( {
            "pagingType": "simple"
        }).on('click', '.magistrate-remove', function () {
            var id = $(this)[0].id;
            var splitId = id.split('_');
            var conf = confirm('Are you sure you want to Delete ' + splitId[2]);

            if (conf == true) {

                var magistrateId = splitId[2];
                $.ajax({
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                    },
                    type: "POST",
                    url: '/magistrateCreator/delete',
                    dataType: 'json',
                    data: {id: magistrateId},

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
        }).on('click', '.magistrate-edit', function () {
            var id = $(this)[0].id;
            var splitId = id.split('_');
            var magistrateId = splitId[2];
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
                url: '/magistrateCreator/getMagistrate',
                dataType: 'json',
                data: {magistrateId: magistrateId},

                success: function (data) {
                    console.log(data);
                    $('#db_geo_id').val(data[0][0].id);
                    $('#db_court_id').val(data[1][0].id);
                    $('#edit_court_id').val(data[1][0].court_number);
                    $('#edit_magistrate_id').val(data[1][0].magistrate_id);
                    $('#edit_township').val(data[1][0].township);
                    $('#edit_county').val(data[1][0].county);
                    $('#edit_mdj_name').val(data[1][0].mdj_name);
                    $('#edit_court_number').val(data[1][0].phone_number);
                    $('#edit_court_address_one').val(data[0][0].address_line_one);
                    $('#edit_court_address_two').val(data[0][0].address_line_two);
                    $('#edit_one_under_2000').val(data[1][0].one_defendant_up_to_2000);
                    $('#edit_one_btn_2000_4001').val(data[1][0].one_defendant_between_2001_4000);
                    $('#edit_one_over_4000').val(data[1][0].one_defendant_greater_than_4000);
                    $('#edit_one_oop').val(data[1][0].one_defendant_out_of_pocket);
                    $('#edit_two_under_2000').val(data[1][0].two_defendant_up_to_2000);
                    $('#edit_two_btn_2000_4001').val(data[1][0].two_defendant_between_2001_4000);
                    $('#edit_two_over_4000').val(data[1][0].two_defendant_greater_than_4000);
                    $('#edit_two_oop').val(data[1][0].two_defendant_out_of_pocket);
                    $('#edit_three_under_2000').val(data[1][0].three_defendant_up_to_2000);
                    $('#edit_three_btn_2000_4001').val(data[1][0].three_defendant_between_2001_4000);
                    $('#edit_three_over_4000').val(data[1][0].three_defendant_greater_than_4000);
                    $('#edit_three_oop').val(data[1][0].three_defendant_out_of_pocket);
                    $('#edit_additional_tenants').val(data[1][0].additional_tenant);
                    $('#edit_geo_locations').val(data[0][0].geo_locations);
                },
                error: function (data) {
                    console.log(data);

                }
            });
        });

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
                    if (data.messageDetails == 'All Good') {
                        alertMsgCreate(true, data.responseMessage);
                        location.reload();
                    } else {
                        alertMsgCreate(false, data.responseMessage);
                    }

                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        $('#submit_edit').on('click', function () {
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
                url: '/magistrateCreator/editMagistrate',
                dataType: 'json',
                data: {
                    dbCourtId: $('#db_court_id').val(),
                    dbGeoId: $('#db_geo_id').val(),
                    magistrateId: $('#edit_magistrate_id').val(),
                    township: $('#edit_township').val(),
                    courtId: $('#edit_court_id').val(),
                    county: $('#edit_county').val(),
                    mdjName: $('#edit_mdj_name').val(),
                    courtNumber: $('#edit_court_number').val(),
                    addressOne: $('#edit_court_address_one').val(),
                    addressTwo: $('#edit_court_address_two').val(),
                    oneUnder2000: $('#edit_one_under_2000').val(),
                    oneBtn20004001: $('#edit_one_btn_2000_4001').val(),
                    oneOver4000: $('#edit_one_over_4000').val(),
                    oneOOP: $('#edit_one_oop').val(),
                    twoUnder2000: $('#edit_two_under_2000').val(),
                    twoBtn20004001: $('#edit_two_btn_2000_4001').val(),
                    twoOver4000: $('#edit_two_over_4000').val(),
                    twoOOP: $('#edit_two_oop').val(),
                    threeUnder2000: $('#edit_three_under_2000').val(),
                    threeBtn20004001: $('#edit_three_btn_2000_4001').val(),
                    threeOver4000: $('#edit_three_over_4000').val(),
                    threeOOP: $('#edit_three_oop').val(),
                    additionalTenant: $('#edit_additional_tenants').val(),
                    geoLocations: $('#edit_geo_locations').val()
                },
                success: function (data) {

                },
                error: function (data) {
                    console.log(data);
                }

            });
            setTimeout(function() { location.reload(); }, 1000);

        });
        //Creates a message when user performs an action using ajax i.e add, edit, delete
        function alertMsgCreate(isSuccess, msg) {
            var result = '';
            if (isSuccess) {
                result = $('<div class="alert alert-success">' + msg + '</div>');
                $('#flash-msg').append(result);
                setTimeout(function () {
                    $(".alert").alert('close');
                }, 4000);
            } else {
                result = $('<div class="alert alert-danger">' + msg + '</div>');
                $('#flash-msg').append(result);
                setTimeout(function () {
                    $(".alert").alert('close');
                }, 6000);
            }
        }
    });