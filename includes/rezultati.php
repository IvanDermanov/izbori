<?php
//Brojevi (promenljive) koji se koriste za neku partiju su:
//V - ukupan broj glasova koji je dobila neka parija
//	- broj mjesta u parlamentu
//	- "trenutni broj" vezan za neku partiju - koristi se u racunanju
//	- minimalni procenat za ulazak u parlament . U SR je to 5%.
//S - broj do sada izabranih poslanika (u pocetku je ovaj broj nula) svake partije

//K=V/S+1
//Postupak je sledeci:
//1. Svakoj partiji se na pocetku "trenutni broj" postavi na V - broj glasova koje je osvoila
//2. Nadje se partija koja ima najveci "trenutni broj"
//3. Toj partiji se dodijeli jos jedan poslanik (broj S - te partije se uvaca za 1)
//4. "trenutni broj" te partije je V/(S+1)
//5. Ako je broj do sada izabranih poslanika svih partija manji od broja mjesta u parlamentu (81) 
//postupak se nastavlja od koraka 2.

//У овом примеру, осам мандата се расподељује на четири странке:

//			Странка А 	Странка Б 	Странка В 	Странка Г
//гласова 	30.000 		26.000 		18.000 		13.000

//место 1 	30.000 		26.000 		18.000 		13.000		s=1		1	0	0	0
//место 2 	15.000 		26.000 		18.000 		13.000		s=2		1	1	0	0
//место 3 	15.000 		13.000 		18.000 		13.000		s=3		1	1	1	0
//место 4 	15.000 		13.000 		9.000 		13.000		s=4		2	1	1	0
//место 5 	10.000 		13.000 		9.000 		13.000		s=5		
//место 6 	10.000 		8.666 		9.000 		13.000		s=6
//место 7 	10.000 		8.666 		9.000 		6.500		s=7
//место 8 	7.500 		8.666 		9.000 		6.500		s=8

//мандата 	3 			2 			2 			1

// define('CENZUS_ZA_LISTE',5);
// define('CENZUS_ZA_LISTE_NACIONALNIH_MANJINA',3);

// Ukazni podatak je niz stranka osvojeni glasovi   $glasovi=array("StrankaA"=>30000,"StrankaB"=>26000,"StrankaC"=>18000,"StrankaD"=>13000);
// Ukazni podatak je broj mandata koji se deli	$brojMandata=8;
// Izlazni podatak su je lista sa mandatima  $mandati=array("StrankaA"=>30000,"StrankaB"=>26000,"StrankaC"=>18000,"StrankaD"=>13000);
function dOnt($glasovi,$ukupno_mandata){

	$glasovi=filterCenzus($glasovi);
	if (!$glasovi) return NULL;
	arsort($glasovi);

	//1. Svakoj partiji se na pocetku "trenutni broj" postavi na V - broj glasova koje je osvoila
		$trenutni=$glasovi;
		
	//postavi s svakoj partiji S - broj do sada izabranih poslanika (u pocetku je ovaj broj nula) svake partije
		$s=$glasovi;
		foreach($glasovi as $partija=>$glasova){			
			$s[$partija]=0;
		}
		
	//*************MODIFIKACIJA broj raspodeljenih mandata se cuva u promenljivoj koja pocinje sa odbrojavanjem od broja partije koje su presle cenzus jer svaka od tih partija je dobila po jedan mandat
	$raspodeljenoMandata=raspodeljenoMandata($s);
	do{

		//2. Nadje se partija koja ima najveci "trenutni broj" glasova
			$glasova = max($trenutni);
			$partija = array_search($glasova, $trenutni);
		
		//3. Toj partiji se dodijeli jos jedan poslanik (broj S - te partije se uvaca za 1)	
			$s[$partija]+=1;
		
		//4. "trenutni broj" glasova te partije je V/(S+1)
			$trenutni[$partija]=$glasovi[$partija]/($s[$partija]+1);
		
		//5. Ako je broj do sada izabranih poslanika svih partija manji od broja mesta u parlamentu idi na tacku 2
		$raspodeljenoMandata++;
	}while($raspodeljenoMandata<$ukupno_mandata);
	
		return $s;
}

function raspodeljenoMandata($s){
	$mandati=0;
	
	foreach($s as $mandatStranke){
		$mandati+=$mandatStranke;
	}

	return $mandati;
}

function filterCenzus ($glasovi){
// CENZUS_ZA_LISTE
// CENZUS_ZA_LISTE_NACIONALNIH_MANJINA

	//algoritam dodaje partije koje su presle cenzus
	$ukupnoGlasova=array_sum($glasovi);

	if ($ukupnoGlasova>0){
	
		$prekoCenzusa=array();
		$cenzusPartija=array();
		foreach($glasovi as $partija => $glasoviPartije){
		
			// ako partija u nazivu na prvom mestu ima zvezdicu radi se o manjinskoj stranci na osnovu resenja izborne komisije
			if (is_manjinska($partija)) {$cenzus=CENZUS_ZA_LISTE_NACIONALNIH_MANJINA;} else {$cenzus=CENZUS_ZA_LISTE;}
			
			if((($glasoviPartije/$ukupnoGlasova)*100)>$cenzus) {
				array_push($prekoCenzusa,$glasoviPartije);
				array_push($cenzusPartija,$partija);
			}
		}
		// vraca se niz gde su indeksi broj sa liste kao index a vrednost je broj glasova koje je ta partija osvojila
		return array_combine($cenzusPartija, $prekoCenzusa);
	}
	return false;

}

function is_manjinska($partija){
	$manjinska=false;
	
	$arrNazivPartije = str_split(trim($partija));
	
	if ($arrNazivPartije=="*") $manjinska=true;
	
	return $manjinska;
	
}
?>