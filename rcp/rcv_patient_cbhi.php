<?php
session_start(); 
//var_dump($_SESSION); die;

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

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
		// echo $ins; die;
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
			}
		}

		if($patient_id){
			$_POST['patient_id'] = $patient_id;
		}
		/* check if the category is there for CBHI */
		if($ins == "CBHI" && !$_POST['fcategory']){
			echo "<span class=error-text>Enter Family Category</span>";
			return;
		}
		//var_dump($_POST['tm']); die;
		if(is_null(@$_POST['tm'])){
			echo "<span class=error-text>TM is it Paid or Not?</span>";
			return;
		} else if($_POST['tm'] == 200 && !trim($_POST['receipt_number'])){
			echo "<span class=error-text>Enter Receipt Number Please!</span>";
			return;
		}
		
		
		if(!@$_POST['service']){
			echo "<span class=error-text>Select Service</span>";
			return;
		}

		if(!@$_POST['patient_id']){
			echo "<span class=error-text>The Patient is not register!<br /></span>";
			return;
		}
		
		$save_in_insurance = false;
		if( !$card_id = returnSingleField($sql="SELECT * FROM pa_insurance_cards WHERE InsuranceNameID='{$_POST['insurance']}' && InsuranceCardsID='{$_POST['card_id']}' && Status=1",$field="PatientInsuranceCardsID",$data=true, $con)){
			/* to be incommented when the list of all possible patient is loaded otherwise this lock new patient to be recorded in the system */
			/* 
			echo "<span class=error-text>Invalid Insurance Card Found</span>";
			return; */
			//return;
			//try to save this card info if doesn't exist before
			$save_in_insurance = true;
		}

		/* Check if the service will be available the current patient */
		$inNameID = $_POST['insurance'];
		$seNameD  = $_POST['service'];
		$log = array();

		$ConsultationPriceID = autoConsulatationPricing($inNameID, $seNameD, $log);
		// die("OK ".$ConsultationPriceID);
		if(!$ConsultationPriceID){
			echo "<span class=error-text>No Consulation is available for current service<br />{$log['msg']}</span>";
			return;
		}
		/*var_dump($log);
		die;*/
		
		$patient_id = returnSingleField($sql="SELECT * FROM pa_info WHERE PatientID='{$_POST['patient_id']}'",$field="PatientID",$data=true, $con);
		// var_dump($_POST); die();
		if($save_in_insurance){
			saveData("INSERT INTO pa_insurance_cards SET PatientID='{$patient_id}', InsuranceNameID='{$_POST['insurance']}', InsuranceCardsID='{$_POST['card_id']}', Status=1".($ins == "RSSB RAMA"?" ,AffiliateNumber='{$_POST['fcategory']}', AffiliateName='{$_POST['father']}', Affectation='{$_POST['location']}'":"").($ins == "MMI"?", AffiliateName='{$_POST['father']}'":""),$con);
		}
		// die($patient_id);
		if($patient_id){
			if($record = returnSingleField("SELECT PatientRecordID FROM pa_records WHERE Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$field="PatientRecordID",$data=true, $con)){
				
				//generate the document id now
				$returnSql = "";
				$docid = generateDocID($_POST['insurance'], $returnSql, str_replace("-","",$_POST['date']));
				$documentNumber = (int)substr($docid, 9); //preg_replace("/^\d{8}A/", "", $docid);
				// preg_replace(pattern, replacement, subject);

				// var_dump($docid, $documentNumber, $returnSql); die();
				$currentMonth = date("Y-m", time());
				$currentDate = date("Y-m-d", time());
				$currentSec	 = time();

				$sql = "SELECT 	MAX(a.cbhiMonthlyID) AS maxID
								FROM pa_records AS a
								WHERE a.DateIn LIKE('{$currentMonth}%')
								";
				$cbhiMonthlyID = returnSingleField($sql,$field="maxID",true, $con);
				$cbhiMonthlyID++;
				/*if($currentDate < "2018-12-01"){
					$cbhiMonthlyID = 'NULL';
				}*/

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

				saveData($sql = "UPDATE pa_records 
										SET cbhiMonthlyID={$cbhiMonthlyID},
											monthlyID={$monthlyID},
											dailyID = {$dailyID},
											DocID = '{$docid}',
											DocNumber = {$documentNumber},
											VillageID='{$village_id}',
											ReceptionistID='{$_SESSION['user']['UserID']}'
										WHERE  Status=0 && DateIn='{$_POST['date']}' && PatientRecordID='{$record}'",$con);

				saveData($returnSql, $con);
				if($_POST['service']){
					$serviceRecordID = returnSingleField("SELECT a.ServiceRecordID FROM se_records AS a WHERE a.PatientRecordID = '{$record}'","ServiceRecordID",true, $con);
					if(!$serviceRecordID){
						saveAndReturnID("INSERT INTO se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}'", $con);
					} else{
						saveData("UPDATE se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}' WHERE ServiceRecordID='{$serviceRecordID}'", $con);
					}

					//check if the service 
					// echo "Here!"; die();
					$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.ServiceCode='PST'",$con),$multirows=false,$con);
					if($service['ServiceNameID'] == $_POST['service']){
						//now save data related to the pst service
						saveData("INSERT INTO pst_records SET PatientID='{$patient_id}', Frequency='{$_POST['next_frequency']}', DocIDs='{$_POST['document_list']}{$docid}'",$con);
					}

					// Check TM information
					$tm_private = NULL;
					$tmID = returnSingleField("SELECT a.TicketID FROM mu_tm AS a WHERE a.PatientRecordID='{$record}'", "TicketID", true, $con);
					
					$tmAmount = ($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])));
					$additionalField = ($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"");
					if($_POST['fcategory'] == 1){
						$tmAmount = 0;
						$additionalField = ", Type='INDIGENT', status=1";
					}
					if(!$tmID){
						saveAndReturnID($stm = "INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='{$tmAmount}', ReceiptNumber='{$_POST['receipt_number']}'{$additionalField}, Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
					} else{
						saveData("UPDATE mu_tm SET TicketPaid='{$tmAmount}', ReceiptNumber='{$_POST['receipt_number']}'{$additionalField}, Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}' WHERE TicketID='{$tmID}'",$con);
					}
					

					/*From Here find the correct consultation to be used*/
					$inNameID = $_POST['insurance'];
					$seNameD  = $_POST['service'];

					$consultations = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																					c.ConsultationCategoryName AS ConsultationCategoryName,
																					d.ConsultationPriceID AS ConsultationPriceID,
																					d.Amount AS Amount
																					FROM se_name AS a
																					INNER JOIN se_consultation AS b
																					ON a.ServiceNameID = b.ServiceID
																					INNER JOIN co_category AS c
																					ON b.ConsulationID = c.ConsultationCategoryID
																					INNER JOIN co_price AS d
																					ON c.ConsultationCategoryID = d.ConsultationCategoryID
																					INNER JOIN in_category AS e
																					ON d.InsuranceCategoryID = e.InsuranceCategoryID
																					INNER JOIN in_name AS f
																					ON e.InsuranceCategoryID = f.CategoryID
																					WHERE f.InsuranceNameID = '{$inNameID}' && 
																						  a.ServiceNameID = '{$seNameD}' && 
																						  d.Status = 1 && 
																						  b.Status = 1 &&
																						  c.ConsultationCategoryName = '{$dayDefaultConsultation}'
																					", $con), true, $con);
					if($ConsultationPriceID) {
						// Here Please register the patient consulation record from here
						$co_record = formatResultSet($rslt=returnResultSet("SELECT a.*
																					FROM co_records AS a
																					WHERE a.PatientRecordID = '{$record}'
																					",$con),$multirows=false,$con);
						// var_dump($record);
						// $consultationAmount = $consultations[0]['Amount'];
						if(count($co_record) == 0){
							// Create one
							// $ConsultationPriceID = $ConsultationPriceID;
							saveData("INSERT INTO co_records SET PatientRecordID='{$record}', Date=NOW(), ConsultationPriceID='{$ConsultationPriceID}', ConsultantID=0, RegisterNumber=''", $con);
						} else{
							// Update the Existing Record
							saveData("UPDATE co_records SET ConsultationPriceID='{$ConsultationPriceID}' WHERE ConsultationRecordID='{$co_record['ConsultationRecordID']}'",$con);
						}
						// echo $stm;

						// Here check if the prestation is free or is payable
						$currentDateFiche = date("Y-m-d", time());
						$activeDateFiche = returnSingleField("SELECT Date FROM sy_tarif WHERE Date <= '{$currentDateFiche}' ORDER BY Date DESC LIMIT 0, 1", "Date", true, $con);

						$fichePriceID  = returnSingleField("SELECT 	a.TarifID
																	FROM sy_tarif AS a
																	INNER JOIN sy_product AS b
																	ON a.ProductID = b.ProductID
																	WHERE a.InsuranceNameID = '{$inNameID}' &&
																		  b.ProductName = '{$ficheName}' &&
																		  a.Date = '{$activeDateFiche}'
																		  ","TarifID",true,$con);
						if($fichePriceID){
							// No the prestation document is not free so add it to the patient bill
							$fichecRecordId = returnSingleField("SELECT id FROM sy_records WHERE PatientRecordID={$record} && ProductPriceID={$fichePriceID}", "id", true, $con);
							if(!$fichecRecordId){
								saveData("INSERT INTO sy_records SET PatientRecordID={$record}, ProductPriceID={$fichePriceID}, PerformedOn={$currentSec}, receivedBy={$_SESSION['user']['UserID']}", $con);
							} else {
								saveData("UPDATE sy_records SET PatientRecordID={$record}, ProductPriceID={$fichePriceID}, PerformedOn={$currentSec}, receivedBy={$_SESSION['user']['UserID']} WHERE id={$fichecRecordId}", $con);
							}
						}
					} else{
						// Here Print the error Message that the consultation could not be detected and ask the user to add it manualy
					}
					
				}

				$error = "<span class=success><b><a id=print_file_now href='../receipt/?records=".$record."'>
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
					setTimeout('click_now()',100);
				</script>
SCRIPT;
			}
		}
	}
}

echo $error;
?>