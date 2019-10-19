<?php
	session_start();
	
	require_once "../lib/db_function.php";
	//var_dump($_POST);
	if($_POST['stock_id']){
		//now save the regulatory command
		$sql = "INSERT INTO md_stock_records SET MedicineStockID='{$_POST['stock_id']}', Operation='ADJUST', StockLevel='".returnSingleField("SELECT Quantity FROM md_stock WHERE MedicineStockID='".PDB($_POST['stock_id'],true,$con)."'","Quantity",true,$con)."', Quantity='{$_POST['new_value']}', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status='1'";
		
		saveData($sql, $con);
		$sql = "UPDATE md_stock SET Quantity= {$_POST['new_value']} WHERE MedicineStockID='".PDB($_POST['stock_id'],true,$con)."'";
		
		saveData($sql, $con);
		echo $_POST['new_value'];
		return;
	}
	echo $_POST['old_value'];
?>
