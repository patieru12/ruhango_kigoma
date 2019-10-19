<?php
session_start();
require_once "../lib/db_function.php";
$examId = $_POST['ExamID'];
$date = $_POST['ResultDate'];
$insurance=returnSingleField($sql="SELECT in_name.categoryID FROM pa_records,in_name WHERE pa_records.insurancenameID=in_name.insurancenameID && pa_records.PatientRecordID='{$_POST['patientRecordID']}'",$field="categoryID",$data=true, $con);
$price=returnSingleField($sql="SELECT ExamPriceID FROM la_price WHERE ExamID={$examId} && InsurancetypeID={$insurance} && date <='{$date}' ORDER by Date DESC limit 0,1",$field="ExamPriceID",$data=true, $con);
$q = explode("-",$_POST['ResultDate'])[0];
$q .= $quaters[explode("-",$_POST['ResultDate'])[1]];

$max=returnSingleField($sql="SELECT max(ExamNumber) as ExamNumber FROM la_records,la_price WHERE la_records.ExampriceID=la_price.ExamPriceID && la_price.ExamID={$_POST['ExamID']} && la_records.ExamNumber LIKE('{$q}%')",$field="ExamNumber",$data=true, $con);
//var_dump($q);
//var_dump($max);

$number = (int) str_replace($q,"",$max);
//echo $number;
$new_number = $q.(++$number);
echo $new_number;

if(!$cons = returnSingleField("SELECT * FROM co_records WHERE PatientRecordID='{$_POST['patientRecordID']}' && Date='$date'","ConsultationRecordID",true, $con))
	$cons=saveAndReturnID($insert="INSERT INTO co_records SET PatientRecordID='{$_POST['patientRecordID']}',Date='$date',ConsultationPriceID='41'",$con);

$cons_1=saveAndReturnID($insert_1="INSERT INTO la_records SET ExamNumber='$new_number',ExamPriceID='$price',ConsultationRecordID='$cons',ResultDate='$date'",$con);
//var_dump($cons_1);
//now select all information related to the current insert value
$data = formatResultSet($rslt=returnResultSet($sql="SELECT pa_info.Name, pa_info.DateOfBirth, la_exam.ExamName, la_records.ExamNumber, la_records.ResultDate FROM pa_info, pa_records, co_records, la_records, la_price, la_exam WHERE pa_info.PatientID = pa_records.PatientID && pa_records.PatientRecordID = co_records.PatientRecordID && co_records.ConsultationRecordID = la_records.ConsultationRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_records.ExamRecordID='{$cons_1}'",$con),$multirows=false,$con);
echo "<div style='max-width:300px; border:1px solid #000; font-size:18px; color:green;'>
Patient: {$data['Name']}<br />";
echo "Exam: {$data['ExamName']}<br />";
echo "Number: ".substr($data['ExamNumber'],6)."<br />";
echo "Date: {$data['ResultDate']}</div>";
?>