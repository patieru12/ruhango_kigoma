<?php
//var_dump($con);
if(!isset($con) || $con == null){
	echo "<span class=error-text>SIS Plugin can not work without valid MySQL Connection</span>";
	die;
}
//get the available centre and package them later reference
$posts = explode("_",trim($_POST['post_']));
//var_dump($posts);
$center_condition = "(";
$pcounter = 0;
if(count($posts)>1){
	foreach($posts as $p){
		if(trim($p)){
			if($pcounter++ > 0 ){
				$center_condition .= " || ";
			}
			$center_condition .= "`sy_users`.`CenterID` = '{$p}'";
		}
			
	}
} else{
	echo "<span class=error>Please the Post View Some Information</span>";
	die;
}

$center_condition .= ")";

//sent all patient with insurance counting the presence
$insured_current_patient = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && se_name.ServiceCode = 'CPC' ","Patient",true,$con);
$uninsured_current_patient = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName = 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
$indigent_uninsured_current_patient = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, mu_tm, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientRecordID = mu_tm.PatientRecordID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName = 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && mu_tm.Type='INDIGENT' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Patient With Insurance".$insured_current_patient);
//select all male less than 5 years old with consultation
$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";

$maleLessThan5YearWithConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth > '{$fiveYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Male < 5 = ".$maleLessThan5YearWithConsultation);
$femaleLessThan5YearWithConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth > '{$fiveYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Female < 5 = ".$femaleLessThan5YearWithConsultation);
$malebetween5and19WithConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Male < 5 = ".$malebetween5and19WithConsultation);
$femalebetween5and19WithConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Female < 5 = ".$femalebetween5and19WithConsultation);

$maleGreateThan20WithConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Male < 5 = ".$maleGreateThan20WithConsultation);
$femaleGreateThan20WithConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName != 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Female < 5 = ".$femaleGreateThan20WithConsultation);

$maleLessThan5YearWithoutConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName = 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth > '{$fiveYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Male < 5 = ".$maleLessThan5YearWithConsultation);
$femaleLessThan5YearWithoutConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName = 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth > '{$fiveYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Female < 5 = ".$femaleLessThan5YearWithConsultation);
$malebetween5and19WithoutConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName = 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Male < 5 = ".$malebetween5and19WithConsultation);
$femalebetween5and19WithoutConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName = 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Female < 5 = ".$femalebetween5and19WithConsultation);

$maleGreateThan20WithoutConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName = 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Male < 5 = ".$maleGreateThan20WithConsultation);
$femaleGreateThan20WithoutConsultation = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, pa_info, co_records, co_price, co_category, in_name, sy_users WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.InsuranceNameID = in_name.InsuranceNameID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && in_name.InsuranceName != 'Private' && co_category.ConsultationCategoryName = 'invisible' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Female < 5 = ".$femaleGreateThan20WithConsultation);

$transferts = returnSingleField("SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, sy_users WHERE pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && pa_records.Status >= 2 && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Transfert:".$transferts);

$patient_zone = returnSingleField($sql = "SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, sy_users, ad_village, ad_cell, ad_sector, ad_district WHERE pa_records.VillageID = ad_village.ViillageID && ad_village.CellID  = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && (ad_sector.SectorName = '{$_SECTOR}' && ad_district.DistrictName='{$_DISTRICT}') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Transfert:".$transferts);
//echo $sql;
$patient_hors_zone = returnSingleField($sql = "SELECT COUNT(`pa_records`.`PatientRecordID`) as Patient FROM pa_records, se_records, se_name, sy_users, ad_village, ad_cell, ad_sector, ad_district WHERE pa_records.VillageID = ad_village.ViillageID && ad_village.CellID = ad_cell.CellID && ad_cell.SectorID = ad_sector.SectorID && ad_sector.DistrictID = ad_district.DistrictID && pa_records.ReceptionistID = sy_users.UserID && {$center_condition} && (ad_sector.SectorName != '{$_SECTOR}' || ad_district.DistrictName != '{$_DISTRICT}') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_records.PatientRecordID = se_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'CPC'","Patient",true,$con);
//var_dump("Transfert:".$transferts);

//tous les accouchement
$accouchement_total = returnSingleField($sql = "SELECT COUNT(`ac_records`.`Quantity`) as Patient FROM pa_records, ac_records, ac_price, ac_name WHERE ac_records.PatientRecordID = pa_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_name.Name LIKE('Acc.%') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%')","Patient", true,$con);
$accouchement_eutocique = returnSingleField($sql = "SELECT COUNT(`ac_records`.`Quantity`) as Patient FROM pa_records, ac_records, ac_price, ac_name WHERE ac_records.PatientRecordID = pa_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_name.Name LIKE('Acc.eutocique%') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%')","Patient", true,$con);
$accouchement_dystocique = returnSingleField($sql = "SELECT COUNT(`ac_records`.`Quantity`) as Patient FROM pa_records, ac_records, ac_price, ac_name WHERE ac_records.PatientRecordID = pa_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_name.Name LIKE('Acc. Dystocique%') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%')","Patient", true,$con);

$fifteenYearPastFromNow = ($_POST['year'] - 15)."-".$_POST['month']."-01";
$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
$thityfiveYearPastFromNow = ($_POST['year'] - 35)."-".$_POST['month']."-01";

$accouchement_0_15 = returnSingleField($sql = "SELECT COUNT(`ac_records`.`Quantity`) as Patient FROM pa_records, pa_info, ac_records, ac_price, ac_name WHERE pa_records.PatientID = pa_info.PatientID && ac_records.PatientRecordID = pa_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_name.Name LIKE('Acc.%') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateOfBirth >= '{$fifteenYearPastFromNow}'","Patient", true,$con);
$accouchement_16_19 = returnSingleField($sql = "SELECT COUNT(`ac_records`.`Quantity`) as Patient FROM pa_records, pa_info, ac_records, ac_price, ac_name WHERE pa_records.PatientID = pa_info.PatientID && ac_records.PatientRecordID = pa_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_name.Name LIKE('Acc.%') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateOfBirth < '{$fifteenYearPastFromNow}' && pa_info.DateOfBirth >= '{$nineteenYearPastFromNow}'","Patient", true,$con);
$accouchement_35 = returnSingleField($sql = "SELECT COUNT(`ac_records`.`Quantity`) as Patient FROM pa_records, pa_info, ac_records, ac_price, ac_name WHERE pa_records.PatientID = pa_info.PatientID && ac_records.PatientRecordID = pa_records.PatientRecordID && ac_records.ActPriceID = ac_price.ActPriceID && ac_price.ActNameID = ac_name.ActNameID && ac_name.Name LIKE('Acc.%') && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateOfBirth <= '{$thityfiveYearPastFromNow}'","Patient", true,$con);

$accouchement_apres_transfert = returnSingleField($sql = "SELECT COUNT(`se_records`.`ServiceRecordID`) as Patient FROM pa_records, se_records, se_name WHERE se_records.PatientRecordID = pa_records.PatientRecordID && se_records.ServiceNameID = se_name.ServiceNameID && se_name.ServiceCode = 'MAT' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_records.Status > 1","Patient", true,$con);

/****************************** LABORATORY IN HMIS REPORT *********/
$ge_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='GE'","Patient", true,$con);
$ge_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='GE'","Patient", true,$con);
$ge_plasmodium = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && (la_result.ResultName LIKE('%tropho%') || la_result.ResultName LIKE('%Gametocytes%') || la_result.ResultName LIKE('%schizontes%')) && la_exam.ExamName='GE'","Patient", true,$con);
$ge_microfilaire = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Microfilaire%') && la_exam.ExamName='GE'","Patient", true,$con);
$ge_borellia = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Borellia%') && la_exam.ExamName='GE'","Patient", true,$con);
$ge_trypanosome = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Trypanosome%') && la_exam.ExamName='GE'","Patient", true,$con);

########################## TDR
$tdr_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%positi%') && la_exam.ExamName='TDR'","Patient", true,$con);
$tdr_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='TDR'","Patient", true,$con);
$tdr_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='TDR'","Patient", true,$con);

########################## TDR
########################## SELLES
$selles_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_entamoeba_hist = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%hist%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_giardia = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Giardia%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_ascaris = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Ascaris%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_ankylostome = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Ankylostome%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_schistosome = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Schist%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_trichuris = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%Trichuris%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_tenia = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%enia%') && la_exam.ExamName='Selles'","Patient", true,$con);
$selles_others = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName NOT LIKE('%egat%') && la_result.ResultName NOT LIKE('%hist%') && la_result.ResultName NOT LIKE('%Giardia%') && la_result.ResultName NOT LIKE('%Ascaris%') && la_result.ResultName NOT LIKE('%Ankylostome%') && la_result.ResultName NOT LIKE('%Schist%') && la_result.ResultName NOT LIKE('%Trichuris%') && la_result.ResultName NOT LIKE('%enia%') && la_exam.ExamName='Selles'","Patient", true,$con);
########################## SELLES
########################## Glycosurie
$glycosurie_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%positi%') && la_exam.ExamName='Glycosurie'","Patient", true,$con);
$glycosurie_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='Glycosurie'","Patient", true,$con);
$glycosurie_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Glycosurie'","Patient", true,$con);

########################## Glycosurie
########################## Albumine
$albumine_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && (la_result.ResultName LIKE('%positi%') || la_result.ResultName LIKE('%trace%')) && la_exam.ExamName='Albuminurie'","Patient", true,$con);
$albumine_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='Albuminurie'","Patient", true,$con);
$albumine_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Albuminurie'","Patient", true,$con);

########################## Albumine
########################## Test de Grossesse
$grossesse_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && (la_result.ResultName LIKE('%positi%')) && la_exam.ExamName='Test de Grossesse'","Patient", true,$con);
$grossesse_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='Test de Grossesse'","Patient", true,$con);
$grossesse_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Test de Grossesse'","Patient", true,$con);

########################## Test de Grossesse
########################## Crachat
$crachat_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && (la_result.ResultName LIKE('%+%')) && la_exam.ExamName='Crachat BK'","Patient", true,$con);
$crachat_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='Crachat BK'","Patient", true,$con);
$crachat_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Crachat BK'","Patient", true,$con);

########################## Crachat
########################## RPR
$rpr_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && (la_result.ResultName LIKE('%ositi%')) && la_exam.ExamName='RPR'","Patient", true,$con);
$rpr_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='RPR'","Patient", true,$con);
$rpr_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='RPR'","Patient", true,$con);

########################## RPR
########################## HIV
$hiv_positive = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && (la_result.ResultName LIKE('%ositi%')) && la_exam.ExamName='HIV'","Patient", true,$con);
$hiv_negative = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_result.ResultName LIKE('%egat%') && la_exam.ExamName='HIV'","Patient", true,$con);
$hiv_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='HIV'","Patient", true,$con);

########################## HIV
##########################HB
$hb_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Hb'","Patient", true,$con);

########################## HB
########################## VS
$vs_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='V.S.'","Patient", true,$con);

########################## VS
########################## NFS Hemogramme Complet
$nfs_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Hemogramme Complet'","Patient", true,$con);

########################## NFS Hemogramme Complet
########################## ALAT(GPT)
$gpt_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='SGPT'","Patient", true,$con);

########################## ALAT(GPT)
########################## Glycémie
$glycemie_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Glycémie'","Patient", true,$con);

########################## Glycémie
########################## Amylase
$amylase_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='Amylase'","Patient", true,$con);

########################## Amylase
########################## CD4
$cd4_total = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ResultRecordID`) as Patient FROM pa_records, co_records, la_records, la_result_record, la_result, la_exam WHERE pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamRecordID = la_result_record.ExamRecordID && la_result_record.ResultID = la_result.ResultID && la_result.ExamID = la_exam.ExamID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && la_exam.ExamName='CD4'","Patient", true,$con);

########################## CD4
/************************ END LABORATORY IN HMIS REPORT *********/
//echo "SELLES: Positive = ".$tdr_positive;
//echo "<br />Glycémie: Total = ".$glycemie_total;

//die;
$enfant0_7jour = "";
$enfant8_2Mois = "";
$enfant2_59Mois = "";
//echo $sql;
function NCMNoPECIME(&$new_case,&$con){
	$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";

	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$m5_19 = "";
		$f5_19 = "";
		$m20 = "";
		$f20 = "";
		//var_dump($new_case[$i]);echo "<hr />";
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] == "la"){
			//select the data related to this Diagnostic
			$m5_19 = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ExamRecordID`) as Patient FROM la_result_record, la_result, la_records, co_records, pa_records, pa_info WHERE la_result_record.ResultID = la_result.ResultID && la_result_record.ExamRecordID = la_records.ExamRecordID && la_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && la_result.ResultName LIKE('%{$n['DiagnosticName']}%')","Patient", true,$con);
			//echo $sql."<br />";// die;
			$f5_19 = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ExamRecordID`) as Patient FROM la_result_record, la_result, la_records, co_records, pa_records, pa_info WHERE la_result_record.ResultID = la_result.ResultID && la_result_record.ExamRecordID = la_records.ExamRecordID && la_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && la_result.ResultName LIKE('%{$n['DiagnosticName']}%')","Patient", true,$con);
			//echo $sql."<br />";
			$m20 = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ExamRecordID`) as Patient FROM la_result_record, la_result, la_records, co_records, pa_records, pa_info WHERE la_result_record.ResultID = la_result.ResultID && la_result_record.ExamRecordID = la_records.ExamRecordID && la_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && la_result.ResultName LIKE('%{$n['DiagnosticName']}%')","Patient", true,$con);
			//echo $sql."<br />";
			$f20 = returnSingleField($sql = "SELECT COUNT(`la_result_record`.`ExamRecordID`) as Patient FROM la_result_record, la_result, la_records, co_records, pa_records, pa_info WHERE la_result_record.ResultID = la_result.ResultID && la_result_record.ExamRecordID = la_records.ExamRecordID && la_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && la_result.ResultName LIKE('%{$n['DiagnosticName']}%')","Patient", true,$con);
			//echo $sql."<br />";
		} else if($n["SISCode"] == "fv"){
			$m5_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && pa_records.Temperature >= 37.5","Patient", true,$con);
			//echo $sql; die;
			$f5_19 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && pa_records.Temperature >= 37.5","Patient", true,$con);
			$m20 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_records.Temperature >= 37.5","Patient", true,$con);
			$f20 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_records.Temperature >= 37.5","Patient", true,$con);
			
		} else if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$m5_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			//echo $sql; echo "<hr /><hr />";// die;
			$f5_19 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fiveYearPastFromNow}' && pa_info.DateofBirth > '{$nineteenYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			$m20 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			$f20 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && {$add_condition}","Patient", true,$con);
		}
		$new_case[$i]['M5_19'] = $m5_19?$m5_19:"";
		$new_case[$i]['F5_19'] = $f5_19?$f5_19:"";
		$new_case[$i]['M20'] = $m20?$m20:"";
		$new_case[$i]['F20'] = $f20?$f20:"";
	}
}
function MOO(&$new_case,&$con){
	//$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";
	$thirtyNineYearPastFromNow = ($_POST['year'] - 39)."-".$_POST['month']."-01";
	$fourtyYearPastFromNow = ($_POST['year'] - 40)."-".$_POST['month']."-01";

	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$m0_19 = "";
		$f0_19 = "";
		$m20_39 = "";
		$f20_39 = "";
		$m40 = "";
		$f40 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$m0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			//echo $sql; die;
			$f0_19 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			$m20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			$f20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			$m40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition}","Patient", true,$con);
			$f40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition}","Patient", true,$con);
		}
		$new_case[$i]['M0_19'] = $m0_19?$m0_19:"";
		$new_case[$i]['F0_19'] = $f0_19?$f0_19:"";
		$new_case[$i]['M20_39'] = $m20_39?$m20_39:"";
		$new_case[$i]['F20_39'] = $f20_39?$f20_39:"";
		$new_case[$i]['M40'] = $m40?$m40:"";
		$new_case[$i]['F40'] = $f40?$f40:"";
	}
}

function MoinDe5Ans($dgCode,&$rowData){
	if(!$dgCode){
		$rowData['E0_7'] = "";
		$rowData['E8_2'] = "";
		$rowData['E2_59'] = "";
		return;
	}
	$sixYearPastFromNow = ($_POST['year'] - 6)."-".$_POST['month']."-01";
	$cats = array(1=>"E0_7","E8_2","E2_59");
	//select all patient in a given range and count how many only
	$patient = returnAllData($sql="SELECT pa_records.DateIn, pa_info.DateOfBirth FROM pa_records, pa_info, co_records, co_diagnostic_records, co_diagnostic WHERE pa_records.PatientID = pa_info.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = co_diagnostic_records.ConsulationRecordID && co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic.DiagnosticCode='{$dgCode}' && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateOfBirth >= '{$sixYearPastFromNow}'",$con);
	//var_dump($patient);
	//loop all found patient to search all their category
	if($patient){
		foreach($patient as $pt){
			//get the category of the current patient
			$category = getAge($pt['DateOfBirth'],$notation=15, $current_date=$pt['DateIn']);
			//increase the value of the returned category
			//echo $category."<br />";
			if(isset($cats[$category])){
				$rowData[$cats[$category]]++;
			}
		}
	}
}
function PECIME(&$new_case,&$con){
	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$e0_7 = "";
		$e8_2 = "";
		$e2_59 = "";
		$new_case[$i]['E0_7'] = $e0_7;
		$new_case[$i]['E8_2'] = $e8_2;
		$new_case[$i]['E2_59'] = $e2_59;
		MoinDe5Ans($n['SISCode'],$new_case[$i]);
		//var_dump($new_case);
		//die;
		/* $new_case[$i]['E0_7'] = $e0_7?$e0_7:"";
		$new_case[$i]['E8_2'] = $e8_2?$e8_2:"";
		$new_case[$i]['E2_59'] = $e2_59?$e2_59:""; */
	}
}


function SM(&$new_case,&$con){
	//$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";
	$thirtyNineYearPastFromNow = ($_POST['year'] - 39)."-".$_POST['month']."-01";
	$fourtyYearPastFromNow = ($_POST['year'] - 40)."-".$_POST['month']."-01";

	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$nm0_19 = "";
		$nf0_19 = "";
		$nm20_39 = "";
		$nf20_39 = "";
		$nm40 = "";
		$nf40 = "";
		$am0_19 = "";
		$af0_19 = "";
		$am20_39 = "";
		$af20_39 = "";
		$am40 = "";
		$af40 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$nm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$nf0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			
			//select the data related to this Diagnostic
			$am0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$af0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			
		}
		$new_case[$i]['NM0_19'] = $nm0_19?$nm0_19:"";
		$new_case[$i]['NF0_19'] = $nf0_19?$nf0_19:"";
		$new_case[$i]['NM20_39'] = $nm20_39?$nm20_39:"";
		$new_case[$i]['NF20_39'] = $nf20_39?$nf20_39:"";
		$new_case[$i]['NM40'] = $nm40?$nm40:"";
		$new_case[$i]['NF40'] = $nf40?$nf40:"";
		$new_case[$i]['AM0_19'] = $am0_19?$am0_19:"";
		$new_case[$i]['AF0_19'] = $af0_19?$af0_19:"";
		$new_case[$i]['AM20_39'] = $am20_39?$am20_39:"";
		$new_case[$i]['AF20_39'] = $af20_39?$af20_39:"";
		$new_case[$i]['AM40'] = $am40?$am40:"";
		$new_case[$i]['AF40'] = $af40?$af40:"";
	}
}

function MC(&$new_case,&$con){
	//$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";
	$thirtyNineYearPastFromNow = ($_POST['year'] - 39)."-".$_POST['month']."-01";
	$fourtyYearPastFromNow = ($_POST['year'] - 40)."-".$_POST['month']."-01";

	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$nm0_19 = "";
		$nf0_19 = "";
		$nm20_39 = "";
		$nf20_39 = "";
		$nm40 = "";
		$nf40 = "";
		$am0_19 = "";
		$af0_19 = "";
		$am20_39 = "";
		$af20_39 = "";
		$am40 = "";
		$af40 = "";
		$dm0_19 = "";
		$df0_19 = "";
		$dm20_39 = "";
		$df20_39 = "";
		$dm40 = "";
		$df40 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$nm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$nf0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			
			//select the data related to this Diagnostic
			$am0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$af0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			/* 
			//select the data related to this Diagnostic
			$dm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$df0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$dm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$df20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$dm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$df40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			 */
		}
		$new_case[$i]['NM0_19'] = $nm0_19?$nm0_19:"";
		$new_case[$i]['NF0_19'] = $nf0_19?$nf0_19:"";
		$new_case[$i]['NM20_39'] = $nm20_39?$nm20_39:"";
		$new_case[$i]['NF20_39'] = $nf20_39?$nf20_39:"";
		$new_case[$i]['NM40'] = $nm40?$nm40:"";
		$new_case[$i]['NF40'] = $nf40?$nf40:"";
		$new_case[$i]['AM0_19'] = $am0_19?$am0_19:"";
		$new_case[$i]['AF0_19'] = $af0_19?$af0_19:"";
		$new_case[$i]['AM20_39'] = $am20_39?$am20_39:"";
		$new_case[$i]['AF20_39'] = $af20_39?$af20_39:"";
		$new_case[$i]['AM40'] = $am40?$am40:"";
		$new_case[$i]['AF40'] = $af40?$af40:"";
		$new_case[$i]['DM0_19'] = $dm0_19?$dm0_19:"";
		$new_case[$i]['DF0_19'] = $df0_19?$df0_19:"";
		$new_case[$i]['DM20_39'] = $dm20_39?$dm20_39:"";
		$new_case[$i]['DF20_39'] = $df20_39?$df20_39:"";
		$new_case[$i]['DM40'] = $dm40?$dm40:"";
		$new_case[$i]['DF40'] = $df40?$df40:"";
	}
}

function AMCR(&$new_case,&$con){
	//$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";
	$thirtyNineYearPastFromNow = ($_POST['year'] - 39)."-".$_POST['month']."-01";
	$fourtyYearPastFromNow = ($_POST['year'] - 40)."-".$_POST['month']."-01";

	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$nm0_19 = "";
		$nf0_19 = "";
		$nm20_39 = "";
		$nf20_39 = "";
		$nm40 = "";
		$nf40 = "";
		/* $am0_19 = "";
		$af0_19 = "";
		$am20_39 = "";
		$af20_39 = "";
		$am40 = "";
		$af40 = ""; */
		$dm0_19 = "";
		$df0_19 = "";
		$dm20_39 = "";
		$df20_39 = "";
		$dm40 = "";
		$df40 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$nm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$nf0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			/* 
			//select the data related to this Diagnostic
			$am0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$af0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			 */
			/* 
			//select the data related to this Diagnostic
			$dm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$df0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$dm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$df20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$dm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$df40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			 */
		}
		$new_case[$i]['NM0_19'] = $nm0_19?$nm0_19:"";
		$new_case[$i]['NF0_19'] = $nf0_19?$nf0_19:"";
		$new_case[$i]['NM20_39'] = $nm20_39?$nm20_39:"";
		$new_case[$i]['NF20_39'] = $nf20_39?$nf20_39:"";
		$new_case[$i]['NM40'] = $nm40?$nm40:"";
		$new_case[$i]['NF40'] = $nf40?$nf40:"";
		/* $new_case[$i]['AM0_19'] = $am0_19?$am0_19:"";
		$new_case[$i]['AF0_19'] = $af0_19?$af0_19:"";
		$new_case[$i]['AM20_39'] = $am20_39?$am20_39:"";
		$new_case[$i]['AF20_39'] = $af20_39?$af20_39:"";
		$new_case[$i]['AM40'] = $am40?$am40:"";
		$new_case[$i]['AF40'] = $af40?$af40:""; */
		$new_case[$i]['DM0_19'] = $dm0_19?$dm0_19:"";
		$new_case[$i]['DF0_19'] = $df0_19?$df0_19:"";
		$new_case[$i]['DM20_39'] = $dm20_39?$dm20_39:"";
		$new_case[$i]['DF20_39'] = $df20_39?$df20_39:"";
		$new_case[$i]['DM40'] = $dm40?$dm40:"";
		$new_case[$i]['DF40'] = $df40?$df40:"";
	}
}

function B(&$new_case,&$con){
	//$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";
	$thirtyNineYearPastFromNow = ($_POST['year'] - 39)."-".$_POST['month']."-01";
	$fourtyYearPastFromNow = ($_POST['year'] - 40)."-".$_POST['month']."-01";

	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$nm0_19 = "";
		$nf0_19 = "";
		$nm20_39 = "";
		$nf20_39 = "";
		$nm40 = "";
		$nf40 = "";
		/* $am0_19 = "";
		$af0_19 = "";
		$am20_39 = "";
		$af20_39 = "";
		$am40 = "";
		$af40 = ""; */
		$dm0_19 = "";
		$df0_19 = "";
		$dm20_39 = "";
		$df20_39 = "";
		$dm40 = "";
		$df40 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$nm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$nf0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			$nf40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName != 'invisible'","Patient", true,$con);
			/* 
			//select the data related to this Diagnostic
			$am0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$af0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$am40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$af40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			 */
			/* 
			//select the data related to this Diagnostic
			$dm0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			//echo $sql; die;
			$df0_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$dm20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$df20_39 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && pa_info.DateofBirth >= '{$thirtyNineYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$dm40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			$df40 = returnSingleField("SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$fourtyYearPastFromNow}' && {$add_condition} && co_category.ConsultationCategoryName = 'invisible'","Patient", true,$con);
			 */
		}
		$new_case[$i]['NM0_19'] = $nm0_19?$nm0_19:"";
		$new_case[$i]['NF0_19'] = $nf0_19?$nf0_19:"";
		$new_case[$i]['NM20_39'] = $nm20_39?$nm20_39:"";
		$new_case[$i]['NF20_39'] = $nf20_39?$nf20_39:"";
		$new_case[$i]['NM40'] = $nm40?$nm40:"";
		$new_case[$i]['NF40'] = $nf40?$nf40:"";
		/* $new_case[$i]['AM0_19'] = $am0_19?$am0_19:"";
		$new_case[$i]['AF0_19'] = $af0_19?$af0_19:"";
		$new_case[$i]['AM20_39'] = $am20_39?$am20_39:"";
		$new_case[$i]['AF20_39'] = $af20_39?$af20_39:"";
		$new_case[$i]['AM40'] = $am40?$am40:"";
		$new_case[$i]['AF40'] = $af40?$af40:""; */
		$new_case[$i]['DM0_19'] = $dm0_19?$dm0_19:"";
		$new_case[$i]['DF0_19'] = $df0_19?$df0_19:"";
		$new_case[$i]['DM20_39'] = $dm20_39?$dm20_39:"";
		$new_case[$i]['DF20_39'] = $df20_39?$df20_39:"";
		$new_case[$i]['DM40'] = $dm40?$dm40:"";
		$new_case[$i]['DF40'] = $df40?$df40:"";
	}
}

function Hosp(&$new_case,&$con){
	//$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$oneYearPastFromNow = ($_POST['year'] - 1)."-".$_POST['month']."-01";
	$fourYearPastFromNow = ($_POST['year'] - 4)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	
	//loop all found Diagnostic and link them with their corresponding value now
	
		$m0_1 = ""; $dm0_1 = "";
		$f0_1 = ""; $df0_1 = "";
		$m1_4 = ""; $dm1_4 = "";
		$f1_4 = ""; $df1_4 = "";
		$m5_19 = ""; $dm5_19 = "";
		$f5_19 = ""; $df5_19 = "";
		$m20 = ""; $dm20 = "";
		$f20 = ""; $df20 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		
			//select the data related to this Diagnostic
			$m0_1 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth > '{$oneYearPastFromNow}'","Patient", true,$con);
			//echo $sql; die;
			$f0_1 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth > '{$oneYearPastFromNow}'","Patient", true,$con);
			$m1_4 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$oneYearPastFromNow}' && pa_info.DateofBirth >= '{$fourYearPastFromNow}'","Patient", true,$con);
			$f1_4 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$oneYearPastFromNow}' && pa_info.DateofBirth >= '{$fourYearPastFromNow}'","Patient", true,$con);
			$m5_19 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth < '{$fourYearPastFromNow}' && pa_info.DateofBirth >= '{$nineteenYearPastFromNow}'","Patient", true,$con);
			$f5_19 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth < '{$fourYearPastFromNow}' && pa_info.DateofBirth >= '{$nineteenYearPastFromNow}'","Patient", true,$con);
			$m20 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth < '{$nineteenYearPastFromNow}'","Patient", true,$con);
			$f20 = returnSingleField($sql = "SELECT COUNT(`ho_record`.`HORecordID`) as Patient FROM ho_record, pa_records, pa_info WHERE ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth < '{$nineteenYearPastFromNow}'","Patient", true,$con);
		
		$new_case['M0_1'] = $m0_1?$m0_1:"";
		$new_case['F0_1'] = $f0_1?$f0_1:"";
		$new_case['M1_4'] = $m1_4?$m1_4:"";
		$new_case['F1_4'] = $f1_4?$f1_4:"";
		$new_case['M5_19'] = $m5_19?$m5_19:"";
		$new_case['F5_19'] = $f5_19?$f5_19:"";
		$new_case['M20'] = $m20?$m20:"";
		$new_case['F20'] = $f20?$f20:"";
		
}

function CPHS(&$new_case,&$con){
	$fiveYearPastFromNow = ($_POST['year'] - 5)."-".$_POST['month']."-01";
	$nineteenYearPastFromNow = ($_POST['year'] - 19)."-".$_POST['month']."-01";
	
	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		$nm5 = "";
		$nf5 = "";
		$nm5_19 = "";
		$nf5_19 = "";
		$nm20 = "";
		$nf20 = "";
		
		$dm5 = "";
		$df5 = "";
		$dm5_19 = "";
		$df5_19 = "";
		$dm20 = "";
		$df20 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$nm5 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record, se_records, se_name WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth > '{$fiveYearPastFromNow}' && {$add_condition} && se_name.ServiceCode != 'MAT' ","Patient", true,$con);
			$nf5 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record, se_records, se_name WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth > '{$fiveYearPastFromNow}' && {$add_condition} && se_name.ServiceCode != 'MAT' ","Patient", true,$con);
			
			$nm5_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record, se_records, se_name WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth >= '{$fiveYearPastFromNow}' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && se_name.ServiceCode != 'MAT' ","Patient", true,$con);
			$nf5_19 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record, se_records, se_name WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth >= '{$fiveYearPastFromNow}' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && se_name.ServiceCode != 'MAT' ","Patient", true,$con);
			
			$nm20 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record, se_records, se_name WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Male' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && se_name.ServiceCode != 'MAT' ","Patient", true,$con);
			$nf20 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record, se_records, se_name WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && pa_records.PatientRecordID = se_records.PatientRecordID && se_name.ServiceNameID = se_records.ServiceNameID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.Sex='Female' && pa_info.DateofBirth <= '{$nineteenYearPastFromNow}' && {$add_condition} && se_name.ServiceCode != 'MAT' ","Patient", true,$con);
			
		}
		$new_case[$i]['NM5'] = $nm5?$nm5:"";
		$new_case[$i]['NF5'] = $nf5?$nf5:"";
		$new_case[$i]['NM5_19'] = $nm5_19?$nm5_19:"";
		$new_case[$i]['NF5_19'] = $nf5_19?$nf5_19:"";
		$new_case[$i]['NM20'] = $nm20?$nm20:"";
		$new_case[$i]['NF20'] = $nf20?$nf20:"";
		
	}
}
function SIS_CO(&$new_case,&$con){
	$twentyYearPastFromNow = ($_POST['year'] - 20)."-".$_POST['month']."-01";
	
	//loop all found Diagnostic and link them with their corresponding value now
	for($i=0; $i<count($new_case); $i++){
		$n = $new_case[$i];
		
		$c0_20 = "";
		$c20 = "";
		$h0_20 = "";
		$h20 = "";
		$d0_20 = "";
		$d20 = "";
		
		//SELECT FOUND RESULT TO DISPLAYED
		if($n["SISCode"] != ""){
			//echo $n['SISCode']."<br />";
			$add_condition = "(";
			$c = explode(";",$n['SISCode']);
			$kk_counter = 0;
			foreach($c as $kk_data){
				if($kk_counter++ > 0)
					$add_condition .= " || ";
				$add_condition .= "co_diagnostic.DiagnosticCode='{$kk_data}'";
			}
			$add_condition .= ")";
			//select the data related to this Diagnostic
			$c0_20 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateofBirth > '{$twentyYearPastFromNow}' && {$add_condition} ","Patient", true,$con);
			$c20 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && {$add_condition} ","Patient", true,$con);
			
			$h0_20 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateofBirth > '{$twentyYearPastFromNow}' && {$add_condition} ","Patient", true,$con);
			$h20 = returnSingleField($sql = "SELECT COUNT(`co_diagnostic_records`.`DiagnosticRecordID`) as Patient FROM co_diagnostic_records, co_diagnostic, co_records, pa_records, pa_info, co_price, co_category, ho_record WHERE co_diagnostic_records.DiagnosticID = co_diagnostic.DiagnosticID && co_diagnostic_records.ConsulationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID = pa_records.PatientRecordID && ho_record.RecordID = pa_records.PatientRecordID && co_records.ConsultationPriceID = co_price.ConsultationPriceID && co_price.ConsultationCategoryID = co_category.ConsultationCategoryID && pa_records.PatientID = pa_info.PatientID && pa_records.DateIn LIKE('{$_POST['year']}-{$_POST['month']}%') && pa_info.DateofBirth <= '{$twentyYearPastFromNow}' && {$add_condition} ","Patient", true,$con);
			
		}
		$new_case[$i]['C0_20'] = $c0_20?$c0_20:"";
		$new_case[$i]['C20'] = $c20?$c20:"";
		$new_case[$i]['H0_20'] = $h0_20?$h0_20:"";
		$new_case[$i]['H20'] = $h20?$h20:"";
		$new_case[$i]['D0_20'] = $d0_20?$d0_20:"";
		$new_case[$i]['D20'] = $d20?$d20:"";
		
	}
}
?>