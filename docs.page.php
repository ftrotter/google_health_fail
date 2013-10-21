<?php
	session_start();

      	if(isset($_SESSION['key'])){
                $key = $_SESSION['key'];
        }
        if(isset($_GET['key'])){
                $key = $_GET['key'];
                $_SESSION['key'] = $key;
        }

	require_once('check_SAML.php');

	//if we are here then we are validly logged in!!

	require_once('functions.php');
	require_once('head.php');
	require_once('ClinicalAccess.php');
	require_once('MessageTab.php');
	require_once('menu.php');
	require_once('mysql.php');

	global $head;
	$head = new HTMLHeader();
	$menu = new menu();
	$head->setTheme();

	$clinicalUser = $GLOBALS['clinicalAccess'];

        $patient_id = patient_id_from_key($key);


	echo $head->getHeader();
	echo $menu->getMenu();


	$user_list_sql = "
SELECT person.first_name, person.last_name, cpa.npi, 
qnpi.quicksearch.first_name as npi_first, 
qnpi.quicksearch.last_name as npi_last 
FROM `clinician_patient_access` as cpa
LEFT JOIN person ON person.person_id = cpa.clinical_user_id
LEFT JOIN qnpi.quicksearch ON cpa.npi = qnpi.quicksearch.npi
WHERE `patient_id` = $patient_id 
AND `revoked` = 0
";

/*
$user_list_sql = "
SELECT person.first_name, person.last_name, cpa.npi, 
npifn.name as npi_first, npiln.name as npi_last FROM `clinician_patient_access` as cpa 
LEFT JOIN person ON person.person_id = cpa.clinical_user_id LEFT JOIN npi.npi_to_name as npifntn ON cpa.npi = npifntn.npi_id 
LEFT JOIN npi.name as npifn ON npifntn.name_id = npifn.id
LEFT JOIN npi.npi_to_name as npilntn ON cpa.npi = npilntn.npi_id 
LEFT JOIN npi.name as npiln ON npilntn.name_id = npiln.id
WHERE `patient_id` = $id AND `revoked` = 0 
AND npilntn.nametype_id = 2
AND npifntn.nametype_id = 3

";
*/

	$html = "<h2> This is the start of an interface to manage the doctors associated with a patient. Currently showing patient $patient_id</h2>";
	$html .="<h3> The following users have 'claimed' access to the patients records and the patient has not revoked that access </h3>";
	$html .="<h3> This list will drive the dropdown list of messagable people </h3>";
	$html .= "\n<ul>\n";
 	$result = mysql_query($user_list_sql) or die("Could not get a list of users <br /> $user_list_sql <br />". mysql_error());	
 	while($row = mysql_fetch_array( $result )){
		if(strlen($row['first_name']) < 2){
			$user = 'no login yet';
		}else{
			$user = 'User: '.$row['first_name'].' '.$row['last_name'];
		}
		$html .= '<li>'.$row['npi_last'].' '.$row['npi_first'] .' (npi: '.$row['npi'].')'." $user </li>\n";
	}
	$html .= "\n</ul>\n";

	$html .= "
<h3> Add a clinician to this patients record from the NPI Database</h3>
<br>
<script language='javascript'>

        function getNPIblock(){
	
		var city_name = document.getElementById('city_name'); 
		var first_name = document.getElementById('first_name'); 
		var last_name = document.getElementById('last_name'); 

		var error_div = document.getElementById('npi_form_error');
		if(city_name.value.length < 1 || first_name.value.length < 1 || last_name.value.length < 1 ){
			error_div.innerHTML = 'You must give a first name, last name and city name to search';
			return;			 
		}else{
			error_div.innerHTML = '';
		}

		

             var extra_get = '?city_name=' + city_name.value + '&'
			+ 'first_name=' + first_name.value + '&' 	
			+ 'last_name=' + last_name.value  ; 	
                
		var npi_url = 'npi_proxy.php' + extra_get;
		jah(npi_url,'npi_results');	

        }

</script>



<form action='doc_add.php' method='POST'>
<input type='hidden' id='patient_id' name='patient_id' value='$patient_id'>
<table><tr>
<td> Clinicians First Name: </td>
<td> <input name='first_name' id='first_name' type='text'> </td> 
<td rowspan='3'>     
<div style='padding: 20px' id='npi_results'>

</div>
</td>
</tr>
<tr>
<td> Clinicians Last Name: </td>
<td> <input name='last_name' id='last_name' type='text'> </td>
</tr>
<tr>
<td> Clinicians City Name: </td>
<td> <input name='city_name' id='city_name' type='text'> </td>
</tr>
<tr>
<td>  </td>
<td> <input id='npi_button' value='Search in NPI database' type='button' onclick='getNPIblock();'> </td>
</tr>
</table>
</form>
<div style='color: red' id='npi_form_error'></div>
";


	echo $html;
	echo $head->getFooter();


?>
