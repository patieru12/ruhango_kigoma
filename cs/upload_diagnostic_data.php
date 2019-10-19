<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
$category = array(2=>1,3=>2,4=>3,1=>4);
$header_rows = array("ID","II. POMMADES","III. MEDICAMENTS O.R.L / OPHT.","IV. MEDICAMENTS SIROP","V. MEDICAMENTS INJECTABLES","VI .MATERIELS");
$function_array = array("I. MEDICAMENTS  ORAUX"=>1,"II. POMMADES"=>3,"III. MEDICAMENTS O.R.L / OPHT."=>4,"IV. MEDICAMENTS SIROP"=>5,"V. MEDICAMENTS INJECTABLES"=>6,"VI .MATERIELS"=>2);

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
	$category_id = 0;
	for ($row = 1; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
										NULL,
										TRUE,
										FALSE);
		if(in_array($rowData[0][0],$header_rows)){
			//$fn = $function_array[$rowData[0][0]];
			continue;
		}
		
		if(!is_float($rowData[0][0]) && trim($rowData[0][0])){
			//check if the found category is not exists
			if(!$category_id = returnSingleField($sql = "SELECT DiagnosticCategoryID FROM co_diagnostic_category WHERE CategoryName='".PDB($rowData[0][0],true,$con)."'",$field="DiagnosticCategoryID",$data=true, $con)){
				$category_id = saveAndReturnID($sql = "INSERT INTO co_diagnostic_category SET CategoryName='".PDB($rowData[0][0],true,$con)."'", $con);
			}
			/* 
			$a = 3;
			if($a== 0){ // 0 if(false)
				echo $a." Is zero"; //
			} else{
				echo $a." is not zero"; //3 is not zero
			}
			 */
			continue;
		} elseif(!trim($rowData[0][0])){
			continue;
		}
		//echo $fn;
		if($rowData[0][1])
			saveDG($rowData[0],$category_id,$con);
		//break;
		//echo "<input type=text value='{$rowData[0][1]}' />";
		//echo $category_id;
		//var_dump($rowData); echo "<hr />";
	}
	//break;
}
echo "<span Class=success>All Records Are Prossed<br />";
if(count($_SESSION['conflict'])>0 && !is_null(@$_SESSION['conflict'][0][0])){
	var_dump($_SESSION['conflict']);
	echo "<span class=error>".count($_SESSION['conflict'])." Has Some Error<br />";
	echo "For Other Information Click <a target='_blank' href='./error/dwnld.php'>Here</a>";
} else{
	echo "Every thing is fine!";
	
}
?>