<?php
session_start();
require_once "../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
if(!$_POST['newService']){
	echo "<span class=error>Please select a service to transfer in.</span>";
	return;
}
// var_dump($_POST); echo "<br />";
/* Create pa_records information */
$PatientRecordID = PDB($_POST['patient'], true, $con);
$ServiceNameID = PDB($_POST['newService'], true, $con);

$pa_records_info = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.*
																		FROM pa_records AS a
																		WHERE a.PatientRecordID = '{$PatientRecordID}'
																		", $con), false, $con);
// var_dump($pa_records_info);
if(!$pa_records_info){
	echo "<span class=error>Invalid Parameter. Please select the patient to be transfered and try again</span>";
	return;
}

$toDayDate = date("Y-m-d", time());
$toDayTime = time();

// Check if the patient has any record the deired service to day
$se_records_info = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.*
																		FROM co_records AS a
																		WHERE a.PatientRecordID = '{$PatientRecordID}' &&
																			  a.Date = '{$toDayDate}'
																		", $con), false, $con);
// var_dump($se_records_info);
if(preg_match("/{$ServiceNameID}, /", $se_records_info['transfer'])){
	echo "<span class=error>The Patient has records in the selected service</span>";
	return;
}

$transferInformation = ($se_records_info['transfer']?$se_records_info['transfer']:"").$ServiceNameID.", ";
// echo $transferInformation;
saveData("UPDATE co_records SET transfer='{$transferInformation}' WHERE ConsultationRecordID='{$se_records_info['ConsultationRecordID']}'",$con);
echo "<span class='success'>The Patient is transfered to the new service.<br />Please info the service attendant for new patient</span>";
die();
?>
<br />
<span class="error">Please This feature is in progress Wait a bit.</span>