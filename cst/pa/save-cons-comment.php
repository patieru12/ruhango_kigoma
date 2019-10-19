<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(!trim($_POST['data'])){
	echo "<span class=error-text>PLease Enter the Data to be used as consultation</span>";
	return;
}

// var_dump($_POST['data']); return;
// Check if the Document is Locked
$patientInfo = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																FROM co_records AS a
																INNER JOIN pa_records AS b
																ON a.PatientRecordID = b.PatientRecordID
																WHERE a.ConsultationRecordID = '".PDB($_POST['consultationID'],true,$con)."' AND
																	  b.DocStatus = 'locked'
																", $con), true, $con);
if(count($patientInfo) <= 0){

	// Check if this record Exist for th current Patient
	$records = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																FROM co_records AS a
																WHERE a.ConsultationRecordID = '".PDB($_POST['consultationID'],true,$con)."' &&
																	  a.comment = '{$_POST['data']}'
																", $con), true, $con);
	if(!is_array($records)){
		$consultationData = PDB($_POST['data'],true,$con);
		saveData("UPDATE co_records SET comment='{$consultationData}', ConsultantID='{{$_SESSION['user']['UserID']}}' WHERE ConsultationRecordID='".PDB($_POST['consultationID'],true,$con)."'", $con);
	}
} else{
	echo "<span class='error'>File Locked</span>";
}
$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*
															FROM co_records AS a
															WHERE a.ConsultationRecordID = '".PDB($_POST['consultationID'],true,$con)."'
															", $con), true, $con);
foreach($diagnostic AS $d){
	$transformed_Comment = str_replace("<br />", "\\n", $d['comment']);
	echo "
	<div>".$d['comment']."<br />".(count($patientInfo) <= 0 ?"
		<a style='color:blue; text-decoration:none;' href='#' onclick='$(\"#consultationDataToSave\").val(\"{$transformed_Comment}\"); return false;'>Edit</a> | 
		<a style='color:blue; text-decoration:none;' href='#' onclick='deleteComment(\"{$_POST['consultationID']}\");return false;'>Delete</a>":"")."
	</div>";
}
?>

<script type="text/javascript">
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
