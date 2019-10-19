<?php

session_start();
require_once "../lib/db_function.php";

$receiveItemType 		= $_POST['receiveItemType'];
$receiveItemQuantity	= $_POST['receiveItemQuantity'];
$receiveItemDate 		= $_POST['receiveItemDate'];
$receiveItemSupplier	= $_POST['receiveItemSupplier'];
$receiveItemComment		= $_POST['receiveItemComment'];

if($receiveItemQuantity <= 0){
	echo json_encode(array("error"=>"Provided Quantity is not supported!"));
	return;
}

$d = new DateTime( $receiveItemDate );
$receiveItemDateStamp	= $d->getTimestamp();
if($receiveItemDateStamp > time()){
	echo json_encode(array("error"=>"Invalid Date Selected!"));
	return;
}
$time = time();
$sql1 = "INSERT INTO ac_schoolstockitemtype SET name='{$receiveItemType}', created_at='{$time}', updated_at='{$time}'";
$sql2 = "SELECT * FROM ac_schoolstockitemtype WHERE id='{$receiveItemType}'";

$itemTypeID = insertOrReturnID($sql1, $sql2, "id",$con);

$time = time();
$sql1 = "INSERT INTO Ac_SchoolStockOperator SET name='{$receiveItemSupplier}', type='0', created_at='{$time}', updated_at='{$time}'";
$sql2 = "SELECT * FROM Ac_SchoolStockOperator WHERE id='{$receiveItemSupplier}'";

$supplierID = insertOrReturnID($sql1, $sql2, "id",$con);

$time = time();
$sql1 = "INSERT INTO ac_schoolstockoperation SET operatorID='{$supplierID}', itemTypeID='{$itemTypeID}', operationType='0', quantity='{$receiveItemQuantity}',  date='{$receiveItemDateStamp}', created_at='{$time}', updated_at='{$time}'";
$sql2 = "SELECT * FROM ac_schoolstockoperation WHERE operatorID='{$supplierID}' && itemTypeID='{$itemTypeID}' && operationType='0' && quantity='{$receiveItemQuantity}' &&  date='{$receiveItemDateStamp}'";

$operationId = insertOrReturnID($sql1, $sql2, "id",$con);

if($operationId){
	echo json_encode(array("success"=>"New Item Received Successfully!"));
	return;
} else{
	echo json_encode(array('error'=>'Undefined Error Occured while saving new Item!<br />Please check the inernetconnection and try again'));
	return;
}
?>