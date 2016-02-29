<?php
$lat=$_GET["lat"];
$lon=$_GET["lon"];
$r=$_GET["r"];



$alert="";
$reply="http://nominatim.openstreetmap.org/reverse?email=piersoft2@gmail.com&format=json&lat=".$lat."&lon=".$lon."&zoom=18&addressdetails=1";
$json_string = file_get_contents($reply);
$parsed_json = json_decode($json_string);
//	var_dump($parsed_json);
$comune="";
$temp_c1 =$parsed_json->{'display_name'};

if ($parsed_json->{'address'}->{'town'}) {
  $temp_c1 .="\nCittà: ".$parsed_json->{'address'}->{'town'};
  $comune .=$parsed_json->{'address'}->{'town'};
}else 	$comune .=$parsed_json->{'address'}->{'city'};

if ($parsed_json->{'address'}->{'village'}) $comune .=$parsed_json->{'address'}->{'village'};


//	echo "comune: ".$comune."\n</br>";

  $urlgd="http://opendatasalute.cloudapp.net/DataBrowser/DownloadCsv?container=datacatalog&entitySet=Farmacie&filter=descrizionecomune%20eq%20%27".strtoupper($comune)."%27";

//  $urlgd="/usr/www/piersoft/FarmacieBot/mappa/farmacie.csv";


//echo $url;
//$url ="https://docs.google.com/spreadsheets/d/1x84pu3KF1_II7R8jFGwfzQfY33fOujidBqu_9lGUU_4/pub?gid=0&single=true&output=csv";
$inizio=1;
$homepage ="";
//  echo $url;
$csv = array_map('str_getcsv', file($urlgd));
//$csv=   str_replace(",",".",	$csv);

$latidudine="";
$longitudine="";
$data=0.0;
$data1=0.0;
$count = 0;
$dist=0.0;
  $paline=[];
  $distanza=[];

foreach($csv as $data=>$csv1){
  $count = $count+1;
}

//$count=5;
//var_dump($csv);
//  echo $count;
for ($i=$inizio;$i<$count;$i++){

  $lat1 =substr($csv[$i][19], 0, 2);
  $lat2 =substr($csv[$i][19], 2, 6);
  $lon1 =substr($csv[$i][20], 0, 2);
  $lon2 =substr($csv[$i][20], 2, 6);
//	$data ="Lat".$lat1.".".$lat2;
//	$data ="Lon".$lon1.".".$lon2;
  $latitudine =$lat1.".".$lat2;
  $longitudine =$lon1.".".$lon2;

//$latitudine=$csv[$i][19];
//  $longitudine=$csv[$i][20];
  $homepage .="\n";

  $lat10=floatval($latitudine);
  $long10=floatval($longitudine);
  $theta = floatval($lon)-floatval($long10);
  $dist =floatval( sin(deg2rad($lat)) * sin(deg2rad($lat10)) +  cos(deg2rad($lat)) * cos(deg2rad($lat10)) * cos(deg2rad($theta)));
  $dist = floatval(acos($dist));
  $dist = floatval(rad2deg($dist));
  $miles = floatval($dist * 60 * 1.1515 * 1.609344);


  if ($miles >1 || $miles == 1){
$data1 =number_format($miles, 2, '.', '');
    $data =number_format($miles, 2, '.', '')." Km";
      $t=floatval($r*1);
  } else {
    $data =number_format(($miles*1000), 0, '.', '')." mt";
$data1 =number_format(($miles*1000), 0, '.', '');
  $t=floatval($r*1000);
  }
  $csv[$i][100]= array("distance" => "value");

  $csv[$i][100]= $dat1;
  $csv[$i][101]= array("distancemt" => "value");

  $csv[$i][101]= $data;


//echo "</br>".$data;
      if ($data1 < $t)
      {
        $lat1 =substr($csv[$i][19], 0, 2);
        $lat2 =substr($csv[$i][19], 2, 6);
        $lon1 =substr($csv[$i][20], 0, 2);
        $lon2 =substr($csv[$i][20], 2, 6);
      //	$data ="Lat".$lat1.".".$lat2;
      //	$data ="Lon".$lon1.".".$lon2;
        $latitudine =$lat1.".".$lat2;
        $longitudine =$lon1.".".$lon2;

        $distanza[$i]['distanza'] =$csv[$i][100];
        $distanza[$i]['distanzamt'] =$csv[$i][101];
        $distanza[$i]['id'] =$csv[$i][1];
        $distanza[$i]['lat'] =$latitudine;
        $distanza[$i]['lon'] =$longitudine;
        $distanza[$i]['indirizzo'] =$csv[$i][3];
        $distanza[$i]['nome'] =$csv[$i][4];
        $distanza[$i]['cap'] =$csv[$i][6];
        $distanza[$i]['comune'] =$csv[$i][8];

//echo $distanza[$i]['id'];

      }


}
//var_dump($distanza);

sort($distanza);

$file1 = "mappaf.json";
$original_data="";


$dest1 = fopen($file1, 'w');

//$geostring=geoJson($original_json_string);

$original_data = json_decode($distanza[$tt], true);
if(empty($distanza))
{

  echo "<script type='text/javascript'>alert('Non ci sono farmacie vicino alla tua posizione');</script>";

}
$features = array();

foreach($distanza as $key => $value) {
//  var_dump($value);
    $features[] = array(
            'type' => 'Feature',
            'geometry' => array('type' => 'Point', 'coordinates' => array((float)$value['lon'],(float)$value['lat'])),
            'properties' => array('id' => $value['id'], 'nome' => $value['nome'],'distanza' => $value['distanzamt'],'cap' => $value['cap'],'indirizzo' => $value['indirizzo'],'comune' => $value['comune']),
            );
    };

  $allfeatures = array('type' => 'FeatureCollection', 'features' => $features);

$geostring =json_encode($allfeatures, JSON_PRETTY_PRINT);

//echo $geostring;
fputs($dest1, $geostring);


?>

<!DOCTYPE html>
<html lang="it">
  <head>
  <title>Farmacie Italia Min.Salute</title>
  <link rel="shortcut icon" href="faviconf.ico" />
  <link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="http://necolas.github.io/normalize.css/2.1.3/normalize.css" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
  <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.css" />
        <link rel="stylesheet" href="MarkerCluster.css" />
        <link rel="stylesheet" href="MarkerCluster.Default.css" />
        <meta property="og:image" content="http://www.piersoft.it/FarmacieBot/mappa/farmacielogo.jpg"/>
  <script src="http://cdn.leafletjs.com/leaflet-0.7.5/leaflet.js"></script>
   <script src="leaflet.markercluster.js"></script>
<script type="text/javascript">

function microAjax(B,A){this.bindFunction=function(E,D){return function(){return E.apply(D,[D])}};this.stateChange=function(D){if(this.request.readyState==4 ){this.callbackFunction(this.request.responseText)}};this.getRequest=function(){if(window.ActiveXObject){return new ActiveXObject("Microsoft.XMLHTTP")}else { if(window.XMLHttpRequest){return new XMLHttpRequest()}}return false};this.postBody=(arguments[2]||"");this.callbackFunction=A;this.url=B;this.request=this.getRequest();if(this.request){var C=this.request;C.onreadystatechange=this.bindFunction(this.stateChange,this);if(this.postBody!==""){C.open("POST",B,true);C.setRequestHeader("X-Requested-With","XMLHttpRequest");C.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");C.setRequestHeader("Connection","close")}else{C.open("GET",B,true)}C.send(this.postBody)}};

</script>
  <style>
  #mapdiv{
        position:fixed;
        top:0;
        right:0;
        left:0;
        bottom:0;
}
#infodiv{
background-color: rgba(255, 255, 255, 0.70);

font-family: Titillium Web, Arial, Sans-Serif;
padding: 2px;


font-size: 12px;
bottom: 12px;
left:0px;


max-height: 80px;

position: fixed;

overflow-y: auto;
overflow-x: hidden;
}
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
    left:50%;
    top:50%;
    font-family: Titillium Web, Arial, Sans-Serif;
    font-size: 15px;
}
</style>
  </head>

<body>

  <div data-tap-disabled="true">

  <div id="mapdiv"></div>
<div id="infodiv" style="leaflet-popup-content-wrapper">
  <p><b>Farmacie ubicate nelle vicinanze<br></b>
  Mappa con ubicazione Farmacie, nel raggio di 2km dalla tua posizione. By @piersoft. Fonte dati Lic. IoDL2.0 <a href="http://www.dati.salute.gov.it/dati/dettaglioDataset.jsp?menu=dati&idPag=5">Ministero della Salute</a></br>Trova <a href="https://telegram.me/FarmacieBot" target="_blank">FarmacieBot</a> anche su Telegram</p>
</div>
<div id='loader'><span class='message'>loading</span></div>
</div>
  <script type="text/javascript">
		var lat=parseFloat('<?php printf($_GET['lat']); ?>'),
        lon=parseFloat('<?php printf($_GET['lon']); ?>'),
        zoom=13;



        var osm = new L.TileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {maxZoom: 20, attribution: 'Map Data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'});
		var mapquest = new L.TileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {subdomains: '1234', maxZoom: 18, attribution: 'Map Data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'});
    var osm_grey = L.tileLayer('http://korona.geog.uni-heidelberg.de/tiles/roadsg/x={x}&y={y}&z={z}', {
    	maxZoom: 19,
    	attribution: 'Imagery from <a href="http://giscience.uni-hd.de/">GIScience Research Group @ University of Heidelberg</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });
        var map = new L.Map('mapdiv', {
                    editInOSMControl: true,
            editInOSMControlOptions: {
                position: "topright"
            },
            center: new L.LatLng(lat, lon),
            zoom: zoom,
            layers: [osm_grey]
        });

        var baseMaps = {
    "OSM_Grayscale": osm_grey,
    "Mapnik": osm,
    "Mapquest Open": mapquest
        };
        L.control.layers(baseMaps).addTo(map);
        var markeryou = L.marker([parseFloat('<?php printf($_GET['lat']); ?>'), parseFloat('<?php printf($_GET['lon']); ?>')]).addTo(map);
        markeryou.bindPopup("<b>Sei qui</b>");
       var ico=L.icon({iconUrl:'farmacia.png', iconSize:[20,20],iconAnchor:[10,0]});
       var markers = L.markerClusterGroup({spiderfyOnMaxZoom: false, showCoverageOnHover: true,zoomToBoundsOnClick: true});

        function loadLayer(url)
        {
                var myLayer = L.geoJson(url,{
                        onEachFeature:function onEachFeature(feature, layer) {
                          var popup = '';
                          var str = ".jpg";
                          //var title = bankias.getPropertyTitle(clave);
                          popup += 'Dista: '+feature.properties.distanza+'</b><br />';
                          popup += feature.properties.nome+'</b><br />';
                          popup += feature.properties.indirizzo+' '+feature.properties.cap+' '+feature.properties.comune+'</b><br />';;
                          popup += '<a href="http://map.project-osrm.org/printing.html?z=14&center=40.351025%2C18.184133&loc='+lat+'%2C'+lon+'&loc='+feature.geometry.coordinates[1]+'%2C'+feature.geometry.coordinates[0]+'&hl=en&ly=&alt=&df=&srv=" target="_blank">Percorso fin qui</a>';
                                if (feature.properties && feature.properties.id) {
                                }
layer.bindPopup(popup);
                        },
                        pointToLayer: function (feature, latlng) {
                        var marker = new L.Marker(latlng, { icon: ico });

                        markers[feature.properties.id] = marker;
                      //  marker.bindPopup('<img src="http://www.piersoft.it/dae/ajax-loader.gif">',{maxWidth:50, autoPan:true});

                      //  marker.on('click',showMarker());
                        return marker;
                        }
                }).addTo(map);

              //  markers.addLayer(myLayer);
              //  map.addLayer(markers);
              //  markers.on('click',showMarker);
        }

microAjax('mappaf.json',function (res) {
var feat=JSON.parse(res);
loadLayer(feat);
  finishedLoading();
} );
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
</script>

</body>
</html>
