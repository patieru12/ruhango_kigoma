<?php
//var_dump($_GET);
//check the date and the consultation type and the patient id
if(!@$_GET['cons']){
	echo "<span class=error-text>No Consultation</span>";
	return;
}
//connect the database
require_once "../../lib/db_function.php";
//get the consultation payment now
$sql = "SELECT co_category.ConsultationCategoryName, co_price.Amount FROM co_category, co_price WHERE co_category.ConsultationCategoryID=co_price.ConsultationCategoryID && co_price.ConsultationPriceID='".PDB($_GET['cons'],true,$con)."'";
$cons = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
//var_dump($cons);
//echo "<span class=success>Consul.:</span>";
if($cons){
	echo "{$cons[0]['ConsultationCategoryName']}:<span class='c_amount success'>{$cons[0]['Amount']}</span>";
} else{
	echo "<span class=error-text>No Price</span>";
}
?>