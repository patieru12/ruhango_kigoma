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
	$price = formatResultSet($rslt=returnResultSet($sql = "SELECT 	a.ExamPriceID AS priceID,
															a.Amount AS examPriceAmount
															FROM la_price AS a
															INNER JOIN la_exam AS b
															ON a.ExamID = b.ExamID
															WHERE a.InsuranceTypeID = '{$patient['insuranceCategory']}' &&
																  b.ExamName = '".PDB($_POST['data'], true, $con)."'
															", $con), false, $con);
	// var_dump($sql , $price);
	// var_dump($_POST); die();
	$isTDR = false;
	$resutlID = null;
	/*if(preg_match("/^tdr$/", strtolower($_POST['data']) )){
		$resultName = PDB($_POST['resultData'], true, $con);
		$examNameDAta = PDB($_POST['data'], true, $con);
		$isTDR = true;
		$resultID = returnSingleField("SELECT 	a.ResultID AS ResultID
												FROM la_result AS a
												INNER JOIN la_exam AS b
												ON a.ExamID = b.ExamID
												WHERE a.ResultName = '{$resultName}' &&
													  b.ExamName = '{$examNameDAta}'
												", "ResultID", true, $con);
		$examID = returnSingleField("SELECT 	b.ExamID AS ExamID
												FROM la_exam AS b
												WHERE b.ExamName = '{$examNameDAta}'
												", "ExamID", true, $con);
		if(!$resutlID){
			$resutlID = saveAndReturnID("INSERT INTO la_result SET ResultName='{$resultName}', ExamID='{$examID}', Appear=0",$con);
		}
	}*/
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
		/*$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																		FROM la_records AS a
																		WHERE a.ExamPriceID={$price['priceID']} &&
																			  a.ConsultationRecordID={$patientID} &&
																			  a.QuarterID={$quarterID}
																		", $con), true, $con);*/
		// var_dump($_POST); die;
		if(isset($_POST['examrecordID']) && is_numeric($_POST['examrecordID'])){
			$currentTime = time();
			$examRecordIDNow = PDB($_POST['examrecordID'], true, $con);
			saveData("UPDATE la_records SET ExamNumber='', ExamPriceID={$price['priceID']}, ConsultationRecordID={$patientID}, ConsultantID={$_SESSION['user']['UserID']}, requestedOn='{$currentTime}', ResultDate='', LabAgent=0, ResultNumber=0, QuarterID={$quarterID} WHERE ExamRecordID='{$examRecordIDNow}'",$con);
			saveData("UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}' WHERE ConsultationRecordID={$patientID}", $con);
			if($isTDR){
				// Here Check if the result was there and update if possible
				$sql = "SELECT 	a.ResultRecordID
								FROM la_result_record AS a
								WHERE a.ExamRecordID = {$examRecordIDNow}
								ORDER BY ResultRecordID ASC";
				$existingRecords = formatResultSet(returnResultSet($sql, $con), true, $con);
				// Delete other records from the system
				saveData("DELETE FROM la_result_record WHERE ExamRecordID = {$examRecordIDNow} && ResultRecordID != '{$existingRecords[0]['ResultRecordID']}'", $con);
				// if(count($existingRecords) == 1){
				saveData("UPDATE la_result_record SET ResultID = '{$resultID}' WHERE ResultRecordID='{$existingRecords[0]['ResultRecordID']}'",$con);
				// }
			}
		} else {
			$currentTime = time();
			$la_recordID = saveAndReturnID("INSERT INTO la_records SET ExamNumber='', ExamPriceID={$price['priceID']}, ConsultationRecordID={$patientID}, ConsultantID={$_SESSION['user']['UserID']}, requestedOn='{$currentTime}', ResultDate='', LabAgent=0, ResultNumber=0, QuarterID={$quarterID}",$con);
			if($isTDR){
				generateLaboIDs($la_recordID, $quaters);
				saveData("INSERT INTO la_result_record SET ExamRecordID='{$la_recordID}', ResultID='{$resutlID}', status={$currentTime}", $con);
			}
			saveData("UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}' WHERE ConsultationRecordID={$patientID}", $con);
		}
		$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ExamPriceID AS priceID,
																		c.ExamName AS examName,
																		a.MonthlyID AS examNumber,
																		a.ResultNumber AS resultNumber,
																		a.ExamRecordID AS ExamRecordID,
																		e.resultName AS resultName,
																		COALESCE(a.sampleTaken, 0) AS sampleTaken
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
				$tbData .= "
					<td>
						".($r['sampleTaken'] == 0?
							"
							<a style='color:blue; text-decoration:none;' href='#' onclick='editExam(\"{$r['ExamRecordID']}\", \"{$r['examName']}\"); return false;'>Edit</a> | 
							<a style='color:blue; text-decoration:none;' href='#' onclick='deleteExam(\"{$r['ExamRecordID']}\");return false;'>Delete</a>
							"
						:"")."
				</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ExamPriceID AS priceID,
																		c.ExamName AS examName,
																		a.MonthlyID AS examNumber,
																		a.ResultNumber AS resultNumber,
																		a.ExamRecordID AS ExamRecordID,
																		e.resultName AS resultName,
																		COALESCE(a.sampleTaken, 0) AS sampleTaken,
																		a.ConsultantID AS ConsultantID 
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
																		WHERE a.ConsultationRecordID={$patientID}
																		", $con), true, $con);
		$tbData = "";
		if($examRecords){
			foreach($examRecords AS $r){
				$tbData .= "<tr>";
					$tbData .= "<td>{$r['examNumber']}</td>";
					$tbData .= "<td>{$r['examName']}</td>";
					$tbData .= "<td>{$r['resultName']}</td>";
					$tbData .= "
						<td>
							".($r['sampleTaken'] == 0?
								"
								<a style='color:blue; text-decoration:none;' href='#' onclick='editExam(\"{$r['ExamRecordID']}\", \"{$r['examName']}\"); return false;'>Edit</a> | 
								<a style='color:blue; text-decoration:none;' href='#' onclick='deleteExam(\"{$r['ExamRecordID']}\");return false;'>Delete</a>
								"
							:"")."
					</td>";
				$tbData .= "</tr>";
			}
		}
		echo $tbData."<tr><td colspan=4><span class=error-text>The Selected Exam is not on the pricing Plan Please enter the Active Exams</span></td></tr>";
	}
}
?>
<script type="text/javascript">
	function editExam(examRecord, examName){
		$("#examRecorID").val(examRecord);
		$("#requestExam").val(examName);
	}

	function deleteExam(examRecord){
		if(examRecord){
			$.ajax({
				type: "POST",
				url: "./pa/delete-exam.php",
				data: "examRecord=" + examRecord + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedExams").html(result)
					//console.log(result);
					$("#requestExam").val("");
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>