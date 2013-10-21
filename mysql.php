<?php

$user = 'qhr';
$pass = 'xGe7fstc2KvMnzj2';
$host = 'localhost';
$db = 'ajaxqhr';


$GLOBALS['db_link'] = mysql_connect($host, $user, $pass) or die(mysql_error());
mysql_select_db($db, $GLOBALS['db_link']) or die(mysql_error());

$GLOBALS['demo'] = true;

?>
