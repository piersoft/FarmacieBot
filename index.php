<? php
$lat=$_GET['lat'];
$lon=$_GET['lon'];
$r=$_GET['r'];
 ?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>Farmacie Min.Salute</title>
<link rel="shortcut icon" href="faviconp.ico" />
<link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.mapbox.com/mapbox.js/v2.2.2/mapbox.js'></script>
<link href='https://api.mapbox.com/mapbox.js/v2.2.2/mapbox.css' rel='stylesheet' />
<meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=1.0, user-scalable=no">
<meta property="og:image" content="http://www.piersoft.it/FarmacieBot/farmacielogo.jpg"/>

</head>
<body onload="getLocation()">
<div id='loader'><span class='message'>Sto cercando la tua posizione..</span></div>

<!--
  This example requires jQuery to load the file with AJAX.
  You can use another tool for AJAX.

  This pulls the file airports.csv, converts into into GeoJSON by autodetecting
  the latitude and longitude columns, and adds it to the map.

  Another CSV that you use will also need to contain latitude and longitude
  columns, and they must be similarly named.
-->

<script src='https://code.jquery.com/jquery-1.11.0.min.js'></script>
<script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-pip/v0.0.2/leaflet-pip.js'></script>
<style>
#loader {
    position:absolute; top:0; bottom:0; width:100%;
    background:rgba(255, 255, 255, 1);
    transition:background 1s ease-out;
    -webkit-transition:background 1s ease-out;
}
#loader.done {
    background:rgba(255, 255, 255, 0);
}
#loader.hide {
    display:none;
}
#loader .message {
    position:absolute;
    left:30%;
    top:50%;
    font-family: Titillium Web, Arial, Sans-Serif;
    font-size: 15px;
}
</style>
<script>
var latphp="";
var lonphp="";
var r="";

function getLocation() {

    if (navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
alert('Abilita la localizzazione GPS su tuo smartphone, per cortesia :) ')

    }
}
function showPosition(position) {
  //  x.innerHTML = "Latitude: " + position.coords.latitude +
  //  "<br>Longitude: " + position.coords.longitude;
  latphp = parseFloat('<?php printf($_GET['lat']); ?>');
  lonphp = parseFloat('<?php printf($_GET['lon']); ?>');
  r = parseFloat('<?php printf($_GET['r']); ?>');
  if (!latphp || 0 === latphp.length){
    latphp=position.coords.latitude;
    lonphp=position.coords.longitude;
    r=2;
  }else{

  }

console.log(latphp+" "+lonphp);
  window.location.href = "http://www.piersoft.it/FarmacieBot/mappa/locator.php?lat="+latphp+"&lon="+lonphp+"&r="+r;
}
</script>
function startLoading() {
    loader.className = '';
}

function finishedLoading() {
    // first, toggle the class 'done', which makes the loading screen
    // fade out
    loader.className = 'done';
    setTimeout(function() {
        // then, after a half-second, add the class 'hide', which hides
        // it completely and ensures that the user can interact with the
        // map again.
        loader.className = 'hide';
    }, 500);
}
</body>
</html>
