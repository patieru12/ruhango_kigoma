<?php
session_start();
require_once "../../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
echo "<h1>Exam</h1><label title='Check this to preview all diagnostic'><input onclick='if($(\"#_exam-all\").prop(\"checked\")){ $(\"#custom-exam\").val(\"all\"); $(\"._custom-exam-data\").removeProp(\"checked\"); } else { $(\"#custom-exam\").val(\"\"); $(\"._custom-exam-data\").removeProp(\"checked\"); }' type=checkbox id='_exam-all' checked value='all'>All</label><br />";
/* $diag = returnAllData("SELECT * FROM co_diagnostic WHERE DiagnosticCode != '' ORDER BY DiagnosticCode ASC",$con);
foreach($diag as $d){
	$ommitted = false;
	$dd = ommitStringPart($str=$d['DiagnosticName'],26,$ommitted);
	echo "<label title=\"{$d['DiagnosticCode']}: {$d['DiagnosticName']}\" ><input type=checkbox class='_custom-diag-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-diag\").val() == \"all\"){ $(\"#custom-diag\").val(\"\"); }  $(\"#custom-diag\").val($(\"#custom-diag\").val() + \"{$d['DiagnosticID']};\"); } else{ $(\"#custom-diag\").val($(\"#custom-diag\").val().replace(\"{$d['DiagnosticID']};\",\"\")); }' value='{$d['DiagnosticID']}'/>{$dd}</label><br />";
}
 */
$diag = returnAllData("SELECT DISTINCT la_exam.ExamName, la_exam.ExamID FROM la_exam ORDER BY ExamName ASC",$con);
foreach($diag as $d){
	$ommitted = false;
	$dd = ommitStringPart($str=$d['ExamName'],26,$ommitted);
	echo "<label title=\"{$d['ExamName']}\"><input type=checkbox class='_custom-exam-data' onclick='if($(this).prop(\"checked\")){ if($(\"#custom-exam\").val() == \"all\"){ $(\"#custom-exam\").val(\"\"); }  $(\"#custom-exam\").val($(\"#custom-exam\").val() + \"{$d['ExamID']};\"); $(\"#_exam-all\").removeProp(\"checked\"); } else{ $(\"#custom-exam\").val($(\"#custom-exam\").val().replace(/{$d['ExamID']};/g,\"\")); }' value='{$d['ExamID']}'>{$dd}</label><br />";
}
?>