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
	echo "<span class=error-text>PLease Enter the Data to be used as Diagnostic</span>";
	return;
}

// Check if the Diagnostic Exist in the System Before
$diagnosticData = PDB($_POST['data'], true, $data);
$diagID = formatResultSet($rslt=returnResultSet("SELECT a.*
														FROM co_diagnostic AS a
														WHERE a.English = '{$diagnosticData}' ||
															  a.DiagnosticName = '{$diagnosticData}'
														", $con), false, $con);
// var_dump($diagID);
if(!is_array($diagID)){
	// Add the diagnostic to the List for later Selection
	saveData("INSERT INTO co_diagnostic SET DiagnosticName='".PDB($_POST['data'], true, $data)."',  English='', DiagnosticCode='', Code='', PECIME='', DiagnosticCategoryID=10, Reported=0, SISCode=''  ", $con);
}
$diagID = formatResultSet($rslt=returnResultSet("SELECT a.*
														FROM co_diagnostic AS a
														WHERE a.English = '".PDB($_POST['data'], true, $data)."' ||
															  a.DiagnosticName = '".PDB($_POST['data'], true, $data)."'
														", $con), false, $con);
// var_dump($diagID);
$consultationIDData = PDB($_POST['consultationID'],true,$con);
// Check if this record Exist for th current Patient
$records = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.English AS diagnosticName
															FROM co_diagnostic_records AS a
															INNER JOIN co_diagnostic AS b
															ON a.DiagnosticID = b.DiagnosticID
															WHERE a.ConsulationRecordID = '{$consultationIDData}' &&
																  a.DiagnosticID = '{$diagID['DiagnosticID']}'
															", $con), true, $con);
if(isset($_POST['diagnosticRecordID']) && is_numeric($_POST['diagnosticRecordID'])){
	$co_record_id_id = PDB($_POST['consultationID'],true,$con);
	$dgRecordID 	 = PDB($_POST['diagnosticRecordID'], true, $con);
	$episode 		 = PDB($_POST['episode'], true, $con);
	saveData("UPDATE co_diagnostic_records SET ConsulationRecordID='{$co_record_id_id}', DiagnosticID={$diagID['DiagnosticID']}, DiagnosticType=1, CaseType='{$episode}', PECIME=0 WHERE DiagnosticRecordID='{$dgRecordID}'", $con);
	saveData($sql = "UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}' WHERE ConsultationRecordID='{$consultationIDData}'", $con);
	
} else if(!is_array($records)){
	$episode 		 = PDB($_POST['episode'], true, $con);
	saveData("INSERT INTO co_diagnostic_records SET ConsulationRecordID='".PDB($_POST['consultationID'],true,$con)."', DiagnosticID={$diagID['DiagnosticID']}, DiagnosticType=1, CaseType='{$episode}', PECIME=0", $con);
	saveData($sql = "UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}' WHERE ConsultationRecordID='{$consultationIDData}'", $con);
	// echo $sql;
}
$diagnostic = formatResultSet($rslt=returnResultSet("SELECT a.*,
															b.DiagnosticName AS diagnosticName
															FROM co_diagnostic_records AS a
															INNER JOIN co_diagnostic AS b
															ON a.DiagnosticID = b.DiagnosticID
															WHERE a.ConsulationRecordID = '".PDB($_POST['consultationID'],true,$con)."'
															", $con), true, $con);
foreach($diagnostic AS $d){
	echo "
		<div>
			<a style='color:blue; text-decoration:none;' title='Click to edit' href='' onclick='editDiagnostic(\"{$d['DiagnosticRecordID']}\", \"{$d['diagnosticName']}\", {$d['CaseType']}); return false;'>".$d['diagnosticName']."</a> | 
			<a style='color:blue; text-decoration:none; color:red;' href='' onclick='deleteDiagnostic(\"{$d['DiagnosticRecordID']}\");return false;'>Delete</a>
		</div>";
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