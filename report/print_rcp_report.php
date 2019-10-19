<?php
//require tde MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML("No Data is Transferred!");

$pdf->Output($file = "./results/rcp_all_data.pdf"); 
//var_dump($_POST);
?>
<script>
	//alert($(".printarea").html());
</script>
<a href='<?= $file ?>' id="print_link" target="_blank">Print <?= $_POST['data'] ?></a>