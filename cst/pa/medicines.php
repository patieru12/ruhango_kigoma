<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
if(preg_match("/\d/", $_GET['patientID'])){
	$patientID = $_GET['patientID'];
	
	$medicinesRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecinePriceID AS priceID,
																		c.MedecineName AS medicineName,
																		a.Quantity AS mdQuantity,
																		a.SpecialPrescription AS prescription,
																		a.received AS received,
																		a.MedecineRecordID AS MedecineRecordID,
																		a.ConsulatantID AS ConsulatantID
																		FROM md_records AS a
																		INNER JOIN md_price AS b
																		ON a.MedecinePriceID = b.MedecinePriceID
																		INNER JOIN md_name AS c
																		ON b.MedecineNameID = c.MedecineNameID
																		INNER JOIN co_records AS d
																		ON a.ConsultationRecordID = d.ConsultationRecordID
																		WHERE d.PatientRecordID = {$patientID}
																		", $con), true, $con);
	$tbData = "";
	// var_dump($medicinesRecords);
	if(count($medicinesRecords) > 0){
		foreach($medicinesRecords AS $r){
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['medicineName']}</td>";
				$tbData .= "<td>{$r['prescription']}</td>";
				$tbData .= "<td>{$r['mdQuantity']}</td>";
				$tbData .= "<td>
								".(($r['received'] == 0 || 1 )?
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
	} else{
		echo "<tr><td colspan=4><span class=error-text>The Current Patient Does not have Assigned Medicines!</span></td></tr>";
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