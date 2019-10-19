<?php

session_start();
require_once "../lib/db_function.php";
$id = PDB($_GET['id'], true, $con);

$time = str_replace("_", "/", $id);
$d = new DateTime($time);
$currentTime = $d->getTimestamp();
$reportMonth = $d->format("F Y");

$d = new DateTime( date("Y-m-01", $currentTime) ); 
$monthStartDate 	= $d->getTimestamp();

$endDate = $d->format( 'Y-m-t 23:59:59' );
$d = new DateTime( date($endDate, $currentTime) );
$monthEndDate		= $d->getTimestamp();

$StockSummary = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.id AS id,
																	a.name AS itemName,
																	COALESCE(b.receivedQuantity,0) AS receivedQuantity,
																	COALESCE(c.distributedQuantity,0) AS distributedQuantity,
																	COALESCE(d.carriedForward,0) AS carriedForward,
																	( (COALESCE(d.carriedForward,0) + COALESCE(b.receivedQuantity,0)) - COALESCE(c.distributedQuantity,0) ) AS currentStock
																	FROM ac_schoolstockitemtype AS a
																	LEFT JOIN (
																		SELECT 	b.id AS id,
																				a.date AS date,
																				SUM(a.quantity) AS receivedQuantity
																				FROM ac_schoolstockoperation AS a
																				INNER JOIN ac_schoolstockitemtype AS b
																				ON a.itemTypeID = b.id
																				WHERE a.operationType = 0 &&
																					  a.date >= '{$monthStartDate}' && a.date <= '{$monthEndDate}'
																				GROUP BY b.id
																	) AS b
																	ON a.id = b.id
																	LEFT JOIN (
																		SELECT 	b.id AS id,
																				a.date AS date,
																				SUM(a.quantity) AS distributedQuantity
																				FROM ac_schoolstockoperation AS a
																				INNER JOIN ac_schoolstockitemtype AS b
																				ON a.itemTypeID = b.id
																				WHERE a.operationType = 1 &&
																					  a.date >= '{$monthStartDate}' && a.date <= '{$monthEndDate}'
																				GROUP BY b.id
																	) AS c
																	ON a.id = c.id
																	LEFT JOIN (
																		SELECT 	a.id AS id,
																				(COALESCE(b.receivedQuantity, 0) - COALESCE(c.distributedQuantity, 0)) AS carriedForward
																				FROM ac_schoolstockitemtype AS a
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS receivedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 0 &&
																								  a.date < '{$monthStartDate}'
																							GROUP BY b.id
																				) AS b
																				ON a.id = b.id
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS distributedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 1 &&
																								  a.date < '{$monthStartDate}'
																							GROUP BY b.id
																				) AS c
																				ON a.id = c.id
																	) AS d
																	ON a.id = d.id
																	",$con), true, $con); 
		// echo "<pre>";var_dump($StockSummary);
// echo json_encode($StockSummary);

set_time_limit(0);

$provinceData = strtoupper($_PROVINCE);
$distictData = strtoupper($_DISTRICT);
$sectorData = strtoupper($_SECTOR);
$healthCenter = strtoupper($organisation);
$hcemail = strtolower($organisation_email);

$tableInfor = <<<DATAHTML
<html>
<body>
<style>
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
<div style='text-align:center; margin-top:10px; margin-bottom:10px; text-decoration:underline; font-weight:bold;'>{$app_level} Stock Status Report {$reportMonth}</div>
DATAHTML;

echo $tableInfor;

	                        		$tableInfor .='<table cellspacing="0" style="padding: 1px; border-collapse:collapse; width: 100%; border: solid 2px #000000; font-size: 10pt; padding-top: 10px; "><tr><th style="width: 5%; border: solid 1px #000000;text-align: center;">#</th><th style="width: 10%; border: solid 1px #000000; text-align: center;">Item</th><th style="width: 40%; border: solid 1px #000000; text-align: center;">Carried Forward</th><th style="width: 4.5%; border: solid 1px #000000;">Received</th><th style="width: 4.5%; border: solid 1px #000000;">Distributed</th><th style="width: 6%; border: solid 1px #000000;">Current Stock</th></tr>';
// $tableInfor .='<table cellspacing="0" style="padding: 1px; width: 100%; border-collapse:collapse; border: solid 2px #000000; font-size: 10pt; padding-top: 10px; "><tr><th style="width: 5%; border: solid 1px #000000;text-align: center;">#</th><th style="width: 10%; border: solid 1px #000000; text-align: center;">Date</th><th style="width: 40%; border: solid 1px #000000; text-align: center;">Commited By</th><th style="width: 4.5%; border: solid 1px #000000;">Item</th><th style="width: 4.5%; border: solid 1px #000000;">Quantity</th><th style="width: 6%; border: solid 1px #000000;">Operation</th></tr>';

                        // Start Looping in the found data and try to print Them
                        $operationIndex = 1;
                        foreach($StockSummary AS $item){
                        	// Print One Operation at a time
                        	if ( ($operationIndex != 1) && ($operationIndex % 47 == 0) ) {
	                        		$tableInfor .='</table>';
	                        		$tableInfor .='<table cellspacing="0"  style=" bottom:0px; width: 100%;font-size:8pt; position: fixed; left: 0; bottom: 20px;">';
	                        			$tableInfor .='<tr>';
	                        				$tableInfor .='<td style="width: 100%; text-align:right; color: #dfdfdf; ">Generated using Care Software &reg; Digital Schooling Ltd &copy; Easy One ltd</td>';
	                        			$tableInfor .='</tr>';
	                        		$tableInfor .='</table>';
	                        	$tableInfor .='</body>';
	                        	$tableInfor .='<body>';
	                        		$tableInfor .='<table cellspacing="0" style="padding: 1px; border-collapse:collapse; width: 100%; border: solid 2px #000000; font-size: 10pt; padding-top: 10px; "><tr><th style="width: 5%; border: solid 1px #000000;text-align: center;">#</th><th style="width: 10%; border: solid 1px #000000; text-align: center;">Item</th><th style="width: 40%; border: solid 1px #000000; text-align: center;">Carried Forward</th><th style="width: 4.5%; border: solid 1px #000000;">Received</th><th style="width: 4.5%; border: solid 1px #000000;">Distributed</th><th style="width: 6%; border: solid 1px #000000;">Current Stock</th></tr>';
                        	}
                        	$tableInfor .='<tr style="">';
                        		$tableInfor .='<td style="width:5%; border: solid 1px #000000; ">&nbsp;&nbsp;'.$operationIndex.'</td>';
                        		$tableInfor .='<td style="width:35%; border: solid 1px #000000;">&nbsp;&nbsp;'.$item['itemName'].'</td>';
                        		$tableInfor .='<td style="width:15%; border: solid 1px #000000; text-align: left;">&nbsp;&nbsp;'.$item['carriedForward'].'</td>';
                        		$tableInfor .='<td style="width:15%; border: solid 1px #000000; text-align: left;">&nbsp;&nbsp;'.$item['receivedQuantity'].'</td>';
                        		$tableInfor .='<td style="width:15%; border: solid 1px #000000; text-align: right;">'.number_format($item['distributedQuantity']).'&nbsp;&nbsp;</td>';
                        		$tableInfor .='<td style="width:15%; border: solid 1px #000000; text-align: left;">&nbsp;&nbsp;'.($item['currentStock']).'</td>';
                        	$tableInfor .='</tr>';
                        	$operationIndex++;
                        }
                        $tableInfor .='</table>';
                        $tableInfor .='<table cellspacing="0"  style=" bottom:0px; width: 100%;font-size:8pt; position: fixed; left: 0; bottom: 20px;">';
                        	$tableInfor .='<tr>';
                        		$tableInfor .='<td style="width: 100%; text-align:right; color: #dfdfdf; ">Generated using Care Software &reg; Digital Schooling Ltd &copy; Easy One ltd</td>';
                        	$tableInfor .='</tr>';
                        $tableInfor .='</table>';
                    $tableInfor .='</body>';
                $tableInfor .='</html>'; 

// echo $tableInfor;
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($tableInfor);

$pdf->Output(); 
die;
?>