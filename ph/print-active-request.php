<?php
	session_start();
	
	require_once "../lib/db_function.php";

	$provinceData = strtoupper($_PROVINCE);
	$distictData = strtoupper($_DISTRICT);
	$sectorData = strtoupper($_SECTOR);
	$healthCenter = strtoupper($organisation);
	$hcemail = strtolower($organisation_email);

	$sql = "SELECT 	a.*,
					d.MedecineName AS MedecineName
					FROM md_stock_records_v2 AS a
					INNER JOIN md_stock_v2 AS b
					ON a.MedicineStockID = b.MedicineStockID
					INNER JOIN md_stock_batch AS c
					ON b.batchId = c.id
					INNER JOIN md_name AS d
					ON c.medicineNameId = d.MedecineNameID
					WHERE a.Status=0 && 
						  a.Operation='REQUEST'
					";
	//now select all non-approved request with the current operation code
	// $request_data = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	$request_data = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock_records.*, md_name.MedecineName, md_stock.Quantity as Stock FROM md_name, md_stock, md_stock_records WHERE md_stock.MedicineStockID = md_stock_records.MedicineStockID && md_stock.MedicineNameID = md_name.MedecineNameID && md_stock_records.Status <= 0 && md_stock_records.Operation='REQUEST' ORDER BY md_stock.Quantity ASC;",$con),$multirows=true,$con);
	//var_dump($request_data);
$data = <<<DATA
		<style>
			#n0{
				background-color:#efefef;
			}
			table#top tr td{
				font-weight:bold;
			}
		</style>
		<table id=top style="font-size: 12px; width:100%; border:0px solid #000;">
			<tr>
				<td style="text-align: left;">REPUBLIC OF RWANDA</td>
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
		<h1 style='text-align:center;'>Medicine Request form</h1>
		<table class=list style='width:70%; border-collapse:collapse;' border=1>
			<tr><th>#</th><th>Medicine</th><th>Current Stock</th><th>Quantity</th><th>More Info</th></tr>
DATA;
			$i = 1;
			if($request_data){
				foreach($request_data as $rqst){
					$data .= "<tr id='n".($i%2)."'>
					<td>".($i++)."</td>
					<td>{$rqst['MedecineName']}</td>
					<td style='text-align:right; padding:2px;'>{$rqst['Stock']}</td>
					<td style='text-align:right; padding:2px;'>{$rqst['Quantity']}</td>
					<td style='text-align:left; padding:2px;'>".(!is_null($rqst['Comment'])?$rqst['Comment']:"No Additional info")."</td>
					</tr>";
				}
			} else{
				$data .= "<tr><td colspan=4 align=center ><span style='color:red;'>No Request is Active</span></td></tr>";
			}
	$data .= "
		</table><br />&nbsp;<br />
		Done At {$client} &nbsp; On: ".date("Y-m-d",time())."<br />&nbsp;<br />
		Prepared By<br />{$_SESSION['user']['Name']}
		";
//echo $data;
	//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($data);
//echo $filename;
$pdf->Output(); 
die;

	?>