<?php
session_start();
//var_dump($_SESSION);
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$records = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.{$_GET['field']},
																a.PatientRecordID
																FROM pa_records AS a
																WHERE a.PatientRecordID = '".PDB($_GET['PatientRecordID'],true,$con)."'
																",$con),$multirows=false,$con);
?>
<span class=save_edits></span>
<form action="./pa/save_edit_vital_sign.php" id=edit_address method=post>
	<input type=hidden name=patient value='<?= $records['PatientRecordID'] ?>' />
	<input type=hidden name=field value='<?= $_GET['field'] ?>' />
	<table>
		<tr>
			<td><?= $vitalSignFieldList[$_GET['field']]['display'] ?></td>
		</tr>
		<tr>
			<td><input type=text value='<?= $records[$_GET['field']] ?>' name='value' autocomplete="off" id=village class="txtfield1" style='width:120px' /></td>
			
		</tr>
		<tr>
			<td colspan=4><div class='address_search' style='max-height:300px; overflow:auto; font-size:10px;'></div></td>
		</tr>
	</table>
	<input type=submit id=save class="flatbtn-blu" name=update_address value='Update' />
</form>

<script type="text/javascript">
	$('#save').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_edits").html('');
		$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#edit_address").ajaxForm({ 
			target: '.save_edits'
		}).submit();
			
	});
</script>