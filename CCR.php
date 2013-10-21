<?php

	require_once('mysql.php');
	require_once('GoogleHealth.php');

	class CCRParser{

		private $CCR = array();
		private $CCRtext = '';
		private $CCRmeds = array();
		private $Demo = array();
		private $Protocols = array();
		public $PatientID = 1;

		function __construct($patient_id = 0){

			if($patient_id == 0){
				$this->PatientID = 1;
			}else{
				$this->PatientID = $patient_id;
			}


			//XML parsing goes here!!

	//		$this->_demo_data();
			$this->_mysql_data($patient_id);
		}

		function getHTMLCCR(){
			if(strlen($this->CCRtext) == 0){
				return('There is no CCR text for '. $this->PatientID);
			}
			$return_me = "<pre> ". $this->xmlpp($this->CCRtext,true) . "</pre>";
			return($return_me);
		}

		function __toString(){
			return($this->getHTMLCCR());
		}

function xmlpp($xml, $html_output=false) {  
	try{
     		$xml_obj = new SimpleXMLElement($xml); 
	}catch(Exception $e){
		// no parsing :(
		return("Could not parse this xml string start: $xml :end");
	} 
     $level = 4;  
     $indent = 0; // current indentation level  
     $pretty = array();         
     // get an array containing each XML element  
     $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));  
   
     // shift off opening XML tag if present  
     if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {  
       $pretty[] = array_shift($xml);  
     }  
   
     foreach ($xml as $el) {  
       if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {  
           // opening tag, increase indent  
           $pretty[] = str_repeat(' ', $indent) . $el;  
           $indent += $level;  
       } else {  
         if (preg_match('/^<\/.+>$/', $el)) {              
           $indent -= $level;  // closing tag, decrease indent  
         }  
         if ($indent < 0) {  
           $indent += $level;  
         }  
         $pretty[] = str_repeat(' ', $indent) . $el;  
       }  
     }     
     $xml = implode("\n", $pretty);     
     return ($html_output) ? htmlentities($xml) : $xml;  
 }  




		function getCCR(){

			$id = $this->PatientID;
			if(!isset($id)){
				//then there is not patient selected
				if($GLOBALS['debug']){
					echo "There is not patient id, this is not suppose to happen<br>";
				}
				return(false);
			}

                        $ccr_sql = "SELECT * FROM ccrrecords WHERE patient_id = '$id' ORDER BY date DESC";
                        $result = mysql_query($ccr_sql) or die("CCR.php Error did not get CCR sql $ccr_sql:".mysql_error());
                        $row = mysql_fetch_array( $result );
			if(count($row) > 0){
        	                $xml_text = $row['xml'];
				$date = $row['date'];
				$thistime=strtotime($date);
				$an_hour_ago = strtotime("-1 hour");
				if($an_hour_ago > $thistime){
					$this->updateCCR();
			
				}else{

					$this->CCRtext = $xml_text;
				}

			}else{
				$this->updateCCR();
			}
			
			return(true);
			
		}

	function updateCCR(){

		//first lets get the session from the database!!
		$patient_id = $this->PatientID;
		$patient_id = mysql_real_escape_string($patient_id);
		$sql = "SELECT google_account FROM patientuser WHERE id = '$patient_id'";
  		$result = mysql_query($sql) or die("CCR.php Error:".mysql_error());
       		$row = mysql_fetch_array( $result );
        	$google_account = $row['google_account'];

		$GH = new GoogleHealth($google_account);
		$GH->updateCCR();


	}



		function _mysql_data($patient_id){



			if($patient_id == 0){
				if(isset($_SESSION['key'])){

					$key = $_SESSION['key'];
					$find_patient_id_sql = "
SELECT *
FROM `patientuser`
WHERE `key` = '$key'";
				
					$result = mysql_query($find_patient_id_sql) or die("CCR.php Error loading patient user:".mysql_error());
					$row = mysql_fetch_array( $result );
					$this->PatientID = $row['id'];

				}
			}
				


			$id = $this->PatientID;

			if(!$this->getCCR()){
				//then we do not have a record... we can just stop here...
				if($GLOBALS['debug']){
					echo "No results from getCCR... ok only if this record is entirely empty<br>";
				}

				return(false);
			}
			$xml_text = $this->CCRtext;

			$xml = new SimpleXMLElement($xml_text);
			// the ccr namespace is not always present... frustrating....
			$namespaces = $xml->getNameSpaces(true);
			if(isset($namespaces['ccr'])){
				// great!! then it is already there and we are very happy!!
			}else{
				$xml->registerXPathNamespace('ccr', 'urn:astm-org:CCR');
			}



			$Problems = $xml->xpath("//*[local-name() = 'Problem']");
			$count = 0;
			if(!empty($Problems)){
			foreach($Problems as $aProblem){
				
				$desc = $aProblem->Description->Text;
				$value = $aProblem->Description->Code->Value;
				$system = $aProblem->Description->Code->CodingSystem;
				
				
				$this->CCR['Problems'][$this->PatientID . '_problem_'.$count] = "$desc<br /> ($value: $system )";
				$count++;
			}
			}

			$Procedures = $xml->xpath("//*[local-name() = 'Procedure']");
			$count = 0;
			if(!empty($Procedures)){
			foreach($Procedures as $aProcedure){
			
				$desc = $aProcedure->Description->Text;
				if(!isset($value)){$value = '';}	
				if(!isset($system)){$system = '';}	
				$this->CCR['Procedures'][$this->PatientID . '_procedure_'.$count] = "$desc<br /> $value: $system ";
				$count++;
			}
			}

			$Vitals = $xml->xpath("//*[local-name() = 'VitalSigns']/*[local-name() = 'Result']");
			$count = 0;
			if(!empty($Vitals)){
			foreach($Vitals as $aVital){
			
				$desc = $aVital->Test->Description->Text;
				$value = $aVital->Test->TestResult->Value;
				$unit = $aVital->Test->TestResult->Units->Unit;
				
				$good = true;

				if(strcmp(strtolower($desc),'height') == 0 && strcmp(strtolower($unit),'centimeters') == 0){
					//then this is Height in centimeters, which for now we ignore
					$good = false;
				}	
				if(strcmp(strtolower($desc),'weight') == 0 && strcmp(strtolower($unit),'kilograms') == 0){
					//then this is Weight in kilograms, which for now we ignore
					$good = false;
				}
	
				if(strcmp(strtolower($desc),'weight') == 0 && strcmp(strtolower($unit),'ounces') == 0){

					if($value > 160){ //that is... above ten pounds
						$value = $value * .0625; //convert to pounds
						$unit = 'pounds';
					}
				}	
				
				
				if($good){
					$this->CCR['VitalSigns'][$this->PatientID . '_result_'.$count] = "$desc:<br /> $value $unit ";
					$count++;
				}
			}
			}
			
			$Results = $xml->xpath("//*[local-name() = 'Results']/*[local-name() = 'Result']");
			$count = 0;
			if(!empty($Results)){
			foreach($Results as $aResult){
			
				$desc = $aResult->Test->Description->Text;
				$value = $aResult->Test->TestResult->Value;
				$unit = $aResult->Test->TestResult->Units->Unit;
				
				
				$this->CCR['Results'][$this->PatientID . '_result_'.$count] = "$desc: <br />$value $unit ";
				$count++;
			}
			}

			$Medications = $xml->xpath("//*[local-name() = 'Medication']");
			$count = 0;
			if(!empty($Medications)){
			foreach($Medications as $aMedication){
					
				if(strcmp($aMedication->Status->Text,'ACTIVE') == 0){//then we care!!
					$product = $aMedication->Product->ProductName->Text;
					if(isset($aMedication->Strength->Value)){
						$strength = 	$aMedication->Strength->Value . " " .
								$aMedication->Strength->Units->Unit;
					}else{
						$strength = 'no strength recorded';
					}
					if(isset($aMedication->Directions->Direction->Frequency->Value)){
						$freq = $aMedication->Directions->Direction->Frequency->Value;
					}else{
						$freq = 'no frequency recorded';
					}	



					$this->CCRmeds['Medications'][$this->PatientID . '_medications_'.$count] = "$product $strength $freq ";
					$count++;

				}
				
			}
			}



		}


		function getPrintName(){

			$patient_id = $this->PatientID;
                        $problem_sql = "
SELECT CONCAT( first_name, ' ', middle_name, ' ', last_name ) AS name
FROM `patientuser`
JOIN person ON person.person_id = patientuser.id
WHERE patientuser.id = $patient_id
";

                        $result = mysql_query($problem_sql) or die("CCR.php Error getting print name:".mysql_error());
                        $problem_array = array();
                        $row = mysql_fetch_array( $result );

                        return($row['name']);



		}


		function _demo_data(){


			$this->CCRmeds = array(
			'Medications' => array(
            			'1231257' => 'Lipitor 40mg once daily',
            			'1231258' => 'Aspirin 40 mg once daily and as needed',
            			'1231259' => 'Viagra as "needed"'		
			));

			$this->Demo = array(
			'PatientInformation' => array(
            			'Name: Fred Trotter',
            			'Address: 4414 Rockwood, Houston, TX, 77004',
            			'Cell Phone: (713) 965-4327',		
            			'Home Phone: (713) 636-2549',		
            			'email: fred.trotter@gmail.com',		
            			'DOB: 12/21/1975 (33 years old)',		
            			'Gender: Male',		
				),
			'MedicalHomeInformation' => array(
            			'Physician Name: Dr. Kim Dunn',
            			'Cell Phone: (832) 752-1635',		
            			'email: kim.dunn@uth.tmc.edu',		
            			'NPI: 1952522963',		
				),
			);

			$this->Protocols = array(
			'Protocols' => array(
            			'Asthma',
            			'Lipids',
            			'Conjestive Heart Failure',
            			'Coronary Artery Disease',
            			'Diabetes',
            			'Depression',
            			'Hypertension',
            			'Prevention',
				),
			);
		}


		function getProtocols(){
		

		$return_me = "	<table><tr>";
		$count = 0;
		foreach($this->Protocols as $Name => $Array){
			$count++;
			$return_me .= "<td valign='top'>			
			<div id='$Name"."_store'>
    				<div class='$Name"."_container'>
 		       <h3>$Name</h3>
     			   <ul id='$Name' class='container'> ";

			foreach($Array as $item){

				$return_me .= "<li><a href='#' onclick=\"addPlanRow('$item');\"> 
					<img style='float: left;' src='plus.png' alt='+'></a> &nbsp; 
					$item</li>  ";
			}

      			  $return_me .= "</ul></div></div></td>\n";
		}
		$return_me .= "</table>";
		if($count > 0){//then we have some demographic content to return!!
			return($return_me);
		}else{
			return('');//nothing to return
		}
		
		}


		function getDemographic(){

		$count = 0;
		$return_me = "	<table><tr>";
		foreach($this->Demo as $Name => $Array){
			$count++;
			$return_me .= "<td valign='top'>			
			<div id='$Name"."_store'>
    				<div class='$Name"."_container'>
 		       <h3>$Name</h3>
     			   <ul id='$Name' class='container'> ";

			foreach($Array as $item){

				$return_me .= "<li>$item</li> ";
			}

      			  $return_me .= "</ul></div></div></td>\n";
		}
		$return_me .= "</tr></table>";
		if($count > 0){//then we have some demographic content to return!!
			return($return_me);
		}else{
			return('');//nothing to return
		}
		}

		function getLists(){

	
		if($GLOBALS['debug']){
			$debug_info = 'CCR top level elements count = ' . count($this->CCR).'<br>';
			$debug_info .= 'CCR meds count = ' . count($this->CCRmeds);
		}else{
			$debug_info = '';
		}

		$return_me = "$debug_info <table><tr>";
		$count = 0;
		$got_stuff = false;
		foreach($this->CCR as $Name => $Array){
			$count++;
			$return_me .= "<td valign='top'>			
			<div id='$Name"."_store'>
    				<div class='$Name"."_container'>
 		       <h3>$Name</h3>
     			   <ul dojoType='dojo.dnd.Source' id='$Name' copyOnly='true' class='container'> ";

			foreach($Array as $id => $item){

				$return_me .= "<li id='$id' class='dojoDndItem' dndType='General'>
					<a href='#' onclick=\"addPlanRow('$item');\"> 
					<img style='float: left;' src='plus.png' alt='+'></a> 
					$item</li> ";
			}

      			  $return_me .= "</ul></div></div></td>\n";
		}
		
		if($count > 0){
			$got_stuff = true;
		}
		
		$count = 0;
		foreach($this->CCRmeds as $Name => $Array){
			$count++;
			$return_me .= "	<td valign='top'>		
			<div id='$Name"."_store'>
    				<div class='$Name"."_container'>
 		       <h3>$Name</h3>
     			   <ul dojoType='dojo.dnd.Source' id='$Name' copyOnly='true' class='container'> ";

			foreach($Array as $id => $item){

				$return_me .= "<li id='$id' class='dojoDndItem Meds' dndType='Meds'>
					<a href='#' onclick=\"addPlanRow('Asthma');\"> 
					<img style='float: left;' src='plus.png' alt='+'></a> 
					$item</li> ";
			}

      			  $return_me .= "</ul></div></div></td>\n";
		}

		if($count > 0){
			$got_stuff = true;
		}
		$return_me .= "</tr></table>";

		if($count > 0){//then we have some demographic content to return!!
			return($return_me);
		}else{
			return('');//nothing to return
		}


		}

	}

?>
