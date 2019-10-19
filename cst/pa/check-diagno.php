<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
$sql = "SELECT 	a.DiagnosticRecordID AS DiagnosticRecordID,
				a.DiagnosticID AS DiagnosticID
				FROM co_diagnostic_records AS a
				WHERE a.ConsulationRecordID = '{$_GET['patientId']}'";
$r = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
if(count($r) > 0){
	echo json_encode(array("Diag"=>true) );
} else{
	echo json_encode(array("Diag"=>false));
}