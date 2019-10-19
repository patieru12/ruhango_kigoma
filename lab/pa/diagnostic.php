<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
$consultationID = 0;
// var_dump($_GET);
if(preg_match("/\d/", $_GET['patientID'])){
	$patientID = $_GET['patientID'];
	$sql = "SELECT * FROM co_records WHERE PatientRecordID='{$patientID}'";
	// echo $sql;
	$consultation = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	if(count($consultation) <= 0){
		// Here Register the Patient For Consultation
		// Get the Patient Information Here
		$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																	b.CategoryID AS insuranceCategory
																	FROM pa_records AS a
																	INNER JOIN in_name AS b
																	ON a.InsuranceNameID = b.InsuranceNameID
																	WHERE PatientRecordID ='{$patientID}'
																	", $con), false, $con);
		// var_dump($patient);
		// Get the Consultation type
		$consultationName = dayCategory($patient['DateIn'], $type=1);
		// echo $consultationName;
		$se_consultation = formatResultSet($rslt=returnResultSet("SELECT 	a.ServiceConsultationID AS serviceConsultationID,
																			b.ConsultationCategoryID AS consultationCategoryID,
																			b.ConsultationCategoryName AS consultation
																			FROM se_consultation AS a
																			INNER JOIN co_category AS b
																			ON a.ConsulationID = b.ConsultationCategoryID
																			INNER JOIN se_name AS c
																			ON a.ServiceID = c.ServiceNameID
																			WHERE c.DirectorID = '{$_SESSION['user']['UserID']}' &&
																				  b.ConsultationCategoryName LIKE('%{$consultationName}%')
																			", $con), true, $con);
		if(count($se_consultation) == 0){
			// Print the Error return by here
			echo "<span class=error-text>NO Service is Available</span>";
			return;
		}
		// var_dump($se_consultation);
		// Get the consultation Price
		$price = formatResultSet($rslt=returnResultSet("SELECT 	a.ConsultationPriceID AS priceID,
																a.amount AS consultationAmount,
																a.ConsultationCategoryID
																FROM co_price AS a
																WHERE a.InsuranceCategoryID = '{$patient['insuranceCategory']}' &&
																	  a.ConsultationCategoryID = '{$se_consultation[0]['consultationCategoryID']}'
																", $con), true, $con);
		// echo "<pre>"; var_dump($se_consultation, $price);
		saveData("INSERT INTO co_records SET PatientRecordID='{$patientID}', Date=NOW(), ConsultationPriceID='{$price[0]['priceID']}', ConsultantID='{$_SESSION['user']['UserID']}', RegisterNumber=''", $con);
	}
	$consultation = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	$consultationID = $consultation[0]['ConsultationRecordID'];
	// var_dump($consultation);
	$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*,
																b.English AS diagnosticName
																FROM co_diagnostic_records AS a
																INNER JOIN co_diagnostic AS b
																ON a.DiagnosticID = b.DiagnosticID
																WHERE a.ConsulationRecordID = '{$consultation[0]['ConsultationRecordID']}'
																", $con), true, $con);
	// var_dump($diagnostic);
	if(is_array($diagnostic)){
		foreach($diagnostic AS $d){
			echo "<div style='padding-bottom:5px;'>".$d['diagnosticName']."  <a href='#'>Edit</a> | <a href='#'>Delete</a></div>";
		}
	} else{
		echo "<span class=error-text>No Diagnostic Assigned to the current Patient</span>";
	}
} else{
	echo "<span class=error-text>No Patient is selected</span>";
}
?>
<script type="text/javascript">
	consultationID = <?= $consultation[0]['ConsultationRecordID'] ?>;
</script>