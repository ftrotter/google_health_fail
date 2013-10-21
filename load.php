<?php
	session_start();

      	if(isset($_SESSION['key'])){
                $key = $_SESSION['key'];
        }
        if(isset($_GET['key'])){
                $key = $_GET['key'];
                $_SESSION['key'] = $key;
        }

	if(!isset($key)){
		echo "Not sure how you got here without a key in your session or your get. But thats an error";
		exit();

	}


	require_once('check_SAML.php');

	//if we are here then we are validly logged in!!

	header("Location: message.page.php");	

	require_once('functions.php');
	require_once('head.php');
	require_once('ClinicalAccess.php');



	$clinicalUser = new ClinicalAccess();

	$head = new HTMLHeader();
	$user_id = 1;

	if(!isset($key)){
		//then we are using the system without a patient selected
		// display the protocols and the patient selection widget	
		$patient_id = 0;
		$no_patient = true;
	}else{

		require_once('QRimg.php');
		require_once('SimpleTab.php');
		require_once('CCR.php');
		require_once('Plans.php');
		$patient_id = patient_id_from_key($key);
		$_SESSION['patient_id'] = $patient_id;
		//echo "key = $key and patient_id = $patient_id <br>";
		$ccr = new CCRParser($patient_id);
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
$head->setLogo();
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
        style='width:100%;height:4000px'>
";


$attributes = " frameborder='0' scrolling='no' width='100%' height='3000' ";

if(!$no_patient){

$plans_iframe = 
"<iframe src='plans.iframe.php?key=$key' name='plans_iframe' $attributes > turn on iframe support </iframe>";
$plans_tab = new SimpleTab('Plans',$plans_iframe);
echo $plans_tab->printTab();

$message_iframe = 
"<iframe src='message.iframe.php?key=$key' name='message_iframe' $attributes> turn on iframe support </iframe>";
$message_tab = new SimpleTab('Messages',$message_iframe);
echo $message_tab->printTab();
}//if !nopatient


$protocol_iframe = 
"<iframe src='protocol.iframe.php?key=$key' name='protocol_iframe' $attributes> turn on iframe support </iframe>";
$proto_tab = new SimpleTab('Protocols',$protocol_iframe);
echo $proto_tab->printTab();

$patientlist_iframe = 
"<iframe src='patientlist.iframe.php?key=$key' name='patientlist_iframe' $attributes> turn on iframe support </iframe>";
$proto_tab = new SimpleTab('Patient List',$patientlist_iframe);
echo $proto_tab->printTab();



echo "</div>";


echo $head->getFooter();


?>
