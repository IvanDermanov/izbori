<?php
//bagovi broj izaslih se moze smanjivati
//code=35 & phone=%phone% & message=%message% & date=%date% & time=%time%"
	require("includes/connection.php");
	require_once ("includes/daySeasson.php");
	
	$phone=str_replace(' 381','+381',$_POST['phone']);
	$date=$_POST['date'];
	$time=$_POST['time'];
	$message=$_POST["message"];
	
	// Upis u bazu podataka primljenih poruka
	$sql = "INSERT INTO primljane_sms (`mobilni`,`message`,`time`,`date`,`day_seasson`) VALUES ('$phone','$message','$time','$date',$daySesson)";
	
	if (!$results = $connection->query($sql))die('Ne mogu da izvrsim upit zbog ['. $connection->error . "]");
		
	require ("model.php");

//broj biraca na birackom mestu	B,<biracko mesto>,<broj biraca>
//izlaznost						I,<biracko mesto>,<broj izaslih biraca>
//rezultati izbora				L|P|R,<biracko mesto>,<1-346,2-456,3-766,4-865,5-787,8-990,9-909,10-876,11-997>
//vrati poslednji podatak		B,<biracko mesto>,?
//								I,<biracko mesto>,?
//								L,<biracko mesto>,?
//								P,<biracko mesto>,?
//								R,<biracko mesto>,?

			$letter="";
			$brojBirackogMesta=0;
			$poslatiPodaci="";

	if ($daySesson<24){
	
		$arrayMessage=messageToArray($message);	
		if (is_array($arrayMessage)){
			//ovde dolazi samo ako postoje tri komponente
			
			$letter=$arrayMessage[0];
			$brojBirackogMesta=$arrayMessage[1];
			$poslatiPodaci=$arrayMessage[2];

			$greska=checkLetter($letter);
			if ($greska==""){
				$greska=checkBrojBirackogMesta($brojBirackogMesta);
				if ($greska==""){
					$ovlascen=isTelefonNaSpiskuIBirackomMestu($brojBirackogMesta,$phone,$connection);
					if ($ovlascen)$greska="";else $greska="Telefon nije na birackom mestu";
					if ($greska==""){
						// proverava samo da li je podatak '?' ili array 1-23,2-34,33-43, odgovara broju lista ili broj biraca je numericki podatak
						$greska=checkPoslatiPodaci($letter,$poslatiPodaci);
							if ($greska==""){
								$greska=semantic_check ($letter,$brojBirackogMesta,$poslatiPodaci,$connection,$daySesson);
								if ($greska==""){
									//upisi u ako je ovlascen telefon poslao u bazu podataka i zahvali se
									$greska=unesi_u_bazu_podataka($letter,$brojBirackogMesta,$poslatiPodaci,$date,$time,$phone,"OK",$daySesson,$connection);
								}
							}
						}
					}else $ovlascen=isTelefonNaSpisku($phone,$connection);
				}else $ovlascen=isTelefonNaSpisku($phone,$connection);
		}else {$greska=$arrayMessage;$ovlascen=isTelefonNaSpisku($phone,$connection);}

		if ($ovlascen) echo sendSms ($phone,$greska,$connection);	
		
	}

	include("includes/dbcloseconnection.php");

?>