<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
if(!@$_POST['debtId']){
	echo "<span class='error'>No Debt to be Paid</span><br />&nbsp;<br />&nbsp;";
	return;
}
if(!@$_POST['requiredAmount'] || $_POST['requiredAmount'] <= 0){
	echo "<span class='error'>No Debt to be Paid</span><br />&nbsp;<br />&nbsp;";
	return;
}
if(!@$_POST['paidAmount'] || $_POST['paidAmount'] <= 0){
	echo "<span class='error'>No New Amount received</span><br />&nbsp;<br />&nbsp;";
	return;
}
// Save The Payment Record
$date = date("Y-m-d", time());
$debtId = PDB($_POST['debtId'], true, $con);
$paidAmount = PDB($_POST['paidAmount'], true, $con);
saveData("INSERT INTO sy_debt_payment SET debtID='{$debtId}', paidAmount='{$paidAmount}', Date='{$date}'", $con);
$balance = $_POST['requiredAmount'] - ($_POST['availableAmount'] + $_POST['paidAmount']);
$availableAmount = $_POST['availableAmount'] + $_POST['paidAmount'];
if($balance <= 0){
	saveData("UPDATE sy_debt_records SET availableAmount='{$availableAmount}', status=1 WHERE id='{$debtId}'", $con);
} else {
	saveData("UPDATE sy_debt_records SET availableAmount='{$availableAmount}', status=0 WHERE id='{$debtId}'", $con);
}
if($balance <0){
	$balance = 0;
}
$patientID = $_POST['patientID'];

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

$stringData = "<span style='font-size:13px;'>{$client_receipt_header}<br />Tel: {$client_receipt_phone}</span><br /><span style='font-weight:bold; font-size:12px'>***".date("Y-m-d H:i:s",time())."***</span><br />";
$printCommand = "{$client_receipt_header}\nTel: {$client_receipt_phone}\n***".date("Y-m-d H:i:s",time())."***\n";

$stringData .= "Code: <span style='border:1px solid #000; font-weight:bold; padding-left:30px; padding-right:20px; font-size:12px'>&nbsp;&nbsp;&nbsp;&nbsp;".$patient['dailyID']."&nbsp;&nbsp;&nbsp;&nbsp;</span><br />";
$printCommand .= "Code:      ".$patient['dailyID']."\n";

$stringData .= "<span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['patientName']."</span><br />";
$printCommand .= $patient['patientName']."\n";

$stringData .= "Insurance: <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$patient['InsuranceName']."</span><br />";
$printCommand .= "Insurance: ".$patient['InsuranceName']."\n";
// var_dump($_POST);
$stringData .= "Debt "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".$_POST['debtDate']." RWF</span><br />";
$printCommand .= "Debt ".$_POST['debtDate']." RWF\n";
// var_dump($_POST);
$stringData .= "Total "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($_POST['requiredAmount'])." RWF</span><br />";
$printCommand .= "Total ".number_format($_POST['requiredAmount'])." RWF\n";

$stringData .= "Paid Before "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($_POST['availableAmount'])." RWF</span><br />";
$printCommand .= "Paid Before".number_format($_POST['availableAmount'])." RWF\n";

$stringData .= "Paid Now"." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($_POST['paidAmount'])." RWF</span><br />";
$printCommand .= "Paid Now".number_format($_POST['paidAmount'])." RWF\n";

$stringData .= "Remains "." <span style='border:0px solid #000; font-weight:bold; padding-left:5px; padding-right:2px; font-size:12px'>".number_format($balance)." RWF</span><br />";
$printCommand .= "Remains ".number_format($balance)." RWF\n";


$stringData .= "<span style=' font-size:12px'>Received By: ".$_SESSION['user']['Phone']."</span>";
$printCommand .= "Received By: ".$_SESSION['user']['Phone']."\n\n\n";
// echo $stringData; die();
if($printCommand && $_SESSION['user']['printerID']){
	$cmd = PDB($printCommand, false,$con);
	saveData("INSERT INTO sy_print_command SET printerID='{$_SESSION['user']['printerID']}', commandInfo='{$cmd}', pdfData=\"{$stringData}\", receitValue='{$balance}', submittedOn='".time()."'",$con);
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
?>
<span class=success>Debt Paid Successfuly</span>
<a href="<?= $filename ?>" id="print_now" target="_blank">Print</a>
<script>
	setTimeout(function(){
		// $("#print_now")[0].click();
		window.open("../app/print_cmgd.php?process_id=2018200001", '_blank', 'location=yes,height=360,width=500,scrollbars=yes,status=yes');
	},100);
	setTimeout(function(){
		$(".close").click();
	},500);
	setTimeout(function(){
		$("#generate").click();
	},1000);
</script>