<?php

session_start();
require_once "../lib/db_function.php";

$distributeItemType 		= $_POST['distributeItemType'];
$distributeItemQuantity		= $_POST['distributeItemQuantity'];
$distributeItemDate 		= $_POST['distributeItemDate'];
$distributeItemReceiver		= $_POST['distributeItemReceiver'];
$distributeItemComment		= $_POST['distributeItemComment'];

if($distributeItemQuantity <= 0){
	echo json_encode(array("error"=>"Provided Quantity is not supported!"));
	return;
}

$d = new DateTime( $distributeItemDate );
$distributeItemDateStamp	= $d->getTimestamp();
if($distributeItemDateStamp > time()){
	echo json_encode(array("error"=>"Invalid Date Selected!"));
	return;
}

$itemTypeID = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.*, 
																	carriedForward AS availableQuantity 
																	FROM ac_schoolstockitemtype AS a 
																	LEFT JOIN (
																		SELECT 	a.id AS id,
																				(COALESCE(b.receivedQuantity, 0) - COALESCE(c.distributedQuantity, 0)) AS carriedForward
																				FROM ac_schoolstockitemtype AS a
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS receivedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 0 && a.date <= '{$distributeItemDateStamp}'
																							GROUP BY b.id
																				) AS b
																				ON a.id = b.id
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS distributedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 1 && a.date <= '{$distributeItemDateStamp}'
																							GROUP BY b.id
																				) AS c
																				ON a.id = c.id
																	) AS d
																	ON a.id = d.id
																	WHERE  a.id = '{$distributeItemType}'
																	HAVING availableQuantity >= {$distributeItemQuantity}
																	", $con), true, $con);

if(count($itemTypeID) <= 0) {
	echo json_encode(array("error"=>"No Stock Available"));
	return;
}

$itemTypeID = $itemTypeID[0]['id'];

$time = time();
$sql1 = "INSERT INTO Ac_SchoolStockOperator SET name='{$distributeItemReceiver}', type='1', created_at='{$time}', updated_at='{$time}'";
$sql2 = "SELECT * FROM Ac_SchoolStockOperator WHERE id='{$distributeItemReceiver}'";

$receiverID = insertOrReturnID($sql1, $sql2, "id",$con);

$time = time();
$sql1 = "INSERT INTO ac_schoolstockoperation SET operatorID='{$receiverID}', itemTypeID='{$itemTypeID}', operationType='1', quantity='{$distributeItemQuantity}',  date='{$distributeItemDateStamp}', created_at='{$time}', updated_at='{$time}'";
$sql2 = "SELECT * FROM ac_schoolstockoperation WHERE operatorID='{$receiverID}' && itemTypeID='{$itemTypeID}' && operationType='1' && quantity='{$distributeItemQuantity}' && date='{$distributeItemDateStamp}'";

$operationId = insertOrReturnID($sql1, $sql2, "id",$con);

if($operationId){
	echo json_encode(array("success"=>"Item Distributed Successfully!"));
	return;
} else{
	echo json_encode(array('error'=>'Undefined Error Occured while saving new records!<br />Please check the inernetconnection and try again'));
	return;
}
?>