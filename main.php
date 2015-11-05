<?php
/**
* Telegram Bot example for Italian Museums of DBUnico Mibact Lic. CC-BY
* @author Francesco Piero Paolicelli @piersoft
*/
//include("settings_t.php");
include("Telegram.php");

class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	//$data=new getdata();
	// Instances the class

	/* If you need to manually take some parameters
	*  $result = $telegram->getData();
	*  $text = $result["message"] ["text"];
	*  $chat_id = $result["message"] ["chat"]["id"];
	*/


	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");

	if ($text == "/start") {
		$reply = "Benvenuto. Per ricercare una farmacia, clicca sulla graffetta (📎) e poi 'posizione'. Verrà interrogato il DataBase openData del Ministero della Sanità utilizzabile con licenza iodl2.0 e verranno elencate le farmacie del comune scelto. In qualsiasi momento scrivendo /start ti ripeterò questo messaggio di benvenuto.\nQuesto bot, non ufficiale, è stato realizzato da @piersoft e il codice sorgente per libero riuso si trova su https://github.com/piersoft/FarmacieBot. La propria posizione viene ricercata grazie al geocoder di openStreetMap con Lic. odbl.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ";new chat started;" .$chat_id. "\n";

		}

		//gestione segnalazioni georiferite
		elseif($location!=null)
		{

			$this->location_manager($telegram,$user_id,$chat_id,$location);
			exit;

		}
//elseif($text !=null)

		else{
			$location="Sto cercando le Famacie del Comune di: ".$text;
			$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
			sleep (1);

						$url="http://opendatasalute.cloudapp.net/DataBrowser/DownloadCsv?container=datacatalog&entitySet=Farmacie&filter=descrizionecomune%20eq%20%27".strtoupper($text)."%27";
				//echo $url;
						$csv = array_map('str_getcsv', file($url));
							$csv=str_replace(",",".",	$csv);
							$data="";
							$count = 0;
							foreach($csv as $data1=>$csv1){
								 $count = $count+1;
							}
							echo $count;
			if ($count >=50){
			$location="Sono più di 30\nGli short links goo.gl vengono disabilitati per permettere una veloce risposta:";
			$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
			sleep(2);
			}
			if ($csv[1][1] ==""){
				$reply = "Hai selezionato un comune non riconosciuto. Ricordati che puoi sempre inviare la tua posizione cliccando sulla graffetta (📎) ";
				$content = array('chat_id' => $chat_id, 'text' => $reply);
				$telegram->sendMessage($content);

				$log=$today. ";wrong command sent;" .$chat_id. "\n";
				//$this->create_keyboard($telegram,$chat_id);
			}
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


							if ($csv[$i][19] !=NULL AND $count<31){

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
							$data .="Mappa: ".$shortLink['id']."\n";
							}
							if ($csv[$i][19] !=NULL AND $count>=31){

								$data .= "Guardalo sulla mappa:\nhttp://www.openstreetmap.org/?mlat=".$latitudine."&mlon=".$longitudine."#map=19/".$latitudine."/".$longitudine."\n\n";

							}
							}

					//	echo $alert;

						$chunks = str_split($data, self::MAX_LENGTH);
						foreach($chunks as $chunk) {
				$forcehide=$telegram->buildForceReply(true);
					$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);

							$telegram->sendMessage($content);

						}

		//	}
/*
			 $reply = "Hai selezionato un comando non previsto. Ricordati che devi prima inviare la tua posizione cliccando sulla graffetta (📎) ";
			 $content = array('chat_id' => $chat_id, 'text' => $reply);
			 $telegram->sendMessage($content);

			 $log=$today. ";wrong command sent;" .$chat_id. "\n";
			 //$this->create_keyboard($telegram,$chat_id);
*/

	}


}


// Crea la tastiera
function create_keyboard($telegram, $chat_id)
 {
	 $forcehide=$telegram->buildKeyBoardHide(true);
	 $content = array('chat_id' => $chat_id, 'text' => "Invia la tua posizione cliccando sulla graffetta (📎) in basso e, se vuoi, puoi cliccare due volte sulla mappa e spostare il Pin Rosso in un luogo specifico", 'reply_markup' =>$forcehide);
	 $telegram->sendMessage($content);

 }




function location_manager($telegram,$user_id,$chat_id,$location)
	{

			$lon=$location["longitude"];
			$lat=$location["latitude"];
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
			$location="Sto cercando le Famacie del Comune di: ".$comune." tramite le coordinate che hai inviato: ".$lat.",".$lon;
			$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
			$telegram->sendMessage($content);
	//	echo "comune: ".$comune."\n</br>";

		    $url="http://opendatasalute.cloudapp.net/DataBrowser/DownloadCsv?container=datacatalog&entitySet=Farmacie&filter=descrizionecomune%20eq%20%27".strtoupper($comune)."%27";
		//echo $url;
				$csv = array_map('str_getcsv', file($url));
					$csv=str_replace(",",".",	$csv);
				  $data="";
					$count = 0;
					foreach($csv as $data1=>$csv1){
					   $count = $count+1;
					}
					echo $count;
if ($count >=50){
	$location="Sono più di 30\nGli short links goo.gl vengono disabilitati per permettere una veloce risposta:";
	$content = array('chat_id' => $chat_id, 'text' => $location,'disable_web_page_preview'=>true);
	$telegram->sendMessage($content);
sleep(3);
}
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


					if ($csv[$i][19] !=NULL AND $count<31){
						$theta = $lon-$longitudine;
	  				$dist = sin(deg2rad($lat)) * sin(deg2rad($latitudine)) +  cos(deg2rad($lat)) * cos(deg2rad($latitudine)) * cos(deg2rad($theta));
	  				$dist = acos($dist);
						$dist = rad2deg($dist);
	  				$miles = $dist * 60 * 1.1515 * 1.609344;
						if ($miles >=1){
							$data .="Distanza approssimativa: ".number_format($miles, 2, '.', '')." Km\n";
						} else $data .="Distanza approssimativa: ".number_format(($miles*1000), 0, '.', '')." mt\n";


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
					$data .="Mappa: ".$shortLink['id']."\n";
			  	}
					if ($csv[$i][19] !=NULL AND $count>=31){
						$theta = $lon-$longitudine;
						$dist = sin(deg2rad($lat)) * sin(deg2rad($latitudine)) +  cos(deg2rad($lat)) * cos(deg2rad($latitudine)) * cos(deg2rad($theta));
						$dist = acos($dist);
						$dist = rad2deg($dist);
						$miles = $dist * 60 * 1.1515 * 1.609344;
						if ($miles >=1){
							$data .="Distanza: ".number_format($miles, 2, '.', '')." Km\n";
						} else $data .="Distanza: ".number_format(($miles*1000), 0, '.', '')." mt\n";

						$data .= "Guardalo sulla mappa:\nhttp://www.openstreetmap.org/?mlat=".$latitudine."&mlon=".$longitudine."#map=19/".$latitudine."/".$longitudine."\n\n";

 }
		    	}

			//	echo $alert;

				$chunks = str_split($data, self::MAX_LENGTH);
				foreach($chunks as $chunk) {
		$forcehide=$telegram->buildForceReply(true);
			$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);

					$telegram->sendMessage($content);

				}

	}


}

?>
