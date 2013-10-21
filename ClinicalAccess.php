<?php

	require_once('mysql.php');
        require_once('SimpleSAML/Utilities.php');
        require_once('SimpleSAML/Session.php');
        require_once('SimpleSAML/XHTML/Template.php');
	require_once('doctrine_config.php');

class ClinicalAccess{

	var $id;
	var $username;
	var $email;
	var $npi;
	var $idp;

	function __construct(){	

        		$session = SimpleSAML_Session::getInstance();

			$idp = $session->getIdP();
			$idp = mysql_real_escape_string($idp);

			$attributes = $session->getAttributes();
			$username = $attributes['username'][0];
			$username = mysql_real_escape_string($username);
			$email = $attributes['email'][0];
			$email = mysql_real_escape_string($email);
			$npi = $attributes['npi'][0];
			$npi = mysql_real_escape_string($npi);



                        $clinicaluser_sql = "SELECT * FROM clinicaluser WHERE username = '$username' AND idp = '$idp'";
                        $result = mysql_query($clinicaluser_sql) or die("ClinicalUser.php cannot query for clinical users<br>$clinicaluser_sql".mysql_error());
                        $row = mysql_fetch_array( $result );

			if($row){
				
				//this username and idp combination has been seen before
				$this->id = $row['id'];	

			}else{
				//this username and idp combination is new!! we must create a new record!!
			$person = new Person();
			$person->last_name = $attributes['last_name'][0];
			$person->first_name = $attributes['first_name'][0];
			$person->save();

			
			//Soon we need to setup phone and address with 
			// many to many with person
			// so that we can add an address and phone here.
			// We need to find a way to keep things normalized
			// only create the new address if there is not 
			// something already there w/ identifical information
			// not sure how to make doctrine do that.
	
			$cu = new Clinicaluser();
			$cu->id = $person->person_id;
			$cu->username = $username;
			$cu->email = $email;
			$cu->npi = $npi;
			$cu->idp = $idp;
			$cu->save();
			$this->id = $cu->id;
		//	$person = $cu->Person;
			


/*
//the old way, directly against mysql
				$new_clinical_user_sql = "
INSERT INTO `clinicaluser` (
`id` ,
`username` ,
`email` ,
`npi` ,
`idp`
)
VALUES (
'', '$username', '$email', '$npi', '$idp'
);
";				
				mysql_query($new_clinical_user_sql) 
					or die("ClinicalUser.php failed to create clinical user <br> $new_clinical_user_sql<br>".mysql_error());
				
				$this->id = mysql_insert_id();
*/

				
			}	


				$this->email = $email;	
				$this->npi = $npi;	
				$this->idp = $idp;
				$this->username = $username;	



	}


	function isRevoked($patient_id){
		//this function should test if an NPI has access to a particular patient
		//or if this access has been revoked by the consumer
			$npi = mysql_real_escape_string($this->npi);
			$patient_id = mysql_real_escape_string($patient_id);
			$id = $this->id;

   			$access_sql = "SELECT * FROM clinician_patient_access WHERE npi = '$npi' AND clinical_user_id = '$id' AND patient_id = '$patient_id'";
                        $result = mysql_query($access_sql) or die("ClinicalUser.php cannot query for access rules<br>$access_sql".mysql_error());
                        $row = mysql_fetch_array( $result );

                        if($row){
				// then this access has already been documented!!
				// now we return the rare case that access has been revoked by the patient!!!
				// the row 'revoked' default to t zero for 'not revoked'
				return($row['revoked']);
			}else{
				// then this is the first time for this access... which means that 
				// it is automatically permitted!!
				// but we need to record the access so that the patient
				// has the option to revoke it later...
				$new_access_rule_sql = "
INSERT INTO `clinician_patient_access` 
	(`id`, `npi`, `clinical_user_id`, `patient_id`, `revoked`, `illegal`, `note`) VALUES (NULL, '$npi', '$id',  '$patient_id', '0', '0', '');
";

  				mysql_query($new_access_rule_sql)
                                        or die("ClinicalUser.php failed to create new access rule<br> $new_access_rule_sql<br>".mysql_error());


			}		



	}


	function patientList(){

		// id npi -> patient allow/disallow illegal note 



	}


}


?>
