<?php
session_start();
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
if(!@$_POST['refundId']){
	echo "<span class='error'>No Retains to be refund</span><br />&nbsp;<br />&nbsp;";
	return;
}

if(!@$_POST['paidAmount'] || $_POST['paidAmount'] <= 0){
	echo "<span class='error'>No New Amount received</span><br />&nbsp;<br />&nbsp;";
	return;
}

if(!is_numeric($_POST['paidAmount'])){
	echo "<span class='error'>Provided Amount is not valid</span><br />&nbsp;<br />&nbsp;";
	return;
}

saveData("UPDATE `{$_POST['tabName']}` SET Amount= Amount - {$_POST['paidAmount']} WHERE id='{$_POST['refundId']}'", $con);

?>
<span class=success>Refund Paid Successfuly</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},500);
	setTimeout(function(){
		$("#generate").click();
	},1000);
</script>