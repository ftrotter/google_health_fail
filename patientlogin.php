<?php
  require_once('GoogleOpenID.php');
  $googleLogin = GoogleOpenID::createRequest("patient.php", $association_handle, true);

  if(isset($_GET['key'])){
		$_SESSION['key'] = $key;
	}

  $googleLogin->redirect();
?>
