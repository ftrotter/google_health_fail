<?php
require_once('mysql.php');
require_once('config.php');
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_Health');
Zend_Loader::loadClass('Zend_Gdata_Health_Query');


if(isset($argc)){
	//then we are being run command line...
        // lets test
	$testGH = new GoogleHealth('fred.trotter@gmail.com');
	$testGH->send("This is a test from GoogleHealth.php command line",'a command line test');
	$testGH->updateCCR();
	echo $testGH->CCRText;
	
}

class GoogleHealth{

	var $useH9 = true;
	// connection parameters
	var $privatekey = '/etc/pki/tls/private/qhr.synseer.net.key';
	var $appName = APPVERSION; //from config.php

	//H9 specific
    	var $authSubHandler = 'https://www.google.com/h9/authsub';
	var $scope = 'https://www.google.com/h9/feeds/';
	var $replyURL = 'https://qhr.synseer.net/';
	var $imageURL = 'https://qhr.synseer.net/qhr_logo.jpg';
	var $replyText = 'YourDoctorProgram QHR';



	// internal objects
	var $healthService;
	var $client;
	var $CCRText;

	//account information
	var $googleAccount;
	var $sessionToken;
	var $patientID;


function __construct($google_account, $sessionToken = NULL){
	if(!defined('HEALTH_PRIVATE_KEY')){
		define('HEALTH_PRIVATE_KEY', $this->privatekey);
	}
	if(!defined('SCOPE')){
		define('SCOPE', $this->scope);
	}
	if(strlen($google_account) < strlen('1@gmail.com')){
		// 1@gmail.com is pretty much the smallest account we would allow
		echo "ERROR: this email is way to short: $google_account";
		exit(1);
	}


	if(isset($sessionToken)){
		// then we need to save the session token to the database for this account

		$update_sql = "UPDATE `patientuser` SET 
`google_auth` = '$sessionToken'
 WHERE `google_account` = '$google_account' ";

		mysql_query($update_sql) or 
			die("ERROR failed to update the session token in the database ... trying to execute $update_sql error was:".mysql_error());

		
	}


		$session_sql = "
SELECT `id`, `google_auth`, `key` FROM patientuser WHERE google_account = '$google_account'
";

		$result = mysql_query($session_sql) or die("GoogleHealth.php Error getting sessionToken:".mysql_error());
        	$row = mysql_fetch_array( $result );
        	$sessionToken = $row['google_auth'];
		$patient_id = $row['id'];
		//one way or another at this point we should have a valid Google Account and a sessionToken!!
		$this->sessionToken = $sessionToken;
		$this->googleAccount = $google_account;
		$this->patientID = $patient_id;

	try {
  		// Setup the HTTP client and fetch an AuthSub token for H9
  		$this->client = new Zend_Gdata_HttpClient(SCOPE);
  		$this->client->setAuthSubPrivateKeyFile(HEALTH_PRIVATE_KEY, null, true);
  		$this->client->setAuthSubToken($sessionToken);
  		$this->healthService = new Zend_Gdata_Health($this->client, $this->appName, $this->useH9);
		} catch(Zend_Gdata_App_Exception $e) {
  			echo 'GoogleHealth.php: Error Connecting to Google Health with Zend Class Error: ' . $e->getMessage();
			exit();
		}

}// end __construct

function send($html,$subject){

	try {

		$html = "$html <br><br><br><br><b> You can reply to this message through <a href='$this->replyURL'>$this->replyText</a> </b>" .
		"<br><img src='$this->imageURL'><br>";
	


    		$responseEntry = $this->healthService->sendHealthNotice($subject, $html, 'html');

	} catch(Zend_Gdata_App_Exception $e) {
		echo "Trying to Send subject $subject html $html<br>";
		echo "to $this->googleAccount<br>";
  		echo 'GoogleHealth.php: Error Sending Message ' . $e->getMessage();
		exit();
	}
}


function updateCCR(){


	try{


		$query = new Zend_Gdata_Health_Query();
    		$query->setDigest("true");
       	 	$profileFeed = $this->healthService->getHealthProfileFeed($query);
        	$entries = $profileFeed->getEntries();
		
		if(isset($entries[0])){ 		
			$ccr = $entries[0]->getCcr();
		}else{
			$email = $this->googleAccount;
			echo "CCR.php ERROR: no Google CCR entries for this patient id = $patient_id";
			echo "<br /> This usually happens when a patient has not actually created a Google Health record";
			echo "<br /> you need to email this patient at <a href='mailto:$email'>$email</a> and ask them to actually create a record";
			exit();
		
		}
        	$xmlStr = $ccr->saveXML($ccr);
		$ccr_save_sql = "
INSERT INTO `ccrrecords` (
`id` ,
`patient_id` ,
`xml` ,
`source` ,
`date` ,
`note`
)
VALUES (
'', '$this->patientID', '$xmlStr', '1', CURRENT_TIMESTAMP, '');
";

		mysql_query($ccr_save_sql);

		$this->CCRText = $xmlStr;
	}
	        catch(Zend_Gdata_App_Exception $e) {
			echo '<div class="error_message">'."\n";
	  		echo 'Error Accessing Google Data for this patient: ' . $e->getMessage();
			echo "<div>";
		$this->CCRtext = '';
	}








}



function getTokenInfo($client) {
  $sessionToken = $client->getAuthSubToken();
  return Zend_Gdata_AuthSub::getAuthSubTokenInfo($sessionToken, $client);
}

function revokeToken($client) {
  $sessionToken = $client->getAuthSubToken();
  return Zend_Gdata_AuthSub::AuthSubRevokeToken($sessionToken, $client);
}

/** Prettifies an XML string into a human-readable and indented work of art
 *  @param string $xml The XML as a string
 *  @param boolean $html_output True if the output should be escaped (for use in HTML)
 */
function xmlpp($xml, $html_output=true) {
  $xml_obj = new SimpleXMLElement($xml);
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


}// end Google Health Class
?>
