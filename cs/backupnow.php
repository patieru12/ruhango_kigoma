<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	//echo "<script>window.location='../logout.php';</script>";
	return;
}
set_time_limit(0);

//start excel export system
//now generate the excel file for the report
$file_name = str_replace("-","_",$_POST['date']).".xlsx";
////echo $file_name;

//include phpExcel library in the file
require_once "../lib2/PHPExcel/IOFactory.php";
require_once "../lib2/PHPExcel.php";
//instantiate the PHPExcel object
$objPHPExcel = new PHPExcel;


//var_dump($_POST); die;
$sql = "
	SELECT 
		pa_records.* 
	FROM 
		pa_records
	WHERE
		pa_records.DateIn LIKE('{$_POST['date']}%')";
$pa_records = $patients = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
//var_dump($patients);
	//variable for patient info;
	$pa_info_query = "(";
		//variable for records
	$co_records_query = "(";
	$ho_records_query = "(";
	$ac_records_query = "(";
	$cn_records_query = "(";
	$se_records_query = "(";
		//variable additional query
	$mu_tm_query = "(";
	$sy_users_query = "(";
	
		//add insurance query
	$in_name_query = "(";
if($patients){
	
	$i = 0;
	foreach($patients as $patient){
		if(!preg_match("/`pa_info`.`PatientID` = '{$patient['PatientID']}'/",$pa_info_query)){
			if($i>0)
				$pa_info_query .= " || ";
			
			$pa_info_query .= "`pa_info`.`PatientID` = '{$patient['PatientID']}'";
		}
		//add consultation records to the command
		if(!preg_match("/`co_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'/",$co_records_query)){
			if($i>0)
				$co_records_query .= " || ";
			
			$co_records_query .= "`co_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'";
		}
		//add hospitalization
		if(!preg_match("/`ho_record`.`RecordID` = '{$patient['PatientRecordID']}'/",$ho_records_query)){
			if($i>0)
				$ho_records_query .= " || ";
			
			$ho_records_query .= "`ho_record`.`RecordID` = '{$patient['PatientRecordID']}'";
		}
		//add act records
		if(!preg_match("/`ac_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'/",$ac_records_query)){
			if($i>0)
				$ac_records_query .= " || ";
			
			$ac_records_query .= "`ac_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'";
		}
		//add consumable records
		if(!preg_match("/`cn_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'/",$cn_records_query)){
			if($i>0)
				$cn_records_query .= " || ";
			
			$cn_records_query .= "`cn_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'";
		}
		//add service records
		if(!preg_match("/`se_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'/",$se_records_query)){
			if($i>0)
				$se_records_query .= " || ";
			
			$se_records_query .= "`se_records`.`PatientRecordID` = '{$patient['PatientRecordID']}'";
		}
		
		//add tm records
		if(!preg_match("/`mu_tm`.`PatientRecordID` = '{$patient['PatientRecordID']}'/",$mu_tm_query)){
			if($i>0)
				$mu_tm_query .= " || ";
			
			$mu_tm_query .= "`mu_tm`.`PatientRecordID` = '{$patient['PatientRecordID']}'";
		}
		
		if(!preg_match("/`sy_users`.`UserID` = '{$patient['ReceptionistID']}'/",$sy_users_query)){
			if($i>0)
				$sy_users_query .= " || ";
			
			$sy_users_query .= "`sy_users`.`UserID` = '{$patient['ReceptionistID']}'";
		}
		if(!preg_match("/`in_name`.`InsuranceNameID` = '{$patient['InsuranceNameID']}'/",$in_name_query)){
			if($i>0)
				$in_name_query .= " || ";
			
			$in_name_query .= "`in_name`.`InsuranceNameID` = '{$patient['InsuranceNameID']}'";
		}
		$i++;
	}
} else{
	//echo "<span class='error-text'>No Patient to backup</span>";
	return;
}
$pa_info_query .= ")";
$co_records_query .= ")";
$ho_records_query .= ")";
$ac_records_query .= ")";
$cn_records_query .= ")";
$se_records_query .= ")";
$mu_tm_query .= ")";
$sy_users_query .= ")";
$in_name_query .= ")";
//select information about the patient information
$sql = "SELECT * FROM pa_info WHERE {$pa_info_query}";
$pa_info = $patient_info = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
////echo "<pre>";var_dump($patient_info);

$sql = "SELECT * FROM co_records WHERE {$co_records_query}";
$co_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM ho_record WHERE {$ho_records_query}";
$ho_record = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM ac_records WHERE {$ac_records_query}";
$ac_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM cn_records WHERE {$cn_records_query}";
$cn_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM se_records WHERE {$se_records_query}";
$se_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM mu_tm WHERE {$mu_tm_query}";
$mu_tm = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM sy_users WHERE {$sy_users_query}";
$sy_users = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM in_name WHERE {$in_name_query}";
$in_name = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
////echo $sql;
//var_dump($sy_users);
/*************************** SELECT PATIENT DEPEDENCIES ************************/
$pa_insurance_cards_query = "(";
$pst_records_query = "(";
$ad_village_query = "(";
$i=0;
foreach($patient_info as $patient){
	if(!preg_match("/`pa_insurance_cards`.`PatientID` = '{$patient['PatientID']}'/",$pa_insurance_cards_query)){
		if($i>0)
			$pa_insurance_cards_query .= " || ";
		
		$pa_insurance_cards_query .= "`pa_insurance_cards`.`PatientID` = '{$patient['PatientID']}'";
	}
	if(!preg_match("/`pst_records`.`PatientID` = '{$patient['PatientID']}'/",$pst_records_query)){
		if($i>0)
			$pst_records_query .= " || ";
		
		$pst_records_query .= "`pst_records`.`PatientID` = '{$patient['PatientID']}'";
	}
	if(!preg_match("/`ad_village`.`ViilageID` = '{$patient['VillageID']}'/",$ad_village_query)){
		if($i>0)
			$ad_village_query .= " || ";
		
		$ad_village_query .= "`ad_village`.`ViillageID` = '{$patient['VillageID']}'";
	}
	$i++;
}
$pa_insurance_cards_query .= ")";
$pst_records_query .= ")";
$ad_village_query .= ")";

$sql = "SELECT * FROM pa_insurance_cards WHERE {$pa_insurance_cards_query}";
$pa_insurance_cards = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM pst_records WHERE {$pst_records_query}";
$pst_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM ad_village WHERE {$ad_village_query}";
$ad_village = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

//var_dump($sy_users);
////echo $pa_insurance_cards_query;
/****************** Now Village Dependencies ***********************/
$ad_cell_query = "(";

if($ad_village){
	$i = 0;
	foreach($ad_village as $village){
		if(!preg_match("/`ad_cell`.`CellID`='{$village['CellID']}'/",$ad_cell_query)){
			if($i>0)
				$ad_cell_query .= " || ";
			$ad_cell_query .= "`ad_cell`.`CellID`='{$village['CellID']}'";
		}
		$i++;
	}
}
$ad_cell_query .= ")";

$sql = "SELECT * FROM ad_cell WHERE {$ad_cell_query}";
$ad_cell = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now cell Dependencies ***********************/
$ad_sector_query = "(";

if($ad_cell){
	$i = 0;
	foreach($ad_cell as $cell){
		if(!preg_match("/`ad_sector`.`SectorID`='{$cell['SectorID']}'/",$ad_sector_query)){
			if($i>0)
				$ad_sector_query .= " || ";
			$ad_sector_query .= "`ad_sector`.`SectorID`='{$cell['SectorID']}'";
		}
		$i++;
	}
}
$ad_sector_query .= ")";

$sql = "SELECT * FROM ad_sector WHERE {$ad_sector_query}";
$ad_sector = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now sector Dependencies ***********************/
$ad_district_query = "(";

if($ad_sector){
	$i = 0;
	foreach($ad_sector as $sector){
		if(!preg_match("/`ad_district`.`DistrictID`='{$sector['DistrictID']}'/",$ad_sector_query)){
			if($i>0)
				$ad_district_query .= " || ";
			$ad_district_query .= "`ad_district`.`DistrictID`='{$sector['DistrictID']}'";
		}
		$i++;
	}
}
$ad_district_query .= ")";

$sql = "SELECT * FROM ad_district WHERE {$ad_district_query}";
$ad_district = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now sy_users Dependencies ***********************/
$sy_post_query = "(";
$sy_center_query = "(";

if($sy_users){
	$i = 0;
	foreach($sy_users as $user){
		if(!preg_match("/`sy_post`.`PostID`='{$user['PostID']}'/",$sy_post_query)){
			if($i>0)
				$sy_post_query .= " || ";
			$sy_post_query .= "`sy_post`.`PostID`='{$user['PostID']}'";
		}
		if(!preg_match("/`sy_center`.`CenterID`='{$user['CenterID']}'/",$sy_center_query)){
			if($i>0)
				$sy_center_query .= " || ";
			$sy_center_query .= "`sy_center`.`CenterID`='{$user['CenterID']}'";
		}
		$i++;
	}
}
$sy_post_query .= ")";
$sy_center_query .= ")";

$sql = "SELECT * FROM sy_post WHERE {$sy_post_query}";
$sy_post = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM sy_center WHERE {$sy_center_query}";
$sy_center = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now insurance name Dependencies ***********************/
$in_category_query = "(";

if($in_name){
	$i = 0;
	foreach($in_name as $insurance_name){
		if(!preg_match("/`in_category`.`InsuranceCategoryID`='{$insurance_name['CategoryID']}'/",$in_category_query)){
			if($i>0)
				$in_category_query .= " || ";
			$in_category_query .= "`in_category`.`InsuranceCategoryID`='{$insurance_name['CategoryID']}'";
		}
		$i++;
	}
}
$in_category_query .= ")";

$sql = "SELECT * FROM in_category WHERE {$in_category_query}";
$in_category = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now service records Dependencies ***********************/
$se_name_query = "(";

if($se_records){
	$i = 0;
	foreach($se_records as $record){
		if(!preg_match("/`se_name`.`ServiceNameID`='{$record['ServiceNameID']}'/",$se_name_query)){
			if($i>0)
				$se_name_query .= " || ";
			$se_name_query .= "`se_name`.`ServiceNameID`='{$record['ServiceNameID']}'";
		}
		$i++;
	}
}
$se_name_query .= ")";

$sql = "SELECT * FROM se_name WHERE {$se_name_query}";
$se_name = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now acts records Dependencies ***********************/
$ac_price_query = "(";

if($ac_records){
	$i = 0;
	foreach($ac_records as $record){
		if(!preg_match("/`ac_price`.`ActPriceID`='{$record['ActPriceID']}'/",$ac_price_query)){
			if($i>0)
				$ac_price_query .= " || ";
			$ac_price_query .= "`ac_price`.`ActPriceID`='{$record['ActPriceID']}'";
		}
		$i++;
	}
}
$ac_price_query .= ")";

$sql = "SELECT * FROM ac_price WHERE {$ac_price_query}";
$ac_price = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now acts price Dependencies ***********************/
$ac_name_query = "(";

if($ac_price){
	$i = 0;
	foreach($ac_price as $record){
		if(!preg_match("/`ac_name`.`ActNameID`='{$record['ActNameID']}'/",$ac_name_query)){
			if($i>0)
				$ac_name_query .= " || ";
			$ac_name_query .= "`ac_name`.`ActNameID`='{$record['ActNameID']}'";
		}
		$i++;
	}
}
$ac_name_query .= ")";

$sql = "SELECT * FROM ac_name WHERE {$ac_name_query}";
$ac_name = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now patient DocID Dependencies ***********************/
$sy_ids_query = "(";

if($in_category){
	$i = 0;
	foreach($in_category as $category){
		if(!preg_match("/`sy_ids`.`InsuranceCategoryID`='{$category['InsuranceCategoryID']}'/",$sy_ids_query)){
			if($i>0)
				$sy_ids_query .= " || ";
			$sy_ids_query .= "`sy_ids`.`InsuranceCategoryID`='{$category['InsuranceCategoryID']}'";
		}
		$i++;
	}
}
$sy_ids_query .= ")";

$sql = "SELECT * FROM sy_ids WHERE {$sy_ids_query}";
$sy_ids = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/*************************** SELECT CONSULTATION DEPENDENCIES ******************/
$la_records_query = "(";
$md_records_query = "(";
$co_price_query = "(";
$co_diagnostic_records_query = "(";
$i=0;

foreach($co_records as $patient){
	if(!preg_match("/`la_records`.`ConsultationRecordID` = '{$patient['ConsultationRecordID']}'/",$la_records_query)){
		if($i>0)
			$la_records_query .= " || ";
		
		$la_records_query .= "`la_records`.`ConsultationRecordID` = '{$patient['ConsultationRecordID']}'";
	}
	if(!preg_match("/`md_records`.`ConsultationRecordID` = '{$patient['ConsultationRecordID']}'/",$md_records_query)){
		if($i>0)
			$md_records_query .= " || ";
		
		$md_records_query .= "`md_records`.`ConsultationRecordID` = '{$patient['ConsultationRecordID']}'";
	}
	if(!preg_match("/`co_price`.`ConsultationPriceID` = '{$patient['ConsultationPriceID']}'/",$co_price_query)){
		if($i>0)
			$co_price_query .= " || ";
		
		$co_price_query .= "`co_price`.`ConsultationPriceID` = '{$patient['ConsultationPriceID']}'";
	}
	if(!preg_match("/`co_diagnostic_records`.`ConsulationRecordID` = '{$patient['ConsultationRecordID']}'/",$co_diagnostic_records_query)){
		if($i>0)
			$co_diagnostic_records_query .= " || ";
		
		$co_diagnostic_records_query .= "`co_diagnostic_records`.`ConsulationRecordID` = '{$patient['ConsultationRecordID']}'";
	}
	$i++;
}
$la_records_query .= ")";
$md_records_query .= ")";
$co_price_query .= ")";
$co_diagnostic_records_query .= ")";

$sql = "SELECT * FROM la_records WHERE {$la_records_query}";
$la_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM md_records WHERE {$md_records_query}";
$md_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM co_price WHERE {$co_price_query}";
$co_price = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM co_diagnostic_records WHERE {$co_diagnostic_records_query}";
$co_diagnostic_records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

//var_dump($pa_records);
////echo $la_records_query;

/****************** Now Consultation Price Dependencies ***********************/
$co_category_query = "(";

if($co_price){
	$i = 0;
	foreach($co_price as $category){
		if(!preg_match("/`co_category`.`ConsultationCategoryID`='{$category['ConsultationCategoryID']}'/",$co_category_query)){
			if($i>0)
				$co_category_query .= " || ";
			$co_category_query .= "`co_category`.`ConsultationCategoryID`='{$category['ConsultationCategoryID']}'";
		}
		$i++;
	}
}
$co_category_query .= ")";

$sql = "SELECT * FROM co_category WHERE {$co_category_query}";
$co_category = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now Diagnostic records Dependencies ***********************/
$co_diagnostic_query = "(";

if($co_diagnostic_records){
	$i = 0;
	foreach($co_diagnostic_records as $category){
		if(!preg_match("/`co_diagnostic`.`DiagnosticID`='{$category['DiagnosticID']}'/",$co_diagnostic_query)){
			if($i>0)
				$co_diagnostic_query .= " || ";
			$co_diagnostic_query .= "`co_diagnostic`.`DiagnosticID`='{$category['DiagnosticID']}'";
		}
		$i++;
	}
}
$co_diagnostic_query .= ")";

$sql = "SELECT * FROM co_diagnostic WHERE {$co_diagnostic_query}";
$co_diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now Diagnostic name Dependencies ***********************/
$co_diagnostic_category_query = "(";

if($co_diagnostic){
	$i = 0;
	foreach($co_diagnostic as $category){
		if(!preg_match("/`co_diagnostic_category`.`DiagnosticCategoryID`='{$category['DiagnosticCategoryID']}'/",$co_diagnostic_category_query)){
			if($i>0)
				$co_diagnostic_category_query .= " || ";
			$co_diagnostic_category_query .= "`co_diagnostic_category`.`DiagnosticCategoryID`='{$category['DiagnosticCategoryID']}'";
		}
		$i++;
	}
}
$co_diagnostic_category_query .= ")";

$sql = "SELECT * FROM co_diagnostic_category WHERE {$co_diagnostic_category_query}";
$co_diagnostic_category = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now hospitalization records Dependencies ***********************/
$ho_price_query = "";

if($ho_record){
	$ho_price_query = "(";
	$i = 0;
	foreach($ho_record as $category){
		if(!preg_match("/`ho_price`.`HOPriceID`='{$category['HOPriceID']}'/",$ho_price_query)){
			if($i>0)
				$ho_price_query .= " || ";
			$ho_price_query .= "`ho_price`.`HOPriceID`='{$category['HOPriceID']}'";
		}
		$i++;
	}
	$ho_price_query .= ")";
}
//var_dump($ho_records);
$ho_price = null;
if($ho_price_query){
	$sql = "SELECT * FROM ho_price WHERE {$ho_price_query}";
	$ho_price = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
}
/****************** Now hospitalization price Dependencies ***********************/
$ho_type_query = "";

if($ho_price){
	$ho_type_query = "(";
	$i = 0;
	foreach($ho_price as $category){
		if(!preg_match("/`ho_type`.`TypeID`='{$category['HOTypeID']}'/",$ho_type_query)){
			if($i>0)
				$ho_type_query .= " || ";
			$ho_type_query .= "`ho_type`.`TypeID`='{$category['HOTypeID']}'";
		}
		$i++;
	}
	$ho_type_query .= ")";
}
$ho_type = null;
if($ho_type_query){
	$sql = "SELECT * FROM ho_type WHERE {$ho_type_query}";
	$ho_type = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
}
/****************** Now insurance name additional data **************/
$in_price_query = "(";

if($in_name){
	$i = 0;
	foreach($in_name as $category){
		if(!preg_match("/`in_price`.`InsuranceNameID`='{$category['InsuranceNameID']}'/",$in_price_query)){
			if($i>0)
				$in_price_query .= " || ";
			$in_price_query .= "`in_price`.`InsuranceNameID`='{$category['InsuranceNameID']}'";
		}
		$i++;
	}
}
$in_price_query .= ")";

$sql = "SELECT * FROM in_price WHERE {$in_price_query}";
$in_price = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now exam records dependencies **************/
$la_quarters_query = "(";
$la_price_query = "(";
$la_result_records_query = "(";

if($la_records){
	$i = 0;
	foreach($la_records as $category){
		if(!preg_match("/`la_quarters`.`QuarterID`='{$category['QuarterID']}'/",$la_quarters_query)){
			if($i>0)
				$la_quarters_query .= " || ";
			$la_quarters_query .= "`la_quarters`.`QuarterID`='{$category['QuarterID']}'";
		}
		if(!preg_match("/`la_price`.`ExamPriceID`='{$category['ExamPriceID']}'/",$la_price_query)){
			if($i>0)
				$la_price_query .= " || ";
			$la_price_query .= "`la_price`.`ExamPriceID`='{$category['ExamPriceID']}'";
		}
		if(!preg_match("/`la_result_record`.`ExamRecordID`='{$category['ExamRecordID']}'/",$la_result_records_query)){
			if($i>0)
				$la_result_records_query .= " || ";
			$la_result_records_query .= "`la_result_record`.`ExamRecordID`='{$category['ExamRecordID']}'";
		}
		$i++;
	}
}
$la_quarters_query .= ")";
$la_price_query .= ")";
$la_result_records_query .= ")";

$sql = "SELECT * FROM la_quarters WHERE {$la_quarters_query}";
$la_quarters = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM la_price WHERE {$la_price_query}";
$la_price = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

$sql = "SELECT * FROM la_result_record WHERE {$la_result_records_query}";
$la_result_record = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now exam price dependencies **************/
$la_exam_query = "(";

if($la_price){
	$i = 0;
	foreach($la_price as $category){
		if(!preg_match("/`la_exam`.`ExamID`='{$category['ExamID']}'/",$la_exam_query)){
			if($i>0)
				$la_exam_query .= " || ";
			$la_exam_query .= "`la_exam`.`ExamID`='{$category['ExamID']}'";
		}
		$i++;
	}
}
$la_exam_query .= ")";

$sql = "SELECT * FROM la_exam WHERE {$la_exam_query}";
$la_exam = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now exam result records dependencies **************/
$la_result_query = "(";

if($la_result_record){
	$i = 0;
	foreach($la_result_record as $category){
		if(!preg_match("/`la_result`.`ResultID`='{$category['ResultID']}'/",$la_result_query)){
			if($i>0)
				$la_result_query .= " || ";
			$la_result_query .= "`la_result`.`ResultID`='{$category['ResultID']}'";
		}
		$i++;
	}
}
$la_result_query .= ")";

$sql = "SELECT * FROM la_result WHERE {$la_result_query}";
$la_result = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now medicine records dependencies **************/
$md_price_query = "(";

if($md_records){
	$i = 0;
	foreach($md_records as $category){
		if(!preg_match("/`md_price`.`MedecinePriceID`='{$category['MedecinePriceID']}'/",$md_price_query)){
			if($i>0)
				$md_price_query .= " || ";
			$md_price_query .= "`md_price`.`MedecinePriceID`='{$category['MedecinePriceID']}'";
		}
		$i++;
	}
}
$md_price_query .= ")";

$sql = "SELECT * FROM md_price WHERE {$md_price_query}";
$md_price = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now medicine price dependencies **************/
$md_name_query = "(";

if($md_price){
	$i = 0;
	foreach($md_price as $category){
		if(!preg_match("/`md_name`.`MedecineNameID`='{$category['MedecineNameID']}'/",$md_name_query)){
			if($i>0)
				$md_name_query .= " || ";
			$md_name_query .= "`md_name`.`MedecineNameID`='{$category['MedecineNameID']}'";
		}
		$i++;
	}
}
$md_name_query .= ")";

$sql = "SELECT * FROM md_name WHERE {$md_name_query}";
$md_name = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/****************** Now medicine name dependencies **************/
$md_category_query = "(";

if($md_name){
	$i = 0;
	foreach($md_name as $category){
		if(!preg_match("/`md_category`.`MedecineCategoryID`='{$category['MedecineCategorID']}'/",$md_category_query)){
			if($i>0)
				$md_category_query .= " || ";
			$md_category_query .= "`md_category`.`MedecineCategoryID`='{$category['MedecineCategorID']}'";
		}
		$i++;
	}
}
$md_category_query .= ")";

$sql = "SELECT * FROM md_category WHERE {$md_category_query}";
$md_category = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);

/*************************** SELECT CONSULTATION DEPENDENCIES ******************/


/************************* NOW MAKE THE ARRAY OF ALL INFORMATION *******/
$data_info = array();
//add district  table
$data_info['ad_district'] = "ad_district";
//echo "<ol style=1 ><li>District table added</li>";
//add sector table
$data_info['ad_sector'] = "ad_sector";
//echo "<li>Sector table added</li>";
//add cell table
$data_info['ad_cell'] = "ad_cell";
//echo "<li>Cell table added</li>";
//add village table
$data_info['ad_village'] = "ad_village";
//echo "<li>Village table added</li>";

//add pa_info table
$data_info['pa_info'] = "pa_info";
//echo "<li>pa_info table added</li>";

//add sy_post table
$data_info['sy_post'] = "sy_post";
//echo "<li>sy_post table added</li>";
//add center table
$data_info['sy_center'] = "sy_center";
//echo "<li>sy_center table added</li>";

//add user table
$data_info['sy_users'] = "sy_users";
//echo "<li>sy_users table added</li>";

//add in_category table
$data_info['in_category'] = "in_category";
//echo "<li>in_category table added</li>";
//add in_name table
$data_info['in_name'] = "in_name";
//echo "<li>in_name table added</li>";

//add pa_records table
$data_info['pa_records'] = "pa_records";
//echo "<li>pa_records table added</li>";

//add se_name table
$data_info['se_name'] = "se_name";
//echo "<li>se_name table added</li>";

//add se_records table
$data_info['se_records'] = "se_records";
//echo "<li>se_records table added</li>";

//add pst_records table
$data_info['pst_records'] = "pst_records";
//echo "<li>pst_records table added</li>";

//add sy_ids table
$data_info['sy_ids'] = "sy_ids";
//echo "<li>sy_ids table added</li>";

//add ac_name table
$data_info['ac_name'] = "ac_name";
//echo "<li>ac_name table added</li>";
//add ac_price table
$data_info['ac_price'] = "ac_price";
//echo "<li>ac_price table added</li>";
//add ac_records table
$data_info['ac_records'] = "ac_records";
//echo "<li>ac_records table added</li>";

//add co_category table
$data_info['co_category'] = "co_category";
//echo "<li>co_category table added</li>";
//add co_price table
$data_info['co_price'] = "co_price";
//echo "<li>co_price table added</li>";
//add co_records table
$data_info['co_records'] = "co_records";
//echo "<li>co_records table added</li>";
//add co_diagnostic_category table
$data_info['co_diagnostic_category'] = "co_diagnostic_category";
//echo "<li>co_diagnostic_category table added</li>";
//add co_diagnostic table
$data_info['co_diagnostic'] = "co_diagnostic";
//echo "<li>co_diagnostic table added</li>";
//add co_diagnostic_records table
$data_info['co_diagnostic_records'] = "co_diagnostic_records";
//echo "<li>co_diagnostic_records table added</li>";

//add ho_type table
$data_info['ho_type'] = "ho_type";
//echo "<li>ho_type table added</li>";
//add ho_price table
$data_info['ho_price'] = "ho_price";
//echo "<li>ho_price table added</li>";
//add ho_record table
$data_info['ho_record'] = "ho_record";
//echo "<li>ho_record table added</li>";

//add in_price table
$data_info['in_price'] = "in_price";
//echo "<li>in_price table added</li>";

//add la_exam table
$data_info['la_exam'] = "la_exam";
//echo "<li>la_exam table added</li>";
//add la_price table
$data_info['la_price'] = "la_price";
//echo "<li>la_price table added</li>";
//add la_quarters table
$data_info['la_quarters'] = "la_quarters";
//echo "<li>la_quarters table added</li>";
//add la_records table
$data_info['la_records'] = "la_records";
//echo "<li>la_records table added</li>";
//add la_result table
$data_info['la_result'] = "la_result";
//echo "<li>la_result table added</li>";
//add la_result_record table
$data_info['la_result_record'] = "la_result_record";
//echo "<li>la_result_record table added</li>";

//add md_category table
$data_info['md_category'] = "md_category";
//echo "<li>md_category table added</li>";
//add md_name table
$data_info['md_name'] = "md_name";
//echo "<li>md_name table added</li>";
//add md_price table
$data_info['md_price'] = "md_price";
//echo "<li>md_price table added</li>";
//add md_records table
$data_info['md_records'] = "md_records";
//add cn_records table
$data_info['cn_records'] = "cn_records";
//echo "<li>md_records table added</li>";

//add mu_tm table
$data_info['mu_tm'] = "mu_tm";
//echo "<li>mu_tm table added</li>";

//echo "</ol>";
//$v = "se_records";

////echo var_dump($$v);

//instantiate the writer object
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel, "Excel2007");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

//loop all function table now
$tbl_counter = 0;
foreach($data_info as $tbl){
	//var_dump($$tbl);
	//each valid iteration is a work sheet
	echo $tbl."<br />";
	if($tbl == 'ad_cell')
		break;
	$objPHPExcel->setActiveSheetIndex($tbl_counter);
	
	$activeSheet = $objPHPExcel->getActiveSheet();
	//echo $tbl;
	//loop information in one table information now
	if($$tbl){
		$row_counter = 1; $skipped_rows = 1; $last_colum = 'A';
		foreach($$tbl as $tbl_data){
			$column_counter = 'A';
			foreach($tbl_data as $column=>$value){
				//var_dump($value);
				if($row_counter == 1){
					//now write the Table header now
					$activeSheet->setCellValue($column_counter.$row_counter, $column);
					
					//set the column to be auto resize
					$activeSheet->getColumnDimension($column_counter)->setAutoSize(true);
					
					if($column_counter > $last_colum)
						$last_colum = $column_counter;
				}
				//write the value in the next row
				$activeSheet->setCellValue($column_counter.($row_counter + $skipped_rows), $value);
				
				//increment the column
				$column_counter++;
			}
			//increment rewritable rows
			$row_counter++;
		}
		//trace necessary border in the file
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF000000'),
				)
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle("A1:".$last_colum.($row_counter))->applyFromArray($styleArray);
		
		//table end all data are in the excel worksheet rename it
		$activeSheet->setTitle($tbl);
		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();
		/* 
		echo "<hr /><hr />"; */
		$tbl_counter++;
		
		//now try to unset 
		unset($$tbl);
	}
}
//at the end of all worksheet save the file now

$objWriter->save("./bckup/".$file_name);
//var_dump($data_info);
?>