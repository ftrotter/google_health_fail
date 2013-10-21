<?php

        require_once('check_Google.php');
	//If we get here we are a patient logged in via Google
        require_once 'head.php';
        require_once 'mysql.php';
        require_once 'config.php';
        require_once 'patient_menu.php';


$head = new HTMLHeader();
$head->setTheme();
$head->addTitle('Patient Interface');

echo $head->getHeader();

$menu = new menu();

$menu->printMenu();

$user_email = $_SESSION['user_email'];

$sql = "
SELECT 
person.first_name as user_first_name,
person.last_name as user_last_name,
quicksearch.last_name as npi_last_name,
quicksearch.first_name as npi_first_name,
quicksearch.npi as npi
 FROM `patientuser` 
JOIN clinician_patient_access AS cpa ON patientuser.id = cpa.patient_id
JOIN qnpi.quicksearch ON quicksearch.npi = cpa.npi
LEFT JOIN person ON cpa.clinical_user_id = person.person_id
WHERE `google_account` = '$user_email'
";

echo "<h3> Clinical User Access </h3>\n";

$result = mysql_query($sql) or die('Error: could not load which clinicians have access to this patient:' . mysql_error());
while($row = mysql_fetch_array($result)){
	$rows[] = $row;
	if(isset($row['user_first_name'])){
		echo "a user named ". $row['user_first_name']. " ". $row['user_last_name'] ." under clinician (". $row['npi'] . ") ".$row['npi_first_name']. " ".$row['npi_last_name']   ." has access to your records <br />";
	}else{
		echo "The clinician (". $row['npi'] . ") ".$row['npi_first_name']. " ".$row['npi_last_name']   ." has access to your records, but is recieving messages via faxes for now <br />";
	}

}

echo $head->getFooter();



?>
