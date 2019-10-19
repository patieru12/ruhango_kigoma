<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<span class=error>You are accessing resricted area.</span>";
	echo "<script>window.location='../logout.php';</script>";
	return;
}

$serviceId = PDB($_POST['serviceId'], true,$con);

$register = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT sy_register.* FROM sy_register WHERE sy_register.status=1 && sy_register.serviceId='{$serviceId}' && (sy_register.consultantId IS NULL || sy_register.consultantId = '{$_SESSION['user']['UserID']}') ORDER BY name ASC",$con),$multirows=true,$con);
$i=0; 
// var_dump($service);
if($register){
	echo "<table>";
	foreach($register as $in){
		if( ($i++) % 5 == 0){
			if($i > 1){
				echo "</tr>";
			}
			echo "<tr>";
		}
		echo "<td><label title='{$in['name']}' style='padding:0 10px;'><input type=checkbox name='register_{$in['id']}' ".($in['consultantId'] == $_SESSION['user']['UserID']?"checked":"")."  value='{$in['id']}' id='{$in['registerCode']}'>{$in['name']}: {$in['registerCode']}</label></td>";
		
	} 
	echo "</tr></table>";
} else{
	echo "<span class=error>No register available for the selected services.</span>";
}
?>