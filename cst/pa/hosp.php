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
	$hospRecords = formatResultSet($rslt=returnResultSet("SELECT 	a.HOPriceID AS priceID,
																	c.Name AS hospType,
																	a.StartDate AS StartDate,
																	a.EndDate AS EndDate,
																	a.HORecordID AS HORecordID
																	FROM ho_record AS a
																	INNER JOIN ho_price AS b
																	ON a.HOPriceID = b.HOPriceID
																	INNER JOIN ho_type AS c
																	ON b.HOTypeID = c.TypeID
																	WHERE a.RecordID={$patientID}
																	", $con), true, $con);
	// var_dump($examRecords);
	if(is_array($hospRecords)){
		$tbData= "";
		foreach($hospRecords AS $e){
			$r = $e;
			$tbData .= "<tr>";
				$tbData .= "<td>{$e['hospType']}</td>";
				$tbData .= "<td>{$e['StartDate']}</td>";
				$tbData .= "<td>{$e['EndDate']}</td>";
				$age = getAge($e['StartDate'], 1, $e['EndDate'], true);

				$tbData .= "<td>{$age} day".($age>1?"s":"")."</td>";
				// $tbData .= "<td>".($e['examNumber'] == ''?"<a href='#'>Edit</a> | <a href='#'>Delete</a>":"")."</td>";

				$tbData .= "<td>
								<a style='color:blue; text-decoration:none;' href='#' onclick='editHosp(\"{$r['HORecordID']}\", \"{$r['StartDate']}\", \"{$r['EndDate']}\", \"{$r['hospType']}\"); return false;'>Edit</a> | 
								<a style='color:red; text-decoration:none;' href='#' onclick='deleteHosp(\"{$r['HORecordID']}\");return false;'>Delete</a>
							</td>";
			$tbData .= "</tr>";
		}
		echo $tbData;
	} else{
		echo "<tr><td colspan=4><span class='error-text'>No Hospitalization record is Available</span></td></tr>";
	}
}
?>
<script type="text/javascript">
	function editHosp(HORecordID,StartDate, EndDate, roormType){
		$("#requestType").val(roormType);
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