<?php
session_start();
require_once "../../lib/db_function.php";
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='../se_select.php?msg=select the register please and service'; </script>";
	return;
}
// If there is no tb_record id created it
// var_dump($_POST);

if(!$_POST['tbRecordId']){
	if($_POST['tbId']){
		// Here create the records
		$sql1 = "INSERT INTO tb_co_records SET tbId='{$_POST['tbId']}'";
		$sql2 = "SELECT id FROM tb_co_records WHERE tbId='{$_POST['tbId']}'";
		$field= "id";
		$_POST['tbRecordId'] = insertOrReturnID($sql1, $sql2, $field,$con);
	} else {
		echo "<span class='error'>The Selected Patient does not have any cough case.</span>";
		return;
	}
}
// var_dump($_POST);
if($_POST['tbRecordId']){
	// Here Save the cough information now
	$coughDuration 	= PDB($_POST['coughDuration'], true, $con);
	$tbRecordId 	= PDB($_POST['tbRecordId'], true, $con);
	saveData("UPDATE tb_co_records SET coughDuration ='{$coughDuration}' WHERE id='{$tbRecordId}'",$con);
	echo "<span class=success>The Cough duration parameter received</span>";
} else {
	echo "<span class='error'>Cough Information can't be found here</span>";
	return;
}
?>
