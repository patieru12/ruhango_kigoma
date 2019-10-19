<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}

	
	//var_dump($_POST); //die;
	$error = "";
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['act_name']));
		$cons = mysql_real_escape_string(trim($_POST['medecienId']));
		$qty  = mysql_real_escape_string(trim($_POST['quantity']));
		//$sell_price  = $price_b + round($price_b*0.2,1);
		//$date  = mysql_real_escape_string(trim($_POST['date']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT ActConsumableID FROM ac_consumable WHERE MedecineNameID='{$cons}' && Quantity='{$qty}' && ActNameID='{$name}' && Status=1",$field="ActConsumableID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Consumable is Still Active</span>";
		} else{
			//update the existing status
			saveData("UPDATE ac_consumable SET Status=0 WHERE MedecineNameID='{$cons}' && ActNameID='{$name}' && Status=1",$con);
			//save new data
			if(saveData($sql="INSERT INTO ac_consumable SET  MedecineNameID='{$cons}', ActNameID='{$name}', Quantity='{$qty}', Date=NOW(), Status=1",$con)){
				$error = "<span class=succees>New Consumable Recorded Now</span>";
			}
		}
	}
	
require_once "../lib2/cssmenu/cs_header.html";
?>
	<div id='w' style='height: auto;'>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
		<div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Act and Consumables Configuration Panel</h1>
	<b>
		<?= $error ?>
		</b>
			<form id='medecienId' action='' method='post' >
				<div class='line-1'>
					<table id="" class=frm style="display: visible; background: none; margin-left: auto; margin-right: auto; ">
						<tr>
							<td>
								<table style="display: visible;border: none;">
									<tr>
										<td>Name</td>
										<td>Consumable</td>
										<td for='date'>Quantity:</td>
									</tr>
									<tr>
										<td>
											<?php
												$g = 0;
												$sql = "SELECT ac_name.* FROM ac_name ORDER BY Name ASC";
												$query = mysql_query($sql)or die(mysql_error());
												$a_list = array();
												$a_list_r = array();
												echo "<select name=act_name style='font-size:16px;' class=txtfield1>";
												while ($list = mysql_fetch_assoc($query)) {
													$a_list_r[] = array("name"=>$list['Name'],"id"=>$list['ActNameID']);
											?>
											<option value='<?= $list['ActNameID'] ?>'><?= $list['Name'] ?></option>
											<?php
												}
											?>
											</select>
										</td>
										<td>
											<?php
											$sql = "SELECT `md_name`.`MedecineNameID`,`md_name`.`MedecineName`,`md_name`.`MedecineCategorID`,`md_name`.`Emballage`,`md_category`.`MedecineCategoryName` FROM `md_name`,`md_category` WHERE `md_category`.`MedecineCategoryID` = `md_name`.`MedecineCategorID` AND `md_name`.`Status` = '1' && MedecineCategorID = 2 ORDER BY `md_category`.`MedecineCategoryName` ASC, `md_name`.`MedecineName` ASC";
											$query = mysql_query($sql);
											echo "<select name='medecienId' class=txtfield1 style='font-size:16px;'>";
											while ($row = mysql_fetch_assoc($query)) {
												?>
												<option value='<?= $row['MedecineNameID'] ?>'><?= $row['MedecineName'] ?></option>
												<?php
											}
											echo "</select>";
											?>
										</td>
										<td>
											<input id="date" type='text' name='quantity' class='txtfield1'  style=' font-size:16px;'/>
										</td>
							<td colspan='1'>
								<center>
									<input type="submit" id='save_btn' class="flatbtn-blu" name="save" value="Save" style = " font-size:16px;" />
								</center>
							</td>
									</tr>
								</table>
							</td>
						</tr>
			</form>			
						<tr>
						</tr>
					</table>
				</div>
				<div style='max-height: 400px; width:610px; overflow: auto; padding-top: 10px;'>
					  <style type="text/css">
					  	#th th{
					  		padding: 8px;
					  		background: #999;
					  	}
					  	#fr tr td{
					  		padding: 5px;
					  	}
					  </style>
					<table style="background: none; margin-left: auto; margin-right: auto;width: 560px;padding: 5px;text-align: center;" class='list'>
						<thead>
							<tr id='th'>
								<th>Consumable</th>
								<th>Quantity</th>
							</tr>
						</thead>
						<tbody id='fr'>
							<?php
							$printed = "";
								foreach($a_list_r as $row){
									echo "<tr id=th><th align=left colspan=5>{$row['name']}</th></tr>";
									//$printed = $row['MedecineCategoryName'];
									//var_dump($row);
									$sql = "SELECT `md_name`.`MedecineNameID`,`md_name`.`MedecineName`, `ac_consumable`.`Quantity` FROM `md_name`, `ac_consumable` WHERE `md_name`.`MedecineNameID` = ac_consumable.MedecineNameID AND `ac_consumable`.`Status` = '1' && ac_consumable.ActNameID = '{$row['id']}' ORDER BY Medecinename ASC, `Date` DESC";
									$r = mysql_query($sql)or die(mysql_error()." ".$sql);
									//echo $sql;
									while ($t = mysql_fetch_assoc($r)) {
										
							?>
										<tr>
											<td align=left><?= $t['MedecineName'] ?></td>
											<td><?= $t['Quantity'] ?></td>
										</tr>
							<?php			
									}
								}
							?>
						</tbody>
					</table>
				</div>
		</div>
	</div>
<?php
  include_once "../footer.html";
  ?> 
<script type="text/javascript">
	$(document).ready(function(){
		$('#md_name').keyup(function(){
			var value = $(this).val();
			$.post('./ajaxfile.php',
				{
					name : value
				},
				function(data){
					if (data != 0) {
						$('#other-side').html(data);
					}
				}
			);
			/*var output = $("#unitP").attr('value',function(){
				return value;
			});*/
		});

		

		$("#unitP").keyup(function(){
			var unit = $.trim($("#unitP").val());
			var psg = (unit * 20) / 100;
			psg += parseFloat(unit);
			$("#price").attr("value",function(){
				if(unit != "" || unit.lenght > 0 ) return psg;
				else return "";
			});
		});
/* 
		$("#save_btn").click(function(){
			var cat = $("#md_cat").val();
			var name = $.trim($("#md_name").val());
			var unit = $.trim($("#unitP").val());
			if (name == "" ) alert('PLease select Name.');
			else if (cat == "" ) alert('PLease select Category.');
			else if (unit == "" ) alert('PLease select Unit Price.');
			else{
				$("#medecienId").submit();
			};
		}); */

		$("#update_btn").click(function(){
			var cat = $("#md_cat").val();
			var name = $.trim($("#md_name").val());
			var unit = $.trim($("#unitP").val());
			if (name == "" ) alert('PLease select Name.');
			else if (cat == "" ) alert('PLease select Category.');
			else if (unit == "" ) alert('PLease select Unit Price.');
			else{
				$("#medecienId").submit();
			};
		});
		

	});
</script>

</body>
</html>