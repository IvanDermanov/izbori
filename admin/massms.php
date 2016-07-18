<?php
	session_start();

	if(!$_SESSION['valid']) {header('Location: index.php'); die;}
	
	require_once("../includes/connection.php");
	require_once ("../includes/daySeasson.php");
	require_once ("../model.php");
	
	$message=@trim($_POST["message"]);
	
	$lista=generateListaPosmatraca($connection);
	
	foreach($lista as $posmatrac){
		// posmatrac[0] je redni broj birackog mesta na kojem je posmatrac
		$tmessage=str_replace("<rbr>",$posmatrac[0],$message);
		// posmatrac[1] je broj telefona u bazi podataka
		$phone=$posmatrac[1];
		// posmatrac[2] je ime posmatraca u bazi podataka
		$ime=$posmatrac[2];
		sendSms ($phone,$tmessage,$connection);
		//echo "<i>$tmessage</i> $phone - $ime\n<br>";
	}


	require_once("../includes/dbcloseconnection.php");
	
	header('Location: sms.php');
	
function generateListaPosmatraca($connection){

	$retVal=array();
	
	$sql="SELECT redni_broj_izbornog_mesta, mobilni, ime, prezime FROM posmatraci";
	$results = executeSql($sql,$connection);

	//nadji poslednji upisani podatak
	if ($results->num_rows==0) {
		$retVal="Nema podataka u bazi!";
	}else{
		while ($row = $results->fetch_assoc()){
			$posmatrac=array($row['redni_broj_izbornog_mesta'],$row['mobilni'],$row['ime'],$row['prezime']);
			array_push($retVal,$posmatrac);
		}
	}
	
	$results->free();
	return $retVal;
}
?>