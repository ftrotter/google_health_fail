<?php
	require_once('check_Google.php');

	//if we are here then we are validly logged in!!


	require_once('functions.php');
	require_once('head.php');
	require_once('MessageTab.php');
	require_once('patient_menu.php');

	global $head;
	$head = new HTMLHeader();
	$menu = new menu();

	$mt = new MessageTab();

	$head->setTheme();
	echo $head->getHeader();
	echo $menu->getMenu();
	echo $mt->getContent();


	echo $head->getFooter();


?>
