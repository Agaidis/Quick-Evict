/**
 * Created by andrew on 11/11/18.
 */
if (document.location.href.split('/')[3] === 'register') {

    console.log('its running');
    $('.company').select2({
        placeholder: 'Select an option'
    });
}
$(document).ready(function () {

    $('#court_date').datepicker();
    $('#court_time').timepicker({
        step: 5,
        minuteStep: 5
    });

    $('.calendar_tooltip').tooltip();

    $('.court_calendar').on('click', function() {
        let id = $(this)[0].id.split('_');
        $('#id_court_date').val(id[2]);

        let splitCourtDate = $('#court_date_' + id[2]).text().split(' ');

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
        "pageLength": 50,
        "aaSorting": [],
        "deferRender": true,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
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
        let id = $(this)[0].id;
        let splitId = id.split('_');
        console.log(splitId);

        if ($('#user_role').val() === 'Court') {
            $('#download_status_' + splitId[2]).text('Yes');
        }
        $('#download_id').val(splitId[2]);
    }).on('click', '.get_filings', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');

        $('#download_id').val(splitId[2]);

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
            url: '/get-filings',
            data: {
                id: splitId[2]
            },

            success: function (data) {
                console.log(data);
                let fileType = '';
                if (data.mainFiling.file_type === 'oop') {
                     fileType = 'Order of Possession';
                } else if (data.mainFiling.file_type === 'eviction'){
                     fileType = 'LTC';
                } else {
                     fileType = 'Civil Complaint';
                }

                $('#main_filing_id').val(data.mainFiling.id);

                let tableRow = '<tr>' +
                    '<td class="text-center">' + data.mainFiling.id + '</td> ' +
                    '<td class="text-center"><button type="submit" class="get_file btn btn-primary" id="main_file_'+data.mainFiling.id+'">' + fileType + '</button></td> ' +
                    '</tr>';
                for (let i = 0; i < data.filings.length; i++) {
                    tableRow += '<tr>' +
                        '<td class="text-center">' + data.filings[i].id + '</td> ' +
                        '<td class="text-center"><button type="submit" class="get_file btn btn-primary" id="file_address_'+data.filings[i].file_address+'">' + data.filings[i].original_file_name + '</button></td> ' +
                        '</tr>';
                }

                for (let i = 0; i < data.civilReliefFilings.length; i++) {
                    let fileNum = i + 1;
                    tableRow += '<tr>' +
                        '<td class="text-center">' + fileNum + '</td> ' +
                        '<td class="text-center"><button type="submit" class="get_file btn btn-primary" id="civil_relief_'+data.civilReliefFilings[i].id+'_'+data.civilReliefFilings[i].name+'">Servicemember Affidavit: ' +  data.civilReliefFilings[i].name + '</button></td> ' +
                        '</tr>';
                }


                $('.get_files_title').empty().text('Filings: ');
                $('#filing_body').empty().append(tableRow);
            },
            error: function (data) {
                console.log(data);
            }
        });

    }).on('change', '.status_select', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let status = $('#status_' + splitId[1]).val();

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
    }).on('click', '.eviction-edit', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let evictionId = splitId[1];

        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "GET",
            url: '/dashboard/edit-file',
            dataType: 'json',
            data: {id: evictionId},

            success: function (data) {
                if ( data.file_type === 'oop' ) {

                } else if ( data.file_type === 'eviction' ) {

                } else if ( data.file_type === 'civil complaint' ) {

                } else {
                    alert('File Type not known, please contact the development team');
                }
                console.log(data);
            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $('#county_select').on('change', function() {
        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "GET",
            url: '/dashboard/check-county',
            dataType: 'json',
            data: {
                county: $('#county_select').val()
            },

            success: function (data) {
                $('#file_type_select').prop('disabled', false);
                if (data === 1) {
                    $('#ltcA').prop('disabled', false);
                    $('#oopA').prop('disabled', false);
                } else {
                    $('#ltcA').prop('disabled', true);
                    $('#oopA').prop('disabled', true);
                }
                console.log('success', data);
            },
            error: function (data) {
                console.log('error', data);
            }
        });
    });

     $('#filing_body').on('click', '.get_file', function() {
         let id = $(this)[0].id;
         let splitId = id.split('_');
         let filingid = splitId[2];

         if (splitId[0] === 'main') {
             $('#file_type').val('main');
         } else if (splitId[0] === 'civil') {
             $('#civil_relief_name').val(splitId[3]);
             $('#civil_relief_filing_id').val(filingid);
             $('#file_type').val('civil');
         } else {
             $('#file_type').val('file');
         }

         $('#filing_original_name').val(filingid);
     });
});