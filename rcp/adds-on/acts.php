<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_SESSION['acts']);
?>
<script>
	
	$("#actname<?php echo $_GET['number'] ?>").autocomplete("./auto/act.php", {
		selectFirst: true
	});
	var query_sent_act = false;
	$("#actname<?php echo $_GET['number'] ?>").keyup(function(e){
		
		/* check if the act is simple wound dressing and fill with gant no sterile 1 */
		var pecime_pattern = /pansement simple/;
		if(pecime_pattern.test($("#actname<?= $_GET['number'] ?>").val().toLowerCase() ) && !query_sent_act ){
			query_sent_act = true;
			//console.log($("#olddataact").val());
			if($("#olddataact").val() == 1){
				$(".consumable1").load("./adds-on/consumable.php?number=1&code=10");
			} else{
				$(".consumable" + $("#olddataact").val()).load("./adds-on/consumable.php?number=" + $("#olddataact").val() + "&code=10");
			}
		}
		//alert("<?php echo $_GET['number'] ?>");
		var count=0;
		var l=<?php echo $_GET['number'] ?> - 1;
		while(l>0){
			//alert(l);
			//check if the the intered value is equal to any of the previeous record and save it
			id = "actname" + l;
			if($("#actname<?php echo $_GET['number'] ?>").val() == $("#" + id).val()){
				//alert("EXIST BEFORE!");
				count++;
			}
			l--;
		}
		if(count > 0)
			$("#error<?= $_GET['number'] ?>").html("Act Repeated " + (1 + count) + " time" + ((1 + count)>1?"s":""));
	});
</script>
<div style='height:1px; border:0px solid #000; position:relative; top: 4px; left:-40px;'>
	<span style='font-size:12px; cursor:pointer;'>
		<img onclick='$("#actdate<?php echo $_GET['number'] ?>").val("<?= date('Y-m-d',time()); ?>"); $("#actname<?php echo $_GET['number'] ?>").focus();' src="../images/b_calendar.png" title="Today" alt="Today" />
		<img onclick='$("#actdate<?php echo $_GET['number'] ?>").val($("#consultation_date").val()); $("#actname<?php echo $_GET['number'] ?>").focus();' src="../images/b_insrow.png" title="Reception Date" alt="Reception Date" />
	</span>
</div>
<table class=list-1>
	<tr>
		
		<td><input onclick='' id='actdate<?php echo $_GET['number'] ?>' type=text name='actdate<?php echo $_GET['number'] ?>' placeholder='Select of the Act' class="txtfield1 all_date" value='<?php echo date("Y-m-d",time()); ?>' style='width:75px; font-size:12px; font-weight:bold; position:relative; top: 2px;' />
		
		</td>
		<td><input id='actname<?php echo $_GET['number'] ?>' type=text name='actname<?php echo $_GET['number'] ?>' placeholder='Enter Act Name' class=txtfield1  style='width:150px; font-size:12px;' />
		
		</td>
		<td><input id='actquantity<?php echo $_GET['number'] ?>' type=text name='actquantity<?php echo $_GET['number'] ?>' placeholder='Enter Act Quantity' class=txtfield1  style='width:65px; font-size:12px;' value=1 />
		<span class=error-text id='error<?= $_GET['number'] ?>'></span>
		</td>
	</tr>
</table>
<input type=hidden id='actexistbefore<?= $_GET['number']; ?>' name='actexistbefore<?= $_GET['number']; ?>' />
</div>
<div style='border:0px solid #000; text-align:left; min-height:1px;' class='acts<?php echo ($_GET['number'] + 1) ?>'>
<label id='anotheract' style='max-height:1px;'>
<img style='cursor:pointer; position:relative; top:0px; left:-30px;'  width='25px' title='Add New Medicine' src="../images/256.png" /></label>
<script>
	$('#anotheract').click(function(){
		$(".acts<?= ($_GET['number'] + 1) ?>").load("./adds-on/acts.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 2?"&code=21":"" ?><?= @$_GET['code'] == 500 || @$_GET['code'] == 501 ?"&code=600":"" ?><?= @$_GET['code'] >= 7000 ?"&code=" . (1 + $_GET['code']):"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?>");
		//delay a bit to wait for page loading
		setTimeout(function(e){$("#actname<?= ($_GET['number'] + 1) ?>").focus();},100);
	});
	
</script>

<script>
	$("#actdate<?= $_GET['number'] ?>").val($("#consultation_date").val());
	$("#ac_counter").val("<?= $_GET['number'] ?>");
	
</script>

<?php
if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['acts'])){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	//var_dump($_SESSION);
	$string = $_SESSION['acts'][($_GET['number'] - 1)]['Name'];
	?>
	<script>
		console.log("No Error!");
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#actquantity<?= $_GET['number'] ?>").val("<?= $_SESSION['acts'][($_GET['number'] - 1)]['Quantity'] ?>");
		$("#actdate<?= $_GET['number'] ?>").val("<?= $_SESSION['acts'][($_GET['number'] - 1)]['Date'] ?>");
		
		$("#actexistbefore<?= $_GET['number'] ?>").val("<?= $_SESSION['acts'][($_GET['number'] - 1)]['ActRecordID'] ?>");
		<?php
		if($_GET['number'] < count($_SESSION['acts'])){
			?>
			$("#anotheract").click();
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