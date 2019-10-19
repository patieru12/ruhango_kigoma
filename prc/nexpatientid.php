<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("prc" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// Get the Next Patient ID
$sql = "SELECT 	MAX(a.PatientID) AS maxID
				FROM pa_info AS a
				";
// echo $sql;
$lastID = returnSingleField($sql,$field="maxID",true, $con);
// var_dump($lastID);
$lastID++;
echo $lastID;
?>