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

    var magistrate02101Area = [
        {lng: -76.3059084, lat: 40.0222321},
        {lng: -76.3048784, lat: 40.022725},
        {lng: -76.3034622, lat: 40.026077},
        {lng: -76.3053934, lat: 40.0367073},
        {lng: -76.3055007, lat: 40.0371016},
        {lng: -76.305608, lat: 40.0374466},
        {lng: -76.3056643, lat: 40.0376971},
        {lng: -76.305785, lat: 40.0378984},
        {lng: -76.3086657, lat: 40.0376273},
        {lng: -76.3098352, lat: 40.0375123},
        {lng: -76.3121311, lat: 40.037348},
        {lng: -76.3152478, lat: 40.0369579},
        {lng: -76.3166479, lat: 40.0373563},
        {lng: -76.3185469, lat: 40.0378246},
        {lng: -76.3236325, lat: 40.038268},
        {lng: -76.3243621, lat: 40.0352943},
        {lng: -76.3245874, lat: 40.0344113},
        {lng: -76.324641, lat: 40.0338239},
        {lng: -76.324877, lat: 40.0321235},
        {lng: -76.3245337, lat: 40.0297904},
        {lng: -76.3231175, lat: 40.0296918},
        {lng: -76.3220017, lat: 40.0273916},
        {lng: -76.3250057, lat: 40.0247625},
        {lng: -76.3234608, lat: 40.0235795},
        {lng: -76.3243191, lat: 40.0226593},
        {lng: -76.32286, lat: 40.0215419},
        {lng: -76.3238041, lat: 40.0207531},
        {lng: -76.3214009, lat: 40.0187155},
        {lng: -76.3197701, lat: 40.0205559},
        {lng: -76.3152211, lat: 40.0177952},
        {lng: -76.3059084, lat: 40.0222321}
    ];

    var magistrate02202Area = [
        {lng: -76.3083544, lat: 40.0521251},
        {lng: -76.3117631, lat: 40.0518413},
        {lng: -76.3129525, lat: 40.0476257},
        {lng: -76.3129249, lat: 40.0467988},
        {lng: -76.3142276, lat: 40.0463662},
        {lng: -76.3137429, lat: 40.0436274},
        {lng: -76.3169495, lat: 40.0433198},
        {lng: -76.316754, lat: 40.0417191},
        {lng: -76.3204824, lat: 40.041409},
        {lng: -76.3248491, lat: 40.0423002},
        {lng: -76.325171, lat: 40.0413885},
        {lng: -76.325729, lat: 40.0400252},
        {lng: -76.3263545, lat: 40.0385388},
        {lng: -76.3236325, lat: 40.038268},
        {lng: -76.3185469, lat: 40.0378246},
        {lng: -76.3152478, lat: 40.0369579},
        {lng: -76.305785, lat: 40.0378984},
        {lng: -76.3083544, lat: 40.0521251}
    ];

    var magistrate02203Area = [
        {lng: -76.3019491, lat: 40.0095648},
        {lng: -76.305492, lat: 40.017292},
        {lng: -76.304199, lat: 40.021578},
        {lng: -76.302419, lat: 40.020996},
        {lng: -76.298984, lat: 40.016568},
        {lng: -76.294647, lat: 40.016477},
        {lng: -76.2938219, lat: 40.0184787},
        {lng: -76.3034622, lat: 40.026077},
        {lng: -76.3048784, lat: 40.022725},
        {lng: -76.3152211, lat: 40.0177952},
        {lng: -76.3197701, lat: 40.0205559},
        {lng: -76.3214009, lat: 40.0187155},
        {lng: -76.3238041, lat: 40.0207531},
        {lng: -76.32286, lat: 40.0215419},
        {lng: -76.3243191, lat: 40.0226593},
        {lng: -76.3234608, lat: 40.0235795},
        {lng: -76.3250057, lat: 40.0247625},
        {lng: -76.3220017, lat: 40.0273916},
        {lng: -76.3231175, lat: 40.0296918},
        {lng: -76.3245337, lat: 40.0297904},
        {lng: -76.324877, lat: 40.0321235},
        {lng: -76.3236325, lat: 40.038268},
        {lng: -76.3263545, lat: 40.0385388},
        {lng: -76.3248491, lat: 40.0423002},
        {lng: -76.3204824, lat: 40.041409},
        {lng: -76.316754, lat: 40.0417191},
        {lng: -76.3169495, lat: 40.0433198},
        {lng: -76.3137429, lat: 40.0436274},
        {lng: -76.3142276, lat: 40.0463662},
        {lng: -76.3129249, lat: 40.0467988},
        {lng: -76.3117631, lat: 40.0518413},
        {lng: -76.313546, lat: 40.052132},
        {lng: -76.3134708, lat: 40.0554},
        {lng: -76.3127035, lat: 40.0595114},
        {lng: -76.3135239, lat: 40.061898},
        {lng: -76.314541, lat: 40.0644236},
        {lng: -76.3156654, lat: 40.0642066},
        {lng: -76.3165625, lat: 40.0659241},
        {lng: -76.315374, lat: 40.0665673},
        {lng: -76.3160655, lat: 40.0682149},
        {lng: -76.3180912, lat: 40.0701758},
        {lng: -76.3183732, lat: 40.0710068},
        {lng: -76.3237222, lat: 40.0695628},
        {lng: -76.3252342, lat: 40.0730018},
        {lng: -76.3269463, lat: 40.0719858},
        {lng: -76.3256273, lat: 40.0688565},
        {lng: -76.3285922, lat: 40.0672652},
        {lng: -76.3285283, lat: 40.0664118},
        {lng: -76.331011, lat: 40.0659103},
        {lng: -76.3319552, lat: 40.0656598},
        {lng: -76.3330882, lat: 40.0667738},
        {lng: -76.3345113, lat: 40.0683198},
        {lng: -76.3355212, lat: 40.0686858},
        {lng: -76.3364902, lat: 40.0696118},
        {lng: -76.3387472, lat: 40.0713598},
        {lng: -76.3408542, lat: 40.0726698},
        {lng: -76.3417452, lat: 40.0725688},
        {lng: -76.3421323, lat: 40.0707478},
        {lng: -76.3435922, lat: 40.0671098},
        {lng: -76.345266, lat: 40.067275},
        {lng: -76.3460082, lat: 40.0656248},
        {lng: -76.3438312, lat: 40.0653668},
        {lng: -76.3422122, lat: 40.0649708},
        {lng: -76.3417835, lat: 40.064802},
        {lng: -76.3381675, lat: 40.0627241},
        {lng: -76.3368576, lat: 40.0617215},
        {lng: -76.3335082, lat: 40.0583696},
        {lng: -76.3302952, lat: 40.0565618},
        {lng: -76.3299952, lat: 40.0563828},
        {lng: -76.3291363, lat: 40.0571868},
        {lng: -76.3266593, lat: 40.0571158},
        {lng: -76.3231445, lat: 40.0561211},
        {lng: -76.3234538, lat: 40.0536337},
        {lng: -76.3237132, lat: 40.0531598},
        {lng: -76.3222242, lat: 40.0536178},
        {lng: -76.3212343, lat: 40.0547738},
        {lng: -76.3197672, lat: 40.0535858},
        {lng: -76.3209653, lat: 40.0528568},
        {lng: -76.3206933, lat: 40.0521928},
        {lng: -76.3212842, lat: 40.0516768},
        {lng: -76.3200882, lat: 40.0511218},
        {lng: -76.3213932, lat: 40.0513628},
        {lng: -76.3214903, lat: 40.0511591},
        {lng: -76.3278699, lat: 40.0507391},
        {lng: -76.3404022, lat: 40.0529178},
        {lng: -76.3435838, lat: 40.0477715},
        {lng: -76.34432, lat: 40.046151},
        {lng: -76.34532, lat: 40.044512},
        {lng: -76.344989, lat: 40.043331},
        {lng: -76.3427462, lat: 40.0415369},
        {lng: -76.342611, lat: 40.039779},
        {lng: -76.342744, lat: 40.038608},
        {lng: -76.343064, lat: 40.03769},
        {lng: -76.3421873, lat: 40.0314757},
        {lng: -76.343977, lat: 40.029055},
        {lng: -76.343308, lat: 40.027065},
        {lng: -76.3527823, lat: 40.0230075},
        {lng: -76.3488615, lat: 40.0199153},
        {lng: -76.3453483, lat: 40.0176772},
        {lng: -76.3434718, lat: 40.0186239},
        {lng: -76.3412635, lat: 40.0194327},
        {lng: -76.339851, lat: 40.0185523},
        {lng: -76.3391719, lat: 40.0186976},
        {lng: -76.3381141, lat: 40.0175422},
        {lng: -76.3344541, lat: 40.0131932},
        {lng: -76.3363612, lat: 40.0119891},
        {lng: -76.3401753, lat: 40.0100414},
        {lng: -76.342997, lat: 40.0088415},
        {lng: -76.3443059, lat: 40.0082497},
        {lng: -76.3467519, lat: 40.0071319},
        {lng: -76.3423753, lat: 39.9987825},
        {lng: -76.3283469, lat: 39.987683},
        {lng: -76.3277078, lat: 39.9995989},
        {lng: -76.3317515, lat: 40.0088327},
        {lng: -76.331084, lat: 40.0108638},
        {lng: -76.329472, lat: 40.0119088},
        {lng: -76.3270018, lat: 40.0104557},
        {lng: -76.3251887, lat: 40.0039844},
        {lng: -76.3217342, lat: 39.999456},
        {lng: -76.320661, lat: 40.0056535},
        {lng: -76.31397, lat: 40.005021},
        {lng: -76.310373, lat: 39.996343},
        {lng: -76.3078714, lat: 39.9960866},
        {lng: -76.3019491, lat: 40.0095648}
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
    magistrate02101 = new google.maps.Polygon({
        path: magistrate02101Area,
        geodesic: true,
        strokeColor: '#02314E',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02202 = new google.maps.Polygon({
        path: magistrate02202Area,
        geodesic: true,
        strokeColor: '#CCD839',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02303 = new google.maps.Polygon({
        path: magistrate02203Area,
        geodesic: true,
        strokeColor: '#FF7243',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02204 = new google.maps.Polygon({
        path: magistrate02204Area,
        geodesic: true,
        strokeColor: '#81CFFF',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });

    magistrate02101.setMap(map);
    magistrate02202.setMap(map);
    magistrate02303.setMap(map);
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
            if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02101)){
                $('#court_number').val('02-1-01');
                $('#court_address1').val('641 Union Street');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02202)){
                $('#court_number').val('02-2-02');
                $('#court_address1').val('150 N. Queen Street Suite 120');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02303)){
                $('#court_number').val('02-2-03');
                $('#court_address1').val('1351 Elm Ave');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02303)){
                $('#court_number').val('02-2-03');
                $('#court_address1').val('1351 Elm Ave');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02204)){
                $('#court_number').val('02-2-04');
                $('#court_address1').val('796-A New Holland Ave');
                $('#court_address2').val('Lancaster, PA 17602');
            } else {
                alert('The address is outside of all areas.');
            }
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
                console.log(data);
                //location.reload();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });


});