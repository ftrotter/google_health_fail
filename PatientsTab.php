<?php
require_once('mysql.php');
require_once('Tab.class.php');

class PatientsTab extends Tab{

	var $npi;	

	function getTitle(){
		return('Patient List');
	}
	// returns the div id for the tab
	function getID(){
		return('patient_list');
	}
	//this is where the instanciating classes determine what goes in the tab!!

	function __construct($npi){
		$this->npi = $npi;
	}


	function getContent(){

		$npi = mysql_real_escape_string($this->npi);

$patient_sql = "
SELECT 
CONCAT(person.first_name, ' ', person.last_name) as name,
patientuser.key 
FROM `clinician_patient_access` as cpa
JOIN person ON cpa.patient_id = person.person_id 
JOIN patientuser ON cpa.patient_id = patientuser.id 
WHERE `npi` = $npi
";		



		$result = mysql_query($patient_sql) or die(mysql_error());
		$patients = array();
                while($row = mysql_fetch_array( $result )){
			$patients[$row['key']] = $row['name'];	
		}



		$return_me = 
			"
      <div id='MyPatients' dojoType='dijit.layout.ContentPane'
           title='My Patients' ><br />
<div style='width: 95%;   margin-left: auto ; margin-right: auto '>
";


		foreach($patients as $key => $name){		
			$return_me .= "<a href='plans.page.php?key=$key'>$name</a><br />";

		}

		$return_me .= " </div>     </div>";

	return($return_me);


	}
}


?>
