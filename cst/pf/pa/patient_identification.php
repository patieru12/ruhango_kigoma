<?php
session_start();
require_once "../../../lib/db_function.php";
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='../../se_select.php?msg=select the register please and service'; </script>";
	return;
}
// var_dump($_GET);
$patientID = $_GET['patientID'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															COALESCE(a.serialNumber, '') AS RegisterNumber,
															COALESCE(b.nationalId,'') AS nationalId,
															COALESCE(a.dateIn, '') AS startingDate,
															COALESCE(c.villageId,'') AS villageId,
															COALESCE(c.cellId,'') AS cellId,
															COALESCE(c.sectorId,'') AS sectorId,
															COALESCE(c.districtId,'') AS districtId,
															b.phoneNumber AS phoneNumber,
															b.DateofBirth AS DateofBirth,

															b.sex AS patientGender,
															COALESCE(b.martialStatus,'') AS martialStatus,

															b.Name AS patientName,

															COALESCE(a.lastDelivery,'') AS lastDelivery,
															a.accompaigned AS accompaigned,
															a.pfBeforeQuitMAT AS pfBeforeQuitMAT,
															IFNULL(a.usedMethodId,0) AS pfUsedBefore,
															COALESCE(d.name,'') AS usedMethodName,
															a.hivStatus AS hivStatus,
															COALESCE(a.lastHIVTest, '') AS lastHIVTest,
															a.counselledForHIV AS counselledForHIV,
															a.referedFoHIVTest AS referedFoHIVTest,

															a.dateIn AS DateIn,
															NULL AS lastServiceDate

															FROM pf_user AS a
															INNER JOIN pa_info AS b
															ON a.patientId = b.PatientID
															LEFT JOIN (
																SELECT 	c.ViillageID AS villageId,
																		d.CellID AS cellId,
																		e.SectorID AS sectorId,
																		f.DistrictID AS districtId
																		FROM pf_user AS a
																		INNER JOIN pa_info AS b
																		ON a.patientId = b.PatientID
																		INNER JOIN ad_village AS c
																		ON b.VillageID = c.ViillageID
																		INNER JOIN ad_cell AS d
																		ON c.CellID = d.CellID
																		INNER JOIN ad_sector AS e
																		ON d.SectorID = e.SectorID
																		INNER JOIN ad_district AS f
																		ON e.DistrictID = f.DistrictID
																		WHERE a.id ='{$patientID}'
															) AS c
															ON b.VillageID = c.villageId
															LEFT JOIN pf_method AS d
															ON a.usedMethodId = d.id
															WHERE a.id ='{$patientID}'
															", $con), false, $con);

// echo "<pre>";var_dump($patient);
// var_dump($patient['RegisterNumber']);
/*if(!$patient['RegisterNumber'] && $patient['ServiceCode'] != 'PST'){
	?>
	<a id="askForRegisterID" href="./assign_register.php?recordid=<?= $patient['ConsultationRecordID'] ?>" rel="#overlay2">Register Info</a>
	<script type="text/javascript">
		$("#mainInput").css("opacity", "0");
		setTimeout(function(e){
			$("#askForRegisterID")[0].click();
		}, 500);
	</script>
	<?php
}*/

?>
<style type="text/css">
	.patientIdentification td{
		vertical-align: middle;
	}
</style>
<table style="width: 100%"; border="1" class="patientIdentification <?= strtolower($patient['InsuranceName']) == 'private'?"private":""?>">
	<tbody>
		<tr>
			<td style=" text-align: center; padding: 0px; background-color: #4f94cf; border:1px solid #4f94cf; color:#fff;" id="registerNumber">
				SN:<br /><?= $patient['RegisterNumber'] ?>
			</td>
			<td style="text-align: center;">NID:<br /><b><?= $patient["nationalId"] ?></b></td>
			<td style="text-align: center;">Reg.Date:<br /><b><?= $patient["startingDate"] ?></b></td>
			<td style="text-align: center;">Orgin:<br /><?= in_array($patient['cellId'], $zone_cells)?"Z":(in_array($patient['districtId'], $zone_districts)?"HZ":"HD") ?></b></td>
			<td style="text-align: center;">Phone Number:<br /><b><?= $patient["phoneNumber"] ?></b></td>
			<td style="text-align: center;">Age: <b><?= getAge($patient['DateofBirth'],$notation=1, $current_date=($patient['lastServiceDate']?$patient['lastServiceDate']:date("Y-m-d", time() ) ) )  ?></b><br /><b><?= $patient['DateofBirth'] ?>&nbsp;&nbsp;</b></td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: center;">
				Gender: <b><?= $patient['patientGender'] ?></b>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Martial Status: <b><?= $patient['martialStatus'] ?></b></td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: center;">Name: <b><?= $patient['patientName'] ?></b></td>
		</tr>
		<tr>
			<td colspan="6">
				<table style="width: 100%;" border="1">
					<tr>
						<td style="text-align: center;">Last Delivery: <b><?= $patient['lastDelivery'] ?></b></td>
						<td style="text-align: center;">Accompaigned: <?= $patient['accompaigned']?"<i style='color: green;' class='fa fa-check-circle fa-lg'></i>":"<i style='color: red;' class='fa fa-times-circle fa-lg'></i>" ?></td>
						<td style="text-align: center;">FP Before MAT: <?= $patient['pfBeforeQuitMAT']?"<i style='color: green;' class='fa fa-check-circle fa-lg'></i>":"<i style='color: red;' class='fa fa-times-circle fa-lg'></i>" ?></td>
						<td style="text-align: center;">FP Used Before: <?= $patient['pfUsedBefore']?"<i style='color: green;' class='fa fa-check-circle fa-lg'></i>":"<i style='color: red;' class='fa fa-times-circle fa-lg'></i>" ?></td>
						<td style="text-align: center;">Method Used: <b><?= $patient['usedMethodName'] ?></b></td>
						<td style="text-align: center;">HIV Status: <?= $patient['hivStatus'] == 0?"<span class='btn btn-info btn-sm'>DNK</span>":($patient['hivStatus'] == -1?"<span class='btn btn-sm btn-success '>Neg</span>":"<span class='btn btn-sm btn-danger '>Pos</span>") ?></td>
						<td style="text-align: center;">Last HIV Test: <b><?= $patient['lastHIVTest'] ?></b></td>
						<td style="text-align: center;">Counselled for HIV: <?= $patient['counselledForHIV']?"<i style='color: green;' class='fa fa-check-circle fa-lg'></i>":"<i style='color: red;' class='fa fa-times-circle fa-lg'></i>" ?></td>
						<td style="text-align: center;">Refered for HIV test: <?= $patient['referedFoHIVTest']?"<i style='color: green;' class='fa fa-check-circle fa-lg'></i>":"<i style='color: red;' class='fa fa-times-circle fa-lg'></i>" ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<span id="new_styles"></span>
<style type="text/css">
	.private tr td, .private tr th{
		background: #dda9a9;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$("a[rel]").overlay({
	        mask: '#206095',
	        effect: 'apple',
	        onBeforeLoad: function() {

	            // grab wrapper element inside content
	            var wrap = this.getOverlay().find(".contentWrap");
				
	            // load the page specified in the trigger
	            wrap.load(this.getTrigger().attr("href"));
	        }

	    });
	});
</script>