<?php
	session_start();
	
	require_once "../lib/db_function.php";
	// var_dump($_POST);
	$date = date("Y-m-d", time());
	$date2 = time();
	$quantity = PDB($_POST['new_value'], true, $con);
	$stockId = PDB($_POST['stock_id'], true, $con);
	// die($stockId);
	/*if(!$stockId){
		
		//create a stock record now
		$sql1 = "INSERT INTO md_stock_v2 SET batchId='{$batchId}', quantity='{$quantity}', Date='{$date2}'";
		$sql2 = "SELECT MedicineStockID FROM md_stock_v2 WHERE batchId='{$batchId}'";
		$stockId = insertOrReturnID($sql1, $sql2, 'MedicineStockID', $con);
	}*/
	if($stockId){
		//now save the regulatory command
		$sql = "INSERT INTO md_stock_records SET MedicineStockID='{$stockId}', Operation='ADJUST', StockLevel='".returnSingleField("SELECT Quantity FROM md_stock WHERE MedicineStockID='{$stockId}'","Quantity",true,$con)."', Quantity='{$_POST['new_value']}', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status='1'";
		// echo $sql; die();
		saveData($sql, $con);
		$sql = "UPDATE md_stock SET quantity= {$_POST['new_value']} WHERE MedicineStockID='{$stockId}'";
		
		saveData($sql, $con);
		echo $_POST['new_value'];
		return;
	}
	echo $_POST['old_value'];
?>
