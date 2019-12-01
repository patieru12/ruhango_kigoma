<?php
session_start(); 
//var_dump($_SESSION); die;

require_once "../lib/db_function.php";
if("mut" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);die;
$error = "";
if(@$_POST['rcv_patient_btn']){
	//check if the date in is valid
	$_GET['key'] = $_POST['patient_id'];
	if(preg_match("/^[0-9]{4}$/",$_POST['age']) && ((date("Y", time()) - $_POST['age']) <= 5) ){
		echo "<span class=error-text>For Children under 5 years old<br />Please Enter the full date of birth</span>";
		return;
	}
	
	$_POST['age'] = preg_match("/^[0-9]{4}$/",$_POST['age'])?$_POST['age']."-01-01":$_POST['age'];
	//var_dump($_POST); die;
	$_POST['age'] = !$_POST['age']?"0000-00-00":$_POST['age'];
	if(preg_match("/^[0-9]{2}$/",$_POST['age'])){
		$_POST['age'] = date("Y",time()) - $_POST['age']."-01-01";
	}
	if(date("Y-m-d",time()) < $_POST['age']){
		$error = "<span class=error-text>Invalid Date</span>";
		
	} else if(!isDataExist($sql="SELECT in_name.* FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$_POST['insurance']}'", $con)){
		$error = "<span class=error-text>Unsupported Insurance </span>";
	} else{
		//var_dump($docid); die;
		//save the district
		$district_id = returnSingleField($sql="SELECT * FROM ad_district WHERE DistrictName='".ucfirst($_POST['district'])."'",$field="DistrictID",$data=true, $con);
		if(!$district_id)
			$district_id = saveAndReturnID("INSERT INTO ad_district SET DistrictName='".ucfirst($_POST['district'])."'", $con);
			
		$sector_id = returnSingleField($sql="SELECT * FROM ad_sector WHERE SectorName='".ucfirst($_POST['sector'])."' && DistrictID='{$district_id}'",$field="SectorID",$data=true, $con);
		if(!$sector_id)
			$sector_id = saveAndReturnID("INSERT INTO ad_sector SET SectorName='".ucfirst($_POST['sector'])."', DistrictID='{$district_id}'", $con);
			
		$cell_id = returnSingleField($sql="SELECT * FROM ad_cell WHERE CellName='".ucfirst($_POST['cell'])."' && SectorID='{$sector_id}'",$field="CellID",$data=true, $con);
		if(!$cell_id)
			$cell_id = saveAndReturnID("INSERT INTO ad_cell SET CellName='".ucfirst($_POST['cell'])."', SectorID='{$sector_id}'", $con);
			
		$village_id = returnSingleField($sql="SELECT * FROM ad_village WHERE VillageName='".ucfirst($_POST['village'])."' && CellID='{$cell_id}'",$field="ViillageID",$data=true, $con);
		if(!$village_id)
			$village_id = saveAndReturnID("INSERT INTO ad_village SET VillageName='".ucfirst($_POST['village'])."', CellID='{$cell_id}'", $con);
			//return;
		// var_dump($village_id); die;

		//echo $_POST['insurance'];
		$ins = returnSingleField($sql="SELECT * FROM in_name WHERE InsuranceNameID='{$_POST['insurance']}'",$field="InsuranceName",$data=true, $con);
		//save patient information
		$card_id = 0;
		//echo $ins; die;
		// var_dump($_POST);
		if(!trim($_POST['fatherID'])){
			echo "<span class=error-text>Enter Family Category</span>";
			return;
		}

		$fatherId = preg_replace("/[^0-9]/", "", $_POST['fatherID']);
		$invalidChars = preg_match("/[^0-9]/", $_POST['fatherID']);
		if(!in_array(strlen($fatherId), [16, 8])) {
			echo $invalidChars?"<span class=success>Invalid Character found in the Household Id and will be removed check you input please.</span><br />":"";
			echo "<span class=error-text>Please Household Id Number.</span>";
			return;
		}

		// Here Validate the ID Card Number
		// var_dump($_POST['id_card_']); die();
		$fieldToSaveIn = PDB($_POST['id_card_'], true, $con);
		if( $fieldToSaveIn === "InsuranceCardID"){
			//$_POST['card_id']
			$insuranceCardId = preg_replace("/[^0-9]/", "", $_POST['card_id']);
			$invalidChars = preg_match("/[^0-9]/", $_POST['card_id']);
			if(!in_array(strlen($insuranceCardId), [0,8,16])){
				echo $invalidChars?"<span class=success>Invalid Character found in the Patient Id and will be removed check you input please.</span><br />":"";
				echo "<span class=error-text>Please Sixteen Digits required for Patient Id Number.<br />If it is an application number please switch the button on top of the card input box</span>";
				return;
			}
		}
		/* check if the category is there for CBHI */
		if($ins == "CBHI" && !$_POST['fcategory']){
			echo "<span class=error-text>Enter Family Category</span>";
			return;
		}
		//var_dump($_POST['tm']); die;
		/*if(is_null(@$_POST['tm'])){
			echo "<span class=error-text>TM is Paid or Not</span>";
			return;
		} else if($_POST['tm'] == 200 && !trim($_POST['receipt_number'])){
			echo "<span class=error-text>Enter Receipt Number Please!</span>";
			return;
		}*/
		//var_dump($_POST); die;
		if(!@$_POST['sex']){
			echo "<span class=error-text>Select Patient Gender</span>";
			return;
		}
		
		
		$save_in_insurance = false;
		if( !$card_id = returnSingleField($sql="SELECT * FROM pa_insurance_cards WHERE InsuranceNameID='{$_POST['insurance']}' && InsuranceCardsID='{$_POST['card_id']}' && Status=1",$field="PatientInsuranceCardsID",$data=true, $con)){
			/* to be incommented when the list of all possible patient is loaded otherwise this lock new patient to be recorded in the system */
			/* 
			echo "<span class=error-text>Invalid Insurance Card Found</span>";
			return; */
			//return;
			//try to save this card info if dos not exist before
			$save_in_insurance = true;
		}
		//die;
		//var_dump($_POST); die;
		if(!$_POST['name']){
			echo "<span class=error-text>Enter Patient Name</span>";
			return;
		}
		if(!$_POST['fatherID']){
			echo "<span class=error-text>Enter House Manage Name</span>";
			return;
		}

		// var_dump($_POST); return;
		
		$patient_id = returnSingleField($sql="SELECT * FROM pa_info WHERE PatientID='{$_POST['patient_id']}'",$field="PatientID",$data=true, $con);
		
		if(!$patient_id){
			//return;
			$_POST['name'] = ucwords(mysql_real_escape_string(trim($_POST['name'])));
			$_POST['father'] = ucwords(mysql_real_escape_string(trim($_POST['father'])));
			$father = "";
			
			//var_dump($_POST);
			if(!$_POST['age']){
				$_POST['age'] = "0000-00-00";
			} 
			if(!preg_match("/^[0-9]{4}/",$_POST['age'])){
				echo "<span class=error-text>Invalid Birth date format{$_POST['age']}</span>";
				return;
			} else{
				$_POST['phoneNumber'] = PDB($_POST['phoneNumber'], true, $con);
				//check if the patient exist and return his information to the receptionis
				$sql = "SELECT PatientID FROM pa_info WHERE Name='{$_POST['name']}' && FamilyCode='{$_POST['father']}' && 
						DateofBirth='{$_POST['age']}' && VillageID='{$village_id}'";
				if(!$patient_id = returnSingleField($sql,$field="PatientID",$data=true, $con)){
					$sql = "INSERT INTO pa_info SET Name='{$_POST['name']}', FamilyCode='{$_POST['father']}', VillageID='{$village_id}', 
							DateofBirth='{$_POST['age']}', Sex='{$_POST['sex']}', phoneNumber='{$_POST['phoneNumber']}'";
					// echo $sql; die;
					if(!$patient_id = saveAndReturnID($sql, $con)){
						echo "<span class=error-text><br />Unable to save Patient {$sql}</span>";
						return;
					}
				}
					
			}
		} else {
			$_POST['phoneNumber'] = PDB($_POST['phoneNumber'], true, $con);
			$sql = "UPDATE pa_info SET Name='{$_POST['name']}', FamilyCode='{$_POST['father']}', 
										DateofBirth='{$_POST['age']}', Sex='{$_POST['sex']}', phoneNumber='{$_POST['phoneNumber']}' WHERE PatientID={$patient_id}";
			// echo $sql;
			saveData($sql, $con);
		}
		// return;
		if($save_in_insurance){
			saveData("INSERT INTO pa_insurance_cards SET PatientID='{$patient_id}', InsuranceNameID='{$_POST['insurance']}', InsuranceCardsID='{$_POST['card_id']}', Status=1".($ins == "RSSB RAMA"?" ,AffiliateNumber='{$_POST['fcategory']}', AffiliateName='{$_POST['father']}', Affectation='{$_POST['location']}'":"").($ins == "MMI"?", AffiliateName='{$_POST['father']}'":""),$con);
		}
		//var_dump($_POST); die;
		/*if(@$_POST['update'] && $_POST['patient_id']){
			$phoneNumber = preg_match("/^07[2,3,8]{1}\d{7}/", @$_POST['phonenumber'])?$_POST['phonenumber']:"";
			saveData($sql="UPDATE pa_info SET Name='{$_POST['name']}', 
						DateofBirth='{$_POST['age']}', FamilyCode='{$_POST['father']}', Sex='{$_POST['sex']}', VillageID='{$village_id}', phoneNumber='{$phoneNumber}' WHERE PatientID='{$patient_id}'", $con);
			//check if is the RSSB RAMA INSURANCE AND UPDATE SOME FILES
			
							
		}*/
		
		if($patient_id){
			if(!($record = returnSingleField("SELECT PatientRecordID FROM pa_records WHERE Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$field="PatientRecordID",$data=true, $con))){
				
				//generate the document id now
				$docid = "";
				$documentNumber = NULL; 

				$currentMonth = date("Y-m", time());
				$currentDate = date("Y-m-d", time());
				
				$monthlyID = NULL;
				
				$dailyID = NULL;
				//str_replace()
				$_POST['fatherID'] = PDB($_POST['fatherID'], true, $con);
				
				$sql = "INSERT INTO pa_records SET 	DocID='',
													PatientID='{$patient_id}',
													InsuranceNameID='{$_POST['insurance']}',
													`{$fieldToSaveIn}`='{$_POST['card_id']}',
													HouseManagerID='{$_POST['fatherID']}',
													DateIn='{$_POST['date']}',
													Status=0,
													VillageID='{$village_id}',
													ReceptionistID='0',
													cbhiAgent='{$_SESSION['user']['UserID']}',
													TimeIn = '".(time())."'";
		
				$sql .=  ", FamilyCategory='{$_POST['fcategory']}'";
				
				$record = saveAndReturnID($sql, $con);
				//save the service
				/* var_dump($record);
				echo $sql;
				die; */
				/*if(@$_POST['service']){
					// Get the Serive record if previously available
					$se_recordsid = returnSingleField("SELECT a.ServiceRecordID FROM se_records AS a WHERE a.PatientRecordID = '{$record}'","ServiceRecordID",true,$con);
					if(!$se_recordsid){
						saveAndReturnID("INSERT INTO se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}'", $con);
						//check if the service 
						$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.ServiceCode='PST'",$con),$multirows=false,$con);
						if($service['ServiceNameID'] == $_POST['service']){
							//now save data related to the pst service
							saveData("INSERT INTO pst_records SET PatientID='{$patient_id}', Frequency='{$_POST['next_frequency']}', DocIDs='{$_POST['document_list']}{$docid}'",$con);
						}
					} else {
						saveAndReturnID("UPDATE se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}' WHERE ServiceRecordID = '{$se_recordsid}'", $con);
					}
				}*/
				//if($_POST['tm'] > 0){
				//find the correct money to be paid if private
				$tm_private = null;
				/* if($ins == "Private"){
					$tm_private = 940;
				} */
				// saveAndReturnID("INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
				//}
				$error = "<span class=success><b><a id=print_file_now target='_blank' href='../forms/".returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='1'",$field="FormFile",true, $con)."?records={$record}'>
				New Patient Received<br />Print</a></b></span>";
				$error .= <<<SCRIPT
				<script>
					function click_now(){
						try{
							$('#print_file_now')[0].click();
							$('.clear_cmd').html('');
						} catch(e){
							//console.log("Error!");
						}
					}
					/*$("#doc_search").focus();
					$("#doc_search").select();*/
					// setTimeout('click_now()',100);
				</script>
SCRIPT;
				//header("Location:);
				//return;
			} else{
				// Check if the Patience has a document Record ID
				$docid = returnSingleField("SELECT DocID FROM pa_records WHERE Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$field="DocID",$data=true, $con);
				if(strlen($docid) <= 10){
					$docid = generateDocID($_POST['insurance'], str_replace("-","",$_POST['date']));
				}

				$documentNumber = preg_replace("/^\dA/", "", $docid);
				
				$_POST['fatherID'] = PDB($_POST['fatherID'], true, $con);
				saveData("UPDATE pa_records SET DocID='',
												InsuranceNameID='{$_POST['insurance']}',
												`{$fieldToSaveIn}`='{$_POST['card_id']}',
												HouseManagerID='{$_POST['fatherID']}',
												FamilyCategory='{$_POST['fcategory']}', 
												cbhiAgent='{$_SESSION['user']['UserID']}' 
												WHERE Status=0 && DateIn='{$_POST['date']}' && 
													  PatientID='{$patient_id}'
												",$con);
				$error = "<span class=success><b><a id=print_file_now target='_blank' href='../forms/".returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='1'",$field="FormFile",true, $con)."?records={$record}'>
				New Patient Received<br />Print</a></b></span>";
				$error .= <<<SCRIPT
				<script>
					function click_now(){
						try{
							$('#print_file_now')[0].click();
							$('#reset_form').click();
							$('.clear_cmd').html('');
						} catch(e){
							//console.log("Error!");
						}
					}
					$("#doc_search").focus();
					$("#doc_search").select();
					//setTimeout('click_now()',100);
				</script>
SCRIPT;
			}
		}
	}
}
echo $error;
?>