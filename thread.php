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


	require_once('mysql.php');

	if(isset($_GET['thread_id'])){

	//if we are here then we are validly logged in!!
	require_once('functions.php');


		//then this called directly otherwise, we just want access to the function
	
	$print_view = false;

	$thread_id = $_GET['thread_id'];
	if(isset($_GET['print_view'])){
		$print_view = true;
	}else{
		$print_view = false;
	}


	$content = get_thread_content($thread_id,$user_id,$print_view);

	echo $content;
	}

	function get_thread_content($thread_id,$user_id,$print_view){

			$display_none = "style='display: none'";
			$display_show = "";


			$thread_sql = "
						SELECT 
						COUNT(envelopes.envelope_id) as number_of_recipients,
						envelopes.envelope_id AS envelope_id,
						envelopes.to_person,
						envelopes.from_person,
						envelopes.message_id,
						envelopes.when_sent,
						messages.message_id,
						messages.threads_thread_id,
						messages.priority,
						messages.created,
						threads.is_todo,
						threads.is_done,
						messages.content,
						threads.thread_id,
						threads.thread_name as subject,
						threads.patient_id as patient_id,
						CONCAT(to_person.last_name, \" \", to_person.first_name) as to_field,
						CONCAT(from_person.last_name, \" \", from_person.first_name) as from_field,
						from_person.last_name AS from_last,
						seen.person_id,
						seen.seen, 
						seen.seen_when
			FROM `messages`
			LEFT JOIN envelopes ON envelopes.message_id = messages.message_id
			LEFT JOIN threads ON messages.threads_thread_id = threads.thread_id
			LEFT JOIN person AS to_person ON to_person.person_id = envelopes.to_person
			LEFT JOIN person AS from_person ON from_person.person_id = envelopes.from_person
			LEFT JOIN seen ON  messages.message_id = seen.message_id 
			WHERE threads.thread_id = $thread_id 
			GROUP by envelopes.message_id
			ORDER by envelopes.envelope_id ASC
			";

	$result = mysql_query($thread_sql) or die("thread.php: SQL error<br>$thread_sql<br><br>".mysql_error());

	//$return_me = "<br> $thread_sql <br>";
	$return_me = "";
	$i = 0;// for numbering the divs

	$mark_as_seen = array();
	while($row = mysql_fetch_array( $result )){
		$i++;

		$print_date = pretty_date($row['when_sent']);
		$long_message = $row['content'];
		$subject = $row['subject'];
		$patient_id = $row['patient_id'];
		$short_message = eregi_replace("&nbsp;", " ", strip_tags($long_message));
		$short_message = substr($short_message,0,70);

		$from = $row['from_field'];
		$to = $row['to_field'];

		$seen = $row['seen'];
		$seen_when = $row['seen_when'];

		if(strlen($seen) == 0){
			$seen_status = false;
			$summary_div_show = $display_none;
			$long_div_show = $display_show;
			$mark_as_seen[] = $row['message_id'];
		}else{
			$seen_status = true;
			$summary_div_show = $display_show;
			$long_div_show = $display_none;
		}

//TODO This where you can implement the SEEN functionality
//That determines which div will be set to display none 
//and which one will be set to display on...
//It should be based on whether the user has read that message
//but to do that you must have the SQL above be SEEN aware, 
//and re-implement the functionality to mark messages as SEEN after/with this display of the messages

if(!$print_view){
		$return_me .= "

<div id='$i"."_message_div' class='button' $long_div_show>
$print_date
<a href='#' onclick=\"javascript:turn_on('$i"."_summary_div');turn_off('$i"."_message_div');\">
From: $from To: $to </a> 

<br>
<div style='background-color: white; padding: 10px; border-style: ridge'>
$long_message
</div>
<br>

</div>
<div id='$i"."_summary_div' class='button' $summary_div_show >
$print_date
<a href='#' onclick=\"javascript:turn_on('$i"."_message_div');turn_off('$i"."_summary_div');\">
From: $from To: $to </a> 
$short_message ...
<br>
</div>

";
}else{

$to = str_replace("\r","",$to);
$to = str_replace("\n","",$to);
$from = str_replace("\r","",$from);
$from = str_replace("\n","",$from);

$return_me .= "From: $from  To: $to  Written: $print_date <br>";
$return_me .= "Message:<br> $long_message";

}
	}//while loop




	//this needs to run for faxes too!!
	if($user_id != 0){
	foreach($mark_as_seen as $mark_me){
		$seen_sql = "
INSERT INTO `seen` ( `person_id` , `message_id` , `seen` , `seen_when`)
VALUES ( '$user_id', '$mark_me', '1', NOW( ) )
";
	mysql_query($seen_sql) or die("Unable to Mark as Seen".mysql_error());
	}
	}


	if(!$print_view){
		$return_me .= get_thread_reply($subject,$thread_id,$patient_id,$user_id);
	}else{
		$return_me .= "<br><br> This is added only to the print view ";
	}
	return($return_me);

}


	function get_thread_reply($subject,$thread_id,$patient_id,$from){

	$patient_id = mysql_real_escape_string($patient_id);

$user_list_sql = "
SELECT 
DISTINCT cpa.npi as npi,
qnpi.quicksearch.first_name as npi_first,
qnpi.quicksearch.last_name as npi_last,
qnpi.quicksearch.fax as npi_fax
FROM `clinician_patient_access` as cpa
LEFT JOIN qnpi.quicksearch ON qnpi.quicksearch.npi = cpa.npi
WHERE `patient_id` = $patient_id
AND `revoked` = 0

";

/*
      $user_list_sql = "
SELECT first_name, last_name, npi, clinical_user_id
FROM `clinician_patient_access`
JOIN person ON person.person_id = `clinical_user_id`
WHERE `patient_id` = $patient_id
AND `revoked` = 0
";
*/

        $select = "\n<select id='to' name='to'>\n";
        $result = mysql_query($user_list_sql) 
		or die("Could not get a list of users <br /> $user_list_sql <br />". mysql_error());
	
	$got_one = false;
        while($row = mysql_fetch_array( $result )){
		$got_one = true;
			$first = $row['npi_first'];
			$last  = $row['npi_last'];

                	$select .= '<option value="'.$row['npi'].'_0">'
				."$first, $last "
				.'('.$row['npi'].') '
				."</option>\n";
        }
	$select .= "</select>";

	if(!$got_one){//then we did not get any clinical users associated with this patient
		//TODO change to error div to clearly mark this as an error
		return("<br>There are no doctors associated with this patient so there is no one to send messages to!!, 
				add them in the doctor management screen <br> $user_list_sql");
	}


	$return_me = "
<form action='send.php' method='post'>
        <br />
        <table width='100%'>
                <tr>
                        <td width='30px'>Subject: </td> <td>$subject</td>
		</tr>
		<tr>
                        <td>To: </td> <td>$select</td>
                <tr>
                        <td valign='top'>Message: </td>
                        <td>
                                <textarea id='content' name='content' width='600px'>Type message here!!
                                </textarea>
                        </td>
                </tr>
                <tr>
                        <td>
                        <input type='hidden' name='thread_id' id='thread_id' value='$thread_id'>
                        <input type='hidden' name='patient_id' id='patient_id' value='$patient_id'>
                        <input type='hidden' name='from' id='from' value='$from'>
                        <input type='submit' value='Send' />
                        </td>
                        <td>
                        </td>
                </tr>
        </table>
</form>

";


return($return_me);



}








	function get_random_thread_content($thread_id){
		$message_count = rand(3,15);
		$subject = random_subject();
		$random_number_days_back = rand(1,10);
		$return_me = "<h2> Thread: $subject</h2>";
	for($i=0; $i < $message_count; $i++){
		$random_number_days_back = rand(3,15) + $random_number_days_back;
		$time = strtotime("-$random_number_days_back day");
		$print_date = date("D M j  Y",$time);
		$long_message = random_text();
		$short_message = substr($long_message,0,50);
		$person = random_person();
		$return_me .= "

<div id='$i"."_message_div' class='button' style='display: none' >
$print_date
<a href='#' onclick=\"javascript:turn_on('$i"."_summary_div');turn_off('$i"."_message_div');\">
From: $person </a> 

<br>
<div style='background-color: white; padding: 10px; border-style: ridge'>
$long_message
</div>
<br>

</div>
<div id='$i"."_summary_div' class='button'  >
$print_date
<a href='#' onclick=\"javascript:turn_on('$i"."_message_div');turn_off('$i"."_summary_div');\">
From: $person </a> 
$short_message ...
<br>
</div>

";

	}//for loop over

	return($return_me);

}



function random_subject(){

	$subject_array = array(
	"My Blood Pressure",
	"Your diet",
	"Recent Weight change",
	"My daughters breathing",
	"My stomach pain",
	"which heart medicine",
	"right cholesterol drug",
	"Which counselour",
	"test results",
	"Cariologist retiring",
);

	$number = rand(1,9);
	return($subject_array[$number]);
}




function random_person(){

	$person_array = array(
	"Jane Doe",
	"John Smith",
	"Tammy Baker",
	"Hussien Obama",
	"Sissy Jones",
	"Nancy Netters",
	"Paublo Hussien",
	"Jennifer Dougan",
	"Paul Jones",
	"Michael Rodriguez",
);

	$number = rand(1,9);
	return($person_array[$number]);
}
















function random_text(){

	$text_array = array(
	"Hmmmm...weight loss, rashesl the time itching rash gets worse end up in ER again, cardiologist figures it's the Plavix sends me to allergy doc who thinks it's the Lisinopril. 4 months later still feel lousy and am med sensitive to stuff I have taken for years rashes on neck, tongue burning and swelling feel like crap.ANA comes back positive along with dna Lupus? rheumatologist repeats blood work says no! I'm beginning to think it's the stent any input would be greatly appreciated.",
	"My mother had two stents put in , in December 2008. Since then she has been miserable. She has had increased blood pressure. 200's over 100's. She had dental work done not long after that a now has a burning throughout her entire body including her mouth. Her bp is still very high but for some reason it bottomed out the other night. dropping from 198/100 to 80/42. What the hell is going on with my poor mom. She is getting very depressed and can't take all the doctors visits and no answers.. Somebody please help my mom..Her only daughter Tracey.",
	"I'm extreemly happy with th for them. Me I was totally dumbfounded I didn't know one stent from the other. Had I had the time beforehand to research that I for sure would have but, things don't always work that way. I had no idea I had three arteries blocked. Anyway my bp is normal and my heart rate is good just that darn rash. and I hope all will work out for all of you. Feel free to post here to discuss our problems we face see you later and God Bless",


);



	$number = rand(0,2);

	return($text_array[$number]);
}









?>
