<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";

// var_dump($_POST);
if(preg_match("/\d/", $_POST['consultationID'])){
	$patientID = $_POST['consultationID'];
	$date = date("Y-m-d", time());
	$dateIn 	= PDB($_POST['dateIn'], true, $con);
	$dateOut 	= PDB($_POST['dateOut'], true, $con); //
	$hospType 	= PDB($_POST['hotype'], true, $con); //"Hospitalisation salle commune/j";
	
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
	$price = formatResultSet($rslt=returnResultSet("SELECT 	a.HOPriceID AS priceID,
															a.Amount AS hospPriceAmount
															FROM ho_price AS a
															INNER JOIN ho_type AS b
															ON a.HOTypeID = b.TypeID
															WHERE a.InsuranceCategoryID = '{$patient['insuranceCategory']}' &&
																  b.Name = '{$hospType}' &&
																  a.Date <= '{$date}'
															ORDER BY a.Date DESC
															LIMIT 0, 1
															", $con), false, $con);
	// var_dump($patient);
	if(is_array($price)){
		// var_dump($quarterID);
		// Check if the Exam records Exit before
		$hospRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																		FROM ho_record AS a
																		WHERE a.HOPriceID={$price['priceID']} &&
																			  a.RecordID={$patient['PatientRecordID']}
																		", $con), false, $con);
		if(!is_array($hospRecords)){
			saveData($sql = "INSERT INTO ho_record SET RecordID='{$patient['PatientRecordID']}', Days=0, StartDate='{$dateIn}', EndDate='{$dateOut}', HOPriceID={$price['priceID']}, status=0",$con);
		} else if(is_array($hospRecords)){
			// $days = getAge($date,$notation=1, $current_date=$hospRecords['']);
			saveData($sql = "UPDATE ho_record SET RecordID='{$patient['PatientRecordID']}', Days=0, StartDate='{$dateIn}', EndDate='{$dateOut}', HOPriceID={$price['priceID']}, status=0 WHERE HORecordID='{$hospRecords['HORecordID']}'",$con);
		}
		echo $sql;
		$hospRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.HOPriceID AS HORecordID,
																		c.Name AS hospTypeName,
																		a.Days AS numberOfDays,
																		a.StartDate AS StartDate,
																		a.EndDate AS EndDate,
																		a.status AS status
																		FROM ho_record AS a
																		INNER JOIN ho_price AS b
																		ON a.HOPriceID = b.HOPriceID
																		INNER JOIN ho_type AS c
																		ON b.HOTypeID = c.TypeID
																		WHERE a.RecordID={$patient['PatientRecordID']}
																		", $con), true, $con);
		$tbData = "";
		foreach($hospRecords AS $r){
			$age = getAge($r['StartDate'], 1, $r['EndDate'], true);
			// $age = getAge($e['StartDate'], 1, $e['EndDate'], true);
			// var_dump($age);
			$tbData .= "<tr>";
				$tbData .= "<td>{$r['hospTypeName']}</td>";
				$tbData .= "<td>{$r['StartDate']}</td>";
				$tbData .= "<td>{$r['EndDate']}</td>";
				$tbData .= "<td>{$age} day".($age>1?"s":"")."</td>";
				$tbData .= "<td>
								<a style='color:blue; text-decoration:none;' href='#' onclick='editHosp(\"{$r['HORecordID']}\", \"{$r['StartDate']}\", \"{$r['EndDate']}\", \"{$r['hospTypeName']}\"); return false;'>Edit</a> | 
								<a style='color:blue; text-decoration:none;' href='#' onclick='deleteHosp(\"{$r['HORecordID']}\");return false;'>Delete</a>
							</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=3><span class=error-text>The Selected hospitalization type is not on the pricing plan</span></td></tr>";
	}
}
?>
<script type="text/javascript">
	function editHosp(HORecordID,StartDate, EndDate, roomType){
		$("#requestType").val(roomType);
		$("#requestDateIn").val(StartDate);
		$("#requestDateOut").val(EndDate);
		$("#HORecordID").val(HORecordID);
	}

	function deleteHosp(HORecordID){
		if(HORecordID){
			$.ajax({
				type: "POST",
				url: "./pa/delete-ho.php",
				data: "HORecordID=" + HORecordID + "&url=ajax",
				cache: false,
				success: function(result){
					$("#requestedHostpitalization").html(result)
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		}
	}
</script>