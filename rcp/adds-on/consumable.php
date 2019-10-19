<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
//var_dump($_SESSION['consumables']); echo "<hr />";

//var_dump($_SESSION['consumables'][($_GET['number'] - 1)]);
$value = 0;
?>

<div style='height:1px; border:0px solid #000; position:relative; top: 4px; left:-40px;'>
	<span style='font-size:12px; cursor:pointer;'>
		<img onclick='$("#consumabledate<?php echo $_GET['number'] ?>").val("<?= date('Y-m-d',time()); ?>"); $("#consumablename<?php echo $_GET['number'] ?>").focus();' src="../images/b_calendar.png" title="Today" alt="Today" />
		<img onclick='$("#consumabledate<?php echo $_GET['number'] ?>").val($("#consultation_date").val()); $("#consumablename<?php echo $_GET['number'] ?>").focus();' src="../images/b_insrow.png" title="Reception Date" alt="Reception Date" />
	</span>
</div>
<script>
	$("#consumablename<?php echo $_GET['number'] ?>").autocomplete("./auto/materials.php?date=" + $("#consumabledate<?php echo $_GET['number'] ?>").val(), {
		selectFirst: true
	}); 
</script>

<table class=list-1>
	<tr>
		<td><input value='<?= date("Y-m-d",time()) ?>' type=text id='consumabledate<?php echo $_GET['number'] ?>' name='consumabledate<?php echo $_GET['number'] ?>' placeholder='Date' class="txtfield1 all_date" style='width:75px; font-size:12px; font-weight:bold; position:relative; top: 2px; left:-1px;' onclick="" /></td>
		<td><input type=text id='consumablename<?php echo $_GET['number'] ?>' name='consumablename<?php echo $_GET['number'] ?>' placeholder='Consumable Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' /></td>
		<td><input type=text id='consumablequantity<?php echo $_GET['number'] ?>' name='consumablequantity<?php echo $_GET['number'] ?>' placeholder='Consumable Quantity' class=txtfield1 style='width:65px; font-size:12px; font-weight:bold;' /></td>
	</tr>
</table>
<input type=hidden id='consumableexistbefore<?= $_GET['number']; ?>' name='consumableexistbefore<?= $_GET['number']; ?>' />

<div style='border:0px solid #000; text-align:left; min-height:1px;' class='consumable<?php echo ($_GET['number'] + 1) ?>'>
<label  id='another_consumable'>
<img style='cursor:pointer; position:relative; top:0px; left:-30px;' width='25px' title='Add New Medicine' src="../images/256.png" /></label>
<input type=hidden id=olddataact value='<?= ($_GET['number']) ?>' />

<script>
	if($("#consumabledate0").val() == ""){
		$("#consumabledate0").val($("#consultation_date").val());
	}
	$("#consumabledate<?= $_GET['number'] ?>").val($("#consultation_date").val());
	$("#cons_counter").val("<?= $_GET['number'] ?>");
</script>

<script>
	$("#another_consumable").click(function(e){
		//console.log("New Query Sent Now " + $("#id_value").val());
		$(".consumable<?= $_GET['number'] + 1 ?>").load("./adds-on/consumable.php?number=<?= ($_GET['number'] + 1) ?><?= @$_GET['code'] == 4?"&code=4":"" ?><?= ((@$_GET['code'] >= 440 && @$_GET['code'] <= 445) || @$_GET['code']>=7000)?"&code=".(1 + $_GET['code']):"" ?><?= @$_GET['code'] >= 550 && @$_GET['code'] <= 555?"&code=".(1 + $_GET['code']):"" ?>")
	});
</script>
<?php
if(@$_GET['code'] == 10){
	$string = "gant non sterile ";
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM cn_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		/* $("#medecinequantity<?= $_GET['number'] ?>").focus();
		$("#medecinequantity<?= $_GET['number'] ?>").select(); */
	</script>
	<?php
} else if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['consumables'])){
	$string = $_SESSION['consumables'][($_GET['number'] - 1)]['MedecineName'];
	?>
	<script>
		$("#consumablename<?= ($_GET['number'] )?>").val("<?= $string ?>");
		$("#consumablequantity<?= ($_GET['number'])?>").val("<?= $_SESSION['consumables'][($_GET['number'] - 1)]['Quantity'] ?>");
		$("#consumabledate<?= ($_GET['number']) ?>").val("<?= $_SESSION['consumables'][($_GET['number'] - 1)]['Date'] ?>");
		$("#consumableexistbefore<?= ($_GET['number'])?>").val("<?= $_SESSION['consumables'][($_GET['number'] - 1)]['ConsumableRecordID'] ?>");
		<?php
		if($_GET['number'] < count($_SESSION['consumables'])){
			?>
			/* alert("Click Request can be initiated");*/
			$("#another_consumable").click();
			<?php
		}
		?>
	</script>
	<?php
} else if(@$_GET['code'] == 440){
	$string = "gant non sterile (paire)";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
}  else if(@$_GET['code'] == 441){
	$string = "bande de gaze";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("");
		$("#another_consumable").click();
	</script>
	<?php
}  else if(@$_GET['code'] == 550){
	$string = "gant non sterile (paire)";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 551){
	$string = "gant sterile (paire)";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 6000){
	$string = "gant sterile (paire)";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("5");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7000 || @$_GET['code'] == 7500){
	$string = "gant non sterile - paire";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("15");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7001 || @$_GET['code'] == 7501){
	$string = "sonde d'aspiration";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7502){
	$string = "gant sterile - paire";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 7503){
	$string = "catgut";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] >= 9000 && @$_GET['code'] <= 9001){
	$conss = array(9000=>"catheter g18", "trousse de perfusion");
	$string = $conss[$_GET['code']];
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 552){
	$string = "fil de suture non resorbable";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("1");
		$("#another_consumable").click();
	</script>
	<?php
} else if(@$_GET['code'] == 553){
	$string = "bande de gaze";
	?>
	<script>
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("");
		$("#another_consumable").click();
	</script>
	<?php
} 
?>
<script>
	
	if($("#consumablename<?= $_GET['number'] ?>").val()){
		$("#olddataact").val("<?= $_GET['number'] + 1 ?>");
	}
	
	$("#consumablename<?= $_GET['number'] ?>").keyup(function(e){
		if($("#consumablename<?= $_GET['number'] ?>").val()){
			$("#olddataact").val("<?= $_GET['number'] + 1 ?>");
		}
	});
	
</script>
</div>