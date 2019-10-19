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
	saveData("UPDATE mu_tm SET TicketPaid='{$_POST['tm']}', ReceiptNumber='{$_POST['rcpnumber']}', Type='{$_POST['type']}' WHERE TicketID='{$_POST['ticketid']}'", $con);
	echo $_POST['tm'];
} else{
	echo returnSingleField("SELECT TicketPaid FROM mu_tm WHERE TicketID='{$_POST['ticketid']}'","TicketPaid", true, $con);
}
?>