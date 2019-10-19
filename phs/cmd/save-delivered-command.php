<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST); die();
$currentDate= date("Y-m-d", time());
$currentTime= time();
$stockId	= PDB($_POST['stockId'], true, $con);
$mdnameId	= PDB($_POST['mdNameId'], true, $con);
$quantity	= PDB($_POST['quantity'], true, $con);


// GET The main Stock Information
$mainStockInfo = formatResultSet($rslt=returnResultSet("SELECT 	a.* 
																FROM md_main_stock AS a
																WHERE a.mdNameId = '{$mdnameId}' AND
																	  a.expiration > '{$currentDate}' AND
																	  a.quantity > 0
																ORDER BY expiration ASC
														", $con), true, $con);
// var_dump($mainStockInfo);
if(count($mainStockInfo) <= 0){
	echo "<span class='error-text'>No Main Stock Info can be found.</span>";
	return;
}
$price = 0;
$quantityChanges = $quantity;
for($i=0; $i<count($mainStockInfo); $i++){
	$ms = $mainStockInfo[$i];
	$usableQty = $quantityChanges;
	$price = $ms['price'];
	if($quantityChanges > $ms['quantity']){
		$quantityChanges -= $ms['quantity'];
		$usableQty = $ms['quantity'];
	}
	// Create Record in the Main Stock
	$mainStockRecord = "INSERT INTO md_main_stock_records SET stockId='{$ms['id']}', operation='DISTRIBUTE', stockLevel='{$ms['quantity']}', quantity='{$usableQty}', date='{$currentDate}', userId='{$_SESSION['user']['UserID']}', status=1";
	// echo $mainStockRecord;
	saveData($mainStockRecord, $con);
	$mainStockUpdate = "UPDATE md_main_stock SET quantity= quantity - {$usableQty} WHERE id='{$ms['id']}'";
	// echo$mainStockUpdate;
	saveData($mainStockUpdate, $con);
	if($quantityChanges <= 0){
		break;
	}
}

//Get Stock Information for Distribution
$dsr= formatResultSet($rslt=returnResultSet("SELECT a.*
													FROM md_stock_records AS a
													WHERE a.StockRecordsID = '{$stockId}'
													", $con), false, $con);
$sr = formatResultSet($rslt=returnResultSet("SELECT a.*
													FROM md_stock AS a
													WHERE a.MedicineNameID = '{$mdnameId}'
													", $con), false, $con);
// var_dump($dsr);
// Update the Request
$distRecordUpdate = "UPDATE md_stock_records SET provided='{$quantity}', Status=1 WHERE StockRecordsID = '{$stockId}'";
// echo $distRecordUpdate;
saveData($distRecordUpdate, $con);
// Create the New Record for request
$distRecordReceive = "INSERT INTO md_stock_records SET MedicineStockID='{$dsr['MedicineStockID']}', Operation='IN', StockLevel='{$sr['Quantity']}', Quantity='{$quantity}', Date='{$currentTime}', UserID='{$_SESSION['user']['UserID']}', Status=1";
// echo $distRecordReceive;
saveData($distRecordReceive, $con);
$distrStockUpdate = "UPDATE md_stock SET quantity = quantity + {$quantity} WHERE MedicineStockID='{$sr['MedicineStockID']}'";
// echo $distrStockUpdate;
saveData($distrStockUpdate, $con);
// GET the Last Usable Tarif Settings
$tf = formatResultSet($rslt=returnResultSet("SELECT a.*
													FROM md_price AS a
													WHERE a.MedecineNameID = '{$mdnameId}' AND
														  a.Date <= '{$currentDate}'
													ORDER BY Date DESC
													LIMIT 0, 1
													", $con), false, $con);
// var_dump($tf);
if($tf['BuyingPrice'] != $price){
	// Now Create the New Record for Pricing from Here
	$amount = round($price + (($price*20)/100), 2);
	$pricing = "INSERT INTO md_price SET MedecineNameID = '{$tf['MedecineNameID']}', BuyingPrice='{$price}', Amount='{$amount}', Date='{$currentDate}', Status=1, Emballage='{$tf['Emballage']}'";
	// echo $pricing;
	saveData($pricing, $con);
}
// THANKS CLOSE THE MODAL
?>
<span class=success>Command Received</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		$("#refreshRequest")[0].click();
	},300);
	
</script>