<?php
session_start();
//var_dump($_SESSION);
require_once "../../lib/db_function.php";
if("com" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_GET); die();

$requestedDate = $_GET['year']."-".$_GET['month']."-".$_GET['day'];
// echo $requestedDate;
$sql = "SELECT 	a.id AS id,
				a.pdfData AS pdfData,
				a.receitValue AS receitValue,
				DATE_FORMAT(FROM_UNIXTIME(a.submittedOn), '%Y-%m-%d') AS submittedOn
				FROM sy_print_command AS a
				WHERE a.status=0 AND type = 1
				HAVING submittedOn = '{$requestedDate}'";
// echo $sql; die();
$data = formatResultSet($rslt=returnResultSet($sql,$con), true, $con);
$receitValue = "SELECT SUM(a.receitValue) AS receiptValue FROM ($sql) AS a";
$receiptValue = returnSingleField($receitValue, "receiptValue", true,$con);
// var_dump($data);
$dataString = "No receipt is printed ".$requestedDate;
if(count($data) > 0){
	$dataString = count($data)." Receipts are available for printing containg the total amount of ".number_format($receiptValue);
	$dataString .= "<table id=table border=1>";
	$newRow = true;

	for($i=0; $i<count($data); $i++){
		if($i % 6 == 0){
			$newRow = true;
		}
		if($newRow){
			if($i>0){
				$dataString .= "</tr>";
			}
			$dataString .= "<tr>";
			$newRow = false;
		}
		// $dataString .= "<tr>";
			$dataString .= "<td>";
				$dataString .= str_replace("font-size:12px", "font-size:8px", $data[$i]['pdfData']);
			$dataString .= "</td>";
		// $dataString .= "</tr>";
	}
		$dataString .= "</tr>";
	$dataString .= "</table>";
}
/*echo $dataString;
die();*/
require_once "../../lib/mpdf57/mpdf.php";

$pdf = new MPDF("", "A4",0,'',6,2,2,2);

$pdf->Open();

$pdf->AddPage("P");

$pdf->SetFont("Arial","N",10);
$css = file_get_contents("./pdf_receipt_css.css");

$pdf->WriteHTML($css,1);

$pdf->WriteHTML($dataString);
$filename = "./dailyreceipt.pdf";
//echo $filename;
$pdf->Output($filename);

?>
<a href="<?= $filename ?>" target="_blank" id="downloadLink">Download</a>
<script type="text/javascript">
	setTimeout(function(e){
		$("#downloadLink")[0].click();
	}, 200);
</script>