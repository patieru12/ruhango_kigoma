<?php
	session_start();
	
	require_once "../../lib/db_function.php";
	if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	// unset($_SESSION['user']['ServiceID']);
	if(!isset($_SESSION['user']['ServiceID'])){
		echo "<script>window.location='../cst/se_select.php?msg=Please select the service and register tobe used';</script>";
		return;
	}
	$error = "";
	$success = "";
// var_dump($_POST);
if(@$_POST["save"]){
	if(!trim($_POST["table"])){
		$error = "No Data Type is Select<br />Please Specify weather is Act or Medicine";
	} else if(!trim($_POST["methodId"])){
		$error = "No Method is Select";
	} else if(!trim($_POST["data"])){
		$error = "No Data to be marked as common";
	} else if( ($field = $_POST["table"] == "pf_common_medicine"?"medicineId":"actId") && returnSingleField("SELECT id FROM `{$_POST["table"]}` WHERE methodId='{$_POST["methodId"]}' AND `{$field}`='{$_POST["data"]}'", "id",true,$con)){
		$error = "Data already Saved!";
	} else{
		// $field = $_POST["table"] == "pf_common_medicine"?"medicineId":"actId";
		saveData("INSERT INTO `{$_POST["table"]}` SET methodId='{$_POST["methodId"]}', `{$field}`='{$_POST["data"]}'", $con);
		$success = "New Common Data Saved!";
	}
}
$active = "configure";
require_once "../../lib2/cssmenu/cst_pf_header.html";
?>
<div id="w" style='height: auto;'>
	<div id="content" style='height: auto;'>
    	<b>
			<h1 style='align: center; margin-top:-55px;'>Family Planning Common Data Acts and Medicines</h1>
			<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
				<tr>
					<td id="ds_calclass"></td>
				</tr>
			</table>
			<form action="./configure.php" method=post >
				<table class=frm>
					<tr>
						<td>Type</td>
						<td>Method</td>
						<td>Common Data</td>
					</tr>
					<tr>
						<td>
							<label><input type="radio" name="table" value="pf_common_act" onclick="loadAct()"> Act</label>
							<label><input type="radio" name="table" value="pf_common_medicine" onclick="loadMedicine()"> Medicine</label>
						</td>
						<td>
							<?php
							// Select all active Act
							$sql = "SELECT 	a.id AS id,
											a.Name AS name,
											a.abbreviation AS abbreviation
											FROM pf_method AS a
											ORDER BY a.id ASC";
							$records = formatResultSet($rslt=returnResultSet($sql, $con), true, $con);
							?>

							<select name="methodId" class="form-control">
								<?php
								foreach($records AS $r){
									?>
									<option value="<?= $r['id'] ?>"><?= $r['name'] ?> - <?= $r['abbreviation'] ?></option>
									<?php
								}
								?>
							</select>
						</td>
						<td id="dataLoading">
							<input type="text" name="data" class="form-control" />
						</td>
						<td>
							<input type="submit" name="save" value="Save" class="btn btn-success">
						</td>
					</tr>
				</table>
			</form>
			<div class="row">
				<?php
				if($error){
					?>
					<div class="col-sm-offset-2 col-sm-8 alert alert-danger text-center alert-dismissable">
						<?= $error; ?>
						<i class="fa fa-times close"></i>
					</div>
					<?php
				}
				?>
				<?php
				if($success){
					?>
					<div class="col-sm-offset-2 col-sm-8 alert alert-success text-center alert-dismissable">
						<?= $success; ?>
						<i class="fa fa-times close"></i>
					</div>
					<?php
				}
				?>
			</div>
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2 text-center">
					<h3>Configured Common Data</h3>
					<?php
					$data = formatResultSet($rslt=returnResultSet($s = "SELECT 	c.name AS methodName,
																			b.MedecineName AS commonData,
																			c.duration AS duration
																			FROM pf_common_medicine AS a
																			INNER JOIN md_name AS b
																			ON a.medicineId = b.MedecineNameID
																			INNER JOIN pf_method AS c
																			ON a.methodId = c.id
																		UNION 
																			SELECT 	c.name AS methodName,
																					b.Name AS commonData,
																					c.duration AS duration
																					FROM pf_common_act AS a
																					INNER JOIN ac_name AS b
																					ON a.actId = b.ActNameID
																					INNER JOIN pf_method AS c
																					ON a.methodId = c.id
																			", $con), true, $con);
					// var_dump($s, $data);
					?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Method</th>
								<th>Common Data</th>
								<th>Duration in Month</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							foreach($data AS $d){
								?>
								<tr>
									<td class="text-right"><?= ++$i; ?></td>
									<td class="text-left"><?= $d["methodName"] ?></td>
									<td class="text-left"><?= $d["commonData"] ?></td>
									<td class="text-left"><?= $d["duration"] ?> month</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</b>
	</div>
</div>

<script type="text/javascript">
	function loadAct(){
		$("#dataLoading").load("./pa/loadactinform.php");
	}
	function loadMedicine(){
		$("#dataLoading").load("./pa/loadmedicineinform.php");
	}
</script>