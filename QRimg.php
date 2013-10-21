<?php


	class QRimg{


	function getQRimg(){
		$my_url = $this->curPageURL();
		$my_url_encoded = urlencode($my_url);

		$my_img_link = "qr_img.php?d=$my_url";
	//	echo "$my_img_link <br />";

		return( "<div style='position:absolute; top:0; right:0;'><img  src='qr_img.php?d=$my_url'></div>");

	}

function curPageURL() {
 $pageURL = 'http';
 if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

}

