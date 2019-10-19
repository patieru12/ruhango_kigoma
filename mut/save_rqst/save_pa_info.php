<?php
session_start();
//var_dump($_POST, trim($_POST['val']));
require_once "../../lib/db_function.php";
if("mut" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//$new_code = returnSingleField($sql="SELECT DISTINCT ServiceNameID FROM se_name WHERE ServiceCode='".(PDB($_POST['val'],true,$con))."'",$field="ServiceNameID",$data=true, $con);
if(trim($_POST['val']) != "" && trim($_POST['ref_val']) != ""){
	$sql = "UPDATE `{$_POST['tbl']}` SET `{$_POST['field']}`='".(PDB($_POST['val'],true,$con))."' WHERE `{$_POST['ref_field']}`='{$_POST['ref_val']}'";
	saveData($sql,$con);
	//echo $sql;
	echo "<span class=success>New Value Received!</span>";
}
?>
<script>
	$("#edit_mode").val("0");
</script>