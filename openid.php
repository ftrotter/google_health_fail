<?php

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_OpenId_Consumer');



$status = "";
if (isset($_POST['openid_action']) &&
    $_POST['openid_action'] == "login" &&
    !empty($_POST['openid_identifier'])) {

    $consumer = new Zend_OpenId_Consumer();
    if (!$consumer->login($_POST['openid_identifier'],'openid.php','https://www.google.com/accounts/o8/id')) {
        $status = "OpenID login failed.";
    }
} else if (isset($_GET['openid_mode'])) {
    if ($_GET['openid_mode'] == "id_res") {
        $consumer = new Zend_OpenId_Consumer();
        if ($consumer->verify($_GET, $id)) {
            $status = "VALID " . htmlspecialchars($id);
        } else {
            $status = "INVALID " . htmlspecialchars($id);
        }
    } else if ($_GET['openid_mode'] == "cancel") {
        $status = "CANCELLED";
    }
}
?>
<html><body>
<?php echo "$status<br>" ?>
<form method="post">
<fieldset>
<legend>OpenID Login</legend>
<input type="text" name="openid_identifier" value=""/>
<input type="submit" name="openid_action" value="login"/>
</fieldset>
</form>
</body></html>



