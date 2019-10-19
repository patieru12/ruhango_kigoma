<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../../logout.php';</script>";
	return;
}
$error = "";

// var_dump($_POST);
if(preg_match("/\d/", $_POST['HORecordID'])){
	$patientID = $_POST['HORecordID'];
	$date = date("Y-m-d", time());
	
	$hospType = "Hospitalisation salle commune/j";
	
	// echo $patientID; return;
	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN co_records AS c
																ON a.PatientRecordID = c.PatientRecordID
																INNER JOIN ho_record AS d
																ON a.PatientRecordID = d.RecordID
																WHERE d.HORecordID ='{$patientID}'
																", $con), false, $con);
	
	// Check if the Exam records Exit before
	$hospRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.*
																	FROM ho_record AS a
																	WHERE a.RecordID={$patient['PatientRecordID']}
																	", $con), false, $con);
	saveData($sql = "DELETE FROM ho_record WHERE HORecordID='{$hospRecords['HORecordID']}'",$con);
	
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
	if($hospRecords){
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
	}
	echo $tbData;
}
?>
<script type="text/javascript">
	function editHosp(HORecordID,StartDate, EndDate,roomType){
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