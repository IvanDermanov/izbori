<?php
require("./includes/daySeasson.php");

@$page=$_GET['page'];
	switch($page){
		case "aktivnosti":
			include "view.php";
			$view=str_replace("<!-- SAT -->", insertClock() ,$view);
			$view=setActivity($view,$daySesson);
			$view=setTitle("Aktivnosti u toku izbora",$view);
			break;
		case "izlaznost":
			include "view.php";
			$view=makeBirackaMestaList("install/izborna_mesta_Novi_Sad.txt",$view,$daySesson);
			$view=setTitle("Izlaznost",$view);
			break;
		case "lokalni-rezultati-po-birackim-mestima":
			include "view.php";
			$view=makeRezultatiTabela("install/izborna_mesta_Novi_Sad.txt","lokalni",$view);
			$view=setTitle("Rezultati u Novom Sadu za gradski parlament po biračkim mestima",$view);
			break;
		case "lokalni-rezultati-po-listama":
			include "view.php";
			$view=makeRezultatiGraph("lokalni",$view);
			$view=setTitle("Rezultati u Novom Sadu za gradski parlament po listama",$view);
			break;
		case "pokrajinski-rezultati-po-birackim-mestima":
			include "view.php";
			$view=makeRezultatiTabela("install/izborna_mesta_Novi_Sad.txt","pokrajinski",$view);
			$view=setTitle("Rezultati u Novom Sadu za pokrajinski parlament po biračkim mestima",$view);
			break;
		case "pokrajinski-rezultati-po-listama":
			include "view.php";
			$view=makeRezultatiGraph("pokrajinski",$view);
			$view=setTitle("Rezultati u Novom Sadu za pokrajinski parlament po listama",$view);
			break;
		case "republicki-rezultati-po-birackim-mestima":
			include "view.php";
			$view=makeRezultatiTabela("install/izborna_mesta_Novi_Sad.txt","republicki",$view);
			$view=setTitle("Rezultati u Novom Sadu za republički parlament po biračkim mestima",$view);
			break;
		case "republicki-rezultati-po-listama":
			include "view.php";
			$view=makeRezultatiGraph("republicki",$view);
			$view=setTitle("Rezultati u Novom Sadu za republički parlament po listama",$view);
			break;
		case "izborne-liste-lokalni":
			include "view.php";
			$view=makeList("install/gradske_izborne_liste.txt",$view);
			$view=setTitle("Izborne liste za gradski parlament u Novom Sadu",$view);
			break;
		case "izborne-liste-pokrajinski":
			include "view.php";
			$view=makeList("install/pokrajinske_izborne_liste.txt",$view);
			$view=setTitle("Izborne liste za pokrajinski parlament",$view);
			break;
		case "izborne-liste-republicki":
			include "view.php";
			$view=makeList("install/republicke_izborne_liste.txt",$view);
			$view=setTitle("Izborne liste za republički parlament",$view);
			break;
		case "o-nama":
			include "view.php";
			$view=setTitle("Tim koji je doprineo stvaranju ovog softvera",$view);
			break;
		case "get":
			require ("./includes/get.php");
			break;
		case "post":
			require ("./includes/post.php");
			$view="WRITEN";
			break;
		case "conf":
			require ("./includes/conf.php");
			break;
		default :
			$page='aktivnosti';
			include "view.php";
			$view=str_replace("<!-- SAT -->", insertClock() ,$view);
			$view=setActivity($view,$daySesson);
			break;
	}
echo $view;
?> 
