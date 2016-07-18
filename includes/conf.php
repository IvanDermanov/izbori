<?php
@$c=$_POST["code"];
@$key=$_POST['key'];

if ($c==33){

	require("./includes/connection.php");
	$sql="UPDATE sms_za_slanje SET confirmed=1 WHERE sms_key=$key";
	
	if (!$results = $connection->query($sql))echo "Ne mogu da izvrsim upit zbog [". $connection->error . "]";
	
	$view="CONFRM<eol>";
	$view.="key=$key";
	require("./includes/dbcloseconnection.php");
}
?>