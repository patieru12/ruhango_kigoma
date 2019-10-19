<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
// Get the Next Patient ID
$sql = "SELECT 	MAX(a.PatientID) AS maxID
				FROM pa_info AS a
				";
// echo $sql;
$lastID = returnSingleField($sql,$field="maxID",true, $con);
// var_dump($lastID);
$lastID++;
$data = array(array("lastID"=>$lastID));
echo json_encode($data);
?>