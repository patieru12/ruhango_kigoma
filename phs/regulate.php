<?php
	session_start();
	
	require_once "../lib/db_function.php";
	// var_dump($_POST);
	$date = date("Y-m-d", time());
	$date2 = time();
	$quantity = PDB($_POST['new_value'], true, $con);
	$stockId = PDB($_POST['stock_id'], true, $con);
	if(!$stockId){
		$batchNumber = PDB($_POST['batch_number'], true, $con);
		// Here Prepare the system to hold stock information from here
		$check = returnSingleField("SELECT a.MedecineNameID FROM md_name AS a WHERE a.CategoryCode='{$batchNumber}'", "MedecineNameID", true, $con);
		if(!$check){
			echo "#ERR"; return;
		}

		// create a batch number related to the current request
		$sql1 = "INSERT INTO md_stock_batch SET medicineNameId='{$check}', batchNumber='{$batchNumber}', Date='{$date}'";
		$sql2 = "SELECT id FROM md_stock_batch WHERE medicineNameId='{$check}' && batchNumber='{$batchNumber}'";
		$batchId = insertOrReturnID($sql1, $sql2, "id", $con);
		// $batchId = saveAndReturnID(, $con);

		//create a stock record now
		$sql1 = "INSERT INTO md_main_stock SET batchId='{$batchId}', quantity='{$quantity}', Date='{$date2}'";
		$sql2 = "SELECT id FROM md_main_stock WHERE batchId='{$batchId}'";
		$stockId = insertOrReturnID($sql1, $sql2, 'id', $con);
	}
	if($stockId){
		//now save the regulatory command
		$sql = "INSERT INTO md_main_stock_records SET MedicineStockID='{$stockId}', Operation='ADJUST', StockLevel='".returnSingleField("SELECT Quantity FROM md_main_stock WHERE id='{$stockId}'","Quantity",true,$con)."', Quantity='{$_POST['new_value']}', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status='1'";
		// echo $sql; die();
		saveData($sql, $con);
		$sql = "UPDATE md_main_stock SET quantity= {$_POST['new_value']} WHERE id='{$stockId}'";
		
		saveData($sql, $con);
		echo $_POST['new_value'];
		return;
	}
	echo $_POST['old_value'];
?>
