<?php 
session_start();
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_SESSION);
// var_dump($_GET);
$date = date("Y-m-d", time());
$exams = NULL;
if(@$_GET['filter'] && trim($_GET['keyword'])){
	$keyword = PDB($_GET['keyword'], true, $con);
	$exams = formatResultSet($rslt=returnResultSet("SELECT 	a.ExamPriceID AS priceID,
														GROUP_CONCAT(c.ExamName, ' ', DATE_FORMAT(FROM_UNIXTIME(COALESCE(a.requestedOn,0)), '%H:%i:%s')) AS examName,
														a.ConsultationRecordID AS ConsultationRecordID,
														a.ExamNumber AS examNumber,
														a.ResultNumber AS resultNumber,
														f.Name AS patientName,
														h.RegisterNumber AS registerNumber,
														i.Name AS consultantName,
														g.registerCode AS registerCode
														FROM la_records AS a
														INNER JOIN la_price AS b
														ON a.ExamPriceID = b.ExamPriceID
														INNER JOIN la_exam AS c
														ON b.ExamID = c.ExamID
														INNER JOIN co_records AS d
														ON a.ConsultationRecordID = d.ConsultationRecordID
														INNER JOIN pa_records AS e
														ON d.PatientRecordID = e.PatientRecordID
														INNER JOIN pa_info AS f
														ON e.PatientID = f.PatientID
														INNER JOIN co_records AS h
														ON a.ConsultationRecordID = h.ConsultationRecordID
														INNER JOIN sy_users AS i
														ON h.ConsultantID = i.UserID
														INNER JOIN sy_register AS g
														ON h.registerId = g.id
														WHERE f.name LIKE('%{$keyword}%')
														GROUP BY a.ConsultationRecordID
														", $con), true, $con);
} else {
	// SELECT * Exam Request Without Exam Number
	$exams = formatResultSet($rslt=returnResultSet($s = "SELECT 	a.ExamPriceID AS priceID,
																	GROUP_CONCAT(c.ExamName, ' ', DATE_FORMAT(FROM_UNIXTIME(COALESCE(a.requestedOn,0)), '%H:%i:%s')) AS examName,
																	DATE_FORMAT(FROM_UNIXTIME(COALESCE(a.requestedOn,0)), '%Y-%m-%d') AS ResultDate,
																	a.ConsultationRecordID AS ConsultationRecordID,
																	a.ExamNumber AS examNumber,
																	a.ResultNumber AS resultNumber,
																	f.Name AS patientName,
																	h.RegisterNumber AS registerNumber,
																	i.Name AS consultantName,
																	g.registerCode AS registerCode
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	INNER JOIN co_records AS d
																	ON a.ConsultationRecordID = d.ConsultationRecordID
																	INNER JOIN pa_records AS e
																	ON d.PatientRecordID = e.PatientRecordID
																	INNER JOIN pa_info AS f
																	ON e.PatientID = f.PatientID
																	INNER JOIN co_records AS h
																	ON a.ConsultationRecordID = h.ConsultationRecordID
																	INNER JOIN sy_users AS i
																	ON h.ConsultantID = i.UserID
																	INNER JOIN sy_register AS g
																	ON h.registerId = g.id
																	WHERE a.ExamNumber='' &&
																		  a.status != -1
																	GROUP BY a.ConsultationRecordID
																	HAVING ResultDate = '{$date}'
																	", $con), true, $con);
	// echo $s;
}
	// var_dump($exams);
	if(is_array($exams)){
		$tbData = "
		<div style='max-height:380px; overflow:auto;'>
			<table class='list-1' style='width:100%;'>";
			// $tbData .= "<tr><!-- <th>#</th> --><th>Name</th><th>Exam List</th></tr>";
			foreach($exams AS $e){
				$tbData .= "<tr class='activePatient' id='myTr{$e['ConsultationRecordID']}' onclick='LoadProfile({$e['ConsultationRecordID']})'>";
					$tbData .= "<td style='font-size:13px;'>
									{$e['patientName']}
									<div style='font-size:12px; font-weight:bold;'>{$e['examName']}</div>
									<div style='font-size:12px; font-weight:bold; color:red;'>{$e['registerNumber']}:{$e['registerCode']} by {$e['consultantName']}</div>
								</td>";
					// $tbData .= "<td></td>";
				$tbData .= "</tr>";
			}
			$tbData .= "
			</table>
		</div>";
	} else{
		$tbData = "<tr><td><span class='error-text'>No Exam is Prescribed!</span></td></tr>";
	}
$numberOfPatient = count($exams);
$tbData .= <<<DATA
<style type="text/css">
	.activeP{
		background-color: #efefef;
	}

	.editContent{
		position: relative;
		top: -10px;
		left:-20px;
	}

	.list-1 img{
		width: 35px;
	}
</style>
<script type="text/javascript">
	function LoadProfile(patientID){
		// Load the DIagnostic Information from here
		$(".activePatient").removeClass("activeP");
		$("#myTr" + patientID).addClass("activeP");
		$("#requestedExams").load("./pa/exams.php?patientID=" + patientID);
		$("#patientIdentification").load("./pa/patient_identification.php?patientID=" + patientID);
		activeID = patientID;
	}
	firstNumber = "{$numberOfPatient}"
</script>
DATA;

if(@$_GET['response'] == 'ajax'){
	// $availableResults = 982;
	/*if($availableResults < 10){
		$availableResults = "&nbsp;&nbsp;".$availableResults."&nbsp;&nbsp;";
	} else if($availableResults < 100){
		$availableResults = "&nbsp;".$availableResults."&nbsp;";
	}
	$newResultsStyleData = <<<STYLES
		<style>
			{$newResultsIDs} {
				background-image: url('../images/result-available.png');
				background-repeat: no-repeat;
				background-position: right;
			}
		</style>
		<span id=out class=success style='position:absolute; top:13%; left:27%; background-color:#fff; padding-top:10px; padding-bottom:10px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; border-color: #00ee00;outline: none; border-radius:50%;'>&nbsp;{$availableResults}&nbsp;</span>
STYLES;*/
	// newResultsStyleData .= 
	echo json_encode(array('foundPatient'=>$numberOfPatient, 'foundText'=>$tbData/*, "styleData"=>$newResultsStyleData*/));
} else{
	echo $tbData;
}
?>