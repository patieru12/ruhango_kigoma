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
	<div id='w' style='height: auto;'>
		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
		   style="display: none;">
		<tr>
			<td id="ds_calclass"></td>
		</tr></table>
		
			<div id="content" style='height: auto;'>
				<h1 style='margin-top:-55px'>Uploading Medicine Tarif Document <a href='./uploads/consumable_tarif_tmp.xlsx'>xls</a></h1>
				<b>
					<?= $error ?>
					<label id=out_put></label>
				</b>
				<form action="upload_consumable.php" id="act_frm" method=post enctype="multipart/form-data">
					Select File:<input type=file name='excel_file' id="excel_file" class=txtfield1 style='width:320px; font-size:12px' />
				</form>
			</div>
	</div>
<?php
  include_once "../footer.html";
  ?> 
<script type="text/javascript">
	$(document).ready(function(){
		$("#excel_file").change(function(){
			$("#act_frm").ajaxForm({
				target:"#out_put"
			}).submit();
		});
	});
</script>

</body>
</html>