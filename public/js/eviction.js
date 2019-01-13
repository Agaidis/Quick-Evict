if (document.location.href.split('/')[3] == 'online-eviction') {
    $(document).ready(function () {

        var canvas = document.querySelector("canvas");
        var signaturePad = new SignaturePad(canvas, {});

        //Clear button to remove signature drawing
        $('.clear_signature').on('click', function() {
           // $('#pdf_download_btn').prop('disabled', true);
            // Clears the canvas
            signaturePad.clear();
        });

        //Save and use Signature
        $('.save_signature').on('click', function() {
         //   $('#pdf_download_btn').prop('disabled', false);
            var dataURL = signaturePad.toDataURL(); // save image as PNG
            $('#signature_source').val(dataURL);
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

            console.log(magId);

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
                areaName: magId,
                courtId: value.court_number,
                county: value.county,
                township: value.township
            });
            magArray[count].setMap(map);


            google.maps.event.addListener(magArray[count], 'mouseover', function (e) {
                var magistrateId = $(this)[0].areaName.split('magistrate_');
                injectTooltip(e, magistrateId[1] + '<br>' + $(this)[0].county + '<br>' + $(this)[0].township);
            });

            google.maps.event.addListener(magArray[count], 'mousemove', function (e) {
                moveTooltip(e);
            });

            google.maps.event.addListener(magArray[count], 'mouseout', function (e) {
                deleteTooltip(e);
            });

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

            console.log(place.address_components);

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
                $('.zipcode_div').css('display', 'none');
                $('.unit_number_div').css('display', 'none');
                $('.eviction_form_div').css('display', 'none');
            } else {
                $('.zipcode_div').css('display', 'block');
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





        //create a global variable that will point to the tooltip in the DOM
        var tipObj = null;

//offset along x and y in px
        var offset = {
            x: 6,
            y: -300
        };

        var coordPropName = null;

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
                    for(var i in eventPropNames){
                        var name = eventPropNames[i];
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

        //On Submit
        $('#pdf_download_btn').on('click', function () {
            $('#rented_by_val').val($('input[name=rented_by]:checked').val());
        });
    });
}