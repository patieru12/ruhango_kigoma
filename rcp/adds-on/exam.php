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
			$(".result_list<?php echo $_GET['number'] ?>").load("../rcp/adds-on/ads_result.php?wght=<?= @$_GET['wght'] ?>&number=<?php echo $_GET['number'] ?>&exam=" + $("#examname<?php echo $_GET['number'] ?>").val().replace(/ /g,"%20") + "<?= @$_SESSION['exams'][($_GET['number'] - 1)]['Results']?"&rslt=".str_replace(" ","%20",$_SESSION['exams'][($_GET['number'] - 1)]['Results']):"" ?>");
			$("#examresult<?= $_GET['number'] ?>").attr("required",":true");
		} else
			$(".result_list<?php echo $_GET['number'] ?>").html("");
	});
	function search(key, number){
		//$("#examname" + number).keyup(function(){
		$("#examresult" + number).val("");
			if(key.replace(/ /g,"%20"))
				$(".result_list" + number).load("../rcp/adds-on/ads_result.php?wght=<?= @$_GET['wght'] ?>&number=" + number + "&exam=" + key.replace(/ /g,"%20"));
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
			$("#exrslt<?php echo $_GET['number'] ?>").load("./adds-on/ads_result.php?number=<?php echo $_GET['number'] ?>&wght=<?php echo @$_GET['wght'] ?>&exam=" + $("#examname<?php echo $_GET['number'] ?>").val().replace(/ /g,"%20"));
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
<div style='height:1px; width:80px; border-top:1px solid #000; position:relative; top: 0px; left:-80px;'>
	<?php /*<input type=text name='examdate<?php echo $_GET['number'] ?>' id='exam_date<?php echo $_GET['number'] ?>' placeholder='Enter Exam Date' onclick="ds_sh(this,'exam_date<?php echo $_GET['number'] ?>')" value='' class="txtfield1 all_date date_on" style='width:75px; font-size:12px; margin-top:2px;' /><br />*/ ?>
	<input type=text name='examdate<?php echo $_GET['number'] ?>' id='exam_date<?php echo $_GET['number'] ?>' placeholder='Enter Exam Date' onclick="" value='' class="txtfield1 all_date date_on" style='width:75px; font-size:12px; margin-top:2px;' /><br />
	<span style='font-size:12px; cursor:pointer;'>
		<img onclick='$("#exam_date<?php echo $_GET['number'] ?>").val("<?= date('Y-m-d',time()); ?>")' src="../images/b_calendar.png" title="Today" alt="Today" />
		<img onclick='$("#exam_date<?php echo $_GET['number'] ?>").val($("#consultation_date").val())' src="../images/b_insrow.png" title="Reception Date" alt="Reception Date" />
	</span>
</div>
		
<table style='' id=ex_table class=list-1 border=0>
	<tr >
		<td bgcolor='#efefef' style='width:8%; font-size:12px;'>
			<label class='current_exam<?php echo $_GET['number'] ?>'><input class='rr<?= $_GET['number'] ?>' type=radio onclick='$("#examname<?php echo $_GET['number'] ?>").val("<?php echo $a = returnSingleField("SELECT ExamName FROM la_exam WHERE ExamName='GE'","ExamName",$data=true, $con)?>"); search("<?php echo $a ?>", "<?php echo $_GET['number'] ?>"); $("#examid<?= $_GET['number'] ?>").focus();' name='exaauto<?php echo $_GET['number'] ?>' <?= (strtoupper(@$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'])) == "GE"?"checked":""; ?>>GE</label><br />
			<label class='current_exam<?php echo $_GET['number'] ?>'><input class='rr<?= $_GET['number'] ?>' type=radio onclick='$("#examname<?php echo $_GET['number'] ?>").val("<?php echo $b = returnSingleField("SELECT ExamName FROM la_exam WHERE ExamName='Selles'","ExamName",$data=true, $con)?>"); search("<?php echo $b ?>", "<?php echo $_GET['number'] ?>"); $("#examid<?= $_GET['number'] ?>").focus();' name='exaauto<?php echo $_GET['number'] ?>' <?= (strtoupper(@$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'])) == "SELLES"?"checked":""; ?>>Selles</label>
		</td>
		<td bgcolor='#e0e0e0' style='width:11%;'>
			<input type=text id='examname<?php echo $_GET['number'] ?>' name='examname<?php echo $_GET['number'] ?>' placeholder='Enter Exam Name' class=txtfield1 style='width:150px; font-size:12px; font-weight:bold;' />
		</td>
		<td bgcolor='#efefef' style='width:6%;'>
		<input type=text id='examid<?php echo $_GET['number'] ?>' autocomplete=off name='examid<?php echo $_GET['number'] ?>' placeholder='Enter Result ID' class=txtfield1 style='width:100px; font-size:12px; font-weight:bold;' /><br />
		<div class='examnumber<?= $_GET['number'] ?>'></div>
		</td>
		<td bgcolor='#e0e0e0' style='height:30px; width:300px;'>
			<div style='max-height:45px; overflow:auto; width:100%; font-size:10px;' class='result_list<?php echo $_GET['number'] ?>'></div>
		</td>
		<td bgcolor='#efefef' style='width:120px;'>
			<input type=text name='examresult<?php echo $_GET['number'] ?>' id='examresult<?php echo $_GET['number'] ?>' placeholder='Enter Result For Exam' class=txtfield1 style='width:130px; font-size:12px; font-weight:bold;' />
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
	
	$("#exam_date<?= $_GET['number'] ?>").val($("#default_date").val());
	$("#exam_counter").val("<?= $_GET['number'] ?>");
	/* $("#examname<?= $_GET['number'] ?>").focus(); */
	$('#anotherexam').click(function(){
		$(".exam<?= ($_GET['number'] + 1) ?>").load("../rcp/adds-on/exam.php?number=<?php echo ($_GET['number'] + 1) ?><?= @$_GET['code'] == 2?"&code=21":"" ?><?= @$_GET['code'] == 4?"&code=4":"" ?>&key=<?= @$_GET['key'] ?>&wght=<?= @$_GET['wght'] ?>");
		setTimeout(
			function(e){
				$("#examname<?= $_GET['number'] + 1 ?>").focus();
			},
			100
		);
	});
	$("#examresult<?= $_GET['number'] ?>").keyup(function(e){
		console.log(<?= @$_GET['wght'] ?>);
		queryMedecine("<?= $_GET['number'] ?>","67<?= @$_GET['wght'] ?>");
	});
	/* 
	$("#examid<?= $_GET['number'] ?>").blur(function(e){
		if("<?= @$_GET['source'] ?>" != "labo"){
			//try to simulate the submission now
			setTimeout(function(){
				runSimulation();
			}, 200);
		}
	}); */
	/* $("#examname<?= $_GET['number'] ?>").blur(function(e){
		//try to simulate the submission now
		setTimeout(function(){
			runSimulation();
		}, 200);
	}); */
	$("#examid<?= $_GET['number'] ?>").keyup(function(e){
		if($("#numbersent<?= $_GET['number'] ?>").val() == 0 && $("#examid<?= $_GET['number'] ?>").val().trim() != ""){
			$("#numbersent<?= $_GET['number'] ?>").val("1");
			setTimeout(function(e){
				$("#numbersent<?= $_GET['number'] ?>").val("0");
				//load the same result number if found the database with the link the owner with possibility of correction if necessary
				$(".examnumber<?= $_GET['number'] ?>").load("./find_number.php?exam=" + $("#examname<?= $_GET['number'] ?>").val().replace(/ /g,"%20") + "&number=" + $("#examid<?= $_GET['number'] ?>").val().replace(/ /g,"%20") + "&date=" + $("#exam_date<?= $_GET['number'] ?>").val().replace(/ /g,"%20") + "&key=<?= @$_GET['key'] ?>&existing_id=" + $("#examexistbefore<?= $_GET['number'] ?>").val().replace(/ /g,"%20"));
			}, 500);
			
		}
	});
</script>

<?php
if(@$_GET['source'] == 'labo'){
	if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['exams'])){
		//try to write coartem that correspond to the given weight
		//echo $_GET['wght'];
		//now connect database
		$string = @$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'];
		?>
		<script>
			var wait = 100;
			wait *= <?= $_GET['number'] ?>;
			wait += 500;
			console.log("The milliseconds to wait is: " + wait);
			$("#examname<?= $_GET['number'] ?>").val("<?= $string ?>");
			$("#lock_value<?php echo $_GET['number'] ?>").val("1");
			$("#examname<?php echo $_GET['number'] ?>").keyup();
			
			$("#examid<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ExamNumber'] ?>");
			$("#examexistbefore<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ExamRecordID'] ?>");
			$("#exam_date<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ResultDate'] ?>");
			$("#examresult<?= $_GET['number'] ?>").val("<?= str_replace("_s","+",$_SESSION['exams'][($_GET['number'] - 1)]['Results']) ?>");
			/* setTimeout(
				function(e){
					queryMedecine("<?= $_GET['number'] ?>","<?= @$_GET['wght'] ?>");
				}, wait
			); */
			$("#anotherexam").click();
		</script>
		<?php
	}
} else{
	if(@$_GET['code'] == 4 && $_GET['number'] <= count($_SESSION['exams'])){
		//try to write coartem that correspond to the given weight
		//echo $_GET['wght'];
		//now connect database
		$string = @$_SESSION['exams'][($_GET['number'] - 1)]['ExamName'];
		?>
		<script>
			var wait = 100;
			wait *= <?= $_GET['number'] ?>;
			wait += 500;
			console.log("The milliseconds to wait is: " + wait);
			$("#examname<?= $_GET['number'] ?>").val("<?= $string ?>");
			$("#lock_value<?php echo $_GET['number'] ?>").val("1");
			$("#examname<?php echo $_GET['number'] ?>").keyup();
			
			$("#examid<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ExamNumber'] ?>");
			$("#examexistbefore<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ExamRecordID'] ?>");
			$("#exam_date<?= $_GET['number'] ?>").val("<?= @$_SESSION['exams'][($_GET['number'] - 1)]['ResultDate'] ?>");
			$("#examresult<?= $_GET['number'] ?>").val("<?= str_replace("_s","+",$_SESSION['exams'][($_GET['number'] - 1)]['Results']) ?>");
			setTimeout(
				function(e){
					queryMedecine("<?= $_GET['number'] ?>","<?= @$_GET['wght'] ?>");
				}, wait
			);
			<?php
			if($_GET['number'] < count($_SESSION['exams'])){
				?>
				$("#anotherexam").click();
				<?php
				}
			?>
		</script>
		<?php
	}
}
?>