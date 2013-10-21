<?php

require_once('Zend/Soap/AutoDiscover.php');
require_once('Zend/Soap/Server.php');


class doc {

/**
 * Lists the doctors byfunction
 * @param string first_name
 * @param string last_name
 * @param string state
 * @param string city
 * @return array
 * 
*/
	public function listdocs($first_name, $last_name, $state, $city){

	$my_results = array(
			'12312312312' => 'John Smith, Internist, 3312 Smith st, Houston, TX', 
			'34534345345' => 'Jane Doe, Psychiatrist, 4414 Crazy st, Houston, TX', 
			'56567567567' => 'Ted Jones, Onocologist, 2465 Bundy st, Houston, TX', 
			'78978978978' => 'Kim Dunn, Internist, 1001 Dunn st, Houston, TX', 
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





	$autodiscover->setClass('doc');

	$autodiscover->handle();

}else{

	$server = new Zend_Soap_Server('http://qhrdev.healthquilt.org/ccrimport/docs.php?wsdl=true');
	$server->setClass('doc');
	$server->setObject(new doc());
	$server->handle();
}





?>
