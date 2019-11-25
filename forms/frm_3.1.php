<?php
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title=RSSB-RAMA");
$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$record['PatientRecordID']}'",$con),$multirows=false,$con);
$date_ = date("Y-m-d",time());
$count_consult = 3; count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category, se_consultation WHERE se_consultation.ServiceID = '{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID",$con),$multirows=true,$con));
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);
$patient_address = formatResultSet($rslt=returnResultSet($sql="SELECT ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName from ad_village, ad_cell, ad_sector, ad_district WHERE ad_village.ViillageID='{$patient['VillageID']}' && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID",$con),$multirows=false,$con);
//echo $sql;
$affiliate_info = formatResultSet($rslt=returnResultSet($sql="SELECT pa_insurance_cards.* from pa_insurance_cards WHERE InsuranceCardsID='{$record['InsuranceCardID']}' && PatientID='{$patient['PatientID']}'",$con),$multirows=false,$con);
//echo $sql; die;

$patient['Name'] = ucwords(strtolower($patient['Name']));
$nd_str = "<img src='../images/box.png' style='width:35px;' />";
if($record['visitPurpose'] == 1){
	$nd_str = "<img src='../images/box-checked.png' style='width:35px;' />";
}

$od_str = "<img src='../images/box.png' style='width:35px;' />";
if($record['visitPurpose'] == 2){
	$od_str = "<img src='../images/box-checked.png' style='width:35px;' />";
}

$other_str = "<img src='../images/box.png' style='width:35px;' />";
if($record['visitPurpose'] == 3){
	$other_str = "<img src='../images/box-checked.png' style='width:35px;' />";
}

$rta_str = "<img src='../images/box.png' style='width:35px;' />";
if($record['visitPurpose'] == 5){
	$rta_str = "<img src='../images/box-checked.png' style='width:35px;' />";
}

$wa_str = "<img src='../images/box.png' style='width:35px;' />";
if($record['visitPurpose'] == 4){
	$wa_str = "<img src='../images/box-checked.png' style='width:35px;' />";
}

$genderMale = "<img src='../images/box.png' style='width:35px;' />";
if($patient['Sex'] == "Male"){
	$genderMale = "<img src='../images/box-checked.png' style='width:35px;' />";
}
$genderFemale = "<img src='../images/box.png' style='width:35px;' />";
if($patient['Sex'] == "Female"){
	$genderFemale = "<img src='../images/box-checked.png' style='width:35px;' />";
}
// var_dump($affiliate_info);
$bene = "";
$ben = "<table border=0 style='border-collapse:collapse; width:100%;  font-size:12px;'>";


$age = getAge($patient['DateofBirth']);

$patient['DateofBirth'] = str_replace("-", "/", $patient['DateofBirth']);
if($affiliate_info['Relation'] == "Self" ){
		
	$bene = <<<BEN
	<br />Nom et Prenom:<br />
	Age:{$age}<br />
	Sex:{$patient['Sex']}<br />
BEN;
	$ben .= "<tr><td>Adhérent lui-même</td><td style='text-align:center'><img src='../images/box-checked.png'  style='width:35px;' /></td>";
	$ben .= "<tr><td>Conjoint</td><td><img src='../images/box.png' style='width:35px;' /></td><td> Nom et Prénom: </td>";
	$ben .= "<tr><td>Enfant</td><td><img src='../images/box.png' style='width:35px;' /></td><td> Nom et Prénom: </td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Age: <span style='font-weight:bold;'>{$patient['DateofBirth']} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$age}</span></td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Sex: M {$genderMale}&nbsp;&nbsp;&nbsp;&nbsp;F {$genderFemale}</td>";
} else if($affiliate_info['Relation'] == "Conjoint") {
	$ben .= "<tr><td>Adhérent lui-même</td><td><img src='../images/box.png' style='width:35px;' /></td>";
	$ben .= "<tr><td>Conjoint</td><td><img src='../images/box-checked.png' style='width:35px;' /></td><td> Nom et Prénom: <span style='font-weight:bold;'>{$patient['Name']}</span></td>";
	$ben .= "<tr><td>Enfant</td><td><img src='../images/box.png' style='width:35px;' /></td><td> Nom et Prénom: </td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Age: <span style='font-weight:bold;'>{$patient['DateofBirth']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$age}</span></td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Sex: M {$genderMale}&nbsp;&nbsp;&nbsp;&nbsp;F {$genderFemale}</td>";

} else if($affiliate_info['Relation'] == "Parent") {
	$ben .= "<tr><td>Adhérent lui-même</td><td><img src='../images/box.png' style='width:35px;' /></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
	$ben .= "<tr><td>Conjoint</td><td><img src='../images/box.png' style='width:35px;' /></td><td> Nom et Prénom: </td>";
	$ben .= "<tr><td>Enfant</td><td> <img src='../images/box-checked.png' style='width:35px;' /></td><td style=''> Nom et Prénom: <span style='font-weight:bold;'>{$patient['Name']}</span></td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Age: <span style='font-weight:bold;'>{$patient['DateofBirth']}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$age}</span> </td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Sex: M {$genderMale}&nbsp;&nbsp;&nbsp;&nbsp;F {$genderFemale}</td>";

} else {
		
	$bene = <<<BEN
	<br />Nom et Prenom:<br />
	Age:{$age}<br />
	Sex:{$patient['Sex']}<br />
BEN;
	$ben .= "<tr><td>Adhérent lui-même</td><td style='text-align:center'><img src='../images/box.png'  style='width:35px;' /></td>";
	$ben .= "<tr><td>Conjoint</td><td><img src='../images/box.png' style='width:35px;' /></td><td> Nom et Prénom: </td>";
	$ben .= "<tr><td>Enfant</td><td><img src='../images/box.png' style='width:35px;' /></td><td> Nom et Prénom: <span style='font-weight:bold;'>{$patient['Name']}</span></td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Age: <span style='font-weight:bold;'>{$patient['DateofBirth']} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$age}</span></td>";
	$ben .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td> Sex: M {$genderMale}&nbsp;&nbsp;&nbsp;&nbsp;F {$genderFemale}</td>";
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
$existing_cons = returnAllData("SELECT 	a.*,
										b.Amount AS Amount,
										c.ConsultationCategoryName,
										d.Name AS consultantName,
										d.signature AS signature
										FROM co_records AS a
										INNER JOIN co_price AS b
										ON a.ConsultationPriceID = b.ConsultationPriceID
										INNER JOIN co_category AS c
										ON b.ConsultationCategoryID = c.ConsultationCategoryID
										LEFT JOIN sy_users AS d
										ON a.ConsultantID = d.UserID
										WHERE a.PatientRecordID='{$record['PatientRecordID']}'
										",$con);
// var_dump($existing_cons); die();
$invisibl_ = true;

$consultationString = "";
$consultationStringAmount = "";

$mainTotalAmount = 0;
$consultantName = "";

$nurseSignature = "";


foreach($existing_cons as $c){
	if(!$consultantName){
		$consultantName = $c['consultantName'];
		if($c['signature']){
			$nurseSignature = "<img src='../images/signatures/{$c['signature']}' style='max-height:25px;' />";
		}
	}
	//var_dump($c);
	if($c['ConsultationCategoryName'] != "invisible"){
		$consultationString .= $c['ConsultationCategoryName']."<br />";
		$invisibl_ = false;
	} else{
		$consultationString .= "No Consultation<br />";

	}
	$consultationStringAmount .= $c['Amount']."<br />";

	$mainTotalAmount += $c['Amount'];
	
}
if(!$invisibl_)
	$consult_str .= "<td style='text-align:center; border:0px solid #000; width:150px; text-align:right;'>Register No:</td><td style='width:100px;'></td>";
$consult_str .= "</tr></table>";
$diagno = "";
$existing_medicines = "";
$existing_medicinesAmount = "";
$existing_consumables = "";
$existing_consumables_date = "";
$existing_exams = "";
$existing_examsAmount = "";
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
	$exm = returnAllData("SELECT 	a.MonthlyID AS examNumber,
									b.Amount AS Amount,
									c.ExamName AS ExamName
									FROM la_records AS a
									INNER JOIN la_price AS b
									ON a.ExamPriceID = b.ExamPriceID
									INNER JOIN la_exam AS c
									ON b.ExamID = c.ExamID
									WHERE a.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}' &&
										  a.MonthlyID IS NOT NULL
									",$con);
	//var_dump($exm);
	$count_ = 1;
	if($exm)
		foreach($exm as $di){
			$existing_exams .= $di['examNumber'].". ".ucfirst(strtolower($di['ExamName']))."<br />";
			$existing_examsAmount .= $di['Amount']."<br />";

			$mainTotalAmount += $di['Amount'];
			
		}
	
	//select all medecines for the existing record
	$mds = returnAllData("SELECT md_name.*, md_records.*, md_price.Amount FROM md_name, md_records, md_price WHERE md_name.MedecineNameID = md_price.MedecineNameID && md_price.MedecinePriceID = md_records.MedecinePriceID && md_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
	if($mds)
		foreach($mds as $di){
			$existing_medicines .= ucfirst(strtolower($di['MedecineName']))."; ".$di['SpecialPrescription']."; dose tot: ".$di['Quantity']."<br />";
			$existing_medicinesAmount .= ($di['Quantity']*$di['Amount'])."<br />";

			$mainTotalAmount += ($di['Quantity']*$di['Amount']);
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

//get hospitalisation info
$hospRecords = returnAllData("SELECT 	a.StartDate,
										a.EndDate,
										b.Amount,
										c.Name
										FROM ho_record AS a
										INNER JOIN ho_price AS b
										ON a.HOPriceID = b.HOPriceID
										INNER JOIN ho_type AS c
										ON b.HOTypeID = c.TypeID
										WHERE a.RecordID = '{$record['PatientRecordID']}'
										",$con);

// var_dump($hospRecords); die();
$hospitalisationString = "";
$hospitalisationStringAmount = "";
if(count($hospRecords) > 0){
	foreach($hospRecords AS $h){
		$days = getAge($h['StartDate'], 1, $h['EndDate'], true);
		$hospitalisationString .= ucfirst(strtolower($h['Name']))." ".$days." day".($days > 1?"s":"")."<br />";
		$hospitalisationStringAmount .= $days*$h['Amount']."<br />";

		$mainTotalAmount += ($days*$h['Amount']);
	}
}

$actRecords = returnAllData("SELECT 	a.Quantity,
										b.Amount,
										c.Name
										FROM ac_records AS a
										INNER JOIN ac_price AS b
										ON a.ActPriceID = b.ActPriceID
										INNER JOIN ac_name AS c
										ON b.ActNameID = c.ActNameID
										WHERE a.PatientRecordID = '{$record['PatientRecordID']}'
										",$con);
$actString = "";
$actStringAmount = "";
if(count($actRecords) > 0){
	foreach($actRecords AS $h){
		$actString .= ucfirst(strtolower($h['Name']))." ".$h['Quantity']."<br />";
		$actStringAmount .= $h['Quantity']*$h['Amount']."<br />";

		$mainTotalAmount += ($h['Quantity']*$h['Amount']);
	}
}

$consummableRecords = returnAllData("SELECT 	a.Quantity,
												b.Amount,
												c.MedecineName AS Name
												FROM cn_records AS a
												INNER JOIN cn_price AS b
												ON a.MedecinePriceID = b.MedecinePriceID
												INNER JOIN cn_name AS c
												ON b.MedecineNameID = c.MedecineNameID
												WHERE a.PatientRecordID = '{$record['PatientRecordID']}'
												",$con);
$consumableString = "";
$consumableStringAmount = "";
if(count($consummableRecords) > 0){
	foreach($consummableRecords AS $h){
		$consumableString .= ucfirst(strtolower($h['Name']))." ".$h['Quantity']."<br />";
		$consumableStringAmount .= $h['Quantity']*$h['Amount']."<br />";

		$mainTotalAmount += ($h['Quantity']*$h['Amount']);
	}
}

$patientPart = $mainTotalAmount*15/100;
$patientPart = roundUp($patientPart, 10);
$insurancePart = $mainTotalAmount - $patientPart;
$mainTotalAmount = number_format($mainTotalAmount, 2);
$patientPart = number_format($patientPart);
$insurancePart = number_format($insurancePart, 2);

$patient['FamilyCode'] = ucwords(strtolower($patient['FamilyCode']));
// die();
$info = <<<INFO
<html><head><title>RSSB-RAMA Patient</title></head><body>
<style>
	.withborders td, .withborders th{
		border:1px solid #000;
		vertical-align:top;
		font-size: 11px;
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
		<th style='text-align:lext; font-size:13px;'>
			RWANDA SOCIAL SECURITY BOARD<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;
			(RSSB)<br />
			<span style='font-weight:normal; font-size:12px;'>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Tel: +250 252 598 400<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fax: +250 252 584 445</span>
			<div style='position:absolute; top:80px; right:250px;'>
				<span style='font-size:11px;'>Série:</span> N<sup>o</sup> {$record['serialNumber']}
			</div>
			<div style='position:absolute; top:80px; right:70px; font-size:10px;'>
				TP04
			</div>
		</th>
	</tr>
	<tr>
		<td style='text-align:center'>
			<span style='font-weight:bold; font-size:13px;'>FACTURE POUR SOINS DE SANTE N<sup>0</sup>:</span> {$record['DocID']}
			<div style='font-size:11px; position:absolute; top:100px; right:70px; border:0px solid #000; text-align:center;'>
				Code Médecin
				<div style='border:1px solid #000; height'><br />&nbsp;</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<table style='width:100%; border:0px solid #000; font-size:12px;'>
				<tr>
					<td style='vertical-align:top; border:0px solid #000; width:450px;'>
						REGION SANITAIRE:<span style='font-weight:bold;'>{$rssb_rama_region}</span><br />
						DISTRICT DE SANTE:<span style='font-weight:bold;'>{$rssb_rama_district}</span><br />
						FORMATION SANITAIRE:<span style='font-weight:bold;'>{$institution_name}</span><div style='height:40px;'>&nbsp;</div>
						N<sup>o</sup> d'Affiliation: <span style='font-weight:bold;'>{$record['InsuranceCardID']}</span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						/ Tél:<span style='font-weight:bold;'>{$patient['phoneNumber']}</span><br />
						N<sup>o</sup> Matricule:<br />
						Nom et Prénom de l'Adhérent:<span style='font-weight:bold;'>{$patient['FamilyCode']}</span><br />
						N<sup>o</sup> de Carte d'Identité: <span style='font-weight:bold;'>{$record['applicationNumber']}</span><br />
						Department Affectataire:<span style='font-weight:bold;'>{$affiliate_info['AffiliateNumber']}</span><br />
						Lieu d'Affectation:<span style='font-weight:bold;'>{$affiliate_info['Affectation']}</span><br />
					</td>
					<td style='vertical-align:top; border:0px solid #000;'>
						<table class=maladie>
							<tr><td style='font-size:11px;'>MALADIE NATURELLE</td><td>{$nd_str}</td></tr>
							<tr><td style='font-size:11px;'>MALADIE PROFFESSIONNELLE</td><td>{$od_str}</td></tr>
							<tr><td style='font-size:11px;'>ACCIDENT DE TRAVAIL</td><td>{$rta_str}</td></tr>
							<tr><td style='font-size:11px;'>ACCIDENT DE CIRCULATION</td><td>{$wa_str}</td></tr>
							<tr><td style='font-size:11px;'>AUTRE</td><td>{$other_str}</td></tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border=0 style='width:700px; border-collapse:collapse;'>
				<tr>
					<td style='vertical-align:top; font-size:12px; width:160px'><span style='font-weight:bold; font-size:10px;'>Nom du Beneficiare de soins:</span></td>
					<td style='vertical-align:top; width:500px;'>{$ben}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #f00; text-align:center; text-decoration:underline; font-weight:bold; font-size:11px;'>
			DETAILS DES SOINS RECUS
		</td>
	</tr>
	<tr>
		<td style='border:0px solid #f00;'>
			<table class='withborders' style='width:700px;border-collapse:collapse;'>
				<tr valign='top'>
					<th style='width:90px;'>Date</td>
					<th style='width:450px;'>Libellé (actes)</td>
					<th>Coût(en chiffre)</td>
				</tr>
				<tr>
					<td style="text-align:center; height:40px"><br /><span style='font-weight:bold; font-size:9px;'>{$record['DateIn']}</span></td>
					<td>Consultation:<br />&nbsp;<span style='font-weight:bold; font-size:9px;'>{$consultationString}</span></td>
					<td><br /><span style='font-weight:bold; font-size:9px;'>{$consultationStringAmount}</span></td>
				</tr>
				<tr valign=top>
					<td><br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;</td>
					<td>Exam:<br /><span style='font-weight:bold; font-size:9px;'>{$existing_exams}</span></td>
					<td><br /><span style='font-weight:bold; font-size:9px;'>{$existing_examsAmount}</span></td>
				</tr>
				<tr valign=top>
					<td style='height:100px'>&nbsp;</td>
					<td>Médicaments:<br /><span style='font-weight:bold; font-size:9px;'>{$existing_medicines}</td>
					<td><br /><span style='font-weight:bold; font-size:9px;'>{$existing_medicinesAmount}</td>
				</tr>
				<tr valign=top>
					<td style='height:40px'>&nbsp;</td>
					<td>Hospitalisation (Nombre de Jours)<br /><span style='font-weight:bold; font-size:9px;'>{$hospitalisationString}</span></td>
					<td><br /><span style='font-weight:bold; font-size:9px;'>{$hospitalisationStringAmount}</td>
				</tr>
				<tr valign=top>
					<td style='height:110px'>&nbsp;</td>
					<td>Autre (Spécifier):<br /><span style='font-weight:bold; font-size:9px;'>{$actString} {$consumableString}</span></td>
					<td><br/><span style='font-weight:bold; font-size:9px;'>{$actStringAmount} {$consumableStringAmount}</span></td>
				</tr>
				<tr valign=top>
					<td style='height:50px'>&nbsp;</td>
					<td style='padding-left:80px;'>
						<table border=0 style='border:0px solid #000; font-size: 10px;'>
							<tr>
								<td style='width:80px; border:0px solid #000; font-weight:bold;'>TOTAL</td><td style='text-align:right; border:0px solid #000; font-weight:bold;'>100%:</td><td style='width:200px; border:0px solid #000; font-weight:bold;'>{$mainTotalAmount}</td>
							</tr>
							<tr>
								<td style='border:0px solid #000; font-weight:bold;'>ADHERENT</td><td style='text-align:right; border:0px solid #000; font-weight:bold;'>15%:</td><td style='border:0px solid #000; font-weight:bold;'>{$patientPart}</td>
							</tr>
							<tr>
								<td style='border:0px solid #000; font-weight:bold;'>RSSB</td><td style='text-align:right; border:0px solid #000; font-weight:bold;'>85%:</td><td style='border:0px solid #000; font-weight:bold;'>{$insurancePart}</td>
							</tr>
						</table>
					</td>
					<td></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style='height:20px; font-size:12px;'>Date: {$record['DateIn']}</td>
	</tr>
	<tr>
		<td>
			<table style='width:700px; font-size:11px;' border=0>
				<tr style=''>
					<td style='height:70px; width:300px;vertical-align:top'>
						Nom, Prénom, Signature et Cachet du Médecin ou infirmier(ère) traitant
						<br /><span style='font-weight:bold;'>{$consultantName}</span>{$nurseSignature}<br />&nbsp;<br />
						Approbation du titulaire de la FOSA
					</td>
					<td style='vertical-align:top; width:300px; text-align:center;'>
						Nom, Prénom et Signature du bénéficiaire ou du donnant droit<br />
						<span style='font-weight:bold;'>{$patient['Name']}</span><br />&nbsp;<br />
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

// echo $info; die();

//require the MPDF Library
// require_once "../lib/mpdf57/mpdf.php";

$pdf = new mPDF();

$pdf->Open();

$pdf->AddPage();

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML( $info);

$pdf->Output(); 
die;
?>