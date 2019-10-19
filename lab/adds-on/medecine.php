<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
?>
<script>
	
	$("#medecinename<?php echo $_GET['number'] ?>").autocomplete("./auto/medecine.php", {
		selectFirst: true
	}); 
</script>
<div style='height:1px; border:0px solid #000;'>
	<input type=text id='medecinedate<?php echo $_GET['number'] ?>' name='medecinedate<?php echo $_GET['number'] ?>' class="txtfield1 all_date" style='width:75px; font-size:12px; font-weight:bold; position:relative; top: 4px; left:-80px;' placeholder='Date' value='<?= date("Y-m-d",time()) ?>' onclick="ds_sh(this,'medecinedate<?php echo $_GET['number'] ?>')" />
</div>
<table class=list-1>
	<?php
	if($_GET['number'] == 1){
		;//echo "<tr><th>Name</th><th>Quantity</th></tr>";
	}
	?>
	<tr>
		<td><input type=text id='medecinename<?php echo $_GET['number'] ?>' name='medecinename<?php echo $_GET['number'] ?>' placeholder='Medicine Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' /></td>
		<td><input type=text id='medecinequantity<?php echo $_GET['number'] ?>' name='medecinequantity<?php echo $_GET['number'] ?>' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' placeholder='Medicine Quantity' /></td>
	</tr>
</table>
<input type=hidden id='medecineexistbefore<?= $_GET['number']; ?>' name='medecineexistbefore<?= $_GET['number']; ?>' />
</div>
<div style='border:0px solid #000; text-align:left; min-height:1px;' class='medecine<?php echo ($_GET['number'] + 1) ?>'>
<label id='another'>
	<img style='cursor:pointer; position:relative; top:-15px; left:300px;' width='15px' title='Add New Medicine' src="../images/256.png" />
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
	});
	
	if($("#medecinename<?= $_GET['number'] ?>").val()){
		$("#olddata").val("<?= $_GET['number'] + 1 ?>");
	}
	
	$("#medecinename<?= $_GET['number'] ?>").keyup(function(e){
		if($("#medecinename<?= $_GET['number'] ?>").val()){
			$("#olddata").val("<?= $_GET['number'] + 1 ?>");
		}
	});
	
	$('#another').click(function(){
		$(".medecine<?= ($_GET['number'] + 1) ?>").load("./adds-on/medecine.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 2?"&code=21":"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?>");
		$("#medecinename<?= $_GET['number'] + 1 ?>").focus();
	});
</script>

<script>
	$("#medecinedate<?= $_GET['number'] ?>").val($("#consultation_date").val());
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
} else if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['medecines'])){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = $_SESSION['medecines'][($_GET['number'] - 1)]['MedecineName'];
	?>
	<script>
		$("#medecinename<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#medecinedate<?= $_GET['number'] ?>").val("<?= $_SESSION['medecines'][($_GET['number'] - 1)]['Date'] ?>");
		$("#medecinequantity<?= $_GET['number'] ?>").val("<?= $_SESSION['medecines'][($_GET['number'] - 1)]['Quantity'] ?>");
		$("#medecineexistbefore<?= $_GET['number'] ?>").val("<?= $_SESSION['medecines'][($_GET['number'] - 1)]['MedecineRecordID'] ?>");
		$("#another").click();
	</script>
	<?php
}
?>
