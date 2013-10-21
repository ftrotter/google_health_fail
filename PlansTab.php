<?php
require_once('Tab.class.php');
require_once('Plans.php');

class PlansTab extends Tab{

	var $id = 'Plans';
	var $myTitle;
	var $patient_name;
	var $ccr;
	var $patient_id;

	function getID(){
		return $this->id;
	}

	function getTitle(){
		return($this->myTitle);
	}


	function __construct($patient_id,$ccr){
		$this->patient_id = $patient_id;
		$this->ccr = $ccr;
     		$patient_name = $ccr->getPrintName();
		$this->patient_name = $patient_name;
		$this->myTitle = "Plans for $patient_name";
	}


	function getContent(){

		$return_me = "<h2>CCR Demographic Information</h2>";
		$return_me .=  $this->ccr->getDemographic();
		$return_me .= "<h2>CCR Clinical Elements</h2>";
		$return_me .= $this->ccr->getLists();
		$patient_id = $this->patient_id;

//		$return_me .= "
		$yui_stuff = '';
		$yui_stuff .= "
<div class='yui-skin-sam'>
	<form action='' method='post'>
      		Add a new plan using text: <br />
      		<div style='width:600px'>
        	<input autocomplete='no' id='problem_string' type='text' name='problem_string'/>

        	<div id='ac0'></div>
		<br /><br />
      		</div>
	</form>

</div>
<script type='text/javascript'>                
      YAHOO.example.autocomplete = function() {
        var oConfigs = {
            prehighlightClassName: 'yui-ac-prehighlight',
            queryDelay: 1.0,
            minQueryLength: 4,
            animVert: .01,
	    maxResultsDisplayed: 500
        }
        
        // instantiate remote data source
        // code based on example at 
        // http://developer.yahoo.com/yui/examples/autocomplete/ac_basic_xhr.html
        var oDS = new YAHOO.util.XHRDataSource('problem_string.php'); 
        oDS.responseType = YAHOO.util.XHRDataSource.TYPE_XML; 
        oDS.responseSchema = { 
           resultNode: 'problem', 
           fields: ['display', 'planId', 'name']             
        }; 
        oDS.maxCacheEntries = 500;         
    
        // instantiate YUI autocomplete widgets
        var myAutoComp = new YAHOO.widget.AutoComplete('problem_string', 'ac0', oDS, oConfigs);          

	//define your itemSelect handler function:
var itemSelectHandler = function(sType, aArgs) {
//	console.log(sType); // this is a string representing the event;
				      // e.g., 'itemSelectEvent'
	var oMyAcInstance = aArgs[0]; // your AutoComplete instance
	var elListItem = aArgs[1]; // the <li> element selected in the suggestion
	   					       // container
	var oData = aArgs[2]; // object literal of data for the result
	//console.log(oData);
	var display = oData[0];
	var planId = oData[1];
	var name = oData[2];
	if(planId != 0){
		addPlanRow(name, '$patient_id', 'suggest', planId );
	}else{
             dojo.byId('problem_string').value = '';	
	}
};

//subscribe your handler to the event, assuming
//you have an AutoComplete instance myAC:
myAutoComp.itemSelectEvent.subscribe(itemSelectHandler);


        return {
            oDS: oDS,
            myAutoComp: myAutoComp 
        };
      }();
     </script>

";

         $plans = new Plans($patient_id);
	$return_me .= $plans->getPlans();

	//$return_me .= "</div></div>";


		return($return_me);
	}
}


?>
