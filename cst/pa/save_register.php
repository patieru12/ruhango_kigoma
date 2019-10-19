<?php
session_start();
require_once "../../lib/db_function.php";
// var_dump($_POST, $_SESSION);
$registerNumber = PDB($_POST['number'], true, $con);
$recordId 		= PDB($_POST['recordid'], true, $con);
$registerUsed	= PDB(@$_POST['registerId'], true, $con);

// var_dump($registerNumber <= 0);
if($registerNumber <= 0){
	?>
	<span style="color:red">Error</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		alert("Patient Number provided is not valid\nPlease reload the patient info\nAnd provide another.");
	</script>
	<?php
} else if(!returnSingleField("SELECT ConsultationRecordID FROM co_records WHERE ConsultationRecordID='{$recordId}'", "ConsultationRecordID", true, $con)){
	?>
	<span style="color:red">Error</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		alert("No Patient to be registered\nPlease select one.");
	</script>
	<?php
} else if(!$registerId = returnSingleField("SELECT id FROM sy_register WHERE id='{$registerUsed}'", "id", true, $con)){
	?>
	<span style="color:red">Error</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		alert("No register associated to the your profile\nPlease logout and login again to select available one!");
	</script>
	<?php
} else if(@$_SESSION['user']['special'] == "PF" && !preg_match("/\d\/\d{2}\/\d{2}$/", $registerNumber)){
	// var_dump(preg_match("/\d\/\d{2}\/\d{2}$/", $registerNumber));
	?>
	<span style="color:red">Error The Provided Number is not valid to identify Family Planning User<br />Please use the format [nn/YY/MM]</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		//alert("The Provided Number is not valid to identify Family Planning User\nPlease use the format [nn/YY/MM]");
	</script>
	<?php
}/*else if($registered = returnSingleField("SELECT PatientRecordID FROM co_records WHERE registerId='{$registerId}' && RegisterNumber='{$registerNumber}'", "PatientRecordID", true, $con)){
	$pa_name = returnSingleField("SELECT b.Name AS Name FROM pa_Records AS a INNER JOIN pa_info AS b ON a.PatientID = b.PatientID WHERE a.PatientRecordID ='{$registered}'", "Name", true, $con);
	?>
	<span style="color:red">Error</span>
	<script type="text/javascript">
		// Here Remove any button on the st
		$("#mainInput").css("opacity", "0");
		alert("The Number is used by <?= $pa_name ?>\nPlease reload the patient info\n and find the correct one");
	</script>
	<?php
}*/ else{
	//check if this number has
	// echo $registerId;
	saveData("UPDATE co_records SET registerId='{$registerId}', RegisterNumber='{$registerNumber}' WHERE ConsultationRecordID='{$recordId}'",$con);
	// echo ;
	if(@$_SESSION['user']['special'] == "PF"){
		// Check if the user has already registered for the PF Usage
		$sql = "SELECT 	a.id,
						a.patientId
						FROM pf_user AS a
						WHERE a.serialNumber = '{$registerNumber}'";
		$userInformation = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
		if(is_null($userInformation)){
			// Here Register the new PF User
			// Try to find the user information to allow data binding
			$userSql = "SELECT 	c.PatientID
								FROM co_records AS a
								INNER JOIN pa_records AS b
								ON a.PatientRecordID = b.PatientRecordID
								INNER JOIN pa_info AS c
								ON b.PatientID = c.PatientID
								WHERE a.ConsultationRecordID = '{$recordId}'";
			$userId = returnSingleField($userSql, "PatientID", true, $con);
			if($userId){
				saveData("INSERT INTO pf_user SET patientId='{$userId}', serialNumber='{$registerNumber}'", $con);
			}
		}
	}
	?>
	<span class=success>Selected Additional Added Saved</span>
	<script>
		setTimeout(function(){
			$("#registerNumber").html("<?= $registerNumber ?>");
			$("#mainInput").css("opacity", "1");
			$(".close").click();
		},200);
	</script>
	<?php
}
?>