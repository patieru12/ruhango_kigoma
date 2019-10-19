<?php
session_start();
require_once "../../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
echo "<h1>Diagnostic</h1><label title='Check this to preview all diagnostic'><input onclick='if($(\"#_diag-all\").prop(\"checked\")){ $(\"#custom-diag\").val(\"all\"); $(\"._custom-diag-data\").removeProp(\"checked\"); } else { $(\"#custom-diag\").val(\"\"); $(\"._custom-diag-data\").removeProp(\"checked\"); }' checked type=checkbox id='_diag-all' value='all'>All</label><br />";
$diag = returnAllData("SELECT * FROM co_diagnostic WHERE DiagnosticCode != '' ORDER BY DiagnosticCode ASC",$con);
foreach($diag as $d){
	$ommitted = false;
	$dd = ommitStringPart($str=$d['DiagnosticName'],26,$ommitted);
	echo "<label title=\"{$d['DiagnosticCode']}: {$d['DiagnosticName']}\" ><input type=checkbox class='_custom-diag-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-diag\").val() == \"all\"){ $(\"#custom-diag\").val(\"\"); } $(\"#_diag-all\").removeProp(\"checked\"); $(\"#custom-diag\").val($(\"#custom-diag\").val() + \"{$d['DiagnosticID']};\"); } else{ $(\"#custom-diag\").val($(\"#custom-diag\").val().replace(/{$d['DiagnosticID']};/g,\"\")); }' value='{$d['DiagnosticID']}'/>{$dd}</label><br />";
}
?>