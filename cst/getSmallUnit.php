<?php
session_start();
require_once "./../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
$mainData = PDB($_GET['medicinename'], true, $con);
$data = preg_replace("/[a-zA-Z]/", "", $_GET['medicinename']);
// Get the Small Unit FROM databaase
$smallUnit 			= returnSingleField($sql="SELECT smallUnit FROM md_name WHERE MedecineName='{$mainData}'",$field = "smallUnit",true, $con);
$stockLevel			= formatResultSet($rslt=returnResultSet($sql="SELECT 	a.Quantity AS stockLevel,
																			COALESCE(b.lowLevel, 10) AS lowLevel,
																			COALESCE(b.criticalLevel, 0) AS criticalLevel 
																			FROM md_stock AS a 
																			INNER JOIN md_name AS b 
																			ON a.MedicineNameID = b.MedecineNameID 
																			WHERE b.MedecineName='{$mainData}'
																			",$con),false, $con);
$smallUnitValue 	= preg_replace("/[a-zA-Z]/", "", $smallUnit);
$smallUnitMesure 	= preg_replace("/\d/", "", $smallUnit);
// sleep(10);
echo json_encode(array("smUnit"=>trim($smallUnitValue), "smMesure"=>trim($smallUnitMesure), "stockLevel"=>$stockLevel['stockLevel'], "lowLevel"=>$stockLevel['lowLevel'], "criticalLevel"=>$stockLevel['criticalLevel']));
?>