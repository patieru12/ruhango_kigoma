<?php
session_start();
require_once "../../lib/db_function.php";
$patientID = $_GET['patientId'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															COALESCE(a.serialNumber, '') AS RegisterNumber,
															COALESCE(b.nationalId,'') AS nationalId,
															COALESCE(a.dateIn, '') AS startingDate,
															COALESCE(c.villageId,'') AS villageId,
															COALESCE(c.cellId,'') AS cellId,
															COALESCE(c.sectorId,'') AS sectorId,
															COALESCE(c.districtId,'') AS districtId,
															COALESCE(c.VillageName,'') AS VillageName,
															COALESCE(c.CellName,'') AS CellName,
															COALESCE(c.SectorName,'') AS SectorName,
															COALESCE(c.DistrictName,'') AS DistrictName,
															b.phoneNumber AS phoneNumber,
															b.DateofBirth AS DateofBirth,

															b.sex AS patientGender,
															COALESCE(b.martialStatus,'Mariee') AS martialStatus,

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
																		c.VillageName AS VillageName,
																		d.CellID AS cellId,
																		d.CellName AS CellName,
																		e.SectorID AS sectorId,
																		e.SectorName AS SectorName,
																		f.DistrictID AS districtId,
																		f.DistrictName AS DistrictName
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
																		WHERE a.patientId ='{$patientID}'
															) AS c
															ON b.VillageID = c.villageId
															LEFT JOIN pf_method AS d
															ON a.usedMethodId = d.id
															WHERE a.patientId ='{$patientID}'
															", $con), false, $con);
?>
<div class="row">
	<div class="col-sm-12">
		<h3 class="alert alert-success text-center">Register Information</h3>
	</div>
</div>
<div class="row" style="max-height: 250px; overflow-y: auto;">
	<div class="col-md-12">
		<span class=save_pf_register_info></span>
		<form action="./cmd/save-pf-register.php" method="POST" class="form-horizontal" id="pfRegisterInfoForm">
			<input type="hidden" name="patientId" value="<?= $_GET['patientId'] ?>" />
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Serial Number</label>
				<div class="col-sm-9">
					<input type="text" name="serialNumber" class="form-control" placeholder="Serial Number e.g: nn/YY/mm" value="<?= $patient['RegisterNumber'] ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">NID Number</label>
				<div class="col-sm-4">
					<input type="text" name="nid" class="form-control" placeholder="National Id Number" value="<?= $patient['nationalId'] ?>" />
				</div>
				<label class="col-sm-1 control-label text-center">Date</label>
				<div class="col-sm-4">
					<input type="text" name="registrationDate" class="form-control datePicker" placeholder="Reg.Date" value="<?= $patient['startingDate'] ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-1 control-label text-center">Name</label>
				<div class="col-sm-11">
					<input type="text" name="patientName" class="form-control" placeholder="Patient Name" value="<?= $patient['patientName'] ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Village</label>
				<label class="col-sm-3 control-label text-center">Cell</label>
				<label class="col-sm-3 control-label text-center">Sector</label>
				<label class="col-sm-3 control-label text-center">District</label>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<input type="text" name="villageName" class="form-control" placeholder="Village Name" value="<?= $patient['VillageName'] ?>" />
				</div>
				<div class="col-sm-3">
					<input type="text" name="cellName" class="form-control" placeholder="Cell Name" value="<?= $patient['CellName'] ?>" />
				</div>
				<div class="col-sm-3">
					<input type="text" name="sectorName" class="form-control" placeholder="Sector Name" value="<?= $patient['SectorName'] ?>" />
				</div>
				<div class="col-sm-3">
					<input type="text" name="districtName" class="form-control" placeholder="District Name" value="<?= $patient['DistrictName'] ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-1 control-label text-center">Phone</label>
				<div class="col-sm-4">
					<input type="text" name="phoneNumber" class="form-control" placeholder="Phone Number" value="<?= $patient['phoneNumber'] ?>" />
				</div>
				<label class="col-sm-3 control-label text-center">Birth Date</label>
				<div class="col-sm-4">
					<input type="text" name="birthDate" class="form-control datePicker" placeholder="Birth Date" value="<?= $patient['DateofBirth'] ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Last Delivery</label>
				<div class="col-sm-4">
					<input type="text" name="lastDelivery" class="form-control datePicker" placeholder="Last Delivery Date" value="<?= $patient['lastDelivery'] ?>" />
				</div>
				<label class="col-sm-3 control-label text-center">Accompainned</label>
				<div class="col-sm-2">
					<!-- <label><input type="checkbox" name=""> Yes</label> -->
					<select name="accompainned" class="form-control">
						<option value="0" <?= $patient['accompaigned'] == 0?"selected":"" ?>>No</option>
						<option value="1" <?= $patient['accompaigned'] == 1?"selected":"" ?>>Yes</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label text-center">PF Before Discharge</label>
				<div class="col-sm-2">
					<select name="pfBeforeDischarge" class="form-control">
						<option value="1" <?= $patient['pfBeforeQuitMAT'] == 1?"selected":"" ?>>Yes</option>
						<option value="0" <?= $patient['pfBeforeQuitMAT'] == 0?"selected":"" ?>>No</option>
					</select>
				</div>
				<label class="col-sm-2 control-label text-center">Sex</label>
				<div class="col-sm-4">
					<select name="gender" class="form-control">
						<option value="Female" <?= $patient['patientGender']=="Female"?"selected":"" ?>>Female</option>
						<option value="Male" <?= $patient['patientGender']=="Male"?"selected":"" ?>>Male</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				
				<label class="col-sm-2 control-label text-center">Status</label>
				<div class="col-sm-3">
					<select name="civalStatus" class="form-control">
						<option value="Mariee" <?= $patient['martialStatus']=="Mariee"?"selected":"" ?>>Mariee</option>
						<option value="Celibataire" <?= $patient['martialStatus']=="Celibataire"?"selected":"" ?>>Celibataire</option>
						<option value="Divorcee" <?= $patient['martialStatus']=="Divorcee"?"selected":"" ?>>Divorcee</option>
						<option value="Veuf" <?= $patient['martialStatus']=="Veuf"?"selected":"" ?>>Veuf/Veuve</option>
					</select>
				</div>
				<?php
					// Select all active Act
					$sql = "SELECT 	a.id AS id,
									a.Name AS name,
									a.abbreviation AS abbreviation
									FROM pf_method AS a
									ORDER BY a.id ASC";
					$records = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
					?>
				<label class="col-sm-2 control-label text-center">Used PF</label>
				<div class="col-sm-5">
					<select name="usedPFBefore" class="form-control">
						<option value="">None</option>
						<?php
						if(count($records) > 0){
							foreach($records AS $method){
								echo "<option value='".$method['id']."' ".($patient['pfUsedBefore'] == $method['id']?"selected":"").">".$method['name']."-".$method['abbreviation']."</option>";
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">HIV Status</label>
				<div class="col-sm-3">
					<select name="hivStatus" class="form-control">
						<option value="0" <?= $patient['hivStatus'] == 0?"selected":"" ?>>Negative</option>
						<option value="1" <?= $patient['hivStatus'] == 1?"selected":"" ?>>Positive</option>
						<option value="-1" <?= $patient['hivStatus'] == -1?"selected":"" ?>>DNK</option>
					</select>
				</div>
				<label class="col-sm-2 control-label text-center">Last Test</label>
				<div class="col-sm-4">
					<input type="text" name="lastHIVTest" class="form-control datePicker" placeholder="Last HIV Date" value="<?= $patient['lastHIVTest'] ?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label text-center">Councelled for HIV</label>
				<div class="col-sm-2">
					<select name="counselledForHIV" class="form-control">
						<option value="1" <?= $patient['counselledForHIV']==1?"selected":"" ?>>Yes</option>
						<option value="0" <?= $patient['counselledForHIV']==0?"selected":"" ?>>No</option>
					</select>
				</div>
				<label class="col-sm-4 control-label text-center">Refered for HIV test</label>
				<div class="col-sm-2">
					<select name="referedFoHIVTest" class="form-control">
						<option value="0" <?= $patient['referedFoHIVTest']==0?"selected":"" ?>>No</option>
						<option value="1" <?= $patient['referedFoHIVTest']==1?"selected":"" ?>>Yes</option>
					</select>
				</div>
			</div>
		</form>
	</div>
</div>
<button type="button" id="saveRegisterData" class="btn btn-success"><i class="fa fa-save"></i> Send</button><script type="text/javascript">
	$('#saveRegisterData').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_pf_register_info").html('');
		$(".save_pf_register_info").html('<img src="../../images/loading.gif" alt="Saving"/>'); 
		$("#pfRegisterInfoForm").ajaxForm({ 
			target: '.save_pf_register_info'
		}).submit();
			
	});
</script>
<link rel="stylesheet" type="text/css" href="../../js/plugin/datepicker/css/datepicker.css">
<script type="text/javascript" src="../../js/plugin/datepicker/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../js/plugin/datepicker/js/bootstrap-datepicker.js"></script>

<script type="text/javascript">
	$dp = jQuery.noConflict();
	$dp(".datePicker").datepicker({
		format: 'yyyy-mm-dd',
		weekStart: 1,
		autoclose: true,
		endDate: '<?= date('Y-m-d', time()); ?>'
	});
</script>