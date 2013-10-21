<?php

	$db = $GLOBALS['db_link'];

$tm=time();
if(isset($_SERVER['HTTP_REFERER'])){
	$ref=$_SERVER['HTTP_REFERER'];
}else{
	$ref='none';
}
$agent=$_SERVER['HTTP_USER_AGENT'];
$ip=$_SERVER['REMOTE_ADDR'];
$action = $_SERVER['SCRIPT_NAME'];

if(isset($_SESSION['key'])){
	$key = $_SESSION['key'];
}else{
	$key = 0;
}


if(isset($_SESSION['user_id'])){
	$user_id = $_SESSION['user_id'];
}else{
	$user_id = 0;
}


if(isset($_SESSION['user_email'])){
	$user_email = $_SESSION['user_email'];
}else{
	$user_email = 0;
}


$sql = "
INSERT INTO `track` (
`id` ,
`time` ,
`referer` ,
`agent` ,
`ip` ,
`ip_value` ,
`domain` ,
`action` ,
`key_attempted`,
`user_id`,
`user_email`
)
VALUES (
NULL , '$tm', '$ref', '$agent', '$ip', '', '', '$action', '$key','$user_id','$user_email'
);

 ";



mysql_query($sql,$db);



?>
