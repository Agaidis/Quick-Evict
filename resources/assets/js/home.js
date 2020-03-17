/**
 * Created by andrew on 11/11/18.
 */
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
        "aaSorting": [],
        "deferRender": true
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
                } else if (data.mainFiling.file_type === 'ltc'){
                     fileType = 'Eviction';
                } else {
                     fileType = 'Civil Complaint';
                }

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

     $('#filing_body').on('click', '.get_file', function() {
         let id = $(this)[0].id;
         let splitId = id.split('_');
         let filingName = splitId[2];
         if (splitId[0] === 'main') {
             console.log(splitId);
             $('#main_filing_id').val(filingName);
         }

         $('#filing_original_name').val(filingName);
     });
});