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
$category = array(2=>1,3=>2,4=>3,1=>4);
$header_rows = array("I. MEDICAMENTS ORAUX",
					"II. POMMADES",
					"III. MEDICAMENTS O.R.L / OPHT.",
					"IV. MEDICAMENTS SIROP",
					"V. MEDICAMENTS INJECTABLES");
$function_array = array("I. MEDICAMENTS ORAUX"=>1,"II. POMMADES"=>3,"III. MEDICAMENTS O.R.L / OPHT."=>4,"IV. MEDICAMENTS SIROP"=>5,"V. MEDICAMENTS INJECTABLES"=>6);

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
	$fn = ""; $current_date = "";
	for ($row = 3; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
										NULL,
										TRUE,
										FALSE);
										
		
		//var_dump($rowData[0][0]); echo "<br />";
		if($row == 3){
			if(is_float($rowData[0][0])){
				//$displayDate = PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][$address['DateofBirth']], 'YYYY-MM-DD');
				$current_date = PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][0], 'YYYY-MM-DD');
				//echo  $displayDate."<==".$rowData[0][$address['DateofBirth']]; echo "<br />";
			} else{
				$current_date = $rowData[0][0];
			}
		continue;
		}
		//var_dump($rowData); die;
		//var_dump(in_array($rowData[0][0],$header_rows)); */
		if(in_array($rowData[0][0],$header_rows)){
			$fn = $function_array[$rowData[0][0]];
			//var_dump($rowData); echo "<hr />";
			continue;
		}
		//var_dump($fn);// die;
		//var_dump(in_array(strtoupper($rowData[0][0]),$header_rows));
		//var_dump($rowData,$header_rows); die;
		//echo $fn;
		$rowData[0][] = $current_date;
		if($rowData[0][0])
			saveMD($rowData[0],$fn,$con);
		
		//var_dump($rowData); echo "<hr />";
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