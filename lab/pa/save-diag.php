<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(!trim($_POST['data'])){
	echo "<span class=error-text>PLease Enter the Data to be used as Diagnostic</span>";
	return;
}

// Check if the Diagnostic Exist in the System Before
$diagID = formatResultSet($rslt=returnResultSet("SELECT a.*
														FROM co_diagnostic AS a
														WHERE a.English = '".PDB($_POST['data'], true, $data)."' ||
															  a.DiagnosticName = '".PDB($_POST['data'], true, $data)."'
														", $con), false, $con);
// var_dump($diagID);
if(!is_array($diagID)){
	// Add the diagnostic to the Lit for later Selection
	saveData("INSERT INTO co_diagnostic SET English='".PDB($_POST['data'], true, $data)."',  DiagnosticName='', DiagnosticCode='', Code='', PECIME='', DiagnosticCategoryID=10, Reported=0, SISCode=''  ", $con);
}
$diagID = formatResultSet($rslt=returnResultSet("SELECT a.*
														FROM co_diagnostic AS a
														WHERE a.English = '".PDB($_POST['data'], true, $data)."' ||
															  a.DiagnosticName = '".PDB($_POST['data'], true, $data)."'
														", $con), false, $con);
// var_dump($diagID);
// Check if this record Exist for th current Patient
$records = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.English AS diagnosticName
															FROM co_diagnostic_records AS a
															INNER JOIN co_diagnostic AS b
															ON a.DiagnosticID = b.DiagnosticID
															WHERE a.ConsulationRecordID = '".PDB($_POST['consultationID'],true,$con)."' &&
																  a.DiagnosticID = '{$diagID['DiagnosticID']}'
															", $con), true, $con);
if(!is_array($records)){
	saveData("INSERT INTO co_diagnostic_records SET ConsulationRecordID='".PDB($_POST['consultationID'],true,$con)."', DiagnosticID={$diagID['DiagnosticID']}, DiagnosticType=1, CaseType=0, PECIME=0", $con);
}
$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*,
															b.English AS diagnosticName
															FROM co_diagnostic_records AS a
															INNER JOIN co_diagnostic AS b
															ON a.DiagnosticID = b.DiagnosticID
															WHERE a.ConsulationRecordID = '".PDB($_POST['consultationID'],true,$con)."'
															", $con), true, $con);
foreach($diagnostic AS $d){
	echo "<div>".$d['diagnosticName']."  <a href='#'>Edit</a> | <a href='#'>Delete</a></div>";
}
?>
