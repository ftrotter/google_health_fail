<?php

require_once('Zend/Soap/AutoDiscover.php');
require_once('Zend/Soap/Server.php');


class equipment {

/**
 * Lists the doctors byfunction
 * @param string equipmentname
 * @return array
 * 
*/
	public function listequipment($equipment_name){

	$my_results = array(
			'1231231' => 'Oxegen Tank', 
			'3453434' => 'Crutches', 
			'5656756' => 'Electric Wheelchair', 
			'7897897' => 'Seeing Eye Dog', 
			);
		

	return($my_results);



	}	


}
$options = array();

if(isset($_GET['wsdl'])){

	$autodiscover = new Zend_Soap_AutoDiscover();

        $autodiscover->setOperationBodyStyle(
                    array('use' => 'literal',
                          'namespace' => 'http://qhrdev.healthquilt.org')
                );

        $autodiscover->setBindingStyle(
                    array('style' => 'document',
                          'transport' => 'http://schemas.xmlsoap.org/soap/http')
                );



	$autodiscover->setClass('equipment');
	$autodiscover->handle();

}else{

	$server = new Zend_Soap_Server('http://qhrdev.healthquilt.org/ccrimport/equipment.php?wsdl=true');
	$server->setClass('equipment');
	$server->setObject(new equipment());
	$server->handle();
}





?>
