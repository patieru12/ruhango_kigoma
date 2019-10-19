<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
require_once "../lib/config.php";
$db = new DBConnector();
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
set_time_limit(0);
$_SESSION['conflict'] = array(array());
//var_dump($_FILES);
if($_FILES['excel_file']['name']){
	//save the uploaded cover page
	if(!GetFile("excel_file",array("xls","xlsx"))){
		echo "<span class=error-text>Invalid Format!</span><br />";
		die;
	}
}
//var_dump($_FILES); die;
//upload the file for easy access
$file_name = "";
if(!move_uploaded_file($_FILES['excel_file']['tmp_name'],$file_name = "./uploads/".$_FILES['excel_file']['name'])){
	echo "<span class=error-text>Error While Uploading The File!</span>";
	die;
}

//  Include PHPExcel_IOFactory
require_once '../lib2/PHPExcel/IOFactory.php';

$inputFileName = $file_name;

//  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
	//$objReader->setDelimiter("/");
    $objPHPExcel = $objReader->load($inputFileName);
} catch(Exception $e) {
    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}
//check if the sheet full fill all needed data
//fetch all sheets name
$tables = $objPHPExcel->getSheetNames();
$valid_tables = array();
getDbTableList($valid_tables, $con);
//var_dump($tables); echo "<hr />Valid Tables:<br />";
//var_dump($valid_tables); echo "<hr />";
//filter the file for never 
$valid_sheets = array();
foreach($tables  as $index=> $tb_name ){
	if(in_array($tb_name,$valid_tables))
		$valid_sheets[$index]=$tb_name;
}
//var_dump($valid_sheets);echo "<hr />";
//var_dump($valid_sheets);


$sheets_error = 0;
$changes = array();
$reference_map = array(
						
						"ad_district"=>array(
											"ActiveTable"=>"ad_district",
											"PrimaryKey"=>"DistrictID"
											),
						"ad_sector"=>array(
											"ActiveTable"=>"ad_sector",
											"PrimaryKey"=>"SectorID",
											"DistrictID"=>array("table"=>"ad_district","Field"=>"DistrictID")
											),
						"ad_cell"=>array(
											"ActiveTable"=>"ad_cell",
											"PrimaryKey"=>"CellID",
											"SectorID"=>array("table"=>"ad_sector","Field"=>"SectorID")
											),
						"ad_village"=>array(
											"ActiveTable"=>"ad_village",
											"PrimaryKey"=>"ViillageID",
											"CellID"=>array("table"=>"ad_cell","Field"=>"CellID")
											),
						"pa_info"=>array(
											"ActiveTable"=>"pa_info",
											"PrimaryKey"=>"PatientID",
											"VillageID"=>array("table"=>"ad_village","Field"=>"ViillageID")
											),
						"sy_post"=>array(
											"ActiveTable"=>"sy_post",
											"PrimaryKey"=>"PostID"
											),
						"sy_center"=>array(
											"ActiveTable"=>"sy_center",
											"PrimaryKey"=>"CenterID"
											),
						"sy_users"=>array(
											"ActiveTable"=>"sy_users",
											"PrimaryKey"=>"UserID",
											"PostID"=>array("table"=>"sy_post","Field"=>"PostID")
											),
						"in_category"=>array(
											"ActiveTable"=>"in_category",
											"PrimaryKey"=>"InsuranceCategoryID"
											),
						"in_name"=>array(
											"ActiveTable"=>"in_name",
											"PrimaryKey"=>"InsuranceNameID",
											"CategoryID"=>array("table"=>"in_category","Field"=>"InsuranceCategoryID")
											),
						"pa_records"=>array(
											"ActiveTable"=>"pa_records",
											"PrimaryKey"=>"PatientRecordID",
											"PatientID"=>array("table"=>"pa_info","Field"=>"PatientID"),
											"InsuranceNameID"=>array("table"=>"in_name","Field"=>"InsuranceNameID"),
											"ReceptionistID"=>array("table"=>"sy_users","Field"=>"UserID")
											),
						"se_name"=>array(
											"ActiveTable"=>"se_name",
											"PrimaryKey"=>"ServiceNameID"
											),
						"se_records"=>array(
											"ActiveTable"=>"se_records",
											"PrimaryKey"=>"ServiceRecordID",
											"PatientRecordID"=>array("table"=>"pa_records","Field"=>"PatientRecordID"),
											"ServiceNameID"=>array("table"=>"se_name","Field"=>"ServiceNameID")
											),
						"pst_records"=>array(
											"ActiveTable"=>"pst_records",
											"PrimaryKey"=>"PSTRecordID",
											"PatientID"=>array("table"=>"pa_info","Field"=>"PatientID")
											),
						"sy_ids"=>array(
											"ActiveTable"=>"sy_ids",
											"PrimaryKey"=>"ID",
											"InsuranceCategoryID"=>array("table"=>"in_category","Field"=>"InsuranceCategoryID")
											),
						"ac_name"=>array(
											"ActiveTable"=>"ac_name",
											"PrimaryKey"=>"ActNameID"
											),
						"ac_price"=>array(
											"ActiveTable"=>"ac_price",
											"PrimaryKey"=>"ActPriceID",
											"ActNameID"=>array("table"=>"ac_name","Field"=>"ActNameID"),
											"InsuranceCategoryID"=>array("table"=>"in_category","Field"=>"InsuranceCategoryID")
											),
						"ac_records"=>array(
											"ActiveTable"=>"ac_records",
											"PrimaryKey"=>"ActRecordID",
											"PatientRecordID"=>array("table"=>"pa_records","Field"=>"PatientRecordID"),
											"ActPriceID"=>array("table"=>"ac_price","Field"=>"ActPriceID")
											),
						"co_category"=>array(
											"ActiveTable"=>"co_category",
											"PrimaryKey"=>"ConsultationCategoryID"
											),
						"co_price"=>array(
											"ActiveTable"=>"co_price",
											"PrimaryKey"=>"ConsultationPriceID",
											"ConsultationCategoryID"=>array("table"=>"co_category","Field"=>"ConsultationCategoryID"),
											"InsuranceCategoryID"=>array("table"=>"in_category","Field"=>"InsuranceCategoryID")
											),
						"co_records"=>array(
											"ActiveTable"=>"co_records",
											"PrimaryKey"=>"ConsultationRecordID",
											"PatientRecordID"=>array("table"=>"pa_records","Field"=>"PatientRecordID"),
											"ConsultationPriceID"=>array("table"=>"co_price","Field"=>"ConsultationPriceID")
											),
						"co_diagnostic_category"=>array(
											"ActiveTable"=>"co_diagnostic_category",
											"PrimaryKey"=>"DiagnosticCategoryID"
											),
						"co_diagnostic"=>array(
											"ActiveTable"=>"co_diagnostic",
											"PrimaryKey"=>"DiagnosticID",
											"DiagnosticCategoryID"=>array("table"=>"co_diagnostic_category","Field"=>"DiagnosticCategoryID")
											),
						"co_diagnostic_records"=>array(
											"ActiveTable"=>"co_diagnostic_records",
											"PrimaryKey"=>"DiagnosticRecordID",
											"ConsulationRecordID"=>array("table"=>"co_records","Field"=>"ConsultationRecordID"),
											"DiagnosticID"=>array("table"=>"co_diagnostic","Field"=>"DiagnosticID")
											),
						"ho_type"=>array(
											"ActiveTable"=>"ho_type",
											"PrimaryKey"=>"TypeID"
											),
						"ho_price"=>array(
											"ActiveTable"=>"ho_price",
											"PrimaryKey"=>"HOPriceID",
											"HOTypeID"=>array("table"=>"ho_type","Field"=>"TypeID"),
											"InsuranceCategoryID"=>array("table"=>"in_category","Field"=>"InsuranceCategoryID")
											),
						"ho_record"=>array(
											"ActiveTable"=>"ho_record",
											"PrimaryKey"=>"HORecordID",
											"RecordID"=>array("table"=>"pa_records","Field"=>"PatientRecordID"),
											"HOPriceID"=>array("table"=>"ho_price","Field"=>"HOPriceID")
											),
						"in_price"=>array(
											"ActiveTable"=>"in_price",
											"PrimaryKey"=>"InsurancePriceID",
											"InsuranceNameID"=>array("table"=>"in_name","Field"=>"InsuranceNameID")
											),
						"la_exam"=>array(
											"ActiveTable"=>"la_exam",
											"PrimaryKey"=>"ExamID"
											),
						"la_price"=>array(
											"ActiveTable"=>"la_price",
											"PrimaryKey"=>"ExamPriceID",
											"ExamID"=>array("table"=>"la_exam","Field"=>"ExamID"),
											"InsuranceTypeID"=>array("table"=>"in_category","Field"=>"InsuranceCategoryID")
											),
						"la_quarters"=>array(
											"ActiveTable"=>"la_quarters",
											"PrimaryKey"=>"QuarterID"
											),
						"la_records"=>array(
											"ActiveTable"=>"la_records",
											"PrimaryKey"=>"ExamRecordID",
											"ExamPriceID"=>array("table"=>"la_price","Field"=>"ExamPriceID"),
											"ConsultationRecordID"=>array("table"=>"co_records","Field"=>"ConsultationRecordID"),
											"QuarterID"=>array("table"=>"la_quarters","Field"=>"QuarterID")
											),
						"la_result"=>array(
											"ActiveTable"=>"la_result",
											"PrimaryKey"=>"ResultID",
											"ExamID"=>array("table"=>"la_exam","Field"=>"ExamID")
											),
						"la_result_record"=>array(
											"ActiveTable"=>"la_result_record",
											"PrimaryKey"=>"ResultRecordID",
											"ExamRecordID"=>array("table"=>"la_records","Field"=>"ExamRecordID"),
											"ResultID"=>array("table"=>"la_result","Field"=>"ResultID")
											),
						"md_category"=>array(
											"ActiveTable"=>"md_category",
											"PrimaryKey"=>"MedecineCategoryID"
											),
						"md_name"=>array(
											"ActiveTable"=>"md_name",
											"PrimaryKey"=>"MedecineNameID",
											"MedecineCategorID"=>array("table"=>"md_category","Field"=>"MedecineCategoryID")
											),
						"md_price"=>array(
											"ActiveTable"=>"md_price",
											"PrimaryKey"=>"MedecinePriceID",
											"MedecineNameID"=>array("table"=>"md_name","Field"=>"MedecineNameID")
											),
						"md_records"=>array(
											"ActiveTable"=>"md_records",
											"PrimaryKey"=>"MedecineRecordID",
											"ConsultationRecordID"=>array("table"=>"co_records","Field"=>"ConsultationRecordID"),
											"MedecinePriceID"=>array("table"=>"md_price","Field"=>"MedecinePriceID")
											)
						"cn_records"=>array(
											"ActiveTable"=>"cn_records",
											"PrimaryKey"=>"ConsumableRecordID",
											"PatientRecordID"=>array("table"=>"pa_records","Field"=>"PatientRecordID"),
											"MedecinePriceID"=>array("table"=>"md_price","Field"=>"MedecinePriceID")
											),
						"mu_tm"=>array(
											"ActiveTable"=>"mu_tm",
											"PrimaryKey"=>"TicketID",
											"PatientRecordID"=>array("table"=>"pa_records","Field"=>"PatientRecordID")
											)
						);
$count_sheetss = 0;
foreach($valid_sheets as $index=>$sheetname){
	/* if($sheetname != "la_quarters")
		continue; */
	//echo $sheetname."<hr />";
	$locations = array();
	$sheet = $objPHPExcel->getSheet($index);
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	$changes[$sheetname] = array();
	$tb_header = $db->fieldList($sheetname);
	// echo $sheetname;var_dump($tb_header); echo "<hr />"; continue;
	for ($row = 1; $row <= $highestRow; $row++){
		$rowData = $sheet->rangeToArray(
			'A' . $row . ':' . $highestColumn . $row,
			NULL,
			TRUE,
			FALSE);
		
		//var_dump($rowData);echo "<hr />";
		//continue;
		/* if($row >= 20)
			break; */
		//check if the column data is table headers
		if($rowData[0][0] && in_array($rowData[0][0],$tb_header)){
			//var_dump($rowData); echo "<br />";
			//map every column to corresponding column index
			foreach($rowData[0] as $column_index=>$column_value){
				$changes[$sheetname][$column_value] = array();
				$locations[$column_value] = $column_index;
			}
			continue;
		}
		//var_dump($locations);echo "<hr />"; continue;
		//run the correct function according to the given table
		$function_call = "restoreTable";
		if(function_exists($function_call)){
			restoreTable($rowData[0], $changes, $locations, $reference_map[$sheetname]);
		}
		//var_dump($rowData);
		//echo "<hr />";
		//echo $sheetname.": ". $rowData[0][1] ." Ends<hr />";
	}
	//echo "<pre>"; var_dump($changes);
	/* if($count_sheetss++>=1)
		die; */
}
echo "<span class=success>All Data Restored!</span>";
die;
for($id=0;$id < $objPHPExcel->getSheetCount();$id++){
	$error_counter = 1;
	$sheet = $objPHPExcel->getSheet($id);
	//var_dump($sheet);
	die;
	#var_dump($objPHPExcel->__numberOfSheets());
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	$address = array();
	//  Loop through each row of the worksheet in turn
	$fn = "";
	$category_id = 0;
	for ($row = 1; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray(
				'A' . $row . ':' . $highestColumn . $row,
				NULL,
				TRUE,
				FALSE);
		var_dump($rowData);
		echo "<hr /><hr />";
	}
	//break;
}
echo "<span Class=success>All Records Are Processed<br />";
if(count($_SESSION['conflict'])>0 && !is_null(@$_SESSION['conflict'][0][0])){
	//var_dump($_SESSION['conflict']);
	echo "<span class=error>".count($_SESSION['conflict'])." Has Some Error<br />";
	echo "For Other Information Click <a target='_blank' href='./error/dwnld.php'>Here</a>";
} else{
	echo "Every thing is fine!<br />";
	
}
?>