<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
if(!trim($_POST['ConsultationRecordID'])){
	echo "<span class=error-text>No Patient Selected</span>";
	return;
}

$consultationID = PDB($_POST['ConsultationRecordID'], true, $con);

// var_dump($patientRecordID);
saveData("UPDATE pa_records SET Status='0' WHERE PatientRecordID = '{$consultationID}'",$con);
?>