<?php
	require_once('check_SAML.php');
	require_once('mysql.php');
	require_once('config.php');

	if(isset($_POST['patient_id'])){
		//Ok then we are getting the form

		$patient_id = mysql_real_escape_string($_POST['patient_id']);
		$npi_to_add = mysql_real_escape_string($_POST['npi_select']);

		$sql = "
INSERT INTO `clinician_patient_access` 
(`id`, `npi`, `clinical_user_id`, `patient_id`, `revoked`, `illegal`, `note`, `source`) 
VALUES (NULL, '$npi_to_add', '0', '$patient_id', '0', '0', '', 'clinical_user');
			";

		mysql_query($sql) or 
			die("ERROR: failed to save patient to doc relationship $sql".mysql_error());

        $return_url = $_SERVER['HTTP_REFERER'];

	if($GLOBALS['debug']){
        echo "If you are not a developer, then you can blissfully assume the operation below was a success and then <br>";
        echo "you can go back to <a href='$return_url'>$return_url</a><br> Thank your stars that you are not obsessing about this stuff -> <br><br><br>";
		
		echo "executed <br> $sql";
	}else{
		header("Location: $return_url");
	}

	}else{


		echo "ERROR: this page should only be called by a form";
	}

	//header redirect back to the doctor page
?>
