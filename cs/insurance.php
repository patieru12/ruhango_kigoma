<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
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
	$active = "tarif";
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Insurance</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" cellspacing="0" style='font-size:14px;'>
    		<tr>
    			<!-- Left container -->
    			<td>
    				<table style='font-size:16px;'>
    					<form action='' method='post'>
			    			<input type='hidden' name='id' value='<?= $date['insurance']['id'] ?>' >
			    			<tr>
			    				<td><label for='in_name' >Name:</label></td>
								<td><input id="in_name" required type="text" name='InsuranceName' class='txtfield1' style='width:300px;' value = '<?= @$date['insurance']['name']; ?>' /></td>
							</tr>
							<tr>
			    				<td><label for='cat' >Category:</label></td>
								<td>
									<select required name='CategoryID' id='cat' class='txtfield1' style='width:300px;' >
											<option value=''> Select Here... </option>
											<?php 
												$table = "in_category";
												$sql = "SELECT `InsuranceCategoryID` , `InsuranceCode`, `InsuranceCategoryName` FROM `".$table."` ORDER BY `InsuranceCode` ASC";
												$query = mysql_query($sql);
												while ($row = mysql_fetch_assoc($query)) {
											?>
												<option <?= ($data['category']['id'] == $row["InsuranceCategoryID"] )? "selected" : "" ?> value='<?= $row["InsuranceCategoryID"] ?>'><?= $row['InsuranceCode']." - ".$row['InsuranceCategoryName'] ?></option>
											<?php
												}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" style='font-size:16px;' name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" /></td>
							</tr>
			    		</form>
    				</table>
    			</td>
    			<td style='padding:10px;'>
    				<table style='font-size:16px;'>
    					<form action='' method='post'>
    						<input type='hidden' name='id' value='<?= $data['price']['id'] ?>' > 
    						<tr>
				    			<td><label for='name' >Name:</label></td>
								<td>
									<select required name='InsuranceName' id='name' class='txtfield1' style='width:300px;'>
											<option value=''> Select Here... </option>
											<?php 
												$table = "in_name";
												$sql = "SELECT `InsuranceName`,`InsuranceNameID` FROM `".$table."` ORDER BY `InsuranceName` ASC";
												$query = mysql_query($sql);
												while ($row = mysql_fetch_assoc($query)) {
											?>
												<option <?= (@$date['insurance']['id'] == $row["InsuranceNameID"] )? "selected" : "" ?> value='<?= $row["InsuranceNameID"] ?>'><?= $row['InsuranceName'] ?></option>
											<?php
												}
										?>
									</select>
								</td>
							</tr>
							<tr>
			    				<td><label for='price' >Price:</label></td>
								<td><input id="price" required type="number" name='price' class='txtfield1' style='width:300px;' value='<?= @$data['price']['price'] ?>' /></td>
							</tr>
							<tr>
								<td for='date'>Date:</td>
								<td>
									<input id="date" onclick='ds_sh(this,"date")' type='date' name='date' class='txtfield1'  style='width:300px;' value="<?= (@$_GET['a'] == 'e')? $data['date'] : date('Y-m-d',time()) ?>" readonly />
								</td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" style='font-size:16px;' class="flatbtn-blu" name="<?= (@$_GET['a'] == 'e' )? 'edit_price' : 'save_price'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" /></td>
							</tr>
    					</form>
    				</table>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 40px;'></div>
    	</b>
	  <div class="contentWrap">
	  	<b>
	  		<table width='90%' border='0' class = 'list'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Category</th>
		  				<th>Name</th>
		  				<th>Date</th>
		  				<th>Price</th>
		  				<th></th>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  					$g = 0;
	  					$sql = "SELECT in_name.*,in_price.ValuePaid,in_category.InsuranceCategoryName FROM in_name,in_price,in_category WHERE in_category.InsuranceCategoryID = in_name.CategoryID AND in_price.InsuranceNameID = in_name.InsuranceNameID ORDER BY  in_category.InsuranceCode ASC, in_name.InsuranceName ASC";
	  					$query = mysql_query($sql);

	  					while ($list = mysql_fetch_assoc($query)) {
	  				?>
	  					<tr style='text-align:center;'>
	  						<td><?= ++$g; ?></td>
	  						<td style='text-align:left;'><?= $list['InsuranceCategoryName'] ?></td>
	  						<td style='text-align:left;'><?= $list['InsuranceName'] ?></td>
	  						<td style='text-align:left;'><?= $list['Date'] ?></td>
	  						<td style='text-align:right;'><?= $list['ValuePaid'] ?>%</td>
	  						<td width='150px'>
	  							<a href="?id=<?= $list['InsuranceNameID'] ?>&a=e">Edit</a>
	  							<a onclick='return false' href="?id=<?= $list['InsuranceNameID'] ?>&a=d">Delete</a>
	  						</td>
	  					</tr>
	  				<?php
	  					}
	  				?>
	  				<tr>

	  				</tr>
	  			</tbody>
	  		</table>
	  	</b>
	  </div>
	</div>
</div>
<?php
  include_once "../footer.html";
  ?> 
</body>
</html>