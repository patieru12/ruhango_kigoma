<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(!trim($_POST['data'])){
	echo "<span class=error-text>PLease Enter the Data to be used as Diagnostic</span>";
	return;
}
// var_dump($_POST);
if(preg_match("/\d/", $_POST['consultationID'])){
	$patientID = $_POST['consultationID'];
	// echo $patientID; return;
	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN co_records AS c
																ON a.PatientRecordID = c.PatientRecordID
																WHERE c.ConsultationRecordID ='{$patientID}'
																", $con), false, $con);
	// Get the Price of the selected Exam
	$price = formatResultSet($rslt=returnResultSet("SELECT 	a.ExamPriceID AS priceID,
															a.Amount AS examPriceAmount
															FROM la_price AS a
															INNER JOIN la_exam AS b
															ON a.ExamID = b.ExamID
															WHERE a.InsuranceTypeID = '{$patient['insuranceCategory']}' &&
																  b.ExamName = '".PDB($_POST['data'], true, $con)."'
															", $con), false, $con);
	// var_dump($patient);
	if(is_array($price)){
		// Now Activate The Request
		// echo $patient['DateIn'];
		// Get the Current Quarter
		$quarter = date("Y", strtotime($patient['DateIn'])).$quaters[date("m", strtotime($patient['DateIn']))];
		// echo $patient['DateIn'].":". date("m", strtotime($patient['DateIn']));
		$quarterID = insertOrReturnID($sql1="INSERT INTO la_quarters SET QuarterName='{$quarter}'", 
									  $sql2="SELECT a.QuarterID FROM la_quarters AS a WHERE a.QuarterName = '{$quarter}'",
									  $field = "QuarterID",
									  $con);
		// var_dump($quarterID);
		// Check if the Exam records Exit before
		$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																		FROM la_records AS a
																		WHERE a.ExamPriceID={$price['priceID']} &&
																			  a.ConsultationRecordID={$patientID} &&
																			  a.QuarterID={$quarterID}
																		", $con), true, $con);
		if(!is_array($examRecords)){
			saveData("INSERT INTO la_records SET ExamNumber='', ExamPriceID={$price['priceID']}, ConsultationRecordID={$patientID}, ConsultantID={$_SESSION['user']['UserID']}, ResultDate='', LabAgent=0, ResultNumber=0, QuarterID={$quarterID}",$con);
		}
		$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ExamPriceID AS priceID,
																		c.ExamName AS examName,
																		a.ExamNumber AS examNumber,
																		a.ResultNumber AS resultNumber,
																		e.resultName AS resultName
																		FROM la_records AS a
																		INNER JOIN la_price AS b
																		ON a.ExamPriceID = b.ExamPriceID
																		INNER JOIN la_exam AS c
																		ON b.ExamID = c.ExamID
																		LEFT JOIN (
																			SELECT 	a.ExamRecordID,
																					GROUP_CONCAT(b.ResultName SEPARATOR ';' ) AS resultName
																					FROM la_result_record AS a
																					INNER JOIN la_result AS b
																					ON a.ResultID = b.ResultID
																					GROUP BY ExamRecordID
																		) AS e 
																		ON a.ExamRecordID = e.ExamRecordID
																		WHERE a.ConsultationRecordID={$patientID} &&
																			  a.ConsultantID={$_SESSION['user']['UserID']}
																		", $con), true, $con);
		$tbData = "";
		foreach($examRecords AS $r){
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['examNumber']}</td>";
				$tbData .= "<td>{$r['examName']}</td>";
				$tbData .= "<td>{$r['resultName']}</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=3><span class=error-text>The Selected Exam is not on the pricing Plan Please enter the Active Exams</span></td></tr>";
	}
}
?>