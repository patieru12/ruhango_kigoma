<?php
/* database connection function */
$con = null;

function connectDB(&$con=null, $db="care_full_v1_sovu",$server="127.0.0.1",$username="root",$password=""){
	if($con != null)
		return;
		
	@$con = mysql_connect($server,$username,$password);
	mysql_select_db($db,$con);
	mysql_query("SET NAMES utf-8");
	mysql_set_charset("utf-8",$con);
}
function reconnect(&$con){
	if($con == null)
		connectDB($con);
}

function isDataExist($sql, &$con){
	reconnect($con);
	$result = mysql_query($sql);
	if($result && mysql_num_rows($result)>0)
		return true;
	//echo $sql;
	return false;
	
}

function returnResultSet($sql,&$con){
	if($con == null)
		connectDB($con);
	//echo $sql."<br />";
	if($r = mysql_query($sql)or die(mysql_error()." ".$sql))
		return $r;
	echo mysql_error()." ".$sql;
}

function insertOrReturnID($sql1, $sql2, $field,&$con=null){
	if($con == null)
		connectDB($con);
	$check = returnResultSet($sql2,$con);
	if(mysql_num_rows($check)>0){
		//return the the id
		return formatResultSet($check,$multirows=false,$con)[$field];
	} else{
		return saveAndReturnID($sql1, $con);
	}
}
function returnSingleField($sql,$field,$data=true, &$con=null){
	//echo $sql;
	if($con == null)
		connectDB($con);
	if($r = returnResultSet($sql,$con)){
		//var_dump($r);
		$d = mysql_fetch_assoc($r);
		// var_dump($d, "<hr />");
		return $data?$d[$field]:$data;
	}
	// return 0;
	return null;
	
}

function formatResultSet($rslt,$multirows=true,&$con){
	reconnect($con);
	
	if(mysql_num_rows($rslt)<1)
		return null;
		
	if(!$multirows)
		return mysql_fetch_assoc($rslt);
	
	$rtn = array();
	while($r = mysql_fetch_assoc($rslt))
		$rtn[] = $r;
	
	return $rtn;
}


function returnAllData($sql,&$con){
	reconnect($con);
	$rslt = returnResultSet($sql,$con);
	if(mysql_num_rows($rslt)<1)
		return null;
	//return null;
	return formatResultSet($rslt,$multirows=true,$con);
	
}
function returnAllDataInTable($tbl,&$con, $condition = ""){
	reconnect($con);
	if($tbl == "")
		return null;
	$sql = "SELECT * FROM `{$tbl}` ".$condition;
	// echo $sql;
	$rslt = returnResultSet($sql,$con);
	if(mysql_num_rows($rslt)<1)
		return null;
	
	return formatResultSet($rslt,$multirows=true,$con);
}

function saveAndReturnID($sql, &$con){
	reconnect($con);
	if(mysql_query($sql) && mysql_affected_rows($con) ==1)
		return mysql_insert_id();
	echo mysql_error().$sql;
	return null;
		
}

function saveData($sql, &$con){
	reconnect($con);
	//echo $sql;
	if(mysql_query($sql)or die(mysql_error().$sql) && mysql_affected_rows($con) ==1)
		return true;
	// echo $sql;
	return false;
		
}

function PDB($str, $trim=true, &$con){
	reconnect($con);
	return mysql_real_escape_string($trim?trim($str):$str);
}


/**************************************** OTHER FUNCTION **********************/
function ommitStringPart($str,$char_to_display,&$ommitted=false){
	if(strlen($str)>$char_to_display){
		$ommitted = true;
		//echo $str;
		return "".substr($str,0,$char_to_display)."...";
	} else{
		return $str;
	}
	
}

function JoinArrays(&$array1, &$array2, &$array_out){
	if(is_array($array1))
		$array_out = $array1;
	if(is_array($array2)){
		foreach($array2 as $data)
			$array_out[] = $data;
	}
}

function fromDto1D(&$array, &$out){
	foreach($array as $key=>$value){
		if(is_array($value))
			fromDto1D($array[$key], $out);
		else
			$out[] = $value;
	}
}

function arraytostring(&$array, &$str, $position = 0, $sep = "; "){
	static $separate;
	//var_dump($sep); echo "<hr />";
	if(is_array($array)){
		foreach($array as $key=>$value){
			if(!is_array($value)){
				$str .= ($separate?$sep:"").$value;
				$separate = true;
			} else{
				//convert the dimension and call the array to string again
				$new_array = array();
				fromDto1D($value, $new_array);
				arraytostring($new_array, $str, $position, $sep);
			}
		}
	}
	//now replace any abnormal characters
	$str = preg_replace(array("/^;/"),array(""),$str);
}

function filterData(&$input, &$output, $suffix="srv_"){
	foreach($input AS $key=>$value){
		if(preg_match("/^{$suffix}/", $key) && !in_array(str_replace($suffix, "", $key), $output)){
			$output[] = str_replace($suffix, "", $key);
		}
	}
}

function autoConsulatationPricing($inNameID, $seNameD, &$log){
	$dayDefaultConsultation = returnSingleField("SELECT ServiceCode FROM se_name WHERE ServiceNameID = '{$seNameD}'","ServiceCode",true, $con);
		// echo $dayDefaultConsultation; die();
	if(in_array($dayDefaultConsultation, array("DNT", "MAT") ) ){
		$dayDefaultConsultation = "CPC";
	}
		// Check if the current day is not week-end
		$d = date("D",time());
		$weekendDays = array("Sat", "Sun");
		$toDayDate = date("Y-m-d", time());
		$currentHours = date("H", time());
		if($currentHours >= 17 || $currentHours < 7){
			$dayDefaultConsultation .= " Nuit";
		} else if(in_array($d, $weekendDays)){
			$dayDefaultConsultation .= " Week-End";
		} else if($closedDay =  returnSingleField("SELECT Date FROM sy_conge WHERE Date='{$toDayDate}'","Date",true,$con)){
			$dayDefaultConsultation .= " Jour Ferier";
		} else {
			$dayDefaultConsultation .= " Jour";
		}

		// Get the Category of the found consultatino
		
		// echo $dayDefaultConsultation; die();
		$consultations = formatResultSet($rslt=returnResultSet($s = "SELECT 	a.*,
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
		
		if(is_null($consultations)){
			$consultations = formatResultSet($rslt=returnResultSet($s = "SELECT 	a.*,
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
																			  c.ConsultationCategoryName = 'invisible'
																		", $con), true, $con);
		}
		// echo "<pre>"; var_dump($dayDefaultConsultation, $consultations);
		if(is_null($consultations)){
			$log = array("msg"=>$dayDefaultConsultation);
			return NULL;
		} else{
			return $consultations[0]["ConsultationPriceID"];
		}
}

/********
function
@name dayCategory
@param date_value yyyy-mm-dd
@return	1. normal day	2. week-end		3. jour fierier		0. undefined
*********/
function dayCategory($date, $type=0){
	//var_dump($date);
	$week_end = $type?array('Sat'=>'OPT Night/WE', 'Sun'=>'OPT Night/WE'):array("Sat"=>"CPC Nuit/WE","Sun"=>"CPC Nuit/WE");
	$day = $type?
				array("Mon"=>"OPT Day","Tue"=>"OPT Day","Wed"=>"OPT Day","Thu"=>"OPT Day","Fri"=>"OPT Day")
				:
				array("Mon"=>"CPC Jour","Tue"=>"CPC Jour","Wed"=>"CPC Jour","Thu"=>"CPC Jour","Fri"=>"CPC Jour");
	if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$date))
		return 0;
	$sec = mktime(0,0,0,explode("-",$date)[1],explode("-",$date)[2],explode("-",$date)[0]);
	$d = date("D",$sec);
	// var_dump($week_end);
	if(@$week_end[$d])
		return $week_end[$d];
	/* ferier ici */
	if(returnSingleField("SELECT Date FROM sy_conge WHERE Date='{$date}'","Date",true,$con))
		return $type?"OPT Closed Day":"CPC Jour Ferier";
	/* ferier ici */
	if(@$day[$d])
		return $day[$d];
	return 0;
}
/****
@name ConsType
@param 

*/
/**
* @name getAge
* @param date
* @param $notation
* 
*/
function getAge($date,$notation=1, $current_date='', $onlyDays=false){
	if($date == "0000-00-00")
		return "";
	if($current_date == "")
		$current_date = date("Y-m-d",time());
	$expended = explode("-",$date);
	$expended_current = explode("-",$current_date);
	
	$year = $expended[0];
	$month = $expended[1];
	$day = $expended[2];
	$brth = mktime(0,0,0,$month,$day,$year);
	$current_year = $expended_current[0];
	$current_month = $expended_current[1];
	$current_day = $expended_current[2];
	$crnt = mktime(0,0,0,$current_month,$current_day,$current_year);
	$seconds_of_a_year 	= 60*60*24*365;
	$seconds_of_a_month = 60*60*24*30;
	$seconds_of_a_week 	= 60*60*24*7;
	$seconds_of_a_day 	= 60*60*24;


	//echo date("Y-m-d",$crnt);
	//echo date("Y-m-d",$brth);
	
	$pa_seconds = $crnt - $brth;
	// return $pa_seconds."/".$seconds_of_a_day."=";

	if($onlyDays){
		if($pa_seconds < $seconds_of_a_day)
			return 1; //":".$date."<==>".$current_date;
		$days = (int)($pa_seconds/$seconds_of_a_day);
		if(($pa_seconds%$seconds_of_a_day) > 0){
			$days++;
		}
		return $days;
	}
	//echo $date;
	if($notation == 15){
		if($pa_seconds <= $seconds_of_a_week)
			return 1;
		else if($pa_seconds <= ($seconds_of_a_month*2))
			return 2;
		else if($pa_seconds <= ($seconds_of_a_month*59))
			return 3;
		
		return (int)($pa_seconds/$seconds_of_a_week);
	}
	//pa_year
	$pa_year = (int)($pa_seconds/$seconds_of_a_year);
	//var_dump($pa_year," ",$pa_seconds," ",$seconds_of_a_year); 
	if($pa_year >= 5 || $notation == 2)
		return $pa_year.($notation==1?" yrs":"");

	if($pa_year < 5){
		$pa_month = (int)($pa_seconds/$seconds_of_a_month);
		// $pa_month -= 24;
		if($pa_month <= 2){
			$pa_weeks = (int)($pa_seconds/$seconds_of_a_week);
			
			// $pa_weeks -= 109;
			// var_dump($pa_weeks);
			if($pa_weeks <= 1){
				$pa_days = (int)($pa_seconds/$seconds_of_a_day);
				// var_dump($pa_days);
				return $pa_days. " day".($pa_days>1?"s":"");
			} else {
				return $pa_weeks . " week".($pa_weeks>1?"s":"");
			}
		} else {
			return $pa_month." month";
		}
		// var_dump($pa_month); die();
	}
	
	//var_dump($pa_seconds);
	$pa_seconds %= $seconds_of_a_year;
	$pa_month = (int)($pa_seconds/$seconds_of_a_month);
	//var_dump($pa_seconds, $seconds_of_a_month);
	$pa_seconds %= $seconds_of_a_month;
	if($notation == 2){
		//only year an month in decimal notation
		return $pa_year.($pa_month>0?".".$pa_month:"");
	}
	if($pa_year>=1)
		return ($pa_year>0?$pa_year.($notation==1?" yr":","):"").($pa_month>0?" ".$pa_month." mn":"");
	
	$pa_weeks = (int)($pa_seconds/$seconds_of_a_week);
	$pa_seconds %= $seconds_of_a_week;
	$pa_days = (int)($pa_seconds/$seconds_of_a_day);
	if($notation == 2){
		//only year an month in decimal notation
		return $pa_year.($pa_month>0?".".$pa_month:"");
	}
	return ($pa_year>0?$pa_year." yr":"").($pa_month>0?" ".$pa_month." mn":"")
			.($pa_weeks>0?" ".$pa_weeks." wk":"")
			.($pa_days>0?" ".$pa_days." dy":"");
	
	$found_year = 0;
	//compare all found information return the corresponding calculated year
	if(($current_year - $year) > 1 && ($current_year - $year) > 5){
		//now the full year is found
		return ($current_year - $year)." yr".(($current_year - $year)>1?"s":"");
	}
	if($current_month >= $month){
		return ($current_year - $year)." yr".(($current_year - $year)>1?"s ":" "). (($current_month - $month) > 0?($current_month - $month)." m":"");
	}
}

function generateDocID($type, &$rtnSql,$date = ""){
	//echo $date;
	if($date == "")
		$date = date("Ymd",time());
	$max = returnSingleField($sql="SELECT MAX(`LastID`) as DocID FROM sy_ids, pa_records, in_name, in_category WHERE pa_records.InsuranceNameID = in_name.InsuranceNameID && in_name.CategoryID = in_category.InsuranceCategoryID  && in_category.InsuranceCategoryID = sy_ids.InsuranceCategoryID && pa_records.InsuranceNameID='{$type}' && sy_ids.Day = '{$date}'","DocID",$data=true, $con);
	//echo $sql;
	//return;
	//var_dump($type);
	//var_dump($max);
	
	if($max == null)
		$max = 0;//$date.returnSingleField($sql="SELECT InsuranceCode FROM in_category WHERE InsuranceCategoryID='".returnSingleField($sql="SELECT CategoryID FROM in_name WHERE InsuranceNameID='{$type}'","CategoryID",$data=true, $con)."'","InsuranceCode",$data=true, $con);
	//var_dump($max);
	//$max .= "1";
	$prefix = $date.returnSingleField($sql="SELECT InsuranceCode FROM in_category WHERE InsuranceCategoryID='".returnSingleField($sql="SELECT CategoryID FROM in_name WHERE InsuranceNameID='{$type}'","CategoryID",$data=true, $con)."'","InsuranceCode",$data=true, $con);//substr($max,0,9);
	$int_max = substr($max,9);
	//var_dump($int_max);
	if(!$int_max)
		$int_max = 0;
	++$max;
	$rtnSql = "INSERT INTO sy_ids SET InsuranceCategoryID='".returnSingleField($sql="SELECT CategoryID FROM in_name WHERE InsuranceNameID='{$type}'","CategoryID",$data=true, $con)."', LastID='{$max}', Day={$date}";
	//echo $prefix.$max;
	return $prefix.($max);
}

// function getDefaultConsultation();

/*****************************************************/
function GetFile($filename,$ext,$case="strtolower"){
	$ex = explode(".",$_FILES[$filename]['name']);
	$e = $ex[count($ex)-1];
	if(in_array($case($e),$ext))
		return true;
	else
		return false;
}

function Enc($str){
	return md5(sha1(md5(sha1($str))));
}

function co($data,$loc,&$con){
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	$co_id = returnSingleField("SELECT ConsultationCategoryID FROM co_category WHERE ConsultationCategoryName='{$data[0]}'","ConsultationCategoryID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO co_category SET ConsultationCategoryName='{$data[0]}'", $con);
	//set all price
	foreach($loc as $lo=>$i){
		//updaate all existing 
		if(!returnSingleField("SELECT ConsultationPriceID FROM co_price WHERE ConsultationCategoryID='{$co_id}' && InsuranceCategoryID='{$lo}' && Amount='{$data[$i]}'","ConsultationPriceID",true,$con)){
			saveData("UPDATE co_price SET Status=0 WHERE ConsultationCategoryID='{$co_id}' && InsuranceCategoryID='{$lo}'",$con);
			saveData("INSERT INTO co_price SET ConsultationCategoryID='{$co_id}', InsuranceCategoryID='{$lo}', Amount='{$data[$i]}', Date=NOW(), Status=1",$con);
		}
	}
}

function ho($data,$loc,&$con){
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	$co_id = returnSingleField("SELECT TypeID FROM ho_type WHERE Name='{$data[0]}'","TypeID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO ho_type SET Name='{$data[0]}'", $con);
	//set all price
	foreach($loc as $lo=>$i){
		//updaate all existing 
		if(!returnSingleField("SELECT HoPriceID FROM ho_price WHERE HoTypeID='{$co_id}' && InsuranceCategoryID='{$lo}' && Amount='{$data[$i]}'","HoPriceID",true,$con)){
			saveData("UPDATE ho_price SET Status=0 WHERE HoTypeID='{$co_id}' && InsuranceCategoryID='{$lo}'",$con);
			saveData("INSERT INTO ho_price SET HoTypeID='{$co_id}', InsuranceCategoryID='{$lo}', Amount='{$data[$i]}', Date=NOW(), Status=1",$con);
		}
	}
}



function saveMD($data,$loc,&$con){
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	//var_dump($data); die;
	$co_id = returnSingleField("SELECT MedecineNameID FROM md_name WHERE CategoryCode='{$data[6]}'","MedecineNameID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO md_name SET MedecineName='{$data[0]}', CategoryCode='{$data[6]}', MedecineCategorID='{$loc}', Emballage='{$data[5]}'", $con);
	else{
		saveData("UPDATE md_name SET MedecineName='{$data[0]}', MedecineCategorID='{$loc}', Emballage='{$data[5]}' WHERE MedecineNameID='{$co_id}'",$con);
	}
	//return;
	//updaate all existing 
	$amount = round($data[2] + ($data[2]*0.2),1);
	if(!returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$co_id}' && BuyingPrice='{$data[2]}' && Date='{$data[7]}' ","MedecinePriceID",true,$con)){
		saveData("UPDATE md_price SET Status=0 WHERE MedecineNameID='{$co_id}'",$con);
		saveData($s = "INSERT INTO md_price SET MedecineNameID='{$co_id}', BuyingPrice='{$data[2]}', Amount='{$amount}', Date='{$data[7]}', Status=1, Emballage='{$data[5]}'",$con);
		//echo $s."<br />";
	}
}
function saveCN($data,$loc,&$con){
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	//var_dump($data); die;
	$co_id = returnSingleField("SELECT MedecineNameID FROM cn_name WHERE MedecineName='{$data[0]}'","MedecineNameID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO cn_name SET MedecineName='{$data[0]}'", $con);
	else{
		saveData("UPDATE cn_name SET MedecineName='{$data[0]}' WHERE MedecineNameID='{$co_id}'",$con);
	}
	//return;
	//updaate all existing 
	$amount = round($data[2] + ($data[2]*0.2),1);
	if(!returnSingleField("SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$co_id}' && BuyingPrice='{$data[2]}' && Date='{$data[6]}' ","MedecinePriceID",true,$con)){
		saveData("UPDATE cn_price SET Status=0 WHERE MedecineNameID='{$co_id}'",$con);
		saveData($s = "INSERT INTO cn_price SET MedecineNameID='{$co_id}', BuyingPrice='{$data[2]}', Amount='{$amount}', Date='{$data[6]}', Status=1, Emballage='{$data[5]}'",$con);
		//echo $s."<br />";
	}
}

function saveDG($data,$catid,$con){
	//var_dump($data); return;
	$data[1] = PDB($data[1],true,$con);
	$data[2] = PDB($data[2],true,$con);
	$data[3] = PDB($data[3],true,$con);
	//search for item_id
	$co_id = returnSingleField("SELECT DiagnosticID FROM co_diagnostic WHERE DiagnosticCode='{$data[1]}'","DiagnosticID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO co_diagnostic SET DiagnosticName='{$data[2]}', `English`='{$data[3]}', Code='{$data[4]}', DiagnosticCategoryID='{$catid}', DiagnosticCode='{$data[1]}'", $con);
}


function co_edit($data,&$con){
	//var_dump($data);
	if($data['record_id']){
		//save new record to co_records
		saveData("UPDATE co_records SET ConsultationPriceID='{$data['consultation']}' WHERE ConsultationRecordID='{$data['record_id']}' && PatientRecordID='{$data['patient_record_id']}'",$con);
	}
}

function md_edit($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		saveData("UPDATE md_records SET Quantity='{$data['quantity']}' WHERE MedecineRecordID='{$data['record_id']}'",$con);
	}
}

function md_delete($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		saveData("DELETE FROM md_records WHERE MedecineRecordID='{$data['record_id']}'",$con);
	}
}

function ex_delete($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		//var_dump($data);
		saveData("DELETE FROM la_result_record WHERE ExamRecordID ='{$data['record_id']}'",$con);
		
		saveData("DELETE FROM la_records WHERE ExamRecordID ='{$data['record_id']}'",$con);
	}
}

function ac_delete($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		saveData("DELETE FROM ac_records WHERE ActRecordID ='{$data['record_id']}'",$con);
	}
}

function cn_delete($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		saveData("DELETE FROM cn_records WHERE ConsumableRecordID ='{$data['record_id']}'",$con);
	}
}

function cn_edit($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		saveData("UPDATE cn_records SET Quantity='{$data['quantity']}' WHERE ConsumableRecordID='{$data['record_id']}'",$con);
	}
}

function tm_edit($data,&$con){
	//var_dump($data); die;
	if($data['record_id']){
		//save new record to co_records
		saveData("UPDATE mu_tm SET TicketPaid='{$data['amount']}' WHERE TicketID='{$data['record_id']}'",$con);
	}
}


function la($data,$loc,&$con){
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	$co_id = returnSingleField("SELECT ExamID FROM la_exam WHERE ExamName='{$data[0]}'","ExamID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO la_exam SET ExamName='{$data[0]}', ExamCode='', ResultType=0", $con);
	//set all price
	foreach($loc as $lo=>$i){
		//updaate all existing 
		if(!returnSingleField("SELECT ExamPriceID FROM la_price WHERE ExamID='{$co_id}' && InsuranceTypeID='{$lo}' && Amount='{$data[$i]}'","ExamPriceID",true,$con)){
			saveData("UPDATE la_price SET Status=0 WHERE ExamID='{$co_id}' && InsuranceTypeID='{$lo}'",$con);
			saveData("INSERT INTO la_price SET ExamID='{$co_id}', InsuranceTypeID='{$lo}', Amount='{$data[$i]}', Date=NOW(), Status=1",$con);
		}
	}
}

function ac($data,$loc,&$con){
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	$co_id = returnSingleField("SELECT ActNameID FROM ac_name WHERE Name='{$data[0]}'","ActNameID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO ac_name SET Name='{$data[0]}'", $con);
	//set all price
	foreach($loc as $lo=>$i){
		//updaate all existing 
		if(!returnSingleField("SELECT ActPriceID FROM ac_price WHERE ActNameID='{$co_id}' && InsuranceCategoryID='{$lo}' && Amount='{$data[$i]}'","ActPriceID",true,$con)){
			saveData("UPDATE ac_price SET Status=0 WHERE ActNameID='{$co_id}' && InsuranceCategoryID='{$lo}'",$con);
			saveData("INSERT INTO ac_price SET ActNameID='{$co_id}', InsuranceCategoryID='{$lo}', Amount='{$data[$i]}', Date=NOW(), Status=1",$con);
		}
	}
}

function am($data,$loc,&$con){
	return null;
	$data[0] = PDB($data[0],true,$con);
	//search for item_id
	$co_id = returnSingleField("SELECT ActNameID FROM ac_name WHERE Name='{$data[0]}'","ActNameID",true,$con);
	if(!$co_id)
		$co_id = saveAndReturnID("INSERT INTO ac_name SET Name='{$data[0]}'", $con);
	//set all price
	foreach($loc as $lo=>$i){
		//updaate all existing 
		if(!returnSingleField("SELECT ActPriceID FROM ac_price WHERE ActNameID='{$co_id}' && InsuranceCategoryID='{$lo}' && Amount='{$data[$i]}'","ActPriceID",true,$con)){
			saveData("UPDATE ac_price SET Status=0 WHERE ActNameID='{$co_id}' && InsuranceCategoryID='{$lo}'",$con);
			saveData("INSERT INTO ac_price SET ActNameID='{$co_id}', InsuranceCategoryID='{$lo}', Amount='{$data[$i]}', Date=NOW(), Status=1",$con);
		}
	}
}
function max_value(&$data){
	if(is_array($data) && is_numeric($data[0])){
		$max = $data[0];
		//var_dump($data);echo "<br />";
		foreach($data as $d)
			if($d>$max)
				$max = $d;
		
		return $max;
	} else{
		return null;
	}
}


function restoreTable(&$new_values, &$changes, &$address, &$reference){
	/* //var_dump($new_values);echo "<hr />";
	echo $reference['ActiveTable'];
	//var_dump($changes);echo "<hr />";
	var_dump($address);echo "<hr />";
	//var_dump($reference);
	echo "<hr /><hr />";
	return; */
	$primary_key_field = $reference['PrimaryKey'];
	$active_table = $reference['ActiveTable'];
	//var_dump($reference, $address);
	//loop all information and save new data in the database
	$query = ""; $query_condition = ""; $field_counter = 0;
	$last_address_field = "";
	foreach($address as $field=>$location){
		if($field_counter++ > 0){
			if($primary_key_field != $last_address_field)
				$query .= ", ";
			if($field_counter > 2)
				$query_condition .= " && ";
		} else
			$changes[$active_table][$field][$new_values[$location]] = PDB($new_values[$location],true, $con); //keep the same value of the first expected column to Primary Key of the table
		//check if the field has any other reference
		//var_dump($primary_key_field);
		if(@$reference[$field] && is_array($reference[$field]) && $changes[$reference[$field]['table']][($reference[$field]['Field'])][$new_values[$location]] ){
			if($primary_key_field != $field)
				$query .= "`{$field}`='".PDB($changes[$reference[$field]['table']][($reference[$field]['Field'])][$new_values[$location]],true,$con)."'";
			if($field_counter > 1)
				$query_condition .= "`{$field}`='".PDB($changes[$reference[$field]['table']][($reference[$field]['Field'])][$new_values[$location]],true,$con)."'";
		} else {
			if($primary_key_field != $field)
				$query .= "`{$field}`='".PDB($new_values[$location],true,$con)."'";
			if($field_counter > 1)
				$query_condition .= "`{$field}`='".PDB($new_values[$location],true,$con)."'";
		}
		$last_address_field = $field;
	}
	
	//echo $query_condition;
	//check if the row exist before after all formatting
	if(!$primary_key = returnSingleField($sql = "SELECT `{$primary_key_field}` FROM `{$active_table}`".($query_condition?" WHERE ".$query_condition:""),$primary_key_field,true,$con)){
		//echo $sql; echo "<br />";
		$primary_key = saveAndReturnID($sql = "INSERT INTO `{$active_table}` SET {$query}",$con);
		//echo $sql; echo "<hr />";
	}
	//echo $primary_key;
	//now set new replacement value in the change variable now
	$changes[$active_table][$primary_key_field][$new_values[$address[$primary_key_field]]] = $primary_key;
}
function RoundUp($value, $check=5){
	$value = round($value, 0);
	return ($value + (($value%$check)?($check - ($value%$check)):0) );
}

function MaxNumber(&$data){
	if(is_array($data)){
		$out = array();
		fromDto1D($data, $out);
		$max = $out[0];
		foreach($out as $number)
			if($max < ((int)$number))
				$max = ((int)$number);
		return $max;
	} else if(is_numeric($data))
		return $data;
	else 
		return null;
}
function IncrementString($sql, $field, $static,&$con){
	reconnect($con);
	$data = formatResultSet($rst=returnResultSet($sql, $con), true,$con);
	$filtered = array();
	foreach($data as $d)
		$filtered[] = str_replace($static,"",$d[$field]);
	
	$last = MaxNumber($filtered);
	
	return $static.(++$last);
	
}

function getDbTableList(&$tbl_list, &$con){
	reconnect($con);
	$rst = returnResultSet("SHOW TABLES", $con);
	$data = formatResultSet($rst, true,$con);
	//convert the array in single dimension array
	fromDto1D($data,$tbl_list);
	///var_dump($data);
}

function isPatientFileLocked($PatientRecordID, &$con){
	$sql = "SELECT PatientRecordID FROM pa_records WHERE PatientRecordID='{$PatientRecordID}' AND DocStatus='locked'";
	if(isDataExist($sql, $con))
		return true;
	return false;
}

/************************* PHP EXCEL FUNCTION *********************************/
function SpanCells($activeSheet,$range,$align='left'){
		if(!is_array($range)){
			$activeSheet->mergeCells($range);
			if($align != 'left'){
				$cells = explode(":",$range);
				if($align == 'center')
					$activeSheet->getStyle($cells[0])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				elseif($align == 'right')
					$activeSheet->getStyle($cells[0])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
		} else{
			foreach($range as $cells){
				$activeSheet->mergeCells($cells['from'].":".$cells['to']);
				if($cells['align'] == 'right')
					$activeSheet->getStyle($cells['from'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				else if($cells['align'] == 'center')
					$activeSheet->getStyle($cells['from'])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}
		}
	}
	
	
/************************* PHP EXCEL FUNCTION *********************************/

function getReceptionNumbers($keyword, $date, $table,$field){
	// $select = ($field == "Number"?"COUNT(a.{$field})":"SUM(a.{$field})");
	$sql = "SELECT 	SUM({$field}) AS {$field}
					FROM `{$table}` AS a
					WHERE a.itemName = '{$keyword}' &&
						  a.Date = '{$date}'";
	// return $sql;
	if($keyword == "Other Printings"){
		// echo $sql;
	}
	// echo $sql."<hr />";
	return returnSingleField($sql, $field, true, $con);
}


function printSingleOctet($number, $isLast=false){
	// echo $number."::>";
	$digits = array(0=>"zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");
	$tens 	= array(10=>"ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighten", "nineteen" );
	$tenth 	= array(2=>"twenty", "thirty", "fourty", "fifty", "sixty", "seventy", "eighty", "ninety");
	if($number < 10){
		return $digits[$number];
	}

	if($number < 20){
		return $tens[$number];
	}

	$numberString = (string) $number;
	$numberStringOut = "";
	$started = false;
	$i = 0;
	if(strlen($numberString) == 3){
		$numberStringOut .= " ".$digits[$numberString[$i]]." hundred";
		$i++;
	}
	$lastIndex = strlen($numberString) - 1;
	$replacebleChars = true;
	for($i; $i<=$lastIndex; $i++){
		if($numberString[$i] == 0){
			continue;
		}

		if($i > 0 && $i < $lastIndex && $numberString[$i] == 1){
			$remains = $numberString[$i].$numberString[($i+1)];
			$numberStringOut .= " ".$tens[$remains];
			break;
		}
		// $numberStringOut .= "(:";
		/*if($i == $lastIndex && $isLast){
			$numberStringOut .= " and";
			// $replacebleChars = false;
		}*/
		// echo  $i . " < ".$lastIndex."<br />";
		$numberStringOut .= " ".($i < $lastIndex?$tenth[$numberString[$i]]:$digits[$numberString[$i]]);
		// $numberStringOut .= " ".(($i + 1) == $lastIndex?$tenth[$numberString[$i]]:$digits[$numberString[$i]]);
	}
	// var_dump($numberStringOut);echo "\n";
	return trim($numberStringOut);
}
function getEnglishNumber($number, $level=1){
	$orginal = number_format($number,3);
	// $number = (double)$number;
	if(!is_numeric($number)){
		return "Error";
	}

	$digits = array(0=>"zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");
	$tens 	= array(10=>"ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighten", "nineteen" );
	$tenth 	= array(2=>"twenty", "thirty", "fourty", "fifty", "sixty", "seventy", "eighty", "ninety");
	$levels = array(1=>"", "thousand", "million", "billion");
	// var_dump($number); echo "\n";
	$additional = "";
	if(is_float($number)){
		$decimalString 	= number_format($number, 2);
		$decimalPart 	= explode(".", $decimalString);

		$decimalInteger = (int)$decimalPart[1];
		$number 		= preg_replace("/,/", "", $decimalPart[0]);
		// var_dump($decimalInteger);
		$decimalIntegerString = (string)$decimalInteger;
		// var_dump($decimalIntegerString);
		// Remove all last zero that can be presanted
		// $additional .= getEnglishNumber($decimalInteger);
	}
	// var_dump($number);
	if($number < 10){
		return $digits[$number].($additional?" point ".$additional:"");
	}
	if($number < 20){
		return $tens[$number].($additional?" point ".$additional:"");
	}
	// Here is another case to handle
	$numberString = number_format($number);
	// var_dump($numberString);
	$numberArray  = explode(",", $numberString);
	// var_dump($numberArray);
	$levelCounter = count($numberArray);
	$numberStringOut = "";
	$lastIndex = count($numberArray) - 1;
	$isLast = false;
	for($i=0; $i<=$lastIndex; $i++){
		if($i == $lastIndex){
			$isLast = true;
		}
		$currentOctet = (int)$numberArray[$i];
		$numberStringOut .= " ".printSingleOctet($currentOctet, $isLast). " ".($levels[($levelCounter--)]);
		// var_dump($numberStringOut);
		// break;
	}

	return trim($numberStringOut.($additional?" point ".$additional:""));// . " (".$orginal.")";
}

function generateLaboIDs($patientID, &$quaters=array()){
	// Quanter information here
	$quaterName = date("Y",time()).$quaters[date("m",time())];
	$quaterID 	= formatResultSet($rslt= returnResultSet("SELECT 	a.QuarterID AS QuarterID
																	FROM la_quarters AS a
																	WHERE QuarterName = '{$quaterName}'
																	",$con), false, $con);
	$qID = null;
	if(!is_array($quaterID)){
		$qID = saveAndReturnID($sql="INSERT INTO la_quarters SET QuarterName = '{$quaterName}'", $con);
	} else{
		$qID = $quaterID['QuarterID'];
	}

	$year = date("Y", time());
	$nextAnnualID 	= null;
	$nextGeneralID 	= NULL;
	$nextMonthlyID 	= NULL;
	$updateLaboQuery = "UPDATE la_records SET ";
	$comma = false;
	// Here Find the lat Number in every field
	$lastAnnualID = formatResultSet($rslt=returnResultSet("SELECT 	a.AnnualID AS AnnualID,
																	a.ExamPriceID AS ExamPriceID,
																	c.RegisterID AS RegisterID,
																	a.ConsultationRecordID AS ConsultationRecordID,
																	b.ExamID AS ExamID
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	WHERE a.ExamRecordID = {$patientID}
																	ORDER BY AnnualID DESC
																	LIMIT 0, 1",$con), false, $con);
	$ConsultationRecordID = $lastAnnualID['ConsultationRecordID'];
	// Get the Last exam of this register
	if(is_array($lastAnnualID)){
		// Here the Patient Identification is well registered and we have to take sample
		$veryAnnualSample = formatResultSet($rslt=returnResultSet("SELECT 	a.AnnualID AS AnnualID,
																			a.ExamPriceID AS ExamPriceID,
																			c.RegisterID AS RegisterID,
																			a.ConsultationRecordID AS ConsultationRecordID,
																			b.ExamID AS ExamID
																			FROM la_records AS a
																			INNER JOIN la_price AS b
																			ON a.ExamPriceID = b.ExamPriceID
																			INNER JOIN la_exam AS c
																			ON b.ExamID = c.ExamID
																			WHERE c.ExamID = {$lastAnnualID['ExamID']} &&
																				  a.ResultDate LIKE('{$year}%')
																			ORDER BY AnnualID DESC
																			LIMIT 0, 1",$con), false, $con);
		if(!is_array($veryAnnualSample) || is_null($veryAnnualSample['AnnualID'])){
			$nextAnnualID = 1;
		} else {
			$nextAnnualID = ++$veryAnnualSample['AnnualID'];
		}
		$updateLaboQuery .= ($comma?", ":"")."AnnualID=".$nextAnnualID;
		$comma = true;
		// var_dump($veryAnnualSample, $nextAnnualID, $updateLaboQuery); echo "<hr />";
	}
	// echo $s;
	$lastGeneralID = formatResultSet($rslt=returnResultSet("SELECT 	a.GeneralID AS GeneralID,
																	a.ExamPriceID AS ExamPriceID,
																	c.RegisterID AS RegisterID,
																	a.ConsultationRecordID AS ConsultationRecordID,
																	b.ExamID AS ExamID,
																	a.QuarterID AS QuarterID
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	WHERE a.ExamRecordID = {$patientID}
																	ORDER BY ExamNumber DESC
																	LIMIT 0, 1",$con), false, $con);
	if(is_array($lastGeneralID)){
		// Here the Patient Identification is well registered and we have to take sample
		$veryGeneralSample = formatResultSet($rslt=returnResultSet("SELECT 	a.GeneralID AS GeneralID,
																			a.ExamPriceID AS ExamPriceID,
																			c.RegisterID AS RegisterID,
																			a.ConsultationRecordID AS ConsultationRecordID,
																			b.ExamID AS ExamID
																			FROM la_records AS a
																			INNER JOIN la_price AS b
																			ON a.ExamPriceID = b.ExamPriceID
																			INNER JOIN la_exam AS c
																			ON b.ExamID = c.ExamID
																			WHERE a.QuarterID = {$qID}
																			ORDER BY GeneralID DESC
																			LIMIT 0, 1",$con), false, $con);
		// var_dump($veryGeneralSample['ConsultationRecordID'], $lastGeneralID['ConsultationRecordID']);
		if(!is_array($veryGeneralSample) || is_null($veryGeneralSample['GeneralID'])){
			$nextGeneralID = 1;
		} else if($veryGeneralSample['ConsultationRecordID'] == $lastGeneralID['ConsultationRecordID']){
			$nextGeneralID = $veryGeneralSample['GeneralID'];
		} else {
			$nextGeneralID = ++$veryGeneralSample['GeneralID'];
		}
		$comma = true;
		$updateLaboQuery .= ($comma?", ":"")."GeneralID=".$nextGeneralID;
		// var_dump($veryAnnualSample, $nextGeneralID, $updateLaboQuery); echo "<hr />";
	}

	$lastMonthlyID = formatResultSet($rslt=returnResultSet("SELECT 	a.MonthlyID AS MonthlyID,
																	a.ExamPriceID AS ExamPriceID,
																	c.RegisterID AS RegisterID,
																	a.ConsultationRecordID AS ConsultationRecordID,
																	b.ExamID AS ExamID,
																	a.QuarterID AS QuarterID
																	FROM la_records AS a
																	INNER JOIN la_price AS b
																	ON a.ExamPriceID = b.ExamPriceID
																	INNER JOIN la_exam AS c
																	ON b.ExamID = c.ExamID
																	WHERE a.ExamRecordID = {$patientID}
																	ORDER BY MonthlyID DESC
																	LIMIT 0, 1",$con), false, $con);
	// var_dump($lastMonthlyID);
	if(is_array($lastMonthlyID)){
		// Here the Patient Identification is well registered and we have to take sample
		$veryMonhlySample = formatResultSet($rslt=returnResultSet("SELECT 	a.MonthlyID AS MonthlyID,
																			a.ExamPriceID AS ExamPriceID,
																			c.RegisterID AS RegisterID,
																			a.ConsultationRecordID AS ConsultationRecordID,
																			b.ExamID AS ExamID
																			FROM la_records AS a
																			INNER JOIN la_price AS b
																			ON a.ExamPriceID = b.ExamPriceID
																			INNER JOIN la_exam AS c
																			ON b.ExamID = c.ExamID
																			WHERE c.RegisterID = {$lastMonthlyID['RegisterID']} &&
																				  a.QuarterID = {$qID}
																			ORDER BY MonthlyID DESC
																			LIMIT 0, 1",$con), false, $con);
		// var_dump($veryMonhlySample['ConsultationRecordID'], $lastMonthlyID['ConsultationRecordID']);
		if(!is_array($veryMonhlySample) || is_null($veryMonhlySample['MonthlyID'])){
			$nextMonthlyID = 1;
		} else if($veryMonhlySample['ConsultationRecordID'] == $lastMonthlyID['ConsultationRecordID']){
			$nextMonthlyID = $veryMonhlySample['MonthlyID'];
		} else {
			$nextMonthlyID = ++$veryMonhlySample['MonthlyID'];
		}
		$comma = true;
		$updateLaboQuery .= ($comma?", ":"")."MonthlyID=".$nextMonthlyID;
		// var_dump($lastMonthlyID, $nextMonthlyID, $updateLaboQuery); echo "<hr />";
	}
	// var_dump($lastAnnualID);echo "<hr />";echo "<hr />";
	if($comma){
		$time = time();
		$today = date("Y-m-d", $time);
		$updateLaboQuery .= ", ResultDate='{$today}', sampleTaken='{$time}', LabAgentSample='{$_SESSION['user']['UserID']}' WHERE ExamRecordID={$patientID}";
	}
	
	// echo $examRecordID."<br />";
	// var_dump($updateLaboQuery); echo "<hr />";echo "<hr />";echo "<hr />";
	saveData($updateLaboQuery, $con);
	return $patientID;
}

/**************************************** OTHER FUNCTION **********************/
ini_set('default_charset','utf-8');
date_default_timezone_set('Africa/Kigali');

/**************************************** USABLE VARIABLE *********************/
$institution_name = "Kigoma Health Center";
$institution_code = "";
$cbhi_name = "RUHANGO";
$location = "KIGOMA";
/**************************************** USABLE VARIABLE *********************/

/**************************************** MAIN FILE CONTETS LOADED HERE *******/
require_once @$path."main_file.php";
/**************************************** MAIN FILE CONTETS LOADED HERE *******/

/**************************************** HERE ALLOW THE USER TO Library installed using composer ******/
if(file_exists(__DIR__ . "/../vendor/autoload.php")){
	require_once __DIR__ . "/../vendor/autoload.php";
} else {
	die("
		<div style='color: orange; font-size: 30px; font-weight: bold; font-family: arial; text-align:center;'>
			Some Packages are missing
		</div>
		<div style='color: green; font-size: 28px; font-weight: bold; font-family: arial; text-align:center;'>
			Please contact Care Provider for assisstance.
		</div>
		<div style='border-top: 1px solid #000; font-size: 14px; padding: 20px; font-family: arial; text-align: center; font-weight: bold;'>
			Phone Number: (250) 7 26 22 73 94 or (250) 7 81 05 24 73 or (250) 7 85 75 83 50
		</div>
		");
}
// echo __DIR__;
// die();

//connect db by default
connectDB($con);
require_once @$path."mode_switching.php";
?>