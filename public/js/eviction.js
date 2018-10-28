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
        var magistrate023046Area = [
            {lng: -76.123703, lat: 39.730795},
            {lng: -76.124028, lat: 39.731334},
            {lng: -76.124073, lat: 39.731411},
            {lng: -76.124107, lat: 39.731497},
            {lng: -76.124145, lat: 39.73158}
            ];

        console.log(magistrate023046Area);

        var test = quickEvict.geoData['geo_locations'].replace(/\s/g, '');
        console.log(test);
        test = JSON.parse(test);


        //Create the polygons
         Magistratetest = new google.maps.Polygon({
             path: test,
             geodesic: true,
             strokeColor: 'black',
             strokeOpacity: 1.0,
             strokeWeight: 2,
             fillColor: '#B1AAA9',
             fillOpacity: 0.35
         });
         Magistratetest.setMap(map);





        autocomplete.addListener('place_changed', function () {
            marker.setMap(null);
            var place = autocomplete.getPlace();
            newBounds = bounds;
            if (!place.geometry) {
                window.alert("Returned place contains no geometry");
                return;
            }
            ;

            console.log(place.address_components);

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

            if (google.maps.geometry.poly.containsLocation(place.geometry.location, Magistratetest)) {
                $('#court_number').val('02-1-01');
                $('#court_address1').val('641 Union Street');
                $('#court_address2').val('Lancaster, PA 17603');
            } else {
                alert('The address is outside of all areas.');
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