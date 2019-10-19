<?php
session_start();
var_dump($_SESSION); die;

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

//now try to edit something
saveData("UPDATE `{$_POST['tbl']}` SET `{$_POST['field']}`='{$_POST['val']}' WHERE `{$_POST['ref_field']}`='{$_POST['ref_val']}'",$con);
?>
