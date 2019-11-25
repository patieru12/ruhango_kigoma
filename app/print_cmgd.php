<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
// var_dump($_REQUEST);
if(!trim($_REQUEST['process_id'])){
	echo "<span class='error-text'>Please Fill in Weight</span>";
	return;
}

$dailyID = PDB($_REQUEST['process_id'], true, $con);
$types = array("2018200001"=>1, "2018200002"=>2, "2018200003"=>3);
$type = $types[$dailyID];

$sql = "SELECT 	a.id AS commandID,
				a.pdfData AS commandInfo
				FROM sy_print_command AS a
				WHERE a.printerID = '{$dailyID}' && status = 0 && type={$type}
				ORDER BY submittedOn ASC";
$patientInfo = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
// var_dump($patientInfo);
$printableString = "";
if(count($patientInfo) > 0){
	foreach($patientInfo AS $printable){
		$printableString .= "<br />".$printable['commandInfo']."<br />************************";
	}
	saveData("UPDATE sy_print_command SET status=1 WHERE printerID = '{$dailyID}' AND type={$type}", $con);
}
if(!trim($printableString)){
	echo "Do not print. no command is ready for printing";
	die();
}
// echo json_encode($patientInfo);
$pdf = new mPDF("","A8",0,'',2,2,2,2);

$pdf->Open();

$pdf->AddPage("P");

$pdf->SetFont("Arial","N",10);
// $pdf->SetMargins($left=15,$right=15,$top=15) ;

$pdf->WriteHTML($printableString);
// $filename = "./receipt.pdf";
//echo $filename;
$pdf->Output(); 
die();
?>
<style type="text/css">
	body{
		margin: 1mm;
	}
</style>
<script type="text/javascript">
	// window.print();

	setTimeout(function(){
		// window.close();
	}, 500)
</script>