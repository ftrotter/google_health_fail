<?php



    session_start();
	if(isset($_SESSION['key'])){
		$key = $_SESSION['key'];
	}
	if(isset($_GET['key'])){
		$key = $_GET['key'];
		$_SESSION['key'] = $key;	
	}

	require_once('head.php');//where the head and tail functions are defined
	require_once('mysql.php');//my logging and session database
	require_once('log.php');//my logging and session database
	$head = new HTMLHeader();
	$head->setTheme();

	$demo = $GLOBALS['demo'];

$two_box_css = '
<style>
#content {
	float: left;
	padding: 10px;
	margin: 20px;
	background: #DEE7EC;
	border: 5px solid #8CACBB;
	width: 300px; 
	/* ie5win fudge begins */
	voice-family: "\"}\"";
	voice-family:inherit;
	width: 270px;
	}
html>body #content {
	width: 270px; 
	/* ie5win fudge ends */
	}

#content2 {
	float: left;
	padding: 10px;
	margin: 20px;
	background: #DEE7EC;
	border: 5px solid #8CACBB;
	width: 300px; 
	/* ie5win fudge begins */
	voice-family: "\"}\"";
	voice-family:inherit;
	width: 270px;
	}
html>body #content2 {
	width: 270px; 
	/* ie5win fudge ends */
	}

</style>

';


	$head->addCSS($two_box_css);

	echo $head->getHeader();

		$signup_url = 'signup.php';


	if(isset($key) && ($key !== '') && ($key !==0 )){///

		/// without a key in the link 
		/// this is just a login attempt by someone!!
		///

		$message = "You are trying to access the site with a key of $key";
		$patient_url = "patientlogin.php?key=$key";
		$doctor_url = "load.php?key=$key";
	}else{

		$message = "You are trying to access the site without a specific patient key";
		if($demo){
			$message .= " <br>since this is a demo, you can <a href='demo.php'>load a demo patient</a><br>";
		}
		$patient_url = "patientlogin.php";
		$doctor_url = "load.php";

	}
	
		echo "<br /><h1>Welcome to the YDP QHR test system</h1>$message<br />";
		echo "
<table class='login' width='70%'><tr>		

	<td valign='top' width='50%'>		<h1>Clinicians</h1>	
	If you are a <a href='$doctor_url'>Clinician Login and Signup here</a>.
	</td>				
	<td valign='top'>
			<h1>Patients</h1>
	If you are a Patient <a href='$patient_url'>Patient Login here</a><br>
	If you do not have an account with YDP <a href='$signup_url'>you can signup here</a>
	</td>
</tr></table>
";

	


		echo $head->getFooter();




?>
