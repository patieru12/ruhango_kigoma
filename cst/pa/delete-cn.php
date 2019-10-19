<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(preg_match("/\d/", $_POST['ConsumableRecordID'])){
	$patientID = returnSingleField("SELECT PatientRecordID FROM cn_records WHERE ConsumableRecordID='{$_POST['ConsumableRecordID']}'", "PatientRecordID", true, $con);//$_POST['consultationID'];
	saveData("DELETE FROM cn_records WHERE ConsumableRecordID='{$_POST['ConsumableRecordID']}'",$con);

	$materialsRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.ConsumableRecordID AS ConsumableRecordID,
																		c.MedecineName AS consumableName,
																		a.Quantity AS Quantity,
																		a.status AS status
																		FROM cn_records AS a
																		INNER JOIN cn_price AS b
																		ON a.MedecinePriceID = b.MedecinePriceID
																		INNER JOIN cn_name AS c
																		ON b.MedecineNameID = c.MedecineNameID
																		WHERE a.PatientRecordID = {$patientID}
																		",$con),true, $con);
	// var_dump($patient);
	$tbData = "";
	// var_dump($medicinesRecords);
	if(count($materialsRecords) > 0){
		foreach($materialsRecords AS $r){
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['consumableName']}</td>";
				$tbData .= "<td>{$r['Quantity']}</td>";
				$tbData .= "<td>
								".(($r['status'] == 0 || 1 )?
									"<a style='color:blue; text-decoration:none;' href='#' onclick='editConsumable(\"{$r['ConsumableRecordID']}\", \"{$r['consumableName']}\", \"{$r['Quantity']}\"); return false;'>Edit</a> | 
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
		echo "<tr><td colspan=4><span class=error-text>The Current Patient Does not have Assigned Consumables!</span></td></tr>";
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