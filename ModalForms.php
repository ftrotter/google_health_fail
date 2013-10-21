<?php


	class ModalForms{

		var $risk_form = 
"
<div dojoType='dijit.Dialog' id='risk_form' title='Risk Factors' 
	execute='submitForm(arguments[0]);'>

    <table>
        <tr>
                <td>
		    <input id='rf_active_smoking' dojotype='dijit.form.CheckBox' name='rf_active_smoking'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Active Smoking </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_passive_smoking' dojotype='dijit.form.CheckBox' name='rf_passive_smoking'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Passive Smoking </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_family_smoking' dojotype='dijit.form.CheckBox' name='rf_family_smoking'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Family Member Smoking </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_low_or_no_insurance' dojotype='dijit.form.CheckBox' name='rf_low_insurance'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Low or Now Insurance </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_economic' dojotype='dijit.form.CheckBox' name='rf_economic'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Economic </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_patient_refusal' dojotype='dijit.form.CheckBox' name='rf_refusal'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Patient Refusal </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_not_indicated' dojotype='dijit.form.CheckBox' name='rf_not_indicated'   value='on' type='checkbox' />
		</td>
        <td><label for='name'> Protocol not indicated/contra-indicated  </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_family_history' dojotype='dijit.form.CheckBox' name='rf_family_history'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Family History </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_obesity' dojotype='dijit.form.CheckBox' name='rf_obesity'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Obesity </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_drug_abuse' dojotype='dijit.form.CheckBox' name='rf_drug_abuse'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Drug Abuse </label></td>
        </tr>
        <tr>
                <td>
		    <input id='rf_alcohol_abuse' dojotype='dijit.form.CheckBox' name='rf_alcohol_abuse'   value='on' type='checkbox' />
		</td>
        <td><label for='name'>Alchohol Abuse </label></td>
        </tr>

        <tr>
                <td colspan='2' align='center'>
			<input dojoType='dijit.form.TextBox' type='hidden' name='planny_id_risk' id='planny_id_risk' value=''>
                        <button dojoType=dijit.form.Button type='submit' on>Save</button></td>
        </tr>
    </table>
</div>


";

	var $owner_form = 
"
<div dojoType='dijit.Dialog' id='owner_form' title='Plan Owner' 
	execute='submitForm(arguments[0]);'>

    <table>
        <tr>
		<td><label for='owner_first'>Plan Owner First Name</label></td>
                <td>
			<input dojoType='dijit.form.TextBox' type='text' name='owner_first' id='owner_first'>
		</td>
        </tr>
        <tr>
		<td><label for='owner_last'>Plan Owner Last Name</label></td>
                <td>
			<input dojoType='dijit.form.TextBox' type='text' name='owner_last' id='owner_last'>
		</td>
        </tr>
        <tr>
		<td><label for='owner_phone'>Plan Owner Phone</label></td>
                <td>
			<input dojoType='dijit.form.TextBox' type='text' name='owner_phone' id='owner_phone'>
		</td>
        </tr>
        <tr>
		<td><label for='owner_city'>Plan Owner City</label></td>
                <td>
			<input dojoType='dijit.form.TextBox' type='text' name='owner_city' id='owner_city'>
		</td>
        </tr>
        <tr>
		<td><label for='owner_zip'>Plan Owner Zip</label></td>
                <td>
			<input dojoType='dijit.form.TextBox' type='text' name='owner_zip' id='owner_zip'>
		</td>
        </tr>
        <tr>
		<td><label for='owner_state'>Plan Owner State</label></td>
                <td>
        <select dojoType='dijit.form.FilteringSelect'
                id='owner_state'
                name='owner_state'
                autoComplete='false'
                invalidMessage='Invalid state name'>
            <option value='blank'></option>
            <option value='AL'>Alabama</option>
            <option value='AK'>Alaska</option>
            <option value='AS'>American Samoa</option>
            <option value='AZ'>Arizona</option>
            <option value='AR'>Arkansas</option>
            <option value='AE'>Armed Forces Europe</option>
            <option value='AP'>Armed Forces Pacific</option>
            <option value='AA'>Armed Forces the Americas</option>
            <option value='CA' selected>California</option>
            <option value='CO'>Colorado</option>
            <option value='CT'>Connecticut</option>
            <option value='DE'>Delaware</option>
            <option value='DC'>District of Columbia</option>
            <option value='FM'>Federated States of Micronesia</option>
            <option value='FL'>Florida</option>
            <option value='GA'>Georgia</option>
            <option value='GU'>Guam</option>
            <option value='HI'>Hawaii</option>
            <option value='ID'>Idaho</option>
            <option value='IL'>Illinois</option>
            <option value='IN'>Indiana</option>
            <option value='IA'>Iowa</option>
            <option value='KS'>Kansas</option>
            <option value='KY'>Kentucky</option>
            <option value='LA'>Louisiana</option>
            <option value='ME'>Maine</option>
            <option value='MH'>Marshall Islands</option>
            <option value='MD'>Maryland</option>
            <option value='MA'>Massachusetts</option>
            <option value='MI'>Michigan</option>
            <option value='MN'>Minnesota</option>
            <option value='MS'>Mississippi</option>
            <option value='MO'>Missouri</option>
            <option value='MT'>Montana</option>
            <option value='NE'>Nebraska</option>
            <option value='NV'>Nevada</option>
            <option value='NH'>New Hampshire</option>
            <option value='NJ'>New Jersey</option>
            <option value='NM'>New Mexico</option>
            <option value='NY'>New York</option>
            <option value='NC'>North Carolina</option>
            <option value='ND'>North Dakota</option>
            <option value='MP'>Northern Mariana Islands</option>
            <option value='OH'>Ohio</option>
            <option value='OK'>Oklahoma</option>
            <option value='OR'>Oregon</option>
            <option value='PA'>Pennsylvania</option>
            <option value='PR'>Puerto Rico</option>
            <option value='RI'>Rhode Island</option>
            <option value='SC'>South Carolina</option>
            <option value='SD'>South Dakota</option>
            <option value='TN'>Tennessee</option>
            <option value='TX'>Texas</option>
            <option value='UT'>Utah</option>
            <option value='VT'>Vermont</option>
            <option value='VI'>Virgin Islands, U.S.</option>
            <option value='VA'>Virginia</option>
            <option value='WA'>Washington</option>
            <option value='WV'>West Virginia</option>
            <option value='WI'>Wisconsin</option>
            <option value='WY'>Wyoming</option>
        </select>

		</td>
        </tr>

        <tr>
                <td colspan='2' align='center'>
			<input dojoType='dijit.form.TextBox' type='hidden' name='planny_id_owner' id='planny_id_owner' value=''>
                        <button dojoType=dijit.form.Button type='submit' on>OK</button></td>
        </tr>
    </table>
</div>
";

	var $goal_form = 
"
<div dojoType='dijit.Dialog' id='goal_form' title='Goal' 
	execute='submitForm(arguments[0]);'>

    <table>
        <tr>
        <td><label for='name'>Edit Goal </label></td>
                <td>
			<textarea dojoType='dijit.form.Textarea' style='width:300px' name='goal' id='goal'></textarea>
		</td>
        </tr>
        <tr>
                <td colspan='2' align='center'>
			<input dojoType='dijit.form.TextBox' type='hidden' name='planny_id_goal' id='planny_id_goal' value=''>
                        <button dojoType=dijit.form.Button type='submit' on>Save</button></td>
        </tr>
    </table>
</div>
";

	var $intervention_form = 
"
<div dojoType='dijit.Dialog' id='intervention_form' title='Intervention' 
	execute='submitForm(arguments[0]);'>

    <table>
        <tr>
        <td><label for='name'>Edit Intervention </label></td>
                <td>
			<textarea dojoType='dijit.form.Textarea' style='width:300px' name='intervention' id='intervention'></textarea>
		</td>
        </tr>
        <tr>
                <td colspan='2' align='center'>
			<input dojoType='dijit.form.TextBox' type='hidden' name='planny_id_intervention' id='planny_id_intervention' value=''>
                        <button dojoType=dijit.form.Button type='submit' on>Save</button></td>
        </tr>
    </table>
</div>
";

	function getForms(){

	$return_me = $this->risk_form;
	$return_me .= $this->owner_form;
	$return_me .= $this->goal_form;
	$return_me .= $this->intervention_form;


	return($return_me);

	}

	}

?>
