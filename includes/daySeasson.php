<?php
	$electionDay='2016-04-24';
	$Time7h = new DateTime($electionDay .' 7:00:00');
	$Time9h = new DateTime($electionDay .' 9:00:00');
	$Time11h = new DateTime($electionDay .' 11:00:00');
	$Time15h = new DateTime($electionDay .' 15:00:00');
	$Time18h = new DateTime($electionDay .' 18:00:00');
	$Time20h = new DateTime($electionDay .' 20:00:00');
	$Time24h = new DateTime($electionDay .' 23:59:00');

	$datetimeNow = new DateTime();
	$daySesson=6;
	//daySesson
		if ($datetimeNow>$Time7h) $daySesson=9;
		if ($datetimeNow>$Time9h) $daySesson=11;
		if ($datetimeNow>$Time11h) $daySesson=15;
		if ($datetimeNow>$Time15h) $daySesson=18;
		if ($datetimeNow>$Time18h) $daySesson=19;
		if ($datetimeNow>$Time20h) $daySesson=20;
		if ($datetimeNow>$Time24h) $daySesson=24;
		
//constants
define('LOKALNIH_LISTA', 16);
define('POKRAJINSKIH_LISTA', 15);
define('REPUBLICKIH_LISTA', 20);
define('BIRACKIH_MESTA', 197);
define('LOKALNIH_MANDATA', 78);
define('CENZUS_ZA_LISTE',5);
define('CENZUS_ZA_LISTE_NACIONALNIH_MANJINA',3);

?>