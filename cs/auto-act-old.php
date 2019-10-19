<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	if(@$_POST['save_date___']){
		//var_dump($_POST);
		//check if the saved value exist before
		if(!$auto = returnSingleField("SELECT AutoActID FROM auto_diagnostic WHERE DiagnosticID='".PDB($_POST['diagnosticId'],true,$con)."' && PrescriptionID='".PDB($_POST['prescId'],true,$con)."'","AutoActID",true,$con)){
			//the save the value
			saveData("INSERT INTO auto_diagnostic SET DiagnosticID='".PDB($_POST['diagnosticId'],true,$con)."', PrescriptionID='".PDB($_POST['prescId'],true,$con)."', Quantity='".PDB($_POST['quantity'],true,$con)."', Type='".PDB($_POST['pres_type'],true,$con)."', Date='".PDB($_POST['date'],true,$con)."'",$con);
		} else{
			$error = "<span class=error-text>The Prescription all ready saved!</span>";
		}
	}
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: 86%;'>
    <div id="content" style='height: 100%;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Configure automatic prescription according to Act</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" border=0 cellspacing="0" style='font-size:14px; width:100%; height:100%;'>
    		<tr>
    			<!-- Left container -->
    			<td>
					
					<form action='' method='post' style='border:0px solid #000;'>
						<table border=1 style='font-size:16px; width:100%; height:230px;'>
							<tr>
								<td>Acts</td>
								<td>Prescription Type</td>
								<td>Quantity</td>
								<td>Date</td>
							</tr>
							<tr>
								<td rowspan=2 >
									<div  class=diagnostics  style='border:0px solid #000; height:460px; overflow:auto;' >
									
									</div>
								</td>
								<td>
									<select class=txtfield1 name=pres_type id=pres_type style='width:98%'>
										<option value=''>--Type--</option>
										<option value='md'>Medicines</option>
										<!--<option value='ac'>Acts</option>-->
										<option value='cn'>Consumables</option>
									</select>
								</td>
								<td>
									<input type=text class=txtfield1 name=quantity style='width:98%;' />
								</td>
								<td>
									<input id="date" required type="text" name='date' class='txtfield1' style='width:98%;' value = '' onclick='ds_sh(this,"date")' />
								</td>
								<td align=center><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" value="Save" /></td>
							</tr>
			    			<tr style='vertical-align:top;'>
			    				<td>
									<div  class=auto-data  style='border:0px solid #000; height:420px; overflow:auto;' >
										&nbsp;
									</div>
								</td>
								<td colspan=3>
								Existing Auto Diagnostic<br />
								<?= $error; ?>
								<?php
								//select all saved data 
								$auto_diag = returnAllData($sql="SELECT DISTINCT DiagnosticID, Type, PrescriptionID FROM auto_diagnostic ORDER BY Date DESC ",$con);
								if($auto_diag){
									?>
									<table style="font-size:13px; width:100%" border=1>
										<tr><th>#</th><th>Act</th><th>Type</th><th>Prescription</th><th>Quantity</th><th>Date</th></tr>
										<?php
										/* for($i=0;$i<count($auto_diag);$i++){
											echo "<tr>";
												echo "<td>".($i + 1)."</td>";
												$data_ = returnSingleField("SELECT DiagnosticName FROM co_diagnostic WHERE DiagnosticID='{$auto_diag[$i]['DiagnosticID']}'","DiagnosticName",true,$con);
												$ommitted = false;
												$data = ommitStringPart($str = $data_,30,$ommitted);
												echo "<td title='{$data_}'>".$data."</td>";
												echo "<td>".$types[$auto_diag[$i]['Type']]."</td>";
												
												$data_ = returnSingleField("SELECT {$types_data[$auto_diag[$i]['Type']]} FROM `{$auto_diag[$i]['Type']}_name` WHERE `{$types_data_c[$auto_diag[$i]['Type']]}`='{$auto_diag[$i]['PrescriptionID']}'","{$types_data[$auto_diag[$i]['Type']]}",true,$con);
												$ommitted = false;
												$data = ommitStringPart($str = $data_,30,$ommitted);
												echo "<td title='{$data_}'>".$data."</td>";
												
												$data_ = returnSingleField("SELECT Quantity FROM `auto_diagnostic` WHERE `DiagnosticID`='{$auto_diag[$i]['DiagnosticID']}' && `PrescriptionID`='{$auto_diag[$i]['PrescriptionID']}' ORDER BY Date DESC LIMIT 0, 1","Quantity",true,$con);
												echo "<td>".$data_."</td>";
												
												$data_ = returnSingleField("SELECT Date as Quantity FROM `auto_diagnostic` WHERE `DiagnosticID`='{$auto_diag[$i]['DiagnosticID']}' && `PrescriptionID`='{$auto_diag[$i]['PrescriptionID']}' ORDER BY Date DESC LIMIT 0, 1","Quantity",true,$con);
												echo "<td>".$data_."</td>";
												
												
											echo "</tr>";
										} */
										?>
									</table>
									<?php
								}
								?>
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
	$(".diagnostics").load("./auto/ac.php");
	
	$("#pres_type").change(function(e){
		//now load the corresponding selected category
		if($("#pres_type").val() != ""){
			$(".auto-data").load("./auto/" + $("#pres_type").val() + ".php" );
		} else{
			$(".auto-data").html("");
		}
	});
</script>