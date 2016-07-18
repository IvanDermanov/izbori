<?php
// +381642304546\tPoruka sa web Servera<eol>;
// upozorenje!!! uvek zavrsiti red bez <eol> na kraju ako nema vise poruka
// npr
// 0\t+381642304546\tPoruka sa web Servera<eol>;
// 1\t+381641153086\tPoruka sa web Servera;

@$c=$_POST["code"];

if ($c==37){
	require("./includes/connection.php");
	$sql="SELECT * FROM sms_za_slanje WHERE confirmed=0";
	
	if (!$results = $connection->query($sql)) echo "Ne mogu da izvrsim upit zbog [". $connection->error . "]";
	
	$view="SEND<eol>";
	// ako ima poruka za slanje napravi niz
	if ($results->num_rows>0){
	
		for($i=0;$i<$results->num_rows;$i++){
		// preuzmi zapis u $row asocijativni niz
			$row = $results->fetch_assoc();

			$view.=$row['sms_key']."\t".$row['mobilni']."\t".$row['message']."<eol>";
		}
		
		$results->free();
		
	}
		
		$view=trim($view,"<eol>");
	
	
	require("./includes/dbcloseconnection.php");	
}
?>
