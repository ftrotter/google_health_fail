<?php
	session_start();

	require_once('check_Google.php');

	//if we are here then we are validly logged in!!

	

	require_once('functions.php');
	require_once('head.php');
	require_once('mysql.php');
	require_once('patient_menu.php');

	$head = new HTMLHeader();
	$menu = new menu();

	$from = $_SESSION['patient_id'];	
	$patient_id = $_SESSION['patient_id'];	

	$head->setTheme();
	echo $head->getHeader();
	echo $menu->getMenu();


	//function get_thread_reply($subject,$thread_id,$patient_id,$from){

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
		$npi=$row['npi'];
		/* If you want to do user_id aware messaging...
		if($row['user_id'] > 0){
			if($row['user_id'] == $from){
				$me = "(me)";
			}else{
				$me = '';
			}


			$first = $row['user_first'];
			$last = $row['user_last'];
			$option = "Specific user $first $last under clincian". $row['npi_first']. " ". $row['npi_last']. "($npi)";
			$user_id = $row['user_id'];
		}else{
		*/
			$me = '';
			$first = $row['npi_first'];
			$last  = $row['npi_last'];
			$option = " $first $last ($npi)";
			$user_id = '0';

                	$select .= "<option value='".$row['npi']."_$user_id'> $option </option>\n";
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
                        <td width='30px'>Subject: </td> <td><input type='text' name='subject' id='subject'>    </td>
		</tr>
		<tr>
                        <td>To: </td><td> $select</td>
		</tr>
                <tr>
                        <td valign='top'>Message: </td>

                        <td>
<textarea name='content' width='200px' dojoType='dijit.Editor'>Enter your message here!!</textarea>


                        </td>
                </tr>
                <tr>
                        <td>
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


echo $return_me;








	echo $head->getFooter();


?>
