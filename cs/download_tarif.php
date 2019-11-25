<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
// var_dump($_GET);
	if(@$_GET['type'] == "md"){
		$sql = "SELECT `md_price`.`MedecinePriceID`,`md_name`.`MedecineNameID`,`md_name`.`CategoryCode`,`md_name`.`MedecineName`,`md_name`.`MedecineCategorID`,`md_category`.`MedecineCategoryName`,`md_price`.* FROM `md_name`,`md_category`,`md_price` WHERE `md_category`.`MedecineCategoryID` = `md_name`.`MedecineCategorID` AND `md_price`.`MedecineNameID` = `md_name`.`MedecineNameID` AND `md_price`.`Date` = '{$_GET['date']}' ORDER BY `md_category`.`MedecineCategoryName` ASC, Medecinename ASC, `Date` DESC";
		//echo $sql;
		$query = mysql_query($sql);

		$printed = "";
		$row_ = 1;
		$pdfData = $systemPDFFooter;
		$otherHeader = "<tr><td>".strtoupper($_PROVINCE)." PROVINCE</td></tr>
				<tr><td>".strtoupper($_DISTRICT)." DISTRICT</td></tr>
				<tr><td>".strtoupper($_SECTOR)." SECTOR</td></tr>
				<tr><td style=''>".strtoupper($organisation)."</td></tr>
				<tr><td>Tel: ".$organisation_phone."</td></tr>
				<tr><td>Email: ".strtolower($organisation_email)."</td></tr>";
		$pdfData .= <<<PDFDATA
		<html>
			<head>
				<title> TARIF</title>
				<style>
					table{
						font-size:10px;
					}
				</style>
			</head>
			<body>
				<table style="font-size: 12px;">
					<tr><td style="text-align: left;">REPUBLIC OF RWANDA</td></tr>
					<tr>
						<td style="vertical-align: middle; padding-bottom: 2px; text-align: left;">
							<img src="../images/rwanda.png" style='width:50px' /><br />
						</td>
					</tr>
					{$otherHeader}
				</table>
				<div style='text-align:center; font-weight:bold; text-decoration:underline;'>TARIF DES MEDICAMENT DU {$_GET['date']} </div>
				<table border=1 style='border-collapse:collapse;'>
					<thead>
						<tr id='th'>
							<th style='width:10%;'>#</th>
							<th style='width:10%;'>Code</th>
							<th style='width:30%;'>Medicine Name</th>
							<th style='width:25%;'>Buying Price</th>
							<th style='width:25%;'>Selling Amount</th>
						</tr>
					</thead>
PDFDATA;
		while ($row = mysql_fetch_assoc($query)) {
			if($printed != $row['MedecineCategoryName']){
				$pdfData .= "<tr id=th><th align=left colspan=5>{$row['MedecineCategoryName']}</th></tr>";
				$printed = $row['MedecineCategoryName'];
			}
			$col = 1;
			$pdfData .= "<tr>
				<td style='text-align:right; padding:5px;' align=left>{$row_}</td>
				<td align=left>{$row['CategoryCode']}</td>
				<td align=left >{$row['MedecineName']}</td>
				<td style='text-align:right; padding:5px;'> {$row['BuyingPrice']}</td>
				<td style='text-align:right; padding:5px;'> {$row['Amount']}</td>
			</tr>";			
			$row_++;
		}

		$pdfData .= "<tr id=th><th align=left colspan=5>VI. Materials and Consummables</th></tr>";
		$sql = "SELECT `cn_price`.MedecinePriceID, `cn_name`.`MedecineNameID`, `cn_name`.`MedecineName`,`cn_price`.* FROM `cn_name`,`cn_price` WHERE `cn_price`.`MedecineNameID` = `cn_name`.`MedecineNameID` && `cn_price`.`Date` = '{$_GET['date']}' ORDER BY Medecinename ASC, `Date` DESC";
		//echo $sql;
		$query = mysql_query($sql);
		while ($row = mysql_fetch_assoc($query)) {
									
			$col = 1;
			$pdfData .= "<tr>
				<td style='text-align:right; padding:5px;' align=left>{$row_}</td>
				<td>&nbsp;</td>
				<td align=left >{$row['MedecineName']}</td>
				<td style='text-align:right; padding:5px;'> {$row['BuyingPrice']}</td>
				<td style='text-align:right; padding:5px;'> {$row['Amount']}</td>
			</tr>";		
			$row_++;
		}
		$pdfData .= "
				</table>
				<table style='width:100%;'>
					<tr >
						<td>
							<b>Preparé Par</b>:<br />
							<br />&nbsp;
							.............................<br />
							Comptable C.S ".ucwords($client_abbr)."
						</td>
						<td>
							<b>Verifié Par</b>:<br />
							<br />&nbsp;
							.............................<br />
							Gestionnaire de la Pharmacie
						</td>
						<td>
							<b>Approuvé Par</b>:<br />
							<br />&nbsp;
							<b>{$organisation_represantative}</b><br />
							Titulaire C.S ".ucwords($client_abbr)."
						</td>
					</tr>
				</table>
			</body>
		</html>";
	}

	// echo $pdfData; die();

if($pdfData){
	//require the MPDF Library
	// require_once "../lib/mpdf57/mpdf.php";

	$pdf = new mPDF('c','A4','','',10,10,10,10,16,13);

	$pdf->mirrorMargins = 1;

	// $pdf->Open();

	// $pdf->AddPage();

	// $pdf->SetFont("Arial","N",10);

	$pdf->WriteHTML($pdfData);
	// $pdf->setHTMLFooter("<div style='font-size:7px; font-family:arial; font-weight:bold; text-align:right; border-top:1px dashed #dfdfdf; color:#dfdfdf;'>printed using care software | easy one ltd</div>");
	// $filename = "./files/".$record['DocID'].".pdf";
	//echo $filename;
	$pdf->Output(); 
	die;
}
?>