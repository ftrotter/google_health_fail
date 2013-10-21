<?php
		require_once('config.php');


		$from = "qhr@qhr.synseer.net";
                $from_name = "Your Doctor Program";
                $subject  =  "YDP Secure Message";
                $server = $_SERVER['SERVER_NAME'];

                $path_parts = split('/',$_SERVER['REQUEST_URI']);
                $instance_name = $path_parts[0];

                $main_url = "https://$server/";
	
		if($GLOBALS['h9']){	
			$patient_message_url = 'https://www.google.com/h9';
		}else{
			$patient_message_url = 'https://www.google.com/health/';
		}
		$cc = "fred.trotter@gmail.com";
		$cc = false;

?>
