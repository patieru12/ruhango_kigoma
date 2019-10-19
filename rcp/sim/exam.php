<?php
//var_dump($_GET);
$_GET = $_POST;
require_once "../../lib/db_function.php";
//check for exam number conflict
$quartername = substr($_GET['examdate'],0,4).$quaters[substr($_GET['examdate'],5,2)];
//echo $quartername;
$records = formatResultSet($rslt=returnResultSet($sql="SELECT pa_records.DocID, pa_records.PatientRecordID, la_records.ExamNumber, la_records.ExamRecordID FROM pa_records, pa_info, co_records, la_records, la_price, la_exam, la_quarters WHERE pa_info.PatientID = pa_records.PatientID && co_records.PatientRecordID = pa_records.PatientRecordID && la_records.ConsultationRecordID = co_records.ConsultationRecordID && co_records.PatientRecordID=pa_records.PatientRecordID && la_records.ExamPriceID = la_price.ExamPriceID && la_price.ExamID = la_exam.ExamID && la_exam.ExamName ='" .PDB($_GET['examname'],true,$con). "' && la_records.ExamNumber='".($_GET['examid'])."' && la_records.ExamRecordID != '".PDB($_GET['existing_id'],true,$con)."' && la_records.QuarterID = la_quarters.QuarterID && la_quarters.QuarterName='{$quartername}' ",$con),$multirows=false,$con);
//echo $sql;
//var_dump($records)
if($records){
	echo $_GET['examname'].": ".$_GET['examid']." <span class=error-text>"."<a href='./reprint.php?key={$records['DocID']}&patientid={$records['PatientRecordID']}&rcv_patient=".sha1('rcv_patient')."' target='_blank'><span class=error-text title='Click to open the previous owner' style='cursor:pointer;'>Conflict</span></a>"."</span>";
} else{
	//select the corresponding amount
	//var_dump($_GET);
	$records = formatResultSet($rslt=returnResultSet("SELECT la_price.Amount FROM la_price, la_exam WHERE la_price.ExamID = la_exam.ExamID && la_exam.ExamName='{$_GET['examname']}' && la_price.InsuranceTypeID='{$_GET['insurance']}' && la_price.Date <= '{$_GET['examdate']}' ORDER BY Date DESC LIMIT 0, 1",$con),false,$con);
	//var_dump($records);
	if($records){
		if ((strtolower($_GET['examname']) == "ge" || strtolower($_GET['examname']) == "tdr") && (!preg_match("/neg/",strtolower(trim($_GET['result']))) )){
			?>
			<script>
				$("#lock_malaria").val("1");
			</script>
			<?php
		}
		echo $_GET['examname'].": ".$_GET['examid']." ".$_POST['result']." <span class=success>{$records['Amount']}</span>";
	}
}
?>
<br />