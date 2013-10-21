<?php

require_once('Zend/Soap/AutoDiscover.php');
require_once('Zend/Soap/Server.php');


class allergy {

/**
 * Lists the doctors byfunction
 * @param string allergyname
 * @return array
 * 
*/
	public function listallergies($allergy_name){

	$my_results = array(
			'1231231' => 'Strawberries', 
			'3453434' => 'Penicillin', 
			'5656756' => 'Aspirin', 
			'7897897' => 'Shellfish', 
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



	$autodiscover->setClass('allergy');
	$autodiscover->handle();

}else{

	$server = new Zend_Soap_Server('http://qhrdev.healthquilt.org/ccrimport/allergies.php?wsdl=true');
	$server->setClass('allergy');
	$server->setObject(new allergy());
	$server->handle();
}





?>
