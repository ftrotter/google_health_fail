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
	require_once('ProtocolTab.php');
	require_once('PatientsTab.php');



	$clinicalUser = new ClinicalAccess();

	$head = new HTMLHeader();
	$user_id = 1;
	$prot = new ProtocolTab($user_id);
	$patt = new PatientsTab();
	$forms = new ModalForms();

	if(!isset($key)){
		//then we are using the system without a patient selected
		// display the protocols and the patient selection widget	
		$patient_id = 0;
		$no_patient = true;
	}else{

		require_once('QRimg.php');
		require_once('MessageTab.php');
		require_once('PlansTab.php');
		require_once('SimpleTab.php');
		require_once('CCR.php');
		require_once('Plans.php');
		$patient_id = patient_id_from_key($key);
		$_SESSION['patient_id'] = $patient_id;
		//echo "key = $key and patient_id = $patient_id <br>";
		$mt = new MessageTab($patient_id);
		$ccr = new CCRParser($patient_id);
		$plans_tab = new PlansTab($patient_id,$ccr);
		$ccr_tab = new SimpleTab("Raw CCR",$ccr->getHTMLCCR());
		$qr = new QRimg();
		$isAccessRevoked = $clinicalUser->isRevoked($patient_id);
		$no_patient = false;


	}


	$head->addTitle('AJAX QHR');
	


/*

  x	1. Figure out how to post the results of the dialoug to the server...
 	2. Create a hidden table for each of the editable text fields including responsible doctor. 
	3. Create a Risk Factors checkbox field for the risk factors
  x	5. Create display of the CCR above the plans with an eye towards draggability.
	6. Add + buttons to the display of the problems... in order to create plans automatically from them
  x	7. Create the ability to drag and drop medications
  x	8. Create the ability to drag and drop any CCR object to Risk Factors 
  x	9. Quickly build out a xxzendxx custom backend to save the plan data. 

*/

$head->setTheme();
$logout_menu = "<a href='clinical_logout.php'>Logout</a>";
$head->setTopRight($logout_menu);
echo $head->getHeader();








//echo $qr->getQRimg();

if(!$no_patient){

	

	$patient_name = $ccr->getPrintName();

	echo "
		<h2 style='text-align: center'> Patient:  $patient_name </h2>
	";
}

echo "
   <div id='mainTabContainer' dojoType='dijit.layout.TabContainer'
        style='width:100%;height:3000px'>
";

if(!$no_patient){

echo $plans_tab->printTab();
echo $forms->getForms();
echo "<div id='post_response'></div>";

$debug = false;
if($debug){
	echo $ccr_tab->printTab();
}


echo $mt->printTab();


}//if !nopatient






$protocol = $prot->printTab();

echo $protocol;

$patientTab = $patt->printPatientTab($clinicalUser->npi);
echo $patientTab;



echo "</div>";


echo $head->getFooter();


?>
