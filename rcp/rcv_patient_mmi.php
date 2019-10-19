<?php
session_start(); 
//var_dump($_SESSION); die;

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);die;
$error = "";
if(@$_POST['rcv_patient_btn']){
	//check if the date in is valid
	$_GET['key'] 	= $_POST['patient_id'];
	$_POST['age'] 	= preg_match("/^[0-9]{4}$/",$_POST['age'])?$_POST['age']."-01-01":$_POST['age'];
	$phonenumber 	= '';
	// var_dump($_POST); // die;
	$_POST['age'] = !$_POST['age']?"0000-00-00":$_POST['age'];
	if(preg_match("/^[0-9]{2}$/",$_POST['age'])){
		$_POST['age'] = date("Y",time()) - $_POST['age']."-01-01";
	}

	if(preg_match("/^[0-9]{2}$/",$_POST['location'])){
		$_POST['location'] = date("Y",time()) - $_POST['location']."-01-01";
	}

	// var_dump($_POST['location']);

	if(date("Y-m-d",time()) < $_POST['age']){
		$error = "<span class=error-text>Invalid Birth Date for patient</span>";
		
	} else if(date("Y-m-d",time()) < $_POST['location']){
		$error = "<span class=error-text>Invalid Birth Date for affiliate</span>";
		
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
		//var_dump($village_id); die;
		//echo $_POST['insurance'];
		$ins = returnSingleField($sql="SELECT * FROM in_name WHERE InsuranceNameID='{$_POST['insurance']}'",$field="InsuranceName",$data=true, $con);
		//save patient information
		$card_id = 0;
		//echo $ins; die;
		
		//var_dump($_POST); die;
		if(!@$_POST['sex']){
			echo "<span class=error-text>Select Patient Gender</span>";
			return;
		} else if(!@$_POST['affsex']){
			echo "<span class=error-text>Select Affiliate Gender</span>";
			return;
		}
		//var_dump($_POST); die;
		if(!@$_POST['service']){
			echo "<span class=error-text>Select Service</span>";
			return;
		}
		
		$save_in_insurance = false;
		if($ins != "Private" && !$_POST['card_id']){
			echo "<span class=error-text>Enter Insurance Card ID</span>";
			return;
		} elseif( !$card_id = returnSingleField($sql="SELECT * FROM pa_insurance_cards WHERE InsuranceNameID='{$_POST['insurance']}' && InsuranceCardsID='{$_POST['card_id']}' && Status=1",$field="PatientInsuranceCardsID",$data=true, $con)){
			/* to be incommented when the list of all possible patient is loaded otherwise this lock new patient to be recorded in the system */
			/* 
			echo "<span class=error-text>Invalid Insurance Card Found</span>";
			return; */
			//return;
			//try to save this card info if dos not exist before
			$save_in_insurance = true;
		}
		/* Check if the service will be available the current patient */
		$inNameID = $_POST['insurance'];
		$seNameD  = $_POST['service'];
		$log = array();
		$ConsultationPriceID = autoConsulatationPricing($inNameID, $seNameD, $log);
		if(!$ConsultationPriceID){
			echo "<span class=error-text>No Consulation is available for current service<br />{$log['msg']}</span>";
			return;
		}
		/*var_dump($log, $ConsultationPriceID);
		die;*/
		//var_dump($_POST); die;
		if(!$_POST['name']){
			echo "<span class=error-text>Enter Patient Name</span>";
			return;
		}
		$_POST['father'] = mysql_real_escape_string(trim($_POST['father']));
		$phoneNumber = preg_match("/^07[2,3,8]{1}\d{7}/", @$_POST['phonenumber'])?$_POST['phonenumber']:"";
		if($ins == "MMI"){
			//the MMI information is now available
			// var_dump($_POST);
			if(!$_POST['father']){
				echo "<span class=error-text>Enter Affiliate Name</span>";
				return;
			} else if(!$_POST['fcategory']){
				echo "<span class=error-text>Enter Affiliate Number</span>";
				return;
			}

			// Check if the affiliate info are registered before
			$affiliate_id = returnSingleField($sql="SELECT * FROM pa_info AS a WHERE a.FamilyCode='{$_POST['fcategory']}' && a.AffiliateName='@1'",$field="PatientID",$data=true, $con);
			// echo $sql;
			if(!$affiliate_id){

				saveData("INSERT INTO pa_info SET Name='{$_POST['father']}', FamilyCode='{$_POST['fcategory']}', VillageID='{$village_id}', 
								DateofBirth='{$_POST['location']}', Sex='{$_POST['affsex']}', AffiliateName='@1'",$con);
			} else {
				saveData($sql = "UPDATE pa_info SET Name='{$_POST['father']}', FamilyCode='{$_POST['fcategory']}', VillageID='{$village_id}', 
								DateofBirth='{$_POST['location']}', Sex='{$_POST['affsex']}', AffiliateName='@1'
								WHERE PatientID='{$affiliate_id}'",$con);
				// echo $sql;
			}
			// var_dump($affiliate_id);
			// die; 
		}

		$patient_id = returnSingleField($sql="SELECT * FROM pa_info WHERE PatientID='{$_POST['patient_id']}'",$field="PatientID",$data=true, $con);
		if(!$patient_id){
			//return;
			$_POST['name'] = mysql_real_escape_string(trim($_POST['name']));
			$_POST['father'] = mysql_real_escape_string(trim($_POST['father']));
			$father = "";
			if($ins == "MMI"){
				$father = $_POST['father'];
				$_POST['father'] = $_POST['fcategory'];
			}
			//var_dump($_POST);
			if(!$_POST['age']){
				$_POST['age'] = "0000-00-00";
			} 
			if(!preg_match("/^[0-9]{4}/",$_POST['age'])){
				echo "<span class=error-text>Invalid Birth date format{$_POST['age']}</span>";
				return;
			} else{
				//check if the patient exist and return his information to the receptionis
				$sql = "SELECT PatientID FROM pa_info WHERE Name='{$_POST['name']}' && FamilyCode='{$_POST['father']}' && 
						DateofBirth='{$_POST['age']}' && VillageID='{$village_id}'";
				if(!$patient_id = returnSingleField($sql,$field="PatientID",$data=true, $con)){
					$sql = "INSERT INTO pa_info SET Name='{$_POST['name']}', FamilyCode='{$_POST['father']}', VillageID='{$village_id}', 
							DateofBirth='{$_POST['age']}', Sex='{$_POST['sex']}'";
					// echo $sql; die;
					if(!$patient_id = saveAndReturnID($sql, $con)){
						$error = "<span class=error-text><br />Unable to save Patient {$sql}</span>";
						
					}
				}
				//die;
				if($ins == "MMI"){
					//var_dump($_POST);// die;
					if(!$_POST['location']){
						$_POST['location'] = "0000-00-00";
					}
					if(!@$_POST['affsex']){
						$_POST['affsex'] = "";
					}
					if(!preg_match("/^[0-9]{4}/",$_POST['location'])){
						echo "<span class=error-text>Invalid Age Format {$_POST['location']} For Affiliate</span>";
						return;
					}
					//check if all information are about the affiliate are in the system otherwise save them
					$sql_aff = "SELECT PatientID FROM pa_info WHERE Name='{$father}' && FamilyCode='{$_POST['father']}' && 
							DateofBirth='{$_POST['location']}' && VillageID='{$village_id}' && AffiliateName='@1'";
					//echo $sql_aff;// die;
					//echo "HEY!";
					if(!returnSingleField($sql_aff,$field="PatientID",$data=true, $con)){
						$phoneNumber = preg_match("/^07[2,3,8]{1}\d{7}/", @$_POST['phonenumber'])?$_POST['phonenumber']:"";
						$sql_ = "INSERT INTO pa_info SET Name='{$father}', FamilyCode='{$_POST['father']}', VillageID='{$village_id}', 
								DateofBirth='{$_POST['location']}', Sex='{$_POST['affsex']}', AffiliateName='@1', phoneNumber='{$phoneNumber}'";
						//echo $sql_;
						saveAndReturnID($sql_, $con);
					}
					//die;
				}
					
			}
		}
		if($save_in_insurance){
			saveData("INSERT INTO pa_insurance_cards SET PatientID='{$patient_id}', InsuranceNameID='{$_POST['insurance']}', InsuranceCardsID='{$_POST['card_id']}', Status=1".($ins == "RSSB RAMA"?" ,AffiliateNumber='{$_POST['fcategory']}', AffiliateName='{$_POST['father']}', Affectation='{$_POST['location']}'":"").($ins == "MMI"?", AffiliateName='{$_POST['father']}'":""),$con);
		}
		//var_dump($_POST); die;
		if(@$_POST['update'] && $_POST['patient_id']){
			$phoneNumber = preg_match("/^07[2,3,8]{1}\d{7}/", @$_POST['phoneNumber'])?$_POST['phoneNumber']:"";
			saveData($sql="UPDATE pa_info SET Name='{$_POST['name']}', 
						DateofBirth='{$_POST['age']}', FamilyCode='{$_POST['father']}', Sex='{$_POST['sex']}', VillageID='{$village_id}', phoneNumber='{$phoneNumber}' WHERE PatientID='{$patient_id}'", $con);
			//check if is the RSSB RAMA INSURANCE AND UPDATE SOME FILES
			
							
		}
		
		if($patient_id){
			$record = returnSingleField("SELECT PatientRecordID FROM pa_records WHERE Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$field="PatientRecordID",$data=true, $con);
			if(!$record){
				
				//generate the document id now
				$rtnSQL = "";
				$docid = generateDocID($_POST['insurance'], $rtnSQL, str_replace("-","",$_POST['date']));
				$documentNumber = (int)substr($docid, 9);//str_replace()
				//str_replace()
				// var_dump($rtnSQL, $documentNumber); die();

				$currentMonth = date("Y-m", time());
				$currentDate = date("Y-m-d", time());
				$currentSec	 = time();

				$sql = "SELECT 	MAX(a.monthlyID) AS maxID
								FROM pa_records AS a
								WHERE a.DateIn LIKE('{$currentMonth}%')
								";
				$monthlyID = returnSingleField($sql,$field="maxID",true, $con);
				$monthlyID++;
				// $monthlyID = NULL;
				$sql = "SELECT 	MAX(a.dailyID) AS maxID
								FROM pa_records AS a
								WHERE a.DateIn = '{$currentDate}'
								";
				$dailyID = returnSingleField($sql,$field="maxID",true, $con);
				$dailyID++;
				
				$sql = "INSERT INTO pa_records SET 
						PatientID='{$patient_id}',
						monthlyID={$monthlyID},
						dailyID = {$dailyID},
						DocID = '{$docid}',
						DocNumber = {$documentNumber},

						InsuranceNameID='{$_POST['insurance']}',
						InsuranceCardID='{$_POST['card_id']}',

						DateIn='{$_POST['date']}',
						Status=0,
						VillageID='{$village_id}',
						ReceptionistID='{$_SESSION['user']['UserID']}',
						TimeIn = '".(time())."'";
				// var_dump($sql); die();
				if(@$_POST['DailyNumberID']){
					$sql .= ", serialNumber = '{$_POST['DailyNumberID']}'";
				}
		
				$sql .=  ", FamilyCategory='{$_POST['fcategory']}'";
				
				
				$record = saveAndReturnID($sql, $con);
				//save the service
				saveData($rtnSQL,$con);
				/* var_dump($record);
				echo $sql;
				die; */
				if($_POST['service']){
					$serviceRecordID = returnSingleField("SELECT a.ServiceRecordID FROM se_records AS a WHERE a.PatientRecordID = '{$record}'","",true, $con);
					if(!$serviceRecordID){
						saveAndReturnID("INSERT INTO se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}'", $con);
					} else{
						saveData("UPDATE se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}' WHERE ServiceRecordID='{$serviceRecordID}'", $con);
					}
					//check if the service 
					$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.ServiceCode='PST'",$con),$multirows=false,$con);
					if($service['ServiceNameID'] == $_POST['service']){
						//now save data related to the pst service
						saveData("INSERT INTO pst_records SET PatientID='{$patient_id}', Frequency='{$_POST['next_frequency']}', DocIDs='{$_POST['document_list']}{$docid}'",$con);
					}

					$inNameID = $_POST['insurance'];
					$seNameD  = $_POST['service'];

					if($ConsultationPriceID){
						// Here Please register the patient consulation record from here
						$co_record = formatResultSet($rslt=returnResultSet("SELECT a.*
																				FROM co_records AS a
																				WHERE a.PatientRecordID = '{$record}'
																				",$con),$multirows=false,$con);
						// var_dump($record);
						// $consultationAmount = $consultations[0]['Amount'];
						// $ConsultationPriceID = $ConsultationPriceID;
						if(count($co_record) == 0){
							// Create one
							// $ConsultationPriceID = $consultations[0]['ConsultationPriceID'];
							saveData("INSERT INTO co_records SET PatientRecordID='{$record}', Date=NOW(), ConsultationPriceID='{$ConsultationPriceID}', ConsultantID=0", $con);
						} else{
							// Update the Existing Record
							saveData("UPDATE co_records SET ConsultationPriceID='{$ConsultationPriceID}' WHERE ConsultationRecordID='{$co_record['ConsultationRecordID']}'",$con);
						}

						saveAndReturnID($stm = "INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='".(@$tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
					}
					
				}
				
				$error = "<span class=success><b><a id=print_file_now href='../receipt/?records=".$record."'>
				New Patient Received<br />Print</a></b></span>";
				$error .= "
				<script>
					function click_now(){
						try{
							$('#print_file_now')[0].click();
							$('.clear_cmd').html('');
						} catch(e){
							//console.log('Error!');
						}
					}
					$('#doc_search').focus();
					$('#doc_search').select();
					setTimeout('click_now()',100);
				</script>";
				//header("Location:);
				//return;
			} else{
				// die();

				saveData($sql = "UPDATE pa_records 
										SET InsuranceCardID='{$_POST['card_id']}',
											InsuranceNameID='{$_POST['insurance']}',
											VillageID='{$village_id}',
											ReceptionistID='{$_SESSION['user']['UserID']}'
										WHERE  Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$con);
				// var_dump($sql);

				
				if($_POST['service']){
					$serviceRecordID = returnSingleField("SELECT a.ServiceRecordID FROM se_records AS a WHERE a.PatientRecordID = '{$record}'","ServiceRecordID",true, $con);
					if(!$serviceRecordID){
						saveAndReturnID("INSERT INTO se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}'", $con);
					} else{
						saveData("UPDATE se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}' WHERE ServiceRecordID='{$serviceRecordID}'", $con);
					}

					$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.ServiceCode='PST'",$con),$multirows=false,$con);
					if($service['ServiceNameID'] == $_POST['service']){
						//now save data related to the pst service
						saveData("INSERT INTO pst_records SET PatientID='{$patient_id}', Frequency='{$_POST['next_frequency']}', DocIDs='{$_POST['document_list']}{$docid}'",$con);
					}

					/*From Here find the correct consultation to be used*/
					$inNameID = $_POST['insurance'];
					$seNameD  = $_POST['service'];

					if($ConsultationPriceID){
						// Here Please register the patient consulation record from here
						$co_record = formatResultSet($rslt=returnResultSet("SELECT a.*
																				FROM co_records AS a
																				WHERE a.PatientRecordID = '{$record}'
																				",$con),$multirows=false,$con);
						
						if(count($co_record) == 0){
							
							saveData("INSERT INTO co_records SET PatientRecordID='{$record}', Date=NOW(), ConsultationPriceID='{$ConsultationPriceID}', ConsultantID=0", $con);
						} else{
							// Update the Existing Record
							saveData("UPDATE co_records SET ConsultationPriceID='{$ConsultationPriceID}' WHERE ConsultationRecordID='{$co_record['ConsultationRecordID']}'",$con);
						}

						// Check TM information
						$tm_private = NULL;
						if($ins == "Private"){
							$tm_private = 0; //$consultationAmount;
							
						}
						$tmID = returnSingleField("SELECT a.TicketID FROM mu_tm AS a WHERE a.PatientRecordID='{$record}'", "TicketID", true, $con);
						if(!$tmID){
							saveAndReturnID($stm = "INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
						} else{
							saveData("UPDATE mu_tm SET TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}' WHERE TicketID='{$tmID}'",$con);
						}// echo $stm;
					} else{
						// Here Print the error MEssage that the consultation could not be detected and ask the user to add it manualy
					}
				}
				
				$error = "<span class=success><b><a id=print_file_now href='../receipt/?records=".$record."'>
				New Patient Received<br />Print</a></b></span>";
				// $sql;
				$error .= "
				<script>
					function click_now(){
						try{
							$('#print_file_now')[0].click();
							$('#reset_form').click();
							$('.clear_cmd').html('');
						} catch(e){
							//console.log('Error!'');
						}
					}
					$('#doc_search').focus();
					$('#doc_search').select();
					setTimeout('click_now()',100);
				</script>";
			}
		}
	}
}

echo $error;
?>