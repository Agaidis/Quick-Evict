$(document).ready(function () {

    $('#filing_date').val(new Date());
    $('#landlord').prop('hidden', true);

    var map;
    var marker;
    var magistrate02208;
    var magistrate02102;
    var bounds;
    var houseNum;
    var streetName;
    var town;
    var county;
    var zipcode;
    var state;

    var center = new google.maps.LatLng(40.149660, -76.306370);
    //Create the areas for magistrates
    var magistrate02102Area= [
        {lat: 40.125878 , lng: -76.378521},
        {lat: 40.095704 , lng: -76.362540},
        {lat: 40.097141 , lng: -76.277911},
        {lat: 40.119835 , lng: -76.279963},
        {lat: 40.125878 , lng: -76.378521},
    ];

    var magistrate02208Area= [
        {lat: 40.224045 , lng: -76.299618},
        {lat: 40.205594 , lng: -76.397723},
        {lat: 40.168318 , lng: -76.375707},
        {lat: 40.126021 , lng: -76.3785013},
        {lat: 40.119901 , lng: -76.280041},
        {lat: 40.160385 , lng: -76.228435},
        {lat: 40.208843 , lng: -76.228908},
        {lat: 40.224045 , lng: -76.299618}
    ];

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.144128, lng: -76.311420},
            zoom: 9,
            scaleControl: true
        });
        bounds = new google.maps.LatLngBounds();
        google.maps.event.addListenerOnce(map, 'tilesloaded', function(evt) {

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

        //Create the polygons
        magistrate02102 = new google.maps.Polygon({
            path: magistrate02102Area,
            geodesic: true,
            strokeColor: '#A7A4A3',
            strokeOpacity: 1.0,
            strokeWeight: 2,
            fillColor: '#B1AAA9',
            fillOpacity: 0.35
        });

        magistrate02208 = new google.maps.Polygon({
            path: magistrate02208Area,
            geodesic: true,
            strokeColor: '#A7A4A3',
            strokeOpacity: 1.0,
            strokeWeight: 2,
            fillColor: '#B1AAA9',
            fillOpacity: 0.35
        });

        magistrate02102.setMap(map);
        magistrate02208.setMap(map);


        autocomplete.addListener('place_changed', function() {
            marker.setMap(null);
            var place = autocomplete.getPlace();
            newBounds = bounds;
            if (!place.geometry) {
                window.alert("Returned place contains no geometry");
                return;
            };

            houseNum =  place.address_components[0].long_name;
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
            if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02208)){
                $('#court_number').val('02-2-08');
                $('#court_phone_number').val('717-626-0258');
                $('#court_address1').val('690 Furnace Hills Pike');
                $('#court_address2').val('Lititz, PA 17543');

            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02102)){
                $('#court_number').val('02-1-02');
                $('#court_phone_number').val('717-569-8774');
                $('#court_address1').val('2205 Oregon Pike');
                $('#court_address2').val('Lancaster, PA 17601');

            } else {
                alert('The address is outside of all areas.');
            }
        });





    $('input[type=radio][name=rented_by]').change(function(){
       console.log($(this)[0].id);
       if ($(this)[0].id == 'rented_by_other') {
           $('#landlord').prop('hidden', false);
       } else {
           $('#landlord').prop('hidden', true);
       }
    });

    //On Submit gather variables and make ajax call to backend
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#pdf_download_btn').on('click', function() {
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
                location.reload();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
});