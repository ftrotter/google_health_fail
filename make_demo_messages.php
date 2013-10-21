<?php
	require_once('doctrine_config.php');


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


?>
