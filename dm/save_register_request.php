<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("dm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//disable error reporting now
error_reporting(0);
//now try to edit something
if(strtolower(trim($_POST['val'])) == 'free'){
	$_POST['val'] = 0;
}
if(strtolower(trim($_POST['val'])) == 'not supported'){
	$_POST['val'] = -1;
}
if(!$_POST['ref_val']){
	echo "<span id=out class=error-text style='position:absolute; top:40%; left:45%; background-color:#fff; padding:5px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; border-color: #ff0000;outline: none; border-radius:6px;'>Change Not Saved {$_POST['val']}</span>";
	//return;
} else{
	if($_POST['field'] == "serviceId"){
		// Here get the service Id from database
		$_POST['val'] = returnSingleField("SELECT a.ServiceNameID AS id FROM se_name AS a WHERE a.ServiceCode='{$_POST['val']}'", "id", true, $con);
	}
	$sql = "UPDATE `{$_POST['tbl']}` SET `{$_POST['field']}`='{$_POST['val']}' WHERE `{$_POST['ref_field']}`='{$_POST['ref_val']}'";
	saveData($sql,$con);
	//echo $sql;
	echo "<span id=out class=success style='position:absolute; top:40%; left:45%; background-color:#fff; padding:5px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; border-color: #00ee00;outline: none; border-radius:6px;'>Request Processed</span>";
}
//var_dump($_POST);
?>
<script>
	//setTimeout("$('#out').hide(1000)",2000);
	setTimeout("window.location='<?= $_POST['url'] ?>&mdid=<?= $_POST['mdid'] ?>'",100);
</script>