<?php

$user = 'qhr';
$pass = 'password';
$host = 'localhost';
$db = 'ajaxqhr';


$GLOBALS['db_link'] = mysql_connect($host, $user, $pass) or die(mysql_error());
mysql_select_db($db, $GLOBALS['db_link']) or die(mysql_error());



?>
