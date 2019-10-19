<?php
session_start();

require_once "../lib/db_function.php";
if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
foreach($_POST AS $key=>$value){
	if(preg_match("/^md_/", $key)){
		$md_record_id = preg_replace("/^md_/", "", $key);
		// Get the Medicine Identification
		$mdname = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																	b.MedecineNameID AS MedecineNameID
																	FROM md_records AS a
																	INNER JOIN md_price AS b
																	ON a.MedecinePriceID = b.MedecinePriceID
																	WHERE a.MedecineRecordID ='{$md_record_id}'
																	", $con), false, $con);
		// var_dump($mdname);
		$md_stock_quantity 	= $mdname['Quantity'];
		$md_name_id 		= $mdname['MedecineNameID'];
		// Update md_records
		saveData("UPDATE md_records SET status = 1, received=1, PharmacistID='{$_SESSION['user']['UserID']}', comment='All' WHERE MedecineRecordID='{$md_record_id}'", $con);
		if(!$md_stock_data_id = returnSingleField("SELECT MedicineStockID FROM md_stock WHERE MedicineNameID='{$mdname['MedecineNameID']}'","MedicineStockID",true,$con))
			$md_stock_data_id = saveAndReturnID("INSERT INTO md_stock SET MedicineNameID='{$mdname['MedecineNameID']}', Quantity=0, Date='".time()."'",$con);
		$stock_level = returnSingleField("SELECT Quantity FROM md_stock WHERE MedicineNameID='{$md_name_id}'","Quantity",true,$con);
		
		saveData("UPDATE md_stock SET Quantity = Quantity - {$mdname['Quantity']} WHERE MedicineStockID='{$md_stock_data_id}'",$con);
		
		saveData("INSERT INTO md_stock_records SET MedicineStockID='{$md_stock_data_id}', Operation='DISTRIBUTION', StockLevel='{$stock_level}', Quantity='{$md_stock_quantity}', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status='1'",$con);

		// echo "<hr />";
		// saveData("UPDATE md_records SET status=1 WHERE MedecineRecordID='{$md_record_id}'",$con);
	}
}
// var_dump($_POST);
?>

<script>
	setTimeout(function(){
		LoadProfile("<?= $_POST['patientID'] ?>");
	},400);
	$("#printForm")[0].click();
</script>