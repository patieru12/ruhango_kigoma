<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

// var_dump($_POST);
foreach($_POST AS $key=>$value){
	$_POST[$key] = PDB($value, true, $con);
}
saveData("UPDATE `{$_POST['tableName']}` SET `{$_POST['statusField']}` ='{$_POST['statusValue']}' WHERE `{$_POST['fieldName']}`='{$_POST['valueData']}'",$con);
?>

<span class=success>The Operation completed with success</span>
<script>
	setTimeout(function(){
		LoadProfile("<?= $_POST['patientID'] ?>");
	},500);
</script>