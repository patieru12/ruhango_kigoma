<?php
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title=MMI Form");
$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$record['PatientRecordID']}'",$con),$multirows=false,$con);
$date_ = date("Y-m-d",time());
$count_consult = 3; count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category, se_consultation WHERE se_consultation.ServiceID = '{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID",$con),$multirows=true,$con));
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);
$patient_address = formatResultSet($rslt=returnResultSet($sql="SELECT ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName from ad_village, ad_cell, ad_sector, ad_district WHERE ad_village.ViillageID='{$patient['VillageID']}' && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID",$con),$multirows=false,$con);

$insurance_card = formatResultSet($rslt=returnResultSet($sql="SELECT pa_insurance_cards.* from pa_insurance_cards WHERE PatientID='{$record['PatientID']}' && Status=1",$con),$multirows=false,$con);
$affiliate_info = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE FamilyCode='{$insurance_card['AffiliateName']}' && AffiliateName='@1'",$con),$multirows=false,$con);
$bene = "";
//var_dump($affiliate_info);
//var_dump($patient); die;
if($patient['Name'] == $affiliate_info['Name']){
		$bene = "ADHERENT LUI-MEME";
	} else{
		$bene = <<<BEN
		N<sup>o</sup> d'Affiliation: {$record['InsuranceCardID']} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Nom et Prenom:{$patient['Name']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		Sex: {$patient['Sex']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
BEN;
		$bene .= "Age: ". ($patient['DateofBirth'] == "0000-00-00"?"":getAge($patient['DateofBirth']));
	}
	
	//var_dump($affiliate_info['DateofBirth']);
$patientAge = ($patient['DateofBirth'] == "0000-00-00"?"":getAge($patient['DateofBirth'],$notation=1, $current_date=$record['DateIn']));
$age = "";
if($affiliate_info['DateofBirth'] != null)
	$age = $affiliate_info['DateofBirth'] == "0000-00-00"?"":getAge($affiliate_info['DateofBirth']);
//$family = !$patient['FamilyCode']?null:formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE N_ID='{$patient['FamilyCode']}'",$con),$multirows=false,$con);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//$records = null;
if($existing_cons)
	$date_ = $existing_cons[0]['Date'];
//select three last records and display them
$to_day_records = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.DateIn AS DateIn,
																		a.PatientRecordID,
																		c.Amount AS Amount,
																		d.ConsultationCategoryName AS name,
																		b.Date AS Date,
																		e.Name AS consultantName,
																		e.signature AS signature,
																		b.ConsultationRecordID AS ConsultationRecordID
																		FROM pa_records AS a
																		INNER JOIN co_records AS b
																		ON a.PatientRecordID = b.PatientRecordID
																		INNER JOIN co_price AS c
																		ON b.ConsultationPriceID = c.ConsultationPriceID
																		INNER JOIN co_category AS d
																		ON c.ConsultationCategoryID = d.ConsultationCategoryID
																		LEFT JOIN sy_users AS e
																		ON b.ConsultantID = e.UserID
																		WHERE a.PatientRecordID='{$_GET['records']}'
																		",$con),$multirows=true,$con);
// var_dump($to_day_records); die();
$consultationString = "";
$consultationStringAmount = "";
$consultationStringDate = "&nbsp;";
$consultantName = "";
$nurseSignature = "";

$mainTotalAmount = 0;
if($to_day_records){
	foreach($to_day_records AS $t){
		if($t['name'] != 'invisible'){
			$consultationString .= $t['name'];
		} else{
			$consultationString .= "No Consultation";
		}

		$consultationStringAmount += $t['Amount'];
		$consultationStringDate = $t['Date'];

		$mainTotalAmount += $consultationStringAmount;
		$consultantName = $t['consultantName'];

		if($t['signature']){
			$nurseSignature = "<img src='../images/signatures/{$t['signature']}' style='max-height:25px;' />";
		}
	}
}

$hospRecords = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.StartDate AS StartDate,
																	a.EndDate AS EndDate,
																	c.Name AS name,
																	b.Amount AS Amount
																	FROM ho_record AS a
																	INNER JOIN ho_price AS b
																	ON a.HOPriceID = b.HOPriceID
																	INNER JOIN ho_type AS c
																	ON b.HOTypeID = c.TypeID
																	WHERE a.RecordID = '{$_GET['records']}'
																	", $con), true, $con);
$hospString="";
$hospStringAmount = "";
$hospStringAdmission = "";
$hospStringSortie = "";
$hospStringJours = "";
$hospStringUnitPrice = "";

if($hospRecords){
	foreach($hospRecords AS $h){
		$hospString = $h['name'];
		$hospStringAdmission = $h['StartDate'];
		$hospStringSortie = $h['EndDate'];
		$hospStringJours = getAge($h['StartDate'], 1, $h['EndDate'], true);
		$hospStringAmount = $hospStringJours * $h['Amount'];
		$hospStringUnitPrice = $h['Amount'];

		$mainTotalAmount += $hospStringAmount;
	}
}

$examsStringData = <<<STRINGDATA
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan='3' style='border:0px solid #000; text-align:right; padding-right:10px;'>
							<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td></td>
					</tr>
STRINGDATA;

// Here get the list of requested exams
$examsRecords = formatResultSet($rslt=returnResultSet($sql="SELECT 	b.Amount AS Amount,
																	c.ExamName AS ExamName,
																	DATE_FORMAT(FROM_UNIXTIME(a.sampleTaken), '%Y-%m-%d') AS Date
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	WHERE a.ConsultationRecordID='{$to_day_records[0]['ConsultationRecordID']}' &&
																		  a.MonthlyID IS NOT NULL
																	",$con),$multirows=true,$con);
if($examsRecords){
	$examsStringData = "";
	$examTotal = 0;
	foreach($examsRecords AS $e){
		$examsStringData .= "<tr>";
			$examsStringData .= "<td>";
				$examsStringData .= $e['Date'];
			$examsStringData .= "</td>";
			$examsStringData .= "<td>";
			$examsStringData .= "</td>";
			$examsStringData .= "<td>";
				$examsStringData .= $e['ExamName'];
			$examsStringData .= "</td>";
			$examsStringData .= "<td>";
				$examsStringData .= $e['Amount'];
			$examsStringData .= "</td>";
		$examsStringData .= "</tr>";

		$examTotal += $e['Amount'];
	}
	$mainTotalAmount += $examTotal;
	$examTotal = number_format($examTotal);
	$examsStringData .="
					<tr>
						<td colspan='3' style='border:0px solid #000; text-align:right; padding-right:10px;'>
							<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td style='text-align: right'>
							<span style='font-size: 12px; font-weight:bold;'>{$examTotal}</span></td>
						</td>
					</tr>";
}

$actConsummablesRecords = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.Quantity AS Quantity,
																				b.Amount AS Amount,
																				c.MedecineName AS MedecineName,
																				a.Date AS Date
																				FROM cn_records AS a
																				INNER JOIN cn_price AS b
																				ON a.MedecinePriceID = b.MedecinePriceID
																				INNER JOIN cn_name AS c
																				ON b.MedecineNameID = c.MedecineNameID
																				WHERE a.PatientRecordID='{$_GET['records']}'
																	UNION 
																		SELECT 	d.Quantity AS Quantity,
																				e.Amount AS Amount,
																				f.Name AS MedecineName,
																				d.Date AS Date
																				FROM ac_records AS d
																				INNER JOIN ac_price AS e
																				ON d.ActPriceID = e.ActPriceID
																				INNER JOIN ac_name AS f
																				ON e.ActNameID = f.ActNameID
																				WHERE d.PatientRecordID = '{$_GET['records']}'
																				", $con), true, $con);
// var_dump($actConsummablesRecords); die();
$actConsummablesString = "";
$actConsummablesStringAmount = "";
$actConsummablesStringDate = "<br />&nbsp;<br />&nbsp;<br />&nbsp;";

$actConsummablesTotalAmount = 0;
if($actConsummablesRecords){
	$actConsummablesStringDate = "";
	foreach($actConsummablesRecords AS $ac){
		$actConsummablesString .= $ac['MedecineName']."<br />";
		$actConsummablesStringDate .= $ac['Date']."<br />";
		$actConsummablesStringAmount .= ($ac['Quantity']*$ac['Amount'])."<br />";
		$actConsummablesTotalAmount += ($ac['Quantity']*$ac['Amount']);
	}

	$mainTotalAmount += $actConsummablesTotalAmount;

	$actConsummablesTotalAmount = number_format($actConsummablesTotalAmount, 2);
}


$medicinesString = <<<STRINGDATA
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan='7' style='border:0px solid #000; text-align:right; padding-right:10px;'>
						<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td></td>
					</tr>
STRINGDATA;

$medicinesRecords = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.Quantity AS Quantity,
																		b.Amount AS Amount,
																		c.MedecineName AS MedecineName,
																		a.Date AS Date,
																		c.smallUnit AS smallUnit,
																		c.form AS form
																		FROM md_records AS a
																		INNER JOIN md_price AS b
																		ON a.MedecinePriceID = b.MedecinePriceID
																		INNER JOIN md_name AS c
																		ON b.MedecineNameID = c.MedecineNameID
																		WHERE a.ConsultationRecordID='{$to_day_records[0]['ConsultationRecordID']}'
																		", $con), true, $con);
// var_dump($medicinesRecords); die();
$medicinesTotalAmount = 0;
if($medicinesRecords){
	$i=1;
	$medicinesString = "";
	foreach($medicinesRecords AS $md){
		$medicinesString .= "<tr>";
			$medicinesString .= "<td>";
				// $medicinesString .= $md['Date'];
				$medicinesString .= $i++;
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
				$medicinesString .= $md['MedecineName'];
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
				$medicinesString .= $md['form'];
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
				$medicinesString .= $md['smallUnit'];
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
				$medicinesString .= $md['Quantity'];
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
				$medicinesString .= $md['Amount'];
			$medicinesString .= "</td>";
			$medicinesString .= "<td>";
				$medicinesString .= $md['Amount']*$md['Quantity'];
			$medicinesString .= "</td>";
		$medicinesString .= "</tr>";

		$medicinesTotalAmount += $md['Amount']*$md['Quantity'];

	}
	$mainTotalAmount += $medicinesTotalAmount;
	$medicinesTotalAmount = number_format($medicinesTotalAmount, 2);
	$medicinesString .= "
					<tr>
						<td colspan='7' style='border:0px solid #000; text-align:right; padding-right:10px;'>
						<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td style='text-align: right'>
							<span style='font-size: 12px; font-weight:bold;'>{$medicinesTotalAmount}</span></td>
						</td>
					</tr>";
}
$patientTotalAmount = $mainTotalAmount*15/100;
$patientTotalAmount = roundUp($patientTotalAmount, 10);
$mmiTotalAmount = $mainTotalAmount - $patientTotalAmount;

$mainTotalAmount = number_format($mainTotalAmount, 2);
$patientTotalAmount = number_format($patientTotalAmount);
$mmiTotalAmount = number_format($mmiTotalAmount, 2);
$info = <<<INFO
<html><head><title>MMI Patient</title></head><body>
<style>
	.withborders td{
		border:1px solid #000;
		vertical-align:top;
	}
	.all_border{
		border-collapse:collapse;
	}
	.all_border th, .all_border td{
		border: 1px solid #000;
	}

	.all_border th{
		background-color:#cecece;
		color:#000;
		font-size:9px;
	}
	.all_border td{
		font-size:10px;
	}

	span.normal_bold{
		font-weight:bold;
		font-size:12px;
	}

	span.title_header{
		font-weight:bold;
	}

	span.underline{
		text-decoration:underline;
	}
	
	.inner_table td{margin-left:-2px;}
	.inner_table{ border:0px solid #000; margin-left:-3px;}
	._history{ border-collapse:collapse; border:1px solid #000; }
	._history td{vertical-align:top; border-bottom:1px solid #000; font-weight:normal; font-family:arial; }
	.maladie td{font-size:12px; font-family:arial;}
	td.box{border:1px solid #000; width:40px; }
</style>
<table style='border:0px solid #000; font-size:13px; font-family:sans-serif; width:800px;'>
	<tr>
		<th align=left style='border:0px solid #000;'>
			MILITARY MEDICAL INSURANCE (MMI)<br />
			B.P. 6219<br />
			KIGALI<br />
		</th>
	</tr>
	<tr>
		<td style='text-align:center; border:0px solid #000;'>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span class='title_header underline'>FEUILLE DE PRISE EN CHARGE</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			N<sup>0</sup>: {$record['serialNumber']} 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Date: {$record['DateIn']}
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #000;'>
			<div style='font-weight:normal; font-family:sans-serif; font-size:11px;'>
				<span class='normal_bold'>A. AFFILIE</span><br />
				N<sup>o</sup> d'Affiliation: <span class='normal_bold'>{$affiliate_info['FamilyCode']}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Nom et Prenom: <span class='normal_bold'>{$affiliate_info['Name']}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Sex: <span class='normal_bold'>{$affiliate_info['Sex']}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Age: <span class='normal_bold'>{$age}</span>
			</div>
			<div style='font-size:11px; font-weight:normal; font-family:sans-serif;'>
				<span class='normal_bold'>B. BENEFICIAIRE</span><br />
				- ADHERENT LUI-MEME<br />
				- CONJOINT / - ENFANT / N<sup>o</sup> <span class='normal_bold'>{$record['InsuranceCardID']}</span>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				NOM ET PRENOM: <span class='normal_bold'>{$patient['Name']}</span>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				SEXE:<span class='normal_bold'>{$patient['Sex']}</span>
				&nbsp;&nbsp;&nbsp;&nbsp;
				Age: <span class='normal_bold'>{$patientAge}</span>
			</div><br />
			<div style='font-size:11px; font-weight:normal; font-family:sans-serif;'>
				<span class='normal_bold'>D. TYPE DE MALADIE:</span>&nbsp;
				MALADIE NATURELLE/MALADIE PROFFESSIONNELLE/ACCIDENT DE TRAVAIL
				/ACCIDENT DE CIRCULATION
			</div><br />
			<div style='font-size:12px; font-weight:bold; font-family:sans-serif;'>
				C. ETABLISSEMENT SANITAIRE: Nom:{$institution_name} 
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;
				CODE: {$institution_code}
			</div><br />
			<div style='font-size:12px; font-weight:normal; font-family:sans-serif;'>
				<span class='normal_bold'>D. SOINS MEDICAUX CONSOMMES</span><br />&nbsp;<br />
				<span class='normal_bold' style='margin-top:15px;'>I. CONSULTATION</span>
				<table class='all_border' style='width:800px;'>
					<tr>
						<th style='width:80px;'>Date</th>
						<th style='width:50px;'>Code</th>
						<th style='text-align:left;'>Désignation</th>
						<th style='width:100px;'>Montant</th>
					</tr>
					<tr>
						<td>{$consultationStringDate}</td>
						<td></td>
						<td>{$consultationString}</td>
						<td>{$consultationStringAmount}</td>
					</tr>
					<tr>
						<td colspan='3' style='border:0px solid #000; text-align:right; padding-right:10px;'>
							<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td style='text-align:right;'>
							<span style='font-size: 12px; font-weight:bold;'>{$consultationStringAmount}</span>
						</td>
					</tr>
				</table>
				<span class='normal_bold' style='margin-top:-15px;'>II. HOSPITALISATION</span>
				<table class='all_border' style='width:800px;'>
					<tr>
						<th style='width:80px;'>Code</th>
						<th style='width:50px;'>&nbsp;</th>
						<th style='text-align:left;'>Désignation</th>
						<th style='text-align:left;'>Date d'admission</th>
						<th style='text-align:left;'>Date de sortie</th>
						<th style='text-align:left;'>Nbre de jrs</th>
						<th style='text-align:left;'>Prix Unitaire</th>
						<th style='width:100px;'>Prix Total</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>{$hospString}</td>
						<td>{$hospStringAdmission}</td>
						<td>{$hospStringSortie}</td>
						<td>{$hospStringJours}</td>
						<td>{$hospStringUnitPrice}</td>
						<td>{$hospStringAmount}</td>
					</tr>
					<tr>
						<td colspan='7' style='border:0px solid #000; text-align:right; padding-right:10px;'>
							<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td style='text-align:right;'>
							<span style='font-size: 12px; font-weight:bold;'>{$hospStringAmount}</span>
						</td>
					</tr>
				</table>

				<span class='normal_bold' style='margin-top:15px;'>III. EXAMENS MEDICAUX</span>
				<table class='all_border' style='width:800px;'>
					<tr>
						<th style='width:80px;'>Date</th>
						<th style='width:50px;'>Code</th>
						<th style='text-align:left;'>Désignation</th>
						<th style='width:100px;'>Montant</th>
					</tr>
					{$examsStringData}
				</table>
				<span class='normal_bold' style='margin-top:15px;'>IV. ACTES MEDICAUX ET CONSOMMABLES</span>
				<table class='all_border' style='width:800px;'>
					<tr>
						<th style='width:80px;'>Date</th>
						<th style='width:50px;'>Code</th>
						<th style='text-align:left;'>Désignation</th>
						<th style='width:100px;'>Montant</th>
					</tr>
					<tr>
						<td>{$actConsummablesStringDate}</td>
						<td></td>
						<td>{$actConsummablesString}</td>
						<td>{$actConsummablesStringAmount}</td>
					</tr>
					<tr>
						<td colspan='3' style='border:0px solid #000; text-align:right; padding-right:10px;'>
						<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td style='text-align:right;'>
							<span style='font-size: 12px; font-weight:bold;'>{$actConsummablesTotalAmount}</span>
						</td>
					</tr>
				</table>

				<span class='normal_bold' style='margin-top:15px;'>V. MEDICAMENTS FOURNIS</span>
				<table class='all_border' style='width:800px;'>
					<tr>
						<th style='width:20px;'>N<sup>o</sup></th>
						<th style='width:50px;'>Code</th>
						<th style='text-align:left;'>Nom Produits Fournis</th>
						<th style='width:50px;'>Forme</th>
						<th style='width:50px;'>Dosage</th>
						<th style='width:50px;'>QTE</th>
						<th style='width:70px;'>Prix Unitaire</th>
						<th style='width:100px;'>Prix Total</th>
					</tr>
					{$medicinesString}
				</table>
				<span class='normal_bold' style='margin-top:15px;'>VI. AUTRES ACTES</span>
				<table class='all_border' style='width:800px;'>
					<tr>
						<th style='width:80px;'>Date</th>
						<th style='width:50px;'>Code</th>
						<th style='text-align:left;'>Désignation</th>
						<th style='width:100px;'>Montant</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan='3' style='border:0px solid #000; text-align:right; padding-right:10px;'>
						<span style='font-size: 13px; font-weight:bold;'>Total</span></td>
						<td></td>
					</tr>
				</table>
				<br />
				<table class='all_border' style='width:800px;'>
					<tr>
						<td style='border:0px solid #000;'>&nbsp;</td>
						<th style='width: 100px; text-align:left; border-right:0px solid #000;'>Total</th>
						<th style='width: 50px; text-align:right; border-left:0px solid #000;'>100%</th>
						<td style='width:100px;'>{$mainTotalAmount}</td>
					</tr>
					<tr>
						<td style='border:0px solid #000;'>&nbsp;</td>
						<th style='width: 100px; text-align:left; border-right:0px solid #000;'>ADHERENT</th>
						<th style='width: 50px; text-align:right; border-left:0px solid #000;'>15%</th>
						<td style='width:100px;'>{$patientTotalAmount}</td>
					</tr>
					<tr>
						<td style='border:0px solid #000;'>
							Fait à <span style='font-weight:bold; font-size:9px'>{$client}</span>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							Le <span style='font-weight:bold; font-size:9px'>{$record['DateIn']}</span>
						</td>
						<th style='width: 100px; text-align:left; border-right:0px solid #000;'>MMI</th>
						<th style='width: 50px; text-align:right; border-left:0px solid #000;'>85%</th>
						<td style='width:100px;'>{$mmiTotalAmount}</td>
					</tr>
				</table>
				<br />
				<table class='all_border' style='width:800px; font-size:10px;'>
					<tr>
						<td style='border:0px solid #000;'>
							Sign bénéficiaire<br />ou du donnant droit
						</td>
						<td style='border:0px solid #000;'>
							Nom, Cachet signature du prestataire<br />
							{$consultantName} {$nurseSignature}
						</td>
						<td style='border:0px solid #000;'>
							Cachet de la FOSA
						</td>
						<td style='border:0px solid #000; text-align:right;'>
							VISA MMI
						</td>
					</tr>
				</table>
				
			</div>
		</td>
	</tr>
</table>
</body>

</html>
INFO;

//echo $info; die;

//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($info);

$pdf->Output(); 

die;
?>