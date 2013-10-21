<?php
require_once('Zend/Soap/Client.php');


	echo "hitting <a href='http://qhrdev.healthquilt.org/ccrimport/docs.php?wsdl=true'> http://qhrdev.healthquilt.org/ccrimport/docs.php?wsdl=true </a> <br>";
	$client = new Zend_Soap_Client('http://qhrdev.healthquilt.org/ccrimport/docs.php?wsdl=true');
	var_export($client->listdocs('Fred','Trotter','Houston','TX'));

echo "<br>";
echo "<br>";
	echo "hitting <a href='http://qhrdev.healthquilt.org/ccrimport/meds.php?wsdl=true'> http://qhrdev.healthquilt.org/ccrimport/meds.php?wsdl=true </a><br>";
	$client = new Zend_Soap_Client('http://qhrdev.healthquilt.org/ccrimport/meds.php?wsdl=true');
	var_export($client->listmeds('asprin'));

echo "<br>";
echo "<br>";
	echo "hitting <a href='http://qhrdev.healthquilt.org/ccrimport/equipment.php?wsdl=true'>http://qhrdev.healthquilt.org/ccrimport/equipment.php?wsdl=true </a><br>";

	$client = new Zend_Soap_Client('http://qhrdev.healthquilt.org/ccrimport/equipment.php?wsdl=true');
	var_export($client->listequipment('test'));

echo "<br>";
echo "<br>";
	echo "hitting <a href='http://qhrdev.healthquilt.org/ccrimport/problems.php?wsdl=true'>http://qhrdev.healthquilt.org/ccrimport/problems.php?wsdl=true </a><br>";

	$client = new Zend_Soap_Client('http://qhrdev.healthquilt.org/ccrimport/problems.php?wsdl=true');
	var_export($client->listproblems('test'));

echo "<br>";
echo "<br>";
	echo "hitting <a href='http://qhrdev.healthquilt.org/ccrimport/allergies.php?wsdl=true'> http://qhrdev.healthquilt.org/ccrimport/allergies.php?wsdl=true </a><br>";

	$client = new Zend_Soap_Client('http://qhrdev.healthquilt.org/ccrimport/allergies.php?wsdl=true');
	var_export($client->listallergies('test'));

?>
