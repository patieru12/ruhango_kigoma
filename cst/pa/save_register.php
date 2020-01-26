<?php
session_start();
require_once "../../lib/db_function.php";
// var_dump($_POST, $_SESSION); die();
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
	// Here if Some Medicines are registered Fill them in
	$PatientRecordID = returnSingleField("SELECT PatientRecordID FROM co_records WHERE ConsultationRecordID='{$recordId}'", "PatientRecordID", true, $con);
		
	if(count(@$_POST["mdName"]) > 0){
		$date = date("Y-m-d", time());
		$envelopeQuantity = 0;
		foreach($_POST['mdName'] AS $key=>$value){
			$mdQuatity = $_POST['mdQuantity'][$key];
			$mdPresc   = $_POST['mdPrescription'][$key];
			//Here Have mdName in value
			$medicineName= PDB($value, true, $con);
			$price = formatResultSet($rslt=returnResultSet($s = "SELECT 	a.MedecinePriceID AS priceID,
																			a.Amount AS examPriceAmount,
																			a.Emballage AS Emballage,
																			c.id AS commonDataId,
																			c.methodId AS methodId
																			FROM md_price AS a
																			INNER JOIN md_name AS b
																			ON a.MedecineNameID = b.MedecineNameID
																			LEFT JOIN pf_common_medicine AS c
																			ON b.MedecineNameID = c.medicineId
																			WHERE b.MedecineName = '{$medicineName}' &&
																				  a.Date <= '{$date}'
																			ORDER BY Date DESC LIMIT 1
																			", $con), false, $con);
			if(is_array($price)){
				if($price['Emballage']){
					$envelopeQuantity++;
				}
				$md_record_id_id= saveAndReturnID("INSERT INTO md_records SET Quantity='{$mdQuatity}', MedecinePriceID={$price['priceID']}, ConsultationRecordID={$recordId}, ConsulatantID={$_SESSION['user']['UserID']}, Date=NOW(), SpecialPrescription='{$mdPresc}'",$con);
				// $check_envelope = true;
				
				saveData("INSERT INTO md_prescription SET MedecineRecordID='{$md_record_id_id}', Quantiy='', Frequency='', Days='', Comment=''",$con);
			}
		}
			
		if($envelopeQuantity > 0){
			// Get the Current Patient Envelope consumption
			$actualConsumption = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																				c.Date AS priceDate
																				FROM cn_records AS a
																				INNER JOIN cn_price AS c
																				ON a.MedecinePriceID = c.MedecinePriceID
																				INNER JOIN cn_name AS b
																				ON c.MedecineNameID = b.MedecineNameID
																				WHERE b.MedecineName = '{$envelopeName}' &&
																					  a.Date <= '{$date}' &&
																					  a.PatientRecordID = '{$PatientRecordID}'
																				ORDER BY priceDate DESC
																				LIMIT 0, 1
																				", $con), false, $con);
			// Here Fild the Price for Sachet
			if($actualConsumption){
				saveData("UPDATE cn_records SET Quantity='{$envelopeQuantity}' WHERE ConsumableRecordID='{$actualConsumption['ConsumableRecordID']}'", $con);
			} else {
				// Get the Pricingplan for envelope
				$price = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecinePriceID AS priceID,
																		a.Amount AS medicinePriceAmount,
																		a.Date AS priceDate
																		FROM cn_price AS a
																		INNER JOIN cn_name AS b
																		ON a.MedecineNameID = b.MedecineNameID
																		WHERE b.MedecineName = '{$envelopeName}' &&
																			  a.Date <= '{$date}'
																		ORDER BY priceDate DESC
																		LIMIT 0, 1
																		", $con), false, $con);

				if($price){
					$consumablesRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																							FROM cn_records AS a
																							WHERE a.MedecinePriceID={$price['priceID']} &&
																								  a.PatientRecordID={$PatientRecordID}
																							", $con), false, $con);
					if($consumablesRecords){
						saveData("UPDATE cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$envelopeQuantity}', PatientRecordID={$PatientRecordID}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']} WHERE ConsumableRecordID='{$consumablesRecords['ConsumableRecordID']}'",$con);
					} else {
						saveData("INSERT INTO cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$envelopeQuantity}', PatientRecordID={$PatientRecordID}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']}",$con);
					}
				}
			}
			
		}
	}
	//check if this number has
	// echo $registerId;
	saveData("UPDATE co_records SET ConsultantID='{$_SESSION['user']['UserID']}', registerId='{$registerId}', RegisterNumber='{$registerNumber}' WHERE ConsultationRecordID='{$recordId}'",$con);
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
			LoadProfile("<?= $PatientRecordID ?>");
		},200);
	</script>
	<?php
}
?>