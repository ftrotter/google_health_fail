<?php
        session_start();

if(isset($_SESSION['patient_login'])){
        //patient world
        require_once('check_Google.php');
	$patient_world = true;
}else{
	$patient_world = false;
        //clinical world
        require_once('check_SAML.php');
}

require_once('mysql.php');

if(isset($_GET['user_id'])){
	$user_id = $_GET['user_id'];
}else{
	$user_id = 9;//demo fred trotters account
}


$inbox_sql = "
SELECT 
COUNT( * ) AS message_number,
envelopes.envelope_id AS envelope_id, 
envelopes.to_person, 
envelopes.from_person, 
envelopes.message_id, 
envelopes.when_sent, 
messages.message_id, 
messages.threads_thread_id, 
messages.created, 
messages.content, 
threads.is_todo, 
threads.is_done, 
threads.thread_id, 
threads.thread_name AS subject, 
threads.patient_id,
patientuser.key, 
to_person.first_name AS to_first, 
to_person.last_name AS to_last, 
from_person.first_name AS from_first, 
from_person.last_name AS from_last, 
patient_person.first_name AS patient_first, 
patient_person.last_name AS patient_last, 
seen.seen, 
seen.seen_when
FROM envelopes
LEFT JOIN messages ON envelopes.message_id = messages.message_id
LEFT JOIN threads ON messages.threads_thread_id = threads.thread_id
LEFT JOIN person AS to_person ON to_person.person_id = envelopes.to_person
LEFT JOIN person AS from_person ON from_person.person_id = envelopes.from_person
LEFT JOIN patientuser ON threads.patient_id = patientuser.id
LEFT JOIN person AS patient_person ON threads.patient_id = patient_person.person_id
LEFT JOIN seen ON ( messages.message_id = seen.message_id AND seen.person_id = envelopes.to_person )
WHERE envelopes.from_person = $user_id
GROUP BY messages.threads_thread_id
ORDER BY envelopes.envelope_id DESC 
";

$result = mysql_query($inbox_sql) or die("json_inbox: SQL error<br>$inbox_sql<br><br>".mysql_error());

$inbox_data = array (  'identifier' => 'thread_id',
                        'label' => 'thread_id',
);

$items = array();

while($row = mysql_fetch_array( $result )){

	$thread_javascript_link = "'javascript:jah(\"thread.php?thread_id=".$row['thread_id']."\",\"thread_display\");'";
	$patient_link = "'load.php?key=".$row['key']."'";

	$subject = $row['subject'];
	if(strlen($subject) == 0){
		$subject = '-none-';
	}

        if($patient_world){
                $regarding = $row['patient_first']." ".$row['patient_last'];
        }else{
                $regarding = "<a href=$patient_link> ".$row['patient_first']." ".$row['patient_last']. "</a>";
        }



	$this_thread = array(
		'thread_id' => $row['thread_id'],
		'from' => $row['from_first']. " ".$row['from_last'] ,
		'to' => $row['to_first']. " ".$row['to_last'] ,
		'subject' => "<a href=$thread_javascript_link> $subject </a>",
		'regarding' => $regarding, 
		'task_status' => 'not implemented',
		'total_messages' => $row['message_number'],
		'sent' => $row['when_sent'],
		);

	$items[] = $this_thread;

}

$inbox_data['items']= $items;

$json_data = json_encode($inbox_data);

echo json_format($json_data);


function json_format($json)
{
    $tab = "  ";
    $new_json = "";
    $indent_level = 0;
    $in_string = false;

    $json_obj = json_decode($json);

    if($json_obj === false)
        return false;

    $json = json_encode($json_obj);
    $len = strlen($json);

    for($c = 0; $c < $len; $c++)
    {
        $char = $json[$c];
        switch($char)
        {
            case '{':
            case '[':
                if(!$in_string)
                {
                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                    $indent_level++;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '}':
            case ']':
                if(!$in_string)
                {
                    $indent_level--;
                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ',':
                if(!$in_string)
                {
                    $new_json .= ",\n" . str_repeat($tab, $indent_level);
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case ':':
                if(!$in_string)
                {
                    $new_json .= ": ";
                }
                else
                {
                    $new_json .= $char;
                }
                break;
            case '"':
                if($c > 0 && $json[$c-1] != '\\')
                {
                    $in_string = !$in_string;
                }
            default:
                $new_json .= $char;
                break;                   
        }
    }

    return $new_json;
}





?>
