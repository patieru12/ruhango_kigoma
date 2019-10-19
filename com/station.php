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
	// var_dump($_POST);

	$stationName = PDB($_POST['stationName'], true, $con);
	$personContact = PDB($_POST['personContact'], true, $con);
	$contactPhone = PDB($_POST['contactPhone'], true, $con);
	$startingBalance = PDB($_POST['startingBalance'], true, $con);
	$date = PDB($_POST['date'], true, $con);

	// Check if the station name is stated
	if(!$stationName){
		$error = "<span class='error-text'>Please Provide the gas station name</span>";
	} else if(!$startingBalance){
		$error = "<span class='error-text'>How much for the starting</span>";
	} else if(!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)){
		$error = "<span class='error-text'>The Selected date is invalid</span>";
	} else if($date > date("Y-m-d", time())){
		$error = "<span class='error-text'>The Selected date is invalid</span>";
	} else{
		// Here Save the information if not registered before
		$sql1 = "INSERT INTO gas_station SET station='{$stationName}', contactPerson='{$personContact}', contactPhone='{$contactPhone}', currentBalance='{$startingBalance}', firstDate='{$date}', status=1";
		$sql2 = "SELECT id FROM gas_station WHERE station='{$stationName}'";
		$field = "id";
		$stationId = insertOrReturnID($sql1, $sql2, $field,$con);

		// The Save the transaction now

		$sql1 = "INSERT INTO gas_transaction SET stationId='{$stationId}', amount='{$startingBalance}', date='{$date}', authorityId='{$currentUserId}', operation='CREATE', status=1";
		$sql2 = "SELECT id FROM gas_transaction WHERE stationId='{$stationId}' && amount='{$startingBalance}' && date='{$date}' && operation='CREATE'";
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
							<input type='text' id='stationName' value='<?= @$_POST['stationName'] ?>' name='stationName' class='txtfield1' style='width:100%;' required ><br />
							Contact Person name<br />
							<input type='text' id='personContact' value='<?= @$_POST['personContact'] ?>' name='personContact' class='txtfield1' style='width:100%;' required ><br />
							Contact Person Phone<br />
							<input type='text' id='contactPhone' value='<?= @$_POST['contactPhone'] ?>' name='contactPhone' class='txtfield1' style='width:100%;' required ><br />
							Starting Balance<br />
							<input type='text' id='startingBalance' value='<?= @$_POST['startingBalance'] ?>' name='startingBalance' class='txtfield1' style='width:100%;' required ><br />
							Starting Date<br />
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