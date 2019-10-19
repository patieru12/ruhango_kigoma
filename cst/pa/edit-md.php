<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
if(preg_match("/\d/", $_GET['MedecineRecordID'])){
	$patientID = $_GET['MedecineRecordID'];
	
	$medicinesRecords = formatResultSet($rslt=returnResultSet("SELECT 	c.MedecineName AS MedecineName,
																		a.Quantity AS Quantity,
																		a.SpecialPrescription AS SpecialPrescription,
																		a.MedecineRecordID AS MedecineRecordID,
																		COALESCE(d.Quantiy,'') AS Quantiy,
																		COALESCE(d.Frequency,'') AS Frequency,
																		COALESCE(d.Days,'') AS Days
																		FROM md_records AS a
																		INNER JOIN md_price AS b
																		ON a.MedecinePriceID = b.MedecinePriceID
																		INNER JOIN md_name AS c
																		ON b.MedecineNameID = c.MedecineNameID
																		LEFT JOIN md_prescription AS d
																		ON a.MedecineRecordID = d.MedecineRecordID
																		WHERE a.MedecineRecordID = {$patientID}
																		", $con), false, $con);
	$mainData = $medicinesRecords['MedecineName'];
	$data = preg_replace("/[a-zA-Z]/", "", $medicinesRecords['MedecineName']);
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

	$medicinesRecords['smUnit']		= trim($smallUnitValue);
	$medicinesRecords['smMesure'] 	= trim($smallUnitMesure);
	$medicinesRecords['stockLevel'] = ($stockLevel['stockLevel'] + $medicinesRecords['Quantity']);
	$medicinesRecords['stockLevel'] = $stockLevel['lowLevel'];
	$medicinesRecords['criticalLevel'] = $stockLevel['criticalLevel'];

	echo json_encode($medicinesRecords);
}
?>