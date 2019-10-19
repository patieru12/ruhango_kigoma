<?php
session_start();
require_once "./../lib/db_function.php";
// var_dump($_GET)
$recordId 		= PDB($_GET['recordid'], true, $con);
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='./se_select.php?msg=select the register please and service'; </script>";
	return;
}
// var_dump($_GET);
if(!returnSingleField("SELECT ConsultationRecordID FROM co_records WHERE ConsultationRecordID='{$recordId}'", "ConsultationRecordID", true, $con)){
	?>
	<span style="color:red">Error</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		alert("No Patient to be registered\nPlease select one.");
	</script>
	<?php
} else if(!$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con)){
	?>
	<span style="color:red">Error</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		alert("No register associated to the your profile\nPlease logout and login again to select available one!");
	</script>
	<?php
} else{
	$registers = formatResultSet($rslt=returnResultSet("SELECT * FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", $con), true, $con);
	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory,
																c.Name AS patientName,
																c.DateofBirth AS DateofBirth,
																b.InsuranceName AS InsuranceName,
																d.TypeofPayment AS TypeofPayment,
																d.ValuePaid AS ValuePaid,
																c.sex AS patientGender,
																b.InsuranceNameID AS InsuranceNameID,
																c.phoneNumber AS phoneNumber,
																e.VillageName AS VillageName,
																f.CellName AS CellName,
																g.SectorName AS SectorName,
																h.DistrictName AS DistrictName,
																c.FamilyCode AS FamilyCode
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN pa_info AS c
																ON a.PatientID = c.PatientID
																INNER JOIN in_price AS d
																ON b.InsuranceNameID = d.InsuranceNameID
																INNER JOIN ad_village AS e
																ON a.VillageID = e.ViillageID
																INNER JOIN ad_cell AS f
																ON e.CellID = f.CellID
																INNER JOIN ad_sector AS g
																ON f.SectorID = g.SectorID
																INNER JOIN ad_district AS h
																ON g.DistrictID = h.DistrictID
																INNER JOIN co_records AS i
																ON a.PatientRecordID = i.PatientRecordID
																WHERE i.ConsultationRecordID ='{$recordId}'
																", $con), false, $con);
	?>
	<span style="font-size:16px;">
		Name: <b><?= $patient['patientName'] ?></b><br />
		Insurance: <b><?= $patient['InsuranceName'] ?></b> Card ID: <b><?= $patient['InsuranceCardID'] ?></b>
	</span> <br />
	<h2>Add Number registered in One of following registers.</h2>
	<span class=save_adds></span>
	<form action='./pa/save_register.php' id='add_additional' method='POST'>
		<input type="hidden" name="recordid" value="<?= $recordId ?>">
		<?php
		if(count($registers) > 0){
			foreach($registers as $in){
				/*if( ($i++) % 5 == 0){
					if($i > 1){
						echo "</tr>";
					}
					echo "<tr>";
				}*/
				echo "<label title='{$in['name']}' style='padding:0 10px;'><input type=radio name='registerId' value='{$in['id']}' id='{$in['registerCode']}'>{$in['name']}: {$in['registerCode']}</label><br />";
				
			} 
		}
		?>
		Register Number:
		<input type="text" class='txtfield1' name='number'><br />&nbsp;
		<br />&nbsp;
		<input type='submit' id='save' name='addFees' value='Save & Close' class='flatbtn-blu'>
	</form>
	<script type="text/javascript">
		$('#save').click(function(e){ 
			e.preventDefault();
			//$('#save').attr("desabled",":true");
			$(".save_adds").html('');
			$(".save_adds").html('<img src="../images/loading.gif" alt="Saving"/>'); 
			$("#add_additional").ajaxForm({ 
				target: '.save_adds'
			}).submit();
				
		});
	</script>
	<?php
}
?>