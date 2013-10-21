<?php
	require_once('ClinicalAccess.php');
	require_once('config.php');
	class menu{

	var $patientName = false;

	var $core_menu_links = array(
		'Patient List' => 'patientlist.page.php',
		'Messages' => 'message.page.php',
		'Protocols' => 'protocol.page.php',
		'Logout' => 'clinical_logout.php',
		);

	var $patient_menu_links = array (
		'Plans' => 'plans.page.php',
		'Manage Doctors' => 'docs.page.php',	
		'Send Message' => 'send.page.php'
		);


		function __construct(){

			$key = false;
		      	if(isset($_SESSION['key'])){
		                $key = $_SESSION['key'];
	       	 	}
	 	        if(isset($_GET['key'])){
		                	$key = $_GET['key'];
	       	         	$_SESSION['key'] = $key;
	        	}


			if($key){
				$patient_id = patient_id_from_key($key);
				$person = patient_row_from_id($patient_id);
				$this->patientName = $person['last_name']." ". $person['first_name'];

			}
			
			if(isset($GLOBALS['clinicalAccess'])){
				$clinicalUser = $GLOBALS['clinicalAccess'];
			}else{
				$clinicalUser = new ClinicalAccess();
				$GLOBALS['clinicalAccess'] = $clinicalUser;
			}

			if($GLOBALS['demo']){
				$this->addItem('(demo-only) BTG more patients','demo.php');
			}
			if($GLOBALS['debug']){
				$this->addItem('(debug-only) Admin Send Notices ','adminsendnotice.page.php');
				$this->addPatientItem('(debug-only) View CCR Dump ','ccr.page.php');
			}

		}



		function addItem($label,$link){
			$this->core_menu_links[$label] = $link;	
		}

		function addPatientItem($label,$link){
			$this->patient_menu_links[$label] = $link;	
		}

		function printMenu(){
			$menu = $this->getMenu();

			echo $menu;
		

		}

		function getMenu(){
			$return_me = "<ul class='menu'>\n";
			$return_me .= "<li> Core Menu </li>\n";
			foreach($this->core_menu_links as $label => $link){
					$return_me .= "   <li style='margin-left:8px;'> <a href='$link'>$label</a></li>\n ";
			}
			
			if($this->patientName){
				$return_me .= "<li>$this->patientName Patient Menu</li>\n";
				foreach($this->patient_menu_links as $label => $link){
					$return_me .= "   <li style='margin-left:8px;'> <a href='$link'>$label</a></li>\n ";
				}
			}


			$return_me .= "</ul>";
			return($return_me);	
		}

	}

?>
