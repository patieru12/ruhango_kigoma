<?php
session_start();
require_once "../lib/db_function.php";
// var_dump($_POST); die();
$patientID = $_POST['patientID'];
$date = date("Y-m-d", time());
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
// var_dump($patient, $_POST);
$stringData = "<span style='font-size:13px;'>{$client_receipt_header}<br />Tel: {$client_receipt_phone}</span><br /><span style='font-weight:bold; font-size:12px'>***".date("Y-m-d H:i:s",time())."***</span><br />";
$printCommand = "{$client_receipt_header}\nTel: {$client_receipt_phone}\n***".date("Y-m-d H:i:s",time())."***\n";

$stringData .= "Code: <span style='border:1px solid #000; font-weight:bold; padding-left:30px; padding-right:20px; font-size:12px'>&nbsp;&nbsp;&nbsp;&nbsp;".$patient['dailyID']."&nbsp;&nbsp;&nbsp;&nbsp;</span><br />";
$printCommand .= "Code:      ".$patient['dailyID']."\n";

$stringData .= "<span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['patientName']."</span><br />";
$printCommand .= $patient['patientName']."\n";

$stringData .= "Insurance: <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['InsuranceName']."</span><br />";
$printCommand .= "Insurance: ".$patient['InsuranceName']."\n";
$tableName = "rpt_".str_replace(" ", "_", strtolower($patient['InsuranceName']));
/*var_dump($_POST);
// echo $stringData;
die();*/
$totalBill 		= 0;
$totalPaid 		= 0;
$totalAdjust	= 0;
foreach($_POST AS $key=>$value){
	if($key == "consultation"){
		saveData("UPDATE co_records SET status=1 WHERE ConsultationRecordID='{$value}'",$con);
	} else if(preg_match("/^la_/", $key)){
		$la_record_id = preg_replace("/^la_/", "", $key);
		saveData("UPDATE la_records SET status=1 WHERE ExamRecordID='{$la_record_id}'",$con);
	} else if(preg_match("/^md_/", $key)){
		$md_record_id = preg_replace("/^md_/", "", $key);
		saveData("UPDATE md_records SET status=1 WHERE MedecineRecordID='{$md_record_id}'",$con);
	} else if(preg_match("/^ac_/", $key)){
		$ac_record_id = preg_replace("/^ac_/", "", $key);
		saveData("UPDATE ac_records SET status=1 WHERE ActRecordID='{$ac_record_id}'",$con);
	} else if(preg_match("/^cn_/", $key)){
		$cn_record_id = preg_replace("/^cn_/", "", $key);
		saveData("UPDATE cn_records SET status=1 WHERE ConsumableRecordID='{$cn_record_id}'",$con);
	} else if(preg_match("/^ho_/", $key)){
		$hp_record_id = preg_replace("/^ho_/", "", $key);
		saveData("UPDATE ho_record SET status=1 WHERE HORecordID='{$hp_record_id}'",$con);
	} else if(preg_match("/^sy_/", $key)){
		$id = preg_replace("/^sy_/", "", $key);
		saveData("UPDATE sy_records SET status=1 WHERE id='{$id}'",$con);
	} else if(preg_match("/^patientTM_/", $key)){
		if(strtolower($patient['InsuranceName']) != 'private'){
			$stringData .= "TM: <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($value)." RWF</span><br />";
			$printCommand .= "TM: ".number_format($value)." RWF\n";
		}

		$TicketID = preg_replace("/^patientTM_/", "", $key);
		// $SSSQL = "UPDATE mu_tm SET TicketPaid={$value}, status=1 WHERE TicketID='{$TicketID}'";
		// echo $SSSQL;
		saveData("UPDATE mu_tm SET TicketPaid={$value}, status=1 WHERE TicketID='{$TicketID}'", $con);
		$itemName = "TM Paid";
		saveData($sql = "INSERT INTO `{$tableName}` SET PatientRecordID='{$patientID}', itemName='{$itemName}', Number=1, Amount='{$value}', Date='{$date}'", $con);
		// echo $key."1.".$sql."<hr />";
	} else if(preg_match("/^total/", $key)){
		if($key == "totalBill"){
			$totalBill = $value;
		} else if($key == "totalPaid"){
			$totalPaid = $value;
		} else {
			if($key == "totalAdjust"){
				$totalAdjust = $value;
			}
			$stringData .= preg_replace("/^total/", "", $key).": <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($value)." RWF</span><br />";
			$printCommand .= preg_replace("/^total/", "", $key).": ".number_format($value)." RWF\n";
			$itemName = $reportRenames[$key]; //"TM Paid";
			saveData($sql = "INSERT INTO `{$tableName}` SET PatientRecordID='{$patientID}', itemName='{$itemName}', Number=1, Amount='{$value}', Date='{$date}'", $con);
			// echo $key."2.".$sql."<hr />";
		}
	}
}
// die();
// var_dump($_POST);
$stringData .= "Total "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($totalBill)." RWF</span><br />";
$printCommand .= "Total ".number_format($totalBill)." RWF\n";

$stringData .= "Paid "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($totalPaid)." RWF</span><br />";
$printCommand .= "Paid ".number_format($totalPaid)." RWF\n";

/*$stringData .= "Adjust "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($totalAdjust)." RWF</span><br />";
$printCommand .= "Adjust ".number_format($totalAdjust)." RWF\n";*/

$stringData .= "<span style=' font-size:12px'>Received By: ".$_SESSION['user']['Phone']."</span>";
$printCommand .= "Received By: ".$_SESSION['user']['Phone']."\n\n\n";
// echo $stringData; die();
if($printCommand && $_SESSION['user']['printerID']){
	$cmd = PDB($printCommand, false,$con);
	saveData("INSERT INTO sy_print_command SET printerID='{$_SESSION['user']['printerID']}', commandInfo='{$cmd}', pdfData=\"{$stringData}\", receitValue='{$totalPaid}', submittedOn='".time()."'",$con);
}
// $stringContent = "<span style='font-family:arial; font-size:10px; border:0px solid green;'>******".(date("Y-m-g H:i:s", time()))."*******<br />Ruberandinda Patience<br />Code: <br />-----------------------------------------------<br />My Data are here<hr />Again test<hr />If success by a bottle<hr />Thanks.....</span>";
// require_once "../lib/mpdf57/mpdf.php";

$pdf = new mPDF("","A8",0,'',2,2,2,2);

$pdf->Open();

$pdf->AddPage("P");

$pdf->SetFont("Arial","N",10);
// $pdf->SetMargins($left=15,$right=15,$top=15) ;

$pdf->WriteHTML($stringData);
$filename = "./receipt.pdf";
//echo $filename;
$pdf->Output($filename); 
// exit;
?>
<a href="<?= $filename ?>" id="print_now" target="_blank">Print</a>
<script>
	setTimeout(function(){
		// $("#print_now")[0].click();
		window.open("../app/print_cmgd.php?process_id=2018200001", '_blank', 'location=yes,height=360,width=500,scrollbars=yes,status=yes');
	},200);
	setTimeout(function(){
		LoadProfile("<?= $_POST['patientID'] ?>");
	},600);
</script>