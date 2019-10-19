<?php
$today = date("Y-m-d", time());
$closedDays = returnAllData($sql="SELECT * FROM sy_conge WHERE Date='{$today}'",$con);
// var_dump($closedDays);

$weekend_days= array("Sat", "Sun");
$thisDayPrefix = date("D", time());

$currentHour = date("H", time());

$loginString = "";
if($currentHour >= 17 || $currentHour <= 7){
	$loginString = " Night";
} else if( count($closedDays) > 0) {
	$loginString = " Closed Day";
} else if(in_array($thisDayPrefix, $weekend_days)){
	$loginString = " Week-End";
}
// var_dump($_SERVER);


$weekendDefaultConsultation 	= "CPC Week-End";
$dayDefaultConsultation			= "CPC Jour";
//$dayDefaultConsultation			= "CPC Week-End";
$closedDayDefaultConsultation	= "CPC Jour Ferier";
//$dayDefaultConsultation			= "CPC Jour Ferier";
$nightDefaultConsultation		= "CPC Nuit";


// $myNumber = 829054989.795;
// var_dump(getEnglishNumber($myNumber));
?>
