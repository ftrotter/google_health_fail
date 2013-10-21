<?php
	session_start();

if(isset($_SESSION['patient_login'])){
        //patient world
        require_once('check_Google.php');
	$user_id = $_SESSION['patient_id'];
}else{
        //clinical world
        require_once('check_SAML.php');
	require_once('ClinicalAccess.php');
	$ca = new ClinicalAccess();
	$user_id = $ca->id;
}




	//if we are here then we are validly logged in!!
	require_once('functions.php');
	require_once('mysql.php');
	require_once('config.php');
	require_once('doctrine_config.php');


	$return_url = $_SERVER['HTTP_REFERER'];
	$bounce = false;
	if($GLOBALS['debug']){
	echo "If you are not a developer, then you can blissfully assume the operation below was a success and then <br>";
	echo "you can go back to <a href='$return_url'>$return_url</a><br> Thank your stars that you are not obsessing about this stuff -> <br><br><br>";
	
	var_export($_POST);
	}else{
		$bounce = true;
	}

	// they will either be npi_123121232 or user_123212
	$to_string = $_POST['to'];
	list($to_npi, $to_user) = split("_",$to_string);

	if(isset($_POST['thread_id'])){
		//this is a continued thread!!
	
		$tbl_thread = Doctrine::getTable('Threads');
		$thread = $tbl_thread->find($_POST['thread_id']);
		
		
	}else{
		//this is a new message and thread!!


	        $thread = new Threads();
	        $thread->thread_name = $_POST['subject'];
        	$thread['patient_id'] = $_POST['patient_id'];
	        $thread['is_todo'] = 0;
       		$thread['is_done'] = 0;
        	$thread->save();
	}


        $message = new Messages();
        $message->threads_thread_id = $thread;
        $message['priority'] = 1;
        $message['content'] = $_POST['content'];
	$message['created'] = date( 'Y-m-d H:i:s');
 
        $message->save();


		// there are two ways to send a message
		// I can either send it to a known user
		// or I can send it to an NPI
		
		
	if($to_user == 0){
		//then its an npi only it needs to be sent to every 
		// user with that npi
		
		$users = Doctrine_Query::create()->
				from('Clinicaluser cu')->
				where("cu.npi = $to_npi")->
				fetchArray();

		echo "<br> Matching NPI users <br>";

		var_export($users);

		if(count($users) == 0){

			echo "<br>Creating envelope with no user_id<br>";

			//then there are no current users to message
			//we need to use envelopes without a user id!!		
		   	$envelope = new Envelopes();
		      	$envelope->message_id = $message;
		       	$envelope['to_person'] = 0;
		       	$envelope['to_npi'] = $to_npi;
		       	$envelope['from_person'] = $_POST['from'];
			$envelope['when_sent'] = date( 'Y-m-d H:i:s');
		       	$envelope->save();

		}else{
			echo "<br>At least one user_id, looping over them<br>";

			foreach($users as $user){

		   	     	$envelope = new Envelopes();
		       	 	$envelope->message_id = $message;
			       	$envelope['to_person'] = $user['id'];
			       	$envelope['to_npi'] = $to_npi;
			       	$envelope['from_person'] = $_POST['from'];
				$envelope['when_sent'] = date( 'Y-m-d H:i:s');
			       	$envelope->save();
			}
		}


	}else{
		echo "<br>Being used to send a message to a single clinical user $to_user<br>";

		$envelope = new Envelopes();
	        $envelope->message_id = $message;
	        $envelope['to_person'] = $to_user;
	        $envelope['to_npi'] = $to_npi;
	        $envelope['from_person'] = $_POST['from'];
	        $envelope->save();

	}







/*

        $thread = new Threads();
        $thread->thread_name = 'This is a test of the Doctrine System';
        $thread['patient_id'] = 200;
        $thread['is_todo'] = 0;
        $thread['is_done'] = 0;
        $thread->save();


        $message = new Messages();
        $message->threads_thread_id = $thread;
        $message['priority'] = 1;
        $message['content'] = 'This is a test of the Doctrine System. <br> This message has <b>some html markup</b> Just <i>enough</i> to be <h3> Dangerous</h3>. Lets hope no one knows how to add a <a href="http://www.google.com">link</a>';
        $message->save();



        $envelope = new Envelopes();
        $envelope->message_id = $message;
        $envelope['to_person'] = 2;
        $envelope['from_person'] = 200;
        $envelope->save();




*/

	if($bounce){
		header("Location: $return_url");
	}




?>
