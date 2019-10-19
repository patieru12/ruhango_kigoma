<?php
session_start();
require_once "../lib/db_function.php";
$patientRecordID = PDB($_GET['record_id'], true,$con);

$sql = "SELECT 	a.*,
				b.*,
				c.VillageName AS VillageName,
				d.CellName AS CellName,
				e.SectorName AS SectorName,
				f.DistrictName AS DistrictName
				FROM pa_info AS a
				INNER JOIN pa_records AS b
				ON a.PatientID = b.PatientID
				INNER JOIN ad_village AS c
				ON a.VillageID = c.ViillageID
				INNER JOIN ad_cell AS d
				ON c.CellID = d.CellID
				INNER JOIN ad_sector AS e
				ON d.SectorID = e.SectorID
				INNER JOIN ad_district AS f
				ON e.DistrictID = f.DistrictID
				WHERE b.PatientRecordID = {$patientRecordID}
				";

$patientInfo = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=false,$con);
// var_dump($patientInfo);
// return;
$date = date("Y-m-d H:i:s", time());
$vitalSign = "Temp: <b>".($patientInfo['Temperature'] > 0?($patientInfo['Temperature']." 0<sup>o</sup>C"):"")."</b><br />";
$vitalSign .= "Weight: <b>".($patientInfo['Weight'] > 0?($patientInfo['Weight']." Kg"):"")."</b><br />";
$vitalSign .= "Length: <b>".($patientInfo['lngth'] > 0?($patientInfo['lngth']." m"):"")."</b><br />";
$vitalSign .= "MUAC: <b>".($patientInfo['muac'] > 0?($patientInfo['muac']." cm"):"")."</b><br />";
// var_dump(str_replace("<", "_", $vitalSign));
$printableData = <<<PRINT
	<div style='width:100%; border 1px solid #000; font-size:10px;'>
		***{$date}***<br />
		CODE: <b>{$patientInfo['PatientID']}</b><br />
		------------------------------------------------------ <br />
		Monthly Number: <b>{$patientInfo['monthlyID']}</b><br />
		Daily Number: <b>{$patientInfo['dailyID']}</b><br />
		&nbsp;<br />
		{$vitalSign}
	</div>
PRINT;
// echo $vitalSign;return;
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF('', "A7");

$pdf->Open();

$pdf->AddPage("P");

$pdf->SetFont("Arial","N",10);

$pdf->SetMargins($left=10,$right=10,$top=10 );

$pdf->WriteHTML($printableData);

$pdf->Output(); 
?>