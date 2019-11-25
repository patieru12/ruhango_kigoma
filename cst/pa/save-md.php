<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(!trim($_POST['mdname']) || !trim($_POST['mdqty'])){
	echo "<span class=error-text>Medicine name and Quantity are Required</span>";
	return;
}

$date = date("Y-m-d", time());
// var_dump($_POST);
if(preg_match("/\d/", $_POST['consultationID'])){
	$patientID = $_POST['consultationID'];
	// echo $patientID; return;
	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory,
																c.RegisterNumber AS serialNumber,
																d.id AS pfUserId
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN co_records AS c
																ON a.PatientRecordID = c.PatientRecordID
																LEFT JOIN pf_user AS d
																ON c.RegisterNumber = d.serialNumber
																WHERE c.ConsultationRecordID ='{$patientID}'
																", $con), false, $con);

	
	// Get the Price of the selected Exam
	$medicineName= PDB($_POST['mdname'], true, $con);
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
	// var_dump($date, $s, $price); die();
	// var_dump($patient);
	if(is_array($price)){
		// Now Activate The Request
		$currentDateNow = date("Y-m-d", time());
		
		// Check if the Exam records Exit before
		$medicinesRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																			FROM md_records AS a
																			WHERE a.MedecinePriceID={$price['priceID']} &&
																				  a.ConsultationRecordID={$patientID}
																			", $con), true, $con);
		// $check_envelope = false;
		if(isset($_POST['MedecineRecordID']) && is_numeric($_POST['MedecineRecordID']) ){
			
			$md_record_id_id= saveData("UPDATE md_records SET Quantity='{$_POST['mdqty']}', MedecinePriceID={$price['priceID']}, ConsultationRecordID={$patientID}, ConsulatantID={$_SESSION['user']['UserID']}, Date='{$currentDateNow}', SpecialPrescription='{$_POST['mdins']}' WHERE MedecineRecordID='{$_POST['MedecineRecordID']}' ",$con);
			// $check_envelope = true;
			$qtyPrise 		= PDB($_POST['qtyPrise'], true, $con);
			$numberOfTimes 	= PDB($_POST['numberOfTimes'], true, $con);
			$numberOfDay 	= PDB($_POST['numberOfDay'], true, $con);
			$md_prescription_id = returnSingleField("SELECT PrescriptionID FROM md_prescription WHERE MedecineRecordID='{$_POST['MedecineRecordID']}'","PrescriptionID",true,$con);
			if($md_prescription_id){
				saveData("UPDATE md_prescription SET MedecineRecordID='{$_POST['MedecineRecordID']}', Quantiy='{$qtyPrise}', Frequency='{$numberOfTimes}', Days='{$numberOfDay}', Comment='' WHERE PrescriptionID='{$md_prescription_id}'",$con);
			} else {
				saveData("INSERT INTO md_prescription SET MedecineRecordID='{$_POST['MedecineRecordID']}', Quantiy='{$qtyPrise}', Frequency='{$numberOfTimes}', Days='{$numberOfDay}', Comment=''",$con);
			}
		
		} else if(!is_array($medicinesRecords)){
			$md_record_id_id= saveAndReturnID("INSERT INTO md_records SET Quantity='{$_POST['mdqty']}', MedecinePriceID={$price['priceID']}, ConsultationRecordID={$patientID}, ConsulatantID={$_SESSION['user']['UserID']}, Date=NOW(), SpecialPrescription='{$_POST['mdins']}'",$con);
			// $check_envelope = true;
			$qtyPrise 		= PDB($_POST['qtyPrise'], true, $con);
			$numberOfTimes 	= PDB($_POST['numberOfTimes'], true, $con);
			$numberOfDay 	= PDB($_POST['numberOfDay'], true, $con);

			saveData("INSERT INTO md_prescription SET MedecineRecordID='{$md_record_id_id}', Quantiy='{$qtyPrise}', Frequency='{$numberOfTimes}', Days='{$numberOfDay}', Comment=''",$con);
		}

		// Here Check if the Medicines is in the PF Commond Data and register required Information
		if(!is_null($price["commonDataId"]) && !is_null($patient["pfUserId"])){
			// Register PF INFORMATION FROM HERE NOW
			if(!returnSingleField("SELECT a.id FROM pf_records AS a WHERE a.userId = '{$patient['pfUserId']}' AND a.methodId='{$price['methodId']}' AND a.date='{$currentDateNow}'", "id", true,$con)){
				// Check the Duration the method used
				$methodInfo = formatResultSet($rslt=returnResultSet("SELECT a.duration FROM pf_method AS a WHERE a.id='{$price['methodId']}'", $con), false, $con);
				$checkedSecond = strtotime($date);
				for($month = 0; $month < $methodInfo["duration"]; $month++){
					$checkedSecond = strtotime(date("Y-m-t 23:59:59", $checkedSecond)) + 1;
					$usableDate = date("Y-m-d", $checkedSecond);
					saveData("INSERT INTO pf_records SET userId = '{$patient['pfUserId']}', methodId='{$price['methodId']}', date='{$usableDate}', consultantId={$_SESSION['user']['UserID']}", $con);
				}
			}
		}

		$medicinesRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecinePriceID AS priceID,
																			c.MedecineName AS medicineName,
																			a.Quantity AS mdQuantity,
																			a.SpecialPrescription AS prescription,
																			a.received AS received,
																			a.MedecineRecordID AS MedecineRecordID,
																			a.ConsulatantID AS ConsulatantID,
																			b.Emballage AS Emballage
																			FROM md_records AS a
																			INNER JOIN md_price AS b
																			ON a.MedecinePriceID = b.MedecinePriceID
																			INNER JOIN md_name AS c
																			ON b.MedecineNameID = c.MedecineNameID
																			WHERE a.ConsultationRecordID={$patientID}
																			", $con), true, $con);
		$tbData = "";
		// var_dump($medicinesRecords);
		$envelopeQuantity = 0;
		foreach($medicinesRecords AS $r){
			if($r['Emballage']){
				$envelopeQuantity++;
			}
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['medicineName']}</td>";
				$tbData .= "<td>{$r['prescription']}</td>";
				$tbData .= "<td>{$r['mdQuantity']}</td>";
				$tbData .= "<td>
								".($r['received'] == 0?
									"<a style='color:blue; text-decoration:none;' href='#' onclick='editMedicine(\"{$r['MedecineRecordID']}\"); return false;'>Edit</a> | 
									<a style='color:red; text-decoration:none;' href='#' onclick='deleteMedicine(\"{$r['MedecineRecordID']}\");return false;'>Delete</a>
									"
								:
									""
								)."
							</td>";
			$tbData .= "</tr>";
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
																					  a.PatientRecordID = '{$patient['PatientRecordID']}'
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
																								  a.PatientRecordID={$patient['PatientRecordID']}
																							", $con), false, $con);
					if($consumablesRecords){
						saveData("UPDATE cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$envelopeQuantity}', PatientRecordID={$patient['PatientRecordID']}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']} WHERE ConsumableRecordID='{$consumablesRecords['ConsumableRecordID']}'",$con);
					} else {
						saveData("INSERT INTO cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$envelopeQuantity}', PatientRecordID={$patient['PatientRecordID']}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']}",$con);
					}
				}
			}
			
		}
		echo $tbData;
	} else{
		$medicinesRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecinePriceID AS priceID,
																			c.MedecineName AS medicineName,
																			a.Quantity AS mdQuantity,
																			a.SpecialPrescription AS prescription,
																			a.received AS received,
																			a.MedecineRecordID AS MedecineRecordID
																			FROM md_records AS a
																			INNER JOIN md_price AS b
																			ON a.MedecinePriceID = b.MedecinePriceID
																			INNER JOIN md_name AS c
																			ON b.MedecineNameID = c.MedecineNameID
																			WHERE a.ConsultationRecordID={$patientID} &&
																				  a.ConsulatantID={$_SESSION['user']['UserID']}
																			", $con), true, $con);
		$tbData = "";
		// var_dump($medicinesRecords);
		foreach($medicinesRecords AS $r){
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['medicineName']}</td>";
				$tbData .= "<td>{$r['prescription']}</td>";
				$tbData .= "<td>{$r['mdQuantity']}</td>";
				$tbData .= "<td>
								".($r['received'] == 0?
									"<a style='color:blue; text-decoration:none;' href='#' onclick='editMedicine(\"{$r['MedecineRecordID']}\"); return false;'>Edit</a> | 
									<a style='color:red; text-decoration:none;' href='#' onclick='deleteMedicine(\"{$r['MedecineRecordID']}\");return false;'>Delete</a>
									"
								:
									""
								)."
							</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
		echo "<tr><td colspan=3><span class=error-text>The Selected Medicine is not on the pricing Plan Please enter the Active Medicine</span></td></tr>";
	}
}
?>

<script type="text/javascript">
	var prescription = "";
	function editMedicine(MedecineRecordID){
		if(MedecineRecordID){
			var url = "./pa/edit-md.php?MedecineRecordID=" + MedecineRecordID;
			
			$.getJSON( url, function(data) {
				$("#MedecineRecordID").val(data.MedecineRecordID);
				$("#requestMedicines").val(data.MedecineName);
				$("#requestMedicinesQty").val(data.Quantity);
				$("#requestMedicinesInstruction").val(data.SpecialPrescription);
				
				prescription = data.Quantiy;
				$("#nbrDays").val(data.Frequency);
				$("#cnDay").val(data.Days);

				smallUnit = data.smUnit;
				smallUnitMesure = data.smMesure;
				mesurePattern = "/" + smallUnitMesure + "/";

				$("#stockAlert").html($("#requestMedicines").val() + " Stock Value:" + data.stockLevel);

				
			}).done(function(){
				$("#dosage").focus();
				setTimeout(function(){
					$("#barEdit").html("<img src='../images/ajax_clock_small.gif' />");
					waitForTheRequest();
			},50);
				
			});
		}
	}

	function waitForTheRequest(){
		// console.log("waitForTheRequest.....");
		if(requestCompleted){
			// console.log("Request completed continue your way please!!!!!!!!");
			$("#dosage").val(prescription);
			// console.log("Wait a second:");
			setTimeout(function(){
				$("#barEdit").html("");
				// console.log(" trigerring blur function.");
				$("#nbrDays").focus();
			},1000);

			return true;
		} else{
			// console.log("Still Waiting.....");
			setTimeout(function(e){
				// console.log("waitForTheRequest.....");
				waitForTheRequest();
			}, 100);
		}
	}

	function deleteMedicine(MedecineRecordID){
		if(MedecineRecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-md.php",
				data: "MedecineRecordID=" + MedecineRecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedMedicines").html(result)
				},
				error: function(err){
					console.log(err.responseText);
				}
			});
		}
	}
</script>