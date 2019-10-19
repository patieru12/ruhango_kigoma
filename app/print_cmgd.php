<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
// var_dump($_REQUEST);
if(!trim($_REQUEST['process_id'])){
	echo "<span class='error-text'>Please Fill in Weight</span>";
	return;
}

$dailyID = PDB($_REQUEST['process_id'], true, $con);
$types = array("2018200001"=>1, "2018200002"=>2, "2018200003"=>3);
$type = $types[$dailyID];

$sql = "SELECT 	a.id AS commandID,
				a.commandInfo AS commandInfo
				FROM sy_print_command AS a
				WHERE a.printerID = '{$dailyID}' && status = 0 && type={$type}
				ORDER BY submittedOn ASC";
$patientInfo = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
// var_dump($patientInfo);
saveData("UPDATE sy_print_command SET status=1 WHERE printerID = '{$dailyID}' AND type={$type}", $con);
echo json_encode($patientInfo);
?>