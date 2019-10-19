<br /><?php
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
require_once "../../lib/db_function.php";
$result = returnAllData($sql="SELECT DISTINCT la_result.ResultName, la_exam.ResultType FROM la_result, la_exam WHERE la_result.ExamID=la_exam.ExamID && la_exam.ExamName='{$_GET['exam']}' && Appear='1' ORDER BY ResultName",$con);
//echo $sql;
//var_dump($_GET);
if($result){
	$ii = 0;
	$i = 0;
	$k = 0;
	echo "<table>";
	foreach($result as $rs){
		if($k == 0)
			echo "<tr>";
		else if($k % 4 == 0)
			echo "<tr>";
		
		$om;
		$result_display = ommitStringPart($rs['ResultName'],15, $om);
		$rslts = explode("; ",str_replace("_s","+",@$_GET['rslt']));
		//var_dump($rslts); echo "<br />";
		echo "<td style='font-size:12px; width:150px;'><label style='cursor:pointer;'".($om?"title='{$rs['ResultName']}'":"")."><input onclick='".($rs['ResultType']?"if($(\"#rs{$i}{$_GET['number']}\").prop(\"checked\")){writeResponseAdd(\"{$rs['ResultName']}\",\"{$_GET['number']}\")} else{ writeResponseRemove(\"{$rs['ResultName']}\",\"{$_GET['number']}\") }":"writeResponse(\"{$rs['ResultName']}\",\"{$_GET['number']}\")")."' type='".($rs['ResultType']?"checkbox":"radio")."' name='rs".($rs['ResultType']?($ii++):"")."{$_GET['number']}' id='rs".($rs['ResultType']?($i++):"")."{$_GET['number']}' ".(@$_GET['rslt'] && in_array($rs['ResultName'],$rslts)?"checked":"")." />{$result_display}</label></td>";
		
		$k++;
		if($k % 4 == 0)
			echo "</tr>";
	}
	echo "</table>";
}

?><!--
<select name='' required class=txtfield1 id='examresult<?php echo $_GET['number'] ?>' style='width:300px'>
	<option value=''>Select Result for <?php echo $_GET['exam'] ?></option>
</select>-->
<script>
	function writeResponseAdd(data,number){
		//alert($("#examresult<?= $_GET['number'] ?>").val());
		/* if($("#examresult<?= $_GET['number'] ?>").val() != "")
			$("#examresult<?= $_GET['number'] ?>").val($("#examresult<?= $_GET['number'] ?>").val() + "; ");
		 */$("#examresult" + number).val($("#examresult" + number ).val() + data + "; ");
	}
	function writeResponseRemove(data, number){
		//$("#examresult<?= $_GET['number'] ?>").val($("#examresult<?= $_GET['number'] ?>").val() + "; ");
		$("#examresult" + number).val($("#examresult" + number).val().replace(data + "; ",""));
		/* $("#examresult<?= $_GET['number'] ?>").val($("#examresult<?= $_GET['number'] ?>").val().replace(data,""));
		$("#examresult<?= $_GET['number'] ?>").val($("#examresult<?= $_GET['number'] ?>").val().replace("; ; ","; "));
		 *///alert($("#examresult<?= $_GET['number'] ?>").val());
		if($("#examresult" + number).val() == "; ")
			$("#examresult" + number ).val("");
		//$("#examresult<?= $_GET['number'] ?>").val($("#examresult<?= $_GET['number'] ?>").val() + data);
	}
	function writeResponse(data, number){
		//alert($("#examresult<?= $_GET['number'] ?>").val());
		
		$("#examresult" + number).val(data);
	}
</script>