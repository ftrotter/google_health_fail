<?php



function register_form(){


echo " 
	<h2> Register for a Quality Health Record</h2>	
	<form action='signup.php' method='POST'>
	<input type='hidden' name='register' value='1'>
	<fieldset>	
	<legend>
	Your Information
	</legend>
	
	Your Google Account in the form 'yourname@gmail.com', just 'yourname' will not work (go <a href='http://gmail.com'>here</a> if you do not have one): <input type='text' name='me_email'> <br>
	Your Salutation (like Mr, Mrs or Dr): <input type='text' name='me[salutation]'> <br>
	Your First Name: <input type='text' name='me[first_name]'> <br>
	Your Last Name: <input type='text' name='me[last_name]'> <br>
	Your Middle Name: <input type='text' name='me[middle_name]'> <br>
	Your other email (if you have one) <input type='text' name='me_secondary_email'> <br>
	Your Home Phone <input type='text' name='me_home_phone'> <br>
	Your Work Phone <input type='text' name='me_work_phone'> <br>
	Your Cell Phone <input type='text' name='me_cell_phone'> <br>
	Your Address First Line <input type='text' name='me_address[line1]'> <br>
	Your Address Second Line <input type='text' name='me_address[line2]'> <br>
	Your Address City <input type='text' name='me_address[city]'> <br>
	Your Address State 
<select name='me_state'>
<option value='1'>Alabama</option>
<option value='2'>Alaska</option>
<option value='3'>Arizona</option>
<option value='4'>Arkansas</option>
<option value='5'>California</option>
<option value='6'>Colorado</option>
<option value='7'>Connecticut</option>
<option value='8'>Delaware</option>
<option value='9'>Florida</option>
<option value='10'>Georgia</option>
<option value='11'>Hawaii</option>
<option value='12'>Idaho</option>
<option value='13'>Illinois</option>
<option value='14'>Indiana</option>
<option value='15'>Iowa</option>
<option value='16'>Kansas</option>
<option value='17'>Kentucky</option>
<option value='18'>Louisiana</option>
<option value='19'>Maine</option>
<option value='20'>Maryland</option>
<option value='21'>Massachusetts</option>
<option value='22'>Michigan</option>
<option value='23'>Minnesota</option>
<option value='24'>Mississippi</option>
<option value='25'>Missouri</option>
<option value='26'>Montana</option>
<option value='27'>Nebraska</option>
<option value='28'>Nevada</option>
<option value='29'>New Hampshire</option>
<option value='30'>New Jersey</option>
<option value='31'>New Mexico</option>
<option value='32'>New York</option>
<option value='33'>North Carolina</option>
<option value='34'>North Dakota</option>
<option value='35'>Ohio</option>
<option value='36'>Oklahoma</option>
<option value='37'>Oregon</option>
<option value='38'>Pennsylvania</option>
<option value='39'>Rhode Island</option>
<option value='40'>South Carolina</option>
<option value='41'>South Dakota</option>
<option value='42'>Tennessee</option>
<option value='43'>Texas</option>
<option value='44'>Utah</option>
<option value='45'>Vermont</option>
<option value='46'>Virginia</option>
<option value='47'>Washington</option>
<option value='48'>West Virginia</option>
<option value='49'>Wisconsin</option>
<option value='50'>Wyoming</option>
</select>

<br>
		Your Address Zip <input type='text' name='me_address[postal_code]'> <br>

		</fieldset>
		<br>

                <fieldset>      
                <legend>
                Your Emergency Contact
                </legend>
                Emergency Relationship  
<select name='emergency_relationship'>
<option value='1'>Spouse</option>
<option value='2'>Parent</option>
<option value='3'>Child</option>
<option value='4'>Other</option>

</select>
<br>
                Emergency Contact Salutation (like Mr, Mrs or Dr): <input type='text' name='em[salutation]'> <br>
                Emergency Contact First Name: <input type='text' name='em[first_name]'> <br>
                Emergency Contact Last Name: <input type='text' name='em[last_name]'> <br>
                Emergency Contact Middle Name: <input type='text' name='em[middle_name]'> <br>
                Emergency Contact email <input type='text' name='em_email'> <br>
		Emergency Contact Home Phone <input type='text' name='em_home_phone'> <br>
		Emergency Contact Work Phone <input type='text' name='em_work_phone'> <br>
		Emergency Contact Cell Phone <input type='text' name='em_cell_phone'> <br>
		Emergency Contact Address First Line <input type='text' name='em_address[line1]'> <br>
		Emergency Contact Address Second Line <input type='text' name='em_address[line2]'> <br>
        Your Address State 
<select name='em_state'>
<option value='1'>Alabama</option>
<option value='2'>Alaska</option>
<option value='3'>Arizona</option>
<option value='4'>Arkansas</option>
<option value='5'>California</option>
<option value='6'>Colorado</option>
<option value='7'>Connecticut</option>
<option value='8'>Delaware</option>
<option value='9'>Florida</option>
<option value='10'>Georgia</option>
<option value='11'>Hawaii</option>
<option value='12'>Idaho</option>
<option value='13'>Illinois</option>
<option value='14'>Indiana</option>
<option value='15'>Iowa</option>
<option value='16'>Kansas</option>
<option value='17'>Kentucky</option>
<option value='18'>Louisiana</option>
<option value='19'>Maine</option>
<option value='20'>Maryland</option>
<option value='21'>Massachusetts</option>
<option value='22'>Michigan</option>
<option value='23'>Minnesota</option>
<option value='24'>Mississippi</option>
<option value='25'>Missouri</option>
<option value='26'>Montana</option>
<option value='27'>Nebraska</option>
<option value='28'>Nevada</option>
<option value='29'>New Hampshire</option>
<option value='30'>New Jersey</option>
<option value='31'>New Mexico</option>
<option value='32'>New York</option>
<option value='33'>North Carolina</option>
<option value='34'>North Dakota</option>
<option value='35'>Ohio</option>
<option value='36'>Oklahoma</option>
<option value='37'>Oregon</option>
<option value='38'>Pennsylvania</option>
<option value='39'>Rhode Island</option>
<option value='40'>South Carolina</option>
<option value='41'>South Dakota</option>
<option value='42'>Tennessee</option>
<option value='43'>Texas</option>
<option value='44'>Utah</option>
<option value='45'>Vermont</option>
<option value='46'>Virginia</option>
<option value='47'>Washington</option>
<option value='48'>West Virginia</option>
<option value='49'>Wisconsin</option>
<option value='50'>Wyoming</option>
</select>
<br>
		Emergency Contact Address City <input type='text' name='em_address[city]'> <br>
		Emergency Contact Address Zip <input type='text' name='em_address[postal_code]'> <br>

                </fieldset>


		<input type='submit' name='Register'>
		</form>


	";

}






?>
