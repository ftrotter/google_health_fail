<?php
//based on the ClearHealth chmed proxy

$apiKey = 101101;

	//The standard URL for the HealthCloud API AJAX Server
$url =  'http://npi.synseer.net/api/index.php?apiKey='. $apiKey;

if(!isset($_GET['format'])){
	$_GET['format'] = 'select';
}
if(!isset($_GET['api_key'])){
	$_GET['api_key'] = $apiKey;
}

//this code carries any GET arguments along so they are included in the request to the AJAX server
foreach($_GET as $var => $val)  {
	$url .= "&$var=$val";
}

$session = curl_init($url);

//return the response as a return value rather than just outputting
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);


$select_box = curl_exec($session);

if(strpos($select_box,'No Matching Results') !== false){
	$select_form = "No Matching Doctors";
}else{

	$select_form = "
Matching Doctors: $select_box <br>
<input type='submit' value='Add This Doctor'>
";
}
echo $select_form;

//close the curl session
curl_close($session);


?>
