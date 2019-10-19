<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
?>
<div style='height:1px; border:0px solid #000; position:relative; top: 4px; left:-40px;'>
	<span style='font-size:12px; cursor:pointer;'>
		<img onclick='$("#medecinedate<?php echo $_GET['number'] ?>").val("<?= date('Y-m-d',time()); ?>"); $("#medecinename<?php echo $_GET['number'] ?>").focus();' src="../images/b_calendar.png" title="Today" alt="Today" />
		<img onclick='$("#medecinedate<?php echo $_GET['number'] ?>").val($("#consultation_date").val()); $("#medecinename<?php echo $_GET['number'] ?>").focus();' src="../images/b_insrow.png" title="Reception Date" alt="Reception Date" />
	</span>
	
</div>
<table class=list-1>
	<?php
	if($_GET['number'] == 1){
		;//echo "<tr><th>Name</th><th>Quantity</th></tr>";
	}
	?>
	<tr>
		<td>
			
			<?php /*<input type=text id='medecinedate<?php echo $_GET['number'] ?>' name='medecinedate<?php echo $_GET['number'] ?>' class="txtfield1 all_date" style='width:75px; font-size:12px; font-weight:bold;' placeholder='Date' value='' onclick="" /> */ ?>
			<input type=text id='medecinedate<?php echo $_GET['number'] ?>' name='medecinedate<?php echo $_GET['number'] ?>' class="txtfield1 all_date" style='width:75px; font-size:12px; font-weight:bold;' placeholder='Date' value='' onclick="" />
			
			
		</td>
		<td><input type=text id='medecinename<?php echo $_GET['number'] ?>' name='medecinename<?php echo $_GET['number'] ?>' placeholder='Medicine Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' /></td>
		<td><input type=text id='medecinequantity<?php echo $_GET['number'] ?>' autocomplete="off" name='medecinequantity<?php echo $_GET['number'] ?>' class=txtfield1 style='width:65px; font-size:12px; text-align:right; font-weight:bold;' placeholder='Medicine Quantity' /></td>
		<td id='stock_level<?php echo $_GET['number'] ?>'></td>
	</tr>
</table>
<input type=hidden id='medecineexistbefore<?= $_GET['number']; ?>' name='medecineexistbefore<?= $_GET['number']; ?>' />
</div>
<div style='border:0px solid #000; text-align:left; min-height:1px;' class='medecine<?php echo ($_GET['number'] + 1) ?>'>
<label id='another'>
	<img style='cursor:pointer; position:relative; top:0px; left:-30px;' width='25px' title='Add New Medicine' src="../images/256.png" />
</label>
<input type=hidden id=olddata value='<?= ($_GET['number']) ?>' />

<script>
	$("#medecinename<?= $_GET['number'] ?>").keyup(function(e){
		var pattern = /^coartem/;
		if(pattern.test($("#medecinename<?= $_GET['number'] ?>").val() )){
			$("#medecinequantity<?= $_GET['number'] ?>").val("1");
		} else{
			//alert($("#medecinename<?= $_GET['number'] ?>").val());
		}
		
		if($("#medecinename<?= $_GET['number'] ?>").val()){
			$("#olddata").val("<?= $_GET['number'] + 1 ?>");
		}
		
		var lidoc = /lidocaine 2% 30 ml/;
		if(lidoc.test($("#medecinename<?= $_GET['number'] ?>").val())){
			$("#medecinename<?= $_GET['number'] ?>").after("<input id='lidoc<?= $_GET['number'] ?>' type=text placeholder='ML' class='txtfield1' style='width:30px; font-size:12px; font-weight:bold; margin-left:4px;' />");
			$("#lidoc<?= $_GET['number'] ?>").focus();
			$("#medecinequantity<?= $_GET['number'] ?>").width("30px");
			
			$("#lidoc<?= $_GET['number'] ?>").keyup(function(e){
				//alert($("#lidoc").val());
				var qty_ml = $("#lidoc<?= $_GET['number'] ?>").val();
				var qty = qty_ml/30;
				$("#medecinequantity<?= $_GET['number'] ?>").val(qty.toFixed(2));
			});
		}
		
		var lidoc = /lidocaine 2% 50 ml/;
		if(lidoc.test($("#medecinename<?= $_GET['number'] ?>").val())){
			$("#medecinename<?= $_GET['number'] ?>").after("<input id='lidoc<?= $_GET['number'] ?>' type=text placeholder='Enter QTY in ml' class='txtfield1' style='width:55px; font-size:12px; font-weight:bold; margin-left:4px;' />");
			$("#lidoc<?= $_GET['number'] ?>").focus();
			
			$("#lidoc<?= $_GET['number'] ?>").keyup(function(e){
				//alert($("#lidoc").val());
				var qty_ml = $("#lidoc<?= $_GET['number'] ?>").val();
				var qty = qty_ml/50;
				$("#medecinequantity<?= $_GET['number'] ?>").val(qty);
			});
		}
		//test the perfusion medicines
		perfusion(<?= $_GET['number'] ?>);
	});
	
	if($("#medecinename<?= $_GET['number'] ?>").val()){
		$("#olddata").val("<?= $_GET['number'] + 1 ?>");
	}
	$("#medecinequantity<?= $_GET['number'] ?>").keyup(function(){
		//if($("#medecinequantity<?= $_GET['number'] ?>").val()>0){
			//call emballage check function
			Emballage("<?= $_GET['number'] ?>");
		//}
	});
	$('#another').click(function(){
		$(".medecine<?= ($_GET['number'] + 1) ?>").load("../rcp/adds-on/medecine.php?number=<?php echo ($_GET['number'] + 1) ?>&wght=<?php echo (@$_GET['wght']) ?><?= @$_GET['code'] == 30?"&code=31":"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?><?= @$_GET['code'] >= 7000?"&code=".(1 + $_GET['code']):"" ?><?= @$_GET['code'] == 7502?"&code=5000":"" ?>");
		setTimeout( function(e){
			$("#medecinename<?= $_GET['number'] + 1 ?>").focus();
		},1000);
	});
	
</script>

<script>
	$("#medecinedate<?= $_GET['number'] ?>").val($("#default_date").val());
	//$("#medecinedate<?= $_GET['number'] ?>").val($("#consultation_date").val());
	$("#med_counter").val("<?= $_GET['number'] ?>");
</script>
<?php
if(@$_GET['code'] == 3){
	$string = "quinine 300 mg";
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		/* $("#medecinequantity<?= $_GET['number'] ?>").focus();
		$("#medecinequantity<?= $_GET['number'] ?>").select(); */
	</script>
	<?php
} else if(@$_GET['code'] == 21){
	$string = "paracetamol 500 mg";
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		/* $("#medecinequantity<?= $_GET['number'] ?>").focus();
		$("#medecinequantity<?= $_GET['number'] ?>").select(); */
	</script>
	<?php
} else if(@$_GET['code'] == 30){
	$string = "fer-folic-acid";
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("30");
		/* $("#medecinequantity<?= $_GET['number'] ?>").focus();
		$("#medecinequantity<?= $_GET['number'] ?>").select(); */
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 31){
	$string = "mebendazole 100 mg";
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#medecinequantity<?= $_GET['number'] ?>").focus();
		$("#medecinequantity<?= $_GET['number'] ?>").select();
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 2 && @$_GET['wght'] >= 5){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "coartem ";
	if($_GET['wght'] >= 5 && $_GET['wght'] < 15){
		$string .= "6x1";
	} else if($_GET['wght'] >= 15 && $_GET['wght'] < 25){
		$string .= "6x2";
	} else if($_GET['wght'] >= 25 && $_GET['wght'] < 35){
		$string .= "6x3";
	} else if($_GET['wght'] >= 35){
		$string .= "6x4";
	}
	
	//echo $string;
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("1");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 300 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "mebendazole 100 mg";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 301 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "metronidazole 250 mg";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 302 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "metronidazole 250 mg";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 303 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "nystatine ovule 100000 ui";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 304 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "co-trimoxazole 480 mg";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 305 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "mebendazole 100 mg";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 306 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "ac.nalidixique 500 mg";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] >= 7000 && @$_GET['code'] <= 7002 ){
	//try to write coartem that correspond to the given weight
	$medecine_data = array(7000=>"ocytocine 1ml, 10 ui","vit k inj.","tetracycline 1% pommade opht.");
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = $medecine_data[$_GET['code']];
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("1");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] >= 7500 && @$_GET['code'] <= 7502 ){
	//try to write coartem that correspond to the given weight
	$medecine_data = array(7500=>"ocytocine 1ml, 10 ui","vit k inj.","tetracycline 1% pommade opht.");
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = $medecine_data[$_GET['code']];
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("1");
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 5000 ){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = "lidocaine 2% 30 ml";
	
	//connect database now and search the correct medicine name
	if(file_exists($filename = "../../lib/db_function.php")){
		require_once $filename;
		$string = returnSingleField($sql = "SELECT MedecineName FROM md_name WHERE MedecineName LIKE('%{$string}%');","MedecineName",$data=true, $con);
		//echo $sql;
	}
	
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("");
		$("#medecinename<?= $_GET['number'] ?>").keyup();
		$("#another").click();
	</script>
	<?php
} else if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['medecines'])){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = $_SESSION['medecines'][($_GET['number'] - 1)]['MedecineName'];
	?>
	<script>
		
		tropho_sent = true;
		ascaris_sent = true;
		kehist_sent = true;
		trichomnas_sent = true;
		levure_sent = true;
		gb_sent = true;
		ankylostome_sent = true;
		gr_sent = true;
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinedate<?= $_GET['number'] ?>").val("<?= $_SESSION['medecines'][($_GET['number'] - 1)]['Date'] ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("<?= $_SESSION['medecines'][($_GET['number'] - 1)]['Quantity'] ?>");
		$("#medecineexistbefore<?= $_GET['number'] ?>").val("<?= $_SESSION['medecines'][($_GET['number'] - 1)]['MedecineRecordID'] ?>");
		<?php
		if($_GET['number'] < count($_SESSION['medecines'])){
			?>
			$("#another").click();
			<?php
		}
		?>
	</script>
	<?php
}
?>

<script>
	$("#medecinename<?php echo $_GET['number'] ?>").autocomplete("./auto/medecine.php?date=" + $("#consultation_date").val(), {
		selectFirst: true
	}); 
</script>