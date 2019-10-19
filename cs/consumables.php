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
		$tarif = returnAllData($sql="SELECT DISTINCT Date FROM cn_price WHERE Date = '".(PDB($_GET['reference'],true,$con))."' ORDER BY Date DESC LIMIT 0, 1",$con);
		//var_dump($tarif);
		//select all component of the selected tarif now
		$tarifs = returnAllData($sql="SELECT DISTINCT * FROM cn_price WHERE Date = '{$tarif[0]['Date']}'",$con);
		//var_dump($tarifs);
		
		for($i=0;$i<count($tarifs);$i++){
			//save_new tarif if not exist
			if(!$act_price = returnSingleField("SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$tarifs[$i]['MedecineNameID']}' && Date='".(PDB($_GET['date'],true,$con))."'","MedecinePriceID",$data=true, $con)){
				//save the new price now
				saveData("INSERT INTO cn_price SET  MedecineNameID='{$tarifs[$i]['MedecineNameID']}', BuyingPrice='{$tarifs[$i]['BuyingPrice']}', Amount='{$tarifs[$i]['Amount']}', Date='".(PDB($_GET['date'],true,$con))."', Status=1, Emballage='{$tarifs[$i]['Emballage']}'",$con);
			} else{
				//update the record is exists before
				saveData("UPDATE cn_price SET BuyingPrice='{$tarifs[$i]['BuyingPrice']}', Amount='{$tarifs[$i]['Amount']}', Emballage='{$tarifs[$i]['Emballage']}' WHERE MedecinePriceID='{$act_price}'",$con);
			}
		}
		
	}
	
	if(@$_GET['emb'] == "update" && is_numeric($_GET['mdid'])){
		$current = $_GET['mdid'];
		saveData("UPDATE md_price SET Emballage='".(PDB($_GET['emballage'],true,$con))."' WHERE MedecinePriceID='".PDB($_GET['mdid'],true,$con)."'",$con);
	}
	
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['name']));
		$price_b  = mysql_real_escape_string(trim($_POST['BuyingPrice']));
		$sell_price  = $price_b + round($price_b*0.2,1);
		$date  = mysql_real_escape_string(trim($_POST['date']));
		
		//check medicine name database
		if(!$md_id = returnSingleField($sql="SELECT MedecineNameID FROM cn_name WHERE MedecineName='{$name}'",$field="MedecineNameID",$data=true, $con)){
			//save the medicine and return the current medicines id
			$md_id = saveAndReturnID("INSERT INTO cn_name SET MedecineName='{$name}'",$con);
		}
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT MedecinePriceID FROM cn_price WHERE MedecineNameID='{$md_id}' && BuyingPrice='{$price_b}' && Date <= '{$date}'",$field="MedecinePriceID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Price is Still Active</span>";
		} else{
			//update the existing status
			saveData("UPDATE cn_price SET Status=0 WHERE MedecineNameID='{$name}' && Status=1",$con);
			//save new data
			if(saveData($sql="INSERT INTO cn_price SET  MedecineNameID='{$md_id}', BuyingPrice='{$price_b}', Amount='{$sell_price}', Date='{$date}', Emballage='0', Status=1",$con)){
				$error = "<span class=success>New Price Recorded Now</span>";
			}
		}
	}
	
require_once "../lib2/cssmenu/cs_header.html";
?>

  <script type="text/javascript">
  function save_request( tbl, fld, ref_val,ref_field, mdid){
	//alert(mdid); return false;
	$.ajax({
		type: "POST",
		url: "./save_cn_request.php",
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val().replace(/ /g,"%20") + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=consumables.php?date=<?= @$_GET['date'] ?>&mdid=" + mdid,
		cache: false,
		success: function(result){
			//alert(result);
			$(".update_result").html(result);
			//$("." + cl).html(ex_val);
			setTimeout(function(){
				$("#edit_mode").val("0");
			}, 200);
		}
	});
  }
  function edit_function(cl,ex_val,tbl,ref_val,ref_field, fld, cl_ass='fld_txt', mdid=''){
	  
	  $("#edit_mode").val("1");
	  $("." + cl).html("<input id=focus_now class='" + cl_ass + "' onclick='' onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\",\"" + mdid + "\");' type=text value='" + ex_val + "' />");
	  $("#focus_now").focus();
  }
  </script>
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
	
		<div id="content" style='height: auto; border:0px solid #000;'>
	<h1 style='margin-top:-55px'>Consumables Configuration Panel</h1>
	<b>
		<?= $error ?>
		</b>
			<form id='medecienId' action='./consumables.php' method='post' >
				<div class='line-1'>
					<table id="" class=frm style="display: visible; background: none; margin-left: auto; margin-right: auto; ">
						<tr>
							<td>
								<table style="display: visible;border: none;">
									<tr>
										<td>Consumable Name</td>
										<td>Buying Price</td>
										<td>Date</td>
									</tr>
									<tr>
										<td>
											<input id='name' name='name' class='txtfield1' style='width:200px;' required >
										</td>
										<td>
											<input id='unitP' name='BuyingPrice' class='txtfield1' style='width:150px;' required >
											
										</td>
										<td>
											<input id="date" onclick='ds_sh(this,"date")' type='date' name='date' class='txtfield1'  style='width:100px;' value="<?= date('Y-m-d',time()) ?>" readonly />
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
					Consumables Pricing Changes<br />
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
					$tarifs = returnAllData($sql="SELECT DISTINCT Date FROM cn_price ORDER BY Date DESC",$con);
					//var_dump($tarifs);
					$printed = "from ";
					if(!@$_GET['date'])
						$_GET['date'] = $tarifs[0]['Date'];
					$t_option = "";
					foreach($tarifs as $tdate){
						echo "<a class='lh' ".(@$_GET['date']==$tdate['Date']?"id='lh_active'":"")." style='' href='./consumables.php?date={$tdate['Date']}' title='Change tarif for bill made {$printed} {$tdate['Date']}'>".$tdate['Date']."</a><br />";
						$printed = "between ".$tdate['Date']." and ";
						$t_option .= "<option>{$tdate['Date']}</option>";
					}
					?>
					</div>
				</td>
				<td>
				<center>Pricing <?= @$_GET['date']?"du ".$_GET['date']:"Actif"; ?></center>
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
						$sql = "SELECT `cn_price`.MedecinePriceID, `cn_name`.`MedecineNameID`, `cn_name`.`MedecineName`,`cn_price`.* FROM `cn_name`,`cn_price` WHERE `cn_price`.`MedecineNameID` = `cn_name`.`MedecineNameID` && `cn_price`.`Date` = '{$_GET['date']}' ORDER BY Medecinename ASC, `Date` DESC";
						//echo $sql;
						$query = mysql_query($sql);
					?>
					<table style="background: none; margin-left: auto; margin-right: auto; width: 100%; padding: 5px;text-align: center;" class='list' id=tbl_ref>
						<tr id='th'>
							<th>Consumable Name</th>
							<th>Buying Price</th>
							<th>Selling Amount</th>
						</tr>
						<tbody id='fr'>
							<?php
							$printed = "";
								$row_ = 1;
								while ($row = mysql_fetch_assoc($query)) {
									
									$col = 1;
									?>
									<tr>
										<td style='width:253px;' align=left onclick='if($("#edit_mode").val() == 0){edit_function("edit<?= $row_.$col  ?>","<?= $row['MedecineName'] ?>","cn_name","<?= $row['MedecineNameID'] ?>","MedecineNameID","MedecineName","fld_txt","<?= $row['MedecinePriceID'] ?>"); }' class='edit<?= $row_.($col++) ?>'><?= $row['MedecineName'] ?></td>
										<td style='width:80px;' onclick='if($("#edit_mode").val() == 0){edit_function("edit<?= $row_.$col  ?>","<?= $row['BuyingPrice'] ?>","cn_price","<?= $row['MedecinePriceID'] ?>","MedecinePriceID","BuyingPrice","fld_txt","<?= $row['MedecinePriceID'] ?>"); }' class='edit<?= $row_.($col++) ?>'><?= $row['BuyingPrice'] ?></td>
										<td style='width:80px;' <?= $current == $row['MedecinePriceID']?"id='past_data'":"" ?>><?= $current == $row['MedecinePriceID']?"<input style='font-size:12px; text-align:center;' type=text id='test_focus' value='{$row['Amount']}' />":$row['Amount'] ?></td>
										<?php 
										$row['MedecineName'] = str_replace("'"," ",$row['MedecineName']);
										?>
										<!--<td style='width:90px;' class='link' title='Click to Toggle' onclick='if(confirm("Change From <?= $row['Emballage']?"Packed":"Unpacked" ?> Medicine To  <?= $row['Emballage']?"Unpacked":"Packed" ?> Medecine For <?= $row['MedecineName'] ?>")){ window.location="./medecines.php?emb=update&mdid=<?= $row['MedecinePriceID'] ?>&emballage=<?= !$row['Emballage'] ?>&date=<?= @$_GET['date'] ?>"; }'><?= ($row['Emballage'] == '1')? "Yes" : "No" ?></td>
										<td><?= $row['Date'] ?></td>-->
									</tr>
									<?php			
									$row_++;
								}
								/*  */
								?>
						</tbody>
					</table>
				</div>
				</td>
				<td style=''>
					<table>
						<tr>
							<td style='padding-left:4px;'>
							<br /><!--
								Change Selected Tarif<br />
								<img class=img_link onclick='<?= @$_GET['date'] == $tarifs[(count($tarifs) - 1)]['Date']?"alert(\"No Previous Tarif Available\"); return;":"" ?>if(confirm("Copy The usable Tarif Before <?= @$_GET['date'] ?>")){ window.location="./medecines.php?date=<?= @$_GET['date'] ?>&action=copy" ; }' style='cursor:pointer;' src='../images/copy.png' title='Copy tarif from last updates' /><br />
								-->Nouveau Tarif<br />
								Du:<br /><input type=text class=txtfield1 id='save_new_date' onclick='ds_sh(this,"save_new_date")' name=date value='<?= date("Y-m-d",time()) ?>' /><br />
								Reference:<br /><select class=txtfield1 id=reference name='' ><?= $t_option; ?></select>
								<br /><img class=img_link onclick='if(confirm("Nouveau Tarif Du " + $("#save_new_date").val() + " Avec les donnees du " + $("#reference").val())){ window.location="./consumables.php?date=" + $("#save_new_date").val() + "&action=copy&reference=" + $("#reference").val() ; }' style='cursor:pointer;' src='../images/save.png' title='Save New Tarif for the selected Date' />
							</td>
						</tr>
					</table>
				</td>
				</tr>
			</table>
		</div>
	</div>

<?php
  include_once "../footer.html";
  ?> 
<script type="text/javascript">
	$(document).ready(function(){
		$("#test_focus").focus();
		//$("#test_focus").focus(function(){
			setTimeout(
				function(e){
					$("#past_data").html($("#test_focus").val());
				},2);
		//});
		//alert();
		//change the copy table width
		setTimeout(function(){
			$("#copy").css("width:" + $("#tbl_ref").css("width") );
			//alert($("#copy").css("width"));
		},500);
		$("#filter_check").scroll(function(){
			//now track the top position of the header of the table
			$("#th").css("position:absolute");
			$("#th").css("top:0px");
			$("#th").css("left:0px");
			console.log($("#th").css("top:relative"));
		});
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
