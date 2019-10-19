<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	if(@$_POST['save_date']){
		//var_dump($_POST);
		if(!preg_match("/^[0-9]{4}-[0-9]{2}[0-9]{2}$/",$_POST['date']) || $_POST['date'] < date("Y-m-d",time())){
			//now save the date if not exist in the list
			if(!returnSingleField("SELECT DateID FROM sy_conge WHERE Date='{$_POST['date']}'","DateID",true,$con)){
				//now save the new date
				saveData("INSERT INTO sy_conge SET Date='{$_POST['date']}', UserID='{$_SESSION['user']['UserID']}'",$con);
				$error = "<span class=success>New Closed Day Saved</span>";
			} else{
				$error = "<span class=error>Date already in the list</span>";
			}
		} else{
			$error = "<span class=error>Invalid Date Selected</span>";
		}
		
	}
	
	//delete date now
	//if()
	$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	if (isset($_POST['save_name'])) {
		$name = mysql_real_escape_string(trim($_POST['InsuranceName']));
		$cat  = mysql_real_escape_string(trim($_POST['CategoryID']));

		if(saveData("INSERT INTO `in_name` SET `InsuranceName` = '{$name}' , `CategoryID` = '{$cat}' , `Date` = '".date('Y-m-d',time())."'  ",$con)) {
			echo "
				<script>
					alert('Save Done Successfully');
					window.location = './insurance.php';
				</script>
			";
		}
		else{
			echo "
				<script>
					alert('Save Fail');
					window.location = './insurance.php';
				</script>
			";
		}
	}
	elseif (isset($_POST['save_price'])) {
		$ID = mysql_real_escape_string(trim($_POST['InsuranceName']));
		$price = mysql_real_escape_string(trim($_POST['price']));
		$date = date("Y-m-d",time());
		if (saveData("INSERT INTO `in_price` SET `InsuranceNameID` = '{$ID}', `ValuePaid` = '{$price}',`Date` = '{$date}'  ",$con)) {
			echo "
				<script>
					alert('Save Done Successfully');
					window.location = './insurance.php';
				</script>
			";
		}
		else{
			echo "
				<script>
					alert('Save Fail');
					window.location = './insurance.php';
				</script>
			";
		}
	}
	elseif (isset($_GET['a'])) {
		$id = mysql_real_escape_string(trim($_GET['id']));
		//check if id exist 
		$sql_c = "SELECT InsuranceNameID FROM `in_name` WHERE `InsuranceNameID` = '{$id}' ";
		if (!isDataExist($sql_c,$con)) {
			echo "<script>
				alert('Incorrect entered data');
				window.location = 'insurance.php';
			</script>";
			exit();
		}
		if ($_GET['a'] == 'e') {
			$sql = "SELECT in_name.*,in_price.*,in_category.InsuranceCode FROM in_name,in_price,in_category WHERE in_category.InsuranceCategoryID = in_name.CategoryID AND in_price.InsuranceNameID = in_name.InsuranceNameID AND in_name.InsuranceNameID = '{$id}' ";
			$dr = mysql_query($sql);
			$dataD = mysql_fetch_assoc($dr);
			$date['insurance']['name'] = $dataD['InsuranceName'];
			$date['insurance']['id'] = $dataD['InsuranceNameID'];
			$data['category']['code'] = $dataD['InsuranceCode'];
			$data['category']['id'] = $dataD['CategoryID'];
			$data['price']['id'] = $dataD['InsurancePriceID'];
			$data['price']['price'] = $dataD['ValuePaid'];

			$data['date'] = $dataD['Date'];
			$r = 0;
		}
		elseif ($_GET['a'] == 'd') {
			$sql = "UPDATE `in_name` SET Status=0 WHERE `InsuranceNameID` = '{$id}' "; $r = 1;
			if (saveData($sql,$con) && $r == 1) {
				echo "<script>
					alert('Delete Done Successfully');
					window.location = 'insurance.php';
				</script>";
			}
			else {
				echo "<script>
					alert('Delete Fail!');
					window.location = 'insurance.php';
				</script>";
			}
		}
		else echo "<script>window.location = 'insurance.php';</script>";
	}/* 
	elseif (isset($_POST['edit_name'])) {
		$name = mysql_real_escape_string(trim($_POST['InsuranceName']));
		$cat  = mysql_real_escape_string(trim($_POST['CategoryID']));
		$id = $_POST['id'];
		if(saveData("UPDATE  `in_name` SET `InsuranceName` = '{$name}' , `CategoryID` = '{$cat}' WHERE `InsuranceNameID` = '{$id}' ",$con)) {
			echo "
				<script>
					alert('Update Done Successfully');
					window.location = './insurance.php';
				</script>
			";
		}
		else{
			echo "
				<script>
					alert('Update Fail');
					window.location = './insurance.php';
				</script>
			";
		}
	} */
	elseif (isset($_POST['edit_price'])) {
		$ID = mysql_real_escape_string(trim($_POST['InsuranceName']));
		$price = mysql_real_escape_string(trim($_POST['price']));
		$id = $_POST['id'];
		if (saveData("UPDATE `in_price` SET `ValuePaid` = '{$price}' WHERE `InsurancePriceID` = '{$id}' ",$con)) {
			echo "
				<script>
					alert('Update Done Successfully');
					window.location = './insurance.php';
				</script>
			";
		}
		else{
			echo "
				<script>
					alert('Update Fail');
					window.location = './insurance.php';
				</script>
			";
		}
	}
require_once "../lib2/cssmenu/rcp_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Automatic Prescription According To Diagnostic</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" cellspacing="0" style='font-size:14px;'>
    		<tr>
    			<!-- Left container -->
				<td>
					<input type=text name=search autocomplete=off style='width:100%' id=search placeholder="Filter Diagnostic" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<div  style='height:420px; width:250px; overflow:auto;'>
						<?php
						//select all active diagnostic now
						$diagnostic = formatResultSet($rslt=returnResultSet($sql="SELECT * FROM co_diagnostic WHERE DiagnosticCode != '' ORDER BY DiagnosticCode ASC",$con),$multirows=true,$con);
						for($i=0; $i<count($diagnostic); $i++){
							$ommit = false;
							$display = ommitStringPart($str=$diagnostic[$i]['DiagnosticName'],$char_to_display=30,$ommit);
							echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['DiagnosticName']}'":"").">".$display."</div>";
						}
						?>
					</div>
				</td>
				<td>
					<?= $error; ?>
					<form action='' method='post'>
    				<table style='font-size:16px;' border=0>
			    			
							<tr>
			    				<td><label>Diagnostic:</label></td>
								<td class=diagnostic></td>
							</tr>
			    			<tr>
			    				<td><label>Type:</label></td>
								<td>
								<label><input required type="radio" name='type' class='' style='font-size:12px;' value = '' onclick='' /> Medicines</label>
								<label style='margin-left:20px;'><input required type="radio" name='type' class='' style=' font-size:12px;' value = '' onclick='' /> Act</label>
								<label style='margin-left:20px;'><input required type="radio" name='type' class='' style='font-size:12px;' value = '' onclick='' /> Consumable</label>
								
								</td>
							</tr>
							<tr>
			    				<td><label>Prescription:</label></td>
								<td><input required type="text" placeholder="Name of act medicine or consumable" name='presc' class='txtfield1' style='width:100%; ' value = '' onclick='' /></td>
							</tr>
							<tr>
			    				<td><label>Quantity:</label></td>
								<td><input required type="text" name='quantity' class='txtfield1' style='width:100%; ' value = '' onclick='' /></td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" value="Save" /></td>
							</tr>
    				</table>
			    	</form>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 10px;'></div>
    	</b>
	</div>
</div>
  <?php
  include_once "../footer.html";
  ?>
</body>
</html>

<script>
	$(document).ready(function(){
		$("#search").keyup(function(){
			$("#bar").html("<img src='../images/ajax_clock_small.gif' />");
		});
	});
</script>