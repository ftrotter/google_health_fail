<?php


	class Plans{

		var $plans;
		var $patient_id;

		function __construct($patient_id){

			//load plans from db here...
			$this->patient_id = $patient_id;
		//	$this->_demo_data();
			$this->_mysql_data();

		}


		function _mysql_data(){

		require_once('mysql.php');



$plans_sql = "SELECT plan.id as plan_id, plan.protocol_id , plan.name , plan.active, 
plan_data.id, plan_data.value, plan_data.data, plan_data.created
FROM `plan`
LEFT JOIN plan_data ON plan_data.plan_id = plan.id
WHERE `patient_id` = ". $this->patient_id ."
ORDER BY plan_data.created ";

//This gets the whole history of the plan
//but the while loop below has the effect of creating an assocarray of only the most recent entries!!

$result = mysql_query($plans_sql)
or die(mysql_error());  
$plan_values_array = array();
$this->plans = array();
while($row = mysql_fetch_array( $result )){

	if(strcmp($row['value'],'risk') == 0 || strcmp($row['value'],'meds') == 0){
		if(isset($plan_values_array[$row['plan_id']][$row['value']])){
		$plan_values_array[$row['plan_id']][$row['value']] .= $row['data']."<br />";
		}else{

		$plan_values_array[$row['plan_id']][$row['value']] = $row['data']."<br />";
		}
	}else{
	$plan_values_array[$row['plan_id']][$row['value']] = $row['data'];
	}
	$plan_values_array[$row['plan_id']]['name']= $row['name'];
	$plan_values_array[$row['plan_id']]['active']= $row['active'];
	$plan_values_array[$row['plan_id']]['protocol_id']= $row['protocol_id'];

}
//$results_count = $row['count'];



foreach($plan_values_array as $plan_id => $plan_array){

	foreach($plan_array as $key => $value){
		$this->plans[$plan_id][$key] = $value;
	}
}
	krsort($this->plans);
	//var_export($this->plans);
	

		}


		function _demo_data(){

			$this->plans[1231] = array(
					'patient_id' => 1,
					'protocol_id' => 0,
					'name' => 'Neck Pain',
					'active' => 'active',
					'risk_factors' => 'Smoking <br /> Depression <br /> Obesity <br /> Family History',
					'intervention' => 'Intervention Text: o.dnd, is designed to manage the process of dragging items between two or more containers, including multiple selection, item acceptance filtering on drop targets, and other behavioral tweaks. The second API, dojo.dnd.move, is a bit lower-level in scope; it’s designed to manage the process of moving sin',
					'goal' => 'Goal Text: is that Dojo actually has two drag and drop APIs. The first, dojo.dnd, is designed to manage the process of dragging items between two or more containers, including multiple selection, e objects around, without the concept of attaching items t item acceptance filtering on drop targets, and other behavioral tweaks. The second',
					'owner' => 'Dr. Richard Jacobson <br />(713)234-2345',
		
		);
			


		}
	
		function getPlans(){

		$return_me = "
<table id='plans_table' class='grid' style='width: 90%; margin-left: 5%; margin-right: 5%;'>
<tbody id='plans'>
	<tr>
		<th>Active</th>
		<th>Plan Name</th>
		<th>Risk Factors</th>
		<th>Interventions</th>
		<th>Goals</th>
		<th>Medications</th>
		<th>Plan Owner</th>	
	</tr>

";
		$count = 0;
		foreach($this->plans as $plan_id => $plan_array){
		$count++;
		$risk_factors = '<br /><br /><br /><br />';$goal='';$intervention='';$name='';$active='';$meds='';
		foreach($plan_array as $key => $data){
			$$key = $data; // created variables
			if(strpos($key,'rf_') !== false){
				$risk_array = split('_',$key);
				unset($risk_array[0]);// gets ride of the 'rf'
				$risk_factors .= ucwords(implode(" ",$risk_array)) . "<br />";
				
			}
			if(strpos($key,'risk') !== false){
				$risk_factors .= "$data";
				
			}
			if(strpos($key,'meds') !== false){
				$meds .= "$data";
				
			}


		}

		if($active == 1){$active = 'Active';}else{$active='Inactive';}
		
		if(isset($owner_phone)){
			$owner = "$owner_last, $owner_first <br /> $owner_phone";
		}else{
			$owner = '';
		}


		if($count%2 == 0){ 
			$class = '';
		}else{
			$class = " class='alt' ";
		}

		$return_me .= "
	<tr$class>
		<td rowspan='2'>
			$active</td>
		<td rowspan='2'>
			$name</td>
		<td height='50%' id='risk_$plan_id' onclick=\"editRisk('risk_$plan_id')\">
			$risk_factors</td>
		<td rowspan='2' id='intervention_$plan_id' onclick=\"editIntervention('intervention_$plan_id')\">
			$intervention</td>
		<td rowspan='2' id='goal_$plan_id' onclick=\"editGoal('goal_$plan_id')\">
			$goal</td>
		<td rowspan='2' dojoType='dojo.dnd.Target' accept='Meds' id='meds_$plan_id' class='meddrop_target'>
		$meds
		</td>
		<td rowspan='2' id='owner_$plan_id' onclick=\"editOwner('owner_$plan_id')\">
			$owner</td>
	</tr>
	<tr> <td dojoType='dojo.dnd.Target' id='risk_$plan_id' accept='General,Meds' class='drop_target' > 
			&nbsp;<br />&nbsp;<br />&nbsp;<br /> </td> </tr>


";
		foreach($plan_array as $key => $data){ $$key = '';}
		}// row loop

	$return_me .= "</tbody></table>";

	return($return_me);

	}






}



?>
