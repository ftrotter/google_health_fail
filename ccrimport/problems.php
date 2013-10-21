<?php

require_once('Zend/Soap/AutoDiscover.php');
require_once('Zend/Soap/Server.php');


class problem {

/**
 * Lists the doctors byfunction
 * @param string allergyname
 * @return array
 * 
*/
	public function listproblems($problem_name){

	$my_results = array(
			'1231231' => 'Diabetes', 
			'3453434' => 'Heart failure', 
			'5656756' => 'Ashtma', 
			'7897897' => 'Other', 
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




	$autodiscover->setClass('problem');
	$autodiscover->handle();

}else{

	$server = new Zend_Soap_Server('http://qhrdev.healthquilt.org/ccrimport/problems.php?wsdl=true');
	$server->setClass('problem');
	$server->setObject(new problem());
	$server->handle();
}





?>
