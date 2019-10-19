<?php
session_start();
//var_dump($_SESSION);
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
$fieldName = PDB($_POST['field'], true, $con);
$value = PDB($_POST['value'], true, $con);
$patient = PDB($_POST['patient'], true, $con);
if($fieldName == "lngth"){
	if($value > 3){
		$value = "0.".$value;
	}
}
if($fieldName == "Weight"){
	if($value > 150){
		$value /= 1000;
	}
}
$sql = "UPDATE pa_records SET `{$fieldName}`='{$value}' WHERE PatientRecordID='{$patient}'";
// echo $sql;
saveData($sql, $con);
?>
<span class=success>Address Saved</span>
<script type="text/javascript">
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		LoadProfile("<?= $patient ?>");
	},300);
</script>