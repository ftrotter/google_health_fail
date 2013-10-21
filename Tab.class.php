<?php


abstract class Tab{


	var $navigation_target = '_self';
	var $type = 'div';

	// returns the title of the tab
	abstract function getTitle();
	// returns the div id for the tab
	abstract function getID();
	//this is where the instanciating classes determine what goes in the tab!!
	abstract function getContent();

	function asIframe(){
		$this->navigation_target = '_parent';	
		$this->type = 'iframe';	
	}


	function printContent(){

		return($this->getContent());

	}


	function printTab(){

		$title = $this->getTitle();
		$id = $this->getID();

		$return_me = "
	<div id='$id' dojoType='dijit.layout.ContentPane' title='$title' style='padding: 20px'>

";

		$content = $this->getContent();

		$return_me .= $content;

		$return_me .= "

	</div>
";

		return($return_me);

	}
}

/*
// working split pain
		<div dojoType='dijit.layout.BorderContainer' design='sidebar' liveSplitters='false' style=' width: 90%; height: 1000px'>
			<div dojoType='dijit.layout.ContentPane' region='leading' style='width: 20px;'>
			</div>
			<div dojoType='dijit.layout.ContentPane' region='center' style=' padding: 10px;'>
			</div>
		</div>

*/







?>
