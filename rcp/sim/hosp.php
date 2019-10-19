<?php
//var_dump($_GET);
//check the date and the consultation type and the patient id
if(!@$_GET['days']){
	echo "<span class=error-text>No Hospitasation</span>";
	return;
}
//connect the database
require_once "../../lib/db_function.php";
//get the consultation payment now
$sql = "SELECT ho_price.Amount FROM ho_price WHERE ho_price.HOPriceID='".PDB($_GET['type'],true,$con)."'";
$cons = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
//var_dump($cons);
//echo "<span class=success>Consul.:</span>";
if($cons){
	echo "Hospitasation:<span class='c_amount success'>".($cons[0]['Amount']*$_GET['days'])."</span>";
} else{
	echo "<span class=error-text>No Price</span>";
}
?>