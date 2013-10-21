<?php
require_once('Tab.class.php');

class MessageTab extends Tab{

	var $id = 'Message';
	var $myTitle = 'Messages';


	function getID(){
		return $this->id;
	}

	function getTitle(){
		return($this->myTitle);
	}


	function __construct(){


	}


	function getContent(){


		if(isset($GLOBALS['clinicalAccess'])){
			//clinical user world
			$user_id = $GLOBALS['clinicalAccess']->id;
		}else{
			//patient user world
			$user_id = $_SESSION['patient_id'];
		}


		$inbox_json_link = "inbox_json.php?user_id=$user_id";
		$sent_json_link = "sent_json.php?user_id=$user_id";

			//echo $user_id;

	$return_me = "



                <div dojoType='dijit.layout.BorderContainer' design='sidebar' liveSplitters='false' style=' width: 90%; height: 2000px'>
                        <div dojoType='dijit.layout.ContentPane' region='leading' style='width: 130px;'>
				<ul>
					<li>
				<a href='#' onclick='turn_on(\"inbox_display\");turn_off(\"sent_display\");turn_off(\"thread_display\");'> 
						Inbox </a>
					</li>
					<li>
				<a href='#' onclick='turn_off(\"inbox_display\");turn_on(\"sent_display\");turn_off(\"thread_display\");'> 
						Sent </a>
					</li>
				</ul>
                        </div>




                        <div dojoType='dijit.layout.ContentPane' region='center' style=' padding: 10px;'>

";

		$grid = "
	<script type='text/javascript'>
		var inJsonStore = new dojo.data.ItemFileReadStore({ url: '$inbox_json_link' });
	</script>
	<script type='text/javascript'>
		var sentJsonStore = new dojo.data.ItemFileReadStore({ url: '$sent_json_link' });
	</script>
";

		$grid .= "
	<div id='inbox_display'>
						<h2> Inbox </h2>
        <table id='inboxGridNode' jsId='inboxGrid' dojoType='dojox.grid.DataGrid'
               query=\"{ thread_id: '*' }\" store='inJsonStore' style='height: 250px'>
                <thead>
                        <tr>
                                <th field='from' width='100px'>From</th>
                                <th field='to' width='100px'>To</th>
                                <th field='subject' width='200px'>Subject</th>
                                <th field='regarding' width='100px'>Regarding Patient</th>
                                <th field='total_messages' width='100px'>Message #</th>
                                <th field='task_status' width='100px'>Task Status</th>
                                <th field='sent' width='100px'>Sent</th>
                        </tr>
                </thead>
        </table>
	</div>


	<div id='sent_display' >
						<h2> Sent </h2>
        <table id='sentGridNode' jsId='sentGrid' dojoType='dojox.grid.DataGrid'
               query=\"{ thread_id: '*' }\" store='sentJsonStore' style='height: 250px'>
                <thead>
                        <tr>
                                <th field='from' width='100px'>From</th>
                                <th field='to' width='100px'>To</th>
                                <th field='subject' width='200px'>Subject</th>
                                <th field='regarding' width='100px'>Regarding Patient</th>
                                <th field='total_messages' width='100px'>Message #</th>
                                <th field='task_status' width='100px'>Task Status</th>
                                <th field='sent' width='100px'>Sent</th>
                        </tr>
                </thead>
        </table>
	</div>

        <script type='text/javascript'>
		setTimeout(\"turn_off('sent_display')\",2000);
        </script>

	<br \>
	<br \>
	<br \>
	<br \>
	<div id='thread_display'> </div>


	


";

/*		$iframe = "<iframe src='json_test.html' width='1000px' height='2000px' style='border: 0px'> Nothing here </iframe>";


		$javascript = '
<div dojoType="dijit.layout.BorderContainer" jsid="container" id="container" design="headline" >Supposed to go here</div>

';
*/


		$return_me .= $grid;
		//$return_me .= $iframe;


		//closes out the layout grids
		$return_me .= "
                        </div>
                </div>
			";




		if($GLOBALS['debug']){
			$return_me .= "<br><br><br><br> <a href='$inbox_json_link'>Inbox json link</a>";
			$return_me .= "<br><a href='$sent_json_link'>sent json link</a>";

		}



		return($return_me);

	}



	function getSingleMessageContent(){

		$email = $this->patient_email;
		
		$return_me = 
			"
<form action='google_send.php' method='post'>
	<br />
	<table width='100%'>
		<tr>
			<td width='30px'>Subject: </td>
			<td><input type='text' id='subject' name='subject'><br /></td>
		<tr>
			<td valign='top'>Message: </td>
			<td>
				<textarea name='html_message' width='600px' dojoType='dijit.Editor'>
				        Your message text goes here!!
				</textarea>
			</td>
		</tr>
		<tr>
			<td>
			<input type='hidden' name='google_account' id='google_account' value='$email'>
			<input type='submit' value='Send' />
			</td>
			<td>
			</td>
		</tr>
	</table>
</form>

			";

	return($return_me);


	}
}


?>
