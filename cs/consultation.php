<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	$error = "";
	if (isset($_POST['save_name'])) {
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['co_name']));
		$cat  = mysql_real_escape_string(trim($_POST['CategoryID']));
		$price  = mysql_real_escape_string(trim($_POST['price']));
		if(returnSingleField($sql="SELECT ConsultationPriceID FROM co_price WHERE ConsultationCategoryID='{$name}' && InsuranceCategoryID='{$cat}' && Amount='{$price}' && Status=1",$field="ConsultationPriceID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Price is Still Active</span>";
		} else{
			//update the current record
			saveData($sql="UPDATE co_price SET Status=0 WHERE ConsultationCategoryID='{$name}' && InsuranceCategoryID='{$cat}' ", $con);
			//save new data
			if(saveData($sql="INSERT INTO co_price SET ConsultationCategoryID='{$name}', InsuranceCategoryID='{$cat}', Amount='{$price}', Date=NOW(), Status=1",$con)){
				$error = "<span class=succees>New Price Recorded Now</span>";
			}
		}
	}
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Consultation Configuration Panel</h1>
    	<b>
		
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="1" cellspacing="0">
    		<tr>
    			<!-- Left container -->
    			<td>
					<?= $error ?>
    				<table>
    					<form action='' method='post'>
			    			<tr>
			    				<td><label for='in_name' >Name:</label></td>
								<td>
								<?php
	  					$g = 0;
	  					$sql = "SELECT co_category.* FROM co_category ORDER BY ConsultationCategoryName ASC";
	  					$query = mysql_query($sql)or die(mysql_error());
						$a_list = array();
						$a_list_r = array();
						echo "<select name=co_name style='width:300px;font-size:16px;' class=txtfield1>";
	  					while ($list = mysql_fetch_assoc($query)) {
							$a_list_r[] = array("name"=>$list['ConsultationCategoryName'],"id"=>$list['ConsultationCategoryID']);
	  				?>
					<option value='<?= $list['ConsultationCategoryID'] ?>'><?= $list['ConsultationCategoryName'] ?></option>
	  				<?php
	  					}
	  				?>
					</select>
								</td>
							</tr>
							<tr>
			    				<td><label for='cat' >Category:</label></td>
								<td>
									<select required name='CategoryID' id='cat' class='txtfield1' style='width:300px;font-size:16px;' >
											<option value=''> Select Here... </option>
											<?php 
												$table = "in_category";
												$sql = "SELECT `InsuranceCategoryID` , `InsuranceCode`, `InsuranceCategoryName` FROM `".$table."` ORDER BY `InsuranceCode` ASC";
												$query = mysql_query($sql);
												$i_category = array();
												while ($row = mysql_fetch_assoc($query)) {
													$i_category[] = $row["InsuranceCategoryID"];
													$i_category_h[] = $row["InsuranceCategoryName"];
											?>
												<option <?= ($data['category']['id'] == $row["InsuranceCategoryID"] )? "selected" : "" ?> value='<?= $row["InsuranceCategoryID"] ?>'><?= $row['InsuranceCode']." - ".$row['InsuranceCategoryName'] ?></option>
											<?php
												}
										?>
									</select>
								</td>
							</tr><tr>
			    				<td><label for='in_name' >Price:</label></td>
								<td><input id="in_name" required type="text" name='price' class='txtfield1' style='width:300px; font-size:16px;' value = '<?= @$date['insurance']['name']; ?>' /></td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" style=' font-size:16px;' name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" /></td>
							</tr>
			    		</form>
    				</table>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 4px;'></div>
    	</b>
	  <div class="contentWrap">
	  	<b>
	  		<table width='' border='0' class = 'list'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th><?php
						foreach($i_category_h as $h){
							echo "<th style='width:120px;'>{$h}</th>";
						}
						?>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  					foreach($a_list_r as $e){
	  				?>
	  					<tr style='text-align:right;'>
	  						<td style='text-align:left;'><?= $e['name']; ?></td>
	  						<?php
							foreach($i_category as $id){
								$amount = returnSingleField($sql="SELECT Amount FROM co_price WHERE ConsultationCategoryID='{$e['id']}' && InsuranceCategoryID='{$id}' && Status=1",$field="Amount",$data=true, $con);
								if($amount == 0)
									$amount = "Free";
								if($amount == -1){
									$amount = "Not Supported";
								}
								echo "<td>{$amount}</td>";
							}
							?>
	  					</tr>
	  				<?php
	  					}
	  				?>
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