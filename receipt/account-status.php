<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$patientID = $_GET['PatientRecordID'];
$debtInfor = formatResultSet($rslt=returnResultSet("SELECT 	*
															FROM sy_debt_records AS a
															WHERE a.PatientRecordID = '{$patientID}' &&
																  a.accountantID IS NOT NULL
															", $con), false, $con);

// var_dump($debtInfor);
if(count($debtInfor) > 0){
	echo "OK";
} else {
	echo "Please Wait...";
}
?>