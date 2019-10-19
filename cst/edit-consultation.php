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
// Get the Possible Consultation 
$consultations = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																c.ConsultationCategoryName AS ConsultationCategoryName,
																d.ConsultationPriceID AS ConsultationPriceID
																FROM se_name AS a
																INNER JOIN se_consultation AS b
																ON a.ServiceNameID = b.ServiceID
																INNER JOIN co_category AS c
																ON b.ConsulationID = c.ConsultationCategoryID
																INNER JOIN co_price AS d
																ON c.ConsultationCategoryID = d.ConsultationCategoryID
																INNER JOIN in_category AS e
																ON d.InsuranceCategoryID = e.InsuranceCategoryID
																INNER JOIN in_name AS f
																ON e.InsuranceCategoryID = f.CategoryID
																WHERE f.InsuranceNameID = '{$records['InsuranceNameID']}' && a.ServiceNameID = '{$records['ServiceNameID']}' && d.Status = 1 && b.Status = 1
																", $con), true, $con);
// echo "<div style='max-height:300px; overflow:auto;'><pre>"; var_dump($consultations); echo "</div>";
?>

Patient Name: <b><?= $records['patientName'] ?></b><br />
Age: <?= getAge($records['DateofBirth'],$notation=1, $current_date=$records['DateIn'])  ?><br />
Date of Birth: <?= $records['DateofBirth'] ?> <br />
Insurance: <?= $records['InsuranceName'] ?>
<hr />
Service Name: <?= $records['ServiceName'] ?> <br />
Service Code: <?= $records['ServiceCode'] ?> <br />
Consultation to be Used: <?= ($records['ConsultationCategoryName'] == "invisible"?"No Consultation":$records['ConsultationCategoryName']) ?> <br />
<?php
// echo $records['status'];
if($records['status'] == 1){
	echo "<hr />Consultation Service is paid<br />You can't Update it!";
	return;
}
?>
<form action="./save_edit_consultation.php" id="edit_consultation" method="POST">
	<input type="hidden" name="PatientRecordID" value="<?= $patientID ?>">
	<select name="newConsultation" class=txtfield1 style='font-size:16px; width:200px;'>
		<?php
		if(count($consultations) > 0){
			foreach($consultations AS $c){
				echo "<option value='{$c['ConsultationPriceID']}' ".($c['ConsultationPriceID'] == $records['ConsultationPriceID']?"selected":"").">".($c['ConsultationCategoryName'] == "invisible"?"No Consultation":$c['ConsultationCategoryName'])."</option>";
			}
		}
		?>
	</select>
	<input type="button" id="editConsultatioReceived" value="Save" class="flatbtn-blu" />
</form>
	

<span class=save_edits></span>
<script type="text/javascript">
	$('#editConsultatioReceived').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_edits").html('');
		$(".save_edits").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#edit_consultation").ajaxForm({ 
			target: '.save_edits'
		}).submit();
			
	});
</script>
