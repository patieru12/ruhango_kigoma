<?php
session_start();
require_once "../lib/db_function.php";
// var_dump($_POST);
$patientID = $_POST['PatientRecordID'];
// Check if the consultation RecordExist
$record = formatResultSet($rslt=returnResultSet("SELECT a.*
														FROM co_records AS a
														WHERE a.PatientRecordID = '{$patientID}'
														",$con),$multirows=false,$con);
// var_dump($record);
if(count($record) == 0){
	// Create one
	saveData("INSERT INTO co_records SET PatientRecordID='{$patientID}', Date=NOW(), ConsultationPriceID='{$_POST['newConsultation']}', ConsultantID=0, RegisterNumber=''", $con);
} else{
	// Update the Existing Record
	saveData("UPDATE co_records SET ConsultationPriceID='{$_POST['newConsultation']}' WHERE ConsultationRecordID='{$record['ConsultationRecordID']}'",$con);
}
?>
<span class=success>Consultation Record Changed</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		LoadProfile("<?= $patientID ?>");
	},400);
</script>