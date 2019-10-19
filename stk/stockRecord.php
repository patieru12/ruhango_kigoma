<?php 

session_start();
require_once "../lib/db_function.php";

$operationType = $_GET["accStockOperationType"];
$operationTypeString = "";
if($operationType == "All"){
	$operationTypeString = "1, 0";
} else{
	$operationTypeString =  $operationType;
}
$operationItem = $_GET["accountingStockRecordItem"];
if($operationItem != "All"){
	$operationItem = " && b.id = '".$operationItem."'";
} else{
	$operationItem = "";
}

$operationMonth = $_GET["accountingStockRecordMonth"];
if($operationMonth == "now"){
	$operationMonth = date("m/d/Y", time());
}

$d = new DateTime($operationMonth);
	
$operationMonth = $d->format("m/01/Y");

$d = new DateTime( $operationMonth ); 
$monthStartDate 	= $d->getTimestamp();

$endDate = $d->format( 'Y-m-t 23:59:59' );

$d = new DateTime( $endDate );
$monthEndDate		= $d->getTimestamp();

$stockOperations = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.id AS id,
																		DATE_FORMAT(FROM_UNIXTIME(a.date), '%Y-%m-%d') AS operationDate,
																		c.name AS operatorName,
																		b.name AS itemName,
																		a.quantity AS operationQuantity,
																		a.operationType AS operationType
																		FROM ac_schoolstockoperation AS a
																		INNER JOIN ac_schoolstockitemtype AS b
																		ON a.itemTypeID = b.id
																		INNER JOIN Ac_SchoolStockOperator AS c
																		ON a.operatorID = c.id
																		WHERE a.date >= '{$monthStartDate}' && a.date <= '{$monthEndDate}' && a.operationType IN ({$operationTypeString})
																			  {$operationItem}
																		ORDER BY a.date DESC
																		", $con), true, $con);
//var_dump($stockOperations);
// if(count($stockOperations) == 0){
// 	$stockOperations[] = array();
// }
// echo $sql;
echo json_encode($stockOperations);