<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//now save the new information if the query is from ajax
if($_POST['url'] == "ajax"){
	saveData("UPDATE pa_insurance_cards SET Relation='{$_POST['Relation']}' WHERE PatientInsuranceCardsID='{$_POST['PatientInsuranceCardsID']}'", $con);
	echo "OK";
} else{
	echo "No Action Taken";
	// echo returnSingleField("SELECT TicketPaid FROM mu_tm WHERE TicketID='{$_POST['ticketid']}'","TicketPaid", true, $con);
}
?>