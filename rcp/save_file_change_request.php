<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//echo "<script>alert('Not Done!')</script>";
echo "<span id=out class=error-text style='position:absolute; top:40%; left:45%; background-color:#fff; padding:5px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; border-color: #ff0000;outline: none; border-radius:6px;'>Change Not Saved {$_POST['val']}</span>";
//now try to edit something
//saveData("UPDATE `{$_POST['tbl']}` SET `{$_POST['field']}`='{$_POST['val']}' WHERE `{$_POST['ref_field']}`='{$_POST['ref_val']}'",$con);
?>
<script>
	setTimeout("$('#out').hide(1000)",2000);
</script>