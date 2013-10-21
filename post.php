<?php


	if(isset($_POST['message'])){

		require_once('mysql.php');

		$JSONmessage = $_POST['message'];
		$message_array = json_decode($JSONmessage,true);
		foreach($message_array as $key => $value){
	
			$pos = strpos($key,'planny_id');
	
			if($pos===false){
				$plan_id = 0;
			}else{
				$ignore_key = $key;
				list($throw_away,$plan_id) = split("_",$value);
			}

			if(is_array($value)){
				$real_value = array_pop($value);
				if(is_null($real_value)){
					unset($message_array[$key]);
				}else{
					$message_array[$key] = $real_value; 
				}
			}

		}

		var_export($message_array);

		foreach($message_array as $key => $value){

			if($key == $ignore_key){
				continue;
			}


		$sql = 
"
INSERT INTO `plan_data` (
`id` ,
`plan_id` ,
`value` ,
`data` ,
`codeset` ,
`code` ,
`xml_id` ,
`ccr_id` ,
`provider_id` ,
`created`
)
VALUES (
NULL , '$plan_id', '$key', '$value', '', '', '', '', '',
CURRENT_TIMESTAMP
);
";

	echo $sql;

$result = mysql_query($sql)
or die(mysql_error());  

//$row = mysql_fetch_array( $result );
//$results_count = $row['count'];

		}


	}else{
		echo "Nothing \$_POST";
	}




?>
