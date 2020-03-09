/**
 * Created by andrew on 10/8/18.
 */

$(document).ready(function () {

    $('#magistrate_table').DataTable( {
        "pagingType": "simple",
        "aaSorting": []
    }).on('click', '.magistrate-remove', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let conf = confirm('Are you sure you want to Delete ' + splitId[2]);

        if (conf === true) {

            let magistrateId = splitId[2];
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

                },
                error: function (data) {
                    console.log(data);
                }

            }); location.reload();

        }
    }).on('click', '.magistrate-edit', function () {
        console.log($('#edit_is_digital_signature_allowed').val());
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let magistrateId = splitId[2];

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
                $('#db_geo_id').val(data[0][0].id);
                $('#db_court_id').val(data[1].id);

                $('#edit_court_id').val(data[1].court_number);
                $('#edit_magistrate_id').val(data[1].magistrate_id);
                $('#edit_township').val(data[1].township);
                $('#edit_county').val(data[1].county);
                $('#edit_mdj_name').val(data[1].mdj_name);
                $('#edit_court_number').val(data[1].phone_number);
                $('#edit_court_address_one').val(data[0][0].address_line_one);
                $('#edit_court_address_two').val(data[0][0].address_line_two);
                $('#edit_one_under_2000').val(data[1].one_defendant_up_to_2000);
                $('#edit_one_btn_2000_4001').val(data[1].one_defendant_between_2001_4000);
                $('#edit_one_over_4000').val(data[1].one_defendant_greater_than_4000);
                $('#edit_one_oop').val(data[1].one_defendant_out_of_pocket);
                $('#edit_two_under_2000').val(data[1].two_defendant_up_to_2000);
                $('#edit_two_btn_2000_4001').val(data[1].two_defendant_between_2001_4000);
                $('#edit_two_over_4000').val(data[1].two_defendant_greater_than_4000);
                $('#edit_two_oop').val(data[1].two_defendant_out_of_pocket);
                $('#edit_three_under_2000').val(data[1].three_defendant_up_to_2000);
                $('#edit_three_btn_2000_4001').val(data[1].three_defendant_between_2001_4000);
                $('#edit_three_over_4000').val(data[1].three_defendant_greater_than_4000);
                $('#edit_three_oop').val(data[1].three_defendant_out_of_pocket);
                $('#edit_additional_tenants').val(data[1].additional_tenant);
                $('#edit_geo_locations').val(data[0][0].geo_locations);
                $('#edit_online_submission').val(data[1].online_submission);
                $('#edit_oop_additional_tenant_fee').val(data[1].oop_additional_tenant_fee);
                $('#edit_civil_mail_additional_tenant_fee').val(data[1].civil_mail_additional_tenant_fee);
                $('#edit_civil_constable_additional_tenant_fee').val(data[1].civil_constable_additional_tenant_fee);

                if (data[2] !== 'empty') {
                    console.log('im in here');
                    $('#db_civil_id').val(data[2].id);
                    $('#edit_one_under_500_mailed').val(data[2].under_500_1_def_mail);
                    $('#edit_one_btn_500_2000_mailed').val(data[2].btn_500_2000_1_def_mail);
                    $('#edit_one_btn_2000_4000_mailed').val(data[2].btn_2000_4000_1_def_mail);
                    $('#edit_one_btn_4000_12000_mailed').val(data[2].btn_4000_12000_1_def_mail);
                    $('#edit_two_under_500_mailed').val(data[2].under_500_2_def_mail);
                    $('#edit_two_btn_500_2000_mailed').val(data[2].btn_500_2000_2_def_mail);
                    $('#edit_two_btn_2000_4000_mailed').val(data[2].btn_2000_4000_2_def_mail);
                    $('#edit_two_btn_4000_12000_mailed').val(data[2].btn_4000_12000_2_def_mail);
                    $('#edit_one_under_500_constable').val(data[2].under_500_1_def_constable);
                    $('#edit_one_btn_500_2000_constable').val(data[2].btn_500_2000_1_def_constable);
                    $('#edit_one_btn_2000_4000_constable').val(data[2].btn_2000_4000_1_def_constable);
                    $('#edit_one_btn_4000_12000_constable').val(data[2].btn_4000_12000_1_def_constable);
                    $('#edit_two_under_500_constable').val(data[2].under_500_2_def_constable);
                    $('#edit_two_btn_500_2000_constable').val(data[2].btn_500_2000_2_def_constable);
                    $('#edit_two_btn_2000_4000_constable').val(data[2].btn_2000_4000_2_def_constable);
                    $('#edit_two_btn_4000_12000_constable').val(data[2].btn_4000_12000_2_def_constable);
                } else {
                    $('#db_civil_id').val('');
                    $('#edit_one_under_500_mailed').val('');
                    $('#edit_one_btn_500_2000_mailed').val('');
                    $('#edit_one_btn_2000_4000_mailed').val('');
                    $('#edit_one_btn_4000_12000_mailed').val('');
                    $('#edit_two_under_500_mailed').val('');
                    $('#edit_two_btn_500_2000_mailed').val('');
                    $('#edit_two_btn_2000_4000_mailed').val('');
                    $('#edit_two_btn_4000_12000_mailed').val('');
                    $('#edit_one_under_500_constable').val('');
                    $('#edit_one_btn_500_2000_constable').val('');
                    $('#edit_one_btn_2000_4000_constable').val('');
                    $('#edit_one_btn_4000_12000_constable').val('');
                    $('#edit_two_under_500_constable').val('');
                    $('#edit_two_btn_500_2000_constable').val('');
                    $('#edit_two_btn_2000_4000_constable').val('');
                    $('#edit_two_btn_4000_12000_constable').val('');
                }

                if (data[1].digital_signature === 1) {
                    $('#edit_is_digital_signature_allowed').prop('checked', true);
                }

                if (data[1].is_distance_fee === 1) {
                    $('#edit_ltc_is_driving_fee_allowed').prop('checked', true);
                }
                if (data[1].oop_distance_fee === 1) {
                    $('#edit_oop_is_driving_fee_allowed').prop('checked', true);
                }
                if (data[1].civil_distance_fee === 1) {
                    $('#edit_civil_is_driving_fee_allowed').prop('checked', true);
                }
            },
            error: function (data) {
                console.log(data);

            }
        });
    });

        $('#submit_magistrate').on('click', function () {
            let data = $('#magistrate_form').serialize();
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
                if (data.messageDetails === 'All Good') {
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
                dbCivilId: $('#db_civil_id').val(),
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
                oopAdditionalTenant: $('#edit_oop_additional_tenant_fee').val(),
                civilMailedAdditionalTenant: $('#edit_civil_mail_additional_tenant_fee').val(),
                civilConstableAdditionalTenant: $('#edit_civil_constable_additional_tenant_fee').val(),
                geoLocations: $('#edit_geo_locations').val(),
                digitalSignature: $('#edit_is_digital_signature_allowed')[0].checked,
                drivingFee: $('#edit_ltc_is_driving_fee_allowed')[0].checked,
                oopDrivingFee: $('#edit_oop_is_driving_fee_allowed')[0].checked,
                civilDrivingFee: $('#edit_civil_is_driving_fee_allowed')[0].checked,
                onlineSubmission: $('#edit_online_submission').val(),
                oneUnder500Mailed: $('#edit_one_under_500_mailed').val(),

                oneBtn500And2000: $('#edit_one_btn_500_2000_mailed').val(),
                oneBtn2000And4000Mailed: $('#edit_one_btn_2000_4000_mailed').val(),
                oneBtn4000And12000Mailed: $('#edit_one_btn_4000_12000_mailed').val(),
                twoUnder500Mailed:  $('#edit_two_under_500_mailed').val(),
                twoBtn500And2000Mailed:  $('#edit_two_btn_500_2000_mailed').val(),
                twoBtn2000And4000Mailed: $('#edit_two_btn_2000_4000_mailed').val(),
                twoBtn4000And12000Mailed: $('#edit_two_btn_4000_12000_mailed').val(),
                oneUnder500Constable: $('#edit_one_under_500_constable').val(),
                oneBtn500And2000Constable: $('#edit_one_btn_500_2000_constable').val(),
                oneBtn2000And4000Constable: $('#edit_one_btn_2000_4000_constable').val(),
                oneBtn4000And12000Constable: $('#edit_one_btn_4000_12000_constable').val(),
                twoUnder500Constable: $('#edit_two_under_500_constable').val(),
                twoBtn500And2000Constable:  $('#edit_two_btn_500_2000_constable').val(),
                twoBtn2000And4000Constable: $('#edit_two_btn_2000_4000_constable').val(),
                twoBtn4000And12000Constable:  $('#edit_two_btn_4000_12000_constable').val()
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