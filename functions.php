<?php


	function patient_id_from_key($key){
		require_once('mysql.php');
		$key = mysql_real_escape_string($key);
                        $patient_id_sql = "
SELECT *
FROM `patientuser`
WHERE `key` = '$key'
";
                        $result = mysql_query($patient_id_sql) 
				or die("functions.php error getting key".mysql_error());
                        $row = mysql_fetch_array( $result );

                        return($row['id']);
	}


        function patient_id_from_googleaccount($account){
                require_once('mysql.php');
                $account = mysql_real_escape_string($account);
                        $patient_account_sql = "
SELECT *
FROM `patientuser`
WHERE `google_account` = '$account'
";
                        $result = mysql_query($patient_account_sql)
                                or die("functions.php error getting key".mysql_error());
                        $row = mysql_fetch_array( $result );

                        return($row['id']);
        }




	function patient_row_from_id($id){
		require_once('mysql.php');
		$id = mysql_real_escape_string($id);
                        $patient_id_sql = "
SELECT *
FROM `person`
WHERE `person_id` = '$id'
";
                        $result = mysql_query($patient_id_sql) 
				or die("functions.php error getting key".mysql_error());
                        $row = mysql_fetch_array( $result );

                        return($row);
	}



	function pretty_date($sql_date)
	{

	$time_stamp = strtotime($sql_date);
	$today_midnight = mktime(0, 0, 0, date("m") , date("d") , date("Y"));



	if($time_stamp > $today_midnight){
		//date is today just display the time
		$date_string = date( 'g:i a',$time_stamp);
	}else{// before today
		$jan_first = mktime(0,0,0,1,1,date("Y"));	
		if($time_stamp > $jan_first){
			$date_string = date('M j',$time_stamp);
		
		}else{ // last years dates
		
			$date_string = date('F j, Y',$time_stamp);
		}
	}

	return $date_string;
	}





















	function getorpost($term){

		if(isset($_GET[$term])){
			return $_GET[$term];
		}

		if(isset($_POST[$term])){
			return $_POST[$term];
		}
		
		if(isset($_SESSION[$term])){	
			return $_SESSION[$term];
		}
		return false;		

	}


function insertRow($table_name, $input_array){
	$link = mysql_connect(SERVER, USER, PASS);
	mysql_select_db(DATABASE, $link);
	$SQL = "INSERT INTO $table_name ";
	$fields = "(";
	$values = "(";
	foreach ($input_array as $k => $v) {
		$fields .= "`$k` ,";
		$values .= "'$v' ,";
	}
	$fields .="#";
	$values .="#";
	$fields = explode(",#", $fields);
	$fields = $fields[0];
	$values = explode(",#", $values);
	$values = $values[0];
	$fields .= ")";
	$values .= ")";
	$SQL .= $fields . " VALUES " . $values;
	mysql_query($SQL);
	mysql_close($link);
}



?>
