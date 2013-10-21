<?php

require_once 'head.php';
require_once 'mysql.php';
require_once 'config.php';


$head = new HTMLHeader();
$head->setTheme();
$head->addTitle('Patient Splash Page');
echo $head->getHeader();

echo "<h1> Patient Login or Register Page </h1>";
echo "<a href='patientlogin.php'>login</a> to your QHR instance<br> Or you can <a href='signup.php'>Sign Up</a> for a new account.";

echo $head->getFooter();



?>
