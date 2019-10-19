<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("com" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$currentUserId = $_SESSION['user']['UserID'];

$active = "essance";
require_once "../lib2/cssmenu/com_header.html";

$error = "";


if(@$_POST['saveStation']){
	// var_dump($_POST); die();

	$stationId = PDB($_POST['stationId'], true, $con);
	$amount = PDB($_POST['amount'], true, $con);
	$date = PDB($_POST['date'], true, $con);

	// Check if the station name is stated
	if(!$stationId){
		$error = "<span class='error-text'>Which station is being recharged</span>";
	} else if(!$amount){
		$error = "<span class='error-text'>How much for the recharging</span>";
	} else if(!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)){
		$error = "<span class='error-text'>The Selected date is invalid</span>";
	} else if($date > date("Y-m-d", time())){
		$error = "<span class='error-text'>The Selected date is invalid</span>";
	} else{
		

		$sql1 = "INSERT INTO gas_transaction SET stationId='{$stationId}', amount='{$amount}', date='{$date}', authorityId='{$currentUserId}', operation='RECHARGE', status=1";
		$sql2 = "SELECT id FROM gas_transaction WHERE stationId='{$stationId}' && amount='{$amount}' && date='{$date}' && operation='RECHARGE'";
		$field = "id";
		$transactionId = insertOrReturnID($sql1, $sql2, $field,$con);

	}
}

$gasStations = returnAllData($sql="SELECT 	a.id, 
											a.station,
											a.contactPerson,
											a.contactPhone,
											a.currentBalance,
											a.firstDate
											FROM gas_station AS a
											WHERE a.status = 1",$con);

?>
<style>
	.fld_txt{
		width:100%;
	}
</style>
<div id='w' style='height: auto;'>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
		<tr>
			<td id="ds_calclass"></td>
		</tr>
	</table>

	<div id="content" style='height: auto;'>
		<h1 style='margin-top:-55px'>Gas Station We work With</h1>
		<b>
			<?= $error ?>
			<table border="0" cellpadding="0" cellspacing="0" style='font-size:14px; width: 100%'>
				<tr>
					<td style="width: 30%;">
						<h3>New Petrol Station</h3>
						<form action='' method='post'>
							Station Name<br />
							<select class="txtfield1" name="stationId" style='width:100%;'>
								<?php
								foreach($gasStations AS $g){
									echo "<option value='{$g['id']}'>{$g['station']} - {$g['currentBalance']}</option>";
								}
								?>
							</select>
							Amount<br />
							<input type='text' id='amount' value='<?= @$_POST['amount'] ?>' name='amount' class='txtfield1' style='width:100%;' required ><br />
							Date<br />
							<input type='text' id='date' value='<?= @$_POST['date'] ?>' name='date' class='txtfield1' onclick="ds_sh(this, 'date')" style='width:100%;' required ><br />
							<input type="submit" name="saveStation" class="flatbtn-blu" value="Save" /><br />&nbsp;<br />
						</form>
					</td>
					<td style="border-left: 1px solid #000;">
						<h3>Available Station with their Balance</h3>
						<table border="1" style="width: 100%;">
							<thead>
								<tr>
									<th>#</th>
									<th>Station Name</th>
									<th>Contact Person</th>
									<th>Contact Phone</th>
									<th>Starting Time</th>
									<th>Current Balance</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if($gasStations){
									$i = 2;
									foreach($gasStations AS $g){
										echo "<tr>";
											echo "<td>";
												echo $i++;
											echo "</td>";
											echo "<td>";
												echo $g['station'];
											echo "</td>";
											echo "<td>";
												echo $g['contactPerson'];
											echo "</td>";
											echo "<td>";
												echo $g['contactPhone'];
											echo "</td>";
											echo "<td>";
												echo $g['firstDate'];
											echo "</td>";
											echo "<td>";
												echo $g['currentBalance'];
											echo "</td>";
										echo "</tr>";
									}
								}
								?>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</b>
	</div>
	<?php
  	include_once "../footer.html";
  	?>
</body>
</html>