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
	require_once('ProtocolTab.php');
	require_once('ClinicalAccess.php');
	require_once('menu.php');



	$clinicalUser = new ClinicalAccess();

        $user_id = 1;
        $prot = new ProtocolTab($user_id);


	$head = new HTMLHeader();
	$menu = new menu();
	$user_id = 1;


	$head->setTheme();
	echo $head->getHeader();
	echo $menu->getMenu();
$protocol = $prot->printContent();

echo $protocol;



	echo $head->getFooter();


?>
