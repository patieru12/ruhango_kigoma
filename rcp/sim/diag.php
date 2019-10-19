<?php
//var_dump($_GET);// die;
require_once "../../lib/db_function.php";
//check the price for medicine
$records = returnSingleField($sql="SELECT DiagnosticID from co_diagnostic WHERE DiagnosticName='".PDB($_GET['diag'],true,$con)."' && DiagnosticCode !=''",$field="DiagnosticID",$data=true, $con);
//echo $sql;
//var_dump($records);

if(!$records){
	?>
	<style>
		#diagname<?= $_GET['number'] ?>{
			border-color:red;
		}
	</style>
	<script>
		$("#diagnamelog<?= $_GET['number'] ?>").html("<span class=error-text>Can't be saved</span>");
		//alert("Daignostic Number <?= $_GET['number'] ?>\n Can't be saved!");
	</script>
	<?php
}
?>
<br />