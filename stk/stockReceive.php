<?php
session_start();
require_once "../lib/db_function.php";
$id = PDB($_GET['id'], true, $con);
$data = formatResultSet($rslt=returnResultSet($sql="SELECT * FROM Ac_SchoolStockOperator WHERE type = '{$id}'", $con), true, $con);

echo json_encode($data);
?>