<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
?>
<script>
	
	$("#diagname<?php echo $_GET['number'] ?>").autocomplete("./auto/diag.php", {
		selectFirst: true
	});
	
	
	var query_sent_act = false;
	$("#diagname<?php echo $_GET['number'] ?>").blur(function(e){
		AutoPrescription("diag",$("#diagname<?php echo $_GET['number'] ?>").val(),"<?= $_GET['age'] ?>","<?= $_GET['wght'] ?>");
	});
</script>
<table class=list-1 style='width:100%'>
	<tr>
		
		<td>
			<input id='diagname<?php echo $_GET['number'] ?>' type=text name='diagname<?php echo $_GET['number'] ?>' placeholder='Enter Diagnostic From System Recognized List' class=txtfield1  style='width:75%; font-size:12px;' />
			<span id='diagnamelog<?= $_GET['number'] ?>'></span>
		</td>
	</tr>
</table>
<label id='new_style<?= $_GET['number'] ?>'>

</label>
<input type=hidden id='diagexistbefore<?= $_GET['number']; ?>' name='diagexistbefore<?= $_GET['number']; ?>' />
</div>
<div style='border:0px solid #000; text-align:left; min-height:1px;' class='diag<?php echo ($_GET['number'] + 1) ?>'>
<label id='anotherdiag' style='max-height:1px;'>
<img style='cursor:pointer; position:relative; top:-35px; left:-30px;'  width='25px' title='Add New Diagnostic' src="../images/256.png" /></label>
<script>
	$('#anotherdiag').click(function(){
		$(".diag<?= ($_GET['number'] + 1) ?>").load("./adds-on/diag.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 2?"&code=21":"" ?><?= @$_GET['code'] == 500 || @$_GET['code'] == 501 ?"&code=600":"" ?><?= @$_GET['code'] >= 7000 ?"&code=" . (1 + $_GET['code']):"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?>&wght=<?= @$_GET['wght']; ?>&age=<?= $_GET['age']; ?>");
		//delay a bit to wait for page loading
		setTimeout(function(e){$("#diagname<?= ($_GET['number'] + 1) ?>").focus();},100);
	});
	
	$("#diag_counter").val("<?= $_GET['number'] ?>");
	$("#diagname<?= $_GET['number'] ?>").keydown(function(){
		//now verify if is active and print error
		
		$("#diagnamelog<?= $_GET['number'] ?>").html("");
		$("#diagname<?= $_GET['number'] ?>").css("border-color","green");
	});
	$("#diagname<?= $_GET['number'] ?>").blur(function(){
		setTimeout(function(){
			//now verify if is active and print error
			$("#new_style<?= $_GET['number'] ?>").load("./sim/diag.php?&number=<?= $_GET['number'] ?>&diag=" + $("#diagname<?= $_GET['number'] ?>").val().replace(/ /g,"%20"));
		}, 500);
	});
</script>

<?php
if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['diagnostics'])){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	//var_dump($_SESSION);
	$string = $_SESSION['diagnostics'][($_GET['number'] - 1)]['DiagnosticName'];
	?>
	<script>
		console.log("No Error!<?= $_GET['number'] ?>");
		$("#diagname<?= $_GET['number'] ?>").val("<?= $string ?>");
		
		$("#diagexistbefore<?= $_GET['number'] ?>").val("<?= $_SESSION['diagnostics'][($_GET['number'] - 1)]['DiagnosticRecordID'] ?>");
		<?php
		if($_GET['number'] < count($_SESSION['diagnostics'])){
			?>
			$("#anotherdiag").click();
			<?php
			}
		?>
	</script>
	<?php
} else if(@$_GET['code'] == 400 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Pansement Simple";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 401 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Pansement compliqué";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 500 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Suture Simple";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 501 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Suture compliqué";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 600 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Injection IM";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7000 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Acc.eutocique sans épisiotomie";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7500 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Acc.eutocique avec épisiotomie";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 8000 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Acc. Dystocique";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 9000 ){
	$string = "Injection IV";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#anotheract").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7001 || @$_GET['code'] == 7501 || @$_GET['code'] == 8001 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "Injection IM";
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#actquantity<?= $_GET['number'] ?>").val("2");
		$("#anotheract").click();
	</script>
	<?php
}
?>