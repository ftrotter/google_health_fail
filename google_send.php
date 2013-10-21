<?php
define('HEALTH_PRIVATE_KEY', '/var/www/keys/myrsakey.pem');


// Load the Zend Gdata classes.
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_Health');
Zend_Loader::loadClass('Zend_Gdata_Health_Query');

session_start();

// Google H9 Sandbox AuthSub/OAuth scope
define('SCOPE', 'https://www.google.com/h9/feeds/');

$filename = '/tmp/h9session.txt';
$fh = fopen($filename,'r');
$token = fgets($fh);

$_SESSION['sessionToken'] = $token;

try {
  // Setup the HTTP client and fetch an AuthSub token for H9
  $client = authenticate();
  $useH9 = true;
  $healthService = new Zend_Gdata_Health($client, 'google-HealthPHPSample-v1.0', $useH9);
} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

if(isset($_POST['html_message'])){

$html = $_POST['html_message'];
$subject = $_POST['subject'];
try {

	$html = "$html <br><br><br><br><b> You can reply to this message through <a href='https://phi.synseer.net/qhr/reply.php'>HealthQuilt</a> </b>" .
		"<br><img src='https://phi.synseer.net/qhr/qhr_logo.jpg'><br>";
	


    $responseEntry = $healthService->sendHealthNotice($subject, $html, 'html');

} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}


header('refresh: 3; url="https://phi.synseer.net/qhr/"');


echo "Message Sent!!";

//echo '<div class="data"><pre>';
//echo xmlpp($responseEntry->getXML());
//echo '</pre></div>';

}else{
// gather html to send

echo "<h1> Error, I was not called with the right arguments </h1>";
exit();

}

// =============================================================================
// Revoke the AuthSub session token
// =============================================================================
//$revoked = Zend_Gdata_AuthSub::AuthSubRevokeToken($client->getAuthSubToken(), $client) ? 'yes' : 'no';
//echo '<b>Token revoked</b>: ' . @$revoked;
//unset($_SESSION['sessionToken']);
?>
</body>
</html>

<?php
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
    echo '<a href="' . $authSubURL . '">Link your Google Health Account</a>';
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
?>
