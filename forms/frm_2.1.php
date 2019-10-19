<?php
session_start();
/* header("Content-Type: application/pdf"); */
//var_dump($_SESSION);
require_once "../lib/db_function.php";
header("Title='CBHI FORM");
$record = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.* from pa_records WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
$tm_paid = formatResultSet($rslt=returnResultSet($sql="SELECT mu_tm.* from mu_tm WHERE PatientRecordID='{$_GET['records']}'",$con),$multirows=false,$con);
//var_dump($tm_paid); die;
$tm = "";
if($tm_paid['TicketPaid'] == 0){
	$tm = "<tr><th>&nbsp;</th><th style='font-size:10px; border:1px solid #000;'>TM Not Paid!</th></tr>";
}
$service = formatResultSet($rslt=returnResultSet($sql="SELECT se_records.*, se_name.ServiceCode from se_records, se_name WHERE se_name.ServiceNameID = se_records.ServiceNameID && se_records.PatientRecordID='{$record['PatientRecordID']}'",$con),$multirows=false,$con);
$date_ = $record['DateIn'];
//var_dump($service); die;
$count_consult = 3; count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category, se_consultation WHERE se_consultation.ServiceID = '{$service['ServiceNameID']}' && se_consultation.ConsulationID = co_category.ConsultationCategoryID ORDER BY ConsultationCategoryName ASC",$con),$multirows=true,$con));
$patient = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE PatientID='{$record['PatientID']}'",$con),$multirows=false,$con);
$patient_address = formatResultSet($rslt=returnResultSet($sql="SELECT ad_village.VillageName, ad_cell.CellName, ad_sector.SectorName, ad_district.DistrictName from ad_village, ad_cell, ad_sector, ad_district WHERE ad_village.ViillageID='{$patient['VillageID']}' && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID",$con),$multirows=false,$con);
//$family = !$patient['FamilyCode']?null:formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.* from pa_info WHERE N_ID='{$patient['FamilyCode']}'",$con),$multirows=false,$con);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//$records = null;
if($existing_cons)
	$date_ = $existing_cons[0]['Date'];
//select three last records and display them
$records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID, pa_records.PatientRecordID from pa_records, co_records  WHERE pa_records.PatientRecordID != '{$_GET['records']}' && pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientID='{$record['PatientID']}' && pa_records.DateIn < '{$date_}' ORDER BY co_records.Date DESC LIMIT 0, 2",$con),$multirows=true,$con);
$to_day_records = formatResultSet($rslt=returnResultSet($sql="SELECT co_records.*, pa_records.DocID from pa_records, co_records  WHERE pa_records.PatientRecordID = co_records.PatientRecordID && pa_records.PatientRecordID='{$_GET['records']}' ORDER BY co_records.Date DESC LIMIT 0, 2",$con),$multirows=true,$con);
//var_dump($to_day_records);

$old_consultation = "";
$last = "";
if($records){
	//$records[] = $records[0];
	$last .= "<div>
	Historique 2 dernières visites
	<table class=_history><tr><td>Date</td><td>Diagnostic</td><td>Exams</td><td>Medecines</td><td>Acts</td><td>Hosp.</td></tr>";
	foreach($records as $r){
		//var_dump($r);
		$last .= "<tr valign=top><td style='width:70px;'>{$r['Date']}</td><td style=''>";
		//search for diagnostic
		$diag_ = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic_records.*, co_diagnostic.DiagnosticName FROM co_diagnostic_records, co_diagnostic  WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = '{$r['ConsultationRecordID']}' ORDER BY DiagnosticType ASC",$con),$multirows=true,$con);
		if($diag_){
			foreach($diag_ as $dg){
				$last .= ($dg['DiagnosticType'] == 1?"Pr.":"Sec.").": ".$dg['DiagnosticName']."; ".($dg['CaseType']?"AC":"NC")."<br />";
			}
		}
		$last .= "</td><td style='width:150px;'>";
		//select exams
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT la_records.*, la_exam.ExamName, la_result.ResultName from la_result_record, la_result, la_records, la_price, la_exam  WHERE la_result.ResultID = la_result_record.ResultID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ConsultationRecordID='{$r['ConsultationRecordID']}'",$con),$multirows=true,$con);
		//echo $sql;
		if($exams){
			$ck= 0;
			foreach($exams as $ex){
				$last .= "{$ex['ExamName']}: <font color=red>{$ex['ResultName']}</font><br />";
			}
		}
		$last .= "</td><td style='width:170px;'>";
		//select medecines
		$exams = formatResultSet($rslt=returnResultSet($sql="SELECT md_records.*, md_name.MedecineName from md_records, md_price, md_name  WHERE md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.ConsultationRecordID='{$r['ConsultationRecordID']}' ORDER BY MedecineName ASC LIMIT 0, 4",$con),$multirows=true,$con);
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
		$last .= "</td><td style='width:70px'>";
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
$consult_str = "<table style='border:0px solid #000;'><tr>";
$empty_cells = "";
$old_consultation_cells = "";
//var_dump($record);
$existing_cons = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$record['PatientRecordID']}'",$con);
//var_dump($existing_cons);
$register_id = is_array($existing_cons)?$existing_cons[0]['RegisterNumber']:"";
$invisibl_ = true;
foreach($conslt as $c){
	//var_dump($c);
	if($c['ConsultationCategoryName'] != "invisible"){
		$invisibl_ = false;
	}
	$consult_str .= $c['ConsultationCategoryName'] != "invisible"?"<td style='vertical-align:bottom; text-align:center; width:10px;'>".($c['ConsultationCategoryID'] == returnSingleField("SELECT co_price.ConsultationCategoryID FROM co_price WHERE co_price.ConsultationPriceID='{$to_day_records[0]['ConsultationPriceID']}'","ConsultationCategoryID",true,$con)?"<img src='../images/close.png' style='width:15px;' />":"&nbsp;")."</td></tr><tr><td style='text-align:center; border:0px solid #000; width:10px; padding-bottom:20px;'>{$c['ConsultationCategoryName']}</td></tr><tr>":"";
	//var_dump($existing_cons);
	if($existing_cons)
		$empty_cells .= "<td style='height:20px'>".(($ssss = returnSingleField($s_q_l = "SELECT co_price.ConsultationPriceID FROM co_price WHERE ConsultationPriceID='{$existing_cons[0]['ConsultationPriceID']}' && ConsultationCategoryID='{$c['ConsultationCategoryID']}'","ConsultationPriceID",$data=true, $con)) == $existing_cons[0]["ConsultationPriceID"]?"<center><img src='../images/close.png' style='width:15px;' /></center>":"")."</td>";
	else
		$empty_cells .= "<td style='height:20px'></td>";
}

$consult_str .= "<td style='vertical-align:bottom; text-align:center; width:20px;'>".($c['ConsultationCategoryID'] == returnSingleField("SELECT co_price.ConsultationCategoryID FROM co_price WHERE co_price.ConsultationPriceID='{$to_day_records[0]['ConsultationPriceID']}'","ConsultationCategoryID",true,$con)?"<img src='../images/close.png' style='width:15px;' />":"&nbsp;")."</td></tr><tr><td style='text-align:center; border:0px solid #000; width:10px; padding-bottom:20px;'>AC</td></tr>";
$consult_str .= "</tr></table>";
$diagno = "";
$existing_medicines = "";
$existing_medicines_date = "";
$existing_consumables = "";
$existing_consumables_date = "";
$existing_exams = "";
$existing_acts = "";
$existing_acts_date = "";
$existing_exams_date = "";
if($existing_cons){
	//select all diagno for the existing record
	$diag = returnAllData("SELECT co_diagnostic.*, co_diagnostic_records.* FROM co_diagnostic, co_diagnostic_records WHERE co_diagnostic.DiagnosticID = co_diagnostic_records.DiagnosticID && co_diagnostic_records.ConsulationRecordID='{$existing_cons[0]['ConsultationRecordID']}' ORDER BY DiagnosticType ASC",$con);
	//var_dump($diag);
	if($diag)
		foreach($diag as $di){
			$diagno .= ($di['DiagnosticType'] == 1?"Principal":"Secondary").":".$di['DiagnosticName']." ".($di['CaseType']?"AC":"NC")."<br />";
		}
	
	//select all exams for the existing record
	$exm = returnAllData("SELECT la_exam.*, la_result.*, la_records.* FROM la_exam, la_records, la_result_record, la_result WHERE la_exam.ExamID = la_result.ExamID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_records.ConsultationRecordID = '{$existing_cons[0]['ConsultationRecordID']}'",$con);
	//var_dump($exm);
	$count_ = 1;
	if($exm)
		foreach($exm as $di){
			$existing_exams .= ($di['ResultNumber']).". ".$di['ExamName']." Result:".$di['ResultName']."<br />";
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
			$existing_medicines .= $di['Quantity']." ".$di['MedecineName']."<br />";
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
			$existing_consumables .= $di['Quantity']." ".$di['MedecineName']."<br />";
			if(!preg_match("/{$di['Date']}/",$existing_consumables_date))
				$existing_consumables_date .= $di['Date']."<br />";
			else
				$existing_consumables_date .= "<br />";
		}
		
	//select all Acts for the existing record
	$cns = returnAllData("SELECT ac_name.*, ac_records.* FROM ac_name, ac_records, ac_price WHERE ac_name.ActNameID = ac_price.ActNameID && ac_price.ActPriceID = ac_records.ActPriceID && ac_records.PatientRecordID = '{$existing_cons[0]['PatientRecordID']}'",$con);
	//var_dump($cns);
	$count_ = 1;
	if($cns)
		foreach($cns as $di){
			$existing_acts .= $di['Quantity']." ".$di['Name']."<br />";/* 
			if(!preg_match("/{$di['Date']}/",$existing_consumables_date))
				$existing_consumables_date .= $di['Date']."<br />";
			else
				$existing_consumables_date .= "<br />"; */
		}
	//select all Diagnostic for the existing record
	$cns = returnAllData("SELECT ac_name.*, ac_records.* FROM ac_name, ac_records, ac_price WHERE ac_name.ActNameID = ac_price.ActNameID && ac_price.ActPriceID = ac_records.ActPriceID && ac_records.PatientRecordID = '{$existing_cons[0]['PatientRecordID']}'",$con);
	//var_dump($cns);
	$count_ = 1;
	if($cns)
		foreach($cns as $di){
			$existing_acts .= $di['Quantity']." ".$di['Name']."<br />";/* 
			if(!preg_match("/{$di['Date']}/",$existing_consumables_date))
				$existing_consumables_date .= $di['Date']."<br />";
			else
				$existing_consumables_date .= "<br />"; */
		}
}
$age = getAge($patient['DateofBirth'],$record['DateIn']);
$file_number = explode("/",$record['InsuranceCardID'])[2];
//echo $file_number; die;
$info = <<<INFO
<html><head><title>CBHI Patients</title></head><body>
<style>
	.withborders{
		border-collapse:collapse;
	}
	.withborders td{
		border:1px solid #000;
		vertical-align:top;
		padding:2px;
		text-align:center;
	}
	.withborders td.body_td{
		height:250px;
	}
	
	.body_tb{
		width:1000px
	}
	
	.header_table{
		border:0px solid #000;
		font-size:12px;
		font-family:arial;
	}
	.inner_table td{margin-left:-2px;}
	.inner_table{ border:0px solid #000; margin-left:-3px;}
	._history{ border-collapse:collapse; }
	._history td{vertical-align:top; border-bottom:1px solid #000; font-size:10px; font-weight:normal; font-family:arial; }
</style>
<table border=0 style='border:0px solid #000; width:100%;'>
	{$tm}
	<tr>
		<th align=left style='border:0px solid #000;'>
			RWANDA SOCIAL SECURITY BOARD (RSSB) / <br />
			Community Based Healh Insurance (CBHI)
		</th>
		<th style='text-align:center; font-size:12px; border:0px solid #000;'>
			PATIENT'S FILE / FICHE INDIVIDUELLE DES PRESTATIONS N<sup>r</sup>: {$record['DocID']}
		</th>
	</tr>
	<tr>
		<td colspan=2>
			<table width=100% style='border:0px solid #000; font-size:8px; font-family:arial; width:1000px;'>
				<tr>
					<td style=' width:220px; vertical-align:top; border:0px solid #000;'>
						<table class='header_table'>
							<tr>
								<td>PROVINCE/MVK</td>
								<td>{$PROVINCE}</td>
							</tr>
							<tr>
								<td>ADMINISTRATIVE DISTRICT</td>
								<td>{$DISTRICT}</td>
							</tr>
							<tr>
								<td>ADMINISTRATIVE SECTION</td>
								<td>{$SECTOR}</td>
							</tr>
							<tr>
								<td>HEALTH FACILITY</td>
								<td>{$institution_name}</td>
							</tr>
						</table>
					</td>
					<td style=' width:340px; vertical-align:top; text-align:center; border:0px solid #000;'>
						<table class='header_table'>
							<tr>
								<td>Beneficiary's Name</td>
								<td>{$patient['Name']}</td>
							</tr>
							<tr>
								<td>Affiliation Number</td>
								<td>{$record['InsuranceCardID']}</td>
							</tr>
							<tr>
								<td>Sex</td>
								<td>{$patient['Sex']}</td>
							</tr>
							<tr>
								<td>Date Of Birth</td>
								<td>{$patient['DateofBirth']}</td>
							</tr>
							<tr>
								<td>Age</td>
								<td>{$age}</td>
							</tr>
						</table>
					</td>
					<td style='border:0px solid #000; width:270px; vertical-align:top; border:0px solid #000;'>
						<table class='header_table'>
							<tr>
								<td colspan=2 style='text-align:center;'>HOUSEHOLD </td>
							</tr>
							<tr>
								<td>Name</td>
								<td>{$patient['FamilyCode']}</td>
							</tr>
							<tr>
								<td>Family's Code</td>
								<td>{$file_number}</td>
							</tr>
							<tr>
								<td>Member's Cat.</td>
								<td>{$record['FamilyCategory']}</td>
							</tr>
						</table>
					</td>
					<td style='border:0px solid #000; width:200px; vertical-align:top; border:0px solid #000;'>
						<table class='header_table'>
							<tr>
								<td colspan=2 style='text-align:center;'>Address </td>
							</tr>
							<tr>
								<td>Village</td>
								<td>{$patient_address['VillageName']}</td>
							</tr>
							<tr>
								<td>Cell</td>
								<td>{$patient_address['CellName']}</td>
							</tr>
							<tr>
								<td>Sector</td>
								<td>{$patient_address['SectorName']}</td>
							</tr>
							<tr>
								<td>District</td>
								<td>{$patient_address['DistrictName']}</td>
							</tr>
						</table>
					</td>
				</tr>
			</table><!--
			<table class=inner_table style='display:none;' width=100% style='width:100%'>
				<tr>
					<td colspan=3>CBHI Code: <b>{$record['InsuranceCardID']}</b></td>
					<td>Village: <b>{$patient_address['VillageName']}</b></td>
				</tr>
				<tr>
					<td style='width:370px'>Patient: <b>{$patient['Name']}</b></td>
					<td style='width:210px'>Age: <b>{$age}</b></td>
					<td style='width:130px; text-align:left;'>Sex: <b>{$patient['Sex']}</b></td>
					<td>Cell: <b>{$patient_address['CellName']}</b></td>
				</tr>
				<tr>
					<td>Family Chief: <b>{$patient['FamilyCode']}</b></td>
					<td colspan=2>Family Category: <b>{$record['FamilyCategory']}</b></td>
					<td> Sector: <b>{$patient_address['SectorName']}</b></td>
				</tr>
				<tr>
					<td>
						File Number: {$file_number}
					</td>
					<td colspan=2>Service: <b>{$service['ServiceCode']}</b></td>
					<td>District: <b>{$patient_address['DistrictName']}</b></td>
				</tr>
				<tr>
					<td colspan=4 style='text-align:center'>Document Number:{$record['DocID']}</td>
				</tr>
			</table>-->
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td>Service</td><td style='width:80px;'>{$service['ServiceCode']}</td>
							</tr>
							<tr>
								<td style=''>Weight(Kg):</td><td><b>{$record['Weight']}</b></td>
							</tr>
							<tr>
								<td style=''>Temp.(<sup>o</sup>C):</td><td><b>{$record['Temperature']}</b></td>
							</tr>
							<tr>
								<td style=''>Register:</td><td style='border:1px solid #000; height:40px;'><b>{$register_id}</b></td>
							</tr>
						</table>
					</td>
					<td>
						{$last}
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<table class='withborders body_tb'>
				<tr>
					<!--<td rowspan=2>N<sup>o</sup></td>-->
					<td rowspan=2 style='width:70px;'>Date</td>
					<td rowspan=2>Diagnostic</td>
					<td rowspan=2>Consultation</td>
					<td rowspan=2>Laboratory</td>
					<td rowspan=2>Medicines</td>
					<td rowspan=2>Acts</td>
					<td rowspan=2>Materials</td>
					<td rowspan=2 style='width:50px;'>Hospitalization</td>
					<td colspan=3>Transfer</td><!--
					<td rowspan=2>Others</td>-->
					
				</tr>
				<tr>
					
					<td style='width:20px;'>Amb.</td>
					<td style='width:20px;'>Yes</td>
					<td style='width:20px;'>No</td>
				</tr>
				<tr>
					<!-- <td class=body_td></td>-->
					<td class=body_td>{$record['DateIn']}</td>
					<td class=body_td></td>
					<td style='width:90px;'>{$consult_str}</td>
					<!--<td class=body_td></td>-->
					<td class=body_td>{$existing_exams}</td>
					<td class=body_td>{$existing_medicines}</td>
					<td class=body_td>{$existing_acts}</td>
					<td class=body_td>{$existing_consumables}</td>
					<td class=body_td><br /><br />Days:<br /><br />
					____________</br><br /><br />
						In:<br /><br />
					____________<br /><br /><br />
						Out:<br />
					____________<br />
					</td>
					<td class=body_td></td>
					<td class=body_td></td><!--
					<td class=body_td></td>-->
					<td class=body_td></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 style='border:0px solid #000;'>
			<table style='border:0px solid #000;' border=0>
				<tr valign=top>
					<td style='width:510px; border:0px solid #000;'>
						Nurse's Name & Signature
					</td><!--
					<td style='width:510px; text-align:right; border:0px solid #000;'>
						Beneficiary's Name<br /> 
						{$patient['Name']}
					</td>-->
				</tr>
			</table>
			<div style='color:#f0f0f0; position:absolute; bottom:0px; right:0px;'>{$developer}</div>
		</td>
	</tr><!--
	<tr>
		<td colspan=2 style='border:0px solid #f00;'>
			<br />
			Details on received acts
			
			
			<table class='withborders' style='width:900px;border-collapse:collapse;'>
				<tr valign='top'>
					<td rowspan=2  style='width:90px;'>Date</td><td colspan='2'>ITEM</td>
					<td rowspan=2 style='width:500px;'><table style=''>
					<tr><td style='width:150px;border:0px solid #000;'>Diagnostic:</td><td style='border:0px solid #000;'>NC</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>AC</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>PECIME</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td><td style='border:0px solid #000;'>Hosp.</td><td style='border:1px solid #000; width:40px;'>&nbsp;&nbsp;&nbsp;</td></tr>
					<tr><td style='width:150px;border:0px solid #000;'>Principal:</td></tr>
					<tr><td style='width:150px;border:0px solid #000;'>Secondary:</td></tr>
					</table><br />{$diagno} </td>
				</tr>
				
				<tr>
					<td style="text-align:center;">{$record['DateIn']}</td>
					<td colspan=3>{$consult_str}</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_exams_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Exam:<br />{$existing_exams}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_medicines_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Medicines:<br />{$existing_medicines}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:90px'></td>
					<td colspan='{$count_consult}'>Acts:<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:100px'><br />{$existing_consumables_date}<br /><br /><br />&nbsp;</td>
					<td colspan='{$count_consult}'>Consumables:<br />{$existing_consumables}<br /><br />&nbsp;</td>
				</tr>
				<tr valign=top>
					<td style='height:50px'></td>
					<td colspan='{$count_consult}'>
						Hospitalization: Days and Type: <br /> <br />
						Date In: ......./........./.........&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Date Out: ......./......../.........&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
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
		<td colspan=2 style='height:20px;'>Date: {$record['DateIn']}</td>
	</tr>
	<tr>
		<td colspan=2 style='border:0px solid #000;'>
			<table width=100% style='border:0px solid #000;' border=0>
				<tr valign=top>
					<td style='width:400px; border:0px solid #000;'>
						Nurse's Name & Signature
					</td>
					<td style='width:510px; text-align:right; border:0px solid #000;'>
						Beneficiary's Name: 
						{$patient['Name']}
					</td>
				</tr>
			</table>
		</td>
	</tr>-->
</table>
</body>

</html>
INFO;

//echo $info; die;

//require the MPDF Library
require_once "../lib/mpdf57/mpdf.php";

$pdf = new MPDF();

$pdf->Open();

$pdf->AddPage("L");

$pdf->SetFont("Arial","N",10);

$pdf->WriteHTML($info);
$filename = "./files/".$record['DocID'].".pdf";
//echo $filename;
$pdf->Output(); 
die;
?>
<script type="text/javascript" language=JavaScript>
    function CheckIsIE()
    {
        if (navigator.appName.toUpperCase() == 'MICROSOFT INTERNET EXPLORER') 
            { return true;  }
        else 
            { return false; }
    }
    function PrintThisPage()
    {
         if (CheckIsIE() == true)
         {
            document.content.focus();
            document.content.print();
         }
         else
         {
            window.frames['iframeprint'].focus();
            window.frames['iframeprint'].print();
         }
     }
</script> 
<link href="./print.css" rel="stylesheet" type="text/css" media="print" />
<button value='print' onclick='PrintThisPage()'></button>
<script type="text/javaScript" src='../js/jquery.full.js'></script>
<script>
jQuery(document).ready(function($) {
  function print(url)
  {
      var _this = this,
          iframeId = 'iframeprint',
          $iframe = $('iframe#iframeprint');
		  
      $iframe.attr('src', url);

      $iframe.load(function() {
          callPrint(iframeId);
		  //console.log($iframe);
      });
  }

  //initiates print once content has been loaded into iframe
  function callPrint(iframeId) {
	  //alert("Print trigger_error");
      var PDF = document.getElementById(iframeId);
		//window.frames['iframeprint'].focus();
		//window.frames['iframeprint'].print();
      /* PDF.focus();
	  try{
			
			console.log("Attempt to print");
			PDF.contentWindow.print();
			//
			console.log("End of print attempt");
	  } catch(e){
		  console.log(e);
		  window.print();
	  } */
  }
  //try to call print function now
  print("<?= $filename ?>");
});
</script>

<iframe id="iframeprint" name="iframeprint" width='99%' height='98%'></iframe>

<?php die; ?>
<style type="text/css">
    
        .dontprint{display:none} 
    
</style>
<script type="text/javascript">
    function printIframePdf(){
        window.frames["printf"].focus();
        try {
            window.frames["printf"].print();
        }
        catch(e){
            window.print();
            console.log(e);
        }
    }
    function printObjectPdf() {
        try{            
            document.getElementById('idPdf').Print();
        }
        catch(e){
            printIframePdf();
            console.log(e);
        }
    }

    function idPdf_onreadystatechange() {
        if (idPdf.readyState === 4){
			alert("Ready to print now"); return;
            setTimeout(printObjectPdf, 1000);
		}
    }
</script>
<div class="dontprint" >
    <form><input type="button" onClick="printObjectPdf()" class="btn" value="Print"/></form>
</div>

<iframe id="printf" onreadystatechange='alert("OK");' name="printf" src="<?= $filename ?>" frameborder="0" width="440" height="580" style="width: 99%; height: 98%;display: none;"></iframe>
<object id="idPdf" onreadystatechange="alert('OK');idPdf_onreadystatechange()"
    width="440" height="580" style="width: 99%; height: 98%;" type="application/pdf"
    data="<?= $filename ?>">
    <embed src="<?= $filename ?>" width="440" height="580" style="width: 440px; height: 580px;" type="application/pdf">
    </embed>
    <span>PDF plugin is not available.</span>
</object>