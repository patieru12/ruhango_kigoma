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
$age = "";
if($affiliate_info['DateofBirth'] != null)
	$age = $affiliate_info['DateofBirth'] == "0000-00-00"?"":getAge($affiliate_info['DateofBirth']);
//$family = !$patient['FamilyCode']?null:formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE N_ID='{$patient['FamilyCode']}'",$con),$multirows=false,$con);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//$records = null;
if($existing_cons)
	$date_ = $existing_cons[0]['Date'];
//select three last records and display them
$records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID, pa_records.PatientRecordID from pa_records, co_records  WHERE pa_records.PatientRecordID != '{$_GET['records']}' && pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientID='{$record['PatientID']}' && pa_records.DateIn < '{$date_}' ORDER BY co_records.Date DESC LIMIT 0, 3",$con),$multirows=true,$con);
$to_day_records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID from pa_records, co_records  WHERE pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientRecordID='{$_GET['records']}' ORDER BY co_records.Date DESC LIMIT 0, 3",$con),$multirows=true,$con);
//var_dump($to_day_records);

$old_consultation = "";
$last = "";
if($records){
	//$records[] = $records[0];
	$last .= "
	<table class=_history style=' border:0px solid #000;'>
	<tr><td>Date</td><td>Diagnostic</td><td>Exams</td><td>Medecines</td><td>Acts</td><td>Hosp.</td></tr>";
	foreach($records as $r){
		//var_dump($r);
		$last .= "<tr valign=top><td>{$r['Date']}</td><td style='width:150px;'>";
		//search for diagnostic
		$diag_ = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic_records.*, co_diagnostic.DiagnosticName FROM co_diagnostic_records, co_diagnostic  WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = '{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		if($diag_){
			foreach($diag_ as $dg){
				$last .= ($dg['DiagnosticType'] == 1?"Pr.":"Sec.").": ".$dg['DiagnosticName']."; ".($dg['CaseType']?"AC":"NC");
			}
		}
		$last .= "</td><td style='width:250px;'>";
		//select exams
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT la_records.*, la_exam.ExamName, la_result.ResultName from la_result_record, la_result, la_records, la_price, la_exam  WHERE la_result.ResultID = la_result_record.ResultID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ConsultationRecordID='{$r['ConsultationRecordID']}' LIMIT 0, 4",$con),$multirows=true,$con);
		//echo $sql;
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['ResultNumber']} {$ex['ExamName']}: <font color=red>{$ex['ResultName']}</font><br />";
			}
		}
		$last .= "</td><td style='width:250px;'>";
		//select medecines
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.*, md_name.MedecineName from md_records, md_price, md_name  WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.ConsultationRecordID='{$r['ConsultationRecordID']}' LIMIT 0, 4",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Quantity']} {$ex['MedecineName']}<br />";
			}
		}
		$last .= "</td><td style='width:150px;'>";
		//select acts
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT ac_records.*, ac_name.Name FROM ac_records, ac_price, ac_name  WHERE ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_records.PatientRecordID='{$r['PatientRecordID']}' LIMIT 0, 4",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Name']}<br />";
			}
		}
		$last .= "</td><td style='width:98px;'>";
		//select hospitalisation history now
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT ho_record.* FROM ho_record WHERE ho_record.RecordID='{$r['PatientRecordID']}' LIMIT 0, 4",$con),$multirows=true,$con);
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['Days']} Jrs<br />";
			}
		}
		$last .= "</td></tr>";
	}
	$last .= "</table>";
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
	$consult_str .= $c['ConsultationCategoryName'] != "invisible"?"<td style='text-align:center; border:0px solid #000; width:120px; text-align:right;'>".($c['ConsultationCategoryName'] != "invisible"?$c['ConsultationCategoryName']:"not applicable")."</td><td class=box></td>":"";
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
	foreach($diag as $di){
		$diagno .= ($di['DiagnosticType'] == 1?"Principal":"Secondary").":".$di['DiagnosticName']."<br />";
	}
	
	//select all exams for the existing record
	$exm = returnAllData("SELECT la_exam.*, la_result.*, la_records.* FROM la_exam, la_records, la_result_record, la_result WHERE la_exam.ExamID = la_result.ExamID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
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
	foreach($cns as $di){
		$existing_consumables .= ($count_++).". ".$di['MedecineName']." Qty:".$di['Quantity']."<br />";
		if(!preg_match("/{$di['Date']}/",$existing_consumables_date))
			$existing_consumables_date .= $di['Date']."<br />";
		else
			$existing_consumables_date .= "<br />";
	}
}
$info = <<<INFO
<html><head><title>MMI Patient</title></head><body>
<style>
	.withborders td{
		border:1px solid #000;
		vertical-align:top;
	}
	
	.inner_table td{margin-left:-2px;}
	.inner_table{ border:0px solid #000; margin-left:-3px;}
	._history{ border-collapse:collapse; border:1px solid #000; }
	._history td{vertical-align:top; border-bottom:1px solid #000; font-weight:normal; font-family:arial; }
	.maladie td{font-size:12px; font-family:arial;}
	td.box{border:1px solid #000; width:40px; }
</style>
<table style='border:0px solid #000; font-size:16px; font-family:sans-serif; width:800px;'>
	<tr>
		<th align=left style='border:0px solid #000;'>
			MILITARY MEDICAL INSURANCE (MMI)<br />
			B.P. 6219<br />
			KIGALI<br />
		</th>
	</tr>
	<tr>
		<td style='text-align:center; border:0px solid #000;'>
			FEUILLE DE PRISE EN CHARGE N<sup>0</sup>: {$record['DocID']} 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Date: {$record['DateIn']}
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #000;'>
			<table style='width:800px; border:0px solid #000; font-size:16px;'>
				<tr>
					<td style='vertical-align:top; width:750px; border:0px solid #000;'>
						<div style='font-weight:normal; font-family:sans-serif;'>
						A. AFFILIE <br />
						N<sup>o</sup> d'Affiliation: {$affiliate_info['FamilyCode']} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Nom et Prenom: {$affiliate_info['Name']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Sex: {$affiliate_info['Sex']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Age: {$age}</div>
						<br /><div style='font-size:16px; font-weight:normal; font-family:sans-serif;'>
						B. BENEFICIAIRE <br />{$bene}
						</div><br />
						<div style='font-size:13px; font-weight:normal; font-family:sans-serif;'>
						B. TYPE DE MALADIE <br />
						MALADIE NATURELLE/MALADIE PROFFESSIONNELLE/ACCIDENT DE TRAVAIL
						/ACCIDENT DE CIRCULATION
						</div><br />
						<div style='font-size:16px; font-weight:normal; font-family:sans-serif;'>
						C. ETABLISSEMENT SANITAIRE: Nom:{$institution_name} 
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						CODE: {$institution_code} <br />
						</div>
					</td>
					<td style='border:0px solid #000; vertical-align:top; width:100px;'>
						Village: <b>{$patient_address['VillageName']}</b><br />
						Cell: <b>{$patient_address['CellName']}</b><br />
						Sector: <b>{$patient_address['SectorName']}</b><br />
						District: <b>{$patient_address['DistrictName']}</b><br />
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #000;'>
			History of 2 last visits<br/ >
			{$last}
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #f00;'>
			D. Details on received acts
			<table class='withborders' style='width:800px; border-collapse:collapse;'>
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
					<td style='height:90px'><br />{$existing_exams_date}<br />&nbsp;</td>
					<td colspan='{$count_consult}'>Exam:<br />{$existing_exams}<br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:120px'><br />{$existing_medicines_date}<br />&nbsp;</td>
					<td colspan='{$count_consult}'>Medicines:<br />{$existing_medicines}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:90px'><br />{$existing_consumables_date}<br />&nbsp;</td>
					<td colspan='{$count_consult}'>Consumables:<br />{$existing_consumables}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:30px'></td>
					<td colspan='{$count_consult}'>Hospitalization:<br />Number of Days:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;From ----------/----------/---------- TO ----------/----------/----------</td>
				</tr>
				<tr valign=top>
					<td style='height:50px'></td>
					<td colspan='{$count_consult}'>Other acts:<br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:30px'></td>
					<td colspan='{$count_consult}'>Decision:
						<table>
							<tr>
								<td style='text-align:center; border:0px solid #000; width:220px; text-align:right;'>Sortie Definitif</td><td style='width:40px'></td>
								<td style='text-align:center; border:0px solid #000; width:220px; text-align:right;'>Transfer Avec Ambulance</td><td style='width:40px'></td>
								<td style='text-align:center; border:0px solid #000; width:220px; text-align:right;'>Transfer Sans Ambulance</td><td style='width:40px'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='height:50px;'>Done at {$location} on {$record['DateIn']}</td>
	</tr>
	<tr>
		<td>
			<table style='width:800px;' border=0>
				<tr style=''>
					
					<td style='vertical-align:top; text-align:center;'>
						Beneficiary's Signature
						<br />
						<br />&nbsp;
					</td>
					<td style='vertical-align:top; text-align:center;'>
						Nom, cachet et signature du prestataire
					</td>
					<td style='vertical-align:top; text-align:center;'>
						VISA MMI
					</td>
				</tr>
			</table>
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