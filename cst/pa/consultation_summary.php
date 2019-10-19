<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
$consultationID = 0;
// var_dump($_GET);
if(preg_match("/\d/", $_GET['patientID'])){
	$patientID = $_GET['patientID'];
	$sql = "SELECT 	a.*,
					b.DocStatus
					FROM co_records AS a
					INNER JOIN pa_records AS b
					ON a.PatientRecordID = b.PatientRecordID
					WHERE a.PatientRecordID='{$patientID}'";
	// echo $sql;
	$consultation = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	
	
	if(is_array($consultation)){
		$consultationID = $consultation[0]['ConsultationRecordID'];
		foreach($consultation AS $d){
			// var_dump($d);
			if(trim($d['comment'])){
				$transformed_Comment = str_replace("<br />", "\\n", $d['comment']);
				echo "
				<div style='padding-bottom:5px;'>".str_replace("<br />", "; ",$d['comment'])."".($d['DocStatus'] != 'locked'?"  &nbsp;|&nbsp; 
					<a style='color:blue; text-decoration:none;' href='#' onclick='$(\"#consultationDataToSave\").val(\"{$transformed_Comment}\"); return false;'>Edit</a> | 
					<a style='text-decoration:none; color:red;' href='#' onclick='deleteComment(\"{$consultationID}\");return false;'>Delete</a>":"")."
				</div>";
			} else{
				echo "<span class=error-text>Summary Not found.</span>";
			}
		}
	} else{
		echo "<span class=error-text>No Summary Assigned to the current Patient</span>";
	}
} else{
	echo "<span class=error-text>No Patient is selected</span>";
}
?>
<script type="text/javascript">
	consultationID = <?= $consultation[0]['ConsultationRecordID'] ?>;
	function deleteComment(patientID){
		if(patientID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-cons-comment.php",
				data: "consultationID=" + patientID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#patientConsultation").html(result)
					//console.log(result);
					$("#consultationDataToSave").val("");
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}

	}
</script>
