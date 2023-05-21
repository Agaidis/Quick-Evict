
if (document.location.href.split('/')[3] === 'new-file') {
    $(document).ready(function () {

        $('[data-toggle="tooltip"]').tooltip();

        let text_max = 500;
        $('#textarea_feedback').html(text_max + ' characters remaining');

        $('#claim_description').on('keyup', function () {
            let text_length = $('#claim_description').val().length;
            let text_remaining = text_max - text_length;

            $('#textarea_feedback').html(text_remaining + ' characters remaining');
        });

        $('.use_signature').on('click', function(e) {
            if ($('#legal_checkbox').is(':checked') === false) {
                $('#terms_of_agreement_error_msg').text('You must accept to the terms of agreement. Check the box above.');
            } else {
                $('#terms_of_agreement_error_msg').text('');
                if ($('#user_pay_type').val() === 'full_payment') {
                    $('.payment_section').css('display', 'initial');
                } else {
                    $('.payment_section').css('display', 'none');
                }
                $('.pay_submit_section').css('display', 'initial');
            }
        });

        $('#preview_document').on('click', function() {
            $('#rented_by_val').val($('input[name=rented_by]:checked').val());
        });

        $('#filing_date').val(new Date());
        $('#landlord').prop('hidden', true);

        let map;
        let marker;
        let bounds;
        let houseNum;
        let streetName;
        let town;
        let county;
        let zipcode;
        let state;

        let center = new google.maps.LatLng(40.149660, -76.306370);
        //Create the areas for magistrates

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.144128, lng: -76.311420},
            zoom: 8,
            scaleControl: true
        });
        function ResizeMap() {
            google.maps.event.trigger(map, "resize");
        }

        $("#VehicleMovementModal").on('shown', function () {
            ResizeMap();
        });

        bounds = new google.maps.LatLngBounds();
        google.maps.event.addListenerOnce(map, 'tilesloaded', function (evt) {

            bounds = map.getBounds();
        });
        marker = new google.maps.Marker({
            position: center
        });


        let input = /** @type {!HTMLInputElement} */(
            document.getElementById('pac-input'));
        let types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        let autocomplete = new google.maps.places.Autocomplete(input);
        let magArray = [];
        let objArray = [];
        let magNamesArray = [];
        let count = 0;

        $.each(quickEvict.geoData, function(key, value) {
             magId = 'magistrate_' + value.magistrate_id;
             console.log(value.magistrate_id);
            let geoPoints = value.geo_locations.replace(/\s/g, '').replace(/},/g, '},dd').split(',dd');
            let obj = [];

            for (let i in geoPoints) {
                obj.push(JSON.parse(geoPoints[i]));
            }
            magNamesArray.push(magId);
            objArray.push(obj);
            magArray.push(magId);
            if (quickEvict.userId === 'Administrator') {
                magArray[count] = new google.maps.Polygon({
                    path: obj,
                    geodesic: true,
                    strokeColor: '#091096',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    fillColor: '#B1AAA9',
                    fillOpacity: 0.35,
                    areaName: magId,
                    courtId: value.court_number,
                    county: value.county,
                    township: value.township
                });
            } else {
                magArray[count] = new google.maps.Polygon({
                    path: obj,
                    geodesic: true,
                    areaName: magId,
                    courtId: value.court_number,
                    county: value.county,
                    township: value.township
                });
            }
            magArray[count].setMap(map);

            if (quickEvict.userId === 'Administrator') {
                google.maps.event.addListener(magArray[count], 'mouseover', function (e) {
                    let magistrateId = $(this)[0].areaName.split('magistrate_');
                    injectTooltip(e, magistrateId[1] + '<br>' + $(this)[0].county + '<br>' + $(this)[0].township);
                });

                google.maps.event.addListener(magArray[count], 'mousemove', function (e) {
                    moveTooltip(e);
                });

                google.maps.event.addListener(magArray[count], 'mouseout', function (e) {
                    deleteTooltip(e);
                });
            }
            count++;
        });
        autocomplete.addListener('place_changed', function () {
            marker.setMap(null);
            let place = autocomplete.getPlace();
            newBounds = bounds;
            if (!place.geometry) {
                window.alert("Returned place contains no geometry");
                return;
            }


            houseNum = place.address_components[0].long_name;
            streetName = place.address_components[1].long_name;

            if (place.address_components[3].types[0].indexOf('administrative') >= 0) {
                town = place.address_components[2].long_name;
            } else {
                town = place.address_components[3].long_name;
            }

            county = place.address_components[3].long_name;
            state = place.address_components[4].short_name;

            console.log('Here are the place Components: ', place.address_components);
            console.log('Length: ', place.address_components.length);

            if (place.address_components.length === 8) {
                zipcode = place.address_components[6].long_name;
            } else if (place.address_components.length === 9) {
                zipcode = place.address_components[7].long_name;
            } else if (place.address_components.length === 6) {
                zipcode = place.address_components[5].long_name;
            } else {
                county = place.address_components[2].long_name;
                state = place.address_components[3].short_name;
                zipcode = place.address_components[5].long_name;
            }

            $('#state').val('PA');
            $('#zipcode').val(zipcode);
            $('#county').val(county);
            $('#house_num').val(houseNum);
            $('#street_name').val(streetName);
            $('#town').val(town);
            $('#incident_display_address').html(houseNum + ' ' + streetName + '<br><span id="incident_second_line" style="margin-left:43%;"> ' + town + ', ' + 'PA' + ' ' + zipcode + '</span>');

            marker.setPosition(place.geometry.location);
            marker.setMap(map);
            newBounds.extend(place.geometry.location);
            map.fitBounds(newBounds);
            let isFound = false;
            for (let k = 0; k < magArray.length; k++) {
                if (google.maps.geometry.poly.containsLocation(place.geometry.location, magArray[k])) {
                    $('#court_number').val(magArray[k].areaName);
                    isFound = true;
                }
            }
            if (isFound === false) {
                alert('Address is either in a different county or outside all zones. Please go back to step 1 and verify you selected the right county.');
                $('.zipcode_div').css('display', 'none');
                $('.unit_number_div').css('display', 'none');
                $('.filing_form_div').css('display', 'none');
            } else {
                $('.zipcode_div').css('display', 'block');
                $('.unit_number_div').css('display', 'block');
                $('.filing_form_div').css('display', 'block');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                 $.ajax({
                     beforeSend: function (xhr) {
                         xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                     },
                     url : '/get-signature-type',
                     type : 'POST',
                     data : {
                         'courtNumber' : $('#court_number').val()
                     },
                     success : function(data) {
                        if (data[0].digital_signature === 0) {
                            $('#finalize_document').css('display', 'none');
                        }
                         let validEmails = ['brc@saxtonstump.com', 'tiffanymitchell0202@gmail.com', 'sparkleclean85@gmail.com', 'andrew.gaidis@gmail.com', 'erin@courtzip.com', 'andrew@home365.co'];

                        if (data[0].online_submission !== 'of' && (quickEvict.userEmail.indexOf('slatehousegroup') === -1 && (quickEvict.userEmail.indexOf('home365.co') === -1 && quickEvict.userEmail.indexOf('elite.team') === -1 && quickEvict.userEmail.indexOf('cnmhousingsolutions') === -1) && validEmails.includes(quickEvict.userEmail) === false)) {
                            alert('Sorry, but this magistrate is currently not accepting online submissions');
                            window.location.replace("/dashboard");
                        }
                     },
                     error : function(data)
                     {

                     },
                 });
            }
        });


        $(window).keydown(function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                return false;
            }
        });

        let residedHouseNum;
        let residedStreetName;
        let residedTown;
        let residedCounty;
        let residedState;
        let residedZipcode;

        $('input[type=radio][name=does_tenant_reside]').change(function () {

            if ($(this)[0].id === 'tenant_resides') {
                $('#incident_address_descriptor').text('Incident/Tenant Address: ');
                $('#tenant_resides_other_address_div').css('display', 'none');
                $('#incident_display_address').html(houseNum + ' ' + streetName + '<br><span id="incident_second_line" style="margin-left:43%;"> ' + town + ', ' + 'PA' + ' ' + zipcode + '</span>');
            } else {
                $('#incident_address_descriptor').text('Incident Address: ');
                $('#incident_second_line').css('margin-left', '30%');
                let input = document.getElementById('reside_address');
                let autocompleteResideAddress = new google.maps.places.Autocomplete(input);

                autocompleteResideAddress.addListener('place_changed', function () {
                    let residedPlace = autocompleteResideAddress.getPlace();

                    residedHouseNum = residedPlace.address_components[0].long_name;
                    residedStreetName = residedPlace.address_components[1].long_name;

                    if (residedPlace.address_components[3].types[0].indexOf('administrative') >= 0) {
                        residedTown = residedPlace.address_components[2].long_name;
                    } else {
                        residedTown = residedPlace.address_components[3].long_name;
                    }

                    residedCounty = residedPlace.address_components[3].long_name;
                    residedState = residedPlace.address_components[4].short_name;

                    if (residedPlace.address_components[6].short_name === 'US') {
                        residedZipcode = residedPlace.address_components[7].long_name;
                    } else {
                        residedZipcode = residedPlace.address_components[6].long_name;
                    }

                    $('#resided_zipcode').val(residedZipcode);
                    $('#resided_county').val(residedCounty);
                    $('#resided_house_num').val(residedHouseNum);
                    $('#resided_street_name').val(residedStreetName);
                    $('#resided_town').val(residedTown);
                    $('#resided_state').val();
                    $('#tenant_display_address').html('<span style="font-weight:bold;">Tenant Address</span>: <span style="font-weight:normal;" id="tenant_display_address">' +
                        '' + residedHouseNum + ' ' +   residedStreetName + '<br><span style="margin-left:28%;"> ' + residedTown + ', ' + residedState + ' ' + residedZipcode + '</span></span>');
                });

                $('#tenant_resides_other_address_div').css('display', 'block');
            }
        });

        $('input[type=radio][name=rented_by]').change(function () {

            if ($(this)[0].id === 'rented_by_other') {
                $('#landlord').prop('hidden', false);
                $('#rented_by_other_div').css('display', 'block');
                $('#rented_by_owner_div').css('display', 'none');
            } else {
                $('#landlord').prop('hidden', true);
                $('#rented_by_other_div').css('display', 'none');
                $('#rented_by_owner_div').css('display', 'block');

            }
        });

        $('input[type=radio][name=addit_rent]').change(function () {

            if ($(this)[0].id === 'addit_rent') {
                $('.additional_rent_amt_div').css('display', 'block');

            } else {
                $('.additional_rent_amt_div').css('display', 'none');
            }
        });


        //create a global variable that will point to the tooltip in the DOM
        let tipObj = null;

//offset along x and y in px
        let offset = {
            x: 6,
            y: -300
        };

        let coordPropName = null;

        function injectTooltip(event, data) {
            if (!tipObj && event) {
                //create the tooltip object
                tipObj = document.createElement("div");
                tipObj.style.width = '80px';
                tipObj.style.height = '80px';
                tipObj.style.background = "lightgrey";
                tipObj.style.borderRadius = "3px";
                tipObj.style.padding = "6px";
                tipObj.style.fontFamily = "Arial,Helvetica";
                tipObj.style.textAlign = "center";
                tipObj.style.fontSize = "10";
                tipObj.innerHTML = data;

                //fix for the version issue
                eventPropNames = Object.keys(event);
                if(!coordPropName){
                    //discover the name of the prop with MouseEvent
                    for(let i in eventPropNames){
                        let name = eventPropNames[i];
                        if(event[name] instanceof MouseEvent){
                            coordPropName = name;
                            break;
                        }
                    }
                }

                if(coordPropName) {
                    //position it
                    tipObj.style.position = "fixed";
                    tipObj.style.top = event[coordPropName].clientY + window.scrollY + offset.y + "px";
                    tipObj.style.left = event[coordPropName].clientX + window.scrollX + offset.x + "px";

                    //add it to the body
                    document.body.appendChild(tipObj);
                }
            }
        }

        /********************************************************************
         * moveTooltip(e)
         * update the position of the tooltip based on the event data
         ********************************************************************/
        function moveTooltip(event) {
            if (tipObj && event) {
                //position it
                tipObj.style.top = event.Ba.clientY + window.scrollY + offset.y + "px";
                tipObj.style.left = event.Ba.clientX + window.scrollX + offset.x + "px";
            }
        }

        /********************************************************************
         * deleteTooltip(e)
         * delete the tooltip if it exists in the DOM
         ********************************************************************/
        function deleteTooltip(event) {
            if (tipObj) {
                //delete the tooltip if it exists in the DOM
                document.body.removeChild(tipObj);
                tipObj = null;
            }
        }

        $('#tenant_num_select').on('change', function() {
            let tenantNum = $(this)[0].value;
            let html = '';

            $('#tenant_num').val(tenantNum);

            for (let i = 1; i <= tenantNum; i++) {
                let currentTenantObj = $('#tenant_name_' + i);

                if (currentTenantObj.length > 0) {
                    html += '<label class="labels" for="tenant_name_'+ i +'" >Name '+ i +'</label>' +
                        '<input class="form-control eviction_fields tenant_names" placeholder="Name '+ i +'" type="text" id="tenant_name_'+ i +'" name="tenant_name[]" value="' + currentTenantObj.val() + '"/><br>' +
                        '<h5>Servicemembers Civil Relief Act Affidavit (link to website)</h5>' +
                        '<input type="radio" class="is_military" id="is_military_' + i + '" name="tenant_military_' + i + '" value="military" />' +
                        '<label class="military_label" for="is_military_' + i + '">I have personal knowledge that the defendant named above is in military service.</label><br> ' +
                        '<input type="radio" checked class="is_not_military" id="is_not_military_' + i + '" name="tenant_military_' + i + '" value="not military" />' +
                        '<label class="military_label" for="is_not_military_' + i + '">I have personal knowledge that the defendant named above is not in the military service.</label><br>' +
                        '<input type="radio" class="unable_determine_military" id="unable_determine_military_' + i + '" name="tenant_military_' + i + '" value="unable determine military" />' +
                        '<label class="military_label" for="unable_determine_military_' + i + '">I am unable to determine whether the defendant named above is in the military service.</label><br>' +
                    '<div class="tenant_explanation_div">The following facts support the above statement (explain how you know the defendant is or is not in military service, or, if unable to make a determination, the steps you took to investigate the defendant\'s military status):</div>' +
                    '<textarea class="form-control tenant_military_explanation" name="tenant_military_explanation_' + i + '" style="height:120px; width:70%;"></textarea><hr/>';
                } else {
                    html += '<label class="labels" for="tenant_name_'+ i +'" >Name '+ i +'</label>' +
                        '<input class="form-control eviction_fields tenant_names" placeholder="Name '+ i +'" type="text" id="tenant_name_'+ i +'" name="tenant_name[]" value=""/><br>' +
                        '<h5>Servicemembers Civil Relief Act Affidavit (link to website)</h5>' +
                        '<input type="radio" class="is_military" id="is_military_' + i + '" name="tenant_military_' + i + '" value="military" />' +
                        '<label class="military_label" for="is_military_' + i + '">I have personal knowledge that the defendant named above is in military service.</label><br> ' +
                        '<input type="radio" checked class="is_not_military" id="is_not_military_' + i + '" name="tenant_military_' + i + '" value="not military" />' +
                        '<label class="military_label" for="is_not_military_' + i + '">I have personal knowledge that the defendant named above is not in the military service.</label><br>' +
                        '<input type="radio" class="unable_determine_military" id="unable_determine_military_' + i + '" name="tenant_military_' + i + '" value="unable determine military" />' +
                        '<label class="military_label" for="unable_determine_military_' + i + '">I am unable to determine whether the defendant named above is in the military service.</label><br>' +
                        '<div class="tenant_explanation_div">The following facts support the above statement (explain how you know the defendant is or is not in military service, or, if unable to make a determination, the steps you took to investigate the defendant\'s military status):</div>' +
                        '<textarea class="form-control tenant_military_explanation" name="tenant_military_explanation_' + i + '" style="height:120px; width:70%;"></textarea><hr/>';
                }
            }

            $('#tenant_input_container').empty().append($(html));
        });

        $('#breached_conditions_lease').on('change', function() {
           if ($(this).is(':checked')) {
               $('#breached_details').prop('disabled', false);
           } else {
               $('#breached_details').prop('disabled', true);
           }
        });

        let totalJudgment = '';
        let deliveryType = '';
        $('#finalize_document').on('click', function() {

            if ($('#owner_name').val() === '' ) {
                alert('Owner Name is a required field.')
            } else if ($('#rented_by_owner')[0].checked) {
                if ($('#owner_address_1').val() === '') {
                    alert('Owner Address Line 1 is a required field.')
                } else if ( $('#owner_address_2').val() === '') {
                    alert('Owner Address Line 2 is a required field.')
                }
            } else if ($('#rented_by_other')[0].checked) {
                if ($('#other_name').val() === '') {
                    alert('Property Management Company Name is required.');
                } else if ($('#pm_name').val() === '') {
                    alert('Property Manager Name is required.');
                } else if ($('#pm_phone').val() === '') {
                    alert('Property Manager Phone Number is required.');
                } else if ($('#pm_address_1').val() === '') {
                    alert('Property Manager Address is required.');
                } else if ($('#pm_address_2').val() === '') {
                    alert('Property Manager Address is required.');
                }
            } else if ($('#security_deposit').val() === '') {
                alert('Security Deposit is required.');
            } else if ($('#monthly_rent').val() === '') {
                alert('Monthly Rent is required.');
            } else if ($('#tenant_num_select').val() === null) {
                alert('You have to select the number of tenants and add their name.');
            } else if ($('#due_rent').val() === '') {
                alert('Rent Due at Filing Date is required.');
            } else if ($('#tenant_num_select').val() != null) {
                $.each($('.tenant_names'), function(key, value) {
                    let name = value.value;
                    let tenantNum = key + 1;
                    if (name === '') {
                        alert('Tenant Name ' + tenantNum + ' cannot be blank.');
                    }
                });
            } else {
                $('#modal_signature').modal('show');


                let userAddress = houseNum + ' ' + streetName + ' ' + town + ' ' + 'PA ' + county + ', ' + zipcode;

                if ($('#file_type').val() === 'civil') {
                    totalJudgment = $('#total_judgment').val();
                    deliveryType = $("input[name=delivery_type]:checked").val()
                }

                $.ajax({
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                    },
                    url: '/new-file/get-court-fee',
                    type: 'GET',
                    data: {
                        'court_number': $('#court_number').val(),
                        'tenant_num_select': $('#tenant_num_select').val(),
                        'fileType': $('#file_type').val(),
                        'additional_rent_amt': $('#additional_rent_amt').val(),
                        'attorney_fees': $('#attorney_fees').val(),
                        'due_rent': $('#due_rent').val(),
                        'unjust_damages': $('#unjust_damages').val(),
                        'damage_amt': $('#damage_amt').val(),
                        'tenant_num': $('#tenant_num').val(),
                        'total_judgment': totalJudgment,
                        'delivery_type': deliveryType,
                        'userAddress': userAddress

                    },
                    success: function (data) {
                        console.log(data);
                        let total = '';

                        if (data['calculatedFee'] !== '') {

                            if ($('#isComplaintFee').val() === 'yes') {

                                if ($('#file_type').val() === 'ltcA') {
                                    $('#courtzip_filing_fee').text(' $225.00');
                                    total = parseFloat(data['filingFee']) + parseFloat(data['calculatedFee']) + 225.00;
                                } else if ($('#file_type').val() === 'oopA') {
                                    $('#courtzip_filing_fee').text(' $275.00');
                                    total = parseFloat(data['filingFee']) + parseFloat(data['calculatedFee']) + 275.00;
                                } else {
                                    $('#courtzip_filing_fee').text(' $25.00');
                                    total = 25.00 + parseFloat(data['filingFee']) + parseFloat(data['calculatedFee'])
                                }
                            } else {
                                $('#courtzip_filing_fee').text(' $25.00');
                                total = 25.00 + parseFloat(data['filingFee']) + parseFloat(data['calculatedFee'])
                            }

                            $('#distance_fee_display').text(data['calculatedFee']);
                            $('#distance_fee').val(data['calculatedFee']);
                            $('#distance_fee_container').css('display', 'initial');
                        } else {

                            if ($('#isComplaintFee').val() === 'yes') {

                                if ($('#file_type').val() === 'ltcA') {
                                    $('#courtzip_filing_fee').text(' $225.00');
                                    total = parseFloat(data['filingFee']) + 225.00;
                                } else if ($('#file_type').val() === 'oopA') {
                                    $('#courtzip_filing_fee').text(' $275.00');
                                    total = parseFloat(data['filingFee']) + 275.00;
                                } else {
                                    $('#courtzip_filing_fee').text(' $25.00');
                                    total = 25.00 + parseFloat(data['filingFee']);
                                }
                            } else {
                                $('#courtzip_filing_fee').text(' $25.00');
                                total = 25.00 + parseFloat(data['filingFee']);
                            }
                            $('#distance_fee_container').css('display', 'none');
                        }

                        $('#filing_fee_display').text(data['filingFee']);
                        $('#total').text(total.toFixed(2));
                        $('#total_input').val(total.toFixed(2));

                    },
                    error: function (data) {
                        console.log(data)
                    },
                });
            }
        });

        $('.file').on('change', function() {
            console.log($(this));
            if ($(this).val() !== '') {
                upload(this);
            }
        });

        function upload(img) {
            let form_data = new FormData();
            form_data.append('file', img.files[0]);
            form_data.append('csrf-token', '{{csrf_token()}}');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                },
                url: '/file-upload',
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    if (data !== '') {
                        $('#is_extra_filing').val(1);
                        $('#file_container').append($('<input type="hidden" name="file_address_ids[]" id="file_address_ids" value="'+data+'"/>'));
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                    console.log(status);
                },
            });
        }
    });
}