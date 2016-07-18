<?php
	$file = 'templates/template.tmp';
	
	// Open the file to get template content
	$view = file_get_contents($file);
	
	$file = 'templates/meni.tmp';
	
	// Open the file to get meni content
	$meni = file_get_contents($file);
	
	$file = 'templates/'.$page.'.txt';
	
	// Open the file to get page content
	$pageContent = file_get_contents($file);
	
	//$daySesson vremenska linija tokom dana
	//$page tekst koji identifikuje stranicu koja ce se prikazati
	//$view fajl koji sadrzi template i koji ce se na kraju prikazati
	//$meni meni na stranici
	//$pageContent sadrzaj koji ce se prikazati na stranici
	
	//return meni on page
	$view=str_replace("|meni|", customizeMeni ($meni,$page,$daySesson),$view);
	//return tekst on page
	$view=str_replace("|tekst|", $pageContent ,$view);

function customizeMeni ($meni,$page,$daySesson){
		
		if ($daySesson>19){
				$file = 'templates/20.txt';
	
				// Open the file to get page content
				$meni20 = file_get_contents($file);
				
				//dodaj u meni rezultate izbora
				$meni = str_replace("<!-- |20| -->", $meni20, $meni);

		}
		
		//postavi class="active" u aktivni meni
		$meni=str_replace("class=\"".$page."\"","class=\"active\"",$meni);
		
		if(strpos("izborne-liste-lokalni|izborne-liste-pokrajinski|izborne-liste-republicki", $page) !== false) $meni=str_replace("class=\"izborne-liste-lokalni|izborne-liste-pokrajinski|izborne-liste-republicki\"","class=\"active\"",$meni);
		if(strpos("lokalni-rezultati-po-birackim-mestima|lokalni-rezultati-po-listama", $page) !== false) $meni=str_replace("class=\"lokalni-rezultati-po-birackim-mestima|lokalni-rezultati-po-listama\"","class=\"active\"",$meni);
		if(strpos("pokrajinski-rezultati-po-birackim-mestima|pokrajinski-rezultati-po-listama", $page) !== false) $meni=str_replace("class=\"pokrajinski-rezultati-po-birackim-mestima|pokrajinski-rezultati-po-listama\"","class=\"active\"",$meni);
		if(strpos("republicki-rezultati-po-birackim-mestima|republicki-rezultati-po-listama", $page) !== false) $meni=str_replace("class=\"republicki-rezultati-po-birackim-mestima|republicki-rezultati-po-listama\"","class=\"active\"",$meni);
		return $meni;
}

function insertClock(){
	$file = 'templates/clock.txt';

	// Open the file to get page content
	return file_get_contents($file);
}

function setActivity($view,$daySesson){

	for ($i = 0; $i <= 24; $i++) {
	
		if ($i<$daySesson) {$aktivnost='Završeno';$cssAktivnosti='tg-lqy6-Zavrseno';}
		if ($i==$daySesson) {$aktivnost='U toku';$cssAktivnosti='tg-lqy6-U-toku';}
		if ($i>$daySesson) {$aktivnost='Na čekanju';$cssAktivnosti='tg-lqy6-Cekanje';}
		
		$view=str_replace("|$i|", $aktivnost, $view);
		$view=str_replace("|css$i|", $cssAktivnosti, $view);
	}
	
	$view=Generate_Reports($view,$daySesson);

	return $view;
}
function Generate_Reports($view,$daySesson){
	require_once ("model.php");

	if ($daySesson>9) $view=str_replace("btn9.style.display = \"none\";", "", $view);
	if ($daySesson>11) $view=str_replace("btn11.style.display = \"none\";", "", $view);
	if ($daySesson>15) $view=str_replace("btn15.style.display = \"none\";", "", $view);
	if ($daySesson>18) $view=str_replace("btn18.style.display = \"none\";", "", $view);
	if ($daySesson>20) $view=str_replace("btn20.style.display = \"none\";", "", $view);
	//izlaznost return($biraca,$biralista,$izlaznost11,$izlaznost15,$izlaznost18,$izlaznost)
	$izlaznostReport=izlaznost();
	
	try {
		$biralistaProcenti=@round(100*$izlaznostReport[1]/BIRACKIH_MESTA,2);
	} catch (Exception $e) {
		$biralistaProcenti=0;
	}
	
	try {
		$izlaznostProcenti11=@round(100*$izlaznostReport[2]/$izlaznostReport[0],2);
	} catch (Exception $e) {
		$izlaznostProcenti11=0;
	}
	
	try {
		$izlaznostProcenti15=@round(100*$izlaznostReport[3]/$izlaznostReport[0],2);
	} catch (Exception $e) {
		$izlaznostProcenti15=0;
	}
	
	try {
		$izlaznostProcenti18=@round(100*$izlaznostReport[4]/$izlaznostReport[0],2);
	} catch (Exception $e) {
		$izlaznostProcenti18=0;
	}
	
	try {
		$izlaznostProcenti=@round(100*$izlaznostReport[5]/$izlaznostReport[0],2);
	} catch (Exception $e) {
		$izlaznostProcenti=0;
	}
	
	
	$view=str_replace("|do9|", $izlaznostReport[1], $view);
	$view=str_replace("|do9%|", $biralistaProcenti, $view);
	
	$view=str_replace("|do11|", $izlaznostReport[2], $view);
	$view=str_replace("|do11%|", $izlaznostProcenti11, $view);
	
	$view=str_replace("|do15|", $izlaznostReport[3], $view);
	$view=str_replace("|do15%|", $izlaznostProcenti15, $view);
	
	$view=str_replace("|do18|", $izlaznostReport[4], $view);
	$view=str_replace("|do18%|", $izlaznostProcenti18, $view);
	
	$view=str_replace("|do20|", $izlaznostReport[5], $view);
	$view=str_replace("|do20%|", $izlaznostProcenti, $view);
	
	return $view;
}

function makeList($fileName, $view){
	$arrLista=explode("\r",file_get_contents($fileName));
	$Lista="";
	
	foreach ($arrLista as $arrListaItem) {
		$Lista.="<li class='lista'>".$arrListaItem."</li>\r\n";
	}

	return str_replace("|<li class='lista'>lista</li>|", $Lista, $view);

}

function makeBirackaMestaList($fileName, $view, $daySesson){
	
	$arrLista=explode("\r\n",file_get_contents($fileName));
	
	$lstAll="";
	$biralista="";
	$i=0;
	
//upit baze podataka o podacima

	 require_once ("model.php");

	 $tabela_izlaznost=tabela_izbor();
	 
	foreach ($arrLista as $arrListaItem) {
		$Lista=explode("\t",$arrListaItem);
		if (count($Lista)>1){
			$Lista[3]=trim($Lista[3]," ");
			$Lista[4]=trim($Lista[4]," ");
			
			$zajednickaIzbornaMesta=explode(",",$Lista[0]);
			
			foreach ($zajednickaIzbornaMesta as $izbMes){

				 $lstAll.="<tr id=anchr$i>\r\n";
					 $lstAll.="<td class='tg-yw4l'>$izbMes<a href='#'><img src='./themes/assets/images/map.png'></a></td>\r\n";
					 $lstAll.="<td class='tg-yw4l'><button onclick='newLocation($Lista[1], $Lista[2])'>$Lista[3]</button></td>\r\n";
					 $lstAll.="<td class='tg-yw4l'>".$tabela_izlaznost[(int)$izbMes][0]."</td>\r\n";
					 $lstAll.="<td class='tg-yw4l'>".$tabela_izlaznost[(int)$izbMes][1]."</td>\r\n";
					 $lstAll.="<td class='tg-yw4l'>".$tabela_izlaznost[(int)$izbMes][2]."</td>\r\n";
					 $lstAll.="<td class='tg-yw4l'>".$tabela_izlaznost[(int)$izbMes][3]."</td>\r\n";
					 $lstAll.="<td class='tg-yw4l'>".$tabela_izlaznost[(int)$izbMes][4]."</td>\r\n";
					 $lstAll.="<td class='tg-yw4l'>".@round((($tabela_izlaznost[(int)$izbMes][4]/$tabela_izlaznost[(int)$izbMes][0])*100),2)."%</td>\r\n";
				 $lstAll.="</tr>\r\n";
			}

			$i++;
			$biralista.="[\"$Lista[0]. $Lista[3] - $Lista[4]\", $Lista[1], $Lista[2]],\n";
			
		}
	}
	
	$biralista=trim($biralista,",\n");
	
	$view=str_replace("<!-- |table row| -->", $lstAll, $view);
	$view=str_replace("|biralista|", $biralista, $view);
	checkToSetDefaulBrojBiraca($daySesson);
	return $view;

}

function makeRezultatiTabela ($fileNameBiralista, $vrstaIzbora, $view){
	$arrLista=explode("\r\n",file_get_contents($fileNameBiralista));
	// iz txt fajla vadim samo naziv birackog mesta!!! treba popraviti ovo!!!
	
	$lstAll="";
	$biralista="";
	
//upit baze podataka o podacima

	 require ("model.php");
	 $tabela_izlaznost=tabela_izbor();

	 
	foreach ($arrLista as $arrListaItem) {
		$Lista=explode("\t",$arrListaItem);
		if (count($Lista)>1){
			$Lista[3]=trim($Lista[3]," ");
			
			$zajednickaIzbornaMesta=explode(",",$Lista[0]);
			
			foreach ($zajednickaIzbornaMesta as $izbMes){

				 $lstAll.="<tr>\r\n";
					 $lstAll.="<td class=\"tg-yw4l-rez\">$izbMes</td>\r\n";
					 $lstAll.="<td class=\"tg-yw4l-rez\">$Lista[3]</td>\r\n";
					 switch ($vrstaIzbora){
						 case "lokalni":
							$lstAll.=rezultatNaBirackomMestu($tabela_izlaznost[(int)$izbMes][5],LOKALNIH_LISTA);
							$spanNum=LOKALNIH_LISTA;
						 break;
						 case "pokrajinski":
							$lstAll.=rezultatNaBirackomMestu($tabela_izlaznost[(int)$izbMes][6],POKRAJINSKIH_LISTA);
							$spanNum=POKRAJINSKIH_LISTA;
						 break;
						 case "republicki":
							$lstAll.=rezultatNaBirackomMestu($tabela_izlaznost[(int)$izbMes][7],REPUBLICKIH_LISTA);
							$spanNum=REPUBLICKIH_LISTA;
						 break;
					 }
				 $lstAll.="</tr>\r\n";
			}
			
		}
	}
	
	$view=str_replace("<!--- |header lista kandidata| --->", listaKandidata($vrstaIzbora), $view);
	$view=str_replace("<!-- |table row| -->", $lstAll, $view);
	$view=str_replace("|spanNum|", $spanNum+2, $view);
	
	return $view;
}
function listaKandidata ($vrstaIzbora){
	switch ($vrstaIzbora){
		 case "lokalni":
			$fileName="install/gradske_izborne_liste.txt";
		 break;
		 case "pokrajinski":
			$fileName="install/pokrajinske_izborne_liste.txt";
		 break;
		 case "republicki":
			$fileName="install/republicke_izborne_liste.txt";
		 break;
	}
	
	$arrLista=explode("\r",file_get_contents($fileName));
	$Lista="";
	
	foreach ($arrLista as $arrListaItem) {
		$Lista.="<td class=\"tg-baqh-rez\">$arrListaItem</td>";
	}
	
	return $Lista;
}

function rezultatNaBirackomMestu($rezultati,$brojLista){

	$Lista=explode(",",$rezultati);

	if(count($Lista)!=$brojLista) $Lista=bezPodatakaORezultatima($brojLista);
	$retVal=array();
	$retKey=array();
	//procitaj iz stringa, zapis potom ga indeksiraj pa slozi po kljucu
	for ($i=0;$i<$brojLista;$i++){
		$splited_rezultat=explode("-",$Lista[$i]);
		if (is_numeric($splited_rezultat[1])){
			array_push($retKey,$splited_rezultat[0]);
			array_push($retVal,$splited_rezultat[1]);
		}else{
			array_push($retKey,$i+1);
			array_push($retVal,"N/A");
		}
	}

	$arrRezultat = array_combine($retKey, $retVal);
	ksort($arrRezultat);

	//ubaci u html sortiran zapis po kljucu
	$retVal="";
	foreach($arrRezultat as $rez){
		$retVal.="<td class=\"tg-yw4l-rez\">$rez</td>\r\n";
	}

	return $retVal;
}

function bezPodatakaORezultatima($brojLista){
	$retVal="";
	for ($i=0;$i<$brojLista;$i++) $retVal.="-,";
	 return explode(",",(trim($retVal,",")));
}

function makeRezultatiGraph($vrstaIzbora,$view){
	//Napravi listu partija
	require ("model.php");
	$gutterLeft=65;
	$procenat=0;
	
	//Napravi tabelu rezultata po birackim mestima
	$rezultati_po_biralistima=tabela_rezultati($vrstaIzbora,tabela_izbor());
	//Saberi rezultate
	$zbirni_rezultat=saberiPoPartijamaGlasove($vrstaIzbora,$rezultati_po_biralistima);
	// ako su lokalni izbori onda ukljuci Dhont obracun
	if($vrstaIzbora=="lokalni"){
		//urezultati.php je Dhont
		require("./includes/rezultati.php");
		$dOnt=dOnt($zbirni_rezultat,LOKALNIH_MANDATA);
		if (count($dOnt)>0)$zbirni_rezultat=$dOnt;
		$gutterLeft=30;
	}

	//Prikazi stranke i glasove u grafikonu
	$strRezultat="";
	$strStranke="";
	$strLegenda="";
	$legenda=makeAllLegenda($vrstaIzbora);

	foreach($zbirni_rezultat as $key=>$rezultat){
		$strStranke.=$key.",";
		$strRezultat.=$rezultat.",";
		$strLegenda.="<p>$key ".trim($legenda[$key])."</p>\n";
	}
	
	$strRezultat=trim($strRezultat,",");	
	$strStranke=trim($strStranke,",");
		
	$view=str_replace("|mandati|", $strRezultat, $view);
	$view=str_replace("|liste|", $strStranke, $view);
	$view=str_replace("|gutter|", $gutterLeft, $view);
	$view=str_replace("|procenata|", procenatUnetihPodataka($rezultati_po_biralistima,$vrstaIzbora), $view);
	$view=str_replace("|legenda|", $strLegenda, $view);
	return $view;
}

function tabela_rezultati($vrstaIzbora,$tabelaIzlaznost){
	$rezultati=array();
	
	foreach($tabelaIzlaznost as $rezultatGlasanjaPoBirackomMestu){
		
		switch ($vrstaIzbora){
			case "lokalni":
				array_push($rezultati,numerickiRezultatNaBirackomMestu($rezultatGlasanjaPoBirackomMestu[5],LOKALNIH_LISTA));
				break;
			case "pokrajinski":
				array_push($rezultati,numerickiRezultatNaBirackomMestu($rezultatGlasanjaPoBirackomMestu[6],POKRAJINSKIH_LISTA));
				break;
			case "republicki":
				array_push($rezultati,numerickiRezultatNaBirackomMestu($rezultatGlasanjaPoBirackomMestu[7],REPUBLICKIH_LISTA));
				break;
		}
	}

	return $rezultati;
	
}

function numerickiRezultatNaBirackomMestu($rezultati,$brojLista){
	$Lista=explode(",",$rezultati);
	if(count($Lista)!=$brojLista) $Lista=bezNumerickihPodatakaORezultatima($brojLista);
	$retVal=array();
	$retKey=array();
	for ($i=0;$i<$brojLista;$i++){
		$splited_rezultat=explode("-",$Lista[$i]);
		array_push($retKey,$splited_rezultat[0]);
		array_push($retVal,$splited_rezultat[1]);
	}
	
	$arrRezultat = array_combine($retKey, $retVal);
	ksort($arrRezultat);

	return $arrRezultat;
}

function procenatUnetihPodataka($rezultati){

	$retVal=0;
	foreach($rezultati as $rezultatNaBirackomMestu){
		if(array_sum($rezultatNaBirackomMestu)>0) $retVal++;
	}
	
	return round(($retVal/BIRACKIH_MESTA)*100,0);
}
function bezNumerickihPodatakaORezultatima($brojLista){
	$retVal="";
	for ($i=1;$i<=$brojLista;$i++) $retVal.="$i-0,";
	 return explode(",",(trim($retVal,",")));
}

function saberiPoPartijamaGlasove($vrstaIzbora,$rezultati_po_biralistima){
	$brojLista=brojLista($vrstaIzbora);
	$zbirni_rezultat=array_fill(1, $brojLista, 0);
	
	foreach ($rezultati_po_biralistima as $rezultatNaBiralistu){

		foreach($rezultatNaBiralistu as $key=>$val){
			$zbirni_rezultat[$key]+=$val;
		}
	}

	return $zbirni_rezultat;
	
}
function brojLista($vrstaIzbora){
	switch ($vrstaIzbora){
		case "lokalni":
			$brojLista=LOKALNIH_LISTA;
			break;
		case "pokrajinski":
			$brojLista=POKRAJINSKIH_LISTA;
			break;
		case "republicki":
			$brojLista=REPUBLICKIH_LISTA;
			break;
	}
	return $brojLista;
}

function makeAllLegenda($vrstaIzbora){
	switch ($vrstaIzbora){
		case "lokalni":
			$brojLista=LOKALNIH_LISTA;
			$fileName='install/gradske_izborne_liste.txt';
			break;
		case "pokrajinski":
			$brojLista=POKRAJINSKIH_LISTA;
			$fileName='install/pokrajinske_izborne_liste.txt';
			break;
		case "republicki":
			$brojLista=REPUBLICKIH_LISTA;
			$fileName='install/republicke_izborne_liste.txt';
			break;
	}

	$arrLista=explode("\r",file_get_contents($fileName));
	$lista=array();
	$listaKey=array();
	foreach ($arrLista as $key=>$arrListaItem) {
		array_push($lista,$arrListaItem);
		array_push($listaKey,$key+1);
	}
	$arrRezultat = array_combine($listaKey, $lista);
	return $arrRezultat;
}

function checkToSetDefaulBrojBiraca($daySesson){

	$indikator = file_get_contents('install/indikator.txt');
	if ($indikator!='true'){
		//prepravi broj biraca na default posle 9 sati
		if ($daySesson>9){
			set_time_limit(90);
			setToDefalultBrojBiraca();
			file_put_contents('install/indikator.txt', 'true', LOCK_EX);
		}
	}
}

function setTitle($title, $view){
	$view=str_replace("|naslov|", $title, $view);
	return $view;
}

?>
