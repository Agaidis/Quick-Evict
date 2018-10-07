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

    var magistrate02204Area = [
        {lng: -76.305785, lat: 40.0378984},
        {lng: -76.2871603, lat: 40.0400057},
        {lng: -76.2866655, lat: 40.0434372},
        {lng: -76.2835956, lat: 40.044963},
        {lng: -76.2836357, lat: 40.0440066},
        {lng: -76.2776218, lat: 40.0443276},
        {lng: -76.2829317, lat: 40.0468753},
        {lng: -76.2841103, lat: 40.0477656},
        {lng: -76.2858667, lat: 40.0513203},
        {lng: -76.2642416, lat: 40.043185},
        {lng: -76.2640748, lat: 40.0422769},
        {lng: -76.2630546, lat: 40.0413807},
        {lng: -76.2628168, lat: 40.039654},
        {lng: -76.2590369, lat: 40.0403073},
        {lng: -76.2626772, lat: 40.0458849},
        {lng: -76.2553237, lat: 40.0468875},
        {lng: -76.256066, lat: 40.0487285},
        {lng: -76.2560482, lat: 40.0501436},
        {lng: -76.2554976, lat: 40.0512655},
        {lng: -76.2546108, lat: 40.0522608},
        {lng: -76.2597464, lat: 40.0544491},
        {lng: -76.2626363, lat: 40.0550154},
        {lng: -76.26906, lat: 40.0546043},
        {lng: -76.2722087, lat: 40.053585},
        {lng: -76.2718519, lat: 40.0528701},
        {lng: -76.2757866, lat: 40.051301},
        {lng: -76.2762317, lat: 40.0526633},
        {lng: -76.2787418, lat: 40.0523639},
        {lng: -76.2786121, lat: 40.0559041},
        {lng: -76.2798979, lat: 40.0572689},
        {lng: -76.2808386, lat: 40.0578305},
        {lng: -76.2821623, lat: 40.0569828},
        {lng: -76.2861401, lat: 40.0543016},
        {lng: -76.2892032, lat: 40.0520924},
        {lng: -76.2886348, lat: 40.0541125},
        {lng: -76.289941, lat: 40.0548519},
        {lng: -76.2906248, lat: 40.0540145},
        {lng: -76.295769, lat: 40.0540807},
        {lng: -76.2967448, lat: 40.0548043},
        {lng: -76.2982241, lat: 40.0534921},
        {lng: -76.301569, lat: 40.0539226},
        {lng: -76.2971441, lat: 40.0590539},
        {lng: -76.301126, lat: 40.0599547},
        {lng: -76.3049697, lat: 40.0562707},
        {lng: -76.3051898, lat: 40.0558661},
        {lng: -76.3021535, lat: 40.0527907},
        {lng: -76.3083544, lat: 40.0521251},
        {lng: -76.305785, lat: 40.0378984}
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

        magistrate02204 = new google.maps.Polygon({
            path: magistrate02204Area,
            geodesic: true,
            strokeColor: '#A7A4A3',
            strokeOpacity: 1.0,
            strokeWeight: 2,
            fillColor: '#B1AAA9',
            fillOpacity: 0.35
        });

        magistrate02102.setMap(map);
        magistrate02208.setMap(map);
        magistrate02204.setMap(map);


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
        });



    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
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