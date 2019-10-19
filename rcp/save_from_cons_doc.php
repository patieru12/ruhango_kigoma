<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
	$consid = returnSingleField($sql="SELECT ConsultationRecordID from co_records WHERE PatientRecordID='{$patientid}'", $field="ConsultationRecordID",$data=true, $con);
	//$consid = returnSingleField($sql="SELECT ConsultationRecordID from co_records WHERE PatientRecordID='{$patientid}' && ConsultationPriceID='{$cons_type}' && Date='{$_POST['consultation_date']}'", $field="ConsultationRecordID",$data=true, $con);
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
				RegisterNumber='{$_POST['register_id']}'
				WHERE ConsultationRecordID='{$consid}'";
		//echo $sql;
		//save and return the consultation id
		$consid = saveData($sql, $con);
	}
	//echo $consid;
	//save principal diagnostic if found
	if(@$_POST['principal_existbefore']){
		//now update the existing all deleted the information if ignored
		if(!@$_POST['principaldiagnostic']){
			saveData($sgg = "DELETE FROM co_diagnostic_records WHERE DiagnosticRecordID='{$_POST['principal_existbefore']}'",$con);
			//echo $sql;
		} else{
		
			$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['principaldiagnostic'],true,$con)."' && PECIME='{$_POST['pecime']}'",$field="DiagnosticID",$data=true, $con);
			saveData("UPDATE co_diagnostic_records SET DiagnosticID='{$diag_id}', DiagnosticType='1', CaseType='{$_POST['case']}', PECIME='{$_POST['pecime']}' WHERE DiagnosticRecordID='{$_POST['principal_existbefore']}'",$con);
		}
		//echo "RECEIVED!";
	} else if(@$_POST['principaldiagnostic']){
		//save the diagnostice if not saved
		$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['principaldiagnostic'],true,$con)."' && PECIME='{$_POST['pecime']}'",$field="DiagnosticID",$data=true, $con);
		
		if($diag_id && !returnSingleField($sql="SELECT DiagnosticID FROM co_diagnostic_records WHERE DiagnosticID='{$diag_id}' && ConsulationRecordID='{$consid}'",$field="DiagnosticID",$data=true, $con))
			saveData("INSERT INTO co_diagnostic_records SET ConsulationRecordID='{$consid}', DiagnosticID='{$diag_id}', DiagnosticType='1', CaseType='{$_POST['case']}', PECIME='{$_POST['pecime']}'", $con);
		//echo $sql;
	}
	
	//save secondary diagnostic if found
	if(@$_POST['secondary_existbefore']){
		//now update the existing all delete the information if ignored
		if(!@$_POST['secondarydiagnostic']){
			saveData($sql = "DELETE FROM co_diagnostic_records WHERE DiagnosticRecordID='{$_POST['secondary_existbefore']}'",$con);
			//echo $sql;
		} else{
		
			if(!$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['secondarydiagnostic'],true,$con)."' && PECIME='{$_POST['pecime']}'",$field="DiagnosticID",$data=true, $con)){
				$diag_id = saveAndReturnID($sql = "INSERT INTO co_diagnostic SET DiagnosticName='".PDB($_POST['secondarydiagnostic'],true,$con)."', Code='', PECIME='{$_POST['pecime']}', DiagnosticCategoryID='".returnSingleField("SELECT DiagnosticCategoryID FROM co_diagnostic_category WHERE CategoryName='Autres'","DiagnosticCategoryID",true,$con)."'",$con);
			}
			
			saveData("UPDATE co_diagnostic_records SET DiagnosticID='{$diag_id}', DiagnosticType='2', CaseType='{$_POST['case']}', PECIME='{$_POST['pecime']}' WHERE DiagnosticRecordID='{$_POST['secondary_existbefore']}'",$con);
		}
		//echo "RECEIVED!";
	} else if(@$_POST['secondarydiagnostic']){
		//save the diagnostice if not saved
		//$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['secondarydiagnostic'],true,$con)."'",$field="DiagnosticID",$data=true, $con);
		
		//save the diagnostice if not saved
		if(!$diag_id = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_POST['secondarydiagnostic'],true,$con)."' && PECIME='{$_POST['pecime']}'",$field="DiagnosticID",$data=true, $con)){
			$diag_id = saveAndReturnID($sql = "INSERT INTO co_diagnostic SET DiagnosticName='".PDB($_POST['secondarydiagnostic'],true,$con)."', Code='', PECIME='{$_POST['pecime']}', DiagnosticCategoryID='".returnSingleField("SELECT DiagnosticCategoryID FROM co_diagnostic_category WHERE CategoryName='Autres'","DiagnosticCategoryID",true,$con)."'",$con);
		}
		
		if($diag_id && !returnSingleField($sql="SELECT DiagnosticID FROM co_diagnostic_records WHERE DiagnosticID='{$diag_id}' && ConsulationRecordID='{$consid}'",$field="DiagnosticID",$data=true, $con))
			saveData("INSERT INTO co_diagnostic_records SET ConsulationRecordID='{$consid}', DiagnosticID='{$diag_id}', DiagnosticType='2', CaseType='{$_POST['case']}', PECIME='{$_POST['pecime']}'", $con);
	}
	
	//continue saving other information
	//echo $consid;
	//var_dump($_POST); die;
	
	//isolate every field type
	$diag_data = array(); $diag_counter=-1; $diag_updates = array();
	$exams = array(); $exam_counter=-1; $exams_updates = array();
	$medecines = array(); $medecine_counter=-1; $medecines_updates = array();
	$consumables = array(); $consumable_counter=-1; $consumables_updates = array();
	$acts = array(); $act_counter=-1; $acts_updates = array();
	//loop all element of the the post array searching for special fields
	
	foreach($_POST as $key=>$value){
		if(preg_match("/^diag/",$key)){
			if(preg_match("/^diagname/",$key)){
				$diag_counter++;
			}
			if(preg_match("/^diagexistbefore/",$key) && $value ){
				$diag_data[$diag_counter][$key] = PDB($value,true,$con);
				$diag_updates[] = $diag_data[$diag_counter];
				unset($diag_data[$diag_counter]);
				continue;
				
			} else if(preg_match("/^diagexistbefore/",$key)){
				continue;
			}
			
			$diag_data[$diag_counter][$key] = PDB($value,true,$con);
			
		}
		
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
		if(preg_match("/^consumable/",$key)){
			if(preg_match("/^consumabledate/",$key)){
				$consumable_counter++;
			}
			if(preg_match("/^consumableexistbefore/",$key) && $value ){
				$consumables[$consumable_counter][$key] = PDB($value,true,$con);
				$consumables_updates[] = $consumables[$consumable_counter];
				unset($consumables[$consumable_counter]);
				continue;
			} else if(preg_match("/^consumableexistbefore/",$key) ){
				
				continue;
			}
			
			$consumables[$consumable_counter][$key] = PDB($value,true,$con);
		}
		if(preg_match("/^act/",$key)){
			if(preg_match("/^actdate/",$key)){
				$act_counter++;
			}
			if(preg_match("/^actexistbefore/",$key) && $value ){
				$acts[$act_counter][$key] = PDB($value,true,$con);
				$acts_updates[] = $acts[$act_counter];
				unset($acts[$act_counter]);
				continue;
			} else if(preg_match("/^actexistbefore/",$key)){
				continue;
			}
			$acts[$act_counter][$key] = PDB($value,true,$con);
		}
	}
	
	/******************** Simulation area *****************************************/
	
	/* var_dump($diag_data);
	var_dump($diag_updates);
	die; */
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
	
	//now try to save the received diagnostic
	foreach($diag_data as $exam){
		$sql = "INSERT INTO co_diagnostic_records SET ";
		$break = false;
		foreach($exam as $key=>$value){
			//echo $sql .= "DiagnosticID={$value}";
			//select diagnostic id
			if(!$d_id = returnSingleField("SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($value,true,$con)."' && DiagnosticCode !=''",$field="DiagnosticID",$data=true, $con)){
				$break = true;
				break;
			}
			
			$sql .= "DiagnosticID='{$d_id}'";
		}
		if($break)
			continue;
		$sql .= ", ConsulationRecordID='{$consid}'";
		
		saveData($sql,$con);
	}
	//now try to save the received diagnostic updates
	//var_dump($_POST);
	foreach($diag_updates as $exam){
		$sql = "UPDATE co_diagnostic_records SET ConsulationRecordID='{$consid}', ";
		$break = false; $diag_r_id = 0;
		foreach($exam as $key=>$value){
			//echo $sql .= "DiagnosticID={$value}";
			if(preg_match("/^diagname/",$key)){
				//select diagnostic id
				if(!$d_id = returnSingleField("SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($value,true,$con)."' && DiagnosticCode !=''",$field="DiagnosticID",$data=true, $con)){
					//delete the request now
					$break = true;
					$diag_r_id = $_POST['diagexistbefore'.substr($key,8)];
					break;
				}
				$sql .= "DiagnosticID='{$d_id}'";
			} else if(preg_match("/^diagexistbefore/",$key)){
				//select diagnostic id
				$sql .= "WHERE DiagnosticRecordID='{$value}'";
			}
			
		}
		if($break){
			saveData($st= "DELETE FROM co_diagnostic_records WHERE DiagnosticRecordID='{$diag_r_id}'",$con);
			//echo $st;
			continue;
		}
		saveData($sql,$con);
	}
	//var_dump($diag_updates);
	//die;
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
				$sql .= "ResultDate='{$value}'";
				//echo $sql."<br />";
				$date = $value;
			}
			elseif(preg_match("/^examresult/",$key)){
				//select the exam price id based on the found name
				//echo $value;
				
				$rsss = explode("; ",trim($value));
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
		if($skip || returnSingleField("SELECT la_records.ExamRecordID FROM la_records, la_quarters WHERE ExamNumber='{$examid}' && ExamPriceID='{$price_id}' && la_quarters.QuarterID = la_records.QuarterID && la_quarters.QuarterName='".(substr($date,0,4).$quaters[substr($date,5,2)])."' ",$field="ExamRecordID",$data=true, $con))
			continue;
		$sql .= ", ConsultationRecordID='{$consid}',
				ConsultantID=0,
				LabAgent=0";
		
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
				
				$rsss = explode("; ",trim($value));
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
		//var_dump($skip); the exam number is used before
		if($skip ){
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
	/* echo "Exam Updated check now....<br />stoped!";
	die; */
	//exam well captured now try for medicines
	
	
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
		$medecine_record_id = ""; $old_quatity = 0;
		
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
				$old_quatity = returnSingleField("SELECT Quantity FROM md_records WHERE MedecineRecordID='{$value}'","Quantity",true,$con);
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
				$md_stock_id = $exam_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
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
		//restore the existing value now
		if(!$md_stock_id_update = returnSingleField("SELECT MedicineStockID FROM md_stock WHERE MedicineNameID='{$md_stock_id}'","MedicineStockID",true,$con))
			$md_stock_id_update = saveAndReturnID("INSERT INTO md_stock SET MedicineNameID='{$md_stock_id}', Quantity=0, Date='".time()."'",$con);
			
		//$md_stock_id_update = returnSingleField("SELECT MedicineStockID FROM md_stock WHERE MedicineNameID='{$md_stock_id}'","MedicineStockID",true,$con);
		$stock_level_updates = returnSingleField("SELECT Quantity FROM md_stock WHERE MedicineStockID='{$md_stock_id_update}'","Quantity",true,$con);
		
		//restore the value
		saveData("UPDATE md_stock SET Quantity = Quantity + {$old_quatity} WHERE MedicineStockID='{$md_stock_id_update}'",$con);
		//save the log file
		saveData("INSERT INTO md_stock_records SET MedicineStockID='{$md_stock_id_update}', Operation='RESTORE', StockLevel='{$stock_level_updates}', Quantity='{$old_quatity}', Date='".time()."', UserID='{$_SESSION['user']['UserID']}', Status=1",$con);
		//register this as new distribution after deleting restore
		//restore the value
		$stock_level_updates = returnSingleField("SELECT Quantity FROM md_stock WHERE MedicineStockID='{$md_stock_id_update}'","Quantity",true,$con);
		saveData("UPDATE md_stock SET Quantity = Quantity - {$qty} WHERE MedicineStockID='{$md_stock_id_update}'",$con);
		//save the log file
		saveData("INSERT INTO md_stock_records SET MedicineStockID='{$md_stock_id_update}', Operation='DISTRIBUTION', StockLevel='{$stock_level_updates}', Quantity='{$qty}', Date='".time()."', UserID='{$_SESSION['user']['UserID']}', Status=1",$con);
		
		/* $sql2 .= ", MedecineRecordID='{$md_id}'";
		//echo $sql."<br /><br />";
		//save the medecine prescription because now no error in the query
		if($md_id && !returnSingleField("SELECT MedecineRecordID FROM  md_prescription WHERE MedecineRecordID='{$md_id}'",$field="MedecineRecordID",$data=true, $con))
			saveData($sql2, $con); //echo $sql2."<br /><br />"; */
	}
	//die;
	
	
	//now save found consumables and continue
	$_id = returnSingleField("SELECT MedecineNameID FROM cn_name WHERE MedecineName='Sachets'",$field="MedecineNameID",$data=true, $con);
	
	//this a bug fixed by changing the date to the consultation date
	$c_date = $_POST['consultation_date'];
	$emballage_id = returnSingleField($sq = "SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$_id}' && Amount >= 0 && Date <= '{$c_date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
	//echo $sq;
	$emballage_id_existing = returnSingleField("SELECT * FROM cn_records WHERE MedecinePriceID='{$emballage_id}' && PatientRecordID <= '{$patientid}'",$field="ConsumableRecordID",$data=true, $con);
	//echo $emballage_id_existing;
	//var_dump($consumables); die;
	$emb_saved = false;
	foreach($consumables as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "INSERT INTO cn_records SET ";
		//var_dump($exam);
		$f = 0; $examid=null; $price_id=0; $qty=0; $date="";
		$cons_name = "";
		foreach($exam as $key=>$value){
			if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^consumablequantity/",$key)){
				if($value){
					//echo $price_id;
					if($price_id == $emballage_id && $emballage > 0){
						//$value += $emballage;
						$emb_saved = true;
					}
					$sql .= "Quantity='{$value}'";
					$qty = $value;
				} else{
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^consumabledate/",$key)){
				$sql .= "Date='{$value}'";
				$date = $value;
			}
			elseif(preg_match("/^consumablename/",$key)){
				//select the consumable price id based on the found name
				$exam_id = returnSingleField("SELECT MedecineNameID FROM cn_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
				$price_id = returnSingleField($q = "SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$exam_id}' && Amount >= 0 && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
				//echo $q;
				//var_dump($price_id);
				if(!$price_id){
					$skip = true;
					break;
				}
				$sql .= "MedecinePriceID='{$price_id}'";
				$cons_name = $value;
			}
			
		}
		//echo $sql;
		//var_dump($skip);
		if($skip)
			continue;
		$sql .= ", PatientRecordID='{$patientid}'";
		//die;
		//echo $sql;// continue;
		//save the consumable found no problem in the request
		if($price_id && !returnSingleField("SELECT ConsumableRecordID FROM  cn_records WHERE MedecinePriceID='{$price_id}' && PatientRecordID='{$patientid}' && Date='".(date("Y-m-d",time()))."'",$field="ConsumableRecordID",$data=true, $con))
			saveData($sql, $con);// echo $sql."<br /><br />";
		//die;
	}
	//save existing consumables
	foreach($consumables_updates as $exam){
		//save one exam records
		$skip = false;
		//var_dump($exam); continue;
		//check data validity
		$sql = "UPDATE cn_records SET ";
		//var_dump($exam);
		$f = 0; $examid=null; $price_id=0; $qty=0; $date=""; $cn_record_id = 0;
		$cons_name = ""; $where= "";
		foreach($exam as $key=>$value){
			if(!preg_match("/^consumableexistbefore/",$key) && $f++ != 0)
				$sql .= ", ";
			if(preg_match("/^consumablequantity/",$key)){
				if($value){
					//echo $price_id;
					if($price_id == $emballage_id && $emballage > 0){
						//$value += $emballage;
						$emb_saved = true;
					}
					$sql .= "Quantity='{$value}'";
					$qty = $value;
				} else{
					$_sql_ = "DELETE FROM cn_records WHERE ConsumableRecordID='".($_POST["consumableexistbefore".substr($key,18)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					
					$skip = true;
					break;
				}
			}
			elseif(preg_match("/^consumabledate/",$key)){
				$sql .= "Date='{$value}'";
				$date = $value;
			}
			elseif(preg_match("/^consumablename/",$key)){
				//select the consumable price id based on the found name
				$exam_id = returnSingleField("SELECT MedecineNameID FROM cn_name WHERE MedecineName='{$value}'",$field="MedecineNameID",$data=true, $con);
				$price_id = returnSingleField($q = "SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$exam_id}' && Amount >= 0 && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
				//echo $q;
				//var_dump($price_id);
				if(!$price_id){
				
					$_sql_ = "DELETE FROM cn_records WHERE ConsumableRecordID='".($_POST["consumableexistbefore".substr($key,14)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					
					$skip = true;
					break;
				}
				$sql .= "MedecinePriceID='{$price_id}'";
				$cons_name = $value;
			} else if(preg_match("/^consumableexistbefore/",$key)){
				$where = "WHERE ConsumableRecordID='{$value}'";
			}
			
		}
		//echo $sql;
		//var_dump($skip);
		if($skip)
			continue;
		$sql .= ", PatientRecordID='{$patientid}' ".$where;
		//die;
		//save the consumable found no problem in the request
		saveData($sql, $con);// 
		//echo $sql."<br /><br />";
		//die;
	}
	//var_dump($consumables_updates);
	//die;
	//echo $emballage;
	/* if($emballage > 0 && !$emb_saved){
		//delete all existing record amballage
		saveData($ee = "DELETE FROM cn_records WHERE MedecinePriceID='{$emballage_id}' && PatientRecordID='{$patientid}'", $con);
		saveData($dd = "INSERT INTO cn_records SET MedecinePriceID='{$emballage_id}',Quantity={$emballage}, PatientRecordID='{$patientid}', Date='{$_POST['consultation_date']}'", $con);
		//echo $ee; echo "<br />";
		//echo $dd;
	} *//*  else if($emballage > 0 && !$emb_saved){
		if(!returnSingleField("SELECT ConsumableRecordID FROM  cn_records WHERE MedecinePriceID='{$emballage_id}' && PatientRecordID='{$patientid}' && Date='{$_POST['consultation_date']}'",$field="ConsumableRecordID",$data=true, $con))
		 	saveData("INSERT INTO cn_records SET MedecinePriceID='{$emballage_id}',Quantity={$emballage}, PatientRecordID='{$patientid}', Date='{$_POST['consultation_date']}'", $con);
	}*/
	
	//now save found acts and continue
	//var_dump($acts_updates);
	foreach($acts as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "INSERT INTO ac_records SET ";
		//var_dump($exam);
		$f = 0; $examid=null; $price_id=0; $qty=0; $date = ""; $rel_cons = null; $price=0;
		foreach($exam as $key=>$value){
			//var_dump($key);
			if($f++ != 0)
				$sql .= ", ";
			if(preg_match("/^actname/",$key)){
				//select the act price id based on the found name
				$exam_id = returnSingleField("SELECT ActNameID FROM ac_name WHERE Name='{$value}'",$field="ActNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT ActPriceID FROM ac_price WHERE ActNameID='{$exam_id}' && Amount >= 0 && Date <= '{$date}' && InsuranceCategoryID='{$ins_cat_id}' ORDER BY Date DESC LIMIT 0, 1",$field="ActPriceID",$data=true, $con);
				if(!$price_id){
					$skip = true;
					break;
				}
				///////ALTER TABLE `cn_records` ADD `Status` INT NOT NULL DEFAULT '1' ;
				/******************* SEARCH FOR CONSUMMABLE ANS SAVE THEM NOW *************/
				$rel_cons = formatResultSet($rslt=returnResultSet("SELECT ac_consumable.* FROM ac_consumable WHERE ActNameID='{$exam_id}' && Date <= '{$date} LIMIT 0, 1'",$con),$multirows=true,$con);
				
				/******************* END OF CONSUBLE RELATED TO THE ACT ******************/
				$sql .= "ActPriceID='{$price_id}'";
				$price = $price_id;
				//echo "act Found";
			} elseif(preg_match("/^actdate/",$key)){
				$sql .= "Date='{$value}'";
				$date = $value;
			} elseif(preg_match("/^actquantity/",$key)){
				if(!$value)
					$value = 1;
				$sql .= "Quantity='{$value}'";
				$qty = $value;
				$date = $value;
			}
			
		}
		if($skip)
			continue;
		$sql .= ", PatientRecordID='{$patientid}',
				NurseID=0";
		
		//save the act found no problem in the request
		if($price_id && !returnSingleField("SELECT ActRecordID FROM ac_records WHERE PatientRecordID='{$patientid}' && ActPriceID='{$price}' && Quantity='{$qty}' && Date='{$date}'","ActRecordID",true,$con))
			$act_id_id = saveAndReturnID($sql, $con); //echo $sql."<br />{$price_id}<br />";
			
		//var_dump($rel_cons);
		if($rel_cons){
			//echo $sql;
			
			if(!$db->checkField("cn_records","Facture")){
				//add the status field to the database
				saveData("ALTER TABLE `cn_records` ADD `Facture` INT NOT NULL DEFAULT '0'",$con);
			}
			
			foreach($rel_cons as $related_consumable){
				//save the consummable to the patient now
				//search the price id
				$price_data_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$related_consumable['MedecineNameID']}' && Amount >= 0 && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
				//check if the consumable
				saveData("INSERT INTO cn_records SET MedecinePriceID='{$price_data_id}', Quantity='{$related_consumable['Quantity']}', PatientRecordID='{$patientid}', Date='{$_POST['consultation_date']}', Facture='{$act_id_id}'",$con);
			}
		}
		//die;
	}
	
	//try to updates some old acts
	foreach($acts_updates as $exam){
		//save one exam records
		$skip = false;
		//check data validity
		$sql = "UPDATE ac_records SET ";
		//var_dump($exam);
		$f = 0; $examid=null; $price_id=0; $qty=0; $date = ""; $act_record_id = 0;
		foreach($exam as $key=>$value){
			//var_dump($key);
			if(!preg_match("/^actexistbefore/",$key) && $f++ != 0)
				$sql .= ", ";
			if(preg_match("/^actname/",$key)){
				//select the act price id based on the found name
				$exam_id = returnSingleField("SELECT ActNameID FROM ac_name WHERE Name='{$value}'",$field="ActNameID",$data=true, $con);
				$price_id = returnSingleField("SELECT ActPriceID FROM ac_price WHERE ActNameID='{$exam_id}' && Amount >= 0 && Date <= '{$date}' && InsuranceCategoryID='{$ins_cat_id}' ORDER BY Date DESC LIMIT 0, 1",$field="ActPriceID",$data=true, $con);
				if(!$price_id){
					$_sql_ = "DELETE FROM ac_records WHERE ActRecordID='".($_POST["actexistbefore".substr($key,7)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					$_sql_ = "DELETE FROM cn_records WHERE Facture='".($_POST["actexistbefore".substr($key,7)])."'";
					//echo $_sql_;// die;
					saveData($_sql_, $con);
					
					$skip = true;
					break;
				}
				///////ALTER TABLE `cn_records` ADD `Status` INT NOT NULL DEFAULT '1' ;
				/******************* SEARCH FOR CONSUMMABLE ANS SAVE THEM NOW *************/
				$rel_cons = formatResultSet($rslt=returnResultSet("SELECT ac_consumable.* FROM ac_consumable WHERE ActNameID='{$exam_id}' && Date <= '{$date} LIMIT 0, 1'",$con),$multirows=true,$con);
				
				/******************* END OF CONSUBLE RELATED TO THE ACT ******************/
				$sql .= "ActPriceID='{$price_id}'";
				//echo "act Found";
			} elseif(preg_match("/^actdate/",$key)){
				$sql .= "Date='{$value}'";
				$date = $value;
			} elseif(preg_match("/^actquantity/",$key)){
				$sql .= "Quantity='{$value}'";
				$date = $value;
			} elseif(preg_match("/^actexistbefore/",$key)){
				if($value)
					$act_record_id = $value;
			}
			
		}
		if($skip)
			continue;
		$sql .= ", PatientRecordID='{$patientid}',
				NurseID=0 WHERE ActRecordID='{$act_record_id}'";
		
		//save the consumable found no problem in the request
		if($price_id)
			saveData($sql, $con); //echo $sql."<br />{$price_id}<br />";
			
		//var_dump($rel_cons);
		if($rel_cons){
			//echo $sql;
			foreach($rel_cons as $related_consumable){
				//save the consummable to the patient now
				if(!$db->checkField("cn_records","Facture")){
					//add the status field to the database
					saveData("ALTER TABLE `cn_records` ADD `Facture` INT NOT NULL DEFAULT '1'",$con);
				}
				//search the price id
				$price_data_id = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$related_consumable['MedecineNameID']}' && Amount >= 0 && Date <= '{$date}' ORDER BY Date DESC LIMIT 0, 1",$field="MedecinePriceID",$data=true, $con);
				//check if the consumable
				saveData("INSERT INTO cn_records SET MedecinePriceID='{$price_data_id}', Quantity='{$related_consumable['Quantity']}', PatientRecordID='{$patientid}', Date='{$_POST['consultation_date']}', Facture='0'",$con);
			}
		}
		//die;
	}
	//echo ;
	//now try to save the hospitalization informations if available
	//var_dump($_POST);
	if(@$_POST['hospitalisationexistbefore']){
		$sql = "UPDATE ho_record SET Days='{$_POST['hospitalizationdays']}', HOPriceID='{$_POST['hospitalization_room_type']}', StartDate='{$_POST['hospitalizationdatein']}', EndDate='{$_POST['hospitalizationdateout']}' WHERE HORecordID='".(PDB($_POST['hospitalisationexistbefore'],true, $con))."'";
		//echo $sql;
		saveData($sql,$con);
	} else if(@$_POST['hospitalizationdays'] && is_numeric($_POST['hospitalizationdays']) && $_POST['hospitalizationdays']>0 && is_numeric($_POST['hospitalization_room_type'])){
		//now save hospitalization record 
		$sql = "INSERT INTO ho_record SET RecordID='{$patientid}', Days='{$_POST['hospitalizationdays']}', HOPriceID='{$_POST['hospitalization_room_type']}', StartDate='{$_POST['hospitalizationdatein']}', EndDate='{$_POST['hospitalizationdateout']}'";
		if(!returnSingleField("SELECT HORecordID FROM  ho_record WHERE RecordID='{$patientid}' && Days='{$_POST['hospitalizationdays']}' && HOPriceID='{$_POST['hospitalization_room_type']}'",$field="HORecordID",$data=true, $con))
			saveData($sql, $con);
		//echo $sql;
	}
	/* 
	//now try to save the ambulance if available
	if(@$_POST['ambulancelength'] && is_numeric($_POST['ambulancelength']) && $_POST['ambulancelength']>0){
		//now save hospitalization record 
		$sql = "INSERT INTO am_records SET PatientRecordID='{$patientid}', Length='{$_POST['ambulancelength']}'";//" HOPriceID='{$_POST['hospitalization_room_type']}'";
		$sql2 = "SELECT AmbulanceRecordID from am_records WHERE PatientRecordID='{$patientid}' && Length='{$_POST['ambulancelength']}'";//" HOPriceID='{$_POST['hospitalization_room_type']}'";
		if(@$_POST['ambulancemedical_document'] == "on"){
			$sql .= ", MedicalDocument='{$_POST['ambulancemedical_document']}'";
			$sql2 .= " && MedicalDocument='{$_POST['ambulancemedical_document']}'";
			
		}
		if(@$_POST['ambulanceconsultation_document'] == "on"){
			$sql .= ", ConsultationDocument='{$_POST['ambulanceconsultation_document']}'";
			$sql2 .= " && ConsultationDocument='{$_POST['ambulanceconsultation_document']}'";
		}
		if(@$_POST['ambulanceordonance_document'] == "on"){
			$sql .= ", Ordonance='{$_POST['ambulanceordonance_document']}'";
			$sql2 .= " && Ordonance='{$_POST['ambulanceordonance_document']}'";
		}
		$sql .= ", Date=NOW()";
		$sql2 .= " && Date='".date("Y-m-d",time())."'";
		if(!returnSingleField($sql2,$field="AmbulanceRecordID",$data=true, $con))
			saveData($sql, $con); 
		//echo $sql2.$sql;
	} */
	//echo $patientid;
	if(@$_POST['decision'])
		saveData("UPDATE pa_records SET Status='{$_POST['decision']}' WHERE PatientRecordID='{$patientid}'",$con);
	
	$error = "<span class='success' >Bill Completed <a target='_blank' title='Print' href='./print_bill.php?record={$patientid}'><img src='../images/print.png' /></a></span>";
	$error .= <<<DONE
	<script>
		$(".other_out").html("<span class='success' >Bill Completed <a target='_blank' title='Print' href='./print_bill.php?record={$patientid}'><img src='../images/print.png' /></a></span>");
	</script>
DONE;

 	if($_POST['save_and_print'] == 1){
		$error = "<span class='success clear_cmd' >Bill Completed <a target='_blank' title='Print' id='print_bill_now' href='./print_bill.php?record={$patientid}&print=print2'><img src='../images/print.png' /></a>
		<br />Printing Please Wait ...</span>";
		$error .= <<<DONE
		<script>
			function click_now(){
				try{
					$('#print_bill_now')[0].click();
					$('.clear_cmd').html('');
				} catch(e){
					//console.log("Error!");
				}
			}
			$("#doc_search").focus();
			$("#doc_search").select();
			setTimeout('click_now()',1000);
		</script>
DONE;

	}
}
echo $error;
?>