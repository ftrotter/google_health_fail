<?php

 	require_once('/var/simplesamlphp_qhr/www/_include.php');
        require_once('SimpleSAML/Utilities.php');
        require_once('SimpleSAML/Session.php');
        require_once('SimpleSAML/XHTML/Template.php');



        /* Load simpleSAMLphp, configuration and metadata */
        $config = SimpleSAML_Configuration::getInstance();
        $session = SimpleSAML_Session::getInstance();

        /* Check if valid local session exists.. */
        if (!isset($session) || !$session->isValid('saml2') ) {
          SimpleSAML_Utilities::redirect(
            '/' . $config->getBaseURL() .
            'saml2/sp/initSSO.php',
            array('RelayState' => SimpleSAML_Utilities::selfURL())
            );
        }

	//if we are here then we are validly logged in!!

?>
