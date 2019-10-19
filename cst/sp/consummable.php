<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
echo "<h1>Consumable</h1><label title='Check this to preview all Consumable'><input onclick='if($(\"#_consumable-all\").prop(\"checked\")){ $(\"#custom-consumable\").val(\"all\"); $(\"._custom-consumable-data\").removeProp(\"checked\"); } else { $(\"#custom-consumable\").val(\"\"); $(\"._custom-consumable-data\").removeProp(\"checked\"); }' checked type=checkbox id='_consumable-all' value='all'>All</label><br />";

$diag = returnAllData("SELECT DISTINCT MedecineName FROM cn_name ORDER BY MedecineName ASC",$con);
	
if($diag){
	/* foreach($diag as $d){
		$ommitted = false;
		$dd = ommitStringPart($str=$d['Name'],26,$ommitted);
		echo "<label title=\"{$d['Name']}\" ><input class='_custom-acts-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-acts\").val() == \"all\"){ $(\"#custom-acts\").val(\"\"); } $(\"#_acts-all\").removeProp(\"checked\"); $(\"#custom-acts\").val($(\"#custom-acts\").val() + \"{$d['Name']};\"); } else{ $(\"#custom-acts\").val($(\"#custom-acts\").val().replace(/{$d['Name']};/g,\"\")); }' type=checkbox value='{$d['Name']}' />{$dd}</label><br />";
	} */
	foreach($diag as $d){
		$ommitted = false;
		$dd = ommitStringPart($str=$d['MedecineName'],26,$ommitted);
		echo "<label title=\"{$d['MedecineName']}\" ><input class='_custom-consumable-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-consumable\").val() == \"all\"){ $(\"#custom-consumable\").val(\"\"); } $(\"#_consumable-all\").removeProp(\"checked\"); $(\"#custom-consumable\").val($(\"#custom-consumable\").val() + \"{$d['MedecineName']};\"); } else{ $(\"#custom-consumable\").val($(\"#custom-consumable\").val().replace(/{$d['MedecineName']};/g,\"\")); }' type=checkbox value='{$d['MedecineName']}'/>{$dd}</label><br />";
	}
}
?>