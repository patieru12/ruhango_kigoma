<?php
session_start();
set_time_limit(0);
//error_reporting(E_ALL);
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("adm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//die;
$_SESSION['conflict'] = array(array());
//var_dump($_FILES);
if($_FILES['patient_list']['name']){
	//save the uploaded cover page
	if(!GetFile("patient_list",array("xls","xlsx"))){
		echo "<span class=error-text>Invalid Format!</span>";
		die;
	}
}
//upload the file for easy access
$file_name = "";
if(!move_uploaded_file($_FILES['patient_list']['tmp_name'],$file_name = "./fls/".$_FILES['patient_list']['name'])){
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
	//var_dump($inputFileName);
    $objPHPExcel = $objReader->load($inputFileName);
} catch(Exception $e) {
    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

//var_dump($_POST);die;
//search for insurance info now
$insu = returnSingleField("SELECT InsuranceName FROM in_name WHERE InsuranceNameID='{$_POST['insurance_id']}'","InsuranceName",$data=true, $con);
//var_dump($insu);die;
$list = array(); $header_row = "";
if($insu == "CBHI"){
	$list = array("InsuranceCardsID"=>array("HOUSEHOLD ID")
					,"Name"=>array("NAME")
					,"DateofBirth"=>array("DN")
					,"Sex"=>array("SEX")
					,"FamilyCategory"=>array("Categ")
					,"FamilyCode"=>array("Chef de famille")
					,"VillageName"=>array("VILLAGE")
					,"CellName"=>array("CELLULE")
					,"SectorName"=>array("Secteur")
					);
	$header_row = "S/N";
} elseif($insu == "RSSB RAMA"){
	$list = array("InsuranceCardsID"=>array("BENEFICIARY'S AFFILIATION NUMBER")
					,"Name"=>array("BENEFICIARY'S NAMES")
					,"DateofBirth"=>array("BENEFICIARY'S AGE")
					,"Sex"=>array("BENEFICIARY'S SEX")
					,"AffiliateName"=>array("AFFILIATE'S NAMES")
					,"Affectation"=>array("AFFILIATE'S AFFECTATION")
					,"VillageName"=>array("SECTION")
					,"CellName"=>array("CELLULE")
					,"SectorName"=>array("SECTOR")
					);
	$header_row = "BENEFICIARY'S AFFILIATION NUMBER";
}
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
	for ($row = 1; $row <= $highestRow; $row++){ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
										NULL,
										TRUE,
										FALSE);
		
		//echo "<pre>";var_dump($rowData); echo "<hr>";
		//break;
		$error_field = array();
		if($rowData[0][0] == $header_row){
			//echo "Header found";
			//now map all fields to their collesponding location
			foreach($rowData[0] as $index=>$fld_label){
				//loop all references 
				foreach($list as $fld=>$alt){
					foreach($alt as $al){
						if(strtolower($al) == strtolower($fld_label)){
							$address[$fld] = $index;
							break;
						}
					}
					/* if(@$address[$fld] == NULL)
						$error_field[] = $fld; */
					//var_dump(@$address[$fld]);
				}
				
				if(!in_array($index,$address) && $index != $header_row)
					$error_field[] = $fld_label;
			}
			//var_dump($address);
			continue;
		}
		//var_dump($address);
		//check if all required field are found
		if(count($address) != count($list)){
			echo "<span class=error-text>Some Field Are Missing<br />";
			foreach($error_field as $e)
				echo $e." ;";
			echo count($error_field)>0?"<br />Unnecessary field found<br />":"";
			echo "<br />Check Their Spelling Please!</span>";
			die;
		}
		//echo "<pre>";var_dump($rowData); echo "<hr>";die;
		//now build the the sector query if available
		$district_id = returnSingleField($sql="SELECT * FROM ad_district WHERE DistrictName='Huye'",$field="DistrictID",$data=true, $con);
		
		$sector_id = returnSingleField($sql="SELECT * FROM ad_sector WHERE SectorName='".PDB(ucfirst($rowData[0][$address["SectorName"]]),true,$con)."' && DistrictID='{$district_id}'",$field="SectorID",$data=true, $con);
		if(!$sector_id)
			$sector_id = saveAndReturnID("INSERT INTO ad_sector SET SectorName='".PDB(ucfirst($rowData[0][$address["SectorName"]]),true,$con)."', DistrictID='{$district_id}'", $con);
			
		$cell_id = returnSingleField($sql="SELECT * FROM ad_cell WHERE CellName='".PDB(ucfirst($rowData[0][$address["CellName"]]),true,$con)."' && SectorID='{$sector_id}'",$field="CellID",$data=true, $con);
		if(!$cell_id)
			$cell_id = saveAndReturnID("INSERT INTO ad_cell SET CellName='".PDB(ucfirst($rowData[0][$address["CellName"]]),true,$con)."', SectorID='{$sector_id}'", $con);
			
		$village_id = returnSingleField($sql="SELECT * FROM ad_village WHERE VillageName='".PDB(ucfirst($rowData[0][$address["VillageName"]]),true,$con)."' && CellID='{$cell_id}'",$field="ViillageID",$data=true, $con);
		if(!$village_id)
			$village_id = saveAndReturnID("INSERT INTO ad_village SET VillageName='".PDB(ucfirst($rowData[0][$address["VillageName"]]),true,$con)."', CellID='{$cell_id}'", $con);
			//return;
		//echo $village_id;
		//var_dump();
		if(preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#",$rowData[0][$address['DateofBirth']])){
			$d = explode("/",$rowData[0][$address['DateofBirth']]);
			$rowData[0][$address['DateofBirth']] = $d[2]."-".$d[1]."-".$d[0];
		}
		if(is_float($rowData[0][$address['DateofBirth']])){
			//$displayDate = PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][$address['DateofBirth']], 'YYYY-MM-DD');
			$rowData[0][$address['DateofBirth']] = PHPExcel_Style_NumberFormat::toFormattedString($rowData[0][$address['DateofBirth']], 'YYYY-MM-DD');
			//echo  $displayDate."<==".$rowData[0][$address['DateofBirth']]; echo "<br />";
		}
		//echo date("m/d/Y",40116);
		//var_dump($rowData[0][$address['DateofBirth']]);
		if($rowData[0][$address['DateofBirth']] && !preg_match("/^[0-9]{4}/",$rowData[0][$address['DateofBirth']])){
			//echo "<span class=error-text>Invalid Birth date format{$rowData[0][$address['DateofBirth']]}</span>";
			;//'continue;
		} else if(preg_match("/^[0-9]{4}$/",$rowData[0][$address['DateofBirth']])){
			$rowData[0][$address['DateofBirth']] = $rowData[0][$address['DateofBirth']]."-01-01";
		}
		
		if(!$rowData[0][$address['DateofBirth']] || trim($rowData[0][$address['DateofBirth']]) == "-")
			$rowData[0][$address['DateofBirth']] = "0000-00-00";
		
		//die;
		if($rowData[0][$address['DateofBirth']] > date("Y-m-d",time())){
			//echo "<span class=error-text>Invalid Birth date {$rowData[0][$address['DateofBirth']]}</span>";
			$rowData[0][count($rowData[0])] = "Invalid Birth date";
			$conflict[($sheets_error++)][($error_counter++)] = $rowData[0];
			continue;
		}
		
		//echo $rowData[0][$address['DateofBirth']];
		//check if the patient exist and return his information to the receptionis
		$sql = "SELECT PatientID FROM pa_info WHERE Name='{$rowData[0][$address['Name']]}' && 
				DateofBirth='{$rowData[0][$address['DateofBirth']]}' && FamilyCode='{$rowData[0][$address['FamilyCode']]}' && VillageID='{$village_id}'";
		//save patient information
		//echo $sql;
		if(!$patient_id = returnSingleField($sql,$field="PatientID",$data=true, $con)){
			$sql = "INSERT INTO pa_info SET Name='{$rowData[0][$address['Name']]}', FamilyCode='', VillageID='{$village_id}', 
					DateofBirth='{$rowData[0][$address['DateofBirth']]}', Sex='{$rowData[0][$address['Sex']]}'";
			//echo $sql;
			if($insu == "RSSB RAMA")
				$sql .= ", AffiliateName='{$rowData[0][$address['AffiliateName']]}'
						, Affectation='{$rowData[0][$address['Affectation']]}'";
			
			if(!$patient_id = saveAndReturnID($sql, $con)){
				//echo "<span class=error-text><br />Unable to save Patient {$sql}</span>";
				$rowData[0][count($rowData[0])] = "Error When Saving";
				$conflict[($sheets_error++)][($error_counter++)] = $rowData[0];
				continue;
			}
		} else{
			saveData($sql="UPDATE pa_info SET Name='{$rowData[0][$address['Name']]}', 
							DateofBirth='{$rowData[0][$address['DateofBirth']]}', FamilyCode='{$rowData[0][$address['FamilyCode']]}', Sex='{$rowData[0][$address['Sex']]}', VillageID='{$village_id}' WHERE PatientID='{$patient_id}'", $con);
							
		}
		
		
		if($patient_id){
			//generate the document id now
			$docid = generateDocID($_POST['insurance_id']);
			$sql = "INSERT INTO pa_records SET DocID='{$docid}',
					PatientID='{$patient_id}',
					InsuranceNameID='{$_POST['insurance_id']}',
					InsuranceCardID='{$rowData[0][$address['InsuranceCardsID']]}',
					FamilyCategory='{$rowData[0][$address['FamilyCategory']]}',
					Weight='',
					Temperature='',
					DateIn='0000-00-00',
					Status=1,
					ReceptionistID='{$_SESSION['user']['UserID']}'";
					
			if(($record = returnSingleField("SELECT PatientRecordID FROM pa_records WHERE Status=1 && InsuranceCardID='{$rowData[0][$address['InsuranceCardsID']]}'",$field="PatientRecordID",$data=true, $con))|| ($record = saveAndReturnID($sql, $con))){
				;//$error = "<span class=success><b>New Patient Received <a href='../forms/".returnSingleField($sql="SELECT in_forms.* FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$_POST['insurance']}'","FormFile",$data=true, $con)."?records=".$record."' target='_blank'>Print</a></b></span>";
				//header("Location:);
				//return;
			} 
			//save in pa_insurance_cards
			$sql = "INSERT INTO pa_insurance_cards SET 
					PatientID='{$patient_id}',
					InsuranceNameID='{$_POST['insurance_id']}',
					InsuranceCardsID='{$rowData[0][$address['InsuranceCardsID']]}',
					Date=NOW(),
					Status=1";
			if(!returnSingleField("SELECT PatientInsuranceCardsID FROM pa_insurance_cards WHERE Status=1 && InsuranceCardsID='{$rowData[0][$address['InsuranceCardsID']]}' && Date='".date("Y-m-d",time())."}'",$field="PatientInsuranceCardsID",$data=true, $con) && saveData("UPDATE pa_insurance_cards SET Status=0 WHERE PatientID='{$patient_id}'", $con))	
				$record = saveAndReturnID($sql, $con);
			
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