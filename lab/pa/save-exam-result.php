<?php
session_start();
require_once "../../lib/db_function.php";
if(trim($_POST['data'])){
	$currentTime = time();
	// Get the Result if Possible
	$results = explode(",", $_POST['data']);
	// Update the Exam Record Table
	$sql = "UPDATE la_records SET ExamNumber='{$_POST['resultID']}', ResultNumber='{$_POST['resultID']}', ResultDateSavedOn='{$currentTime}', LabAgent='{$_SESSION['user']['UserID']}' WHERE ExamRecordID='{$_POST['recordID']}'";
	saveData($sql, $con);
	// Delete any record related to the current record now
	saveData("DELETE FROM la_result_record WHERE ExamRecordID='{$_POST['recordID']}'",$con);

	foreach($results AS $r){
		if(!trim($r)){
			continue;
		}
		// var_dump($r); 
		// $r = preg_match("/tropho/", strtolower($r))?str_replace(" ", "+", $r):$r;
		$r = str_replace(" ", "+", $r);
		// Get the Result ID
		$examID = formatResultSet($rslt=returnResultSet($sql = "SELECT 	b.ExamID
																	FROM la_exam AS b
																	INNER JOIN la_price AS c
																	ON b.ExamID = c.ExamID
																	INNER JOIN la_records AS d
																	ON c.ExamPriceID = d.ExamPriceID
																	WHERE d.ExamRecordID = '{$_POST['recordID']}'
																	", $con), false, $con)['ExamID'];
		$sql1 = $sql = "INSERT INTO la_result SET ResultName='{$r}', ExamID = '{$examID}'"; // 	a.ResultID FROM la_result AS a WHERE 
		$sql2 = $sql = "SELECT a.ResultID FROM la_result AS a WHERE a.ResultName='{$r}' && a.ExamID = '{$examID}'";
		$resultID = insertOrReturnID($sql1, $sql2, 'ResultID', $con);

		// var_dump($resultID);
		$sql1 = "INSERT INTO la_result_record SET ExamRecordID='{$_POST['recordID']}', ResultID='{$resultID}'";
		$sql2 = "SELECT ResultRecordID FROM la_result_record WHERE ExamRecordID='{$_POST['recordID']}' && ResultID='{$resultID}'";
		$resultRecordsID = insertOrReturnID($sql1, $sql2, $field='ResultRecordID',$con);
	}
}
echo returnSingleField("SELECT a.ConsultationRecordID FROM la_records AS a WHERE a.ExamRecordID = '{$_POST['recordID']}'", "ConsultationRecordID", true, $con); die;
// die();
// Get Consultation ID

$error = "";
if(preg_match("/\d/", $_POST['recordID'])){
	$patientID = $_POST['recordID'];

	$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ConsultationRecordID
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	INNER JOIN co_records AS d
																	ON a.ConsultationRecordID = d.ConsultationRecordID
																	WHERE a.ExamRecordID={$patientID}
																	", $con), false, $con);
	// var_dump($examRecords);

	/*$examRecords = formatResultSet($rslt=returnResultSet($sql = "SELECT 	a.ExamPriceID AS priceID,
																	c.ExamName AS examName,
																	a.ExamNumber AS examNumber,
																	a.ResultNumber AS resultNumber,
																	a.ExamRecordID AS ExamRecordID,
																	e.resultName AS resultName
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	INNER JOIN co_records AS d
																	ON a.ConsultationRecordID = d.ConsultationRecordID
																	LEFT JOIN (
																		SELECT 	a.ExamRecordID,
																				GROUP_CONCAT(b.ResultName ) AS resultName
																				FROM la_result_record AS a
																				INNER JOIN la_result AS b
																				ON a.ResultID = b.ResultID
																				GROUP BY ExamRecordID
																	) AS e 
																	ON a.ExamRecordID = e.ExamRecordID
																	WHERE a.ConsultationRecordID={$examRecords['ConsultationRecordID']}
																	", $con), true, $con);*/
	// var_dump($examRecords);
	/*if(is_array($examRecords)){
		$tbData= "";
		foreach($examRecords AS $e){
			$tbData .= "<tr>";
				$tbData .= "<td>{$e['resultNumber']}</td>";
				$tbData .= "<td>{$e['examName']}</td>";
				$tbData .= "<td>{$e['resultName']}</td>";
				$tbData .= "<td><a style='color:blue; text-decoration:none;' onclick='AllowToAddResult({$e['ExamRecordID']});return false;' href='?{$e['ExamRecordID']}'>Edit</a></td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=4><span class='error-text'>No Exam is Prescribed!</span> {$sql}</td></tr>";
	}*/
}
?>
<script type="text/javascript">
	function AllowToAddResult(recordID){
		// The Result Here
		var url = "./add-exam-form.php?recordID=" + recordID;
		$.getJSON( url, function(data) {
			// console.log(data);
			$("#requestRecordID").val(recordID);
			$("#requestExamID").val(data.id);
			$("#requestExamName").val(data.examName);
			$("#requestExamResult").val(data.resultName);
		}).done(function(){
			console.log("Requested Completed Succesfully!");

			$("#requestExamResult").autocomplete("./auto/results.php?exam=" + $("#requestExamName").val(), {
				selectFirst: true
			});
		}).fail(function(error){
			console.log(error);
		}).always(function(){
		});
	}
</script>