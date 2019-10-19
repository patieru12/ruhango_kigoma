<?php
session_start();
require_once "../../lib/db_function.php";
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//echo "<h1>Diagnostic</h1><label title='Check this to preview all diagnostic'><input onclick='if($(\"#_diag-all\").prop(\"checked\")){ $(\"#custom-diag\").val(\"all\"); $(\"._custom-diag-data\").removeProp(\"checked\"); } else { $(\"#custom-diag\").val(\"\"); $(\"._custom-diag-data\").removeProp(\"checked\"); }' checked type=checkbox id='_diag-all' value='all'>All</label><br />";
$diag = returnAllData("SELECT ac_name.* FROM ac_name ORDER BY ac_name.Name ASC",$con);
$printed = ""; $i=0;
//var_dump($_GET);
if($diag){
	echo "<div style='background-color:#4f4a41; color:#eee; font-weight:bold; padding:2px; border-top-left-radius:5px; border-top-right-radius:5px; margin-top:10px; text-align:center'>Acts</div><div style='border:1px solid #4f4a41; padding-bottom:5px; border-bottom-left-radius:5px; border-bottom-right-radius:5px;'>";
	foreach($diag as $d){
		
		$ommitted = false;
		$dd = ommitStringPart($str=$d['Name'],17,$ommitted);
		echo "<label title=\"{$d['Name']}\" ><input type=radio class='_custom-diag-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-diag\").val() == \"all\"){ $(\"#custom-diag\").val(\"\"); } $(\"#_diag-all\").removeProp(\"checked\"); $(\"#custom-diag\").val($(\"#custom-diag\").val() + \"{$d['ActNameID']};\"); } else{ $(\"#custom-diag\").val($(\"#custom-diag\").val().replace(/{$d['ActNameID']};/g,\"\")); }' ".($d['ActNameID'] == @$_GET['exist']?"checked":"")." value='{$d['ActNameID']}' name=".(@$_GET['type'] == "master"?"actId":"prescId")." required />{$dd}</label><br />";
	}

	echo "</div>";
}
?>