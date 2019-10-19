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

$sql = "SELECT 	a.Quantity AS Quantity,
				d.MedecineName AS MedecineName,
				a.StockRecordsID AS StockRecordsID,
				c.id AS batchId,
				a.MedicineStockID AS MedicineStockID,
				d.MedecineNameID AS MedecineNameID
				FROM md_stock_records_v2 AS a 
				INNER JOIN md_stock_v2 AS b
				ON a.MedicineStockID = b.MedicineStockID
				INNER JOIN md_stock_batch AS c
				ON b.batchId = c.id
				INNER JOIN md_name AS d
				ON c.medicineNameId = d.MedecineNameID
				WHERE a.StockRecordsID='{$recordId}' && 
					  a.Status=0";
// echo $sql;
$data = returnAllData($sql,$con);
// var_dump($data); die();

// UPDATE THE BATCH INFORMATION
if(count($data)> 0){
	$data = $data[0];
	$date = date("Y-m-d", time());
	$time = time();
	$user = $_SESSION['user']['UserID'];
	// UPDATE THE BATCH
	$mdSql = "SELECT 	c.id AS batchId,
						b.id AS mainStockID,
						b.quantity AS mainStockQuantity,
						b.price AS mainStockPrice,
						b.expirationDate AS expirationDate,
						b.Emballage AS Emballage
						FROM md_main_stock AS b
						INNER JOIN md_stock_batch AS c
						ON b.batchId = c.id
						WHERE c.medicineNameId='{$data['MedecineNameID']}'
						ORDER BY expirationDate ASC
						";
	$mainData = returnAllData($mdSql,$con);
	$currentTime = time();
	if(count($mainData) > 0){
		// Here start processing
		$requiredQty = $quantity;
		$createNewStockInfo = false;
		foreach($mainData AS $foundData){
			if($requiredQty > 0){
				$quantityToUse = $requiredQty > $foundData['mainStockQuantity']?$foundData['mainStockQuantity']:$requiredQty;
				$updateRecordsV2 = "";
				if($createNewStockInfo){
					// Here Take the complete mainStockQuantity to the Distribution Stock
					$sqlUpdate = "INSERT INTO md_stock_v2 SET 
																batchId='{$foundData['batchId']}',
																expirationDate='{$foundData['expirationDate']}', 
																Quantity='{$quantityToUse}',
																price='{$foundData['mainStockPrice']}',
																Date='{$currentTime}',
																Emballage='{$foundData['Emballage']}'";
				} else {
					// Here Take the complete mainStockQuantity to the Distribution Stock
					$sqlUpdate = "UPDATE md_stock_v2 SET 
															batchId='{$foundData['batchId']}',
															expirationDate='{$foundData['expirationDate']}', 
															Quantity='{$quantityToUse}',
															price='{$foundData['mainStockPrice']}',
															Date='{$currentTime}',
															Emballage='{$foundData['Emballage']}'
															WHERE MedicineStockID='{$data['MedicineStockID']}'";
					// $updateRecordsV2 = "UPDATE md_stock_records_v2 SET "
				}
				// echo $sqlUpdate."\n";
				saveData($sqlUpdate, $con);
				// Create the main stock record informationdata
				$mainStockRecord = "INSERT INTO md_main_stock_records SET 
																			MedicineStockID='{$foundData['mainStockID']}',
																			Operation='DISTIBUTE',
																			StockLevel='{$foundData['mainStockQuantity']}',
																			Quantity='{$quantityToUse}',
																			Date='{$currentTime}',
																			UserID='{$user}',
																			Status='1'";
				// echo $mainStockRecord."\n";
				saveData($mainStockRecord, $con);
				$mainStockUpdate = "UPDATE md_main_stock SET quantity= quantity - {$quantityToUse} WHERE id='{$foundData['mainStockID']}'";
				saveData($mainStockUpdate, $con);
				// echo $mainStockUpdate."\n++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n";
				$requiredQty -= $quantityToUse;
				$createNewStockInfo = true;
			}

			if($requiredQty <= 0){
				// Here Approve the request and stop the operation by here
				$md_stock_records_v2 = "UPDATE md_stock_records_v2 SET providedQuantity='{$quantity}', Status=1 WHERE StockRecordsID='{$recordId}'";
				// echo $md_stock_records_v2."\n";
				saveData($md_stock_records_v2, $con);
				break;
			}
		}
	} else{
		echo "No stock Available for the current request.";
		return;
	}
	echo "OK";
	die();
	//saveData("UPDATE md_stock_batch SET batchNumber='{$batchNumber}', Date='{$date}' WHERE id='{$data['batchId']}'", $con);
	// Update the stock information
	saveData("UPDATE md_main_stock SET quantity='{$quantity}', price='{$price}', expirationDate='{$expiration}', comment='{$comment}' WHERE id='{$data['mainStockID']}'",$con);
	//Update the stock records information
	saveData("UPDATE md_main_stock_records SET status=1 WHERE StockRecordsID='{$data['mainStockID']}'", $con);
	$sql = "INSERT INTO md_main_stock_records SET MedicineStockID='{$data['MedicineStockID']}', Operation='IN', Quantity='{$quantity}', Date='{$time}', UserID='{$user}', Status='1'";
	saveData($sql, $con);
	echo "OK";					
}
?>
DONE