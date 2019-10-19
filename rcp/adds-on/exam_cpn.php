<?php
session_start();
$_GET['number'] = @$_GET['number']?$_GET['number']:1;
//var_dump($_GET);
require_once "../../lib/db_function.php";
?>
<script>
	
	$("#examname<?php echo $_GET['number'] ?>").keyup(function(){
		$("#examresult<?= $_GET['number'] ?>").val("");
		if($("#lock_value<?php echo $_GET['number'] ?>").val() == 0)
			$(".rr<?= $_GET['number'] ?>").removeAttr("checked");
		if($("#examname<?php echo $_GET['number'] ?>").val().replace(/ /g,"%20")){
			$(".result_list<?php echo $_GET['number'] ?>").load("./adds-on/ads_result.php?wght=<?= @$_GET['wght'] ?>&number=<?php echo $_GET['number'] ?>&exam=" + $("#examname<?php echo $_GET['number'] ?>").val().replace(/ /g,"%20") + "<?= @$_SESSION['exams'][($_GET['number'] - 1)]['Results']?"&rslt=".str_replace(" ","%20",$_SESSION['exams'][($_GET['number'] - 1)]['Results']):"" ?>");
			$("#examresult<?= $_GET['number'] ?>").attr("required");
		} else
			$(".result_list<?php echo $_GET['number'] ?>").html("");
	});
	function search(key, number){
		//$("#examname" + number).keyup(function(){
		$("#examresult" + number).val("");
			if(key.replace(/ /g,"%20"))
				$(".result_list" + number).load("./adds-on/ads_result.php?wght=<?= @$_GET['wght'] ?>&number=" + number + "&exam=" + key.replace(/ /g,"%20"));
			else
				$(".result_list" + number).html("");
		//});
	}
	$("#examname<?php echo $_GET['number'] ?>").autocomplete("./auto/exam.php", {
		selectFirst: true
	}); 
	$("#tag2").autocomplete("autocomplete.php", {
		selectFirst: true
	});
	$("#examresult<?php echo $_GET['number'] ?>").focus(function(){
		if($("#examname<?php echo $_GET['number'] ?>").val().replace(/ /g,"%20"))
			$("#exrslt<?php echo $_GET['number'] ?>").load("./adds-on/ads_result.php?number=<?php echo $_GET['number'] ?>&exam=" + $("#examname<?php echo $_GET['number'] ?>").val().replace(/ /g,"%20"));
	});
</script>
<?php
if($_GET['number'] > 1){
	?>
	<style>
		#ex_table td {border-top:1px solid #000; font-size:12px;}
	</style>
	
	<?php
}
?>
<input type=hidden id='lock_value<?php echo $_GET['number'] ?>' value='0' />
<div style='height:1px; border:0px solid #000;'>
	<input type=text name='examdate<?php echo $_GET['number'] ?>' id='exam_date<?php echo $_GET['number'] ?>' placeholder='Enter Exam Date' onclick="ds_sh(this,'exam_date<?php echo $_GET['number'] ?>')" value='<?php echo date("Y-m-d",time()) ?>' class="txtfield1 all_date" style='width:75px; font-size:12px; position:relative; top: 18px; left:-80px;' />
</div>
<table style='width:' id=ex_table class=list-1 border=0>
	<tr >
		<td bgcolor='#e0e0e0' style='width:100px; border:0px solid #000; font-size:12px;'>
			<label class='current_exam<?php echo $_GET['number'] ?>'><input class='rr<?= $_GET['number'] ?>' type=radio onclick='$("#examname<?php echo $_GET['number'] ?>").val("<?php echo $a = returnSingleField("SELECT ExamName FROM la_exam WHERE ExamName='Hb'","ExamName",$data=true, $con)?>"); search("<?php echo $a ?>", "<?php echo $_GET['number'] ?>");' name='exaauto<?php echo $_GET['number'] ?>' <?= (strtoupper(@$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'])) == "HB"?"checked":""; ?>>Hb</label><br />
			<label class='current_exam<?php echo $_GET['number'] ?>'><input class='rr<?= $_GET['number'] ?>' type=radio onclick='$("#examname<?php echo $_GET['number'] ?>").val("<?php echo $b = returnSingleField("SELECT ExamName FROM la_exam WHERE ExamName='Albuminurie'","ExamName",$data=true, $con)?>"); search("<?php echo $b ?>", "<?php echo $_GET['number'] ?>");' name='exaauto<?php echo $_GET['number'] ?>' <?= (strtoupper(@$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'])) == "ALBUMINURIE"?"checked":""; ?>>Albuminurie</label>
		</td>
		<td bgcolor='#e0e0e0' style='width:170px;'>
			<input type=text id='examname<?php echo $_GET['number'] ?>' name='examname<?php echo $_GET['number'] ?>' placeholder='Enter Exam Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' />
		</td>
		<td bgcolor='#efefef' style='width:120px;'>
		<input type=text id='examid<?php echo $_GET['number'] ?>' name='examid<?php echo $_GET['number'] ?>' placeholder='Enter Result ID' class=txtfield1 style='width:100px; font-size:12px; font-weight:bold;' /><br />
		<div class='examnumber<?= $_GET['number'] ?>'></div>
		</td>
		
		<td bgcolor='#e0e0e0' style='width:600px;'>
			<div style='max-height:45px; overflow:auto; width:100%; font-size:10px;' class='result_list<?php echo $_GET['number'] ?>'></div>
		</td>
		<td bgcolor='#e0e0e0' style='width:151px;'>
			<input type=text name='examresult<?php echo $_GET['number'] ?>' id='examresult<?php echo $_GET['number'] ?>' placeholder='Enter Result For Exam' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' />
		</td>
	</tr>
</table>
<input type=hidden id='examexistbefore<?= $_GET['number']; ?>' name='examexistbefore<?= $_GET['number']; ?>' />
<div style='border:0px solid #000; width:100%; text-align:left; min-height:1px;' class='exam<?php echo ($_GET['number'] + 1) ?>'>
	<label id= 'anotherexam'>
	<img style='cursor:pointer; position:relative; top:0px; left:-30px;' width='25px' title='Add New Exam' src="../images/256.png" /></label>
</div>
<input type=hidden id='numbersent<?= $_GET['number'] ?>' value=0 />
<script>
	$("#exam_date<?= $_GET['number'] ?>").val($("#consultation_date").val());
	/* $("#examname<?= $_GET['number'] ?>").focus(); */
	$('#anotherexam').click(function(){
		$(".exam<?= ($_GET['number'] + 1) ?>").load("./adds-on/exam_cpn.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 2?"&code=21":"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?>&key=<?= $_GET['key'] ?>");
	});
	$("#examid<?= $_GET['number'] ?>").keyup(function(e){
		if($("#numbersent<?= $_GET['number'] ?>").val() == 0 && $("#examid<?= $_GET['number'] ?>").val().trim() != ""){
			$("#numbersent<?= $_GET['number'] ?>").val("1");
			setTimeout(function(e){
				$("#numbersent<?= $_GET['number'] ?>").val("0");
				//load the same result number if found the database with the link the owner with possibility of correction if necessary
				$(".examnumber<?= $_GET['number'] ?>").load("./find_number.php?exam=" + $("#examname<?= $_GET['number'] ?>").val().replace(/ /g,"%20") + "&number=" + $("#examid<?= $_GET['number'] ?>").val().replace(/ /g,"%20") + "&date=" + $("#exam_date<?= $_GET['number'] ?>").val().replace(/ /g,"%20") + "&key=<?= $_GET['key'] ?>&existing_id=" + $("#examexistbefore<?= $_GET['number'] ?>").val().replace(/ /g,"%20"));
			}, 1000);
		}
	});
</script>

<?php
if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['exams'])){
	//try to write coartem that correspond to the given weight
	//echo $_GET['wght'];
	//now connect database
	$string = @$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'];
	?>
	<script>
		$("#examname<?= $_GET['number'] ?>").val("<?= $string ?>");
		$("#lock_value<?php echo $_GET['number'] ?>").val("1");
		$("#examname<?php echo $_GET['number'] ?>").keyup();
		
		$("#examid<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ExamNumber'] ?>");
		$("#examexistbefore<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ExamRecordID'] ?>");
		$("#exam_date<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ResultDate'] ?>");
		$("#examresult<?= $_GET['number'] ?>").val("<?= str_replace("_s","+",$_SESSION['exams'][($_GET['number'] - 1)]['Results']) ?>");
		
		$("#anotherexam").click();
	</script>
	<?php
}
?>