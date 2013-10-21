<?php

	


 	require_once('/var/simplesamlphp_qhr/www/_include.php');
        require_once('SimpleSAML/Utilities.php');
        require_once('SimpleSAML/Session.php');
        require_once('SimpleSAML/XHTML/Template.php');

	$config = SimpleSAML_Configuration::getInstance();
     	$session = SimpleSAML_Session::getInstance();

        if (!$session->isValid('saml2')) {
                error('Not logged in.');
        }

        //if ($protocol === 'saml2') { // OPENID how do we do that?
        if (true) {
                $url = '/' . $config->getBaseURL() . 'saml2/sp/initSLO.php';
        } else {
                error('Logout unsupported for protocol "' . $protocol . '".');
        }

        $relayState = '/index.php';


        SimpleSAML_Utilities::redirect(
                $url,
                array('RelayState' => $relayState)
                );






?>
