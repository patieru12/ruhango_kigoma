<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);

$patientID = returnSingleField("SELECT a.ConsultationRecordID FROM la_records AS a WHERE a.ExamRecordID = '{$_POST['examRecord']}'", "ConsultationRecordID", true, $con);
if(isset($_POST['examRecord']) && is_numeric($_POST['examRecord'])){
	$currentTime = time();
	$examRecordIDNow = PDB($_POST['examRecord'], true, $con);
	saveData("DELETE FROM la_records WHERE ExamRecordID='{$examRecordIDNow}' && sampleTaken IS NULL",$con);
	saveData("UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}' WHERE ConsultationRecordID={$patientID}", $con);

}
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
						<a style='color:red; text-decoration:none;' href='#' onclick='deleteExam(\"{$r['ExamRecordID']}\");return false;'>Delete</a>
						"
					:"")."
			</td>";
		$tbData .= "</tr>";
	}
}
echo $tbData;
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