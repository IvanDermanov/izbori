<?php
	require_once("db_parameters.php");
	$connection = new mysqli(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
	if($connection->connect_errno > 0){
		die('Ne mogu da se povezem sa bazom zbog ['. 
		$connection->connect_error .']');
	}
	mysqli_set_charset($connection,"utf8");
?>