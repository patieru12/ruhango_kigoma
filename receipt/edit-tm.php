<?php
session_start();
require_once "../lib/db_function.php";
// var_dump($_GET);
$patientID = PDB($_GET['patientID'],true,$con);
// var_dump($_GET);
$records = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															e.ServiceName AS ServiceName,
															e.ServiceCode AS ServiceCode,
															COALESCE(f.ConsultationCategoryName, '') AS ConsultationCategoryName,
															COALESCE(f.ConsultationPriceID, 0) AS ConsultationPriceID,
															d.ServiceNameID AS ServiceNameID,
															COALESCE(f.status, 0) AS status
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN se_records AS d
															ON a.PatientRecordID = d.PatientRecordID
															INNER JOIN se_name AS e
															ON d.ServiceNameID = e.ServiceNameID
															LEFT JOIN (
																SELECT 	a.ConsultationPriceID AS ConsultationPriceID,
																		c.ConsultationCategoryName AS ConsultationCategoryName,
																		a.PatientRecordID AS PatientRecordID,
																		a.status AS status
																		FROM co_records AS a
																		INNER JOIN co_price AS b
																		ON a.ConsultationPriceID = b.ConsultationPriceID
																		INNER JOIN co_category AS c
																		ON b.ConsultationCategoryID = c.ConsultationCategoryID
																		WHERE a.PatientRecordID = '{$patientID}'
															) AS f
															ON a.PatientRecordID = f.PatientRecordID
															WHERE a.PatientRecordID ='{$patientID}'
															", $con), false, $con);
// var_dump($records);

?>

Patient Name: <b><?= $records['patientName'] ?></b><br />
Age: <?= getAge($records['DateofBirth'],$notation=1, $current_date=$records['DateIn'])  ?><br />
Date of Birth: <?= $records['DateofBirth'] ?> <br />
Insurance: <?= $records['InsuranceName'] ?>
<hr />
Service Name: <?= $records['ServiceName'] ?> <br />
Service Code: <?= $records['ServiceCode'] ?> <br />
Category Code: <?= $records['FamilyCategory'] ?> <br />
<?php
// echo $records['status'];
if($records['status'] == 1){
	// echo "<hr />Consultation Service is paid<br />You can't Update it!";
	// return;
}

if($records['FamilyCategory'] == 1){
	?>
	<script type="text/javascript">
		var nowCatOne = true;
	</script>
	<?php
} else {
	?>
	<script type="text/javascript">
		var nowCatOne = false;
	</script>
	<?php
}
?>

<form action="./save_edit_tm.php" id="edit_tm" method="POST">
	<input type="hidden" name="PatientRecordID" value="<?= $patientID ?>">
	<input type="hidden" name="Date" value="<?= $records['DateIn'] ?>">
	<input type="hidden" name="ins" value="<?= $records['InsuranceName'] ?>">
	<label style='margin-left:20px;'>
		<input type=radio id='paid' onclick='if(nowCatOne){alert("Category 1 never pay TM"); $("#cat").prop("checked", "true"); return false } else { return true; }' name='tm' value='200' />200 RWF TM Paid
	</label><br />
	<input type=hidden id=receipt_number name=receipt_number class=txtfield1 style='width:160px;' value='78885454' placeholder='Enter Amount' />
	<!-- <label style='margin-left:20px;'>
		<input type=radio id='cmp' name='tm' value="COMPASSION" />Compassion
	</label><br />
	<label style='margin-left:20px;'>
		<input type=radio id='dettes' name='tm' value="DETTES" />Dettes
	</label><br />
	<label style='margin-left:20px;'>
		<input type=radio id='not_paid' name='tm' value="INDIGENT" />Indigent
	</label><br />
	<label style='margin-left:20px;'>
		<input onclick='if(!$("#pst_").prop("checked")){ alert("Pour PST Seulement"); return false; };' type=radio id='pst' name='tm' value="PANSEMENT" />AC PST
	</label><br /> -->
	<label style='margin-left:20px;'>
		<input onclick='' type=radio id='cat' name='tm' value="CATEGORY" />CAT 1 & 2
	</label><br />
	<!-- <label style='margin-left:20px;'>
		<input onclick='' type=radio id='ac' name='tm' value="AC" />AC
	</label><br /> -->
	<input type="button" id="editTMReceived" value="Save" class="flatbtn-blu" />
</form>
	

<span class=save_edits></span>
<script type="text/javascript">
	$('#editTMReceived').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_edits").html('');
		$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#edit_tm").ajaxForm({ 
			target: '.save_edits'
		}).submit();
			
	});
</script>
