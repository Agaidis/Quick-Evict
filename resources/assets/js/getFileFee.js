if (document.location.href.split('/')[3] === 'get-file-fee') {
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

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
            } else {

            }
        });

        $('#file_type_select').on('change', function() {
           if ($(this).val() === 'civil') {
               $('.send_method_container').css('display', 'block');
           } else {
               $('.send_method_container').css('display', 'none');
           }
        });
        $('#calculate_file_fee').on('click', function() {
            let splitCourtNumber = $('#court_number').val().split('_');
            let splitCourtNumberDisplay = splitCourtNumber[1].split('-');

            $('#court_number_display').text(splitCourtNumberDisplay[0] + '-' + splitCourtNumberDisplay[1] + '-' + splitCourtNumberDisplay[2]).val(splitCourtNumberDisplay[0] + '-' + splitCourtNumberDisplay[1] + '-' + splitCourtNumberDisplay[2]);
            let userAddress = houseNum + ' ' + streetName + ' ' + town + ' ' + state + ' ' + county + ', ' + zipcode;
            let fileType = $('#file_type_select').val();
            let totalJudgment = $('#total_judgment').val();
            let numDefendants = $('#num_defendants').val();

            $('.error_msgs').text('');

            if ( streetName === undefined ) {
                alert('Don\'t forget to enter an address of the location you are filing for in the map.');
            }

            if ( fileType === 'none' ) {
                $('#file_type_error_msg').text('Select a File Type.');
            }

            if ( totalJudgment === '' ) {
                $('#total_judgment_error_msg').text('Fill in a total judgment');
            }

            if ( numDefendants === 'none' || numDefendants === null ) {
                $('#num_def_error_msg').text('Select a number of Defendants');
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
                },
                url : '/get-file-fee/fee',
                type : 'POST',
                data : {
                    courtNumber : splitCourtNumber[1],
                    numDefs: numDefendants,
                    fileType: fileType,
                    totalJudgment: totalJudgment,
                    userAddress: userAddress,
                    deliveryType: $('#send_method').val()
                },
                success : function(data) {
                    $('#filing_fee').val(data['filingFee']).text(data['filingFee']);
                    $('#distance').val(data['distance']).text(data['distance']);
                    $('#calculated_fee').val(data['calculatedFee']).text(data['calculatedFee']);
                    console.log(data);
                },
                error : function(data) {
                    console.log(data);
                },
            });
        })
    });
}