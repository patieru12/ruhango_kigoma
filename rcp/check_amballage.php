<?php
session_start();
//check if the medecine is checked for this session
if(in_array($_POST['mdname'],$_SESSION['sachets'])){
	echo 0;
	return;
}
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
//select the medecines embalage status;
$exam_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='{$_POST['mdname']}'",$field="MedecineNameID",$data=true, $con);
$price_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$exam_id}' && Amount >= 0 && Date <= '{$_POST['date']}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
$emba = returnSingleField($emba_sql = "SELECT Emballage FROM md_price WHERE MedecinePriceID='{$price_id}'",$field="Emballage",$data=true, $con);
echo $emba;
//$_SESSION['sachets'][] = $_POST['mdname'];
?>