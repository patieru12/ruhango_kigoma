<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
$mdid 		= PDB($_POST['stockId'], true, $con);
$mdqty 		= PDB($_POST['quantity'], true, $con);
$batch 		= PDB($_POST['batch'], true, $con);
$price 		= PDB($_POST['price'], true, $con);
$expiration = PDB($_POST['expiration'], true, $con);

if($expiration <= date("Y-m-d", time())){
	echo "<span class='error-text'>Command Already Expired!</span>";
	return;
}

if($mdqty <= 0){
	echo "<span class='error-text'>No Stock Received</span>";
	return;
}

if(empty($price) || $price < 0){
	echo "<span class='error-text'>Please Specify the price</span>";
	return;
}

if(empty($batch)){
	echo "<span class='error-text'>Please Specify the batch number</span>";
	return;
}
$currentDate = date("Y-m-d", time());


// Now Update Everything and close the modal
$stockRecords = formatResultSet($rslt=returnResultSet("SELECT * FROM md_main_stock_records WHERE id='{$mdid}'", $con), false, $con);
if(!is_array($stockRecords)){
	echo "<span class='error-text'>Please Make sure to select active command</span>";
	return;
}

// Create the Record to receive new Medicine
saveData("INSERT INTO md_main_stock_records SET stockId='{$stockRecords['stockId']}', operation='IN', stockLevel='{$stockRecords['stockLevel']}', quantity='{$mdqty}', date='{$currentDate}', userId='{$_SESSION['user']['UserID']}', status=1",$con);
// Update the Request record so that it never appear again in the list
saveData("UPDATE md_main_stock_records SET status=1 WHERE id='{$mdid}'",$con);
// Update the Stock record now for later use
saveData("UPDATE md_main_stock SET batchNumber='{$batch}', quantity='{$mdqty}', price='{$price}', expiration='{$expiration}', date='{$currentDate}' WHERE id='{$stockRecords['stockId']}'", $con);
// THANKS CLOSE THE MODAL
?>
<span class=success>Command Received</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		$("#refreshCommand")[0].click();
	},300);
	
</script>