<?php
session_start();
require_once "../lib/db_function.php";

$data = formatResultSet($rslt=returnResultSet($sql="SELECT * FROM ac_schoolstockitemtype", $con), true, $con);
// var_dump($data);
echo json_encode($data);
?>