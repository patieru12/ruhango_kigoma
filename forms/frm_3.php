<?php
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title=RSSB-RAMA Form");
$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$record['PatientRecordID']}'",$con),$multirows=false,$con);
$date_ = date("Y-m-d",time());
$count_consult = 3; count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category, se_consultation WHERE se_consultation.ServiceID = '{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID",$con),$multirows=true,$con));
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);
$patient_address = formatResultSet($rslt=returnResultSet($sql="SELECT ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName from ad_village, ad_cell, ad_sector, ad_district WHERE ad_village.ViillageID='{$patient['VillageID']}' && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID",$con),$multirows=false,$con);
//echo $sql;
$affiliate_info = formatResultSet($rslt=returnResultSet($sql="SELECT pa_insurance_cards.* from pa_insurance_cards WHERE InsuranceCardsID='{$record['InsuranceCardID']}' && PatientID='{$patient['PatientID']}'",$con),$multirows=false,$con);
//echo $sql; die;
$bene = "";
$ben = "<table>";
if($patient['Name'] == $patient['FamilyCode'] ){
		
		$age = getAge($patient['DateofBirth']);
		$bene = <<<BEN
		<br />Nom et Prenom:<br />
		Age:{$age}<br />
		Sex:{$patient['Sex']}<br />
BEN;
		$ben .= "<tr><td>ADHERENT LUI-MEME</td><td class=box style='text-align:center'><img src='../images/checked.png' height=10 /></td>";
		$ben .= "<tr><td>Conjoint</td><td class=box></td>";
		$ben .= "<tr><td>Enfant</td><td class=box></td>";
	} else {
		$age = getAge($patient['DateofBirth']);
		$bene = <<<BEN
		<br />Nom et Prenom:{$patient['Name']}<br />
		Age: {$age}<br />
		Sex: {$patient['Sex']}<br />
BEN;
		$ben .= "<tr><td>ADHERENT LUI-MEME</td><td class=box style='border:1px solid #000; text-align:center;'></td>";
		$ben .= "<tr><td>Conjoint</td><td class=box style='border:1px solid #000; text-align:center;'></td>";
		$ben .= "<tr><td>Enfant</td><td class=box style='border:1px solid #000; text-align:center;'></td>";
	
	}
$ben .= "</table>";
//$bene = "";
//$family = !$patient['FamilyCode']?null:formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE N_ID='{$patient['FamilyCode']}'",$con),$multirows=false,$con);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//$records = null;
if($existing_cons)
	$date_ = $existing_cons[0]['Date'];
//select three last records and display them
$records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID, pa_records.PatientRecordID from pa_records, co_records  WHERE pa_records.PatientRecordID != '{$_GET['records']}' && pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientID='{$record['PatientID']}' && pa_records.DateIn < '{$date_}' ORDER BY co_records.Date DESC LIMIT 0, 2",$con),$multirows=true,$con);
$to_day_records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID from pa_records, co_records  WHERE pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientRecordID='{$_GET['records']}' ORDER BY co_records.Date DESC LIMIT 0, 3",$con),$multirows=true,$con);
//var_dump($to_day_records);

$old_consultation = "";
$last = "";
if($records){
	//$records[] = $records[0];
	$last .= "<div style='font-size:13px;'>
	<table class=_history><tr><td>Date</td><td>Diagnostic</td><td>Exams</td><td>Medecines</td><td>Acts</td><td>Hosp.</td></tr>";
	foreach($records as $r){
		//var_dump($r);
		$last .= "<tr valign=top><td>{$r['Date']}</td><td style='width:200px;'>";
		//search for diagnostic
		$diag_ = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic_records.*, co_diagnostic.DiagnosticName FROM co_diagnostic_records, co_diagnostic  WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = '{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		if($diag_){
			foreach($diag_ as $dg){
				$last .= ($dg['DiagnosticType'] == 1?"Pr.":"Sec.").": ".$dg['DiagnosticName']."; ".($dg['CaseType']?"AC":"NC");
			}
		}
		$last .= "</td><td style='width:150px;'>";
		//select exams
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT la_records.*, la_exam.ExamName, la_result.ResultName from la_result_record, la_result, la_records, la_price, la_exam  WHERE la_result.ResultID = la_result_record.ResultID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		//echo $sql;
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['ExamName']}: <font color=red>{$ex['ResultName']}</font>";
			}
		}
		$last .= "</td><td style='width:300px;'>";
		//select medecines
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.*, md_name.MedecineName from md_records, md_price, md_name  WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Quantity']} {$ex['MedecineName']}<br />";
			}
		}
		$last .= "</td><td style='width:150px'>";
		//select acts
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT ac_records.*, ac_name.Name FROM ac_records, ac_price, ac_name  WHERE ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_records.PatientRecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Name']}<br />";
			}
		}
		$last .= "</td><td>";
		//select hospitalisation history now
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT ho_record.* FROM ho_record WHERE ho_record.RecordID='{$r['PatientRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Days']} Jrs<br />";
			}
		}
		$last .= "</td></tr>";
	}
	$last .= "</table></div>";
} else{
	$last = "No Previous Record";
}
//var_dump($records);
//die;
$consult_str = "<table><tr>";
$empty_cells = "";
$old_consultation_cells = "";
//var_dump($record);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//var_dump($existing_cons);
$invisibl_ = true;
foreach($conslt as $c){
	//var_dump($c);
	if($c['ConsultationCategoryName'] != "invisible"){
		$invisibl_ = false;
	}
	//var_dump($c);
	$consult_str .= $c['ConsultationCategoryName'] != "invisible"?"<td style='text-align:center; border:0px solid #000; width:120px; text-align:right;'>".($c['ConsultationCategoryName'] != "invisible"?$c['ConsultationCategoryName']:"not applicable")."</td><td class=box>&nbsp;&nbsp;&nbsp;</td>":"";
	//var_dump($existing_cons);
	if($existing_cons)
		$empty_cells .= "<td style='height:20px'>".(($ssss = returnSingleField($s_q_l = "SELECT co_price.ConsultationPriceID FROM co_price WHERE ConsultationPriceID='{$existing_cons[0]['ConsultationPriceID']}' && ConsultationCategoryID='{$c['ConsultationCategoryID']}'","ConsultationPriceID",$data=true, $con)) == $existing_cons[0]["ConsultationPriceID"]?"<center><img src='../images/checked.png' width='30px' /></center>":"")."</td>";
	else
		$empty_cells .= "<td style='height:20px'></td>";
}
if(!$invisibl_)
	$consult_str .= "<td style='text-align:center; border:0px solid #000; width:150px; text-align:right;'>Register No:</td><td style='width:100px;'></td>";
$consult_str .= "</tr></table>";
$diagno = "";
$existing_medicines = "";
$existing_medicines_date = "";
$existing_consumables = "";
$existing_consumables_date = "";
$existing_exams = "";
$existing_exams_date = "";
if($existing_cons){
	//select all diagno for the existing record
	$diag = returnAllData("SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic, co_diagnostic_records WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && co_diagnostic_records.ConsulationRecordID='{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($diag);
	if($diag)
		foreach($diag as $di){
			$diagno .= ($di['DiagnosticType'] == 1?"Principal":"Secondary").":".$di['DiagnosticName']."<br />";
		}
	
	//select all exams for the existing record
	$exm = returnAllData("SELECT la_exam.*, la_result.*, la_records.* FROM la_exam, la_records, la_result_record, la_result WHERE la_exam.ExamID = la_result.ExamID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
	if($exm)
		foreach($exm as $di){
			$existing_exams .= ($count_++).". ".$di['ExamName']." Result:".$di['ResultName']."<br />";
			if(!preg_match("/{$di['ResultDate']}/",$existing_exams_date))
				$existing_exams_date .= $di['ResultDate']."<br />";
			else
				$existing_exams_date .= "<br />";
		}
	
	//select all medecines for the existing record
	$mds = returnAllData("SELECT md_name.*, md_records.* FROM md_name, md_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_price.MedecinePriceID = md_records.MedecinePriceID && md_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
	if($mds)
		foreach($mds as $di){
			$existing_medicines .= ($count_++).". ".$di['MedecineName']." Qty:".$di['Quantity']."<br />";
			if(!preg_match("/{$di['Date']}/",$existing_medicines_date))
				$existing_medicines_date .= $di['Date']."<br />";
			else
				$existing_medicines_date .= "<br />";
		}
	
	//select all consummables for the existing record
	$cns = returnAllData("SELECT md_name.*, cn_records.* FROM md_name, cn_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_price.MedecinePriceID = cn_records.MedecinePriceID && cn_records.PatientRecordID = '{$existing_cons[0]['PatientRecordID']}'",$con);
	//var_dump($cns);
	$count_ = 1;
	if($cns)
		foreach($cns as $di){
			$existing_consumables .= ($count_++).". ".$di['MedecineName']." Qty:".$di['Quantity']."<br />";
			if(!preg_match("/{$di['Date']}/",$existing_consumables_date))
				$existing_consumables_date .= $di['Date']."<br />";
			else
				$existing_consumables_date .= "<br />";
		}
}
$info = <<<INFO
<html><head><title>RSSB-RAMA Patient</title></head><body>
<style>
	.withborders td{
		border:1px solid #000;
		vertical-align:top;
	}
	
	.inner_table td{margin-left:-2px;}
	.inner_table{ border:0px solid #000; margin-left:-3px;}
	._history{ border-collapse:collapse; width:900px; }
	._history td{vertical-align:top; border-bottom:0px solid #000; font-size:12px; font-weight:normal; font-family:arial; }
	.maladie td{font-size:12px; font-family:arial;}
	td.box{border:1px solid #000; width:40px; }
</style>
<table width=100% style='border:0px solid #000; font-size:16px; font-family:sans-serif; width:800px;'>
	<tr>
		<th style='text-align:lext; font-size:14px;'>
			RWANDA SOCIAL SECURITY BOARD<br />
			(RSSB)<br />
			Tel: +250 252 598 400<br />
			Fax: +250 252 584 445<br />
		</th>
	</tr>
	<tr>
		<td style='text-align:center'>
			FACTURE POUR SOINS DE SANTE N<sup>0</sup>: {$record['DocID']}
		</td>
	</tr>
	<tr>
		<td>
			<table style='width:100%; border:0px solid #000;'>
				<tr>
					<td style='vertical-align:top; border:0px solid #000; width:700px;'>
						REGION SANITAIRE:<br />
						DISTRICT DE SANTE:<br />
						FORMATION SANITAIRE:{$institution_name}<div style='height:40px;'>&nbsp;</div>
						N<sup>o</sup> d'Affiliation: {$record['InsuranceCardID']} 
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						/ Tel:<br />
						N<sup>o</sup> Matricule:<br />
						Nom et Prenom:{$patient['FamilyCode']}<br />
						N<sup>o</sup> de Carte d'identite:<br />
						Department Affectataire:{$affiliate_info['AffiliateNumber']}<br />
						Lieu d'Affectation:{$affiliate_info['Affectation']}<br />
					</td>
					<td style='vertical-align:top; border:0px solid #000;'>
						<table class=maladie>
							<tr><td>MALADIE NATURELLE</td><td class=box>&nbsp;&nbsp;</td></tr>
							<tr><td>MALADIE PROFFESSIONNELLE</td><td class=box>&nbsp;&nbsp;</td></tr>
							<tr><td>ACCIDENT DE TRAVAIL</td><td class=box>&nbsp;&nbsp;</td></tr>
							<tr><td>ACCIDENT DE CIRCULATION</td><td class=box>&nbsp;&nbsp;</td></tr>
							<tr><td>AUTRE</td><td class=box>&nbsp;&nbsp;</td></tr>
							<tr><td colspan=2>&nbsp;&nbsp;</td></tr>
							<tr><td colspan=2>
								Village: <b>{$patient_address['VillageName']}</b><br />
								Cell: <b>{$patient_address['CellName']}</b><br />
								Sector: <b>{$patient_address['SectorName']}</b><br />
								District: <b>{$patient_address['DistrictName']}</b><br />
							</td></tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table style='border:0px solid #000; width:680px;'>
				<tr>
					<td style='vertical-align:top; border:0px solid #000;' colspan=5>Nom du Beneficiare de soins:</td>
					<td style='vertical-align:top;'>{$ben}</td>
					<td style='vertical-align:top; width:460px;'><br />{$bene}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #000;'>
			{$last}
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #f00;'>
			Details on received acts
			<table class='withborders' style='width:900px;border-collapse:collapse;'>
				<tr valign='top'>
					<td rowspan=2  style='width:90px;'>Date</td><td colspan='2'>ITEM</td>
					<td rowspan=2 style='width:500px;'><table style=''><tr><td style='width:150px;border:0px solid #000;'>Diagnostic:</td><td style='border:0px solid #000;'>NC</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>AC</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>PECIME</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>Hosp.</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td></tr></table><br />{$diagno} <br /><br /><br /></td>
				</tr>
				<tr>
					<td style='width:180px;'>Temperature(<sup>o</sup>C): {$record['Temperature']}</td><td style='width:160px;'>Weight(Kg): {$record['Weight']}</td>
										
				</tr>
				<tr>
					<td style="text-align:center;">{$record['DateIn']}</td>
					<td colspan=3>{$consult_str}</td>
				</tr>
				<tr valign=top>
					<td style='height:130px'><br />{$existing_exams_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Exam:<br />{$existing_exams}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:150px'><br />{$existing_medicines_date}<br />&nbsp;</td>
					<td colspan='{$count_consult}'>Medicines:<br />{$existing_medicines}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_consumables_date}<br />&nbsp;</td>
					<td colspan='{$count_consult}'>Act & Consumables:<br />{$existing_consumables}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:30px'></td>
					<td colspan='{$count_consult}'>Hospitalization:<br />Number of Days:<br />&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='height:50px;'>Date: {$record['DateIn']}</td>
	</tr>
	<tr>
		<td>
			<table style='width:900px' border=0>
				<tr style=''>
					<td style='height:70px; width:400px;vertical-align:top'>
						Nurse's Name & Signature<br /><br /><br />
						Approbation du titulaire de la FOSA
					</td>
					<td style='vertical-align:top; text-align:right;'>
						Beneficiary's Name & Signature<br />
						{$patient['Name']}
						<br /><br />
						Approbation du District
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>

</html>
INFO;

//echo $info;

//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($info);

$pdf->Output(); 
die;



/*****************************************************************************************








*******************************************************************************************/

die;
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title=RSSB-RAMA FORM");
$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
$count_consult = count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category",$con),$multirows=true,$con));
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);
$family = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE N_ID='{$patient['FamilyCode']}'",$con),$multirows=false,$con);
//select three last records and display them
$records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.* from pa_records, co_records  WHERE pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientID='{$record['PatientID']}' ORDER BY co_records.Date DESC LIMIT 0, 3",$con),$multirows=true,$con);
$last = "";
if($records){
	foreach($records as $r){
		$last .= "<div>";
		$last .= "Date: {$last['Date']}<br />";
		$last .= "Deases: {$last['DeasesFound']}<br />";
		
		//select exams
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT la_records.*, la_exam.ExamCode from la_records, la_price, la_exam  WHERE la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$last .= "Exams<br />";
			$last .= "<table style='width:100%'>";
			foreach($exams as $ex){
				$last .= "<tr><td>Code:{$ex['ExamCode']}</td><td>Result: {$ex['Result']}</td><td>Date: {$ex['ResultDate']}</td></tr>";
			}
			$last .= "</table>";
		}
		//select medecines
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.*, la_exam.ExamCode from md_records, md_price, md_name, md_prescription  WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.MedecineRecordID = md.prescription.MedecineRecordID && md_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		if($exams){
			$last .= "Medecines:<br />";
			$last .= "<table style='width:100%'>";
			foreach($exams as $ex){
				$last .= "<tr><td>:{$ex['MedecineName']}</td><td>{$ex['Result']}</td><table><tr><td>M</td><td>N</td><td>E</td><td>MD</td><td>Special</td></tr><tr><td>{$ex['Morning']}</td><td>{$ex['Noon']}</td><td>{$ex['Evening']}</td><td>{$ex['Midnight']}</td><td>{$ex['SpecialPrescription']}</td></tr></table></tr>";
			}
			$last .= "</table>";
		}
		$last .= "</div>";
	}
} else{
	$last = "No Previous Record!";
}
//var_dump($records);
//die;
$consult_str = "";
$empty_cells = "";
foreach($conslt as $c){
	$consult_str .= "<td>{$c['ConsultationCategoryName']}</td>";
	$empty_cells .= "<td style='height:20px'></td>";
}
$info = <<<INFO
<html><head><title>Private Patients</title></head><body>
<style>
	.withborders td{
		border:1px solid #000;
	}
</style>
<table width=100% border=0>
	<tr>
		<th align=left>
			Republique du Rwanda<br />
			MINISTERE DE LA SANTE <br />
			Health Center: {$institution_name}<br />
		</th>
	</tr>
	<tr>
		<th align=center>
			Prestation Document No: {$record['DocID']}
		</th>
	</tr>
	<tr>
		<td>
			<table width=100% style='width:100%'>
				<tr>
					<td colspan=3>Code: <b>{$patient['PatientID']}</b></td>
				</tr>
				<tr>
					<td style='width:370px'>Patient Name: <b>{$patient['Name']}</b></td>
					<td style='width:210px'>Birth Date: <b>{$patient['DateofBirth']}</b></td>
					<td>Sex: <b>Male</b></td>
				</tr>
				<tr>
					<td colspan=3>Family Chief Name: <b>{$family['Name']}</b></td>
					<!--<td>Birth Date: <b>{$patient['DateofBirth']}</b></td>
					<td>Sex: <b>Male</b></td>-->
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			{$last}
		</td>
	</tr>
	<tr>
		<td>
			Details on received acts
			<table width=100% border=0 class='withborders' style='border-collapse:collapse;'>
				<tr valign=top>
					<td rowspan=3>Date</td><td rowspan=3>Code</td><td colspan='{$count_consult}'>ITEM</td><td>Quantity</td><td>Unit Price</td><td>Total Price</td>
				</tr>
				<tr>
				{$consult_str}
					<td rowspan=2></td>
					<td rowspan=2></td>
					<td rowspan=2></td>
				</tr>
				<tr>
					{$empty_cells}
				</tr>
				<tr valign=top>
					<td style='height:80px'></td>
					<td></td>
					<td colspan='{$count_consult}'>Exam:<br /><br /><br />&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr valign=top>
					<td style='height:80px'></td>
					<td></td>
					<td colspan='{$count_consult}'>Medecines:<br /><br /><br /><br />&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr valign=top>
					<td style='height:80px'></td>
					<td></td>
					<td colspan='{$count_consult}'>Consummables:<br /><br /><br />&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr valign=top>
					<td style='height:50px'></td>
					<td></td>
					<td colspan='{$count_consult}'>Ambulance:<br /><br />&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr valign=top>
					<td style='height:30px'></td>
					<td></td>
					<td colspan='{$count_consult}'>Hospitalization:<br />Number of Days:<br /><br />&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr valign=top>
					<td style='height:30px'></td>
					<td></td>
					<td colspan='{$count_consult}'>Other:<br /><br />&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan='2'></td>
					<td colspan='{$count_consult}'>Total</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='height:50px;'>Date: {$record['DateIn']}</td>
	</tr>
	<tr>
		<td>
			<table width=100% border=0>
				<tr valign=top>
					<td style='height:70px; width:400px'>
						Nurse's Name & Signature
					</td>
					<td>
						Beneficiary Name & Signature<br />
						{$patient['Name']}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
INFO;

//echo $info;

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