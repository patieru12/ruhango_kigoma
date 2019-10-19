<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$patientID = $_GET['patientID'];
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.sex AS patientGender,
															b.InsuranceNameID AS InsuranceNameID,
															c.phoneNumber AS phoneNumber,
															e.VillageName AS VillageName,
															f.CellName AS CellName,
															g.SectorName AS SectorName,
															h.DistrictName AS DistrictName,
															c.FamilyCode AS FamilyCode
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN in_price AS d
															ON b.InsuranceNameID = d.InsuranceNameID
															INNER JOIN ad_village AS e
															ON a.VillageID = e.ViillageID
															INNER JOIN ad_cell AS f
															ON e.CellID = f.CellID
															INNER JOIN ad_sector AS g
															ON f.SectorID = g.SectorID
															INNER JOIN ad_district AS h
															ON g.DistrictID = h.DistrictID
															WHERE PatientRecordID ='{$patientID}'
															", $con), false, $con);
// var_dump($patient);
// Get all Assigned fees for prior check
$sy_records = formatResultSet($rslt=returnResultSet("SELECT a.id AS recordID,
															a.ProductPriceID AS ProductPriceID,
															a.status AS recordStatusID,
															a.Quantity AS Quantity
															FROM sy_records AS a
															WHERE a.PatientRecordID = '{$patientID}'
															", $con), true, $con);
$existings = array();
$existingsQty = array();
$someData = array();
if($sy_records){
	foreach($sy_records AS $y){
		// var_dump($y['ProductPriceID'], $existings, "<hr />");
		if(!in_array($y['ProductPriceID'], $existings)){
			$existings[] = $y['ProductPriceID'];
			$existingsQty[$y['ProductPriceID']] = $y['Quantity'];
			$someData[$y['ProductPriceID']] = " checked ".($y['recordStatusID'] == 2 ? " onclick='return false'":"");
		}
	}

	// var_dump($y['ProductPriceID'], $existings, "<hr />");
}

// var_dump($sy_records, $someData);
// Get the Active Date
$currentDate = date("Y-m-d", time());
$activeDate = returnSingleField("SELECT Date FROM sy_tarif WHERE Date <= '{$currentDate}' ORDER BY Date DESC LIMIT 0, 1", "Date", true, $con);
if(!$activeDate){
	echo "<span class='error'>No Additional Charges can be added</span>";
	return;
}
// Get the Possible Tarif for additional fees
$sy_price = formatResultSet($rslt=returnResultSet("SELECT 	a.TarifID AS ProductPriceID,
															a.Amount AS productAmount,
															b.ProductName AS ProductName
															FROM sy_tarif AS a
															INNER JOIN sy_product AS b
															ON a.ProductID = b.ProductID
															WHERE a.InsuranceNameID = '{$patient['InsuranceNameID']}' &&
																  a.Date = '{$activeDate}'
															", $con), true, $con);
// var_dump($sy_price);
?>
Name: <b><?= $patient['patientName'] ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gender: <b><?= $patient['patientGender'] ?></b><br />
Insurance: <b><?= $patient['InsuranceName'] ?></b> Card ID: <b><?= $patient['InsuranceCardID'] ?></b> <br />
<h2>AdditionalCharges</h2>
<?php
if($sy_price){
	echo "<span class=save_adds></span>
	<form action='./save-additional.php' id='add_additional' method='POST'>
		<input type='hidden' name='patientID' value='{$patientID}' />
		";
		foreach($sy_price AS $s){
			if(strtolower($patient['patientGender']) == "not specified"){
				// check if the product is in the List here
				if(preg_match("/lunette/", strtolower($s['ProductName']))){
					echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				} else if(preg_match("/supanet/", strtolower($s['ProductName']))){
					echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				} else if(preg_match("/vaccination/", strtolower($s['ProductName']))){
					echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				}
			} else{
				if(preg_match("/lunette/", strtolower($s['ProductName']))){
					// echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				} else if(preg_match("/supanet/", strtolower($s['ProductName']))){
					// echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				} else if(preg_match("/vaccination/", strtolower($s['ProductName']))){
					// echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				} else{
					echo "<label><input type='checkbox' ".(in_array($s['ProductPriceID'], $existings)?$someData[$s['ProductPriceID']]:"")." name='{$s['ProductPriceID']}' value='1'>{$s['ProductName']} - ".number_format($s['productAmount'])." RWF </label><br />";
				}
			}
		}
		echo "<input type='submit' id='save' name='addFees' value='Save & Close' class='flatbtn-blu'>";
	echo "</form>";
}
?>

<script type="text/javascript">
	$('#save').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_adds").html('');
		$(".save_adds").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#add_additional").ajaxForm({ 
			target: '.save_adds'
		}).submit();
			
	});
</script>