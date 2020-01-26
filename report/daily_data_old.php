<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
$sp_condition = "";
if(@$_GET['filter']){
	$sp_condition .= " && (
		pa_records.InsuranceCardID LIKE('%{$_GET['filter']}%') ||
		pa_info.Name LIKE('%{$_GET['filter']}%') ||
		pa_info.FamilyCode LIKE('%{$_GET['filter']}%')
	)";
}
//echo $sys;
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.DocID, pa_records.PatientRecordID, pa_records.FamilyCategory, pa_records.DateIn, pa_records.InsuranceCardID, pa_info.* FROM se_name, se_records, pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district, sy_users, sy_center WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode='MAT' && pa_info.PatientID = pa_records.PatientID && pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && sy_users.CenterID = sy_center.CenterID && {$sys} && DateIn LIKE('{$_GET['year']}-{$_GET['month']}-{$_GET['day']}') {$sp_condition} ORDER BY pa_records.DateIn ASC, pa_records.PatientRecordID ASC",$con),$multirows=true,$con); // && pa_records.Status != 0 
	$date = $_GET['year']."-".$_GET['month']."-".$_GET['day'];
//echo $sql;
if($patients || !$patients){
	$all_amount = 0;
	//sum of patient CBHI
	$patient_cbhi = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount CBHI
	$patient_cbhi_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && mu_tm.Type != 'COMPASSION' && mu_tm.Type != 'DETTES' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//sum of patient CBHI
	$patient_cbhi_compassion = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && mu_tm.Type = 'COMPASSION' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount CBHI
	$patient_cbhi_amount_compassion = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && mu_tm.Type = 'COMPASSION' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//count how many CBHI user paid deterrent fees
	$facture = $ph = $patient_cbhi_paid = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && (mu_tm.Type='OK' || (mu_tm.Type='CATEGORY' && mu_tm.TicketPaid != 0 ) )","TMCBHIPAID",true,$con);
	//sum all deterrent fees paid by CBHI Patients
	$patient_cbhi_paid_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && (mu_tm.Type='OK' || (mu_tm.Type='CATEGORY' && mu_tm.TicketPaid != 0 ) )","TMCBHIPAID",true,$con);
	
	//select how many patient that can pay themselves deterrent fees
	$patient_cbhi_not_paid_indigent = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='INDIGENT'","TMCBHIPAID",true,$con);
	//sum all deterrent fees paid by CBHI Patients
	$patient_cbhi_not_paid_indigent_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='INDIGENT'","TMCBHIPAID",true,$con);
	
	//select how many patient that can pay themselves deterrent fees
	$patient_cbhi_not_paid_cat2_malaria = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='CATEGORY' && mu_tm.TicketPaid=0","TMCBHIPAID",true,$con);
	//sum all deterrent fees paid by CBHI Patients
	$patient_cbhi_not_paid_cat2_malaria_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='CATEGORY' && mu_tm.TicketPaid=0","TMCBHIPAID",true,$con);
	
	//select how many patient that can pay themselves deterrent fees
	$patient_cbhi_not_paid_pst = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='PANSEMENT'","TMCBHIPAID",true,$con);
	//sum all deterrent fees paid by CBHI Patients
	$patient_cbhi_not_paid_pst_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='PANSEMENT'","TMCBHIPAID",true,$con);
	
	//select how many patient that can pay themselves deterrent fees
	$patient_cbhi_not_paid_dettes = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='DETTES'","TMCBHIPAID",true,$con);
	//sum all deterrent fees paid by CBHI Patients
	$patient_cbhi_not_paid_dettes_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CBHI' && pa_records.DateIn='{$date}' && mu_tm.Type='DETTES'","TMCBHIPAID",true,$con);
	
	//sum of patient RSSB RAMA
	$patient_rama = returnSingleField("SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='RSSB RAMA' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount RSSB RAMA
	$patient_rama_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='RSSB RAMA' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//sum of patient MMI
	$patient_mmi = returnSingleField($sql = "SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='MMI' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount MMI
	$patient_mmi_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='MMI' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//sum of patient MEDIPLAN
	$patient_mediplan = returnSingleField($sql = "SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='MEDIPLAN' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount MEDIPLAN
	$patient_mediplan_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='MEDIPLAN' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//sum of patient CORAR
	$patient_corar = returnSingleField($sql = "SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CORAR' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount CORAR
	$patient_corar_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='CORAR' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//sum of patient PRIVATE
	$private_total = returnSingleField($sql = "SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='Private' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount PRIVATE
	$private_total_amount = returnSingleField($sql = "SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName='Private' && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//echo $sql;
	//sum of patient All
	$all_patient_number = returnSingleField($sql = "SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	//sum of amount All
	$all_patient_amount = returnSingleField("SELECT  SUM(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.DateIn='{$date}'","TMCBHIPAID",true,$con);
	
	//count how many transfer per day
	$transfer = returnSingleField($sql = "SELECT  COUNT(`TicketPaid`) AS TMCBHIPAID FROM mu_tm, pa_records, in_name WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.DateIn='{$date}' && pa_records.Status>=2","TMCBHIPAID",true,$con);
	
	//select records of amount related to the private patients
	//$private_records_amount = formatResultSet($rslt=returnResultSet($sql="SELECT SUM(`Amount`) as ConsulatationPrice FROM co_price, co_records, pa_records, in_category WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.InsuranceCategoryID = in_category.InsuranceCategoryID && in_category.InsuranceCode = 'D' && pa_records.DateIn='{$date}'",$con),$multirows=false,$con);
	//select the consultation related to the private patient
	//echo $sql;
	$amount_consultation = returnSingleField($sql="SELECT SUM(`Amount`) as ConsulatationPrice FROM co_price, co_records, pa_records, in_category WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.InsuranceCategoryID = in_category.InsuranceCategoryID && in_category.InsuranceCode = 'E' && pa_records.DateIn='{$date}'","ConsulatationPrice",true,$con);//$private_records_amount['ConsulatationPrice'];
	//$private_total_amount += $amount_consultation;
	//echo $sql;
	$amount_labo = returnSingleField($sql="SELECT SUM(`Amount`) as LaboPrice FROM la_price, la_records, co_records, pa_records, in_category WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.InsuranceTypeID = in_category.InsuranceCategoryID && in_category.InsuranceCode = 'E' && pa_records.DateIn='{$date}'","LaboPrice",true,$con);//$private_records_amount['ConsulatationPrice'];
	$private_total_amount += $amount_labo;
	$md_records = formatResultSet($rslt=returnResultSet($sql="SELECT md_price.Amount, md_records.Quantity FROM md_price, md_records, co_records, pa_records, in_name WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName = 'Private' && pa_records.DateIn='{$date}'",$con),true,$con);//$private_records_amount['ConsulatationPrice'];
	//var_dump($md_records);
	//loop all medicines found
	$amount_md = 0;
	if($md_records)
		foreach($md_records as $m){
			$amount_md += $m['Quantity'] * $m['Amount'];
		}
	$private_total_amount += $amount_md;
	$ac_without_acc_records = formatResultSet($rslt=returnResultSet($sql="SELECT ac_price.Amount, ac_records.Quantity FROM ac_price, ac_name, ac_records, pa_records, in_name WHERE pa_records.PatientRecordID = ac_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName = 'Private' && pa_records.DateIn='{$date}' && ac_name.Name NOT LIKE('%acc%')",$con),true,$con);//$private_records_amount['ConsulatationPrice'];
	//var_dump($ac_without_acc_records);
	//loop all medicines found
	$amount_act_sans_accouchement = 0;
	if($ac_without_acc_records)
		foreach($ac_without_acc_records as $m){
			$amount_act_sans_accouchement += $m['Quantity'] * $m['Amount'];
		}
	$private_total_amount += $amount_act_sans_accouchement;
	$ac_with_acc_records = formatResultSet($rslt=returnResultSet($sql="SELECT ac_price.Amount, ac_records.Quantity FROM ac_price, ac_name, ac_records, pa_records, in_name WHERE pa_records.PatientRecordID = ac_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName = 'Private' && pa_records.DateIn='{$date}' && ac_name.Name LIKE('%acc%')",$con),true,$con);//$private_records_amount['ConsulatationPrice'];
	//var_dump($ac_without_acc_records);
	//loop all medicines found
	$amount_accouchement = 0;
	if($ac_with_acc_records)
		foreach($ac_with_acc_records as $m){
			$amount_accouchement += $m['Quantity'] * $m['Amount'];
		}
	$private_total_amount += $amount_accouchement;
	$cn_records = formatResultSet($rslt=returnResultSet($sql="SELECT cn_price.Amount, cn_records.Quantity FROM cn_price, cn_records, pa_records, in_name WHERE pa_records.PatientRecordID = cn_records.PatientRecordID && cn_records.MedecinePriceID = cn_price.MedecinePriceID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName = 'Private' && pa_records.DateIn='{$date}'",$con),true,$con);//$private_records_amount['ConsulatationPrice'];
	//var_dump($md_records);
	//loop all medicines found
	$amount_consummable = 0;
	if($cn_records)
		foreach($cn_records as $m){
			$amount_consummable += $m['Quantity'] * $m['Amount'];
		}
	$private_total_amount += $amount_consummable;
	$ho_records = formatResultSet($rslt=returnResultSet($sql="SELECT ho_price.Amount, ho_record.Days FROM ho_price, ho_record, pa_records, in_name WHERE pa_records.PatientRecordID = ho_record.RecordID && ho_record.HOPriceID = ho_price.HOPriceID && pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceName = 'Private' && pa_records.DateIn='{$date}'",$con),false,$con);//$private_records_amount['ConsulatationPrice'];
	//var_dump($ho_records);
	//loop all medicines found
	$amount_hosp = 0;
	if($ho_records)
		//foreach($ho_records as $m){
			$amount_hosp += $ho_records['Days'] * $ho_records['Amount'];
		//}
	$private_total_amount += $amount_hosp;
	//select number coarteme6x1 delivered at the selected date
	$coarteme6x1 = returnSingleField($sql="SELECT COUNT(`md_records`.`Quantity`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('coartem 6x1%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	if($malariaid = returnSingleField("SELECT MalariaID FROM la_malaria WHERE Date='{$date}'","MalariaID",true,$con)){
		saveData("UPDATE la_malaria SET `coartem 6x1`='{$coarteme6x1}' WHERE MalariaID='{$malariaid}'",$con);
	} else{
		$malariaid = saveAndReturnID("INSERT INTO la_malaria SET `coartem 6x1`='{$coarteme6x1}', Date='{$date}'",$con);
	}
	$coarteme6x2 = returnSingleField($sql="SELECT COUNT(`md_records`.`Quantity`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('coartem 6x2%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	
	saveData("UPDATE la_malaria SET `coartem 6x2`='{$coarteme6x2}' WHERE MalariaID='{$malariaid}'",$con);
	
	$coarteme6x3 = returnSingleField($sql="SELECT COUNT(`md_records`.`Quantity`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('coartem 6x3%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	
	saveData("UPDATE la_malaria SET `coartem 6x3`='{$coarteme6x3}' WHERE MalariaID='{$malariaid}'",$con);
	
	$coarteme6x4 = returnSingleField($sql="SELECT COUNT(`md_records`.`Quantity`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('coartem 6x4%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	
	saveData("UPDATE la_malaria SET `coartem 6x4`='{$coarteme6x4}' WHERE MalariaID='{$malariaid}'",$con);
	
	$quinine_total = returnSingleField($sql="SELECT SUM(`md_records`.`Quantity`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('quinine%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	$quinine = returnSingleField($sql="SELECT COUNT(`md_records`.`MedecineRecordID`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('quinine%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	
	saveData("UPDATE la_malaria SET `quinine`='{$quinine}' WHERE MalariaID='{$malariaid}'",$con);
	
	$artesunate_total = returnSingleField($sql="SELECT SUM(`md_records`.`Quantity`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('artesunate%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	$artesunate = returnSingleField($sql="SELECT COUNT(`md_records`.`MedecineRecordID`) AS QTY FROM md_price, md_records, md_name, co_records, pa_records WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = md_records.ConsultationRecordID && md_records.MedecinePriceID = md_price.MedecinePriceID && md_price.MedecineNameID = md_name.MedecineNameID && md_records.Date='{$date}' && md_name.MedecineName LIKE('artesunate%')","QTY",$con);//$private_records_amount['ConsulatationPrice'];
	
	saveData("UPDATE la_malaria SET `artesunate`='{$artesunate}' WHERE MalariaID='{$malariaid}'",$con);
	
	
	//var_dump($coarteme6x1);
	?>
	<script>
		$("#filter").click(function(e){
			$(".patient_found").load("mat_data.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			$(".patient_found").load("mat_data.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
			return e.preventDefault();
		});
		//$("#filter").focus();
		function deleteProfileNow(record_id){
			$.ajax({
				type: "POST",
				url: "../rcp/delete_patient_file.php",
				data: "record_id=" + record_id,
				cache: false,
				success: function(result){
					$(".doc_selected").html(result);
					$(".patient_found").load("mat_data.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
				}
			});
		}
	</script>
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
	<table border=0 class=no-out-border style='margin:2px;'>
		<tr>
			<td colspan=9 class='tb_title tb_separator' style="border:0px solid #000;">&nbsp;</td>
			<td colspan=7 style='text-align:center;'>Empty Documents</td>
		</tr>
		<tr>
			<td rowspan=2 colspan=2 class=tb_title style='border-left:1px solid #000;'><label class=rpt_title>RECEPTION</label></td>
			<td colspan=2 class=tb_title>CARE</td>
			<td colspan=2 class=tb_title>QUITTANCIER</td>
			<td class=tb_title>FICHES</td>
			<td rowspan=21 class='tb_title tb_separator' style="border:0px solid #000;"></td>
			<td class=tb_title>&nbsp;</td>
			<td class=tb_title>CPC</td>
			<td class=tb_title>CPN</td>
			<td class=tb_title>PST</td>
			<td class=tb_title>MAT</td>
			<td class=tb_title>NCDs</td>
			<td class=tb_title>SM</td>
			<td class=tb_title>TOT</td>
		</tr>
		<tr>
			<td class=tb_title style='width:40px'>Nb</td><td class=tb_title>Montant</td><td class=tb_title style='width:40px'>Nb</td><td class=tb_title>Montant</td><td class=tb_title>Nb</td>
			<td>CBHI</td>
			<td class="emptyCBHICPC" id=number>&nbsp;</td>
			<td class="emptyCBHICPN" id=number>&nbsp;</td>
			<td class="emptyCBHIPST" id=number>&nbsp;</td>
			<td class="emptyCBHIMAT" id=number>&nbsp;</td>
			<td class="emptyCBHINCDs" id=number>&nbsp;</td>
			<td class="emptyCBHISM" id=number>&nbsp;</td>
			<td class="emptyCBHI" id=number>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan=6 class=tb_title>CBHI</td>
			<td>TM Paid</td>
			<td id=number><a href='../rcp/document_cbhi_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=OK' target='_blank'><?= $cbhi_patient_ph = $patient_cbhi_paid + $patient_cbhi_compassion + $patient_cbhi_not_paid_dettes ?></a></td>
			<td id=number><?= $patient_cbhi_paid_amount + $patient_cbhi_amount_compassion + $patient_cbhi_not_paid_dettes_amount ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			
			<td>RAMA</td>
			<td class="emptyRSSBRAMACPC" id=number>&nbsp;</td>
			<td class="emptyRSSBRAMACPN" id=number>&nbsp;</td>
			<td class="emptyRSSBRAMAPST" id=number>&nbsp;</td>
			<td class="emptyRSSBRAMAMAT" id=number>&nbsp;</td>
			<td class="emptyRSSBRAMANCDs" id=number>&nbsp;</td>
			<td class="emptyRSSBRAMASM" id=number>&nbsp;</td>
			<td class="emptyRSSBRAMA" id=number>&nbsp;</td>
		</tr>
		<tr>
			<td>TM not paid Indigents</td>
			<td id=number><a href='../rcp/document_cbhi_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=INDIGENT' target='_blank'><?= $patient_cbhi_not_paid_indigent ?></a></td>
			<td id=number><?= $patient_cbhi_not_paid_indigent_amount ?></td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>MMI</td>
			<td class="emptyMMICPC" id=number>&nbsp;</td>
			<td class="emptyMMICPN" id=number>&nbsp;</td>
			<td class="emptyMMIPST" id=number>&nbsp;</td>
			<td class="emptyMMIMAT" id=number>&nbsp;</td>
			<td class="emptyMMINCDs" id=number>&nbsp;</td>
			<td class="emptyMMISM" id=number>&nbsp;</td>
			<td class="emptyMMI" id=number>&nbsp;</td>
		</tr>
		<tr>
			<td>TM not paid(CAT 2 malaria)</td>
			<td id=number><?= $patient_cbhi_not_paid_cat2_malaria ?></td>
			<td id=number><?= $patient_cbhi_not_paid_cat2_malaria_amount ?></td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>MEDIPLAN</td>
			<td class="emptyMEDIPLANCPC" id=number>&nbsp;</td>
			<td class="emptyMEDIPLANCPN" id=number>&nbsp;</td>
			<td class="emptyMEDIPLANPST" id=number>&nbsp;</td>
			<td class="emptyMEDIPLANMAT" id=number>&nbsp;</td>
			<td class="emptyMEDIPLANNCDs" id=number>&nbsp;</td>
			<td class="emptyMEDIPLANSM" id=number>&nbsp;</td>
			<td class="emptyMEDIPLAN" id=number>&nbsp;</td>
		</tr>
		<tr><td>TM Not Paid PST</td>
			<td id=number><a href='../rcp/document_cbhi_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=PANSEMENT' target='_blank'><?= $patient_cbhi_not_paid_pst ?></a></td>
			<td id=number><?= $patient_cbhi_not_paid_pst_amount ?></td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>CORAR</td>
			<td class="emptyCORARCPC" id=number>&nbsp;</td>
			<td class="emptyCORARCPN" id=number>&nbsp;</td>
			<td class="emptyCORARPST" id=number>&nbsp;</td>
			<td class="emptyCORARMAT" id=number>&nbsp;</td>
			<td class="emptyCORARNCDs" id=number>&nbsp;</td>
			<td class="emptyCORARSM" id=number>&nbsp;</td>
			<td class="emptyCORAR" id=number>&nbsp;</td>
		</tr>
		<tr><td>FICHES</td>
			<td id=number><?= $patient_cbhi; ?></td>
			<td id=number> <?= $cbhi_patient_ph_p  = $patient_cbhi * 20; ?></td>
			<?php
			$patient_cbhi_amount += $cbhi_patient_ph_p ;
			?>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>Sans Mutuelle</td>
			<td class="emptyPRIVATECPC" id=number>&nbsp;</td>
			<td class="emptyPRIVATECPN" id=number>&nbsp;</td>
			<td class="emptyPRIVATEPST" id=number>&nbsp;</td>
			<td class="emptyPRIVATEMAT" id=number>&nbsp;</td>
			<td class="emptyPRIVATENCDs" id=number>&nbsp;</td>
			<td class="emptyPRIVATESM" id=number>&nbsp;</td>
			<td class="emptyPRIVATE" id=number>&nbsp;</td>
		</tr>
		<tr><td>Sous-total CBHI</td>
			<td id=number><a href='../rcp/document_cbhi_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=all' target='_blank'><?= $patient_cbhi ?></a></td>
			<td id=number><?= $patient_cbhi_amount ?></td>
			<?php
				$all_amount += $patient_cbhi_amount;
			?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>Total</td>
			<td class="emptyCPC" id=number>&nbsp;</td>
			<td class="emptyCPN" id=number>&nbsp;</td>
			<td class="emptyPST" id=number>&nbsp;</td>
			<td class="emptyMAT" id=number>&nbsp;</td>
			<td class="emptyNCDs" id=number>&nbsp;</td>
			<td class="emptySM" id=number>&nbsp;</td>
			<td class="emptyServiceTotal" id=number>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan=4 class=tb_title>Autres</td>
			<td>RAMA</td>
			<td id=number><a href='../rcp/document_rama_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=all' target='_blank'><?= $patient_rama ?></a></td>
			<td id=number><?= round($patient_rama_amount,0) ?></td>
			<?php
				$all_amount += $patient_rama_amount;
			?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<!-- <td>Total</td>
			<td class="emptyCPC" id=number>&nbsp;</td>
			<td class="emptyCPN" id=number>&nbsp;</td>
			<td class="emptyPST" id=number>&nbsp;</td>
			<td class="emptyMAT" id=number>&nbsp;</td>
			<td class="emptyServiceTotal" id=number>&nbsp;</td> -->
			
		</tr>
		<tr><td>MMI</td>
			<td id=number><a href='../rcp/document_mmi_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=all' target='_blank'><?= $patient_mmi ?></a></td>
			<td id=number><?= round($patient_mmi_amount,0) ?></td>
			<?php
				$all_amount += $patient_mmi_amount;
			?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan=6 rowspan=3 style='border:0px solid #000;'>&nbsp;</td>
		</tr>
		<tr><td>MEDIPLAN</td>
			<td id=number><a href='../rcp/document_mediplan_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=all' target='_blank'><?= $patient_mediplan ?></a></td>
			<td id=number><?= round($patient_mediplan_amount,0) ?></td>
			<?php
				$all_amount += $patient_mediplan_amount;
			?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		$r = round($patient_mediplan_amount,0);
		$cbhi_ph_facture = 0;
		$cbhi_ph_facture_amount = 0;
		$data_to_print_part1 = <<<ENDDATA
		<tr>
			<td rowspan=2 colspan=2 class=tb_title style='border-left:1px solid #000;'><label class=rpt_title>RECEPTION</label></td>
			<td colspan=2 class=tb_title>CARE</td>
			<td colspan=2 class=tb_title>QUITTANCIER</td>
			<td class=tb_title>FICHES</td>
		</tr>
		<tr>
			<td class=tb_title style='width:40px'>Nb</td><td class=tb_title>Montant</td><td class=tb_title style='width:40px'>Nb</td><td class=tb_title>Montant</td><td class=tb_title>Nb</td>
		</tr>
		<tr>
			<td rowspan=5 class=tb_title>CBHI</td>
			<td>TM Paid</td>
			<td id=number>{$patient_cbhi_paid}</td>
			<td id=number>{$patient_cbhi_paid_amount}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr>
			<td>TM not paid Indigents</td>
			<td id=number>{$patient_cbhi_not_paid_indigent}</td>
			<td id=number>{$patient_cbhi_not_paid_indigent_amount}</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>TM Not Paid PST</td>
			<td id=number>{$patient_cbhi_not_paid_pst}</td>
			<td id=number>{$patient_cbhi_not_paid_pst_amount}</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>PH + Facture</td>
			<td id=number>{$cbhi_patient_ph_p}</a></td>
			<td id=number>{$cbhi_patient_ph}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td>Sous-total CBHI</td>
			<td id=number>{$patient_cbhi}</td>
			<td id=number>{$patient_cbhi_amount}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td rowspan=4 class=tb_title>Autres</td>
			<td>RAMA</td>
			<td id=number>{$patient_rama}</td>
			<td id=number>{$patient_rama_amount}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td>MMI</td>
			<td id=number>{$patient_mmi}</a></td>
			<td id=number>{$patient_mmi_amount}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr><td>MEDIPLAN</td>
			<td id=number>{$patient_mediplan}</td>
			<td id=number>{$r}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
ENDDATA;
		
		$r = round($patient_corar_amount,0);
		$all_amount += $patient_corar_amount;
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>CORAR</td>
			<td id=number><a href='../rcp/document_corar_sp.php?day={$_GET['day']}&month={$_GET['month']}&year={$_GET['year']}&sp=all' target='_blank'>{$patient_corar}</a></td>
			<td id=number>{$r}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>CORAR</td>
			<td id=number><a href='../rcp/document_corar_sp.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=all' target='_blank'><?= $patient_corar ?></a></td>
			<td id=number><?= round($patient_corar_amount,0) ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		$r = round($amount_consultation,0);
		$data_to_print_part1 .= <<<ENDDATA
		<tr>
			<td rowspan=10 class=tb_title>Sans Mutuelle</td>
			<td>Consultation</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr>
			<td rowspan=10 class=tb_title>Sans Mutuelle</td>
			<td>Consultation</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= round($amount_consultation,0) ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td rowspan=2 style='border:0px solid #000;'>&nbsp;</td>
			<td colspan=7 style='text-align:center;'>Patients accueillis</td>
			
		</tr>
		<?php
		$r = round($amount_labo,0);
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>Laboratoire</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>Laboratoire</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= round($amount_labo,0) ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>CPC</td>
			<td>CPN</td>
			<td>PST</td>
			<td>MAT</td>
			<td>NCDs</td>
			<td>SM</td>
			<td>TOT</td>
		</tr>
		<?php
		$r = round($amount_md,0);
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>Medicaments</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>Medicaments</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= round($amount_md,0) ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>CBHI</td>
			<td class="totalsCBHICPC" id=number>&nbsp;</td>
			<td class="totalsCBHICPN" id=number>&nbsp;</td>
			<td class="totalsCBHIPST" id=number>&nbsp;</td>
			<td class="totalsCBHIMAT" id=number>&nbsp;</td>
			<td class="totalsCBHINCDS" id=number>&nbsp;</td>
			<td class="totalsCBHISM" id=number>&nbsp;</td>
			<td class="totalsCBHI" id=number>&nbsp;</td>
		</tr>
		<?php
		$r = round($amount_act_sans_accouchement,0);
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>Actes hors accouchement</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>Actes hors accouchement</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= round($amount_act_sans_accouchement,0) ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>RAMA</td>
			<td class="totalsRSSBRAMACPC" id=number>&nbsp;</td>
			<td class="totalsRSSBRAMACPN" id=number>&nbsp;</td>
			<td class="totalsRSSBRAMAPST" id=number>&nbsp;</td>
			<td class="totalsRSSBRAMAMAT" id=number>&nbsp;</td>
			<td class="totalsRSSBRAMANCDs" id=number>&nbsp;</td>
			<td class="totalsRSSBRAMASM" id=number>&nbsp;</td>
			<td class="totalsRSSBRAMA" id=number>&nbsp;</td>
		</tr>
		<?php
		$r = round($amount_accouchement,0);
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>Accouchement</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>Accouchement</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= round($amount_accouchement,0) ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>MMI</td>
			<td class="totalsMMICPC" id=number>&nbsp;</td>
			<td class="totalsMMICPN" id=number>&nbsp;</td>
			<td class="totalsMMIPST" id=number>&nbsp;</td>
			<td class="totalsMMIMAT" id=number>&nbsp;</td>
			<td class="totalsMMINCDs" id=number>&nbsp;</td>
			<td class="totalsMMISM" id=number>&nbsp;</td>
			<td class="totalsMMI" id=number>&nbsp;</td>
		</tr>
		<?php
		$r = round($amount_consummable,0) ;
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>Consommables</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>Consommables</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= $r ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>MEDIPLAN</td>
			<td class="totalsMEDIPLANCPC" id=number>&nbsp;</td>
			<td class="totalsMEDIPLANCPN" id=number>&nbsp;</td>
			<td class="totalsMEDIPLANPST" id=number>&nbsp;</td>
			<td class="totalsMEDIPLANMAT" id=number>&nbsp;</td>
			<td class="totalsMEDIPLANNCDs" id=number>&nbsp;</td>
			<td class="totalsMEDIPLANSM" id=number>&nbsp;</td>
			<td class="totalsMEDIPLAN" id=number>&nbsp;</td>
		</tr>
		<?php
		$r = round($amount_hosp,0);
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>Hospitalisation</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>Hospitalisation</td>
			<td class=empty_field>&nbsp;</td>
			<td id=number><?= round($amount_hosp,0) ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>CORAR</td>
			<td class="totalsCORARCPC" id=number>&nbsp;</td>
			<td class="totalsCORARCPN" id=number>&nbsp;</td>
			<td class="totalsCORARPST" id=number>&nbsp;</td>
			<td class="totalsCORARMAT" id=number>&nbsp;</td>
			<td class="totalsCORARNCDS" id=number>&nbsp;</td>
			<td class="totalsCORARSM" id=number>&nbsp;</td>
			<td class="totalsCORAR" id=number>&nbsp;</td>
		</tr>
		<?php
		$fiche_amount = $private_total * 100;
		$ph_facture_private_amount = 0;// $private_total * 40;
		$r = RoundUp($private_total_amount,5);
		//$private_total_amount += $ph_facture_private_amount + $fiche_amount;
		$data_to_print_part1 .= <<<ENDDATA
		<tr><td>&nbsp;</td>
			<!--<td id=number>{$private_total}</td>-->
			<td id=number>&nbsp;</td>
			<!--<td id=number>{$ph_facture_private_amount}</td>-->
			<td id=number>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>Fiche</td>
			<td id=number>{$private_total}</td>
			<td id=number>{$fiche_amount}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>Sous-total Sans mutuelle</td>
			<td id=number>{$private_total}</td>
			<td id=number>{$r}</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr><td>&nbsp;</td>
			<td id=number>&nbsp;</td>
			<!-- <td id=number>&nbsp;<?= $private_total ?></td> -->
			<td id=number>&nbsp;</td>
			<!-- <td id=number>&nbsp;<?= $ph_facture_private_amount ?></td> -->
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>Sans Mutuelle</td>
			<td class="totalsPRIVATECPC" id=number>&nbsp;</td>
			<td class="totalsPRIVATECPN" id=number>&nbsp;</td>
			<td class="totalsPRIVATEPST" id=number>&nbsp;</td>
			<td class="totalsPRIVATEMAT" id=number>&nbsp;</td>
			<td class="totalsPRIVATENCDS" id=number>&nbsp;</td>
			<td class="totalsPRIVATESM" id=number>&nbsp;</td>
			<td class="totalsPRIVATE" id=number>&nbsp;</td>
		</tr>
		<tr><td>Fiche</td>
			<td id=number><?= $private_total ?></td>
			<td id=number><?= $fiche_amount ?></td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>Total</td>
			<td class="totalsCPC" id=number>&nbsp;</td>
			<td class="totalsCPN" id=number>&nbsp;</td>
			<td class="totalsPST" id=number>&nbsp;</td>
			<td class="totalsMAT" id=number>&nbsp;</td>
			<td class="totalsNCDS" id=number>&nbsp;</td>
			<td class="totalsSM" id=number>&nbsp;</td>
			<td class="totalspatientsServices" id=number>&nbsp;</td>
		</tr>
		<tr><td>Sous-total Sans mutuelle</td>
			<td id=number><a href='../report/empty_file_private.php?day=<?= $_GET['day'] ?>&month=<?= $_GET['month'] ?>&year=<?= $_GET['year'] ?>&sp=all' target='_blank'><?= $private_total ?></a></td>
			<!-- <td id=number><?= RoundUp($private_total_amount,5) ?></td> -->
			<td id=number><?= ($private_total_amount) ?></td>
			<?php
				$all_amount += $private_total_amount;
			?>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<?php
		$data_to_print_part1 .= <<<ENDDATA
		<tr>
			<td rowspan=17 style="border:0px solid #000;">&nbsp;</td>
			<td>SOUS TOTAL PATIENTS</td>
			<td id=number>{$all_patient_number}</td>
			<td id=number>{$all_patient_amount}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
ENDDATA;
		?>
		<tr>
			<td rowspan=17 style="border:0px solid #000;">&nbsp;</td>
			<td>SOUS TOTAL PATIENTS</td>
			<td id=number><?= $all_patient_number ?></td>
			<td id=number><?= $all_amount ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td >&nbsp;</td>
		</tr>
		<?php
			$all_amount += ($facture*20);
			$transfer_amount = $transfer * 100;
		$data_to_print = <<<ENDDATA
		
		<tr><td>Dette du jour</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>Remboursements</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td colspan=6 class='tb_separator' style='border-left:1px solid #fff; border-right:1px solid #fff;'>&nbsp;</td></tr>
		<tr><td>Transfert</td>
			<td id=number>{$transfer}</td>
			<td id=number>{$transfer_amount}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>Attestation</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>Amende</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
		<tr><td>Grand Total</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
			<td>&nbsp;</td>
			<td class=empty_field>&nbsp;</td>
		</tr>
ENDDATA;
	echo $data_to_print;
		$recept = "<span class=success style='font-weight:bold; font-size:20px;'>
	<br />
	{$post} Daily Records<br />
	Date: {$_GET['day']}/{$_GET['month']}/{$_GET['year']}<br />
	</span><table border=1 id=table>".$data_to_print_part1.$data_to_print."<tr>
			<td colspan=6 class=row_separator style='border:0px solid #000; height:50px; padding-top:10px;'>
				Prepared By:<b style='font-weight:bold;'>{$_SESSION['user']['Name']}</b>
				
				<div style='float:right; padding-right:40px;'>Signature:</div>
			</td></tr></table>";
		require_once "../lib/mpdf57/mpdf.php";

		$pdf = new MPDF();

		$pdf->Open();

		$pdf->AddPage("P");

		$pdf->SetFont("Arial","N",10);
		$css = file_get_contents("./pdf_css.css");
		
		$pdf->WriteHTML($css,1);
		
		$pdf->WriteHTML($recept);
		$filename = "./dailyreceptiondata.pdf";
		//echo $filename;
		$pdf->Output($filename); 
	
	?>
		<tr>
			<td colspan=6 class=row_separator style='border:0px solid #000; height:50px; padding-top:10px;'>
				Prepared By:<b style='font-weight:bold;'><?= $_SESSION['user']['Name'] ?></b>
				
				<div style='float:right; padding-right:40px;'>Signature:</div>
				<a target='_blank' href='<?= $filename ?>'>Print</a>
			</td>
			<td colspan=3 class=row_separator style='border:0px solid #000'>&nbsp;</td>
		</tr>
		
		<tr>
			<td class=tb_title><label class=rpt_title>MALARIA</label></td>
			<td class=tb_title>CARE</td>
			<td class=tb_title>PHARM</td>
			<td rowspan=8 style='border:0px solid #000'></td>
			<td rowspan=5 style='border:0px solid #000'></td>
		</tr>
		<?php
			$all_malaria_cases = 0;
		?>
		<tr>
			<td>Coartem 6x1</td>
			<td id=number><?= $coarteme6x1; ?></td>
			<?php
			$all_malaria_cases += $coarteme6x1;
			?>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Coartem 6x2</td>
			<td id=number><?= $coarteme6x2; ?></td>
			<?php
			$all_malaria_cases += $coarteme6x2;
			?>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Coartem 6x3</td>
			<td id=number><?= $coarteme6x3; ?></td>
			<?php
			$all_malaria_cases += $coarteme6x3;
			?>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Coartem 6x4</td>
			<td id=number><?= $coarteme6x4; ?></td>
			<?php
			$all_malaria_cases += $coarteme6x4;
			?>
			<td>&nbsp;</td>
			<td>CARE</td>
			<td>LABO</td>
		</tr>
		<tr>
			<td>Quinine</td>
			<td id=number title='Total Items: <?= $quinine_total ?>'><?= $quinine; ?></td>
			<?php
			$all_malaria_cases += $quinine;
			?>
			<td>&nbsp;</td>
			<td>GE pos</td>
			<td id=number class='ge_summary'><?= @$ge_positive ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Artesunate</td>
			<td id=number title="Total Items: <?= $artesunate_total ?>"><?= $artesunate; ?></td>
			<?php
			$all_malaria_cases += $artesunate;
			?>
			<td>&nbsp;</td>
			<td>GE neg & TDR pos</td>
			<td id=number class='tdr_summary'><?= @$ge_positive ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>TOTAL prescriptions</td>
			<td id=number><?= $all_malaria_cases; ?></td>
			<td>&nbsp;</td>
			<td>Total</td>
			<td id=number class='care_labo_summary'><?= @$ge_positive ?></td>
			<td>&nbsp;</td>
		</tr>

	</table>
	<?php
		//check if the day had ever been saved
		if($malariaid = returnSingleField("SELECT MalariaID FROM la_malaria WHERE Date='{$date}'","MalariaID",true,$con)){
			saveData("UPDATE la_malaria SET PharmacyData='{$all_malaria_cases}' WHERE MalariaID='{$malariaid}'",$con);
		} else{
			saveData("INSERT INTO la_malaria SET PharmacyData='{$all_malaria_cases}', Date='{$date}'",$con);
		}
		$exam_id = returnSingleField("SELECT ExamID FROM la_exam WHERE ExamName='GE'","ExamID",true,$con);
		$tdr_id = returnSingleField("SELECT ExamID FROM la_exam WHERE ExamName='TDR'","ExamID",true,$con);
		//echo $exam_id;
		//echo $date;
	?>
	<div class=test_data style=''>
		
	</div>
	</div>
	<style>
		.status_bar td{
			min-width:100px;
			border-radius:10px;
			background-color:orange;
			text-align:center;
			font-size:16px;
			color:#fff;
			font-weight"bold;
		}
		.status_bar td.money{
			background-color:green;
		}
		.status_bar td.print_all{
			background-color:orange;
			cursor:not-allowed;
		}
	</style>
	<input type=hidden id=process value="1" />
	<table class=status_bar>
		<tr><td class=money>Money</td><td class=malaria>Malaria</td><td class=patientss>Patient</td><td class=print_all title="Print The Full Report">Please Wait</td></tr>
	</table>
	</b>
	<?php
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['day']}/{$_GET['month']}/{$_GET['year']} at selected station {$post}</span>";
}
}
?>
<div class="outputdata">

</div>
<script>
	$(document).ready(function() { 
	
		$(".print_all").click(function(){
			//check if all data are available
			if($(".print_all").html() != "Please Wait"){
				var cnt = $(".printarea").html();
				$.ajax({
				type: "POST",
				url: "./print_rcp_report.php",
				data: "url=ajax",
				cache: false,
				success: function(result){
					//alert(result);
					$(".outputdata").html(result);
					/* setTimeout(function(){
						$("#print_link").click();
					},100); */
				},
				error:function(er){
					console.log(er.responseText);
				}
			});
			}
		});
		$('#excel_file').live('change', function(){ 
			
			$("#upload_out").html('');
			$("#upload_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#upload_patient").ajaxForm({ 
				target: '#upload_out'
			}).submit(); 
		});
		
		//delay 2000 milliseconds and send anther request to the number of positive GE
		setTimeout(function(){
			$(".malaria").css("background-color","yellow");
			$.ajax({
				type: "POST",
				url: "./ge-counter.php",
				data: "ExamID=<?= $exam_id ?>&TDRID=<?= $tdr_id ?>&ResultDate=<?= $date ?>&url=ajax",
				cache: false,
				success: function(result){
					$(".test_data").html(result);
					$(".malaria").css("background-color","green");
					if($("#process").val() < 2){
						$("#process").val("2");
						$(".print_all").css("background-color","yellow");
						$(".print_all").css("color","green");
					} else{
						$("#process").val("3");
						$(".print_all").css("background-color","green");
						$(".print_all").css("color","white");
						$(".print_all").css("cursor","pointer");
						$(".print_all").html("Print");
					}
				},
				error:function(er){
					console.log(er.responseText);
				}
			});
			
			//send the request to get empty files information now
		}, 4000);
		
		
		setTimeout(function(){
			$(".patientss").css("background-color","yellow");
			$.ajax({
			   type: "POST",
			   url: "./empty_files_summary.php",
			   data: {  
						'date': '<?= $date ?>',
						'post': '<?= $_GET['post'] ?>',
						},
				/* dataType: "json", */
			   success: function(data){
				   //console.log(data);
				   
				   $(".test_data").html(data);
				   $(".patientss").css("background-color","green");
				   
				   if($("#process").val() < 2){
						$("#process").val("2");
						$(".print_all").css("background-color","yellow");
						$(".print_all").css("color","green");
					} else{
						$("#process").val("3");
						$(".print_all").css("background-color","green");
						$(".print_all").css("color","white");
						$(".print_all").css("cursor","pointer");
						$(".print_all").html("Print");
					}
			   },
			   error: function(err){
				console.log(err.responseText);
			   }, 
			});
		}, 100);
	});
</script>
<div id=upload_out></div>