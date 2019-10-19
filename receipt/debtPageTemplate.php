<?php 
session_start();
require_once "../lib/db_function.php";

$provinceData = strtoupper($_PROVINCE);
$distictData = strtoupper($_DISTRICT);
$sectorData = strtoupper($_SECTOR);
$healthCenter = strtoupper($organisation);
$hcemail = strtolower($organisation_email);
$PatientRecordID = $_GET['record'];
$patients = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.requiredAmount AS requiredAmount,
																	a.availableAmount AS availableAmount,
																	COALESCE(a.houseHolderName, d.Name) AS Name,
																	a.dueDate AS dueDate,
																	a.phoneNumber AS phoneNumber,
																	a.address AS address,
																	a.status AS status,
																	b.DateIn AS DateIn,
																	c.InsuranceName AS InsuranceName,
																	b.PatientRecordID AS PatientRecordID,
																	b.DocID AS DocID,
																	a.idCard AS idCard,
																	d.Name AS patientName,
																	e.StartDate AS hospStartDate,
																	e.EndDate AS hospEndDate
																	FROM sy_debt_records AS a
																	INNER JOIN pa_records AS b
																	ON a.PatientRecordID = b.PatientRecordID
																	INNER JOIN in_name AS c
																	ON b.InsuranceNameID = c.InsuranceNameID
																	INNER JOIN pa_info AS d
																	ON b.PatientID = d.PatientID
																	LEFT JOIN ho_record AS e
																	ON b.PatientRecordID = e.RecordID
																	WHERE b.PatientRecordID = '{$PatientRecordID}'
																	", $con),false, $con);
if($patients){
$balance = number_format($patients['requiredAmount'] - $patients['availableAmount']);

$dateIn = date("d/m/Y", strtotime($patients['DateIn']));
$range = " ku itariki ya <span style='font-weight:bold'>{$dateIn}</span>";
if(!is_null($patients['hospStartDate']) && !is_null($patients['hospEndDate'])){
	$startDate = date("d/m/Y", strtotime($patients['hospStartDate']));
	$endDate = date("d/m/Y", strtotime($patients['hospEndDate']));
	$range = "guhera tariki ya {$startDate} kugeza tariki ya {$endDate}";
}
$dueDate = date("d/m/Y", strtotime($patients['dueDate']));
$toDay = date("d", time())." ".$amezi[((int)date("m",time() ) )]." ".date("Y", time());
$currentMonth = $amezi[((int)date("m",time() ) )];
// $date = date("d {$currentMonth} Y", time());

$phoneString 	= "";
$idString 		= "";
$addressString 	= "";
if(@$patients['phoneNumber']){
	$phoneString = " ukoresha telefone nimero ".$patients['phoneNumber'];
}
if(@$patients['idCard']){
	$idString = "  indangamuntu nimero".$patients['idCard'];
}
if(@$patients['address']){
	$addressString = " utuye ".$patients['address'];
}
$tableInfor = <<<DEBT
<style>
	table#top tr td{
		font-weight:bold;
	}
</style>
<table id=top style="font-size: 12px; width:100%; border:0px solid #000;">
	<tr>
		<td style="text-align: left;">REPUBLIC OF RWANDA</td>
		<!--<td style="text-align: right; padding-right:10px;">Kuwa {$toDay}</td>-->
	</tr>
	<tr>
		<td style="vertical-align: middle; padding-bottom: 2px; text-align: left;">
			<img src="../images/rwanda.png" style='width:50px' /><br />
		</td>
	</tr>
	<tr><td>{$provinceData} PROVINCE</td></tr>
	<tr><td>{$distictData} DISTRICT</td></tr>
	<tr><td>{$sectorData} SECTOR</td></tr>
	<tr><td>{$healthCenter}</td></tr>
	<tr><td>Tel: {$organisation_phone}</td></tr>
	<tr><td>Email: {$hcemail}</td></tr>
</table>
<br />&nbsp;
<br />&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span style="font-weight: bold;">AMASEZERANO Y'UMWENDA N<sup>o</sup>: {$patients['DocID']}</span>
<br />&nbsp;
<br />
<span style='font-weight:bold'>Ingingo ya mbere: ABAGIRANYE AMASEZERANO</span>
<br />
Hagati y'{$organisationKiny} aricyo gitanze umwenda ku ruhande rumwe
<br />
<div style='text-align:center'>Na</div>
{$patients['Name']} {$phoneString} {$idString} {$addressString} ariwe uhawe umwenda ku rundi ruhande
<br />&nbsp;
<br />
<span style='font-weight:bold'>Ingingo ya kabiri: IMPAMVU Y'AMASEZERANO</span>
<br />
Aya masezerano agamije guha {$patients['Name']} umwenda w'amafaranga {$balance} kubera servisi z'ubuvuzi zahawe {$patients['patientName']} {$range}

<br />&nbsp;
<br />
<span style='font-weight:bold'>Ingingo ya gatatu: IGIHE CYO KWISHYURA</span>
<br />
Impande zonbi zumvikanye ko umwenda uzishyurwa tariki ya {$dueDate}

<br />&nbsp;
<br />
<span style='font-weight:bold'>Ingingo ya kane: IGIHE AMASEZERANO AZAMARA</span>
<br />
Aya masezerano azarangira aruko umwenda wishyuwe.

<br /><br />&nbsp;
<br />
Bikorewe i <span style='font-weight:bold'>{$client}</span> kuwa {$toDay}<br />
<br />&nbsp;
<br />
<table style="font-size: 12px; width:100%; border:0px solid #000;">
	<tr>
		<td style='text-align:left'>
			<span style='font-weight:bold'>Uhawe umwenda<br />
			<br/>&nbsp;
			<br/>
			{$patients['Name']}</span>
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>
		</td>
		<td style='text-align:center'>
			<span style='font-weight:bold'>
			Utanze umwenda<br />
			<br/>&nbsp;
			<br/>
			{$_SESSION['user']['Name']}</span>
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>
		</td>
	</tr>
	<tr style='margin-top:70px;'>
		<td style='text-align:left'>
			<span style='font-weight:bold'>Bigenzuwe na</span>
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>
			comptable wa CS {$client}
		</td>
		<td style='text-align:center'>
			<span style='font-weight:bold'>Byemejwe na</span>
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>&nbsp;
			<br/>
			Umuyobozi CS {$client}
		</td>
	</tr>
</table>
DEBT;
} else {
	$tableInfor = "No Debt with the provided information";
}
// echo $tableInfor;
if($tableInfor){
	//require the MPDF Library
	require_once "../lib/mpdf57/mpdf.php";

	$pdf = new MPDF('c','A4','','',10,10,10,10,16,13);

	$pdf->mirrorMargins = 1;

	// $pdf->Open();

	// $pdf->AddPage();

	// $pdf->SetFont("Arial","N",10);

	$pdf->WriteHTML($tableInfor);
	// $pdf->setHTMLFooter("<div style='font-size:7px; font-family:arial; font-weight:bold; text-align:right; border-top:1px dashed #dfdfdf; color:#dfdfdf;'>printed using care software | easy one ltd</div>");
	// $filename = "./files/".$record['DocID'].".pdf";
	//echo $filename;
	$pdf->Output(); 
	die;
}
?>