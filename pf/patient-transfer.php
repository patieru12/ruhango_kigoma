<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_GET); die();
$patientRecord = PDB($_GET['records'], true, $con);
//select patient Address Information now
$records = formatResultSet($rslt=returnResultSet($sql="SELECT 	DISTINCT pa_records.PatientRecordID, 
																ad_village.*, 
																ad_cell.*, 
																ad_sector.*, 
																ad_district.*,
																pa_info.*,
																pa_records.InsuranceCardID AS InsuranceCardID,
																in_name.InsuranceName AS InsuranceName
																FROM pa_records, 
																ad_village, 
																ad_cell, 
																ad_sector, 
																ad_district,
																pa_info,
																in_name
																WHERE pa_records.VillageID=ad_village.ViillageID && 
																	  ad_village.CellID=ad_cell.CellID && 
																	  ad_cell.SectorID=ad_sector.SectorID && 
																	  ad_sector.DistrictID=ad_district.DistrictID && 
																	  pa_records.PatientID = pa_info.PatientID &&
																	  in_name.InsuranceNameID = pa_records.InsuranceNameID &&
																	  pa_records.PatientRecordID = '{$patientRecord}'
																",$con),$multirows=false,$con);
//var_dump($records);

$activeService = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT a.* 
																	FROM se_name AS a
																	INNER JOIN se_records AS b
																	ON a.ServiceNameID = b.ServiceNameID
																	WHERE a.Status=1 && b.PatientRecordID = '{$patientRecord}'
																	ORDER BY ServiceCode ASC
																	",$con),$multirows=false,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.Status=1 && se_name.ServiceNameID NOT IN({$activeService['ServiceNameID']}) ORDER BY ServiceCode ASC",$con),$multirows=true,$con);
?>
<style>
	.list tr td,.list tr th{
		font-size:12px;
	}
</style>
Name: <b><?= $records['Name'] ?></b><br />
House Holder: <b><?= $records['FamilyCode'] ?></b><br />
Insurance Name: <b><?= $records['InsuranceName'] ?></b><br />
Insurance Card ID: <b><?= $records['InsuranceCardID'] ?></b><br />
Current Service: <b><?= $activeService['ServiceName']?> - <?= $activeService['ServiceCode']?></b>
<hr />
<span class=save_edits></span>
<form action="./transfer_patient.php" id=edit_address method=post>
	<input type=hidden name=patient value='<?= $records['PatientRecordID'] ?>' />
	<table>
		<tr>
			<td>Next Service</td>
		</tr>
		<tr>
			<td>
				<?php 
				if($service){
					$i=0;
					echo "<select class='txtfield1' name='newService'><option value=''>--select service--</option>";
					foreach($service as $in){
						?>
						<option value="<?= $in['ServiceNameID'] ?>"><?= $in['ServiceCode'] ?></option>
						<?php
					}
					echo "</select>";
				}
				?>
			</td>
		</tr>
	</table>
	<input type=submit id=save class="flatbtn-blu" name=update_address value='Transfer' />
</form>
<script>
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