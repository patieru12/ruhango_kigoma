<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
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
		echo "<span class=error-text>Invalid Format!</span>";
		die;
	}
}

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

//var_dump($_POST);die;
//search for insurance info now
//$insu = returnSingleField("SELECT InsuranceName FROM in_name WHERE InsuranceNameID='{$_POST['insurance_id']}'","InsuranceName",$data=true, $con);
//var_dump($insu);die;

//rename all data in the column of insurance category
/**********************************************************************************
*                                                                                 *
* This array map the tarif excel document to the the current insurance categories *  
*                                                                                 *
* Index are insurance Category Ids in database                                    *
* Value are column index in the excel worksheet                                   *
***********************************************************************************/
$category = array(
					2=>1,	// 2=Community Insurance  	==> Column B
					3=>2,	// 3=MMI				  	==> Column C
					5=>5,   // 5=No Valuable Insurance	==> Column F
					4=>3,	// 4=RAMA				  	==> Column D
					1=>4	// 1=Private Insurance	  	==> Column E
				);
$header_rows = array("I. CONSULTATION","II. HOSPITALISATION","III. LABORATOIRE","IV. CHIRURGIE","V.SOINS INFIRMIERS","VI. AUTRES PROCEDURES");
$function_array = array("I. CONSULTATION"=>"co","II. HOSPITALISATION"=>"ho","III. LABORATOIRE"=>"la","IV. CHIRURGIE"=>"ac","V.SOINS INFIRMIERS"=>"ac","VI. AUTRES PROCEDURES"=>"am");
$locations = array();
$sheets_error = 0;
for($id=0;$id < $objPHPExcel->getSheetCount();$id++){
	$error_counter = 1;
	$sheet = $objPHPExcel->getSheet($id);
	#var_dump($sheet);
	#var_dump($objPHPExcel->__numberOfSheets());
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	$address = array();
	//  Loop through each row of the worksheet in turn
	$fn = "";
	for ($row = 1; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
										NULL,
										TRUE,
										FALSE);
		if(in_array($rowData[0][0],$header_rows)){
			$fn = $function_array[$rowData[0][0]];
			continue;
		}
		if($fn == "")
			continue;
		//echo $fn;
		if($rowData[0][0]){
			//var_dump($fn, $rowData[0],"<br />",$category,"<br />", $con); echo "<hr />";
			$fn($rowData[0],$category,$con);
		}
		//break;
	}
	//break;
}
echo "<span Class=success>All Records Are Prossed<br />";
if(count($_SESSION['conflict'])>0){
	echo "<span class=error>".count($_SESSION['conflict'])." Has Some Error<br />";
	echo "For Other Information Click <a target='_blank' href='./error/dwnld.php'>Here</a>";
} else{
	echo "Every thing is fine!";
	
}
?>