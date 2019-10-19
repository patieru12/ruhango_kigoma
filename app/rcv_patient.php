<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
// var_dump($_REQUEST);
if(!trim($_REQUEST['patientWeigth']) || $_REQUEST['patientWeigth'] <= 0){
	echo "<span class='error-text'>Please Fill in Weight</span>";
	return;
}

/*if(!trim($_REQUEST['patientIDSearch']) || $_REQUEST['patientIDSearch'] <= 0){
	echo "<span class='error-text'>Provide the Code</span>";
	return;
}*/
// var_dump($_REQUEST);

//check if the patience ID exist Before
$dailyID 	= PDB($_REQUEST['dailyID'], true, $con);
$patienceID = PDB($_REQUEST['dailyID'], true, $con);

// var_dump($_REQUEST); die();
if($patienceID){
	// Get the Patient reconrd information
	$date = date("Y-m-d H:i:s", time());
	$dateIn = date("Y-m-d", time());
	$sql = "SELECT 	a.PatientID AS PatientID,
					a.Name AS Name,
					b.InsuranceCardID AS InsuranceCardID,
					b.Temperature AS Temperature,
					b.Weight AS Weight,
					b.lngth AS lngth,
					b.muac AS muac,
					b.monthlyID AS monthlyID,
					b.dailyID AS dailyID,
					'{$date}' AS DateIN,
					b.PatientRecordID AS PatientRecordID
					FROM pa_info AS a
					INNER JOIN pa_records AS b
					ON a.PatientID = b.PatientID
					WHERE b.dailyID = '{$dailyID}' && b.DateIN = '{$dateIn}'";
	// echo $sql; die();
	$patientInfo = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	if($patientInfo){
		// Now try to make the update query
		$updateSqlQuery = "UPDATE pa_records SET ";
		$comma = false;
		if(@$_REQUEST['patientWeigth'] && $_REQUEST['patientWeigth'] > 0){
			$weight = PDB($_REQUEST['patientWeigth'], true,$con);
			$updateSqlQuery .= "Weight='{$weight}'";
			$comma = true;
		}
		
		if(@$_REQUEST['patientTemp'] && $_REQUEST['patientTemp'] > 0){
			$temp = PDB($_REQUEST['patientTemp'], true,$con);

			if($comma)
				$updateSqlQuery .= ", ";
			$updateSqlQuery .= "Temperature='{$temp}'";
			$comma = true;
		}
		// $_REQUEST['patientLength'] = 90;
		if(@$_REQUEST['patientLength'] && $_REQUEST['patientLength'] > 0){
			$lngth = PDB($_REQUEST['patientLength'], true,$con);
			if($lngth > 3){
				$lngth = "0.".$lngth;
			}

			if($comma)
				$updateSqlQuery .= ", ";
			$updateSqlQuery .= "lngth='{$lngth}'";
			$comma = true;
		}
		
		if(@$_REQUEST['patientMUAC'] && $_REQUEST['patientMUAC'] > 0){
			$muac = PDB($_REQUEST['patientMUAC'], true,$con);

			if($comma)
				$updateSqlQuery .= ", ";
			$updateSqlQuery .= "muac='{$muac}'";
			$comma = true;
		}

	}
	
	$patientRecordID = $patientInfo[0]['PatientRecordID'];
	$updateSqlQuery .= " WHERE PatientRecordID='{$patientRecordID}'";
	saveData($updateSqlQuery, $con);
	if(@$_REQUEST['hasCough'] && $_REQUEST['hasCough'] == 1){
		// Here Fill in the Cough arrangement Register
		$coughPatientInfo = returnSingleField("SELECT id FROM tb_records WHERE PatientRecordID='{$patientRecordID}'", "id", true, $con);
		if(!$coughPatientInfo){
			saveData("INSERT INTO tb_records SET PatientRecordID='{$patientRecordID}', Date='{$dateIn}'", $con);
		}
	}

	$date = date("Y-m-d H:i:s", time());
	$sql = "SELECT 	a.PatientID AS PatientID,
					a.Name AS Name,
					b.InsuranceCardID AS InsuranceCardID,
					COALESCE(b.Temperature, 0) AS Temperature,
					COALESCE(b.Weight, 0) AS Weight,
					COALESCE(b.lngth, 0) AS lngth,
					COALESCE(b.muac, 0) AS muac,
					b.monthlyID AS monthlyID,
					b.dailyID AS dailyID,
					'{$date}' AS DateIN
					FROM pa_info AS a
					INNER JOIN pa_records AS b
					ON a.PatientID = b.PatientID
					INNER JOIN ad_village AS c
					ON a.VillageID = c.ViillageID
					INNER JOIN ad_cell AS d
					ON c.CellID = d.CellID
					INNER JOIN ad_sector AS e
					ON d.SectorID = e.SectorID
					INNER JOIN ad_district AS f
					ON e.DistrictID = f.DistrictID
					WHERE b.PatientRecordID = {$patientRecordID}
					";

	$patientInfo = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

	echo json_encode($patientInfo);
}
?>
