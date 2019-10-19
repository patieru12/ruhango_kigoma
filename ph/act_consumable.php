<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}

	
	//var_dump($_POST); //die;
	$error = "";
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['medecienId']));
		$price_b  = mysql_real_escape_string(trim($_POST['BuyingPrice']));
		$sell_price  = $price_b + round($price_b*0.2,1);
		$date  = mysql_real_escape_string(trim($_POST['date']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$name}' && BuyingPrice='{$price_b}' && Status=1",$field="MedecinePriceID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Price is Still Active</span>";
		} else{
			//update the existing status
			saveData("UPDATE md_price SET Status=0 WHERE MedecineNameID='{$name}' && Status=1",$con);
			//save new data
			if(saveData($sql="INSERT INTO md_price SET  MedecineNameID='{$name}', BuyingPrice='{$price_b}', Amount='{$sell_price}', Date='{$date}', Status=1",$con)){
				$error = "<span class=succees>New Price Recorded Now</span>";
			}
		}
	}
	
?>
<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <title>Care Mini Version 1</title>
  <link rel="shortcut icon" href="">
  <link rel="icon" href="">
  <link rel="stylesheet" type="text/css" media="all" href="../style.css">
  <!--<link rel="stylesheet" type="text/css" media="all" href="../style_menu.css">-->
  <link rel="stylesheet" type="text/css" media="all" href="../calendarcss.css">
  <link rel="stylesheet" type="text/css" media="all" href="../apple_css.css"><!--
  <script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>-->
  <script type="text/javascript" charset="utf-8" src="../js/jquery.full.js"></script>
  <script type="text/javascript" charset="utf-8" src="../js/calendarfile.js"></script>
  <!-- jQuery plugin leanModal under MIT License http://leanmodal.finelysliced.com.au/ -->
  <style type="text/css">
  	.th th{
  		padding: 5px;
  	}
  	tbody td{
  		padding: 5px;
  	}
  </style>
  <script type="text/javascript">
  	$(function(){
  		$("#update_btn").hide();
  	});
  </script>
</head>
<body>
	<div id="topbar">
	  <a href="./index.php">Acts</a> | 
	  <a href="./medecines.php">Medecines Price</a> | 
	  <a href="./insurance.php">Insurance</a> | 
	  <!--<a href='./create.php'>Create New Document</a> |-->
	  <a href='report.php'>Reports</a> |
	  <a href='../logout.php'>Logout</a>
	</div>
	<div id='w' style='height: auto;'>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
		<div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Medicines Configuration Panel</h1>
	<b>
		<?= $error ?>
		</b>
			<form id='medecienId' action='' method='post' >
				<div class='line-1'>
					<table id="" style="display: visible; background: none; margin-left: auto; margin-right: auto; ">
						<tr>
							<td>
								<table style="display: visible;border: none;">
									<tr>
										<td>Name</td>
										<td>Buying Price</td>
										<td for='date'>Date:</td>
									</tr>
									<tr>
										<td>
											<?php
											$sql = "SELECT `md_name`.`MedecineNameID`,`md_name`.`MedecineName`,`md_name`.`MedecineCategorID`,`md_name`.`Emballage`,`md_category`.`MedecineCategoryName` FROM `md_name`,`md_category` WHERE `md_category`.`MedecineCategoryID` = `md_name`.`MedecineCategorID` AND `md_name`.`Status` = '1' ORDER BY `md_category`.`MedecineCategoryName` ASC, `md_name`.`MedecineName` ASC";
											$query = mysql_query($sql);
											echo "<select name='medecienId' class=txtfield1 style='font-size:16px; width:300px;'>";
											while ($row = mysql_fetch_assoc($query)) {
												?>
												<option value='<?= $row['MedecineNameID'] ?>'><?= $row['MedecineName'] ?></option>
												<?php
											}
											echo "</select>";
											?>
										</td>
										<td>
											<input id='unitP' name='BuyingPrice' class='txtfield1' style='width:300px;' required >
											
										</td>
										<td>
											<input id="date" onclick='ds_sh(this,"date")' type='date' name='date' class='txtfield1'  style='width:300px;' value="<?= date('Y-m-d',time()) ?>" readonly />
										</td>
							<td colspan='1'>
								<center>
									<input type="submit" id='save_btn' class="flatbtn-blu" name="save" value="Save" style = " font-size:16px;" />
								</center>
							</td>
									</tr>
								</table>
							</td>
							<td>
								<table id='other-side'>
									<tr>
									</tr>
									<tr>
									</tr>
								</table>
							</td>
						</tr>
			</form>			
						<tr>
						</tr>
					</table>
				</div>
				<div style='max-height: 400px;overflow: auto; padding-top: 10px;'>
					  <style type="text/css">
					  	#th th{
					  		padding: 8px;
					  		background: #999;
					  	}
					  	#fr tr td{
					  		padding: 5px;
					  	}
					  </style>
					<?php
						$sql = "SELECT `md_name`.`MedecineNameID`,`md_name`.`MedecineName`,`md_name`.`MedecineCategorID`,`md_name`.`Emballage`,`md_category`.`MedecineCategoryName`,`md_price`.* FROM `md_name`,`md_category`,`md_price` WHERE `md_category`.`MedecineCategoryID` = `md_name`.`MedecineCategorID` AND `md_price`.`MedecineNameID` = `md_name`.`MedecineNameID` AND `md_price`.`Status` = '1' ORDER BY `md_category`.`MedecineCategoryName` ASC, Medecinename ASC, `Date` DESC";
						$query = mysql_query($sql);
					?>
					<table style="background: none; margin-left: auto; margin-right: auto;width: 960px;padding: 5px;text-align: center;" class='list'>
						<thead>
							<tr id='th'>
								<th>Medecine Name</th>
								<th>Buying Price</th>
								<th>Selling Amount</th>
								<th>Emballage</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id='fr'>
							<?php
							$printed = "";
								while ($row = mysql_fetch_assoc($query)) {
									if($printed != $row['MedecineCategoryName']){
										echo "<tr id=th><th align=left colspan=5>{$row['MedecineCategoryName']}</th></tr>";
										$printed = $row['MedecineCategoryName'];
									}
						?>
									<tr>
										<td align=left><?= $row['MedecineName'] ?></td>
										<td><?= $row['BuyingPrice'] ?></td>
										<td><?= $row['Amount'] ?></td>
										<td><?= ($row['Emballage'] == '1')? "Yes" : "No" ?></td>
										<td><?= $row['Date'] ?></td>
									</tr>
						<?php			
								}
							?>
						</tbody>
					</table>
				</div>
		</div>
	</div>

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