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
                $('.payment_section').css('display', 'initial');
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

            if (place.address_components[6].short_name === 'US') {
                zipcode = place.address_components[7].long_name;
            } else {
                zipcode = place.address_components[6].long_name;
            }

            $('#state').val('PA');
            $('#zipcode').val(zipcode);
            $('#county').val(county);
            $('#house_num').val(houseNum);
            $('#street_name').val(streetName);
            $('#town').val(town);
            $('#display_address').text(houseNum + ' ' + streetName + ' ' + town + ' ' + 'PA');

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
                         let validEmails = ['brc@saxtonstump.com', 'tiffanymitchell0202@gmail.com', 'sparkleclean85@gmail.com', 'andrew.gaidis@gmail.com'];

                        if (data[0].online_submission !== 'of' && ((quickEvict.userEmail.indexOf('slatehousegroup') === -1) && validEmails.includes(quickEvict.userEmail) === false)) {
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
                    html += '<label class="labels" for="tenant_name_'+ i +'" >Name '+ i +'</label><input class="form-control eviction_fields" placeholder="Name '+ i +'" type="text" id="tenant_name_'+ i +'" name="tenant_name[]" value="' + currentTenantObj.val() + '"/><br>';
                } else {
                    html += '<label class="labels" for="tenant_name_'+ i +'" >Name '+ i +'</label><input class="form-control eviction_fields" placeholder="Name '+ i +'" type="text" id="tenant_name_'+ i +'" name="tenant_name[]" value=""/><br>';
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

            if ($('#file_type').val() === 'civil') {
                totalJudgment = $('#total_judgment').val();
                deliveryType = $("input[name=delivery_type]:checked").val()
            }

            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                },
                url : '/new-file/get-court-fee',
                type : 'GET',
                data : {
                    'court_number' : $('#court_number').val(),
                    'tenant_num_select': $('#tenant_num_select').val(),
                    'fileType': $('#file_type').val(),
                    'additional_rent_amt': $('#additional_rent_amt').val(),
                    'attorney_fees': $('#attorney_fees').val(),
                    'due_rent': $('#due_rent').val(),
                    'unjust_damages': $('#unjust_damages').val(),
                    'damage_amt': $('#damage_amt').val(),
                    'tenant_num': $('#tenant_num').val(),
                    'total_judgment': totalJudgment,
                    'delivery_type': deliveryType

                },
                success : function(data) {
                    $('#filing_fee_display').text(data);
                    let total = 16.99 + parseFloat(data);
                    $('#total').text(total.toFixed(2));

                },
                error : function(data)
                {},
            });
        });
    });
}