<?php
session_start();
require_once "../lib/db_function.php";
// var_dump($_POST);
$patientID = $_POST['PatientRecordID'];
if(!@$_POST['tm']){
	?>
	<span class=error>Is TM Paid or not!</span>
	<?php
	return;
}
// Check if the consultation RecordExist
$tm_private = NULL;
$ins 		= $_POST['ins'];
$tmID = returnSingleField("SELECT a.TicketID FROM mu_tm AS a WHERE a.PatientRecordID='{$patientID}'", "TicketID", true, $con);
$tmAmount = ($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])));
$additionalField = ($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"");

if(!$tmID){
	saveAndReturnID($stm = "INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='{$tmAmount}', ReceiptNumber='{$_POST['receipt_number']}'{$additionalField}, Date='{$_POST['Date']}', UserID='{$_SESSION['user']['UserID']}', status=0", $con);
} else{
	saveData("UPDATE mu_tm SET TicketPaid='{$tmAmount}', ReceiptNumber='{$_POST['receipt_number']}'{$additionalField}, Date='{$_POST['Date']}', UserID='{$_SESSION['user']['UserID']}', status=0 WHERE TicketID='{$tmID}'",$con);
}

/*$record = formatResultSet($rslt=returnResultSet("SELECT a.*
														FROM co_records AS a
														WHERE a.PatientRecordID = '{$patientID}'
														",$con),$multirows=false,$con);*/

?>
<span class=success>New TM Information Recorded.</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		LoadProfile("<?= $patientID ?>");
	},400);
</script>