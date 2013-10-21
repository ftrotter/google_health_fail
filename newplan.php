<?php


	if(isset($_POST['planName'])){

		require_once('mysql.php');

		$new_plan = $_POST['planName'];
		$patient_id = $_POST['patient_id'];
		$code_system = $_POST['code_system'];
		$code_value = $_POST['code_value'];
		$protocol_id = $_POST['protocol_id'];

		$sql = 
"
INSERT INTO `ajaxqhr`.`plan` (
`id` ,
`patient_id` ,
`protocol_id` ,
`name` ,
`active`
)
VALUES (
NULL , '$patient_id', '$protocol_id', '$new_plan', '1'
);
";


$result = mysql_query($sql)
or die(mysql_error());  


	}else{
		echo "Nothing \$_POST";
	}




?>
