<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";
// var_dump($_POST);
if(!trim($_POST['actname'])){
	echo "<span class=error-text>Please Enter Act name</span>";
	return;
}

if(!trim($_POST['actqty'])){
	echo "<span class=error-text>Please Enter Act Quantity</span>";
	return;
}
// var_dump($_POST);
if(preg_match("/\d/", $_POST['consultationID'])){
	$patientID = $_POST['consultationID'];
	$date = date("Y-m-d", time());
	$actname = PDB($_POST['actname'], true, $con);
	$actqty  = PDB($_POST['actqty'], true, $con);
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
	$price = formatResultSet($rslt=returnResultSet("SELECT 	a.ActPriceID AS priceID,
															a.Amount AS actPriceAmount,
															c.id AS commonDataId,
															c.methodId AS methodId
															FROM ac_price AS a
															INNER JOIN ac_name AS b
															ON a.ActNameID = b.ActNameID
															LEFT JOIN pf_common_act AS c
															ON b.ActNameID = c.actId
															WHERE a.InsuranceCategoryID = '{$patient['insuranceCategory']}' &&
																  b.Name = '{$actname}' &&
																  a.date <= '{$date}'
															LIMIT 0, 1
															", $con), false, $con);
	// var_dump($patient);
	if(is_array($price)){
		// var_dump($quarterID);
		// Check if the Exam records Exit before
		$actRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																		FROM ac_records AS a
																		WHERE a.ActPriceID={$price['priceID']} &&
																			  a.PatientRecordID={$patient['PatientRecordID']} &&
																			  a.Quantity = {$actqty}
																		", $con), true, $con);
		if(isset($_POST['ActRecordID']) && is_numeric($_POST['ActRecordID'])){
			saveData("UPDATE ac_records SET PatientRecordID='{$patient['PatientRecordID']}', ActPriceID={$price['priceID']}, Quantity={$actqty}, `Date`='{$date}', NurseID={$_SESSION['user']['UserID']}, status=0 WHERE ActRecordID='{$_POST['ActRecordID']}'", $con);
		} else if(!is_array($actRecords)){
			saveData("INSERT INTO ac_records SET PatientRecordID='{$patient['PatientRecordID']}', ActPriceID={$price['priceID']}, Quantity={$actqty}, `Date`='{$date}', NurseID={$_SESSION['user']['UserID']}, status=0",$con);
		}
		// Here Check if the Medicines is in the PF Commond Data and register required Information
		if(!is_null($price["commonDataId"]) && !is_null($patient["pfUserId"])){
			// Register PF INFORMATION FROM HERE NOW
			if(!returnSingleField("SELECT id FROM pf_records AS a WHERE a.userId = '{$patient['pfUserId']}' AND a.methodId='{$price['methodId']}' AND a.date='{$date}'", "id", true,$con)){
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
																	WHERE a.PatientRecordID={$patient['PatientRecordID']}
																	", $con), true, $con);
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
		echo "<tr><td colspan=3><span class=error-text>The Selected Act is not on the pricing Plan Please enter the Active act</span></td></tr>";
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