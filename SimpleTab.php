<?php
require_once('Tab.class.php');

class SimpleTab extends Tab{

	var $id;
	var $myName;
	var $myContent;


	function getID(){
		return $this->id;
	}

	function getTitle(){
		return($this->myName);
	}


	function __construct($label,$content){
		$clean_label =  preg_replace('/[^a-z0-9]/is', '', $label);
		$this->id = str_replace(" ","_",strtolower($clean_label));
		$this->myName = $label;
		$this->myContent = $content;
	}


	function getContent(){
		return($this->myContent);
	}
}


?>
