<?php

//TODO Convert this program to use Doctrine
require_once('config.php');

define('HEALTH_PRIVATE_KEY', '/etc/pki/tls/private/qhr.synseer.net.key');
// do not change this
// unless you also change it in GoogleHealth.php


// Load the Zend Gdata classes.
require_once 'Zend/Loader.php';
require_once 'register_form.php';
require_once 'head.php';
require_once 'mysql.php';
require_once 'config.php';

$result = mysql_query('SELECT id FROM `sequences`');
$row = mysql_fetch_array($result);
$sequence = $row['id'];

$head = new HTMLHeader();
$head->setTheme();
$head->addTitle('Patient Signup Sheet');
echo $head->getHeader();

Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_Health');
Zend_Loader::loadClass('Zend_Gdata_Health_Query');

session_start();

// Google H9 Sandbox AuthSub/OAuth scope
define('SCOPE', 'https://www.google.com/h9/feeds/');

if(isset($_GET['token'])){
	try {
	  // Setup the HTTP client and fetch an AuthSub token for H9
	  $client = authenticate(@$_GET['token']);
	  $useH9 = true;
	  $healthService = new Zend_Gdata_Health($client, APPVERSION, $useH9);

	  //if healthservice is set... then we just need to display the key here!!!

	$token = $_SESSION['sessionToken'];
	$id = $_SESSION['id'];
	$key = genRandomString(15);

	$collision = true;
	while($collision){

		$sql = " SELECT count( * ) AS count
FROM `patientuser`
WHERE `key` = '$key' ";
		$result = mysql_query($sql) or die("Error checking for previous keys<br /> $sql <br />".mysql_error() );
		$row = mysql_fetch_array($result);
		if($row['count'] > 0){// then it is already in the db and we have to choose another!!
			$collision = true; //
			$key = genRandomString(15);
		}else{
			$collision = false;
		}
	}


	echo "<h2> You have been added to the Break the Glass system</h2>";
	echo "your magic url is $self_url"."index.php?key=$key<br />";
	echo "You can print this out and put it in your wallet... and there you go....";

	$sql = "UPDATE `patientuser` SET `google_auth` = '$token',

`key` = '$key' WHERE `patientuser`.`id` = $id";


	mysql_query($sql) or die("Failed to add google token and phi key <br /> $sql <br /> ". mysql_error());


	} catch(Zend_Gdata_App_Exception $e) {
	  echo 'Error: ' . $e->getMessage();
	}
}else{
	// display our registration form... 







	if(isset($_POST['register'])){


		$me = $_POST['me'];
		//This means my form has come back
		$comma = '';
		$me_sql = 'INSERT INTO `person` ( ';
		$me_values = ") VALUES ( ";
		foreach($me as $field => $value){
			$me_sql .= "$comma `$field` ";
			$me_values .= "$comma '$value'";
			$comma = ", ";
		}

		$sql = $me_sql . $me_values . ")";
		mysql_query($sql) or die("Could not add person <br /> $sql <br /> ". mysql_error()); 

		$me_id = mysql_insert_id();

                $em = $_POST['em'];
                //This means my form has come back
                $comma = '';
                $em_sql = 'INSERT INTO `person` ( ';
                $em_values = ") VALUES ( ";
                foreach($em as $field => $value){
                        $em_sql .= "$comma `$field` ";
                        $em_values .= "$comma '$value'";
			$comma = ", ";
                }

                $sql = $em_sql . $em_values . ")";
                mysql_query($sql) or die("Could not add emergency person <br /> $sql <br /> ". mysql_error()); 

		$em_id = mysql_insert_id();
	

                $me_address = $_POST['me_address'];
                //This means my form has come back
                $comma = '';
                $me_sql = 'INSERT INTO `address` ( `address_id` ,';
                $me_values = ") VALUES ( '$sequence' ,";
                foreach($me_address as $field => $value){
                        $me_sql .= "$comma `$field` ";
                        $me_values .= "$comma '$value'";
			$comma = ", ";
                }

                $sql = $me_sql . $me_values . ")";
                mysql_query($sql) or die("Could not add address <br /> $sql <br />". mysql_error());

                $address_id = $sequence;
                $sequence++;

		$sql = "INSERT INTO `person_address` (
`person_id` ,
`address_id` ,
`address_type`
)
VALUES (
'$me_id', '$address_id', '1'
);";


                mysql_query($sql) or die("Could not add address to person <br /> $sql <br />". mysql_error());


                $em_address = $_POST['em_address'];
                //This means my form has come back
                $comma = '';
                $em_sql = 'INSERT INTO `address` ( `address_id` ,';
                $em_values = ") VALUES ( '$sequence' ,";
                foreach($em_address as $field => $value){
                        $em_sql .= "$comma `$field` ";
                        $em_values .= "$comma '$value'";
			$comma = ", ";
                }

                $sql = $em_sql . $em_values . ")";
                mysql_query($sql) or die("Could not add address <br /> $sql <br />". mysql_error());

                $em_address_id = $sequence;
                $sequence++;

                $sql = "INSERT INTO `person_address` (
`person_id` ,
`address_id` ,
`address_type`
)
VALUES (
'$em_id', '$em_address_id', '1'
);";


                mysql_query($sql) or die("Could not add emergency address to person <br /> $sql <br />". mysql_error());



	


		//needs to be better
		$phone[1] = $_POST['me_home_phone'];
		$phone[2] = $_POST['me_work_phone'];
		$phone[3] = $_POST['me_cell_phone'];
	
		$em_phone[1] = $_POST['em_home_phone'];
		$em_phone[2] = $_POST['em_work_phone'];
		$em_phone[3] = $_POST['em_cell_phone'];
	
		foreach($phone as $type => $number){
			$sql = "INSERT INTO `number` (
`number_id` ,
`number_type` ,
`notes` ,
`number` ,
`active`
)
VALUES (
'$sequence', '$type', '', '$number', '1'
);";

                mysql_query($sql) or die("Could not add phone <br /> $sql <br />". mysql_error()); 

$sql = "INSERT INTO `person_number` (
`person_id` ,
`number_id`
)
VALUES (
'$me_id', '$sequence'
);";

                mysql_query($sql) or die("Could not add phone <br /> $sql <br />". mysql_error()); 

	$sequence++;

		}


                foreach($em_phone as $type => $number){
                        $sql = "INSERT INTO `number` (
`number_id` ,   
`number_type` ,
`notes` ,
`number` ,
`active`
)
VALUES (
'$sequence', '$type', '', '$number', '1'
);";

                mysql_query($sql) or die("Could not add phone <br /> $sql <br />". mysql_error());            

$sql = "INSERT INTO `person_number` (
`person_id` ,
`number_id`
)               
VALUES (
'$em_id', '$sequence'
);";

                mysql_query($sql) or die("Could not add phone <br /> $sql <br />". mysql_error());

        $sequence++;

                }




		$sequence_update_sql = "UPDATE `sequences` SET `id` = '$sequence' WHERE 1 ;";
		mysql_query($sequence_update_sql) or die("could not update sequence <br /> $sql <br />  " . mysql_error());

		$account = $_POST['me_email'];

		$google_user_sql = "INSERT INTO `patientuser` (
`id` ,
`google_account` ,
`google_auth` ,
`key`
)
VALUES (
'$me_id', '$account', '', ''
);";
		mysql_query($google_user_sql) or die("could not add patientuser <br /> $sql <br />  " . mysql_error());
		

		$_SESSION['id'] = $me_id;
		$_SESSION['account'] = $account;

		authenticate();
		

	}else{

		register_form();
		echo $head->getFooter();
	}
} // end of else

// =============================================================================
// Revoke the AuthSub session token
// =============================================================================
//$revoked = Zend_Gdata_AuthSub::AuthSubRevokeToken($client->getAuthSubToken(), $client) ? 'yes' : 'no';
//echo '<b>Token revoked</b>: ' . @$revoked;
//unset($_SESSION['sessionToken']);

function genRandomString($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';    

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }

    return $string;
}




function getCurrentUrl() {
  $phpRequestUri =
    htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

  if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
    $protocol = 'https://';
  } else {
    $protocol = 'http://';
  }
  $host = $_SERVER['HTTP_HOST'];
  if ($_SERVER['SERVER_PORT'] != '' &&
     (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
     ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
    $port = ':' . $_SERVER['SERVER_PORT'];
  } else {
    $port = '';
  }
  return $protocol . $host . $port . $phpRequestUri;
}

function authenticate($singleUseToken=null) {
  $sessionToken = isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : null;


  // If there is no AuthSub session or one-time token waiting for us,
  // redirect the user to Google Health's AuthSub handler to get one.
  if (!$sessionToken && !$singleUseToken) {
    $next = getCurrentUrl();
    $secure = 1;
    $session = 1;
    $authSubHandler = 'https://www.google.com/h9/authsub';
    $permission = 1;  // 1 - allows reading of the profile && posting notices
    $authSubURL =
      Zend_Gdata_AuthSub::getAuthSubTokenUri($next, SCOPE, $secure, $session,
                                             $authSubHandler);
    $authSubURL .= '&permission=' . $permission;	
    header("Location: $authSubURL");
    exit();
  }

  $client = new Zend_Gdata_HttpClient();
  $client->setAuthSubPrivateKeyFile(HEALTH_PRIVATE_KEY, null, true);

  // Convert an AuthSub one-time token into a session token if needed
  if ($singleUseToken && !$sessionToken) {
    $sessionToken =
      Zend_Gdata_AuthSub::getAuthSubSessionToken($singleUseToken, $client);
    $_SESSION['sessionToken'] = $sessionToken;
  }
  $client->setAuthSubToken($sessionToken);
  return $client;
}

function getTokenInfo($client) {
  $sessionToken = $client->getAuthSubToken();
  return Zend_Gdata_AuthSub::getAuthSubTokenInfo($sessionToken, $client);
}

function revokeToken($client) {
  $sessionToken = $client->getAuthSubToken();
  return Zend_Gdata_AuthSub::AuthSubRevokeToken($sessionToken, $client);
}

?>
