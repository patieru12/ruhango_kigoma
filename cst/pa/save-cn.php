<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(!trim($_POST['cnname']) || !trim($_POST['cnqty'])){
	echo "<span class=error-text>Medicine name and Quantity are Required</span>";
	return;
}

$date = date("Y-m-d", time());
// var_dump($_POST);
if(preg_match("/\d/", $_POST['consultationID'])){
	$patientID = $_POST['consultationID'];
	// echo $patientID; return;
	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN co_records AS c
																ON a.PatientRecordID = c.PatientRecordID
																WHERE c.ConsultationRecordID ='{$patientID}'
																", $con), false, $con);

	
	// Get the Price of the selected Exam
	$consumableName= PDB($_POST['cnname'], true, $con);
	$price = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecinePriceID AS priceID,
															a.Amount AS medicinePriceAmount,
															a.Date AS priceDate
															FROM cn_price AS a
															INNER JOIN cn_name AS b
															ON a.MedecineNameID = b.MedecineNameID
															WHERE b.MedecineName = '{$consumableName}' &&
																  a.Date <= '{$date}'
															ORDER BY priceDate DESC
															LIMIT 0, 1
															", $con), false, $con);
	// var_dump($price); die();
	// var_dump($patient);
	if(is_array($price)){
		// Now Activate The Request
		
		// Check if the Exam records Exit before
		$consumablesRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																				FROM cn_records AS a
																				WHERE a.MedecinePriceID={$price['priceID']} &&
																					  a.PatientRecordID={$patient['PatientRecordID']}
																				", $con), true, $con);
		if(isset($_POST['ConsumableRecordID']) && is_numeric($_POST['ConsumableRecordID'])){
			saveData("UPDATE cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$_POST['cnqty']}', PatientRecordID={$patient['PatientRecordID']}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']} WHERE ConsumableRecordID='{$_POST['ConsumableRecordID']}'",$con);
		} else if(!is_array($consumablesRecords)){
			saveData("INSERT INTO cn_records SET MedecinePriceID={$price['priceID']}, Quantity='{$_POST['cnqty']}', PatientRecordID={$patient['PatientRecordID']}, `Date`='{$date}', Facture=1, NurseID={$_SESSION['user']['UserID']}",$con);
		}
		$consumablesRecords = formatResultSet($rslt=returnResultSet("SELECT a.ConsumableRecordID AS ConsumableRecordID,
																			a.MedecinePriceID AS priceID,
																			c.MedecineName AS medicineName,
																			a.Quantity AS mdQuantity,
																			a.status AS status
																			FROM cn_records AS a
																			INNER JOIN cn_price AS b
																			ON a.MedecinePriceID = b.MedecinePriceID
																			INNER JOIN cn_name AS c
																			ON b.MedecineNameID = c.MedecineNameID
																			WHERE a.PatientRecordID={$patient['PatientRecordID']} &&
																				  a.NurseID={$_SESSION['user']['UserID']}
																			", $con), true, $con);
		$tbData = "";
		// var_dump($medicinesRecords);
		foreach($consumablesRecords AS $r){
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['medicineName']}</td>";
				$tbData .= "<td>{$r['mdQuantity']}</td>";
				$tbData .= "<td>
								".(($r['status'] == 0 || 1 )?
									"<a style='color:blue; text-decoration:none;' href='#' onclick='editConsumable(\"{$r['ConsumableRecordID']}\", \"{$r['medicineName']}\", \"{$r['mdQuantity']}\"); return false;'>Edit</a> | 
									<a style='color:red; text-decoration:none;' href='#' onclick='deleteConsumable(\"{$r['ConsumableRecordID']}\");return false;'>Delete</a>
									"
								:
									""
								)."
							</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=3><span class=error-text>The Selected Medicine is not on the pricing Plan Please enter the Active Medicine</span></td></tr>";
	}
}
?>
<script type="text/javascript">
	function editConsumable(ConsumableRecordID,consumableName, Quantity){
		$("#ConsumableRecordID").val(ConsumableRecordID);
		$("#requestConsumables").val(consumableName);
		$("#requestConsumablesQty").val(Quantity);
	}

	function deleteConsumable(ConsumableRecordID){
		if(ConsumableRecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-cn.php",
				data: "ConsumableRecordID=" + ConsumableRecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedConsumables").html(result)
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>