<?php


	if(isset($_POST['source_id'])){

		require_once('mysql.php');

		$source_id = $_POST['source_id'];
		$target_id = $_POST['target_id'];
		$source_value = trim($_POST['source_value']);
		var_export($_POST);


		list( $cell, $plan_id) = split('_',$target_id);

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
NULL , '$plan_id', '$cell', '$source_value', 'none', '$source_id', '', '', '',
CURRENT_TIMESTAMP
);
";

	echo $sql;

$result = mysql_query($sql)
or die(mysql_error());  

//$row = mysql_fetch_array( $result );
//$results_count = $row['count'];


	}else{
		echo "Nothing \$_POST";
	}




?>
