<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
require_once "./../lib/db_function.php";
$records = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.DocID, pa_records.PatientRecordID, la_records.ExamNumber, la_records.ExamRecordID FROM pa_records, pa_info, co_records, la_records, la_price, la_exam, la_quarters WHERE pa_info.PatientID = pa_records.PatientID && co_records.PatientRecordID = pa_records.PatientRecordID && la_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID=pa_records.PatientRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_exam.ExamName ='" .PDB($_GET['exam'],true,$con). "' && la_records.ResultDate='".PDB($_GET['date'],true,$con)."' && la_records.ResultNumber='".($_GET['number'])."' && la_quarters.QuarterID = la_records.QuarterID && la_quarters.QuarterName='".(substr($_GET['date'],0,4).$quaters[substr($_GET['date'],5,2)])."' && la_records.ExamRecordID != '".PDB($_GET['existing_id'],true,$con)."' && pa_records.PatientRecordID = '".(PDB($_GET['key'],true,$con))."'",$con),$multirows=false,$con);
//echo $sql;
//var_dump($records);
if($records){
	echo "<a href='./?key={$records['DocID']}&patientid={$records['PatientRecordID']}&rcv_patient=".sha1('rcv_patient')."' target='_blank'><span class=error-text title='Click to open the previous owner' style='cursor:pointer;'>{$_GET['number']}</span></a>";
}
?>