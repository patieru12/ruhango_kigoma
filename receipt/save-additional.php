<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
// Delete every record related to the current record
// saveData("DELETE FROM sy_records WHERE PatientRecordID='{$_POST['patientID']}'", $con);
$allowedCharges = array();
foreach($_POST AS $key=>$p){
	if(is_numeric($key) && !in_array($key, $allowedCharges)){
		$allowedCharges[] = $key;
	}
	// Check if the product already added
	$r = returnSingleField("SELECT * FROM sy_records WHERE PatientRecordID='{$_POST['patientID']}' && ProductPriceID='{$key}'", "id", true, $con);
	if(!$r && is_numeric($key)){
		$time = time();
		saveData("INSERT INTO sy_records SET PatientRecordID='{$_POST['patientID']}', ProductPriceID='{$key}', PerformedOn='{$time}', receivedBy='{$_SESSION['user']['UserID']}', status=0", $con);
	}
}

// Delete any record which is not included in the allowedCharges array
$allowedChargesString = implode(",", $allowedCharges);
$notInCondition = $allowedChargesString?" && ProductPriceID NOT IN({$allowedChargesString})":"";
saveData("DELETE FROM sy_records WHERE PatientRecordID='{$_POST['patientID']}'{$notInCondition}  && status=0", $con);
?>
<span class=success>Selected Additional Added Saved</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		LoadProfile("<?= $_POST['patientID'] ?>");
	},200);
</script>