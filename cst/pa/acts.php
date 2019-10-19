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
	$actRecords = formatResultSet($rslt=returnResultSet("SELECT a.ActRecordID AS ActRecordID,
																c.Name AS actName,
																a.Quantity AS actQuantity,
																a.status AS status
																FROM ac_records AS a
																INNER JOIN ac_price AS b
																ON a.ActPriceID = b.ActPriceID
																INNER JOIN ac_name AS c
																ON b.ActNameID = c.ActNameID
																WHERE a.PatientRecordID = {$patientID}
																", $con), true, $con);
	// var_dump($examRecords);
	if(is_array($actRecords)){
		$tbData= "";
		foreach($actRecords AS $e){
			$tbData .= "<tr>";
				$tbData .= "<td>{$e['actName']}</td>";
				$tbData .= "<td>{$e['actQuantity']}</td>";
				$e['actName'] = PDB($e['actName'], true, $con);
				$tbData .= "<td>
								".($e['status'] == 0 || 1?
									"<a style='color:blue; text-decoration:none;' href='#' onclick=\"editAct('{$e['ActRecordID']}', '{$e['actName']}', '{$e['actQuantity']}'); return false;\">Edit</a> |
									<a style='color:red; text-decoration:none;' href='#' onclick='deleteAct(\"{$e['ActRecordID']}\"); return false;'>Delete</a>"
									:""
								)."
							</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=4><span class='error-text'>No Act is Prescribed is Prescribed!</span></td></tr>";
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