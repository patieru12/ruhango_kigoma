<?php
session_start();	
require_once "../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "Invalid User requesting pharmacy changes";
	return;
}
// var_dump($_POST);
$recordId 	= PDB($_POST['recordId'], true, $con);
$quantity 	= PDB($_POST['quantity'], true, $con);
$batchNumber= PDB($_POST['batch_number'], true, $con);
$price 		= PDB($_POST['price'], true, $con);
$expiration = PDB($_POST['expiration'], true, $con);
$emballage 	= PDB($_POST['emballage'], true, $con);
$comment	= PDB($_POST['comment'], true, $con);

$sql = "SELECT 	a.Quantity AS Quantity,
				d.MedecineName AS MedecineName,
				a.StockRecordsID AS StockRecordsID,
				b.id AS mainStockID,
				c.id AS batchId,
				a.MedicineStockID AS MedicineStockID
				FROM md_main_stock_records AS a 
				INNER JOIN md_main_stock AS b
				ON a.MedicineStockID = b.id
				INNER JOIN md_stock_batch AS c
				ON b.batchId = c.id
				INNER JOIN md_name AS d
				ON c.medicineNameId = d.MedecineNameID
				WHERE a.StockRecordsID='{$recordId}' && 
					  a.Status=0";
// echo $sql;
$data = returnAllData($sql,$con);
// var_dump($data);

// UPDATE THE BATCH INFORMATION
if(count($data)> 0){
	$data = $data[0];
	$date = date("Y-m-d", time());
	$time = time();
	$user = $_SESSION['user']['UserID'];
	// UPDATE THE BATCH
	saveData("UPDATE md_stock_batch SET batchNumber='{$batchNumber}', Date='{$date}' WHERE id='{$data['batchId']}'", $con);
	// Update the stock information
	saveData("UPDATE md_main_stock SET quantity='{$quantity}', price='{$price}', expirationDate='{$expiration}', Emballage='{$emballage}', comment='{$comment}' WHERE id='{$data['mainStockID']}'",$con);
	//Update the stock records information
	saveData("UPDATE md_main_stock_records SET status=1 WHERE StockRecordsID='{$data['StockRecordsID']}'", $con);
	$sql = "INSERT INTO md_main_stock_records SET MedicineStockID='{$data['MedicineStockID']}', Operation='IN', Quantity='{$quantity}', Date='{$time}', UserID='{$user}', Status='1'";
	saveData($sql, $con);
	echo "OK";					
}
?>