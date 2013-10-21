<?php

if(isset($_SESSION['patient_id'])){

	$patient_id = $_SESSION['patient_id'];


	$myJavascript = 
"
  <script type='text/javascript'>

     function editIntervention(cell_id){
		dijit.byId('intervention_form').show();
		dojo.byId('planny_id_intervention').value=cell_id;
		dijit.byId('intervention').setValue(dojo.byId(cell_id).innerHTML);
	}

       function editGoal(cell_id){
		dijit.byId('goal_form').show();
		dojo.byId('planny_id_goal').value=cell_id;
		dijit.byId('goal').setValue(dojo.byId(cell_id).innerHTML);
  
	}

       function editOwner(cell_id){
		dijit.byId('owner_form').show();
		dojo.byId('planny_id_owner').value=cell_id;
  
	}

       function editRisk(cell_id){
		dijit.byId('risk_form').show();
		dojo.byId('planny_id_risk').value=cell_id;
  
	}


        function submitForm(my_data,my_form) {

		json_data = dojo.toJson(my_data);

	//	alert(' I get here '+json_data);

                dojo.xhrPost ({
                        // The page that parses the POST request
                        url: 'post.php',
               
                        // Name of the Form we want to submit
                        //form: 'intervention',
              		//is not actually needed since you have the content coming in :)


			content: { message: json_data},
 
                        // Loads this function if everything went ok
                        //load: function (data) {
                                // Put the data into the appropriate div
                        //        dojo.byId('post_response').innerHTML = data;
                        //},
                        // Call this function if an error happened
                        error: function (error) {
                                console.error ('Error: ', error);
				}
            });

		setTimeout('window.location.reload()',1250);

        }
 

	function addPlanRow(planName, patient_id, code_system, code_value, protocol_id){

		 if (patient_id === undefined) {
			    patient_id = '$patient_id';
   		 }
		if (code_system === undefined) {
		   code_system = 1;
		}

		if (code_value === undefined) {
		   code_value = 0;
		}

		if (protocol_id === undefined) {
		   protocol_id = 0;
		}



       		dojo.xhrPost ({
                        // The page that parses the POST request
                        url: 'newplan.php',
               
                        // Name of the Form we want to submit
                        //form: 'intervention',
              		//is not actually needed since you have the content coming in :)


			content: { 
				planName: planName,
				code_system: code_system,
				patient_id: patient_id,
				code_value: code_value, 
				protocol_id: protocol_id 				

				},
 
                        // Loads this function if everything went ok
                        //load: function (data) {
                                // Put the data into the appropriate div
                        //        dojo.byId('post_response').innerHTML = data;
                        //},
                        // Call this function if an error happened
                        error: function (error) {
                                console.error ('Error: ', error);
				}
            });

		setTimeout('window.location.reload()',550);




	/*	var myTbody = dojo.byId('plans');
		var PlanRow = document.createElement('tr');

		var activeCell = document.createElement('td');
		activeCell.innerHTML = 'active';
		var planCell = document.createElement('td');
		planCell.innerHTML = planName;
		var riskCell = document.createElement('td');
		var interCell = document.createElement('td');
		var goalCell = document.createElement('td');
		var medsCell = document.createElement('td');
		var ownerCell = document.createElement('td');
		
		PlanRow.appendChild(activeCell);
		PlanRow.appendChild(planCell);
		PlanRow.appendChild(riskCell);
		PlanRow.appendChild(interCell);
		PlanRow.appendChild(goalCell);
		PlanRow.appendChild(medsCell);
		PlanRow.appendChild(ownerCell);
		myTbody.appendChild(PlanRow);
	*/	
	}

dojo.subscribe('/dnd/drop', function(evnt){
target = dojo.dnd.manager().target.node;

myTargetId = target.id;

source = dojo.dnd.manager().nodes[0];

mySourceId = source.id;
mySource = dojo.byId(mySourceId);
mySourceValue = mySource.textContent;
//for(var i in mySource){
   //  console.log('key', i, 'value', mySource[i]);
//}

var to_post = [
	{'target_id': myTargetId, 'source_id': mySourceId, 'source_value': mySourceValue}
];
	dojo.xhrPost ({
                        // The page that parses the POST request
                        url: 'newitem.php',
               
                        // Name of the Form we want to submit
                        //form: 'intervention',
              		//is not actually needed since you have the content coming in :)


			content: { target_id: myTargetId, source_id: mySourceId, source_value: mySourceValue },
 
                        // Loads this function if everything went ok
                        load: function (data) {
                                // Put the data into the appropriate div
                                dojo.byId('post_response').innerHTML = data;
                        },
                        // Call this function if an error happened
                        error: function (error) {
                                console.error ('Error: ', error);
				}
            });

		setTimeout('window.location.reload()',1250);

//alert('Dragged '+mySourceId+' to '+myTargetId);




/*	id = evnt['anchor'].id;
	alert(id);
// the only way to figure any of the dojo event stuff is to watch this!!
for(var i in evnt){
     console.log('key', i, 'value', evnt[i]);
}
*/
;
});

function findPos(obj) {
	var curtop = 0;
	if (obj.offsetParent) {
		do {
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	return [curtop];
	}
}




	</script>


";
}

$myCSS = "	
	<link rel='stylesheet' type='text/css' href='$qhr_home"."qhr.css' />
	<link rel='stylesheet' type='text/css' href='$qhr_home"."general.css' />
	<link rel='stylesheet' type='text/css' href='$qhr_home"."yui/build/fonts/fonts-min.css' />
  	<link rel='stylesheet' type='text/css' href='$qhr_home"."yui/build/autocomplete/assets/skins/sam/autocomplete.css' />
	<style type='text/css'>
		@import '$qhr_home"."dijit/themes/tundra/tundra.css';
		@import '$qhr_home"."dojox/grid/resources/Grid.css';
		@import '$qhr_home"."dojox/grid/resources/tundraGrid.css';
		@import '$qhr_home"."dojo/resources/dojo.css';
	</style>

  

";




?>
