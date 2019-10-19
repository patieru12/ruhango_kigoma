<?php
session_start();
//var_dump($_SESSION);
require_once "../../lib/db_function.php";
if("com" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}

set_time_limit(0);
$select = "";
$post = "";
$posts = explode("_", $_GET['post']);
//var_dump($_GET['post']);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
$ok = false;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
		$ok = true;
		if($post && $current++ == $count)
			$post .= " And ";
		else
			$post .= " ";
		$post .= $ps;
		if($sys_s++ > 0)
			$sys .= " || ";
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";
if(!$ok){
	echo "<span class=error>No Post Selected</span>";
	return;
}
//var_dump($_GET);

	?>

	<b class=visibl>
	<span class=success style='font-weight:bold; font-size:20px;'>
	<br />
	<?= $post ?> Daily Records<br />
	Date: <?= $_GET['day']."/".$_GET['month']."/".$_GET['year'] ?><br />
	</span>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
		.number_right{ text-align:right; }
		#number{ text-align:right; }
		a{color:blue; text-decoration:none; }
	</style>
	<span class=styling></span>
	<?php /*<?= @$_GET['filter']?"<script>$('#filter_').val('{$_GET['filter']}');</script><br /><span class=error-text>".count($patients)." Result".(count($patients)>1?"s":"")." found for ".$_GET['filter']."</span>":"" ?> <span style='float:right;'><?= @$_GET['filter']?"<a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span> */ ?>
	<div class=printarea style='height:85%; margin-top:2px; width:100%; border:0px solid #000; overflow:auto;'>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
		.number_right{ text-align:right; }
		#number{ text-align:right; }
		a{color:blue; text-decoration:none; }
	</style>
	<?php
	$dataToPrint = "";
	// die();
	if($daily_reception_report){
		$date = $_GET['year']."-".$_GET['month']."-".$_GET['day'];
		$totalAmount = 0;
		$dataToPrint = "<table id=table class=''>";
			$dataToPrint .= "<tr>";
				$dataToPrint .= "<td class=tb_title rowspan=2 style='border:1px solid #000;'>&nbsp;</td>";
				foreach($availableInsurance AS $a){
					$dataToPrint .= "<td colspan=2 class=tb_title style='border:1px solid #000;'>".$a."</td>";
				}
				$dataToPrint .= "<td class=tb_title colspan=2 style='border:1px solid #000;'>Total</td>";
				
			$dataToPrint .= "</tr>";
			$dataToPrint .= "<tr>";
				foreach($availableInsurance AS $a){
					$dataToPrint .= "<td class=tb_title style='border:1px solid #000;'>Number</td>";
					$dataToPrint .= "<td class=tb_title style='border:1px solid #000;'>Amount</td>";
				}
				$dataToPrint .= "<td class=tb_title style='border:1px solid #000;'>Number</td>";
				$dataToPrint .= "<td class=tb_title style='border:1px solid #000;'>Amount</td>";
			$dataToPrint .= "</tr>";
		foreach($daily_reception_report AS $term=>$content){
			$rowTotalNumber = 0;
			$rowTotalAmount = 0;
			$dataToPrint .= "<tr>";
				$dataToPrint .= "<td style='border:1px solid #000;'>".$term."</td>";
				foreach($content AS $table=>$fields){
					foreach($fields AS $field){
						if($field){
							$data = getReceptionNumbers($term, $date, $table,$field);
							$variableName = "rowTotal".$field;
							$$variableName += $data;

							/*if($field == "Amount"){
								$totalAmount += $data;
							}*/
							$dataToPrint .= "<td id=number style='border:1px solid #000;'>";
								$dataToPrint .= number_format($data);
							$dataToPrint .= "</td>";
						} else{
							$dataToPrint .= "<td class='empty_field' id=number style='border:1px solid #000;'>&nbsp;</td>";
						}
						
					}
				}
				$dataToPrint .= "<td id=number style='border:1px solid #000;'>".(number_format($rowTotalNumber))."</td>";
				$dataToPrint .= "<td id=number style='border:1px solid #000;'>".(number_format($rowTotalAmount))."</td>";
				$totalAmount += $rowTotalAmount;
			$dataToPrint .= "</tr>";
		}
		// Here Get the List of addition Product that are still active late use
		/*$otherProduct = returnAllData($sql = "SELECT * FROM sy_product WHERE ProductName != '{$exceptInReport}' ORDER BY ProductName",$con);
		if($otherProduct){
			foreach($otherProduct AS $product){
				$rowTotalNumber = 0;
				$rowTotalAmount = 0;
				$dataToPrint .= "<tr>";
					$dataToPrint .= "<td style='border:1px solid #000;'>{$product['ProductName']}</td>";
					$term = $product['ProductName'];
					foreach($availableInsurance AS $a){
						$table = "rpt_".strtolower(str_replace(" ", "_", $a));
						$field = "Number";
						$data = getReceptionNumbers($term, $date, $table,$field);
						$variableName = "rowTotal".$field;
						$$variableName += $data;
						$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($data)."</td>";

						$field = "Amount";
						$data = getReceptionNumbers($term, $date, $table,$field);
						$variableName = "rowTotal".$field;
						$$variableName += $data;
						$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($data)."</td>";
					}
					$dataToPrint .= "<td id=number style='border:1px solid #000;'>".(number_format($rowTotalNumber))."</td>";
					$dataToPrint .= "<td id=number style='border:1px solid #000;'>".(number_format($rowTotalAmount))."</td>";
					$totalAmount += $rowTotalAmount;
				$dataToPrint .= "</tr>";
			}
		}*/
		$dailyPaymentsNumber = returnSingleField("SELECT COUNT(a.id) AS payments FROM sy_debt_payment AS a WHERE Date='{$date}'", "payments", true, $con);
		$dailyPaymentsAmount = returnSingleField("SELECT SUM(a.paidAmount) AS payments FROM sy_debt_payment AS a WHERE Date='{$date}'", "payments", true, $con);
		$dataToPrint .= "<tr>";
			$dataToPrint .= "<td style='border:1px solid #000;'>Daily Payments</td>";
			foreach($availableInsurance AS $a){
				$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
				$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
			}
			$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($dailyPaymentsNumber)."</td>";
			$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($dailyPaymentsAmount)."</td>";
			$totalAmount += $dailyPaymentsAmount;
		$dataToPrint .= "</tr>";
		$dailyDebtNumber = returnSingleField("SELECT COUNT(a.id) AS debts FROM sy_debt_records AS a WHERE Date='{$date}'", "debts", true, $con);
		$sql = "SELECT 	SUM(a.debts) AS debts 
						FROM (
							SELECT 	(a.requiredAmount - a.availableAmount) AS debts
									FROM sy_debt_records AS a 
									WHERE a.Date = '{$date}'
						) AS a
						";
		// echo $sql;
		$dailyDebtAmount = returnSingleField($sql, "debts", true, $con);
		$dataToPrint .= "<tr>";
			$dataToPrint .= "<td style='border:1px solid #000;'>Daily Debt</td>";
			foreach($availableInsurance AS $a){
				$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
				$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
			}
			$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($dailyDebtNumber)."</td>";
			$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($dailyDebtAmount)."</td>";
			$totalAmount -= $dailyDebtAmount;
		$dataToPrint .= "</tr>";
		$dataToPrint .= "<tr>";
			$dataToPrint .= "<td style='border:1px solid #000;'>Balance</td>";
			foreach($availableInsurance AS $a){
				$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
				$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
			}
			$dataToPrint .= "<td class=empty_field style='border:1px solid #000;'>&nbsp;</td>";
			$dataToPrint .= "<td id=number style='border:1px solid #000;'>".number_format($totalAmount)."</td>";
		$dataToPrint .= "</tr>";
		$dataToPrint .= "</table>";
	}
	echo $dataToPrint;
	$provinceData = strtoupper($_PROVINCE);
	$distictData = strtoupper($_DISTRICT);
	$sectorData = strtoupper($_SECTOR);
	$healthCenter = strtoupper($organisation);
	$hcemail = strtolower($organisation_email);

	$dataToPrintHeader = <<<PDFINFO
		<table style="font-size: 12px; width:100%; border:0px solid #000;">
		<tr>
			<td style="text-align: left;">REPUBLIC OF RWANDA</td>
		</tr>
		<tr>
			<td style="vertical-align: middle; padding-bottom: 2px; text-align: left;">
				<img src="../../images/rwanda.png" style='width:50px' /><br />
			</td>
		</tr>
		<tr><td>{$provinceData} PROVINCE</td></tr>
		<tr><td>{$distictData} DISTRICT</td></tr>
		<tr><td>{$sectorData} SECTOR</td></tr>
		<tr><td>{$healthCenter}</td></tr>
		<tr><td>Tel: {$organisation_phone}</td></tr>
		<tr><td>Email: {$hcemail}</td></tr>
	</table>
	<div style='text-align:center; border-bottom:1px solid #000; font-weight:bold; margin-bottom:5px;'>
		Daily Reception Summary {$date}
	</div>
	<div class='main_title'>
		GIHUNDWE
	</div>
PDFINFO;
	$dataToPrint = $dataToPrintHeader.$dataToPrint;
	$dataToPrint .= "<br />&nbsp;<br /><table style='width:100%;'>";
		$dataToPrint .= "<tr>";
			$dataToPrint .= "<td>";
				$dataToPrint .= "Submitted By: <br />";
				$dataToPrint .= $_SESSION['user']['Name'];
				$dataToPrint .= "<br />&nbsp;<br />&nbsp;<br />Cashier";
			$dataToPrint .= "</td>";
			$dataToPrint .= "<td class='tb_title'>";
				$dataToPrint .= "Verified and approved by: <br />";
				$dataToPrint .= "<br />&nbsp;<br />&nbsp;<br />Accountant";
			$dataToPrint .= "</td>";
		$dataToPrint .= "</tr>";
	$dataToPrint .= "</table>";
	require_once "../../lib/mpdf57/mpdf.php";

	$pdf = new MPDF();

	$pdf->Open();

	$pdf->AddPage("P");

	$pdf->SetFont("Arial","N",10);
	$css = file_get_contents("./pdf_css.css");
	
	$pdf->WriteHTML($css,1);
	
	$pdf->WriteHTML($dataToPrint);
	$filename = "./dailyreceptiondata.pdf";
	//echo $filename;
	$pdf->Output($filename); 
	echo "<a style='font-size:12px; font-weight:bold;' href='./{$filename}' target='_blank'>Download</a>";
	die();
	?>
	