<?php
session_start();
// var_dump($_GET);
require_once "../lib/db_function.php";

if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$requiredPost = str_replace("_", ", ",preg_replace("/^_/", "", $_GET["post"]));
// echo $requiredPost;
$monthPattern = $_GET["year"]."-".$_GET['month'];
// echo $monthPattern;
// GET THE LIST OF MEDICINES PRISCRIBED TO PATIENT OF THE SELECTED MONTH

$sql = "SELECT 	d.Date
				FROM pa_records AS a
				INNER JOIN co_records AS b
				ON a.PatientRecordID = b.PatientRecordID
				INNER JOIN md_records AS c
				ON b.ConsultationRecordID = c.ConsultationRecordID
				INNER JOIN md_price AS d
				ON c.MedecinePriceID  = d.MedecinePriceID
				WHERE a.DateIn LIKE('{$monthPattern}%')
				GROUP BY d.Date
				";

$validDates = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$emptyColumns = "";
$subQuery 		= "";
$subQueryChars	= 'f';
$validCounter 	= 0;
if(is_array($validDates)){
	foreach($validDates AS $dateArray){
		$date = $dateArray['Date'];
		if($validCounter++ >0){
			$emptyColumns .= ", ";
		}
		
		$emptyColumns .= "COALESCE({$subQueryChars}.Amount, '') AS `{$date}`";
		$subQuery		.= " LEFT JOIN md_price AS ".$subQueryChars;
		$subQuery 		.= " ON e.MedecineNameID = ".($subQueryChars).".MedecineNameID && ".($subQueryChars++).".Date='{$date}' ";
	}

	$sql = "SELECT 	d.MedecineNameID,
					e.MedecineName,
					e.MedecineCategorID,
					aa.MedecineCategoryName,
					{$emptyColumns}
					FROM pa_records AS a
					INNER JOIN co_records AS b
					ON a.PatientRecordID = b.PatientRecordID
					INNER JOIN md_records AS c
					ON b.ConsultationRecordID = c.ConsultationRecordID
					INNER JOIN md_price AS d
					ON c.MedecinePriceID  = d.MedecinePriceID
					INNER JOIN md_name AS e
					ON d.MedecineNameID = e.MedecineNameID
					INNER JOIN md_category AS aa
					ON e.MedecineCategorID = aa.MedecineCategoryID
					{$subQuery}
					WHERE a.DateIn LIKE('{$monthPattern}%')
					GROUP BY d.MedecineNameID
					ORDER BY e.MedecineCategorID ASC, e.MedecineName ASC
					";
	$validMedicines = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

	// var_dump($validMedicines)
	?>
	<div style='max-height:85%; padding-top:5px; border:0px solid #000; overflow:auto;'>
		<?php
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
PDFDATA;
		$html = '
		<h3 style="text-align: center;">Tarif used in '.$month[(int)$_GET['month']].' '.$_GET["year"].'</h3>
		<table border="1" style="border-collapse:collapse;">
			<thead>
				<tr>';

					foreach($validMedicines[0] AS $fieldName=>$fieldValue){
						if(in_array($fieldName, array("MedecineCategorID", "MedecineCategoryName"))){
							continue;
						}
						$html .="<th>";
						if($fieldName == "MedecineNameID"){
							$html .="#";
						} else if($fieldName == "MedecineName"){
							$html .="Medicine Name";
						} else{
							$html .=$fieldName;
						}
						$html .="</th>";
					}
					$html .= '
				</tr>
			</thead>
			<tbody>';
				$i=1;
				$currentCategory = "";
				foreach($validMedicines AS $mdTarif){
					if($currentCategory != $mdTarif['MedecineCategorID']){
						$html .="<tr style='background-color:#b8b8b8; color: #ffffff; font-weight: bold;' >";
							$html .="<th colspan='".(count($mdTarif) - 2)."'>".$mdTarif["MedecineCategoryName"]."</th>";
						$html .="<tr>";
						$currentCategory = $mdTarif['MedecineCategorID'];
					}
					$html .="<tr>";
						foreach($mdTarif AS $fieldName=>$fieldValue){
							if(in_array($fieldName, array("MedecineCategorID", "MedecineCategoryName"))){
								continue;
							}
							$html .="<td>";
							if($fieldName == "MedecineNameID"){
								$html .=$i++;
							} else{
								$html .=$fieldValue;
							}
							$html .="</td>";
						}
					$html .="</tr>";
				}
				$html .= '
			</tbody>
		</table>';
		echo $html;
		?>

	</div>
	<?php
	$html = utf8_encode($pdfData.$html);
	//require the MPDF Library
	// require_once "../lib/mpdf57/mpdf.php";

	$pdf = new mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=10,$mgb=10);

	$pdf->Open();

	$pdf->AddPage();

	$pdf->SetFont("Arial","N",10);

	$pdf->WriteHTML($html);
	$pdf->setHTMLFooter("<div style='float:right; border:0px solid red; width:49%;'>Printed using care software | Genius Software ltd</div></div>");
	$filename = "./tarif.pdf";
	//echo $filename;
	$pdf->Output($filename);

	echo "<a href='{$filename}' target='_blank'>Download</a>";
} else{
	echo "<span class=error>No Medicines were distributed in the selected month</span>";
}
?>