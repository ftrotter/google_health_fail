<?php

require_once('Zend/Soap/AutoDiscover.php');
require_once('Zend/Soap/Server.php');


class med {

/**
 * Lists the doctors byfunction
 * @param string medname
 * @return array
 * 
*/
	public function listmeds($med_name){

	$my_results = array(
			'1231231' => 'Asprin', 
			'3453434' => 'Viagra', 
			'5656756' => 'Lipitor', 
			'7897897' => 'Insulin', 
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


	$autodiscover->setClass('med');
	$autodiscover->handle();

}else{

	$server = new Zend_Soap_Server('http://qhrdev.healthquilt.org/ccrimport/meds.php?wsdl=true');
	$server->setClass('med');
	$server->setObject(new med());
	$server->handle();
}





?>
