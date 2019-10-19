<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
$status = returnSingleField("SELECT Status FROM pa_records AS a WHERE a.PatientRecordID = '{$_GET['patientID']}'","Status",true,$con);
$sql = "SELECT 	a.PatientRecordID AS PatientRecordID,
				b.ConsultantID AS ConsultantID
				FROM pa_records AS a 
				INNER JOIN co_records AS b
				ON a.PatientRecordID = b.PatientRecordID
				WHERE a.PatientRecordID = '{$_GET['patientID']}'";
$r = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=false,$con);
// var_dump($r, $_SESSION['user']['UserID']);
if(isset($messages[$status]) && trim($messages[$status])){
	echo $messages[$status].($_SESSION['user']['UserID'] == $r['ConsultantID']?" <a style='color:red; text-decoration:none;' href='#' onclick='removeTransfer(\"{$r['PatientRecordID']}\");return false;'>Delete</a>":"")."<br />&nbsp;";
} /*else{
	echo "Unspecified status <br />&nbsp;";
}*/
?>
<script type="text/javascript">
	$("#patientIDRecords").val("<?= $_GET['patientID'] ?>");
	function removeTransfer(ConsultationRecordID){
		if(ConsultationRecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-transfer.php",
				data: "ConsultationRecordID=" + ConsultationRecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedDecision").html(result)
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>