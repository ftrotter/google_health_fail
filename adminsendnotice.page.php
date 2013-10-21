<?php
	session_start();

      	if(isset($_SESSION['key'])){
                $key = $_SESSION['key'];
        }
        if(isset($_GET['key'])){
                $key = $_GET['key'];
                $_SESSION['key'] = $key;
        }

	require_once('check_SAML.php');

	//if we are here then we are validly logged in!!

	

	require_once('functions.php');
	require_once('head.php');
	require_once('menu.php');
	require_once('ClinicalAccess.php');
	require_once('email_config.php');
	require_once('phpmailer/class.phpmailer.php');
	require_once('SOAP/Client.php');
	require_once('thread.php');
	require_once('fax_config.php');

        $patient_id = patient_id_from_key($key);
        $_SESSION['patient_id'] = $patient_id;
        //echo "key = $key and patient_id = $patient_id <br>";


	$head = new HTMLHeader();
	$menu = new menu();

	$clinicalUser = $GLOBALS['clinicalAccess'];
	$isAccessRevoked = $clinicalUser->isRevoked($patient_id);
	$user_id = $clinicalUser->id;

	$head->setTheme();
	echo $head->getHeader();
	echo $menu->getMenu();



	//require_once('thread.php');

	$not_seen_sql = "
SELECT 
envelopes.to_npi,
envelopes.to_person,
envelopes.from_person,
messages.content,
messages.threads_thread_id as thread_id,
seen.seen,
seen.seen_when,
clinicaluser.username,
clinicaluser.email,
quicksearch.first_name as npi_first ,
quicksearch.last_name as npi_last,
quicksearch.fax,
quicksearch.address,
quicksearch.city,
quicksearch.state,
to_person.first_name as to_first,
to_person.last_name as to_last,
from_person.first_name as from_first,
from_person.last_name as from_last
FROM `envelopes` 
JOIN messages ON envelopes.message_id = messages.message_id
JOIN qnpi.quicksearch on envelopes.to_npi = qnpi.quicksearch.npi
LEFT JOIN seen on envelopes.message_id = seen.message_id
LEFT JOIN clinicaluser on envelopes.to_npi = clinicaluser.npi
LEFT JOIN person as from_person ON envelopes.from_person = from_person.person_id
LEFT JOIN person as to_person ON envelopes.to_person = to_person.person_id
WHERE seen.seen is NULL
";
	
	$result = mysql_query($not_seen_sql) or die("Could not load not seen sql<br>$not_seen_sql". mysql_error());

	$people_to_email = array();
	$people_to_fax = array();
	$people_to_mail = array();
	


	while($row = mysql_fetch_array( $result )){

			if(isset($row['email'])){
				// since we do not send clinical content over email, it we can merge the notice for many unseen messages into one.
				// so we overwrite the keys in people_to_email to ensure one email per username
				$people_to_email[$row['username']] = $row;
			}else{
				//we need each unseen message  thread enumerated, so that we can send whole
				//threads by mail and fax
				if(strcmp($row['fax'],'none') == 0){	
					$people_to_mail[$row['thread_id']] = $row;
				}else{
					$people_to_fax[$row['thread_id']] = $row;
				}
			}
	}


if(count($people_to_email) >0){

	echo "<h1> Unseen Messages that we can email notices for </h1>\n";
	echo "<table>\n";
	echo "<tr>
<th>NPI</th>
<th>NPI First</th>
<th>NPI Last</th>
<th>username</th>
<th>User First</th>
<th>User Last</th>
<th>email</th>
</tr>";

	foreach($people_to_email as $email_row){

		echo "<tr>\n";
			echo "<td> ". $email_row['to_npi'] . "</td>";
			echo "<td> ". $email_row['npi_first'] . "</td>";
			echo "<td> ". $email_row['npi_last'] . "</td>";
			echo "<td> ". $email_row['username'] . "</td>";
			echo "<td> ". $email_row['to_first'] . "</td>";
			echo "<td> ". $email_row['to_last'] . "</td>";
			echo "<td> ". $email_row['email'] . "</td>";
		echo "</tr>\n";

	}
	echo "</table>\n";
}


if(count($people_to_fax) >0){

	echo "<h1> Unseen Messages that we have to send faxes for!! </h1>\n";
	echo "<table>\n";
	echo "<tr>
<th>NPI</th>
<th>NPI First</th>
<th>NPI Last</th>
<th>fax number</th>
</tr>";

	foreach($people_to_fax as $fax_row){

		echo "<tr>\n";
			echo "<td> ". $fax_row['to_npi'] . "</td>";
			echo "<td> ". $fax_row['npi_first'] . "</td>";
			echo "<td> ". $fax_row['npi_last'] . "</td>";
			echo "<td> ". $fax_row['fax'] . "</td>";
		echo "</tr>\n";

	}
	echo "</table>\n";
}


if(count($people_to_mail) >0){

	echo "<h1> Unseen Messages that we have to send postcards for!! </h1>\n";
	echo "<table>\n";
	echo "<tr>
<th>NPI</th>
<th>NPI First</th>
<th>NPI Last</th>
<th>Address</th>
<th>City</th>
<th>State</th>
</tr>";

	foreach($people_to_mail as $mail_row){

		echo "<tr>\n";
			echo "<td> ". $mail_row['to_npi'] . "</td>";
			echo "<td> ". $mail_row['npi_first'] . "</td>";
			echo "<td> ". $mail_row['npi_last'] . "</td>";
			echo "<td> ". $mail_row['address'] . "</td>";
			echo "<td> ". $mail_row['city'] . "</td>";
			echo "<td> ". $mail_row['state'] . "</td>";
		echo "</tr>\n";

	}
	echo "</table>\n";

}



$google_sql = "
SELECT 
messages.message_id,
messages.content,
threads.thread_name as subject,
threads.patient_id,
patientuser.google_account,
patientuser.google_auth,
patientuser.key,
notices_google.notice_sent
FROM `messages` 
JOIN threads ON threads.thread_id = messages.threads_thread_id
JOIN patientuser ON patientuser.id = threads.patient_id
LEFT JOIN notices_google ON notices_google.message_id = messages.message_id 
WHERE notices_google.notice_sent is NULL
";

	$google_people = array();
	$result = mysql_query($google_sql) or die("Could not load google sql<br>$google_sql". mysql_error());
	while($row = mysql_fetch_array( $result )){
		if(isset($google_people[$row['google_account']])){
			$google_people[$row['google_account']]++;
		}else{
			$google_people[$row['google_account']] = 1;
		}	
		$google_messages[$row['google_account']][$row['message_id']] = $row;	
	
	}
	echo "<h1> Messages that have not been sent into Google Health </h1>";
	echo "<table><tr><th>Google Account</th><th>Messages to send</th>    </tr>";

		
	foreach($google_people as $account => $count){
		echo "<tr><td>$account</td><td>$count</td></tr>";
	}
	echo "</table>";

	
	echo "<br><br><br><a href='adminsendnotice.page.php?send=email'>Send Email Notices</a><br>";
	echo "<a href='adminsendnotice.page.php?send=fax'>Send Fax Notices</a><br>";
	echo "<a href='adminsendnotice.page.php?send=postcard'>Send Postcard Notices</a><br>";
	echo "<a href='adminsendnotice.page.php?send=googlehealth'>Send Patient Google Health Notices</a><br>";
//	echo "main url = $main_url instance_name = $instance_name<br>";

	if(isset($_GET['send'])){

		echo "Attempting send!!";
		$count = 0;	
	
		$send = $_GET['send'];	

	if(strcmp($send,'email')==0){
		foreach($people_to_email as $email_row){	
			$count++; 
			
			if($count < 1){ // TODO this just makes things move faster for development. It needs to come out.	
			$first = $email_row['to_first'];
			$last = $email_row['to_last'];

			$mail = new PHPMailer();
    			$mail->From     = $from;
        		$mail->FromName = $from_name;
        		$mail->WordWrap = 50;                              // set word wrap
        		$mail->IsHTML(true);                               // send as HTML
        		$mail->Subject  =  $subject . " for $first $last";

        		$mail->AddAddress(
					$email_row['email'],
		//			"fred.trotter@gmail.com",
					"$first $last ".$email_row['email']);

        		if($cc){ //it is either an email address... or false...
                		$mail->AddAddress($cc 
				,"CCed For $first $last");
			}
	        	$mail->Body     = "You have a new clinical message  <a href='$main_url'>here</a>";
        		$mail->AltBody  = "You have a new clinical message here -> $main_url";

	        	if(!$mail->Send())
        		{
                		echo "Message was not sent <p>";
                		echo "Mailer Error: " . $mail->ErrorInfo;
                		echo "<br> Perhaps there is a problem with your mail setup. </p>";
        		}else{
				echo "sent to ".$email_row['email']."<br>";
			}



			}//end count limitation...
		}

		}// email section


		if(strcmp($send,'fax')==0){


		foreach($people_to_fax as $fax_row){

		$fax = $fax_row['fax'];


		$string = "123456890123456789112345678921234567893123456789412345678951234567896123456789712345678981234567899";

		$fax_message = "<h1>$string</h1>";
		$fax_message .= "<h2>$string</h2>";
		$fax_message .= "<h3>$string</h3>";
		$fax_message .= "<h4>$string</h4>";
		$fax_message .= "<table border=1>
			<tr><td>$string</td></tr>
			<tr><td>$string</td></tr>
			<tr><td>$string</td></tr>
			<tr><td>$string</td></tr>
			<tr><td>$string</td></tr>
			</table>";
		$fax_message = $fax_header;	
		$fax_message .= get_thread_content($fax_row['thread_id'],0,true);
		$fax_message .= "<br><br><br><br><br>$fax_footer";

		$fax_message = wordwrap($fax_message,70,"<br>");

		//echo "attempting to fax <br> $fax_message";

		$wsdl_url = 
        	"http://ws.interfax.net/dfs.asmx?wsdl";
		$WSDL     = new SOAP_WSDL($wsdl_url); 
		$client   = $WSDL->getProxy(); 
		$client->setOpt('timeout',360);


		$fax = '8153667913';

		$fax_id    = $client->sendCharFax(
       	 		'ftrotter', //username
        		'password', //password
        		$fax,
			$fax_message,
			'HTML'
		);


		}
		}//end fax section


		if(strcmp($send,'postcard')==0){
			// postcard section
			echo "postcard not yet supported";
		}

		if(strcmp($send,'googlehealth')==0){
			//Google Health section. 
	
		require_once('GoogleHealth.php');

			//require Google Health libraries

	$tofrom_sql = "
SELECT 
messages.message_id, 
messages.content, 
threads.thread_name as subject,
from_person.person_id as from_id,
from_person.first_name as from_first,
from_person.last_name as from_last,
to_person.person_id as to_id,
to_person.first_name as to_first,
to_person.last_name as to_last,
qnpi.quicksearch.npi,
qnpi.quicksearch.first_name as npi_first,
qnpi.quicksearch.last_name as npi_last
FROM `messages` 
JOIN threads on messages.threads_thread_id = threads.thread_id
LEFT JOIN notices_google ON notices_google.message_id = messages.message_id 
JOIN envelopes ON envelopes.message_id = messages.message_id
LEFT JOIN person as to_person ON envelopes.to_person = to_person.person_id
LEFT JOIN person as from_person ON envelopes.from_person = from_person.person_id
LEFT JOIN qnpi.quicksearch ON envelopes.to_npi = qnpi.quicksearch.npi
WHERE notices_google.notice_sent is NULL
";
	$tofrom = array();
	$result = mysql_query($tofrom_sql) or die("Could not load google sql<br>$google_sql". mysql_error());
	while($row = mysql_fetch_array( $result )){
		$tofrom[$row['message_id']]['from'] = $row['from_first']. " " . $row['from_last'];
		if(isset($row['to_first'])){
			$tofrom[$row['message_id']]['to'] = $row['to_first']. " " . $row['to_last'];
		}else{
			$tofrom[$row['message_id']]['to'] = $row['npi_first']. " " . $row['npi_last'];

		}
		$tofrom[$row['message_id']]['content'] = $row['content'];
		$tofrom[$row['message_id']]['subject'] = $row['subject'];

 
	}


			foreach($google_messages as $google_account => $messages){	
				foreach($messages as $message_id => $message_row){
				if(isset($tofrom[$message_id])){	
					$message = 
				"Subject: ".$tofrom[$message_id]['subject'] ."<br>".
				"From: ".$tofrom[$message_id]['from'] ."<br>".	
				"To: ".$tofrom[$message_id]['to'] ."<br>".
				"Message: <br> ".$tofrom[$message_id]['content'] ."<br>";
				
				$subject = $tofrom[$message_id]['subject']." (Clinical Message)";


		//		if(strcmp($google_account,'fred.trotter@gmail.com') == 0){
					echo "trying to send to GoogleHealth for $google_account <br>";	
					$GH = new GoogleHealth($google_account);
					$GH->send($message,$subject);
		//		}
				}else{// end if isset message_id
					//this should not happen, how are we getting 
					// message ids without data in the tofrom array?
					echo "Error: message_ids without data in the tofrom array";
				}
				}
			
				// so we have sent this into 
				// the Google Health Account!!
				// we need to either send the messages, or at least send 
				// notices to the email account!!				



                        $mail = new PHPMailer();
                        $mail->From     = $from; // set in email_config.php
                        $mail->FromName = $from_name; // set in email_config.php
                        $mail->WordWrap = 50;         // set word wrap
                        $mail->IsHTML(true);         // send as HTML
                        $mail->Subject  =  "New QHR Message";

                        $mail->AddAddress(
                                        $google_account,
                //                      "fred.trotter@gmail.com",
                                        $google_account);

                        if($cc){ //it is either an email address... or false...
                                $mail->AddAddress($cc
                                ,"CCed For $first $last");
                        }
                        $mail->Body     = "You have a new clinical message  <a href='$patient_message_url'>here</a>";
                        $mail->AltBody  = "You have a new clinical message here -> $patient_message_url";

                        if(!$mail->Send())
                        {
                                echo "Message was not sent <p>";
                                echo "Mailer Error: " . $mail->ErrorInfo;
                                echo "<br> Perhaps there is a problem with your mail setup. </p>";
                        }else{
                                echo "sent to $google_account<br>";
                        }






			}// looping over messages
		}

	}else{
		echo "not sending this time";
	}








	echo $head->getFooter();


?>
