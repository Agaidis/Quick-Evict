if (document.location.href.split('/')[3] == 'online-eviction') {
    $(document).ready(function () {

        $('#signArea').signaturePad({drawOnly:true, drawBezierCurves:true, lineTop:90});

        $("#btnSaveSign").click(function(e) {
            html2canvas([document.getElementById('sign-pad')], {
                onrendered: function (canvas) {
                    var canvas_img_data = canvas.toDataURL('image/png');
                    console.log(canvas_img_data);
                    var img_data = canvas_img_data.replace(/^data:image\/(png|jpg);base64,/, "");
                    //ajax call to save image inside folder
                    $.ajax({
                        url: 'online-eviction/saveSignature',
                        data: {img_data: img_data},
                        type: 'post',
                        dataType: 'json',
                        success: function (response) {
                            window.location.reload();
                        }
                    });
                }
            });
        });

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
        var magNamesArray = [];
        var count = 0;

        $.each(quickEvict.geoData, function(key, value) {
            magId = 'magistrate_' + value.magistrate_id;
            var geoPoints = value.geo_locations.replace(/\s/g, '').replace(/},/g, '},dd').split(',dd');
            var obj = [];

            for (var i in geoPoints) {
                obj.push(JSON.parse(geoPoints[i]));
            }
            magNamesArray.push(magId);
            objArray.push(obj);
            magArray.push(magId);
            magArray[count] = new google.maps.Polygon({
                path: obj,
                geodesic: true,
                strokeColor: '#091096',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                fillColor: '#B1AAA9',
                fillOpacity: 0.35,
                areaName: magId
            });
            magArray[count].setMap(map);
            count++;
        });
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
            $('#display_address').text(houseNum + ' ' + streetName + ' ' + town + ' ' + state);

            marker.setPosition(place.geometry.location);
            marker.setMap(map);
            newBounds.extend(place.geometry.location);
            map.fitBounds(newBounds);
            var isFound = false;
            for (var k = 0; k < magArray.length; k++) {
                if (google.maps.geometry.poly.containsLocation(place.geometry.location, magArray[k])) {
                    $('#court_number').val(magArray[k].areaName);
                    isFound = true;
                }
            }
            if (isFound == false) {
                alert('Location outside all Zones');
                $('.unit_number_div').css('display', 'none');
                $('.eviction_form_div').css('display', 'none');
            } else {
                $('.unit_number_div').css('display', 'block');
                $('.eviction_form_div').css('display', 'block');
            }
        });


        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $('input[type=radio][name=rented_by]').change(function () {

            if ($(this)[0].id == 'rented_by_other') {
                $('#landlord').prop('hidden', false);
                $('#rented_by_other_div').css('display', 'block');
            } else {
                $('#landlord').prop('hidden', true);
                $('#rented_by_other_div').css('display', 'none');
            }
        });


        //On Submit gather variables and make ajax call to backend

        $('#pdf_download_btn').on('click', function () {
            $('#rented_by_val').val($('input[name=rented_by]:checked').val());
            $('.eviction_fields').text('');
            // var data = $('#eviction_form').serialize();
            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     }
            // });
            //
            // $.ajax({
            //     beforeSend: function (xhr) {
            //         xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            //     },
            //     type: "POST",
            //     url: '/online-eviction/pdf-data',
            //     dataType: 'json',
            //     data: data,
            //
            //     success: function (data) {
            //         console.log(data);
            //         //location.reload();
            //     },
            //     error: function (data) {
            //         console.log(data);
            //     }
            // });
        });


    });
}