<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_SESSION['acts']);
require_once "../../lib/db_function.php";
?>
<script>
	$("#actname<?php echo $_GET['number'] ?>").autocomplete("./auto/act.php", {
		selectFirst: true
	});
	var query_sent_act = false;
	$("#actname<?php echo $_GET['number'] ?>").keyup(function(e){
		
		/* check if the act is simple wound dressing and fill with gant no sterile 1 */
		var pecime_pattern = /pansement/;
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
<div style='height:1px; border:0px solid #000;'>
	<input onclick='ds_sh(this,"actdate<?php echo $_GET['number'] ?>")' id='actdate<?php echo $_GET['number'] ?>' type=text name='actdate<?php echo $_GET['number'] ?>' placeholder='Select of the Act' class="txtfield1 all_date" value='<?php echo date("Y-m-d",time()); ?>' style='width:75px; font-size:12px; font-weight:bold; position:relative; top: 2px; left:-80px;' />
</div>
<table class=list-1>
	<tr>
		<td style='width:140px; border:0px solid #000; font-size:12px;'>
			<label style='cursor:pointer' class='current_act<?php echo $_GET['number'] ?>'><input class='aa<?= $_GET['number'] ?>' type=radio onclick='$("#actname<?= $_GET['number'] ?>").val("<?= returnSingleField("SELECT Name FROM ac_name WHERE Name='pansement simple'","Name",$data=true, $con) ?>"); $("#actname<?= $_GET['number'] ?>").keyup(); ' name='autoact<?= $_GET['number'] ?>' <?= (strtoupper(@$_SESSION['acts'][($_GET['number'] - 1)]['Name'])) == "pansement simple"?"checked":""; ?>>pst simple</label><br />
			<label style='cursor:pointer' class='current_act<?php echo $_GET['number'] ?>'><input class='aa<?= $_GET['number'] ?>' type=radio onclick='$("#actname<?php echo $_GET['number'] ?>").val("<?= returnSingleField("SELECT Name FROM ac_name WHERE Name='pansement compliqué'","Name",$data=true, $con)?>"); $("#actname<?= $_GET['number'] ?>").keyup();' name='autoact<?= $_GET['number'] ?>' <?= (strtoupper(@$_SESSION['acts'][($_GET['number'] - 1)]['Name'])) == "Pansement compliqué"?"checked":""; ?>>pst compliqué</label>
		</td>
		<td><input id='actname<?php echo $_GET['number'] ?>' type=text name='actname<?php echo $_GET['number'] ?>' placeholder='Enter Act Name' class=txtfield1  style='width:150px; font-size:12px;' />
		<span class=error-text id='error<?= $_GET['number'] ?>'></span>
		</td>
	</tr>
</table>
<input type=hidden id='actexistbefore<?= $_GET['number']; ?>' name='actexistbefore<?= $_GET['number']; ?>' />
</div>
<div style='border:0px solid #000; text-align:left; min-height:1px;' class='acts<?php echo ($_GET['number'] + 1) ?>'>
<label id='anotheract'>
<img style='cursor:pointer; position:relative; top:-15px; left:285px;'  width='15px' title='Add New Medicine' src="../images/256.png" /></label>
<script>
	$('#anotheract').click(function(){
		$(".acts<?= ($_GET['number'] + 1) ?>").load("./adds-on/acts_pst.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 2?"&code=21":"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?>");
		$("#actname<?= $_GET['number'] + 1 ?>").focus();
	});
</script>

<script>
	$("#actdate<?= $_GET['number'] ?>").val($("#consultation_date").val());
</script>

<?php
if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['acts'])){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	//build the coartem request
	$string = $_SESSION['acts'][($_GET['number'] - 1)]['Name'];
	?>
	<script>
		$("#actname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#actdate<?= $_GET['number'] ?>").val("<?= $_SESSION['acts'][($_GET['number'] - 1)]['Date'] ?>");
		
		$("#actexistbefore<?= $_GET['number'] ?>").val("<?= $_SESSION['acts'][($_GET['number'] - 1)]['ActRecordID'] ?>");
		
		$("#anotheract").click();
	</script>
	<?php
}
?>