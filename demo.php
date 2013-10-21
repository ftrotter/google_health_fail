<?php

require_once('mysql.php');

$demo = $GLOBALS['demo'];
if(!$demo){//then these secrets should not get out!!!
	echo "<html><head></head><html><h1>this page works only on the demo system</h1></html>";
	exit();
}


require_once('head.php');



$base_url = 'https://qhr.synseer.net/';

$head = new HTMLHeader();
$head->addTitle('Google H9 Demo Start Page');
$head->setTheme();
echo $head->getHeader();

echo "<h1> Demo page: Access several patients QHR BTG Links </h1><br /><br />";

$sql = "
SELECT * FROM patientuser
";

$result = mysql_query($sql) or die("could not load patientuser SQL".mysql_error());

while($row = mysql_fetch_array($result)){
	$key = $row['key'];
	$google_account = $row['google_account'];
	$url = "$base_url"."index.php?key=$key";
	echo "access $google_account at <a href='$url'> at $url</a><br />";
}

echo $head->getFooter();





?>
