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
		if(!$auto = returnSingleField("SELECT AutoActID FROM auto_diagnostic WHERE DiagnosticID='".PDB($_POST['diagnosticId'],true,$con)."' && PrescriptionID='".PDB($_POST['prescId'],true,$con)."' ".(@$_POST['updateid']?" && AutoActID != '".PDB($_POST['updateid'],true,$con)."'":""),"AutoActID",true,$con)){
			//the save the value
			saveData((!@$_POST['updateid']?"INSERT INTO ":"UPDATE ")."auto_diagnostic SET DiagnosticID='".PDB($_POST['diagnosticId'],true,$con)."', PrescriptionID='".PDB($_POST['prescId'],true,$con)."', Quantity='".PDB($_POST['quantity'],true,$con)."', Type='".PDB($_POST['pres_type'],true,$con)."', Date='".PDB($_POST['date'],true,$con)."'".(@$_POST['updateid']?" WHERE AutoActID='".PDB($_POST['updateid'],true,$con)."'":""),$con);
		} else{
			$error = "<span class=error-text>The Prescription all ready saved!</span>";
		}
	}
	//chcek if the delete command is valid
	if(@$_GET['delete'] && is_numeric($_GET['delete'])){
		//delete the automatic prescription
		if(saveData("DELETE FROM auto_diagnostic WHERE `".PDB($_GET['tb'],true,$con)."`='".PDB($_GET['delete'],true,$con)."'",$con)){
			$error = "<span class=success>Prescription Deleted</span>";
		}
	}
	
	//check if the activity is to update
	$update = null;
	if(@$_GET['update'] && is_numeric($_GET['update'])){
		$update = formatResultSet($rslt=returnResultSet("SELECT * FROM auto_diagnostic WHERE AutoActID='".PDB($_GET['update'],true,$con)."'",$con),$multirows=false,$con);
	}
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: 86%;'>
    <div id="content" style='height: 100%;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Configure automatic prescription according to Diagnostic</h1>
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
					<form action='auto-diag.php' method='post' style='border:0px solid #000;'>
						<?= @$update['AutoActID']?"<input type=hidden name='updateid' value='{$update['AutoActID']}' />":"" ?>
						<table border=0 style='font-size:16px; width:100%; height:230px;'>
							<tr>
								<td>Diagnostic</td>
								<td>Prescription Type</td>
								<td>Quantity</td>
								<td>Date</td>
							</tr>
							<tr>
								<td rowspan=2 >
									<div  class=diagnostics  style='border:0px solid #000; width:180px; height:460px; overflow:auto; font-size:13px;' >
									
									</div>
								</td>
								<td>
									<select class=txtfield1 name=pres_type id=pres_type style='width:98%'>
										<option value=''>--Type--</option>
										<option value='la' <?= @$update['Type'] == 'la'?"selected":"" ?>>Exams</option>
										<option value='md' <?= @$update['Type'] == 'md'?"selected":"" ?>>Medicines</option>
										<option value='ac' <?= @$update['Type'] == 'ac'?"selected":"" ?>>Acts</option>
										<option value='cn' <?= @$update['Type'] == 'cn'?"selected":"" ?>>Consumables</option>
									</select>
								</td>
								<td>
									<input type=text class=txtfield1 name=quantity value='<?= @$update['Quantity'] ?>' style='width:98%;' />
								</td>
								<td>
									<input id="date" required type="text" name='date' class='txtfield1' style='width:98%;' value = '<?= @$update['Date'] ?>' onclick='ds_sh(this,"date")' />
								</td>
								<td align=center><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" value="Save" /></td>
							</tr>
			    			<tr style='vertical-align:top;'>
			    				<td>
									<div  class=auto-data  style='border:0px solid #000; width:190px; font-size:13px; height:420px; overflow:auto;' >
										&nbsp;
									</div>
								</td>
								<td colspan=3>
								<?= $error; ?>
								<div style='background-color:#4f4a41; color:#eee; font-weight:bold; padding:2px; border-top-left-radius:5px; border-top-right-radius:5px; margin-top:10px; text-align:center'>Existing Auto Diagnostic Configuration</div>
								<div class=auto_presc_data style='border:1px solid #4f4a41; padding-bottom:5px; border-bottom-left-radius:5px; border-bottom-right-radius:5px; height:390px; overflow:auto; padding:2px;'>
									
									<?php
									//select all saved data 
									$auto_diag = returnAllData($sql="SELECT DISTINCT DiagnosticID, Type, PrescriptionID FROM auto_diagnostic ORDER BY Date DESC ",$con);
									if($auto_diag){
										?>
										<table style="font-size:13px; width:100%" border=1>
											<tr><th>#</th><th>Diagnostic</th><th>Type</th><th>Prescription</th><th>Quantity</th><th>Date</th><th colspan=2>Action</th></tr>
											<?php
											//var_dump($auto_diag);
											for($i=0;$i<count($auto_diag);$i++){
												echo "<tr>";
													echo "<td>".($i + 1)."</td>";
													$data_ = returnSingleField("SELECT DiagnosticName FROM co_diagnostic WHERE DiagnosticID='{$auto_diag[$i]['DiagnosticID']}'","DiagnosticName",true,$con);
													$ommitted = false;
													$data = ommitStringPart($str = $data_,30,$ommitted);
													echo "<td title='{$data_}'>".$data."</td>";
													echo "<td>".$types[$auto_diag[$i]['Type']]."</td>";
													
													$data_ = returnSingleField("SELECT {$types_data[$auto_diag[$i]['Type']]} FROM `".($auto_diag[$i]['Type'] == "la"?"la_result":"{$auto_diag[$i]['Type']}_name")."` WHERE `{$types_data_c[$auto_diag[$i]['Type']]}`='{$auto_diag[$i]['PrescriptionID']}'","{$types_data[$auto_diag[$i]['Type']]}",true,$con);
													$ommitted = false;
													$data = ommitStringPart($str = $data_,30,$ommitted);
													echo "<td title='{$data_}'>".$data."</td>";
													
													$qty_ = returnSingleField("SELECT Quantity FROM `auto_diagnostic` WHERE `DiagnosticID`='{$auto_diag[$i]['DiagnosticID']}' && `PrescriptionID`='{$auto_diag[$i]['PrescriptionID']}' ORDER BY Date DESC LIMIT 0, 1","Quantity",true,$con);
													echo "<td>".$qty_."</td>";
													
													$date_ = returnSingleField("SELECT Date as Quantity FROM `auto_diagnostic` WHERE `DiagnosticID`='{$auto_diag[$i]['DiagnosticID']}' && `PrescriptionID`='{$auto_diag[$i]['PrescriptionID']}' ORDER BY Date DESC LIMIT 0, 1","Quantity",true,$con);
													echo "<td>".$date_."</td>";
													$id = returnSingleField("SELECT AutoActID FROM `auto_diagnostic` WHERE `DiagnosticID`='{$auto_diag[$i]['DiagnosticID']}' && `PrescriptionID`='{$auto_diag[$i]['PrescriptionID']}' ORDER BY Date DESC LIMIT 0, 1","AutoActID",true,$con);
													?>
													<td><a href='./auto-diag.php?update=<?= $id ?>' style='color:blue; text-decoration:none;' title="Update <?= $data_ ?>">Update</a></td>
													<td><a href='./auto-diag.php?delete=<?= $id ?>' style='color:blue; text-decoration:none;' title="Delete <?= $data_ ?>" onclick='return confirm("Delete <?= $data ?> \nFrom Automatic Prescription List ")'>Delete</a></td>
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
	$(".diagnostics").load("./auto/diag.php<?= @$update['DiagnosticID']?"?exist=".$update['DiagnosticID']:"" ?>");
	$(".auto_presc_data").load("./auto/preview.php?type=dg");
	
	$("#pres_type").change(function(e){
		//now load the corresponding selected category
		if($("#pres_type").val() != ""){
			$(".auto-data").load("./auto/" + $("#pres_type").val() + ".php<?= @$update['PrescriptionID']?"?exist=".$update['PrescriptionID']:"" ?>" );
		} else{
			$(".auto-data").html("");
		}
	});
	<?php
	if(@$update['PrescriptionID']){
		?>
		setTimeout(function(e){
			$(".auto-data").load("./auto/" + $("#pres_type").val() + ".php<?= @$update['PrescriptionID']?"?exist=".$update['PrescriptionID']:"" ?>" );
		},200);
		<?php
	}
	?>
</script>