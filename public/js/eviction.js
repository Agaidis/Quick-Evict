if (document.location.href.split('/')[3] == 'online-eviction') {
    $(document).ready(function () {

        $('#filing_date').val(new Date());
        $('#landlord').prop('hidden', true);

        var map;
        var marker;
        var bounds;
        var houseNum;
        var streetName;
        var town;
        var county;
        var zipcode;
        var state;

        var center = new google.maps.LatLng(40.149660, -76.306370);
        //Create the areas for magistrates

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.144128, lng: -76.311420},
            zoom: 9,
            scaleControl: true
        });
        bounds = new google.maps.LatLngBounds();
        google.maps.event.addListenerOnce(map, 'tilesloaded', function (evt) {

            bounds = map.getBounds();
        });
        marker = new google.maps.Marker({
            position: center
        });
        var input = /** @type {!HTMLInputElement} */(
            document.getElementById('pac-input'));
        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        var magArray = [];
        var objArray = [];

        $.each(quickEvict.geoData, function(key, value) {
            magId = 'magistrate_' + value.magistrate_id;
            var geoPoints = value.geo_locations.replace(/\s/g, '').replace(/},/g,'},dd').split(',dd');
            var obj = [];

            for (var i in geoPoints) {
                obj.push(JSON.parse(geoPoints[i]));
            }
            objArray.push(obj);
            magArray.push(magId);
        });

        //Create the polygons
        for (var k = 0; k < objArray.length; k++) {
            magArray[k] = new google.maps.Polygon({
                path: objArray[k],
                geodesic: true,
                strokeColor: '#D4CEFA',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                fillColor: '#B1AAA9',
                fillOpacity: 0.35
            });

            magArray[k].setMap(map);
        }

        autocomplete.addListener('place_changed', function () {
            marker.setMap(null);
            var place = autocomplete.getPlace();
            newBounds = bounds;
            if (!place.geometry) {
                window.alert("Returned place contains no geometry");
                return;
            }

            houseNum = place.address_components[0].long_name;
            streetName = place.address_components[1].long_name;
            town = place.address_components[2].long_name;
            county = place.address_components[3].long_name;
            state = place.address_components[4].short_name;
            zipcode = place.address_components[6].long_name;

            $('#state').val(state);
            $('#zipcode').val(zipcode);
            $('#county').val(county);
            $('#house_num').val(houseNum);
            $('#street_name').val(streetName);
            $('#town').val(town);

            marker.setPosition(place.geometry.location);
            marker.setMap(map);
            newBounds.extend(place.geometry.location);
            map.fitBounds(newBounds);
            var isFound = false;
            for (var k = 0; k < 0; k++) {
                if (google.maps.geometry.poly.containsLocation(place.geometry.location, magArray[k])) {
                    $('#court_number').val(magArray[k]);
                    isFound = true;
                }
            }
            if (isFound == false) {
                alert('Location outside all Zones');
            }
        });


        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('input[type=radio][name=rented_by]').change(function () {
            console.log($(this)[0].id);
            if ($(this)[0].id == 'rented_by_other') {
                $('#landlord').prop('hidden', false);
            } else {
                $('#landlord').prop('hidden', true);
            }
        });

        //On Submit gather variables and make ajax call to backend

        $('#pdf_download_btn').on('click', function () {
            $('#rented_by_val').val($('input[name=rented_by]:checked').val());
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
                url: '/online-eviction/pdf-data',
                dataType: 'json',
                data: data,

                success: function (data) {
                    console.log(data);
                    //location.reload();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });


    });
}