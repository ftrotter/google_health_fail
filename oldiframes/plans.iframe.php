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
	require_once('ModalForms.php');
	require_once('ClinicalAccess.php');
	require_once('PlansTab.php');
	require_once('Plans.php');
	require_once('CCR.php');

        $patient_id = patient_id_from_key($key);
        $_SESSION['patient_id'] = $patient_id;
        //echo "key = $key and patient_id = $patient_id <br>";
        $ccr = new CCRParser($patient_id);
        $plans_tab = new PlansTab($patient_id,$ccr);

        $forms = new ModalForms();


	$clinicalUser = new ClinicalAccess);
	$isAccessRevoked = $clinicalUser->isRevoked($patient_id);

	$head = new HTMLHeader();
	$user_id = 1;


	$head->setTheme();
	echo $head->getHeader();
	echo $plans_tab->printContent();
	echo $forms->getForms();
	echo "<div id='post_response'></div>";





	echo $head->getFooter();


?>
