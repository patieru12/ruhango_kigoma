<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
if(preg_match("/\d/", $_GET['patientID'])){
	$patientID = $_GET['patientID'];
	$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ExamPriceID AS priceID,
																	c.ExamName AS examName,
																	a.MonthlyID AS examNumber,
																	a.ResultNumber AS resultNumber,
																	e.resultName AS resultName,
																	a.ExamRecordID AS ExamRecordID,
																	COALESCE(a.sampleTaken, 0) AS sampleTaken,
																	a.ConsultantID AS ConsultantID
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	INNER JOIN co_records AS d
																	ON a.ConsultationRecordID = d.ConsultationRecordID
																	LEFT JOIN (
																		SELECT 	a.ExamRecordID,
																				GROUP_CONCAT(b.ResultName SEPARATOR ';' ) AS resultName
																				FROM la_result_record AS a
																				INNER JOIN la_result AS b
																				ON a.ResultID = b.ResultID
																				GROUP BY ExamRecordID
																	) AS e 
																	ON a.ExamRecordID = e.ExamRecordID
																	WHERE d.PatientRecordID={$patientID}
																	", $con), true, $con);
	// var_dump($examRecords);
	if(is_array($examRecords)){
		$tbData= "";
		foreach($examRecords AS $e){
			if(strlen($e['resultName'])){
				$currentTime = time();
				saveData("UPDATE la_result_record SET status='{$currentTime}' WHERE ExamRecordID='{$e['ExamRecordID']}'", $con);
			}
			$tbData .= "<tr>";
				$tbData .= "<td>{$e['examNumber']}</td>";
				$tbData .= "<td>{$e['examName']}</td>";
				$tbData .= "<td>{$e['resultName']}</td>";
				$tbData .= "
				<td>
					".($e['sampleTaken'] == 0?
						"
						<a style='color:blue; text-decoration:none;' href='#' onclick='editExam(\"{$e['ExamRecordID']}\", \"{$e['examName']}\"); return false;'>Edit</a> | 
						<a style='color:blue; text-decoration:none;' href='#' onclick='deleteExam(\"{$e['ExamRecordID']}\");return false;'>Delete</a>
						"
					:"")."
				</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=4><span class='error-text'>No Exam is Prescribed!</span></td></tr>";
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