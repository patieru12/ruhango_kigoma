<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}

	
	//var_dump($_POST); //die;
	$error = ""; $current = @$_GET['mdid'];
	if(@$_GET['action'] == 'copy' && trim(@$_GET['reference']) != ""){
		//copy the tarif to the current date
		$tarif = returnAllData($sql="SELECT DISTINCT Date FROM md_price WHERE Date = '".(PDB($_GET['reference'],true,$con))."' ORDER BY Date DESC LIMIT 0, 1",$con);
		//var_dump($tarif);
		//select all component of the selected tarif now
		$tarifs = returnAllData($sql="SELECT DISTINCT * FROM md_price WHERE Date = '{$tarif[0]['Date']}'",$con);
		//var_dump($tarifs);
		
		for($i=0;$i<count($tarifs);$i++){
			//save_new tarif if not exist
			if(!$act_price = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$tarifs[$i]['MedecineNameID']}' && Date='".(PDB($_GET['date'],true,$con))."'","MedecinePriceID",$data=true, $con)){
				//save the new price now
				saveData("INSERT INTO md_price SET  MedecineNameID='{$tarifs[$i]['MedecineNameID']}', BuyingPrice='{$tarifs[$i]['BuyingPrice']}', Amount='{$tarifs[$i]['Amount']}', Date='".(PDB($_GET['date'],true,$con))."', Status=1, Emballage='{$tarifs[$i]['Emballage']}'",$con);
			} else{
				//update the record is exists before
				saveData("UPDATE md_price SET BuyingPrice='{$tarifs[$i]['BuyingPrice']}', Amount='{$tarifs[$i]['Amount']}', Emballage='{$tarifs[$i]['Emballage']}' WHERE MedecinePriceID='{$act_price}'",$con);
			}
		}
		
	}
	
	if(@$_GET['emb'] == "update" && is_numeric($_GET['mdid'])){
		$current = $_GET['mdid'];
		saveData("UPDATE md_price SET Emballage='".(PDB($_GET['emballage'],true,$con))."' WHERE MedecinePriceID='".PDB($_GET['mdid'],true,$con)."'",$con);
	}
	
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$category = mysql_real_escape_string(trim($_POST['insurance']));
		$name = mysql_real_escape_string(trim($_POST['product']));
		$price_b  = mysql_real_escape_string(trim($_POST['BuyingPrice']));
		
		$date  = mysql_real_escape_string(trim($_POST['date']));
		$_GET['date'] = $_POST['date'];
		//var_dump($name); die;
		if($tarif_id = returnSingleField($sql="SELECT TarifID FROM sy_tarif WHERE ProductID='{$name}' && InsuranceNameID='{$category}' && Date = '{$date}' ",$field="TarifID",$data=true, $con)){
			saveData($sql="UPDATE sy_tarif SET ProductID='{$name}', InsuranceNameID='{$category}', Amount='{$price_b}', Date='{$date}' WHERE TarifID='{$tarif_id}' ",$con);
		} else{
			//save new data
			if(saveData($sql="INSERT INTO sy_tarif SET ProductID='{$name}', InsuranceNameID='{$category}', Amount='{$price_b}', Date='{$date}' ",$con)){
				$error = "<span class=success>New Price Recorded Now</span>";
			}
		}
	}
	
require_once "../lib2/cssmenu/cs_header.html";
?>
  <style>
	.fld_txt{
		width:100%;
	}
  </style>
	<div id='w' style='height: auto;'>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
		<div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Other <?= $app_level ?> Products Configuration Panel</h1>
	<b>
		<?= $error ?>
		<?php
		$pp = null;
		if(@$_GET['edit'] && is_numeric($_GET['edit'])){
			$pp = formatResultSet($rslt=returnResultSet($sql="SELECT * FROM sy_tarif WHERE TarifID='".PDB($_GET['edit'],true,$con)."'",$con),$multirows=false,$con);
		}
		?>
		</b>
			<form id='medecienId' action='./others.php' method='post' >
				<div class='line-1'>
					<table id="" class=frm style="display: visible; background: none; margin-left: auto; margin-right: auto; ">
						<tr>
							<td>
								<table style="display: visible;border: none;">
									<tr>
										<td>Insurance</td>
										<td>Product</td>
										<td>Amount</td>
										<td>Date:</td>
									</tr>
									<tr>
										<td>
											<?php
											$sql = "SELECT `in_name`.`InsuranceNameID`,`in_name`.`InsuranceName` FROM `in_name` ORDER BY `in_name`.`InsuranceName` ASC";
											$query = mysql_query($sql);
											echo "<select name='insurance' class=txtfield1 style='font-size:16px; width:200px;'>";
											while ($row = mysql_fetch_assoc($query)) {
												?>
												<option <?= @$pp['InsuranceNameID'] == $row['InsuranceNameID']?"selected":"" ?> value='<?= $row['InsuranceNameID'] ?>'><?= $row['InsuranceName'] ?></option>
												<?php
											}
											echo "</select>";
											?>
										</td>
										<td>
											<?php
											$sql = "SELECT `sy_product`.* FROM `sy_product` ORDER BY `sy_product`.`ProductName` ASC";
											$query = mysql_query($sql);
											echo "<select name='product' class=txtfield1 style='font-size:16px; width:200px;'>";
											while ($row = mysql_fetch_assoc($query)) {
												?>
												<option <?= @$pp['ProductID'] == $row['ProductID']?"selected":"" ?> value='<?= $row['ProductID'] ?>'><?= $row['ProductName'] ?></option>
												<?php
											}
											echo "</select>";

											// var_dump($pp);
											?>
										</td>
										<td>
											<input id='unitP' name='BuyingPrice' class='txtfield1' style='width:150px;' required value='<?= @$pp['Amount'] ?>'>
											
										</td>
										<td>
											<input id="date" onclick='ds_sh(this,"date")' type='date' name='date' class='txtfield1'  style='width:100px;' value="<?= @$pp['Date']?$pp['Date']:'2020-01-01' ?>" readonly />
										</td>
							<td colspan='1'>
								<center>
									<input type="submit" id='save_btn' class="flatbtn-blu" name="save" value="Save" style = " font-size:14px;" />
								</center>
							</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
						</tr>
					</table>
				</div>
			</form>
		<table border=0 style='width:100%'>
			<tr>
				<td>
					<span class=update_result></span>
					Tarif Changes<br />
					<style>
					.lh{
						color:blue; text-decoration:none;
					}
					.lh:hover, a#lh_active{
						color:red;
					}
					.img_link:hover{
						border-bottom:2px solid red;
					}
					</style>
					<div style='height:330px; overflow:auto;'>
					<?php
					$tarifs = returnAllData($sql="SELECT DISTINCT Date FROM sy_tarif ORDER BY Date DESC",$con);
					//var_dump($tarifs);
					$printed = "from ";
					if(!@$_GET['date'])
						$_GET['date'] = $tarifs[0]['Date'];
					$t_option = "";
					foreach($tarifs as $tdate){
						echo "<a class='lh' ".(@$_GET['date']==$tdate['Date']?"id='lh_active'":"")." style='' href='./others.php?date={$tdate['Date']}' title='Change tarif for bill made {$printed} {$tdate['Date']}'>".$tdate['Date']."</a><br />";
						$printed = "between ".$tdate['Date']." and ";
						$t_option .= "<option>{$tdate['Date']}</option>";
					}
					?>
					</div>
				</td>
				<td>
				<center>Tarif <?= @$_GET['date']?"du ".$_GET['date']:"Actif"; ?></center>
				<table class='list' id='copy' style='width:97.3%;'>
					<thead>
						
					</thead>
				</table>
				<div id="filter_check" style='max-height: 400px; overflow: auto; padding-top:0px;'>
					  <style type="text/css">
					  	#th th{
					  		padding: 8px;
					  		background: #999;
							 font-size:12px;
					  	}
					  	#fr tr td{
					  		padding: 5px; font-size:12px;
					  	}
					  </style>
					  <input type=hidden value=0 id=edit_mode />
					<?php
						$sql = "SELECT `sy_product`.`ProductName`, sy_product.ProductID FROM `sy_product` ORDER BY `sy_product`.`ProductName` ASC";
						$query = mysql_query($sql);
					$insurance_all = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
					// var_dump($insurance_all);
					?>
					<table style="background: none; margin-left: auto; margin-right: auto; padding: 5px;text-align: center;" class='list' id=tbl_ref>
						<tr id='th'>
							<th style='width:10%;'>#</th>
							<th style='width:253px;'>Product</th>
							<?php
							for($i=0; $i<count($insurance_all); $i++){
								echo "<th>{$insurance_all[$i]['InsuranceName']}</th>";
							}
							?>
						</tr><tbody id='fr'>
							<?php
							$printed = "";
								$row_ = 1;
								while ($row = mysql_fetch_assoc($query)) {
									
									$col = 1;
						?>
									<tr>
										<td style='width:10%;' align=left><?= $row_ ?></td>
										<td style='width:253px;' align=left><?= $row['ProductName'] ?></td>
										<?php
										/* Here Find the price for the active insurance */
										for($i=0; $i<count($insurance_all); $i++){
											$sql = "SELECT `sy_product`.`ProductName`,`sy_tarif`.*, in_name.InsuranceName FROM `in_name`,`sy_product`,`sy_tarif` WHERE `sy_tarif`.`InsuranceNameID` = `in_name`.`InsuranceNameID` && `sy_tarif`.`ProductID` = `sy_product`.`ProductID` && `sy_tarif`.`Date` = '{$_GET['date']}' && sy_tarif.InsuranceNameID='{$insurance_all[$i]['InsuranceNameID']}' && sy_tarif.ProductID = {$row['ProductID']} ORDER BY `sy_product`.`ProductName` ASC";
											$amount = returnSingleField($sql, "Amount", true, $con);
											if($amount){
												echo "<td>".$amount."</td>";
											} else{
												echo "<td>&nbsp;</td>";
											}
										}
										?>
									</tr>
						<?php			
									$row_++;
								}
							?>
						</tbody>
					</table>
				</div>
				</td>
				</tr>
			</table>
		</div>
	</div>
  <?php
  include_once "../footer.html";
  ?>
</body>
</html>