<?php
session_start();
//var_dump($_SESSION);
$path = "";
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
require_once "../lib/config.php";
$db = new DBConnector();
/* echo "<pre />";
var_dump($_POST); die; */
$error = "";
//var_dump($_POST); echo "<br /><br />"; die;
if(@$_POST['print_bill']){
	//save the consultation if not the second time it comes
	$cons_type = mysql_real_escape_string(trim($_POST['consultation']));
	
	$patientid = mysql_real_escape_string(trim($_POST['patientid'])); //get the document id
	//echo $patientid."<br />";
	//var_dump($_POST['consultationexistbefore']);
	
	//check if the patient has any consultation assigned to this document
	$consid = returnSingleField($sql="SELECT ConsultationRecordID from co_records WHERE PatientRecordID='{$patientid}' && Date='{$_POST['consultation_date']}'", $field="ConsultationRecordID",$data=true, $con);
	/* echo $consid."<br />";
	echo $sql;
	die; */
	if(@$_POST['consultationexistbefore']){
		//update all information on the existing consultation now
		$sql = "UPDATE co_records SET 
				Date='{$_POST['consultation_date']}',
				ConsultationPriceID='".PDB($_POST['consultation'],true,$con)."',
				ConsultantID='',
				RegisterNumber='{$_POST['register_id']}'
				WHERE ConsultationRecordID='{$_POST['consultationexistbefore']}'";
		//echo $sql;
		//save and return the consultation id
		saveData($sql, $con);
		$consid = $_POST['consultationexistbefore'];
		
	} else if(!$consid){
		//save the consultation record now
		$sql = "INSERT INTO co_records SET PatientRecordID='{$patientid}',
				Date='{$_POST['consultation_date']}',
				ConsultationPriceID='".PDB($_POST['consultation'],true,$con)."',
				ConsultantID='',
				RegisterNumber='{$_POST['register_id']}'";
		//echo $sql;
		//save and return the consultation id
		$consid = saveAndReturnID($sql, $con);
	} else{
		$sql = "UPDATE co_records SET 
				ConsultationPriceID='".PDB($_POST['consultation'],true,$con)."',
				RegisterNumber='{$_POST['register_id']}'
				WHERE ConsultationRecordID='{$consid}'";
		//echo $sql;
		//save and return the consultation id
		$consid = saveData($sql, $con);
	}
	//echo $consid;
	//continue saving other information
	//echo $consid;
	//var_dump($_POST); die;
	//isolate every field type
	$exams = array(); $exam_counter=-1; $exams_updates = array();
	$medecines = array(); $medecine_counter=-1; $medecines_updates = array();
	//loop all element of the the post array searching for special fields
	foreach($_POST as $key=>$value){
		if(preg_match("/^exam/",$key)){
			if(preg_match("/^examdate/",$key)){
				$exam_counter++;
			}
			if(preg_match("/^examexistbefore/",$key) && $value ){
				$exams[$exam_counter][$key] = PDB($value,true,$con);
				$exams_updates[] = $exams[$exam_counter];
				unset($exams[$exam_counter]);
				continue;
				
			} else if(preg_match("/^examexistbefore/",$key)){
				continue;
			}
			
			$exams[$exam_counter][$key] = PDB($value,true,$con);
			
		}
		
		
		if(preg_match("/^medecine/",$key)){
			if(preg_match("/^medecinedate/",$key)){
				$medecine_counter++;
			}
			if(preg_match("/^medecineexistbefore/",$key) && $value ){
				$medecines[$medecine_counter][$key] = PDB($value,true,$con);//1
				$medecines_updates[] = $medecines[$medecine_counter];
				unset($medecines[$medecine_counter]);
				continue;
			} else if(preg_match("/^medecineexistbefore/",$key)){
				continue;
			}
			$medecines[$medecine_counter][$key] = PDB($value,true,$con);//1
		}
	}
	
	/******************** Simulation area *****************************************/
	
	//var_dump($acts_updates);
	//var_dump($acts);
	//die;
	/* var_dump($exams); 
	echo "<br /><br />&nbsp;";
	var_dump($medecines); 
	var_dump($acts); 
	var_dump($consumables);
	
	die; */
	//$consumables[0]=array("consumablename1"=>"GANT STERILE (paire)","consumablequantity1"=>"1");
	//$exams[1]=$exams[0];
	//$exams[2]=$exams[0];
	//$acts[0]=array("act1"=>"Injection IV");
	/*$_POST['hospitalization_room_type'] = 1;
	$_POST['hospitalizationdays'] = 3;
	$_POST['ambulancelength'] = 3;
	$_POST['ambulancemedical_document'] = "on"; */
	/******************** End of Simulation area *****************************************/
	
	//var_dump($exams);echo "<br /><br /><br />";
	//var_dump($medecines);echo "<br /><br /><br />";
	//var_dump($consumables);echo "<br /><br /><br />";
	//var_dump($acts);echo "<br /><br /><br />";
	//die;
	
	
	//search for the patient insurance category id
	//echo $patientid;
	//select insurance name id
	$ins_name_id = returnSingleField($sql="SELECT InsuranceNameID FROM pa_records WHERE PatientRecordID='{$patientid}'",$field="InsuranceNameID",$data=true, $con);
	$ins_cat_id = returnSingleField($sql="SELECT CategoryID FROM in_name WHERE InsuranceNameID='{$ins_name_id}'",$field="CategoryID",$data=true, $con);
	//echo $ins_cat_id; //die;
	
	//now save found exams and continue
	//var_dump($exams);
	foreach($exams as $exam){
		//save one exam records
		$skip = false;
		//var_dump($exam);
		//check data validity
		$sql = "INSERT INTO la_records SET ";
		//echo $sql . "<br />";
		$sql2 = "INSERT INTO la_result_record SET ";
		$f = 0; $examid=null; $ff = 0;
		$ad_result = array(); $price_id = 0; $ex_id_ = 0; $date="";
		foreach($exam as $key=>$value){
			/* if(preg_match("/^examexistbefore/",$key))
				continue; */
			//var_dump($skip); echo " ";
			if(preg_match("/^examresult/",$key)){
				if($ff++ != 0)
					$sql2 .= ", ";
			}
			else if($f++ != 0)
				$sql .= ", ";
			//echo "One Exam Found"; var_dump($skip);
			//echo $key."==>".$value."<br />";
			
			if (preg_match("/^examdate/",$key)){
				if($value){
					$sql .= "ResultDate='{$value}'";
					//echo $sql."<br />";
					$date = $value;
				}
			}
			elseif(preg_match("/^examresult/",$key)){
				//select the exam price id based on the found name
				//echo $value;
				
				$rsss = explode(";",trim($value));
				//var_dump($rsss);
				foreach($rsss as $rssss){
					if($rssss){
						if($exam_id = returnSingleField($s = "SELECT ResultID FROM la_result WHERE ResultName='{$rssss}' && ExamID='{$ex_id_}'",$field="ResultID",$data=true, $con)){
							$ad_result[] = $exam_id;
						} else if($exam_id = saveAndReturnID("INSERT INTO la_result SET ResultName='{$rssss}', ExamID='{$ex_id_}'",$con)){
							$ad_result[] = $exam_id;
						}
					}
				}
				//$price_id = returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$exam_id}' && InsuranceTypeID='{$ins_cat_id}'",$field="ExamPriceID",$data=true, $con);
				//var_dump($ad_result);
				//echo $s;
				if(!count($ad_result)){
					$skip = true;
					break;
				}
				//echo "Not Skiped"; var_dump($skip);
				//$sql .= "ExamPriceID='{$price_id}'";
				//$sql2 .= "ResultID='{$exam_id}'";
			}
			elseif(preg_match("/^examid/",$key)){
				if($force_field_update){
					$force_field_update = false;
					saveData("ALTER TABLE `la_records` CHANGE `ExamNumber` `ExamNumber` VARCHAR(255) NOT NULL",$con);
				}
				if($value){
					
					$vl = (substr($date,0,4)).$quaters[(substr($date,5,2))]/* .$value */;
					$sql .= "QuarterID='".insertOrReturnID("INSERT INTO la_quarters SET QuarterName='{$vl}'","SELECT * FROM la_quarters WHERE QuarterName='{$vl}'","QuarterID",$con)."', ";
					$sql .= "ResultNumber='{$value}', ";
					$sql .= "ExamNumber='{$value}'";
					//echo $sql .= "<br />";
					$examid = $value;
				} else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^examname/",$key)){
				//select the exam price id based on the found name
				$ex_id_ = $exam_id = returnSingleField("SELECT ExamID FROM la_exam WHERE ExamName='{$value}'",$field="ExamID",$data=true, $con);
				$price_id = returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$exam_id}' && Amount >= 0 && InsuranceTypeID='{$ins_cat_id}' && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="ExamPriceID",$data=true, $con);
				//echo $price_id;
				//var_dump($exam_id);
				if(!$price_id){
					$skip = true;
					break;
				}
				//echo "not breaked";
				$sql .= "ExamPriceID='{$price_id}'";
				//echo $sql ."<br />";
			}
		}
		//var_dump($skip);
		if($skip || returnSingleField($ds = "SELECT ExamRecordID FROM la_records, la_quarters  WHERE ExamNumber='{$examid}' && ExamPriceID='{$price_id}' && la_quarters.QuarterID = la_records.QuarterID && la_quarters.QuarterName='".(substr($date,0,4).$quaters[substr($date,5,2)])."' ",$field="ExamRecordID",$data=true, $con))
			continue;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsultantID=0,
				LabAgent=0";
		//echo $ds; 
		//echo $sql; 
		//die;
		//save the new exam records now and continue;
		$examrecordid = saveAndReturnID($sql, $con);
		//saveData($sql, $con);
		//echo $sql."<br /><br />";
		//$sql2 .= ", ExamRecordID='{$examrecordid}'";
		foreach($ad_result as $rs_)
			saveData("INSERT INTO la_result_record SET ResultID='{$rs_}', ExamRecordID='{$examrecordid}'", $con);
		//echo $sql2."<br /><br />";
	}
	/* echo "stoped!";
	die;
	 */
	//now save found exams and continue
	//echo "<pre>"; var_dump($exams_updates);
	foreach($exams_updates as $exam){
		//save one exam records
		$skip = false;
		//var_dump($exam);
		//check data validity
		$sql = "UPDATE la_records SET ";
		//echo $sql . "<br />";
		$sql2 = "UPDATE la_result_record SET ";
		$f = 0; $examid=null; $ff = 0;
		$ad_result = array(); $price_id = 0; $ex_id_ = 0; $date=""; $exam_record_id="";
		foreach($exam as $key=>$value){
			//echo $key."===>".$value;
			if(preg_match("/^examexistbefore/",$key)){
				//var_dump($key);
				$exam_record_id = $value;
				continue;
			} 
			if(preg_match("/^examresult/",$key)){
				if($ff++ != 0)
					$sql2 .= ", ";
			}
			else if($f++ != 0)
				$sql .= ", ";
			//echo "One Exam Found"; var_dump($skip);
			//echo $key."==>".$value."<br />";
			
			if (preg_match("/^examdate/",$key)){
				$sql .= "ResultDate='{$value}'";
				//echo $sql."<br />";
				$date = $value;
			}
			elseif(preg_match("/^examresult/",$key)){
				//select the exam price id based on the found name
				//echo $value;
				
				$rsss = explode(";",trim($value));
				//var_dump($rsss);
				foreach($rsss as $rssss){
					if($rssss){
						if($exam_id = returnSingleField($s = "SELECT ResultID FROM la_result WHERE ResultName='".trim($rssss)."' && ExamID='{$ex_id_}'",$field="ResultID",$data=true, $con)){
							$ad_result[] = $exam_id;
						} else if($exam_id = saveAndReturnID("INSERT INTO la_result SET ResultName='".trim($rssss)."', ExamID='{$ex_id_}'",$con)){
							$ad_result[] = $exam_id;
						}
					}
				}
				//$price_id = returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$exam_id}' && InsuranceTypeID='{$ins_cat_id}'",$field="ExamPriceID",$data=true, $con);
				//var_dump($ad_result);
				//echo $s;
				if(!count($ad_result)){
					//delete the exam because all result are deleted now
					$_sql_ = "DELETE FROM la_result_record WHERE ExamRecordID='".($_POST["examexistbefore".substr($key,8)])."'";
					saveData($_sql_,$con);
					
					$_sql_ = "DELETE FROM la_records WHERE ExamRecordID='".($_POST["examexistbefore".substr($key,8)])."'";
					saveData($_sql_,$con);
					
					$skip = true;
					break;
				}
				//echo "Not Skiped"; var_dump($skip);
				//$sql .= "ExamPriceID='{$price_id}'";
				//$sql2 .= "ResultID='{$exam_id}'";
			}
			elseif(preg_match("/^examid/",$key)){
			
				if($force_field_update){
					$force_field_update = false;
					saveData("ALTER TABLE `la_records` CHANGE `ExamNumber` `ExamNumber` VARCHAR(255) NOT NULL",$con);
				}
				
				if($value){
					$vl = (substr($date,0,4)).$quaters[(substr($date,5,2))]/* .$value */;
					$sql .= "QuarterID='".insertOrReturnID("INSERT INTO la_quarters SET QuarterName='{$vl}'","SELECT * FROM la_quarters WHERE QuarterName='{$vl}'","QuarterID",$con)."', ";
					$sql .= "ResultNumber='{$value}', ";
					$sql .= "ExamNumber='{$value}'";
					//echo $sql .= "<br />";
					$examid = $value;
				}else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^examname/",$key)){
				//select the exam price id based on the found name
				$ex_id_ = $exam_id = returnSingleField("SELECT ExamID FROM la_exam WHERE ExamName='{$value}'",$field="ExamID",$data=true, $con);
				$price_id = returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$exam_id}' && Amount >= 0 && InsuranceTypeID='{$ins_cat_id}' && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="ExamPriceID",$data=true, $con);
				//echo $price_id;
				//var_dump($exam_id);
				if(!$price_id){
					$skip = true;
					//now search for the existing exam record id$_sql_ = "DELETE FROM la_result_record WHERE ExamRecordID='{$exam_record_id}'";
					$_sql_ = "DELETE FROM la_result_record WHERE ExamRecordID='".($_POST["examexistbefore".substr($key,8)])."'";
					saveData($_sql_,$con);
					
					$_sql_ = "DELETE FROM la_records WHERE ExamRecordID='".($_POST["examexistbefore".substr($key,8)])."'";
					saveData($_sql_,$con);
					
					//echo $_sql_;
					//echo ."<br />";
					break;
				}
				//echo "not breaked";
				$sql .= "ExamPriceID='{$price_id}'";
				//echo $sql ."<br />";
			}
		}
		//var_dump($skip);
		if($skip){
			//now delete this record from the system it is no longer valid exam record
			/* $_sql_ = "DELETE FROM la_result_record WHERE ExamRecordID='{$exam_record_id}'";
			//saveData($_sql_,$con);
			echo $_sql_; */
			continue;
		}
		//die;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsultantID=0,
				LabAgent=0 WHERE ExamRecordID='{$exam_record_id}'";
		//echo $sql; die;
		//delete all result to allow new record to recorded
		$_sql_ = "DELETE FROM la_result_record WHERE ExamRecordID='{$exam_record_id}'";
		//echo $_sql_;// die;
		//save the new exam records now and continue;
		saveData($_sql_, $con);
		saveData($sql, $con);
		$examrecordid = $exam_record_id;
		//saveData($sql, $con);
		//echo $sql."<br /><br />";
		//$sql2 .= ", ExamRecordID='{$examrecordid}'";
		foreach($ad_result as $rs_)
			saveData("INSERT INTO la_result_record SET ResultID='{$rs_}', ExamRecordID='{$examrecordid}'", $con);
		//echo $sql2."<br /><br />";
	}
	/************* MEDICINES HERE ************************/
	//echo "<pre>";//var_dump($medecines);
	$emballage = 0;
	$md_stock_id = 0;
	$md_stock_quantity = 0;
	//now save found medicines and continue
	foreach($medecines as $exam){
		//save one exam records
		//var_dump($exam); die;
		$skip = false;
		//check data validity
		$sql = "INSERT INTO md_records SET ";
		$sql2 = "INSERT INTO md_prescription SET ";
		$f = 0; $p = 0; $examid=null; $presc_found=false; $price_id=0; $qty=0; $date="";
		foreach($exam as $key=>$value){
			//echo $key."=>".$value."<br />";
			if(preg_match("/pres_/",$key)){
				if($p++ != 0)
					$sql2 .= ", ";
			}
			else if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^medecinepres_morng/",$key)){
				$sql2 .= "Morning='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_noon/",$key)){
				$sql2 .= "Noon='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_evening/",$key)){
				$sql2 .= "Evening='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_night/",$key)){
				$sql2 .= "Midnight='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinespecial/",$key)){
				$sql .= "SpecialPrescription='{$value}'";
				if($value){
					$presc_found = true;
				}
			} elseif(preg_match("/^medecinequantity/",$key)){
				if($value >0 ){
					$sql .= "Quantity='{$value}'";
					$md_stock_quantity = $qty = $value;
				} else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^medecinedate/",$key)){
				if($value >0 ){
					$sql .= "Date='{$value}'";
					$date = $value;
				} else{
					//echo "Now Skipped!";
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^medecinename/",$key)){
				//select the exam price id based on the found name
				//$value=PDB($value,true,$con);
				$emba = returnSingleField("SELECT Emballage FROM md_name WHERE MedecineName='{$value}'",$field="Emballage",$data=true, $con);
				$md_stock_id = $exam_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$exam_id}' && Amount >= 0 && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
				$emba = returnSingleField($emba_sql = "SELECT Emballage FROM md_price WHERE MedecinePriceID='{$price_id}'",$field="Emballage",$data=true, $con);
				//var_dump($price_id);
				//echo $emba_sql;// die;
				//force the coartem quantity to be one anywhere
				$_POST['medecinequantity'.(substr($key,12))] = preg_match("/coartem/",strtolower($value))?1:$_POST['medecinequantity'.(substr($key,12))];
				/* echo $sql;
				die; */
				if(!$price_id){
					$skip = true;
					break;
				}
				$sql .= "MedecinePriceID='{$price_id}'";
				if($emba)
					$emballage++;
			}
			
		}
		
		//echo $sql; var_dump($skip); die;
		if($skip)
			continue;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsulatantID=0";
		/* //verify the if a medecine has the prescription found
		if(!$presc_found){
			continue;
		} */
		//echo $sql; die;
		
		//save the new medicine records now and continue;
		if(!$md_id = returnSingleField("SELECT MedecineRecordID FROM md_records WHERE MedecinePriceID='{$price_id}' && Quantity='{$qty}' && ConsultationRecordID='{$consid}'",$field="MedecineRecordID",$data=true, $con)){
			$md_id = saveAndReturnID($sql, $con);
			//update the stock status now
			if(!$md_stock_data_id = returnSingleField("SELECT MedicineStockID FROM md_stock WHERE MedicineNameID='{$md_stock_id}'","MedicineStockID",true,$con))
				$md_stock_data_id = saveAndReturnID("INSERT INTO md_stock SET MedicineNameID='{$md_stock_id}', Quantity=0, Date='".time()."'",$con);
			$stock_level = returnSingleField("SELECT Quantity FROM md_stock WHERE MedicineNameID='{$md_stock_id}'","Quantity",true,$con);
			
			saveData("UPDATE md_stock SET Quantity = Quantity - {$md_stock_quantity} WHERE MedicineStockID='{$md_stock_data_id}'",$con);
			//save in records table
			saveData("INSERT INTO md_stock_records SET MedicineStockID='{$md_stock_data_id}', Operation='DISTRIBUTION', StockLevel='{$stock_level}', Quantity='{$md_stock_quantity}', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status='1'",$con);
		}
		/* $sql2 .= ", MedecineRecordID='{$md_id}'";
		//echo $sql."<br /><br />";
		//save the medecine prescription because now no error in the query
		if($md_id && !returnSingleField("SELECT MedecineRecordID FROM  md_prescription WHERE MedecineRecordID='{$md_id}'",$field="MedecineRecordID",$data=true, $con))
			saveData($sql2, $con); //echo $sql2."<br /><br />"; */
	}
	
	
	//echo "<pre>";//var_dump($medecines_updates);
	//echo "<pre>";var_dump($medecines);
	//$emballage = 0;
	//now save found medecines and continue
	foreach($medecines_updates as $exam){
		//save one exam records
		//var_dump($exam); die;
		$skip = false;
		//check data validity
		$sql = "UPDATE md_records SET ";
		$sql2 = "INSERT INTO md_prescription SET ";
		$f = 0; $p = 0; $examid=null; $presc_found=false; $price_id=0; $qty=0; $date="";
		$medecine_record_id = "";
		foreach($exam as $key=>$value){
			//echo $key."=>".$value."<br />";
			if(preg_match("/pres_/",$key)){
				if($p++ != 0)
					$sql2 .= ", ";
			}
			else if(!preg_match("/^medecineexistbefore/",$key) && $f++ != 0)
				$sql .= ", ";
			
			if(preg_match("/^medecineexistbefore/",$key)){
				$medecine_record_id = $value;
			}
			else if(preg_match("/^medecinepres_morng/",$key)){
				$sql2 .= "Morning='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_noon/",$key)){
				$sql2 .= "Noon='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_evening/",$key)){
				$sql2 .= "Evening='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinepres_night/",$key)){
				$sql2 .= "Midnight='{$value}'";
				if($value){
					$presc_found = true;
				}
			}
			elseif(preg_match("/^medecinespecial/",$key)){
				$sql .= "SpecialPrescription='{$value}'";
				if($value){
					$presc_found = true;
				}
			} elseif(preg_match("/^medecinequantity/",$key)){
				if($value >0 ){
					$sql .= "Quantity='{$value}'";
					$qty = $value;
				} else{
					//delete the medicine record because no quantity
					$_sql_ = "DELETE FROM md_records WHERE MedecineRecordID='".($_POST["medecineexistbefore".substr($key,16)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^medecinedate/",$key)){
				if($value >0 ){
					$sql .= "Date='{$value}'";
					$date = $value;
				} else{
					//echo "Now Skipped!";
					$_sql_ = "DELETE FROM md_records WHERE MedecineRecordID='".($_POST["medecineexistbefore".substr($key,12)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^medecinename/",$key)){
				//select the exam price id based on the found name
				//$value=PDB($value,true,$con);
				$exam_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$exam_id}' && Amount >= 0 && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
				$emba = returnSingleField($emba_sql = "SELECT Emballage FROM md_price WHERE MedecinePriceID='{$price_id}'",$field="Emballage",$data=true, $con);
				//echo $emba_sql;// die;
				//var_dump($price_id);
				//force the coartem quantity to be one anywhere
				$_POST['medecinequantity'.(substr($key,12))] = preg_match("/coartem/",strtolower($value))?1:$_POST['medecinequantity'.(substr($key,12))];
				/* echo $sql;
				die; */
				if(!$price_id){
					$_sql_ = "DELETE FROM md_records WHERE MedecineRecordID='".($_POST["medecineexistbefore".substr($key,12)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					$skip = true;
					break;
				}
				$sql .= "MedecinePriceID='{$price_id}'";
				if($emba)
					$emballage++;
			}
			echo "<br />";
		}
		
		//echo $sql; var_dump($skip); die;
		if($skip)
			continue;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsulatantID=0 WHERE MedecineRecordID='{$medecine_record_id}'";
		/* //verify the if a medecine has the prescription found
		if(!$presc_found){
			continue;
		} */
		//echo $sql."<br />";// die;
		
		//save the new medecine records now and continue;
		saveData($sql, $con);
			
			
		/* $sql2 .= ", MedecineRecordID='{$md_id}'";
		//echo $sql."<br /><br />";
		//save the medecine prescription because now no error in the query
		if($md_id && !returnSingleField("SELECT MedecineRecordID FROM  md_prescription WHERE MedecineRecordID='{$md_id}'",$field="MedecineRecordID",$data=true, $con))
			saveData($sql2, $con); //echo $sql2."<br /><br />"; */
	}
	//die;
	
	
	/************* END OF MEDICINES ************************/
	
	//select the content registered exams
	$records = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.Name, co_records.ConsultationRecordID, pa_records.* FROM pa_records, pa_info, co_records WHERE pa_info.PatientID = pa_records.PatientID && co_records.PatientRecordID = pa_records.PatientRecordID && pa_records.PatientRecordID='{$patientid}'",$con),$multirows=false,$con);
	//var_dump($records);
	//select all exam records related the found patient
	$la_records = formatResultSet($rslt=returnResultSet($sql = "SELECT la_exam.ExamName, la_records.ExamRecordID, la_records.ExamNumber, la_records.ResultNumber  FROM la_records, la_price, la_exam WHERE la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ConsultationRecordID = '{$records['ConsultationRecordID']}'",$con),$multirows=true,$con);
	//var_dump($la_records);
	//build the saved information log
	$data = "Name: <b>{$records['Name']}</b><br />Received on: <b>{$records['DateIn']}</b><br />";
	$k = 0;
	if($la_records){
		$data .= "<u>Exam List</u><br />";
		foreach($la_records as $record){
			$data .= ++$k .". ".$record['ResultNumber']." {$record['ExamName']}";
			//select result related to the current exam
			$result_records = formatResultSet($rslt=returnResultSet($sql = "SELECT la_result.ResultName FROM la_result_record, la_result WHERE la_result_record.ResultID = la_result.ResultID && la_result_record.ExamRecordID = '{$record['ExamRecordID']}'",$con),$multirows=true,$con);
			if($result_records){
				$kk = 0;
				$data .= " <span class=error-text>";
				foreach($result_records as $result){
					$data .= ($kk++ > 0?"; ":" ");
					$data .= "{$result['ResultName']}";
				}
				$data .= "</span> ";
			}
			$data .= "<br />";
		}
		
	}
	$error = "<span class='success' ><img src='../images/favour1.png' style='float:left; margin-right:5px; ' />{$data}<br />If Error Click <label onclick='receivePatient(\"{$records["PatientRecordID"]}\",\"{$records["DocID"]}\"); $(\".frm_out\").html(\"\");' style='color:blue; text-decoration:underline; cursor:pointer' title='Click to modify Laboratory Information for {$records['Name']}'>HERE</label> and Fill  Correct Information!</span>";
	
}
echo $error;
?>