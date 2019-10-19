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
	$_GET['key'] = $_POST['patient_id'];
	$_POST['age'] = preg_match("/^[0-9]{4}$/",$_POST['age'])?$_POST['age']."-01-01":$_POST['age'];
	//var_dump($_POST); die;
	$_POST['age'] = !$_POST['age']?"0000-00-00":$_POST['age'];
	if(preg_match("/^[0-9]{2}$/",$_POST['age'])){
		$_POST['age'] = date("Y",time()) - $_POST['age']."-01-01";
	}
	if(date("Y-m-d",time()) < $_POST['age']){
		$error = "<span class=error-text>Invalid Date</span>";
		
	}  else if(!isDataExist($sql="SELECT in_name.* FROM in_name,in_category,in_price,in_forms WHERE in_name.CategoryID= in_category.InsuranceCategoryID && in_forms.InsuranceNameID = in_name.InsuranceNameID && in_price.InsuranceNameID = in_name.InsuranceNameID && in_name.InsuranceNameID='{$_POST['insurance']}'", $con)){
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
		//die;
		$inNameID = $_POST['insurance'];
		$seNameD  = $_POST['service'];
		$log = array();
		$ConsultationPriceID = autoConsulatationPricing($inNameID, $seNameD, $log);
		
		if(!$ConsultationPriceID){
			echo "<span class=error-text>No Consulation is available for current service<br />{$log['msg']}</span>";
			return;
		}
		//var_dump($_POST); die;
		if(!$_POST['name']){
			echo "<span class=error-text>Enter Patient Name</span>";
			return;
		}
		//validate the RSSB RAMA INFORMATION NOW
		if($ins == "RSSB RAMA"){
			//echo "RAMA IS FOUND"; die;
			if(!trim($_POST['father'])){
				echo "<span class=error-text>Enter Affiliate Name</span>";
				return;
			}
			if(!trim($_POST['fcategory'])){
				echo "<span class=error-text>Enter Affectation</span>";
				return;
			}
			if(!trim($_POST['location'])){
				echo "<span class=error-text>Enter Where Affiliate is Affected</span>";
				return;
			}
			//var_dump($_POST);
		} else if($ins == "MMI"){
			//the MMI information is now available
			/* var_dump($ins);
			die; */
		}
		// var_dump($_POST); die();
		$patient_id = returnSingleField($sql="SELECT * FROM pa_info WHERE PatientID='{$_POST['patient_id']}'",$field="PatientID",$data=true, $con);
		// var_dump($patient_id); die();
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
				/*var_dump($patient_id);
				die;*/
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
		// var_dump($_POST); die;
		if(@$_POST['update'] && $_POST['patient_id']){
			$phoneNumber = preg_match("/^07[2,3,8]{1}\d{7}/", @$_POST['phonenumber'])?$_POST['phonenumber']:"";
			saveData($sql="UPDATE pa_info SET Name='{$_POST['name']}', 
						DateofBirth='{$_POST['age']}', FamilyCode='{$_POST['father']}', Sex='{$_POST['sex']}', VillageID='{$village_id}', phoneNumber='{$phoneNumber}' WHERE PatientID='{$patient_id}'", $con);
			//check if is the RSSB RAMA INSURANCE AND UPDATE SOME FILES
			
							
		}
		if($ins == 'RSSB RAMA'){
			//check if the relation field Exist
			require_once"../lib/config.php";
			$db = new DBConnector();
			//var_dump($db);
			if(!$db->checkField($tbl="pa_insurance_cards",$field="Relation")){
				saveData("ALTER TABLE `pa_insurance_cards` ADD `Relation` VARCHAR(255) NOT NULL AFTER `Affectation`",$con);
			}
			/* var_dump($_POST);
			die; */
			//try to keep new information about the insurance cards
			saveData("UPDATE pa_insurance_cards SET AffiliateNumber='{$_POST['fcategory']}', AffiliateName='{$_POST['father']}', Affectation='{$_POST['location']}', `Relation`='{$_POST['rama_affiliate']}' WHERE PatientInsuranceCardsID='{$card_id}'",$con);
		}
		if($patient_id){
			// die();
			if(!($record = returnSingleField("SELECT PatientRecordID FROM pa_records WHERE Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$field="PatientRecordID",$data=true, $con))){
				
				//generate the document id now
				$rtnSQL = "";
				$docid = generateDocID($_POST['insurance'], $rtnSQL, str_replace("-","",$_POST['date']));
				$documentNumber = (int)substr($docid, 9);//str_replace()
				// var_dump($docid, $documentNumber, $returnSql); die();
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
				// var_dump($docid, $rtnSQL); die();
				
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
		
				if($ins == "RSSB RAMA"){
					$_POST['fcategory'] = PDB($_POST['fcategory'],true,$con);
					$_POST['location'] = PDB($_POST['location'],true,$con);
					$sql .=  ", FamilyCategory='0'";
					
					if(!returnSingleField("SELECT PatientInsuranceCardsID FROM pa_insurance_cards WHERE Status=1 && PatientID='{$patient_id}'",$field="PatientInsuranceCardsID",$data=true, $con)){
						$sql_for_cards = "INSERT INTO pa_insurance_cards SET 
									PatientID='{$patient_id}',
									InsuranceNameID='{$_POST['insurance']}',
									InsuranceCardsID='{$_POST['card_id']}',
									AffiliateNumber='{$_POST['fcategory']}',
									Affectation='{$_POST['location']}',
									Date='{$_POST['date']}',
									Status=1,
									AffiliateName='{$_POST['father']}'
									";
					//echo $sql_for_cards; die;
					saveData($sql_for_cards,$con);

					}
				} else{
					$sql .=  ", FamilyCategory='{$_POST['fcategory']}'";
				}
				
				
				$record = saveAndReturnID($sql, $con);
				//save the service
				saveData($rtnSQL, $con);
				 // var_dump($record);
				// echo $sql;
				// die; 
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

					// var_dump($inNameID, $seNameD);
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

					if($ConsultationPriceID){
						// Here Please register the patient consulation record from here
						$co_record = formatResultSet($rslt=returnResultSet("SELECT a.*
																				FROM co_records AS a
																				WHERE a.PatientRecordID = '{$record}'
																				",$con),$multirows=false,$con);
						// var_dump($record);
						$consultationAmount = $consultations[0]['Amount'];
						// $ConsultationPriceID = $consultations[0]['ConsultationPriceID'];
						if(count($co_record) == 0){
							// Create one
							// $ConsultationPriceID = $consultations[0]['ConsultationPriceID'];
							saveData("INSERT INTO co_records SET PatientRecordID='{$record}', Date=NOW(), ConsultationPriceID='{$ConsultationPriceID}', ConsultantID=0", $con);
						} else{
							// Update the Existing Record
							saveData("UPDATE co_records SET ConsultationPriceID='{$ConsultationPriceID}' WHERE ConsultationRecordID='{$co_record['ConsultationRecordID']}'",$con);
						}

						// Check TM information
						$tm_private = NULL;
						if($ins == "Private"){
							$tm_private = 0; //$consultationAmount;
							/*if(FICHE > 0){
								$tm_private += FICHE;
							}

							if(FACTURE > 0){
								$tm_private += FACTURE;
							}*/
						}
						saveAndReturnID($stm = "INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
						// echo $stm;
						if(strtolower($_POST['sex']) != "not specified"){
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
							$requestedServices = [];
							filterData($_POST, $requestedServices);
							foreach($requestedServices AS $key){
								$r = returnSingleField("SELECT * FROM sy_records WHERE PatientRecordID='{$record}' && ProductPriceID='{$key}'", "id", true, $con);
								if(!$r && is_numeric($key)){
									$time = time();
									saveData("INSERT INTO sy_records SET PatientRecordID='{$record}', ProductPriceID='{$key}', PerformedOn='{$time}', receivedBy='{$_SESSION['user']['UserID']}', status=0", $con);
								}
							}
						}
					} else{
						// Here Print the error MEssage that the consultation could not be detected and ask the user to add it manualy
					}
					
				}
				// die();
				//if($_POST['tm'] > 0){
				//find the correct money to be paid if private
				// $tm_private = null;
				/* if($ins == "Private"){
					$tm_private = 940;
				} */
				// saveAndReturnID("INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
				//}
				$error = "<span class=success><b>New Patient Received <br />Code:{$docid} <br /><a href='../receipt/?records=".$record.(strtolower($_POST['sex']) == "not specified"?"&print=".(hash('sha256',$record)):"")."' id='print_file_now'>Print</a></b></span>";
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
					$("#doc_search").focus();
					$("#doc_search").select();
					setTimeout('click_now()',100);
				</script>
SCRIPT;
				//header("Location:);
				//return;
			} else{
				// die();
				/*saveData($sql = "UPDATE pa_info 
										SET VillageID = '{$village_id}'
										WHERE PatientID='{$patient_id}'
										", $con);*/
				saveData($sql = "UPDATE pa_records 
										SET InsuranceCardID='{$_POST['card_id']}',
											InsuranceNameID='{$_POST['insurance']}',
											VillageID='{$village_id}',
											ReceptionistID='{$_SESSION['user']['UserID']}'
										WHERE  Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$con);


				if($_POST['service']){
					saveAndReturnID("INSERT INTO se_records SET PatientRecordID='{$record}', ServiceNameID='{$_POST['service']}', Date='{$_POST['date']}'", $con);
					//check if the service 
					$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.ServiceCode='PST'",$con),$multirows=false,$con);
					if($service['ServiceNameID'] == $_POST['service']){
						//now save data related to the pst service
						saveData("INSERT INTO pst_records SET PatientID='{$patient_id}', Frequency='{$_POST['next_frequency']}', DocIDs='{$_POST['document_list']}{$docid}'",$con);
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
					if(count($consultations) > 0){
						// Here Please register the patient consulation record from here
						$co_record = formatResultSet($rslt=returnResultSet("SELECT a.*
																				FROM co_records AS a
																				WHERE a.PatientRecordID = '{$record}'
																				",$con),$multirows=false,$con);
						// var_dump($record);
						// $consultationAmount = $consultations[0]['Amount'];
						// $ConsultationPriceID = $consultations[0]['ConsultationPriceID'];
						if(count($co_record) == 0){
							// Create one
							// $ConsultationPriceID = $consultations[0]['ConsultationPriceID'];
							saveData("INSERT INTO co_records SET PatientRecordID='{$record}', Date=NOW(), ConsultationPriceID='{$ConsultationPriceID}', ConsultantID=0", $con);
						} else{
							// Update the Existing Record
							saveData("UPDATE co_records SET ConsultationPriceID='{$ConsultationPriceID}' WHERE ConsultationRecordID='{$co_record['ConsultationRecordID']}'",$con);
						}

						// Check TM information
						$tm_private = NULL;
						if($ins == "Private"){
							$tm_private = 0; //$consultationAmount;
							/*if(FICHE > 0){
								$tm_private += FICHE;
							}

							if(FACTURE > 0){
								$tm_private += FACTURE;
							}*/
						}
						
						$tmID = returnSingleField("SELECT * FROM mu_tm WHERE PatientRecordID='{$record}'", "TicketID", true, $con);
						if(!$tmID){
							saveAndReturnID($stm = "INSERT INTO mu_tm SET PatientRecordID='{$record}', TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'", $con);
						} else{
							saveData($stm = "UPDATE mu_tm SET PatientRecordID='{$record}', TicketPaid='".($tm_private != null?$tm_private:($_POST['tm'] == "COMPASSION" || $_POST['tm'] == "DETTES"?200:($_POST['tm'] != 200 && $ins=="CBHI"?0:$_POST['tm'])))."', ReceiptNumber='{$_POST['receipt_number']}'".($_POST['tm'] != 200 && $ins=="CBHI"?", Type='{$_POST['tm']}'":"").", Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}' WHERE TicketPaid={$tmID}", $con);
						}
						// echo $stm;

						// Here check if the prestation is free or is payable
						$fichePriceID  = returnSingleField("SELECT 	a.TarifID
																	FROM sy_tarif AS a
																	INNER JOIN sy_product AS b
																	ON a.ProductID = b.ProductID
																	WHERE a.Date <= '{$currentDate}' &&
																		  a.InsuranceNameID = '{$inNameID}' &&
																		  b.ProductName = 'Fiche de Consultation'
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
						// Here Print the error MEssage that the consultation could not be detected and ask the user to add it manualy
					}
					
				}

				// saveData("UPDATE pa_records SET InsuranceCardID='{$_POST['card_id']}' WHERE  Status=0 && DateIn='{$_POST['date']}' && PatientID='{$patient_id}'",$con);
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