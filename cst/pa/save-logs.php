<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
if(!trim($_POST['patientID'])){
	echo "<span class=error-text>No Patient Selected</span>";
	return;
}
if(!trim($_POST['field'])){
	echo "<span class=error-text>no where to save data</span>";
	return;
}

if(!in_array($_POST['value'], $cst_data[$_POST['field']])){
	echo "<span class=error-text>The Selected option is not valid.</span>";
	return;
}

// Get the Patient Record ID
$patientRecordID= PDB($_POST['patientID'], true, $con);
$field 			= PDB($_POST['field'], true, $con);
$value 			= PDB($_POST['value'], true, $con);

// var_dump($patientRecordID);
saveData("UPDATE pa_records SET `{$field}`='{$value}' WHERE PatientRecordID = '{$patientRecordID}'",$con);
?>