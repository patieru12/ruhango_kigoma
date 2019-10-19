<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";

$date = date("Y-m-d", time());
// var_dump($_POST);
$patientID = 0;
if(preg_match("/\d/", $_POST['MedecineRecordID'])){
	$patientID = returnSingleField("SELECT ConsultationRecordID FROM md_records WHERE MedecineRecordID='{$_POST['MedecineRecordID']}'","ConsultationRecordID",true,$con);

	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN co_records AS c
																ON a.PatientRecordID = c.PatientRecordID
																WHERE c.ConsultationRecordID ='{$patientID}'
																", $con), false, $con);

	saveData("DELETE FROM md_prescription WHERE MedecineRecordID='{$_POST['MedecineRecordID']}'", $con);
	saveData("DELETE FROM md_records WHERE MedecineRecordID='{$_POST['MedecineRecordID']}'", $con);
	
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
	if(is_array($medicinesRecords)){
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
	} else{
		$tbData = "<tr><td colspan=3><span class=error-text>No Active Medicine prescription.</span></td></tr>";
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
			saveData($s1 = "UPDATE cn_records SET Quantity='{$envelopeQuantity}' WHERE ConsumableRecordID='{$actualConsumption['ConsumableRecordID']}'", $con);
			// echo $s1;
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
					saveData($s2= "UPDATE cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$envelopeQuantity}', PatientRecordID={$patient['PatientRecordID']}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']} WHERE ConsumableRecordID='{$consumablesRecords['ConsumableRecordID']}'",$con);
					// echo $s2;
				} else {
					saveData($s3 = "INSERT INTO cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$envelopeQuantity}', PatientRecordID={$patient['PatientRecordID']}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']}",$con);
					// echo $s3;
				}
			}
		}
		
	} else {
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
																			LIMIT 0, 1
																			", $con), false, $con);
		if($actualConsumption){
			saveData($s1 = "DELETE FROM cn_records WHERE ConsumableRecordID='{$actualConsumption['ConsumableRecordID']}'", $con);
		}
	}
	echo $tbData;
	// echo "<tr><td colspan=3><span class=error-text>The Selected Medicine is not on the pricing Plan Please enter the Active Medicine</span></td></tr>";
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
	$("#requestedConsumables").load("./pa/materials.php?patientID=<?= $patientID; ?>&autorelaod=true");
</script>