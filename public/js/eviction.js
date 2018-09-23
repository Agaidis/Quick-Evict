$(document).ready(function () {
    // $("#wizard").steps({
    //     headerTag: "h3",
    //     bodyTag: "section",
    //     transitionEffect: "slideLeft",
    //     autoFocus: true
    // });
    $('#filing_date').val(new Date());
    $('#landlord').prop('hidden', true);





    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);




    var map;
    var marker;
    var polygon;
    var bounds;
    window.onload = initMap;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.144128, lng: -76.311420},
            zoom: 7
        });
        bounds = new google.maps.LatLngBounds();
        google.maps.event.addListenerOnce(map, 'tilesloaded', function(evt) {
            bounds = map.getBounds();
        });
        marker = new google.maps.Marker({
            position: center
        });
        polygon = new google.maps.Polygon({
            path: area,
            geodesic: true,
            strokeColor: '#FFd000',
            strokeOpacity: 1.0,
            strokeWeight: 4,
            fillColor: '#FFd000',
            fillOpacity: 0.35
        });

        polygon.setMap(map);

      //  var input = /** @type {!HTMLInputElement} */(
      //      document.getElementById('pac-input'));
       // var types = document.getElementById('type-selector');
    //    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    //    map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener('place_changed', function() {
            marker.setMap(null);
            var place = autocomplete.getPlace();
            var newBounds = new google.maps.LatLngBounds();
            newBounds = bounds;
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            };
            marker.setPosition(place.geometry.location);
            marker.setMap(map);
            newBounds.extend(place.geometry.location);
            map.fitBounds(newBounds);
            if (google.maps.geometry.poly.containsLocation(place.geometry.location, polygon)){
                alert('The area contains the address');
            } else {
                alert('The address is outside of the area.');
            };
        });
    }

    var center = new google.maps.LatLng(40.149660, -76.306370);
    var area= [
        {lat: 40.224045 , lng: -76.299618},
        {lat: 40.205594 , lng: -76.397723},
        {lat: 40.168318 , lng: -76.375707},
        {lat: 40.126021 , lng: -76.3785013},
        {lat: 40.119901 , lng: -76.280041},
        {lat: 40.160385 , lng: -76.228435},
        {lat: 40.208843 , lng: -76.228908},
        {lat: 40.224045 , lng: -76.299618},
    ];
























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