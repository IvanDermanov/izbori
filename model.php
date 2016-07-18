<?php

function tabela_izbor(){
	require_once ("./includes/connection.php");
	$sql="SELECT `redni_broj_izbornog_mesta`, `upisano_biraca`,`izlaznost_11`, `izlaznost_15`, `izlaznost_18`, `izlaznost`, `rez_lokalnih_izbora_po_listama`, `rez_pokrajinskih_izbora_po_listama`, `rez_republickih_izbora_po_listama` FROM `izbor`";
	$results = executeSql($sql,$connection);
	
	$keys=array();
	$values=array();
	
	while ($row = $results->fetch_assoc()){
	
		array_push($keys,$row['redni_broj_izbornog_mesta']);
		
		$arrData= array($row['upisano_biraca'],$row['izlaznost_11'],$row['izlaznost_15'],$row['izlaznost_18'],$row['izlaznost'],$row['rez_lokalnih_izbora_po_listama'],$row['rez_pokrajinskih_izbora_po_listama'],$row['rez_republickih_izbora_po_listama']);
	
		array_push($values,$arrData);
		
	}
	
	$retVal=array_combine($keys,$values);

	$results->free();
	require_once ("./includes/dbcloseconnection.php");
	return $retVal;
}
	
function sendSms($phone,$message,$connection){
		
	$sql="INSERT INTO sms_za_slanje (`mobilni`, `message`) VALUES ('$phone','$message')";
	$results = executeSql($sql,$connection);
	
}

function unesi_u_bazu_podataka($comm,$br_bir_mes,$rezultati,$date,$time,$phone,$status,$daySesson,$connection){

	$msg="Izvestaj poslat u pogresno vreme.";

	$podatak=arrayToString($rezultati);
	
	$sql = "INSERT INTO zapisnik_izbornog_mesta (`Command`,`broj_izbornog_mesta`, `podatak`, `date`, `time`, `mobilni`, `status`, `day_season`) VALUES ('$comm',$br_bir_mes,'$podatak','$date','$time','$phone','$status',$daySesson)";
	$results = executeSql($sql,$connection);

	switch ($comm){
		case "B":
			if ($daySesson<11){
				$sql = "UPDATE izbor SET upisano_biraca=$podatak WHERE redni_broj_izbornog_mesta=$br_bir_mes";
				$results = executeSql($sql,$connection);
				$msg="Izvestaj uspesno prihvacen!\nHvala.\n(IT tim SDPS-a).";
			}
			break;
		case "I":
			if ($daySesson==9 || $daySesson==11) $sql = "UPDATE izbor SET izlaznost_11=$podatak,izlaznost_15=$podatak,izlaznost_18=$podatak,izlaznost=$podatak WHERE redni_broj_izbornog_mesta=$br_bir_mes";
			if ($daySesson==15) $sql = "UPDATE izbor SET izlaznost_15=$podatak,izlaznost_18=$podatak,izlaznost=$podatak WHERE redni_broj_izbornog_mesta=$br_bir_mes";
			if ($daySesson==18) $sql = "UPDATE izbor SET izlaznost_18=$podatak,izlaznost=$podatak WHERE redni_broj_izbornog_mesta=$br_bir_mes";
			if ($daySesson>18 && $daySesson<24) $sql = "UPDATE izbor SET izlaznost=$podatak WHERE redni_broj_izbornog_mesta=$br_bir_mes";
			$results = executeSql($sql,$connection);
			$msg="Izvestaj uspesno prihvacen!\nHvala.\n(IT tim SDPS-a).";
			break;
		case "L":
			if ($daySesson<=20) {
				$sql = "UPDATE izbor SET rez_lokalnih_izbora_po_listama='$podatak' WHERE redni_broj_izbornog_mesta=$br_bir_mes";
				$results = executeSql($sql,$connection);
				$msg="Izvestaj uspesno prihvacen!\nHvala.\n(IT tim SDPS-a).";
			}
			break;
		case "P":
			if ($daySesson<=20) {
				$sql = "UPDATE izbor SET rez_pokrajinskih_izbora_po_listama='$podatak' WHERE redni_broj_izbornog_mesta=$br_bir_mes";
				$results = executeSql($sql,$connection);
				$msg="Izvestaj uspesno prihvacen!\nHvala.\n(IT tim SDPS-a).";
			}
			break;
		case "R":
			if ($daySesson<=20) {
				$sql = "UPDATE izbor SET  rez_republickih_izbora_po_listama='$podatak' WHERE redni_broj_izbornog_mesta=$br_bir_mes";
				$results = executeSql($sql,$connection);
				$msg="Izvestaj uspesno prihvacen!\nHvala.\n(IT tim SDPS-a).";
			}
			break;
	}
	
	return $msg;
	
}

function arrayToString($rezultati){
	$retVal="";
	if (count($rezultati)==1){
		$retVal=$rezultati;
	}else{
		foreach ($rezultati as $key => $value){
			$retVal.=$key."-".$value.",";
		}
		$retVal=trim($retVal,",");
	}
	return $retVal;
	
}

function isTelefonNaSpiskuIBirackomMestu($brojBirackogMesta,$phone,$connection){
// ovlasceni telefon mora biti vezan za biracko mesto da ne bi posmatraci slali poruke za drugo biracko mesto
		$sql="SELECT redni_broj_izbornog_mesta FROM posmatraci WHERE redni_broj_izbornog_mesta=$brojBirackogMesta AND mobilni='$phone'";
		$results = executeSql($sql,$connection);
		
		$ovlascen=false;
		if ($results->num_rows>0) $ovlascen=true;
		
		$results->free();
		
		return $ovlascen;
}

function isTelefonNaSpisku($phone,$connection){
		$sql="SELECT redni_broj_izbornog_mesta FROM posmatraci WHERE mobilni='$phone'";
		$results = executeSql($sql,$connection);
		
		$ovlascen=false;
		//$na_birackom_mestu se nalazi array sa birackim mestima na kojem je telefon upisan
		// iz baze naci svako biracko mesto gde je upisan telefon

		if ($results->num_rows>0) $ovlascen=true;
		
		$results->free();
		
		return $ovlascen;
}
function u_deset_posto($br_bir_mes,$biraca,$connection){
	$sql="SELECT default_upisano_biraca FROM izbor WHERE redni_broj_izbornog_mesta=$br_bir_mes";
	$results = executeSql($sql,$connection);
	
	$row = $results->fetch_assoc();
	$default_upisano_biraca=$row['default_upisano_biraca'];
	$retVal=true;
	if(abs($biraca-$default_upisano_biraca)>0.005*$default_upisano_biraca) $retVal=false;

	$results->free();
	return $retVal;
}

function manje_od_upisanih($br_bir_mes,$rezultati,$connection){

	$upisano_biraca=upisanih_biraca($br_bir_mes,$connection);
	
	$retVal=true;
	if($rezultati>$upisano_biraca || $rezultati<0) $retVal=false;
	
	return $retVal;
}

function manje_od_prethodnog($br_bir_mes,$rezultati,$connection,$daySesson){
	$retVal=false;
	
	$prethodnaIzlaznost=prethodno_izasli($br_bir_mes,$connection,$daySesson);

	if($rezultati<$prethodnaIzlaznost)$retVal=true;
	
	return $retVal;
}

function prethodno_izasli($br_bir_mes,$connection,$daySesson){

	$sql="SELECT izlaznost_11,izlaznost_15,izlaznost_18 FROM izbor WHERE redni_broj_izbornog_mesta=$br_bir_mes";
	$results = executeSql($sql,$connection);

	$retVal=0;
	if ($results->num_rows>0) {
		$row = $results->fetch_assoc();
		switch ($daySesson){
		case 9:
		case 11:
			$retVal=0;
			break;
		case 15:
			$retVal=$row['izlaznost_11'];
			break;
		case 18:
			$retVal=$row['izlaznost_15'];
			break;
		case 19:
		case 20:
		case 24:
			$retVal=$row['izlaznost_18'];
			break;
		}
	}
	
	$results->free();
	return $retVal;
}
function poruka_sa_poslednjim_podacima($comm,$br_bir_mes,$connection){
	
	$sql="SELECT `podatak` FROM `zapisnik_izbornog_mesta` WHERE broj_izbornog_mesta =$br_bir_mes AND Command = '$comm' ORDER BY `key` DESC";
	$results = executeSql($sql,$connection);

	//nadji poslednji upisani podatak
	if ($results->num_rows==0) {
		$retVal="Nema podataka u bazi!";
	}else{
		$row = $results->fetch_assoc();
		$retVal=$comm.",".$br_bir_mes.",".$row['podatak'];
	}
	
	$results->free();
	return $retVal;
}

function vrati_formular($comm,$brojBirackogMesta){
	$retVal="";

	switch($comm){
		case "B":
			$retVal="B,<broj birackog mesta>,?\nZameni ? brojem upisanih biraca\nPRIMER:B,186,1969";
			break;
		case "I":
			$retVal="I,<broj birackog mesta>,?\nZameni ? brojem izaslih biraca\nPRIMER:I,186,969";
			break;
		case "L":
			$retVal="L,<broj birackog mesta>,".dopuni_listom(LOKALNIH_LISTA);
			break;
		case "P":
			$retVal="P,<broj birackog mesta>,".dopuni_listom(POKRAJINSKIH_LISTA);
			break;
		case "R":
			$retVal="R,<broj birackog mesta>,".dopuni_listom(REPUBLICKIH_LISTA);
			break;
	}
	
	return str_replace ("<broj birackog mesta>",$brojBirackogMesta, $retVal);
	
}

function dopuni_listom($brojLista){
	$retVal="";
	
	for($i=1;$i<=$brojLista;$i++){
		$retVal.="$i-?, ";
	}
	
	return trim($retVal,", ");
	
}



function semantic_check ($comm,$br_bir_mes,$rezultati,$connection,$daySesson){

	$retErr="";
	//provera da li je broj izbornog mesta u opsegu 0<x<198?
	if ($br_bir_mes<0 || $br_bir_mes>BIRACKIH_MESTA){
		$retErr="Broj birackog mesta nije tacno unet!";
		//provera da li su podaci ispravno uneti?
	}else{
		if ($rezultati=="?") {
			$retErr=vrati_formular($comm,$br_bir_mes);
		}else{
			switch ($comm){
				case "B":
					//provera da li je broj biraca +/- 10% od ocekivanog
					if (!u_deset_posto($br_bir_mes,$rezultati,$connection)) $retErr="Broj upisanih biraca znacajno odudara od ocekivanog broja.";
					break;
				case "I":
					//provera da li je broj izaslih biraca < broj upisanih biraca
					if (!manje_od_upisanih($br_bir_mes,$rezultati,$connection)){
						$retErr="Pogresan podatak!\nBroj izaslih biraca je veci od broja upisanih u biracki spisak ili je unet negativan broj biraca.";
					}else{
						//provera da li je broj izaslih biraca < broja izaslih u prethodnom periodu
						if (manje_od_prethodnog($br_bir_mes,$rezultati,$connection,$daySesson)) $retErr="Pogresan podatak!\nBroj izaslih biraca u prethodnom periodu je bio veci.";
					}
					break;
				case "L":
					//provera da li su svi sa izborne liste dobili glasove
					if (!da_li_su_svi_dobili_glasove($rezultati,LOKALNIH_LISTA)) {
						$retErr="Pogresan podatak!\nBroj lokalnih izbornih lista koje ste poslali ne odgovara broju izbornih lista za koje se glasa.";
					}else{
						//provera da li je suma glasova veca od broja upisanih biraca na izbornom mestu
						if (suma_glasova($rezultati)>upisanih_biraca($br_bir_mes,$connection)) $retErr="Pogresan podatak!\nUkupno glasova na birackom mestu ima vise od upisanih biraca.";
					}
					break;
				case "P":
					//provera da li su svi sa izborne liste dobili glasove
					if (!da_li_su_svi_dobili_glasove($rezultati,POKRAJINSKIH_LISTA)) {
						$retErr="Pogresan podatak!\nBroj pokrajinskih izbornih lista koje ste poslali ne odgovara broju izbornih lista za koje se glasa.";
					}else{
						//provera da li je suma glasova veca od broja upisanih biraca na izbornom mestu
						if (suma_glasova($rezultati)>upisanih_biraca($br_bir_mes,$connection)) $retErr="Pogresan podatak!\nUkupno glasova na birackom mestu ima vise od upisanih biraca.";
					}
					break;
				case "R":
					//provera da li su svi sa izborne liste dobili glasove
					if (!da_li_su_svi_dobili_glasove($rezultati,REPUBLICKIH_LISTA)) $retErr="Pogresan podatak!\nBroj republickih izbornih lista koje ste poslali ne odgovara broju izbornih lista za koje se glasa.";
					//provera da li je suma glasova veca od broja upisanih biraca na izbornom mestu
					if (suma_glasova($rezultati)>upisanih_biraca($br_bir_mes,$connection)) $retErr="Pogresan podatak!\nUkupno glasova na birackom mestu ima vise od upisanih biraca.";
					break;
			}
		}
	}
	//da li je unet podatak ranije koji se ponovo salje?

	return $retErr;
}


function checkLetter($letter){
	$retVal="";
	if($letter!="B" && $letter!="I" && $letter!="L" && $letter!="P" && $letter!="R") $retVal="GRESKA!\nPrvo slovo moze biti samo jedno od B,I,L,P,R";
	return $retVal;
}

function checkPoslatiPodaci($letter,$poslatiPodaci){
		$retVal="";

		if($poslatiPodaci!="?"){
			switch($letter){
				case "B":
				case "I":
					if (!@ctype_digit($poslatiPodaci)) $retVal="GRESKA!\nBroj biraca nije numericki podatak\n$poslatiPodaci?";
					break;
				case "L":
					if(count($poslatiPodaci)!=LOKALNIH_LISTA) $retVal="GRESKA!\nBroj poslatih parova lista-glasovi ne odgovara broju lista koje su ucestvovale na lokalnim izborima";
					break;
				case "P":
					if(count($poslatiPodaci)!=POKRAJINSKIH_LISTA) $retVal="GRESKA!\nBroj poslatih parova lista-glasovi ne odgovara broju lista koje su ucestvovale na pokrajinskim izborima";
					break;
				case "R":
					if(count($poslatiPodaci)!=REPUBLICKIH_LISTA) $retVal="GRESKA!\nBroj poslatih parova lista-glasovi ne odgovara broju lista koje su ucestvovale na republickim izborima";
					break;
			}
		}

		return $retVal;
}

function messageToArray($message){
	$retErr="";
	$ommits = array(" ", "\n", "\r", "\t");
	$message = str_replace($ommits, "", $message);
	// convert . to ,
	$message = str_replace(".", ",", $message);
	
	$smsCommand=explode(",",$message,3);
	// ako nema tri komponente pada na syntax testu
	
	if (count($smsCommand)==3){
		$poruka=parse_message($message);

		if(is_array($poruka)){
			$letter=strtoupper($poruka[0]);
			$brojBirackogMesta=$poruka[1];
			$poslatiPodaci=$poruka[2];
			$retErr= array($letter,$brojBirackogMesta,$poslatiPodaci);
		}else{
			$retErr=$poruka;
		}
	}else{
		$retErr="GRESKA!\nMorate imati u poruci najmanje tri podatka odvojena zarezom.\n PRIMER: I,198,2099";
	}
	return $retErr;
	
}

function parse_message($message){
	$isError=false;
	$ommits = array(" ", "\n", "\r", "\t");
	$message = str_replace($ommits, "", $message);
	// convert . to ,
	$message = str_replace(".", ",", $message);
	
	$smsCommand=explode(",",$message,3);
	
	$letter=strtoupper($smsCommand[0]);
	$brojBirackogMesta=$smsCommand[1];
	$poslatiPodaci=$smsCommand[2];
	
	if ($poslatiPodaci!="?"){
		switch ($letter){
			case "L":
			case "P":
			case "R":
				//ako nije niz onda je greska
				$nizPodataka=pretvoriUNizPoslatePodatke($poslatiPodaci);
				if(!is_array($nizPodataka)) $isError=true; else $poslatiPodaci=$nizPodataka;
			break;
		}
	}
	if ($isError) $retVal=$nizPodataka; else $retVal=array($letter,$brojBirackogMesta,$poslatiPodaci);
		
	return $retVal;
	
}

function pretvoriUNizPoslatePodatke($poslatiPodaci){

	$retVal="";
	$rezultati_izbora=array();
	
			$k=array();
			$v=array();
			//razbij na parove 1-23 , broj liste, broj glasova
			$arg_CSV=explode(",",$poslatiPodaci);
			// razbij svaki par i stavi ga u dva niza koja se posle kombinuju u jedan
			for ($i = 0; $i < count($arg_CSV); $i++) {
				$d=explode ("-",$arg_CSV[$i]);
				@array_push($k,$d[0]);
				@array_push($v,$d[1]);
			}
			if (count($k)!=count($v)) {$retVal="GRESKA!\nNije unet isti broj birackih mesta i broj dobijenih glasova";}// L56,1-78,2-89,3-,4-78
					
			foreach ($v as $vrednost){
				if (!ctype_digit($vrednost)) {$retVal="GRESKA!\nBroj glasova dodeljen nekoj listi nije numericki podatak. Proverite da li ste svakoj lista upisali glasove!";}//L89,1-98,9-098,8-98,6-970
			}
			
			foreach ($k as $vrednost){
				if (!ctype_digit($vrednost)) {$retVal="GRESKA!\nBroj izborne liste nije numericki podatak. Proverite da li je svaka lista predstavljena svojim rednim brojem na glasackom listicu.";}
			}
			
			@$rezultati_izbora=array_combine($k,$v);
			ksort($rezultati_izbora);

	if ($retVal!="") return $retVal; else return $rezultati_izbora;

}

function checkBrojBirackogMesta($brojBirackogMesta){
	$retVal="";
	if(!ctype_digit($brojBirackogMesta)|| $brojBirackogMesta==""){
		$retVal="GRESKA!\nBiracko mesto nije numericki podatak";
	}else {
		$broj_birackog_mesta=$brojBirackogMesta;//npr. B 4Biraliste,78
		if ($brojBirackogMesta<=0 || 198<=$brojBirackogMesta) $retVal="GRESKA!\nBiracko mesto nije dobro uneto ";
	}
	return $retVal;
}

function upisanih_biraca($br_bir_mes,$connection){

	$sql="SELECT upisano_biraca FROM izbor WHERE redni_broj_izbornog_mesta=$br_bir_mes";
	$results = executeSql($sql,$connection);
	
	$row = $results->fetch_assoc();
	
	$results->free();
	return $row['upisano_biraca'];
}

function suma_glasova($rezultati){
	$suma=0;
	foreach($rezultati as $d){
		$suma+=$d;
	}
	return $suma;
}

function executeSql($sql,$connection){
	if (!$results = $connection->query($sql)) echo "Ne mogu da izvrsim upit zbog [". $connection->error . "]";
	return $results;
}

function da_li_su_svi_dobili_glasove($rezultati,$broj_lista){
	$retVal=true;
	//provera da li je broj podataka jednak broju izbornih lista

	if(count($rezultati)!=$broj_lista) $retVal=false;

	//provera da li je svaki index u opsegu od 0 do broja lista
	foreach ($rezultati as $key=>$value){
		if ($key>$broj_lista || $key<0) {$retVal=false; break;}
	}
	
	return $retVal;
}

function setToDefalultBrojBiraca(){
	require ("./includes/connection.php");
	$sql="SELECT `upisano_biraca`,`default_upisano_biraca` FROM `izbor`";
	$results = executeSql($sql,$connection);
	$newUpisanoBiraca=array();
	$primljenoIzvestaja=0;
	
	while ($row = $results->fetch_assoc()){
		if($row['upisano_biraca']==0) {
			array_push($newUpisanoBiraca,$row['default_upisano_biraca']);
		}else{
			array_push($newUpisanoBiraca,$row['upisano_biraca']);
			$primljenoIzvestaja++;
		}
	}
	$results->free();
	
	//sacuvaj podatak o broju primljenih izvestaja do 9h
	file_put_contents('install/otvoreno_biralista.txt', $primljenoIzvestaja, LOCK_EX);
	
	for($i=0;$i<count($newUpisanoBiraca);$i++){
		$Sql="UPDATE izbor SET upisano_biraca=".$newUpisanoBiraca[$i]." WHERE redni_broj_izbornog_mesta=".($i+1);
		$results = executeSql($Sql,$connection);
	}
	
	require ("./includes/dbcloseconnection.php");
}

function izlaznost(){
	require ("./includes/connection.php");
	$sql="SELECT upisano_biraca,izlaznost_11,izlaznost_15,izlaznost_18,izlaznost FROM izbor";
	$results = executeSql($sql,$connection);
	$biraca=0;
	$izlaznost11=0;
	$izlaznost15=0;
	$izlaznost18=0;
	$izlaznost=0;
	while ($row = $results->fetch_assoc()){
			$biraca+=$row['upisano_biraca'];
			$izlaznost11+=$row['izlaznost_11'];
			$izlaznost15+=$row['izlaznost_15'];
			$izlaznost18+=$row['izlaznost_18'];
			$izlaznost+=$row['izlaznost'];
	}
	
	$biralista = file_get_contents('install/otvoreno_biralista.txt');
	// struktura $retVal = $biraca, nominalno $izlaznost11, nominalno $izlaznost15, nominalno $izlaznost18, nominalno $izlaznost
	$retVal=array($biraca,$biralista,$izlaznost11,$izlaznost15,$izlaznost18,$izlaznost);
	
	$results->free();
	require ("./includes/dbcloseconnection.php");
	
	return $retVal;
	
}
?>