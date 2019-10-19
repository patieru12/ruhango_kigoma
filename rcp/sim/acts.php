<?php
//var_dump($_GET);
require_once "../../lib/db_function.php";
//check the price for medicine
$records = formatResultSet($rslt=returnResultSet($sql="SELECT ac_price.Amount FROM ac_price, ac_name WHERE ac_name.ActNameID = ac_price.ActNameID && ac_name.Name = '{$_GET['actname']}' && ac_price.Date <= '{$_GET['actdate']}' && ac_price.InsuranceCategoryID = '{$_GET['insurance']}' ORDER BY ac_price.Date DESC LIMIT 0,1",$con),$multirows=false,$con);
//echo $sql;
//var_dump($records)

if($records){
	$o = false;
	$md = ommitStringPart($str=$_GET['actname'],7,$o);
	echo $md.":<span class=success>".($records['Amount']*$_GET['actquantity'])."</span>";
}
?>
<br />