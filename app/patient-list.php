<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
$dailyID = $_GET['dailyID'];
$dateIn = date("Y-m-d", time());
// Get the Next Patient ID
$sql = "SELECT 	b.dailyID AS PatientID,
				a.Name AS Name,
				a.Sex AS Sex,
				a.DateofBirth AS DateofBirth,
				a.FamilyCode AS FamilyCode,
				COALESCE(b.InsuranceCardID,'') AS InsuranceCardID
				FROM pa_info AS a
				LEFT JOIN pa_records AS b
				ON a.PatientID = b.PatientID
				WHERE b.dailyID = '{$dailyID}' && b.DateIN = '{$dateIn}'
				";
// echo $sql;
$patients = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
// var_dump($lastID);
for($i=0; $i<count($patients); $i++){
	$patients[$i]['DateofBirth'] = getAge($patients[$i]['DateofBirth'],$notation=1, $current_date=date("Y-m-d", time()));
}
if(is_array($patients)){
	echo json_encode($patients);
} else{
	echo json_encode(array(array()));
}
?>