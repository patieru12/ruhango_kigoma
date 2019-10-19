<?php
session_start();
require_once "../../lib/db_function.php";
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
//echo "<h1>Diagnostic</h1><label title='Check this to preview all diagnostic'><input onclick='if($(\"#_diag-all\").prop(\"checked\")){ $(\"#custom-diag\").val(\"all\"); $(\"._custom-diag-data\").removeProp(\"checked\"); } else { $(\"#custom-diag\").val(\"\"); $(\"._custom-diag-data\").removeProp(\"checked\"); }' checked type=checkbox id='_diag-all' value='all'>All</label><br />";
$diag = returnAllData("SELECT co_diagnostic.*, co_diagnostic_category.CategoryName, co_diagnostic_category.CategoryCode FROM co_diagnostic, co_diagnostic_category WHERE DiagnosticCode != '' && co_diagnostic.DiagnosticCategoryID = co_diagnostic_category.DiagnosticCategoryID ORDER BY DiagnosticCode ASC",$con);
$printed = ""; $i=0; $k=0;
foreach($diag as $d){
	if($d['CategoryCode'] != $printed){
		$ommitted = false;
		$dd = ommitStringPart($str=$d['CategoryName'],20,$ommitted);
		if($i++ > 0)
			echo "</div>";
		echo "<div style='background-color:#4f4a41; color:#eee; font-weight:bold; padding:2px; border-top-left-radius:5px; border-top-right-radius:5px; margin-top:10px; text-align:center'>".$d['CategoryCode']." ".$dd."</div><div style='border:1px solid #4f4a41; padding-bottom:5px; border-bottom-left-radius:5px; border-bottom-right-radius:5px;'>";
		$printed = $d['CategoryCode'];
	}
	$ommitted = false;
	$dd = ommitStringPart($str=$d['DiagnosticName'],18,$ommitted);
	echo "<label title=\"{$d['DiagnosticCode']}: {$d['DiagnosticName']}\" ><input type=radio class='_custom-diag-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-diag\").val() == \"all\"){ $(\"#custom-diag\").val(\"\"); } $(\"#_diag-all\").removeProp(\"checked\"); $(\"#custom-diag\").val($(\"#custom-diag\").val() + \"{$d['DiagnosticID']};\"); } else{ $(\"#custom-diag\").val($(\"#custom-diag\").val().replace(/{$d['DiagnosticID']};/g,\"\")); }' value='{$d['DiagnosticID']}' ".($d['DiagnosticID'] == @$_GET['exist']?"checked":"")." id='ddd".(++$k)."' name=diagnosticId required />{$dd}</label><br />";
	if($d['DiagnosticID'] == @$_GET['exist']){
		?>
		<script>
			$("#kkk<?= ($k + 2) ?>").focus();
			$("#kkk<?= ($k + 2) ?>").select();
			//$("#ddd<?= $k ?>").prop("checked","true");
		</script>
		<?php
	}
}
if($i > 0)
	echo "</div>";
?>