<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	if(@$_POST['save_date']){
		//var_dump($_POST);
		//check if the saved value exist before
		if(!$auto = returnSingleField("SELECT AutoActID FROM auto_exams WHERE ResultID='".PDB($_POST['examId'],true,$con)."' && PrescriptionID='".PDB($_POST['prescId'],true,$con)."' ".(@$_POST['updateid']?" && AutoActID != '".PDB($_POST['updateid'],true,$con)."'":""),"AutoActID",true,$con)){
			//the save the value
			saveData((!@$_POST['updateid']?"INSERT INTO ":"UPDATE ")."auto_exams SET ResultID='".PDB($_POST['examId'],true,$con)."', PrescriptionID='".PDB($_POST['prescId'],true,$con)."', Quantity='".PDB($_POST['quantity'],true,$con)."', Type='".PDB($_POST['pres_type'],true,$con)."', Date='".PDB($_POST['date'],true,$con)."'".(@$_POST['updateid']?" WHERE AutoActID='".PDB($_POST['updateid'],true,$con)."'":""),$con);
		} else{
			$error = "<span class=error-text>The Prescription all ready saved!</span>";
		}
	}
	//chcek if the delete command is valid
	if(@$_GET['delete'] && is_numeric($_GET['delete'])){
		//delete the automatic prescription
		if(saveData("DELETE FROM auto_exams WHERE `".PDB($_GET['tb'],true,$con)."`='".PDB($_GET['delete'],true,$con)."'",$con)){
			$error = "<span class=success>Prescription Deleted</span>";
		}
	}
	
	//check if the activity is to update
	$update = null;
	if(@$_GET['update'] && is_numeric($_GET['update'])){
		$update = formatResultSet($rslt=returnResultSet("SELECT * FROM auto_exams WHERE AutoActID='".PDB($_GET['update'],true,$con)."'",$con),$multirows=false,$con);
	}
$active = "register";
require_once "../lib2/cssmenu/lab_header.html";
?>
  <div id="w" style='height: 86%;'>
    <div id="content" style='height: 100%;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Custom Laboratory Report</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" border=0 cellspacing="0" style='font-size:14px; width:100%; height:100%;'>
    		<tr>
    			<!-- Left container -->
    			<td>
					<?php
					//var_dump($update);
					?>
					<form id='labo_report_frm' action='la-record-loader-custom.php' method='post' style='border:0px solid #000;'>
						<?= @$update['AutoActID']?"<input type=hidden name='updateid' value='{$update['AutoActID']}' />":"" ?>
						<table border=0 style='font-size:16px; width:100%; height:230px;'>
							<tr>
								<td><label><input onclick='' type=checkbox id=checkallregister class="checkall">Register</label></td>
								<td><label><input onclick='' type=checkbox id=checkall class="checkall">Exam</label></td>
								<td>Start Date</td>
								<td>End Date</td>
							</tr>
							<tr>
								<td rowspan=2 style='width:200px;'>
									<div  class=registerField  style='border:0px solid #000; overflow:auto; font-size:13px;' >
									
									</div>
								</td>
								<td rowspan=2 style='width:200px;' >
									<div  class=diagnostics  style='border:0px solid #000; width:180px; height:460px; overflow:auto; font-size:13px;' >
									
									</div>
								</td>
								<td style='width:200px;'>
									<input id="date1" required type="text" name='date1' class='txtfield1' style='150px;' value = '<?= date('Y-m-01',time()) ?>' onclick='ds_sh(this,"date1")' />
								</td>
								<td style='width:200px;'>
									<input id="date2" required type="text" name='date2' class='txtfield1' style='150px;' value = '<?= date('Y-m-d',time()) ?>' onclick='ds_sh(this,"date2")' />
								</td>
								<td align=center style='width:100px;'><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" id="save_date" value="Save" /></td>
								<td align=center>&nbsp;</td>
							</tr>
			    			<tr style='vertical-align:top;'>
			    				<td colspan=4>
									<div  class=labo_report  style='border:0px solid #000; width:940px; font-size:13px; height:420px; overflow:auto;' >
										Welcome
									</div>
								</td>
							</tr>
						</table>
			    	</form>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 40px;'></div>
    	</b>
	</div>
</div>
  <?php
  include_once "../footer.html";
  ?>
</body>
</html>

<script>
	//load the list of diagnostic
	$(".diagnostics").load("./la_data.php?type=master<?= @$update['ResultID']?"&exist=".$update['ResultID']:"" ?>");
	$(".registerField").load("./la_registers.php?type=master<?= @$update['ResultID']?"&exist=".$update['ResultID']:"" ?>");
	//check if the submit button is clicked and browse for report
	$("#save_date").click(function(e){
		e.preventDefault();
		$(".labo_report").html('');
		$(".labo_report").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
		$("#labo_report_frm").ajaxForm({ 
			target: '.labo_report'
		}).submit();
	});

	function checkAll(){
		// return false;
		$(".all_data").prop("checked",":true");
		$("#checkall").prop("checked", ":true");
	}

	function uncheckAll(){
		// return false;
		$(".all_data").removeProp("checked");
		$("#checkall").removeProp("checked");
	}

	function checkOneRegister(registerID){
		$(".all_data").removeProp("checked");
		$(".r" + registerID).prop("checked",":true");
		$("#i" + registerID).prop("checked",":true");
	}
	function uncheckOneRegister(registerID){
		// $("#checkall").removeProp("checked");
		$(".r" + registerID).removeProp("checked");
	}
</script>