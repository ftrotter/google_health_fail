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
	require_once('menu.php');
	require_once('PatientsTab.php');
	require_once('ClinicalAccess.php');

	$head = new HTMLHeader();	
	$menu = new menu();


	$clinicalUser = $GLOBALS['clinicalAccess'];

	$user_id = 1;
	$patt = new PatientsTab($clinicalUser->npi);


	$head->setTheme();
	echo $head->getHeader();
	echo $menu->getMenu();
	$patt->asIframe();
	$patientTab = $patt->printContent();
	echo $patientTab;





	echo $head->getFooter();


?>
