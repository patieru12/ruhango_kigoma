<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
echo "<h1>Act</h1><label title='Check this to preview all Acts'><input onclick='if($(\"#_acts-all\").prop(\"checked\")){ $(\"#custom-acts\").val(\"all\"); $(\"._custom-acts-data\").removeProp(\"checked\"); } else { $(\"#custom-acts\").val(\"\"); $(\"._custom-acts-data\").removeProp(\"checked\"); }' checked type=checkbox id='_acts-all' value='all'>All</label><br />";
/* $diag = returnAllData("SELECT * FROM co_diagnostic WHERE DiagnosticCode != '' ORDER BY DiagnosticCode ASC",$con);
foreach($diag as $d){
	$ommitted = false;
	$dd = ommitStringPart($str=$d['DiagnosticName'],26,$ommitted);
	echo "<label title=\"{$d['DiagnosticCode']}: {$d['DiagnosticName']}\" ><input type=checkbox class='_custom-diag-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-diag\").val() == \"all\"){ $(\"#custom-diag\").val(\"\"); } $(\"#_diag-all\").removeProp(\"checked\"); $(\"#custom-diag\").val($(\"#custom-diag\").val() + \"{$d['DiagnosticID']};\"); } else{ $(\"#custom-diag\").val($(\"#custom-diag\").val().replace(/{$d['DiagnosticID']};/g,\"\")); }' value='{$d['DiagnosticID']}'/>{$dd}</label><br />";
}

$diag = returnAllData($sql = "SELECT DISTINCT md_name.MedecineName FROM md_name, md_category WHERE md_name.MedecineCategorID = md_category.MedecineCategoryID && md_category.MedecineCategoryName NOT LIKE('%aterials%') && md_name.CategoryCode != '' ORDER BY MedecineName ASC",$con);
//echo $sql;
if($diag){
	foreach($diag as $d){
		$ommitted = false;
		$dd = ommitStringPart($str=$d['MedecineName'],26,$ommitted);
		echo "<label title=\"{$d['MedecineName']}\" ><input class='_custom-medicines-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-medicines\").val() == \"all\"){ $(\"#custom-medicines\").val(\"\"); } $(\"#_medicines-all\").removeProp(\"checked\"); $(\"#custom-medicines\").val($(\"#custom-medicines\").val() + \"{$d['MedecineName']};\"); } else{ $(\"#custom-medicines\").val($(\"#custom-medicines\").val().replace(/{$d['MedecineName']};/g,\"\")); }' type=checkbox value='{$d['MedecineName']}'>{$dd}</label><br />";
	}
}
 */
$diag = returnAllData("SELECT DISTINCT ac_name.Name FROM ac_name ORDER BY Name ASC",$con);

if($diag){
	foreach($diag as $d){
		$ommitted = false;
		$dd = ommitStringPart($str=$d['Name'],26,$ommitted);
		echo "<label title=\"{$d['Name']}\" ><input class='_custom-acts-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-acts\").val() == \"all\"){ $(\"#custom-acts\").val(\"\"); } $(\"#_acts-all\").removeProp(\"checked\"); $(\"#custom-acts\").val($(\"#custom-acts\").val() + \"{$d['Name']};\"); } else{ $(\"#custom-acts\").val($(\"#custom-acts\").val().replace(/{$d['Name']};/g,\"\")); }' type=checkbox value='{$d['Name']}' />{$dd}</label><br />";
	}
}
?>