<?php
	require_once('check_Google.php');
	require_once('config.php');
	require_once('functions.php');
	class menu{

	var $patientName = false;

	var $core_menu_links = array(
		'My Doctors' => 'patient.php',
		'Inbox' => 'message.patient.php',
		'Send Message' => 'send.patient.php',
		'My Plan' => 'plans.patient.php',
		);



		function __construct(){

			$this->patientName = $_SESSION['person']['last_name']." ". $_SESSION['person']['first_name'];
			
		}



		function addItem($label,$link){
			$this->core_menu_links[$label] = $link;	
		}

		function printMenu(){
			$menu = $this->getMenu();

			echo $menu;
		

		}

		function getMenu(){
			$first = $_SESSION['person']['first_name'];
			$last = $_SESSION['person']['last_name'];

			$return_me = "<h1> Welcome to your QHR Record $first, $last</h1> ";

			$return_me .= "<ul class='menu'>\n";
			foreach($this->core_menu_links as $label => $link){
					$return_me .= "   <li style='margin-left:8px;'> <a href='$link'>$label</a></li>\n ";
			}
			
			$return_me .= "</ul>";
			return($return_me);	
		}

	}

?>
