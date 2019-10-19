<?php
session_start();
require_once "../lib/db_function.php";

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
$stringData = "<span style='font-size:13px;'>GIHUNDWE HC<br />Tel: 0786282420</span><br /><span style='font-weight:bold; font-size:12px'>***".date("Y-m-d H:i:s",time())."***</span><br />";
$printCommand = "GIHUNDWE HC\nTel: 0786282420\n***".date("Y-m-d H:i:s",time())."***\n";

$stringData .= "Code: <span style='border:1px solid #000; font-weight:bold; padding-left:30px; padding-right:20px; font-size:12px'>&nbsp;&nbsp;&nbsp;&nbsp;".$patient['dailyID']."&nbsp;&nbsp;&nbsp;&nbsp;</span><br />";
$printCommand .= "Code:      ".$patient['dailyID']."\n";

$stringData .= "<span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['patientName']."</span><br />";
$printCommand .= $patient['patientName']."\n";

$stringData .= "Insurance: <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['InsuranceName']."</span><br />";
$printCommand .= "Insurance: ".$patient['InsuranceName']."\n";


$stringData .= "<br />&nbsp;<br />&nbsp;<br /><span style=' font-size:12px'>Received By: ".$_SESSION['user']['Phone']."</span>";
$printCommand .= "\n\n\nReceived By: ".$_SESSION['user']['Phone']."\n\n\n";
// echo $stringData; die();
if($printCommand && $_SESSION['user']['printerID']){
	$cmd = PDB($printCommand, false,$con);
	saveData("INSERT INTO sy_print_command SET printerID='{$_SESSION['user']['printerID']}', commandInfo='{$cmd}', pdfData=\"{$stringData}\", receitValue='0', submittedOn='".time()."'",$con);
}
// $stringContent = "<span style='font-family:arial; font-size:10px; border:0px solid green;'>******".(date("Y-m-g H:i:s", time()))."*******<br />Ruberandinda Patience<br />Code: <br />-----------------------------------------------<br />My Data are here<hr />Again test<hr />If success by a bottle<hr />Thanks.....</span>";
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF("","A8",0,'',6,2,2);

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
		$("#print_now")[0].click();
	},200);
	setTimeout(function(){
		LoadProfile("<?= $_POST['patientID'] ?>");
	},600);
</script>