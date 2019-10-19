<?php
	require_once "../lib/db_function.php";
	$name = mysql_real_escape_string(trim($_POST['name']));
	$sql = "SELECT `md_name`.`MedecineNameID`,`md_name`.`MedecineCategorID`,`md_name`.`Emballage`,`md_category`.`MedecineCategoryName`,`md_price`.* FROM `md_name`,`md_category`,`md_price` WHERE `md_category`.`MedecineCategoryID` = `md_name`.`MedecineCategorID` AND `md_price`.`MedecineNameID` = `md_name`.`MedecineNameID` AND `md_price`.`Status` = '1' AND `md_name`.`MedecineName` = '{$name}'";
	$query = mysql_query($sql);
	if (mysql_num_rows($query) == 1 ) {
		$row = mysql_fetch_assoc($query);
		$mdID = $row['MedecineNameID'];
		$emb = $row['Emballage'];
		$cat = $row['MedecineCategoryName'];
		$catid = $row['MedecineCategorID'];
		$Unit = $row['BuyingPrice'];
		$price = $row['Amount'];
		$date = $row['Date'];
		$pr_id = $row['MedecinePriceID'];
?>
	<tr>
		<td>Buying Price:</td>
		<td>
			<input type='hidden' name='nameId'value='<?= $mdID ?>' >
			<input type='hidden' name='priceId'value='<?= $pr_id ?>' >
			<input type='number' id='unitP' name='BuyingPrice'  class='txtfield1' style='width:300px;' value = "<?= $Unit ?>" >
		</td>
	</tr>
	<tr>
		<td>Price:</td>
		<td>
			<input type='text' id='price' name='Amount' class='txtfield1' style='width:300px;' value = "<?= $price ?>" >
		</td>
	</tr>
	<tr>
		<td for='date'>Date:</td>
		<td>
			<input type='date' id='date' name='date' onclick='ds_sh(this,"date")'  class='txtfield1'  style='width:300px;' value="<?= $date ?>"  readonly />
		</td>
		<tr id="ds_conclass">
			<td>&nbsp;</td>
			<td id="ds_calclass"></td>
		</tr>
	</tr>
		<script type="text/javascript">
		$(document).ready(function(){
			$('#save_btn').hide();
			$("#update_btn").show();
			var htmlHolder = '<select required disabled name="CategoryID" id="md_cat" class="txtfield1" style="width:300px;" ><option value=""> Select Here... </option><?php $table = "md_category"; $sql = "SELECT * FROM `".$table."` ORDER BY `MedecineCategoryName` ASC"; $query = mysql_query($sql);while ($row = mysql_fetch_assoc($query)) {?><option <?= ($row["MedecineCategoryID"] == $catid )? " selected " : "" ?> value=<?= $row["MedecineCategoryID"] ?>><?= $row["MedecineCategoryName"] ?></option><?php } ?></select>';
			$("#md_category").html(htmlHolder);
			var htmlHolder__ = "<td>Emballage:</td><td><input disabled type='radio' name='d' <?= ($emb == '1')? 'checked' : '' ?> >	Yes<input type='radio' name='d' <?= ($emb == '0')? 'checked' : '' ?> >	No</td>";
			$("#emballage").html(htmlHolder__);
		});
	</script>
<?php				
	}
	else{
?>
	<tr>
		<td>Buying Price:</td>
		<td>
			<input type='text' id='unitP' name='BuyingPrice' class='txtfield1' style='width:300px;' required >
		</td>
	</tr>
	<tr>
		<td>Price:</td>
		<td>
			<input type='text' id='price' name='Amount' class='txtfield1' style='width:300px;' required value="" readonly>
		</td>
	</tr>
	<tr>
		<td for='date'>Date:</td>
		<td>
			<input id="date" onclick='ds_sh(this,"date")' type='date' name='date' class='txtfield1'  style='width:300px;' value="<?= date('Y-m-d',time()) ?>" readonly />
		</td>
	</tr>
	<tr id="ds_conclass">
		<td>&nbsp;</td>
		<td id="ds_calclass"></td>
	</tr>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#update_btn").hide();
			$('#save_btn').show();
			var htmlHolder = '<select required name="CategoryID" id="md_cat" class="txtfield1" style="width:300px;" ><option value=""> Select Here... </option><?php $table = "md_category"; $sql = "SELECT * FROM `".$table."` ORDER BY `MedecineCategoryName` ASC"; $query = mysql_query($sql);while ($row = mysql_fetch_assoc($query)) {?><option value=<?= $row["MedecineCategoryID"] ?>><?= $row["MedecineCategoryName"] ?></option><?php } ?></select>';
			$("#md_category").html(htmlHolder);
			var htmlHolder__ = "<td>Emballage:</td><td><input type='radio' name='emballage' value='1' id='yesR' >	Yes<input type='radio' name='emballage' value='0' id='noR' >	No</td>";
			$("#emballage").html(htmlHolder__);
		});
	</script>
<?php
	}

?>
<script type="text/javascript">
	$(function(){
		$("#unitP").keyup(function(){
			var unit = $.trim($("#unitP").val());
			var psg = (unit * 20) / 100;
			psg += parseFloat(unit);
			$("#price").attr("value",function(){
				if(unit != "" || unit.lenght > 0 ) return psg;
				else return "";
			});
		});
	});
</script>
		
