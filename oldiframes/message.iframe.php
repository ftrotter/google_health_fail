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



	$clinicalUser = new ClinicalAccess();

	global $head;
	$head = new HTMLHeader();
	$user_id = 1;

	$mt = new MessageTab();

	$head->setTheme();
	echo $head->getHeader();

	echo $mt->getContent();


	echo $head->getFooter();


?>
