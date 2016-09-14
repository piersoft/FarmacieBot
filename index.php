<?php

extract($_POST);
$url = 'http://tempfile.ru/file/1483039';
$ch = curl_init();
//$file = fopen('db/spese.json', 'w+'); //da decommentare se si vuole il file locale
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/json','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
curl_setopt($ch,CURLOPT_POSTFIELDS, 'robot_code=a87a19216cdfc068d33fbf22b9e051ba');
curl_setopt($ch, CURLOPT_FILE, $file);
curl_exec($ch);
curl_close($ch);

$doc = new DOMDocument;
$doc->loadHTML($ch);

$xpa    = new DOMXPath($doc);


$divs   = $xpa->query('/*');
$allerta="";
$str_arr="";
function getInnerSubstring($string,$delim){
    // "foo a foo" becomes: array(""," a ","")
    $string = explode($delim, $string, 3); // also, we only need 2 items at most
    // we check whether the 2nd is set and return it, otherwise we return an empty string
    return isset($string[1]) ? $string[1] : '';
}
foreach($divs as $div) {
    $allerta .= "\n".$div->nodeValue;
    $str_arr = getInbetweenStrings('href=', '</a>', $div->nodeValue);

}

print_r($str_arr);

// echo $allerta;



?>
