<?php

	class HTMLHeader{

	private $css_array = array();
	private $javascript_array = array();
	private $body_tag = "<body>";
	private $title = "<title></title>";
	//private $html_tag = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	private $html_tag = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
	//this should be auto-calced
	var $qhr_home = "https://qhr.synseer.net/";
	var $logo = "https://qhr.synseer.net/qhr_logo.jpg";
	var $logo_alt_text = "Your Doctor Program";
 
/*	private $html_tag = 
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">';
*/

	function __construct(){

//		$this->addToggleJS();
		$this->addDojoJS();
	}




	function setTheme($theme_name = 'hq_tundra'){

		if($theme_name == 'hq_tundra'){

			$qhr_home = $this->qhr_home;
      			require_once('cssandjs.php');
			$this->addCSS($myCSS);
	//		if(isset($myJavascript)){
	//			$this->addJS($myJavascript);
	//		}

			$body = "<body class='tundra'> \n\n<div>";


			$this->setBodyTag($body);
		}
	}

	function setLogo($image = 0,$alt = 0){

		if($image == 0){
			$image = $this->logo;
		}

		if($alt == 0){
			$alt = $this->logo_alt_text;
		}

		$current_body = $this->body_tag;
		$new_body = $current_body . "<div> <img src='$image' alt='$alt' /> </div>";			
		$this->setBodyTag($new_body);	
	}



	function setTopRight($content){

		$current_body = $this->body_tag;

		$new_body = $current_body .= "\n <div style='position: absolute; top: 0; right: 0; width: 100px;'> $content   </div> " ;
	
		$this->setBodyTag($new_body);	

	}

	function addDojoJS(){
	$this->javascript_array[] = 
"
	<script type='text/javascript' src='js/util.js'></script>
	<script type='text/javascript' src='dojo/dojo.js' djConfig='parseOnLoad: true'></script>
	<script type='text/javascript'>
		dojo.require('dijit.dijit');


		dojo.require('dijit.Declaration');
		dojo.require('dijit.form.Button');
		dojo.require('dijit.Menu');
		dojo.require('dijit.Tree');
		dojo.require('dijit.Tooltip');
		dojo.require('dijit.Dialog');
		dojo.require('dijit.Toolbar');
		dojo.require('dijit._Calendar');
		dojo.require('dijit.Editor');
		dojo.require('dijit.ProgressBar');

		dojo.require('dijit.form.ComboBox');
		dojo.require('dijit.form.FilteringSelect');
		dojo.require('dijit.form.Textarea');

		dojo.require('dijit.layout.ContentPane');

		dojo.require('dojo.data.ItemFileWriteStore');
		dojo.require('dojox.grid.DataGrid');
		dojo.require('dijit.form.Textarea');
       		dojo.require('dijit.form.Button');
       		dojo.require('dijit.Dialog');
       		dojo.require('dijit.Editor');
       		dojo.require('dijit.form.TextBox');
       		dojo.require('dijit.layout.TabContainer');
       		dojo.require('dijit.form.CheckBox');
       		dojo.require('dijit.form.FilteringSelect');
		dojo.require('dojo.dnd.Source');
		dojo.require('dijit.layout.AccordionContainer');
		dojo.require('dijit.layout.BorderContainer');
       		dojo.require('dojo.parser');


	</script>";

$yui_stuff = "
  <script type='text/javascript' src='yui/build/yahoo/yahoo-min.js'></script>
  <script type='text/javascript' src='yui/build/utilities/utilities.js'></script>
  <script type='text/javascript' src='yui/build/yahoo-dom-event/yahoo-dom-event.js'></script>
  <script type='text/javascript' src='yui/build/connection/connection-min.js'></script>
  <script type='text/javascript' src='yui/build/animation/animation-min.js'></script>  
  <script type='text/javascript' src='yui/build/datasource/datasource-min.js'></script>
  <script type='text/javascript' src='yui/build/autocomplete/autocomplete-min.js'></script>
  
";




	}






/*
// moved this to /js/util.js for better browser caching
	function addToggleJS(){


		$this->javascript_array[] = 
"
<script type='text/javascript'>
function toggle(obj) {
		var el = document.getElementById(obj);
		if ( el.style.display != 'none' ) {
			el.style.display = 'none';
		}
		else {
			el.style.display = '';
		}
		return false;
	}
function turn_on(obj) {
		var el = document.getElementById(obj);
		el.style.display = '';

	}
function turn_off(obj) {
		var el = document.getElementById(obj);
		el.style.display = 'none';
	}

</script>
";




	}

*/



	function addJS($js){

		$this->javascript_array[] = $js;

	}

	function addCSS($css){

		$this->css_array[] = $css;

	}

	function addTitle($title){

		$this->title = "<title>$title</title>";

	}

	function setBodyTag($new_tag){

		$this->body_tag = $new_tag;

	}
	function getHeader(){

		$return_me = $this->html_tag ."<head>\n";

		$return_me .= $this->title."\n";

		foreach($this->css_array as $css){
			$return_me .= "$css\n";
		}

		foreach($this->javascript_array as $js){
			$return_me .= "$js\n";
		}



		$return_me .= "</head> " . $this->body_tag; 

		return($return_me);
	}

	function getFooter(){

		return("</body></html>");
	}

	}

?>
