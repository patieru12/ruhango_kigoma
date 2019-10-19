<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
$mdid 	= PDB($_POST['mdid'], true, $con);
$mdqty 	= PDB($_POST['quantity'], true, $con);
$currentDate = date("Y-m-d", time());

if($mdqty <= 0){
	echo "<span class='error-text'>No quantity</span>";
	return;
}

// check if the Medicine is allready on the wishlist
$stockCheck = formatResultSet($rslt=returnResultSet("SELECT d.id AS stockRecordId
															FROM md_main_stock AS c
															INNER JOIN md_main_stock_records AS d
															ON c.id = d.stockId
															WHERE d.operation = 'REQUEST' AND 
																  d.status=0 AND
																  c.mdNameId = '{$mdid}'
															", $con), false, $con);
// var_dump($stockCheck);

if($stockCheck){
	// Here Modify the Existing records
} else{
	// Create the new records

	$stockQuery = "INSERT INTO md_main_stock SET mdNameId='{$mdid}', quantity=0, date='{$currentDate}'";
	// echo $stockQuery;
	$stockId = saveAndReturnID($stockQuery, $con);
	$stockRecordQuery = "INSERT INTO md_main_stock_records SET stockId='{$stockId}', operation='REQUEST', stockLevel=0, quantity='{$mdqty}', date='{$currentDate}', userId='{$_SESSION['user']['UserID']}'";
	// echo $stockRecordQuery;
	saveData($stockRecordQuery, $con);
}
?>
<span class=success>Medicine Added to command List</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},500);
	
</script>