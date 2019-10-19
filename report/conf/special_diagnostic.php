<?php
session_start();
require_once "../../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(@$_GET['save'] == "OK"){
	//var_dump($_GET['data']);
	if(!returnSingleField($sql="SELECT SpecialID from co_special_diagnostic WHERE SpecialName='{$_GET['data']}'",$field="SpecialID",$data=true, $con)){
		saveData("INSERT INTO co_special_diagnostic SET SpecialName='{$_GET['data']}'", $con);
	}
}
//select all diagnostic in the system 
$diagnostic = returnAllDataInTable($table = "co_special_diagnostic",$con);
#var_dump($diagnostic);
echo <<<DONE
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
#new_ td{
	border:1px solid grey; 
	font-size:10px;
}
</style>
</heade>
<body>
<input type=text id=new_special class=txtfield1 style='width:200px' /> <img title='Save' style='cursor:pointer' id='save_specials' onclick='if($("#new_special").val()){ $(".special").load("./conf/special_diagnostic.php?data=" + $("#new_special").val().replace(" ","%20") + "&save=OK"); }' src='../images/b_save.png' width='20px' />
<label id=styl></label>
DONE;
if($diagnostic){
	echo "<table id='new_' >";
	for($i=0;$i<count($diagnostic);$i++){
		echo "<tr style='cursor:pointer' onclick='$(\"#styl\").html(\"<style>#id{$i}{background-color:#eee;}</style>\"); $(\".update_special\").load(\"./conf/diagnostic.php?sp_acc={$diagnostic[$i]['SpecialID']}\")' id='id{$i}' style=''><td><input type=checkbox name='spdiag{$i}' value='{$diagnostic[$i]['SpecialID']}' /></td><td>".($i+1)."</td><td>{$diagnostic[$i]['SpecialName']}</td></tr>";
	}
	echo "</table>";
}
?>
</body>
</html>

<script>
	
</script>