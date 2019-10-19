<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(preg_match("/\d/", $_POST['ActRecordID'])){
	$patientID = returnSingleField("SELECT PatientRecordID FROM ac_records WHERE ActRecordID='{$_POST['ActRecordID']}'", "PatientRecordID", true, $con);//$_POST['consultationID'];
	saveData("DELETE FROM ac_records WHERE ActRecordID='{$_POST['ActRecordID']}'",$con);

	$actRecords = formatResultSet($rslt=returnResultSet("SELECT a.ActRecordID AS ActRecordID,
																a.ActPriceID AS priceID,
																c.Name AS actName,
																a.Quantity AS actQuantity,
																a.status AS status
																FROM ac_records AS a
																INNER JOIN ac_price AS b
																ON a.ActPriceID = b.ActPriceID
																INNER JOIN ac_name AS c
																ON b.ActNameID = c.ActNameID
																WHERE a.PatientRecordID={$patientID}
																", $con), true, $con);
	// var_dump($patient);
	if(is_array($actRecords)){
		$tbData = "";
		foreach($actRecords AS $e){
			$tbData .= "<tr>";
				$tbData .= "<td>{$e['actName']}</td>";
				$tbData .= "<td>{$e['actQuantity']}</td>";
				$e['actName'] = PDB($e['actName'], true, $con);
				$tbData .= "<td>
								".($e['status'] == 0?
									"<a style='color:blue; text-decoration:none;' href='#' onclick=\"editAct('{$e['ActRecordID']}', '{$e['actName']}', '{$e['actQuantity']}'); return false;\">Edit</a> |
									<a style='color:red; text-decoration:none;' href='#' onclick='deleteAct(\"{$e['ActRecordID']}\"); return false;'>Delete</a>"
									:""
								)."
							</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=3><span class=error-text>No Active Act is assigned.</span></td></tr>";
	}
}
?>
<script type="text/javascript">
	function editAct(ActRecordID,actName, Quantity){
		$("#ActRecordID").val(ActRecordID);
		$("#requestActs").val(actName);
		$("#requestActsQty").val(Quantity);
	}

	function deleteAct(ActRecordID){
		if(ActRecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-act.php",
				data: "ActRecordID=" + ActRecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedActs").html(result)
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>