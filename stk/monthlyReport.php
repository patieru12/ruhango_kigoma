<?php

session_start();
require_once "../lib/db_function.php";
$id = PDB($_GET['id'], true, $con);

$time = str_replace("_", "/", $id);
$d = new DateTime($time);
$currentTime = $d->getTimestamp();

$d = new DateTime( date("Y-m-01", $currentTime) ); 
$monthStartDate 	= $d->getTimestamp();

$endDate = $d->format( 'Y-m-t 23:59:59' );
$d = new DateTime( date($endDate, $currentTime) );
$monthEndDate		= $d->getTimestamp();

$StockSummary = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.id AS id,
																	a.name AS itemName,
																	COALESCE(b.receivedQuantity,0) AS receivedQuantity,
																	COALESCE(c.distributedQuantity,0) AS distributedQuantity,
																	COALESCE(d.carriedForward,0) AS carriedForward,
																	( (COALESCE(d.carriedForward,0) + COALESCE(b.receivedQuantity,0)) - COALESCE(c.distributedQuantity,0) ) AS currentStock
																	FROM ac_schoolstockitemtype AS a
																	LEFT JOIN (
																		SELECT 	b.id AS id,
																				a.date AS date,
																				SUM(a.quantity) AS receivedQuantity
																				FROM ac_schoolstockoperation AS a
																				INNER JOIN ac_schoolstockitemtype AS b
																				ON a.itemTypeID = b.id
																				WHERE a.operationType = 0 &&
																					  a.date >= '{$monthStartDate}' && a.date <= '{$monthEndDate}'
																				GROUP BY b.id
																	) AS b
																	ON a.id = b.id
																	LEFT JOIN (
																		SELECT 	b.id AS id,
																				a.date AS date,
																				SUM(a.quantity) AS distributedQuantity
																				FROM ac_schoolstockoperation AS a
																				INNER JOIN ac_schoolstockitemtype AS b
																				ON a.itemTypeID = b.id
																				WHERE a.operationType = 1 &&
																					  a.date >= '{$monthStartDate}' && a.date <= '{$monthEndDate}'
																				GROUP BY b.id
																	) AS c
																	ON a.id = c.id
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
																							WHERE a.operationType = 0 &&
																								  a.date < '{$monthStartDate}'
																							GROUP BY b.id
																				) AS b
																				ON a.id = b.id
																				LEFT JOIN (
																					SELECT 	b.id AS id,
																							SUM(a.quantity) AS distributedQuantity
																							FROM ac_schoolstockoperation AS a
																							INNER JOIN ac_schoolstockitemtype AS b
																							ON a.itemTypeID = b.id
																							WHERE a.operationType = 1 &&
																								  a.date < '{$monthStartDate}'
																							GROUP BY b.id
																				) AS c
																				ON a.id = c.id
																	) AS d
																	ON a.id = d.id
																	",$con), true, $con); 
		// echo "<pre>";var_dump($StockSummary);
echo json_encode($StockSummary);
?>