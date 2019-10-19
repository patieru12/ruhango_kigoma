<?php
session_start();
require_once "./../lib/db_function.php";
// var_dump($_POST);
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$_SESSION['user']['printerID'] = "2018200002";

$data = str_replace(":", ", ", $_GET['data']); //$_GET['data'];
// echo $data;
$sql = "SELECT 	a.ExamRecordID,
				DATE_FORMAT(FROM_UNIXTIME(a.sampleTaken), '%Y-%m-%d %H:%i:%s') AS takenAt,
				c.ExamName AS ExamName,
				a.MonthlyID AS MonthlyID,
				d.PatientRecordID AS PatientRecordID
				FROM la_records AS a
				INNER JOIN la_price AS b
				ON a.ExamPriceID = b.ExamPriceID
				INNER JOIN la_exam AS c
				ON b.ExamID = c.ExamID
				INNER JOIN co_records AS d
				ON a.ConsultationRecordID = d.ConsultationRecordID
				WHERE a.ExamRecordID IN({$data})
				";
				// echo $sql;
$records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$patientID = $records[0]['PatientRecordID'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.PatientID AS patientID
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN in_price AS d
															ON b.InsuranceNameID = d.InsuranceNameID
															WHERE PatientRecordID ='{$patientID}'
															", $con), false, $con);

$dateInfo = "<span style='font-size:13px;'>GIHUNDWE HC<br />Tel: 0786282420</span><br /><span style='font-weight:bold; font-size:12px'>***".$records[0]['takenAt']."***</span><br />";
$printCommand = "GIHUNDWE HC\nTel: 0786282420\n***".$records[0]['takenAt']."***\n";
// $dateInfo = "Date: <b style='font-size:10px;'>{$records[0]['takenAt']}</b><br />";


$dateInfo .= "Code: <span style='border:1px solid #000; font-weight:bold; padding-left:30px; padding-right:20px; font-size:12px'>&nbsp;&nbsp;&nbsp;&nbsp;".$patient['dailyID']."&nbsp;&nbsp;&nbsp;&nbsp;</span><br />";
$printCommand .= "Code:      ".$patient['dailyID']."\n";

$dateInfo .= "<span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['patientName']."</span><br />";
$printCommand .= $patient['patientName']."\n";

$dateInfo .= "Insurance: <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['InsuranceName']."</span><br />";
$printCommand .= "Insurance: ".$patient['InsuranceName']."\n";

$dateInfo .= '<table>
	';
	for($i=0; $i<count($records); $i++){
		$dateInfo .= "<tr>
			<td>".($records[$i]['MonthlyID']).".".substr($records[$i]['ExamName'], 0, 27).": </td>
			</tr>";
		$printCommand .= "".($records[$i]['MonthlyID']).".".substr($records[$i]['ExamName'], 0, 27).":\n\n\n";
	}
$dateInfo .= "</table>
<style type='text/css'>
	table{
		border-collapse: collapse;
		width:100%;
	}
	table td{
		padding-right: 10px;
		padding-top:4px;
		padding-bottom:4px;
		height:60px;
		vertical-align:top;
	}
</style>";
$dateInfo .= "<span style=' font-size:12px'>Received By: ".$_SESSION['user']['Phone']."</span>";
$printCommand .= "\n\nReceived By: ".$_SESSION['user']['Phone']."\n\n\n";
// echo $stringData; die();
if($printCommand && $_SESSION['user']['printerID']){
	$cmd = PDB($printCommand, false,$con);
	saveData("INSERT INTO sy_print_command SET printerID='{$_SESSION['user']['printerID']}', type=2, commandInfo='{$cmd}', pdfData=\"{$dateInfo}\", receitValue=0, submittedOn='".time()."'",$con);
}
$dateInfo .= "<div style='position:absolute; top:55%; left:50%; font-size:10px; color:#ff0000; text-align:center; border:1px, solid #fab;'>Press Print on the Phone</div>";
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF("","A8",0,'',1,1,1,1);

$pdf->Open();

$pdf->AddPage("P");

$pdf->SetFont("Arial","N",10);
// $pdf->SetMargins($left=15,$right=15,$top=15) ;

// $pdf->WriteHTML("<span style='font-family:arial; font-size:10px; border:0px solid green;'>******".(date("Y-m-g H:i:s", time()))."*******<br />Ruberandinda Patience<br />Code: <br />-----------------------------------------------<br />My Data are here<hr />Again test<hr />If success by a bottle<hr />Thanks.....</span>");
$pdf->WriteHTML($dateInfo);
$filename = "./data.pdf";
//echo $filename;
$pdf->Output(); 
exit;
?>