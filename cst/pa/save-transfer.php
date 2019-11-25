<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
if(!trim($_POST['consultationID'])){
	echo "<span class=error-text>No Patient Selected</span>";
	return;
}

if(!in_array($_POST['status'], array(2,3))){
	echo "<span class=error-text>The Transfer Option is not in range</span>";
	return;
}
$consultationID = PDB($_POST['consultationID'], true, $con);
$status = PDB($_POST['status'], true, $con);
// Get the Patient Record ID
$patientRecordID = returnSingleField("SELECT PatientRecordID FROM co_records AS a WHERE a.ConsultationRecordID = '{$consultationID}'","PatientRecordID",true,$con);

// var_dump($patientRecordID);
saveData("UPDATE pa_records SET Status='{$status}' WHERE PatientRecordID = '{$patientRecordID}'",$con);

/* Add the transfer file automaticaly to the patient consumable */
$sql = "SELECT 	a.PatientRecordID AS PatientRecordID,
				b.ConsultantID AS ConsultantID
				FROM pa_records AS a 
				INNER JOIN co_records AS b
				ON a.PatientRecordID = b.PatientRecordID
				WHERE a.PatientRecordID = '{$patientRecordID}'";
$r = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=false,$con);

if(isset($messages[$_POST['status']])){
	echo $messages[$_POST['status']].($_SESSION['user']['UserID'] == $r['ConsultantID']?" <a style='color:red; text-decoration:none;' href='#' onclick='removeTransfer(\"{$r['PatientRecordID']}\");return false;'>Delete</a>":"")."<br />&nbsp;";
}
?>

<script type="text/javascript">
	function removeTransfer(ConsultationRecordID){
		if(ConsultationRecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-transfer.php",
				data: "ConsultationRecordID=" + ConsultationRecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedDecision").html(result)
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>