<?php
session_start();
require_once "../../lib/db_function.php";
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='../se_select.php?msg=select the register please and service'; </script>";
	return;
}
// var_dump($_GET);
$patientID = $_GET['patientID'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.phoneNumber AS phoneNumber,
															c.sex AS patientGender,
															b.InsuranceNameID AS InsuranceNameID,
															c.phoneNumber AS phoneNumber,
															e.VillageName AS VillageName,
															f.CellName AS CellName,
															g.SectorName AS SectorName,
															h.DistrictName AS DistrictName,
															c.FamilyCode AS FamilyCode,
															COALESCE(i.RegisterNumber, '')AS RegisterNumber,
															i.ConsultationRecordID AS ConsultationRecordID,
															k.ServiceCode AS ServiceCode,
															COALESCE(l.id, '') AS tbId,
															COALESCE(a.InsuranceCardID, a.applicationNumber, '') AS InsuranceCardID
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
															INNER JOIN se_records AS j
															ON a.PatientRecordID = j.PatientRecordID
															INNER JOIN se_name AS k
															ON j.ServiceNameID = k.ServiceNameID
															LEFT JOIN tb_records AS l
															ON a.PatientRecordID = l.PatientRecordID
															WHERE a.PatientRecordID ='{$patientID}'
															", $con), false, $con);

// echo "<pre>";var_dump($patient);
// var_dump($patient['RegisterNumber']);
if(!$patient['RegisterNumber'] && $patient['ServiceCode'] != 'PST'){
	?>
	<a id="askForRegisterID" href="./assign_register.php?recordid=<?= $patient['ConsultationRecordID'] ?>" rel="#overlay2">Register Info</a>
	<script type="text/javascript">
		$("#mainInput").css("opacity", "0");
		setTimeout(function(e){
			$("#askForRegisterID")[0].click();
		}, 500);
	</script>
	<?php
} else if($patient['tbId']){
	?>
	<br />&nbsp;
	<a id="askForTBInfo" href="./tb_consultation.php?recordid=<?= $patient['PatientRecordID'] ?>" class="flatbtn-blu" rel="#overlay2">TB Consulation</a>
	<br />&nbsp;
	<script type="text/javascript">
		//$("#mainInput").css("opacity", "0");
		/*setTimeout(function(e){
			$("#askForTBInfo")[0].click();
		}, 500);*/
	</script>
	<?php
}

if($patient['DateIn'] != date("Y-m-d", time())){
	?>
	<div style="font-size:34px; padding: 5px; border-radius: 10px; background-color: #d99977; color:white; position: fixed; top: 40px; right: 30px;">
		Patient Is not Registered today
	</div>
	<?php
}
?>
<style type="text/css">
	.patientIdentification td{
		vertical-align: middle;
	}
</style>
<table style="width: 100%"; border="1" class="patientIdentification <?= strtolower($patient['InsuranceName']) == 'private'?"private":""?>">
	<thead>
		<tr>
			<th style=" padding: 0px; background-color: #4f94cf; border:1px solid #4f94cf; color:#fff;" id="registerNumber">
				<?= $patient['RegisterNumber'] ?>
			</th>
			<th>Date</th>
			<th>Name</th>
			<th>Phone Number</th>
			<th>Age</th>
			<th>Gender</th>
			<th>Vital Sign</th>
		</tr>
	</thead>
	<tbody>
		<tr style="">
			<td style="text-align: right;">
				<a href='../forms/<?= returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="FormFile",$data=true, $con) ?>?records=<?= $patient['PatientRecordID'] ?>' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'>
					Print
				</a><br />
				<?php
				if($patient['DocStatus'] == "locked"){
					?>
					<a href="./pa/request-unlock.php?records=<?= $patient['PatientRecordID'] ?>" rel="#overlay2">Unlock</a>
					<?php
				}
				?>
			</td>
			<td><?= $patient['DateIn'] ?></td>
			<td colspan="2" style="vertical-align: middle;">Name: <?= $patient['patientName'] ?><br />House Holder: <?= $patient['FamilyCode'] ?></td>
			<!-- <td><?= $patient['phoneNumber'] ?></td> -->
			<td style="text-align: center;"><?= $patient['DateofBirth'] ?><br /><?= getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn'])  ?></td>
			<td><?= $patient['patientGender'] ?></td>
			<td>
				<table style="width: 100%;">
					<tr>
						<td style="width: 50%;">
							Weight:
							<b>
								<?= ($patient['Weight'] >0?$patient['Weight']." Kg":"") ?>
								<a class="fa fa-pencil" href="./pa/editvitalsign.php?PatientRecordID=<?= $patient['PatientRecordID'] ?>&field=Weight" rel="#overlay2" style="color:blue; text-decoration: none;"></a>
							</b>
						</td>
						<td>
							Temp.: 
							<b>
								<?= $patient['Temperature'] > 0?$patient['Temperature']." <sup>o</sup>C":"" ?>
								<a class="fa fa-pencil" href="./pa/editvitalsign.php?PatientRecordID=<?= $patient['PatientRecordID'] ?>&field=Temperature" rel="#overlay2" style="color:blue; text-decoration: none;"></a>
							</b>
						</td>
					</tr>
					<tr>
						<td>
							Length:
							<b>
								<?= $patient['lngth'] > 0?$patient['lngth']." m":"" ?>
								<a class="fa fa-pencil" href="./pa/editvitalsign.php?PatientRecordID=<?= $patient['PatientRecordID'] ?>&field=lngth" rel="#overlay2" style="color:blue; text-decoration: none;"></a>
							</b>
						</td>
						<td>
							MUAC:
							<b>
								<?= $patient['muac'] > 0?$patient['muac']." cm":"" ?>
								<a class="fa fa-pencil" href="./pa/editvitalsign.php?PatientRecordID=<?= $patient['PatientRecordID'] ?>&field=muac" rel="#overlay2" style="color:blue; text-decoration: none;"></a>
							</b>
						</td>
					</tr>
					<tr>
						<td>
							Pulse:
							<b>
								<?= $patient['pulse'] > 0?$patient['pulse']:"" ?>
								<a class="fa fa-pencil" href="./pa/editvitalsign.php?PatientRecordID=<?= $patient['PatientRecordID'] ?>&field=pulse" rel="#overlay2" style="color:blue; text-decoration: none;"></a>
							</b>
						</td>
						<td>
							Blood Group:
							<b>
								<?= !is_null($patient['bloodGroup'])?$patient['bloodGroup']:"" ?>
								<a class="fa fa-pencil" href="./pa/editvitalsign.php?PatientRecordID=<?= $patient['PatientRecordID'] ?>&field=bloodGroup" rel="#overlay2" style="color:blue; text-decoration: none;"></a>
							</b>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="width: 100%;" border="1">
					<tr>
						<td>District: <b><?= $patient['DistrictName'] ?></b></td>
						<td>Sector: <b><?= $patient['SectorName'] ?></b></td>
						<td>Cell: <b><?= $patient['CellName'] ?></b></td>
						<td>Village: <b><?= $patient['VillageName'] ?></b></td>
					</tr>
				</table>
			</td>
			<td colspan="3">
				<table style="width: 100%;" border="1">
					<tr>
						<td>Insurance: <b><?= $patient['InsuranceName'] ?></b></td>
						<td>Card ID: <b><?= $patient['InsuranceCardID'] ?></b></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<div id="" style="line-height: 10px;">
	<table border="1" style="width: 100%;">
		<tr>
			<td style="text-align: center; padding: 5px;">
				Type of visit: 
				<label>Outpatient<input type="radio" id="visitType_1" name="visitType" <?= @$patient['VisitType'] == 1?"checked":""; ?> onclick="patientVisit(1, 'VisitType')" value="1"></label>&nbsp;&nbsp;
				<label>Inpatient<input type="radio" id="visitType_2" name="visitType" <?= @$patient['VisitType'] == 2?"checked":""; ?> onclick="patientVisit(2, 'VisitType')" value="2"></label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
			<td style="text-align: center; padding: 5px;">
				Deasese Episode:
				<label> New Case<input type="radio" id="Episode_1" <?= @$patient['DeseaseEpisode'] == 1?"checked":"" ?> onclick="patientVisit(1, 'DeseaseEpisode')" name="Episode" value="1"></label>&nbsp;&nbsp;
				<label>Old Case<input type="radio" id="Episode_2" <?= @$patient['DeseaseEpisode'] == 2?"checked":"" ?> name="Episode" onclick="patientVisit(2, 'DeseaseEpisode')"  value="2"></label>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center; padding: 5px;">
				Visit Purpose: 
				<label> Natural Deasese<input type="radio" id="Purpose_1" <?= @$patient['visitPurpose'] == 1?"checked":"" ?> onclick="patientVisit(1, 'visitPurpose')" name="Purpose" value="1"></label>&nbsp;&nbsp;
				<label>Occupational Desease<input type="radio" id="Purpose_2" <?= @$patient['visitPurpose'] == 2?"checked":"" ?> onclick="patientVisit(2, 'visitPurpose')" name="Purpose" value="2"></label>&nbsp;&nbsp;
				<label>Road Traffic<input type="radio" id="Purpose_3" <?= @$patient['visitPurpose'] == 3?"checked":"" ?> onclick="patientVisit(3, 'visitPurpose')" name="Purpose" value="3"></label>&nbsp;&nbsp;
				<label>Work Accident<input type="radio" id="Purpose_4" <?= @$patient['visitPurpose'] == 4?"checked":"" ?> name="Purpose" onclick="patientVisit(4, 'visitPurpose')" value="4"></label>&nbsp;&nbsp;
				<label>Other<input type="radio" id="Purpose_5" <?= @$patient['visitPurpose'] == 5?"checked":"" ?> onclick="patientVisit(5, 'visitPurpose')" name="Purpose" value="5"></label>
			</td>
		</tr>
	</table>
</div>
<span id="new_styles"></span>
<style type="text/css">
	.private tr td, .private tr th{
		background: #dda9a9;
	}
</style>
<script type="text/javascript">
	var newConsultationID = "<?= $patient['ConsultationRecordID'] ?>";
	if(consultationID != newConsultationID){
		setTimeout(function(e){
			checkDiagnostic("<?= $patient['ConsultationRecordID'] ?>");
		}, 2000);
	}

	function checkDiagnostic(PatientRecordID){
		if(PatientRecordID != consultationID){
			return;
		}
		var url = "./pa/check-diagno.php?patientId=" + PatientRecordID + "response=ajax";
		$.getJSON( url, function(data) {
			if(!data.Diag){
				var txt = "<style > #mainInput h2{ background-color: #bb5556; } </style>";
				// $("#mainInput").css("background-image", "");
				$("#new_styles").html(txt);
				setTimeout(function(e){
					checkDiagnostic(PatientRecordID)
				}, 5000);
			} else{
				var txt = "<style > #mainInput h2{ background-color: #66bb56; } </style>";
				// $("#mainInput").css("background-image", "");
				$("#new_styles").html(txt);
			}
		}).done(function(){
		});
	}

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
<?php
/*
<table border="0" width="100%" style="">
	<tr>
		<td>
			<table border="0" style="font-size: 80%; width: 100%;">
				<tr><td style="text-align: left;">REPUBLIC OF RWANDA</td></tr>
				<tr>
					<td style="vertical-align: middle; text-align: left;">
						<img src="../images/rwanda.png" />
					</td>
				</tr>
				<tr><td><?= strtoupper($_PROVINCE) ?> PROVINCE</td></tr>
				<tr><td><?= strtoupper($_DISTRICT) ?> DISTRICT</td></tr>
				<tr><td><?= strtoupper($_SECTOR) ?> SECTOR</td></tr>
				<tr><td><?= strtoupper($organisation) ?></td></tr>
				<tr><td>Tel: <?= $organisation_phone ?></td></tr>
				<tr><td>Email: <?= strtolower($organisation_email) ?></td></tr>
			</table>
		</td>
		<td style="font-size: 80%; width: 60%;">
			<table style="font-size: 100%;">
				<tr><td>Date of Birth: <b><?= $patient['DateofBirth'] ?></b></td></tr>
				<tr><td>Gender: <b><?= $patient['patientGender'] ?></b></td></tr>
				<tr><td>Age: <b><?= getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn'])  ?></b></td></tr>
				<tr><td>Weight: <b><?= ($patient['Weight'] >0?$patient['Weight']." Kg":"") ?></b></td></tr>
				<tr style="border-bottom:1px solid #000;"><td>Temperature: <b><?= $patient['Temperature'] > 0?$patient['Temperature']."<sup>o</sup>C":"" ?></b></td></tr>
				<tr><td>Insurance: <b><?= $patient['InsuranceName'] ?></b></td></tr>
				<tr><td>Phone: <b><?= $patient['phoneNumber'] ?></b></td></tr>
				<tr>
					<td style="text-align: right; padding-top: 20px;">
						<a href='../forms/<?= returnSingleField($sql="SELECT FormFile from in_forms WHERE InsuranceNameID='{$patient['InsuranceNameID']}'",$field="FormFile",$data=true, $con) ?>?records=<?= $patient['PatientRecordID'] ?>' title='Print Patient File' target='_blank' style='color:blue; text-decoration:none;'>
							Print Consultation Document
						</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
*/
?>