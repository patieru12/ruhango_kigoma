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
	
require_once "../lib2/cssmenu/cs_header.html";
?>

  <script type="text/javascript">
  function save_request( tbl, fld, ref_val,ref_field, mdid){
	//alert(mdid); return false;
	$.ajax({
		type: "POST",
		url: "./save_md_request.php",
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val().replace(/ /g,"%20") + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=medecines.php?date=<?= @$_GET['date'] ?>&mdid=" + mdid,
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
	
		<div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Active Branches</h1>
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
										<td>Sector</td>
									</tr>
									<tr>
										<td>
											<input id='name' name='name' class='txtfield1' style='width:200px;' required >
											
										</td>
										<td>
											<input id='unitP' name='sector' class='txtfield1' style='width:200px;' required >
										</td>
							<td colspan='1'>
								<center>
									<input type="button" id='save_btn' class="flatbtn-blu" name="save" value="Save" style = " font-size:16px;" />
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
				<center>Active Post</center>
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
				</div>
				</td>
				</tr>
			</table>
		</div>
	</div>

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