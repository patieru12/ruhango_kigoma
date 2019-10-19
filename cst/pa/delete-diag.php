<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($diagID);
// Check if this record Exist for th current Patient

$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*,
															b.DiagnosticName AS diagnosticName
															FROM co_diagnostic_records AS a
															INNER JOIN co_diagnostic AS b
															ON a.DiagnosticID = b.DiagnosticID
															WHERE a.DiagnosticRecordID = '".PDB($_POST['diagnosticRecordID'],true,$con)."'
															", $con), true, $con);
$consultationIDData = $diagnostic[0]['ConsulationRecordID'];

if(isset($_POST['diagnosticRecordID']) && is_numeric($_POST['diagnosticRecordID'])){
	$dgRecordID 	 = PDB($_POST['diagnosticRecordID'], true, $con);
	saveData("DELETE FROM co_diagnostic_records WHERE DiagnosticRecordID='{$dgRecordID}'", $con);
	saveData($sql = "UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}' WHERE ConsultationRecordID='{$consultationIDData}'", $con);
	
}
$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*,
															b.DiagnosticName AS diagnosticName
															FROM co_diagnostic_records AS a
															INNER JOIN co_diagnostic AS b
															ON a.DiagnosticID = b.DiagnosticID
															WHERE a.ConsulationRecordID = '{$consultationIDData}'
															", $con), true, $con);

if($diagnostic){
	foreach($diagnostic AS $d){
		echo "
			<div>
				<a style='color:blue; text-decoration:none;' title='Click to edit' href='' onclick='editDiagnostic(\"{$d['DiagnosticRecordID']}\", \"{$d['diagnosticName']}\", {$d['CaseType']} ); return false;'>".$d['diagnosticName']."</a> | 
				<a style='color:blue; text-decoration:none; color:red;' href='' onclick='deleteDiagnostic(\"{$d['DiagnosticRecordID']}\");return false;'>Delete</a>
			</div>";
	}
}
?>

<script type="text/javascript">
	function editDiagnostic(DiagnosticRecordID, Diagnostic, episode){
		$("#DiagnosticRecordID").val(DiagnosticRecordID);
		$("#diagnosticData").val(Diagnostic);

		var myId = episode?"oldCaseEpisode":"newCaseEpisode";
		$("#" + myId).prop("checked", ":true");
	}
	function deleteDiagnostic(DiagnosticRecordID){
		if(DiagnosticRecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-diag.php",
				data: "consultationID=" + consultationID + "&diagnosticRecordID=" + DiagnosticRecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#patientDiagnostic").html(result)
					//console.log(result);
					$("#diagnosticData").val("");
					$("#DiagnosticRecordID").val("");
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>