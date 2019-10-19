<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
//var_dump($_SESSION['consumables']);
?>

<script>
	$("#consumablename<?php echo $_GET['number'] ?>").autocomplete("./auto/materials.php", {
		selectFirst: true
	}); 
</script>
<div style='height:1px; border:0px solid #000;'>
	<input value='<?= date("Y-m-d",time()) ?>' type=text id='consumabledate<?php echo $_GET['number'] ?>' name='consumabledate<?php echo $_GET['number'] ?>' placeholder='Date' class="txtfield1 all_date" style='width:75px; font-size:12px; font-weight:bold; position:relative; top: 2px; left:-80px;' onclick="ds_sh(this,'consumabledate<?php echo $_GET['number'] ?>')" />
</div>
<table class=list-1>
	<tr>
		<td><input type=text id='consumablename<?php echo $_GET['number'] ?>' name='consumablename<?php echo $_GET['number'] ?>' placeholder='Consumable Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' /></td>
		<td><input type=text id='consumablequantity<?php echo $_GET['number'] ?>' name='consumablequantity<?php echo $_GET['number'] ?>' placeholder='Consumable Quantity' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' /></td>
	</tr>
</table>
<input type=hidden id='consumableexistbefore<?= $_GET['number']; ?>' name='consumableexistbefore<?= $_GET['number']; ?>' />
</div>
<div style='border:0px solid #000; text-align:left; min-height:1px;' class='consumable<?php echo ($_GET['number'] + 1) ?>'>
<label  id='another_consumable'>
<img style='cursor:pointer; position:relative; top:-15px; left:300px;' width='15px' title='Add New Medicine' src="../images/256.png" /></label>
<input type=hidden id=olddataact value='<?= ($_GET['number']) ?>' />

<script>
	$("#consumabledate<?= $_GET['number'] ?>").val($("#consultation_date").val());
</script>
<script>
	$("#another_consumable").click(function(e){
		$(".consumable<?php echo ($_GET['number'] + 1) ?>").load("./adds-on/consumable.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 4?"&code=4":"" ?>")
	});
</script>
<?php
if(@$_GET['code'] == 10){
	$string = "gant non sterile (paire)";
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
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
		$("#consumablename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#consumablequantity<?= $_GET['number'] ?>").val("<?= $_SESSION['consumables'][($_GET['number'] - 1)]['Quantity'] ?>");
		$("#consumabledate<?= $_GET['number'] ?>").val("<?= $_SESSION['consumables'][($_GET['number'] - 1)]['Date'] ?>");
		$("#consumableexistbefore<?= $_GET['number'] ?>").val("<?= $_SESSION['consumables'][($_GET['number'] - 1)]['ConsumableRecordID'] ?>");
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
