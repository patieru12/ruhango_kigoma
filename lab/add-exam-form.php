<?php
session_start();
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
	$examsRequest = 0;
	$patientID = $_GET['recordID'];
	// Get the Active Quarter
	// var_dump($quaters);
	$quaterName = date("Y",time()).$quaters[date("m",time())];
	$quaterID 	= formatResultSet($rslt= returnResultSet("SELECT 	a.QuarterID AS QuarterID
																	FROM la_quarters AS a
																	WHERE QuarterName = '{$quaterName}'
																	",$con), false, $con);
	$qID = null;
	if(!is_array($quaterID)){
		$qID = saveAndReturnID($sql="INSERT INTO la_quarters SET QuarterName = '{$quaterName}'", $con);
	} else{
		$qID = $quaterID['QuarterID'];
	}
	// echo $qID;return;
	$examRecords = formatResultSet($rslt=returnResultSet("SELECT 	b.ExamID AS requiredExamID,
																	a.MonthlyID AS examNumber,
																	COALESCE(e.resultName,'') AS resultName,
																	c.ExamName AS examName,
																	a.ExamRecordID AS ExamRecordID,
																	c.RegisterID AS RegisterID
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	LEFT JOIN (
																		SELECT 	a.ExamRecordID AS ExamRecordID,
																				GROUP_CONCAT(b.ResultName) AS resultName
																				FROM la_result_record AS a
																				INNER JOIN la_result AS b
																				ON a.ResultID = b.ResultID
																				WHERE a.ExamRecordID = {$patientID}
																				GROUP BY ExamRecordID
																	) AS e
																	ON a.ExamRecordID = e.ExamRecordID
																	WHERE a.ExamRecordID = {$patientID}
																	ORDER BY ExamNumber DESC
																	LIMIT 0, 1
																	",$con), false, $con);
	// var_dump($examRecords, $patientID);
/*if(!trim($examRecords['examNumber'])){
	$examRecords['examNumber'] = 1;
} else if($examRecords['ExamRecordID'] != $patientID){
	$examRecords['examNumber']++;
}*/
// $examRecords['examNumber'] = $examRecords['RegisterID'];
echo json_encode(array("id"=>$examRecords['examNumber'], "examName"=>$examRecords['examName'], "resultName"=>$examRecords['resultName']));
?>