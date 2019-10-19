<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	if(@$_POST['save_date']){
		//var_dump($_POST);  
		//check if the saved value exist before
		if(!$auto = returnSingleField("SELECT AutoActID FROM auto_medicines_condition WHERE MedecineNameID='".PDB($_POST['medicineId'],true,$con)."'".(@$_POST['updateid']?" && AutoActID != '".PDB($_POST['updateid'],true,$con)."'":""),"AutoActID",true,$con)){
			//the save the value
			
			$values = ($_POST['value2']?$_POST['value2'].";":"").$_POST['value1'];
			//echo $values;
			//die;
			saveData((!@$_POST['updateid']?"INSERT INTO ":"UPDATE ")."auto_medicines_condition SET MedecineNameID='".PDB($_POST['medicineId'],true,$con)."', Type='".PDB($_POST['cnd_type'],true,$con)."', 	ConditionValue='".$values."', ConditionSign='".PDB($_POST['cnd_sign'],true,$con)."', Date='".PDB($_POST['date'],true,$con)."'".(@$_POST['updateid']?" WHERE AutoActID='".PDB($_POST['updateid'],true,$con)."'":""),$con);
			$error = "<span class=success>The Prescription Rules Well saved!</span>";
		} else{
			$error = "<span class=error-text>The Prescription all ready saved!</span>";
		}
	}
	//chcek if the delete command is valid
	if(@$_GET['delete'] && is_numeric($_GET['delete'])){
		//delete the automatic prescription
		if(saveData("DELETE FROM auto_medicines_condition WHERE AutoActID='".PDB($_GET['delete'],true,$con)."'",$con)){
			$error = "<span class=success>Prescription Rule Deleted</span>";
		}
	}
	
	//check if the activity is to update
	$update = null;
	if(@$_GET['update'] && is_numeric($_GET['update'])){
		$update = formatResultSet($rslt=returnResultSet("SELECT * FROM auto_medicines_condition WHERE AutoActID='".PDB($_GET['update'],true,$con)."'",$con),$multirows=false,$con);
	}
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: 86%;'>
    <div id="content" style='height: 100%;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Automatic Medicines Prescription Rules</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" border=0 cellspacing="0" style='font-size:14px; width:100%; height:100%;'>
    		<tr>
    			<!-- Left container -->
    			<td>
					<style>
						.txtfield1{
							width:98%;
						}
					</style>
					<form action='rules.php' method='post' style='border:0px solid #000;'>
						<?= @$update['AutoActID']?"<input type=hidden name='updateid' value='{$update['AutoActID']}' />":"" ?>
						<table border=0 style='font-size:16px; width:100%; height:230px;'>
							<tr>
								<td>Medicines</td>
								<td>Type</td>
								<td>Sign</td>
								<td>Value</td>
								<td>Date</td>
							</tr>
							<tr>
								<td rowspan=2 >
									<div  class=diagnostics  style='border:0px solid #000; width:180px; height:460px; overflow:auto; font-size:13px;' >
									
									</div>
								</td>
								<td>
									<select class=txtfield1 name=cnd_type id=cnd_type style='width:98%'>
										<option value=''>--Type--</option>
										<option value='age' <?= @$update['Type'] == 'age'?"selected":"" ?>>Age</option>
										<option value='weight' <?= @$update['Type'] == 'weight'?"selected":"" ?>>Weight</option>
									</select>
								</td>
								<td>
									<select class=txtfield1 name=cnd_sign id=cnd_sign style='width:98%'>
										<option value=''>--Sign--</option>
										<option value='>' <?= @$update['ConditionSign'] == '>'?"selected":"" ?>>Greater Than</option>
										<option value='>=' <?= @$update['ConditionSign'] == '>='?"selected":"" ?>>Greater Than Or Equal</option>
										<option value='<' <?= @$update['ConditionSign'] == '<'?"selected":"" ?>>Less Than</option>
										<option value='<=' <?= @$update['ConditionSign'] == '<='?"selected":"" ?>>Less Than Or Equal</option>
										<option value='=' <?= @$update['ConditionSign'] == '='?"selected":"" ?>>Equal To</option>
										<option value='!=' <?= @$update['ConditionSign'] == '!='?"selected":"" ?>>Not Equal To</option>
										<option value='>=;<=' <?= @$update['ConditionSign'] == '>=;<='?"selected":"" ?>>Between</option>
									</select>
								</td>
								<td>
									<?php
									$values_ = explode(";",$update['ConditionValue']);
									?>
									<span id=value2containner><input type=text class=txtfield1 name=value2 id=value2 value='<?= @$values_[0] ?>' /></span>
									<input type=text class=txtfield1 name=value1 id=value1 value='<?= @$values_[1] ?>' />
								</td>
								<td>
									<input id="date" required type="text" name='date' class='txtfield1' style='width:98%;' value = '<?= @$update['Date'] ?>' onclick='ds_sh(this,"date")' />
								</td>
								<td align=center><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" value="Save" /></td>
							</tr>
			    			<tr style='vertical-align:top;'>
			    				<td colspan=5>
								
								<div style='background-color:#4f4a41; color:#eee; font-weight:bold; padding:2px; border-top-left-radius:5px; border-top-right-radius:5px; margin-top:10px; text-align:center'>Existing Auto Medicines Prescription Rules Configuration</div>
								<div style='border:1px solid #4f4a41; padding-bottom:5px; border-bottom-left-radius:5px; border-bottom-right-radius:5px; height:390px; padding:2px;'>
									<?= $error; ?>
									<?php
									//select all saved data 
									$auto_diag = returnAllData($sql="SELECT DISTINCT auto_medicines_condition.*, md_name.MedecineName FROM auto_medicines_condition, md_name WHERE md_name.MedecineNameID = auto_medicines_condition.MedecineNameID ORDER BY Date DESC ",$con);
									//var_dump($auto_diag);
									if($auto_diag){
										?>
										<table style="font-size:13px; width:100%" border=1>
											<tr><th>#</th><th>Medicines</th><th>Type</th><th>Condition</th><th>Value</th><th>Date</th><th colspan=2>Action</th></tr>
											<?php
											//var_dump($auto_diag);
											for($i=0;$i<count($auto_diag);$i++){
												echo "<tr>";
													echo "<td>".($i + 1)."</td>";
													$data_ = $auto_diag[$i]['MedecineName'];//returnSingleField("SELECT MedecineName FROM md_name WHERE MedecineNameID='{$auto_diag[$i]['MedecineNameID']}'","MedecineName",true,$con);
													$ommitted = false;
													$data = ommitStringPart($str = $auto_diag[$i]['MedecineName'],30,$ommitted);
													echo "<td title='{$data_}'>".$data."</td>";
													echo "<td>".$auto_diag[$i]['Type']."</td>";
													echo "<td title='{$data_}'>".$signs[$auto_diag[$i]['ConditionSign']]."</td>";
													echo "<td>".str_replace(";"," and ",$auto_diag[$i]['ConditionValue'])."</td>";
													echo "<td>".$auto_diag[$i]['Date']."</td>";
													$id = $auto_diag[$i]['AutoActID'];//returnSingleField("SELECT AutoActID FROM `auto_medicines` WHERE `MedecineNameID`='{$auto_diag[$i]['MedecineNameID']}' && `PrescriptionID`='{$auto_diag[$i]['PrescriptionID']}' ORDER BY Date DESC LIMIT 0, 1","AutoActID",true,$con);
													?>
													<td><a href='./rules.php?update=<?= $id ?>' style='color:blue; text-decoration:none;' title="Update <?= $data_ ?>">Update</a></td>
													<td><a href='./rules.php?delete=<?= $id ?>' style='color:blue; text-decoration:none;' title="Delete <?= $data_ ?>" onclick='return confirm("Delete <?= $data ?> \nFrom Automatic Prescription List ")'>Delete</a></td>
													<?php
													
													
												echo "</tr>";
											}
											?>
										</table>
										<?php
									}
									?>
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
	$(".diagnostics").load("./auto/md.php?type=master<?= @$update['MedecineNameID']?"&exist=".$update['MedecineNameID']:"" ?>");
	
	$("#pres_type").change(function(e){
		//now load the corresponding selected category
		if($("#pres_type").val() != ""){
			$(".auto-data").load("./auto/" + $("#pres_type").val() + ".php<?= @$update['PrescriptionID']?"?exist=".$update['PrescriptionID']:"" ?>" );
		} else{
			$(".auto-data").html("");
		}
	});
	$("#cnd_sign").change(function(e){
		//now load the corresponding selected category
		if($("#cnd_sign").val() == ">=;<="){
			//alert("2 field Required!");
			$("#value1").css("width","80px");
			$("#value2").css("width","80px");
			$("#value2containner").show(500);
			$("#value2").focus();
			
			//$(".auto-data").load("./auto/" + $("#pres_type").val() + ".php<?= @$update['PrescriptionID']?"?exist=".$update['PrescriptionID']:"" ?>" );
		} else {
			//alert("2 field Required!");
			$("#value1").css("width","160px");
			$("#value2").css("width","80px");
			$("#value2containner").hide(500);
			$("#value1").focus();
			$("#value2").val("");
			
			//$(".auto-data").load("./auto/" + $("#pres_type").val() + ".php<?= @$update['PrescriptionID']?"?exist=".$update['PrescriptionID']:"" ?>" );
		}
	});
	$("#value2containner").hide();
	<?php
	if(@$values_[0]){
		?>
		
		$("#value1").css("width","80px");
		$("#value2").css("width","80px");
		$("#value2containner").show(500);
		$("#value2").focus();
		<?php
	}
	?>
</script>