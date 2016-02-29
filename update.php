<?php
//$indirizzo ="https://docs.google.com/spreadsheets/d/1b4-93cKOzJvwHzkeMcwAgNC5pVa8QlCrHu6zYy9cvzk/pub?gid=473365486&single=true&output=csv";
//$inizio=1;
$homepage ="";
//  echo $url;
//$csv1 = array_map('str_getcsv', file($indirizzo));
	$url ="http://www.dati.salute.gov.it/imgs/C_17_dataset_5_download_itemDownload0_upFile.CSV";

  $homepage1 = file_get_contents($url);
	$homepage1=str_replace(",",".",$homepage1);
	//$homepage1=str_replace("/","-",$homepage1);
	//$homepage1=str_replace("\"","-",$homepage1);
	//$homepage1=str_replace("*","-",$homepage1);
//  $homepage1=str_replace(";",",",$homepage1);

  $file = '/usr/www/piersoft/FarmacieBot/mappa/farmacie.csv';

// Write the contents back to the file
  file_put_contents($file, $homepage1);
	echo "finito download farmacie";

  ?>
