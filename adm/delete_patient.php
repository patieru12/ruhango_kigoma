<?php
session_start();
//disable the time for this request
set_time_limit(0);

//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("adm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$sp_condition = "";
$added = false;
if(@$_POST['dob']){
	$added = true;
	$sp_condition .= " && ( ";
	$sp_condition .= " pa_info.DateOfBirth='".(PDB($_POST['dob'],true, $con))."'";
}

if(@$_POST['datein']){
	if(!$added){
		$added = true;
		$sp_condition .= " && ( ";
	}else
		$sp_condition .= " || ";
	$sp_condition .= "pa_records.DateIn='".(PDB($_POST['datein'],true, $con))."'";
}

if(@$_POST['vill']){
	if(!$added){
		$added = true;
		$sp_condition .= " && ( ";
	}else
		$sp_condition .= " || ";
	$sp_condition .= "ad_village.VillageName='".(PDB((strtolower($_POST['vill'])=="empty"?"":$_POST['vill']),true, $con))."'";
}

if(@$_POST['cell']){
	if(!$added){
		$added = true;
		$sp_condition .= " && ( ";
	}else
		$sp_condition .= " || ";
	$sp_condition .= "ad_cell.CellName='".(PDB((strtolower($_POST['cell'])=="empty"?"":$_POST['cell']),true, $con))."'";
}
//select patient that match the given condition and prepare the elimination for the system 

if($added)
	$sp_condition .= " )";

//echo $sp_condition;
if(strlen($_POST['delete'])){
	$undeleted = 0;
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_records.PatientRecordID, pa_info.*, ad_village.*, ad_cell.*, ad_sector.*, ad_district.* from pa_records, pa_info, ad_village, ad_cell, ad_sector, ad_district WHERE pa_info.VillageID=ad_village.ViillageID && ad_village.CellID=ad_cell.CellID && ad_cell.SectorID=ad_sector.SectorID && ad_sector.DistrictID=ad_district.DistrictID && pa_records.PatientID = pa_info.PatientID && pa_records.InsuranceNameID='".PDB($_POST['delete'],true,$con)."'".$sp_condition,$con),$multirows=true,$con);
	//var_dump($patients);
	//echo count($patients);
	//loop all found patient and delete the active on if possible
	for($i=0;$i<count($patients); $i++){
		//check if the patient has a record in mu_tm if yes skip him
		if(isDataExist("SELECT TicketID FROM mu_tm WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'", $con)){
			//increment the undeleted counter and continue to the next patient
			$undeleted++;
			continue;
		}
		
		//check the service table
		if(isDataExist("SELECT ServiceRecord FROM se_records WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'", $con)){
			//increment the undeleted counter and continue to the next patient
			$undeleted++;
			continue;
		}
		
		if(isDataExist("SELECT PatientRecordID FROM co_records WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'", $con)){
			//increment the undeleted counter and continue to the next patient
			$undeleted++;
			continue;
		}
		//delete pa_records now
		saveData("DELETE FROM pa_records WHERE PatientRecordID='{$patients[$i]['PatientRecordID']}'",$con);
		//delete pa_insurance_cards now
		saveData("DELETE FROM pa_insurance_cards WHERE PatientID='{$patients[$i]['PatientID']}'",$con);
		//delete pa_info now
		//check if the patient is no longer in use by some records
		
		if(isDataExist("SELECT PatientRecordID FROM pa_records WHERE PatientID='{$patients[$i]['PatientID']}'", $con)){
			//increment the undeleted counter and continue to the next patient
			$undeleted++;
			continue;
		}
		saveData("DELETE FROM pa_info WHERE PatientID='{$patients[$i]['PatientID']}'",$con);
		
	}
	$deleted = count($patients) - $undeleted;
	echo "<span class=error-text>{$deleted} Patient".($deleted > 1?"s Are":" Is")." Deleted</span>";
	//echo count($patients);
	return; 
} else{
	echo "<span class=error-text>Unable to select Patient to be deleted!</span>";
	return; 
}
?>