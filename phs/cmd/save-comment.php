<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST); die();
$currentDate= date("Y-m-d", time());
$currentTime= time();
$stockId	= PDB($_POST['stockId'], true, $con);
$comment	= PDB($_POST['comment'], true, $con);

if(empty($comment)){
	echo "<span class='error-text'>Please Leave a comment!</span>";
	return;
}
saveData("UPDATE md_stock_records SET Comment='{$comment}' WHERE StockRecordsID='{$stockId}'", $con);
?>
<span class=success>Comment Saved</span>
<script>
	setTimeout(function(){
		$(".close").click();
	},200);
	setTimeout(function(){
		$("#refreshRequest")[0].click();
	},300);
	
</script>