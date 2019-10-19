<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";

// Check if this record Exist for th current Patient
$records = formatResultSet($rslt=returnResultSet("SELECT 	a.*
															FROM co_records AS a
															WHERE a.ConsultationRecordID = '".PDB($_POST['consultationID'],true,$con)."'
															", $con), true, $con);
if(!is_array($records)){
	
	saveData("UPDATE co_records SET comment='', ConsultantID='{{$_SESSION['user']['UserID']}}' WHERE ConsultationRecordID='".PDB($_POST['consultationID'],true,$con)."'", $con);
}
$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*
															FROM co_records AS a
															WHERE a.ConsultationRecordID = '".PDB($_POST['consultationID'],true,$con)."'
															", $con), true, $con);
foreach($diagnostic AS $d){
	$transformed_Comment = str_replace("<br />", "\\n", $d['comment']);
	echo "
	<div>".$d['comment']."<br />
		<a style='color:blue; text-decoration:none;' href='#' onclick='$(\"#consultationDataToSave\").val(\"{$transformed_Comment}\"); return false;'>Edit</a> | 
		<a style='color:blue; text-decoration:none;' href='#' onclick='deleteComment(\"{$_POST['consultationID']}\");return false;'>Delete</a>
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
