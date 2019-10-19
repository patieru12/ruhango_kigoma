<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

//now try to edit something
if(strtolower(trim($_POST['val'])) == 'free'){
	$_POST['val'] = 0;
}
if(strtolower(trim($_POST['val'])) == 'not supported'){
	$_POST['val'] = -1;
}
if($_POST['val'] == null){
	echo "<span id=out class=error-text style='position:absolute; top:40%; left:45%; background-color:#fff; padding:5px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #ff0000; border-color: #ff0000;outline: none; border-radius:6px;'>Change Not Saved {$_POST['val']}</span>";
	//return;
} else{
saveData($sql = "INSERT INTO `la_price` SET `Amount`='{$_POST['val']}', InsuranceTypeID='{$_POST['exam']}', ExamID='{$_POST['insurance']}', Date='{$_POST['date']}'",$con);
///echo $sql;
echo "<span id=out class=success style='position:absolute; top:40%; left:45%; background-color:#fff; padding:5px; -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; -moz-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15), 0 0 7px #00ee00; border-color: #00ee00;outline: none; border-radius:6px;'>Request Processed</span>";
}
?>
<script>
	setTimeout("$('#out').hide(1000)",2000);
	setTimeout("window.location='<?= $_POST['url'] ?>'",3000);
</script>