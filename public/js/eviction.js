$(document).ready(function () {
    $("#wizard").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",  
        autoFocus: true
    });
    

    map = new google.maps.Map(document.getElementById('map'), {
  center: {lat: 40.144128, lng: -76.311420},
  zoom: 14
});
});