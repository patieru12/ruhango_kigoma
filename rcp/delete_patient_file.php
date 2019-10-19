<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST); die;
$patient_record_id = PDB($_POST['record_id'],true,$con);
$delete_ac = "DELETE FROM ac_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_ac;
saveData($delete_ac,$con);
$delete_cn = "DELETE FROM cn_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_cn;
saveData($delete_cn,$con);
$delete_ho = "DELETE FROM ho_record WHERE RecordID='{$patient_record_id}'";
echo $delete_ho;
saveData($delete_ho,$con);
$delete_mu = "DELETE FROM mu_tm WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_mu;
saveData($delete_mu,$con);
/* $delete_pst = "DELETE FROM pst_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_pst; */
$delete_se = "DELETE FROM se_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_se;
saveData($delete_se,$con);
$delete_sy = "DELETE FROM sy_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_se;
saveData($delete_sy,$con);

//select the consltation record 
//$cons_record = returnSingleField(,"ConsultationRecordID",$data=true, $con);
$cons_record = returnAllData("SELECT * FROM co_records WHERE PatientRecordID='{$patient_record_id}'",$con);
//var_dump($cons_record);
//loop all found cons_record
if($cons_record){
	foreach($cons_record as $co_records){
		
		$la_records = returnAllData("SELECT * FROM la_records WHERE ConsultationRecordID='{$co_records['ConsultationRecordID']}'",$con);
		if($la_records){
			foreach($la_records as $laa_records){
				$delete_la_result = "DELETE FROM la_result_record WHERE ExamRecordID='{$laa_records['ExamRecordID']}'";
				echo $delete_la_result;
				saveData($delete_la_result,$con);

			}
		}
		$delete_la = "DELETE FROM la_records WHERE ConsultationRecordID='{$co_records['ConsultationRecordID']}'";
		echo $delete_la;
		saveData($delete_la,$con);
		
		$md_record = returnAllData($s = "SELECT * FROM md_records WHERE ConsultationRecordID='{$co_records['ConsultationRecordID']}'", $con);
		if($md_record){
			foreach($md_record as $md_r){
				$delete_md_presc = "DELETE FROM md_prescription WHERE MedecineRecordID='{$md_r['MedecineRecordID']}'";
				echo $delete_la_result;
				saveData($delete_md_presc,$con);

			}
		}
		$delete_md_record = "DELETE FROM md_records WHERE ConsultationRecordID='{$co_records['ConsultationRecordID']}'";
		echo $delete_md_record;
		saveData($delete_md_record,$con);
		
		$delete_diag= "DELETE FROM co_diagnostic_records WHERE ConsulationRecordID='{$co_records['ConsultationRecordID']}'";
		echo $delete_diag;
		saveData($delete_diag,$con);
	}
}

$delete_co= "DELETE FROM co_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_co;
saveData($delete_co,$con);
$delete_pa= "DELETE FROM pa_records WHERE PatientRecordID='{$patient_record_id}'";
echo $delete_pa;
saveData($delete_pa,$con);
//select medicines information
//delete the index in the session data
/*if(isset($_SESSION['data_display'][@$_POST['index']]))
	unset($_SESSION['data_display'][$_POST['index']]);*/
?>