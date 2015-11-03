<?php
include('settings_t.php');
$lat=40.6701;
$lon=16.5999;

	//$lon=$row[0]['lng'];
	//$lat=$row[0]['lat'];
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

	$location="Comune di: ".$comune." tramite le coordinate che hai inviato: ".$lat.",".$lon;
	$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
	$telegram->sendMessage($content);
$comune=str_replace(" ","",	$comune);
	//	echo "comune: ".$comune."\n</br>";
    $url="http://opendatasalute.cloudapp.net/DataBrowser/DownloadCsv?container=datacatalog&entitySet=Farmacie&filter=descrizionecomune%20eq%20".$comune;
		$csv = array_map('str_getcsv', file($url));
		$csv=str_replace(",",".",	$csv);
		  $data="";
			$count = 0;
			foreach($csv as $data1=>$csv1){
			   $count = $count+1;
			}
	//		echo $count;

			for ($i=1;$i<$count;$i++){
			$data .="\n";
			$data .="Nome: ".$csv[$i][4]."\n";
			$data .="Indirizzo: ".$csv[$i][3]."\n".$csv[$i][6]." ".$csv[$i][8]."\n";
			$lat1 =substr($csv[$i][19], 0, 2);
			$lat2 =substr($csv[$i][19], 2, 6);
			$lon1 =substr($csv[$i][20], 0, 2);
			$lon2 =substr($csv[$i][20], 2, 6);
		//	$data ="Lat".$lat1.".".$lat2;
		//	$data ="Lon".$lon1.".".$lon2;
			$latitudine =$lat1.".".$lat2;
			$longitudine =$lon1.".".$lon2;
			if ($csv[$i][19] !=NULL){
			$longUrl = "http://www.openstreetmap.org/?mlat=".$latitudine."&mlon=".$longitudine."#map=19/".$latitudine."/".$longitudine;

			$apiKey = API;

			$postData = array('longUrl' => $longUrl, 'key' => $apiKey);
			$jsonData = json_encode($postData);

			$curlObj = curl_init();

			curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey);
			curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curlObj, CURLOPT_HEADER, 0);
			curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
			curl_setopt($curlObj, CURLOPT_POST, 1);
			curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

			$response = curl_exec($curlObj);

			// Change the response json string to object
			$json = json_decode($response);

			curl_close($curlObj);
			$shortLink = get_object_vars($json);
			$data .="\nMappa: ".$shortLink['id'];
	  	}
    	}
      echo $data;
?>
