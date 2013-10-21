<?php
require_once('Tab.class.php');

class ProtocolTab extends Tab{

	var $id = 'protocols';
	var $myName = 'My Protocols';
	var $user_id;

	function getID(){
		return($this->id);
	}

	function getTitle(){
		return($this->myName);
	}

	function __construct($user_id){
		$this->user_id = $user_id;
	}


	function getContent(){


// needs to convert to per user protocol forms.


$list_protocols_sql = "
SELECT *
FROM `ydp_protocol`
";


$result = mysql_query($list_protocols_sql) or die('Could not load protocol names: '.mysql_error());

while($row = mysql_fetch_array($result)){
	$rows[$row['id']] = $row;
}

$style = "style='width: 100%;height: 1000px'";

		$return_me = 
			"
    <div dojoType='dijit.layout.TabContainer' id='protocol_sub_tab_interface' $style >";

foreach($rows as $id => $row){

$name = $row['name'];

if($row == end($rows)) {
    $selected = " selected='true' ";
  } else {
    $selected = "";
  }

	
	$return_me .= "<div dojoType='dijit.layout.ContentPane' id='content_$name' $selected  title='$name' >";
 
	$return_me .= $this->protocolForm($row);

        $return_me .=       "</div>";


}

	$return_me .= '</div>';

	return($return_me);


	}


	function protocolForm($row){

	foreach($row as $aName => $value){
		$$aName = $value;
	}
	
	$rows = 5;
	$height = '150px';
	$width = '400px';

		$item_array = array(
			'history_targets' => array('print'=>'History Targets','item'=>$history_targets),
			'risk_factors' => array('print'=>'Risk Factors','item'=>$risk_factors),
			'outcome_goals' => array('print'=>'Outcome Goals','item'=>$outcome_goals),
			'treatment_goals' => array('print'=>'Treatment Goals','item'=>$treatment_goals),
			'medications' => array('print'=>'Medications','item'=>$medications),
			'lab_studies' => array('print'=>'Lab Studies','item'=>$lab_studies),
			'referral' => array('print'=>'Referral Criteria','item'=>$referral),
			'interventions' => array('print'=>'Interventions','item'=>$interventions),

			);




	$form = "<form action='protocol_form.php' method='post'>
        <br />
        <table width='100%'>";

		foreach($item_array as $item_program_name => $item_array){
			$print_name = $item_array['print'];
			$item = $item_array['item'];

$form .= "		
<tr>
	<td valign='top'>
		<h3>$name $print_name :</h3>
	</td>
	<td>
		<textarea  rows='$rows' height='$height' width='$width' name='$item_program_name"."_$name'> $item </textarea>
	</td>
</tr>
";



		}
   

	$form .= "
             <tr>
                        <td>
                        <input type='submit' value='Save Protocol' />
                        </td>
                        <td>
                        </td>
                </tr>
        </table>
</form>
";

	return($form);






	}




}






?>
