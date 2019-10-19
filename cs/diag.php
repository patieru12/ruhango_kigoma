<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	//check if oos the delete command now
	if(@$_GET['delete'] && is_numeric($_GET['delete'])){
		//check that the diagnostic is ever used
		if(!returnSingleField("SELECT DiagnosticRecordID FROM co_diagnostic_records WHERE DiagnosticID='".PDB($_GET['delete'],true,$con)."'","DiagnosticRecordID",true,$con)){
			if(saveData("DELETE FROM co_diagnostic WHERE DiagnosticID=".PDB($_GET['delete'],true,$con)."",$con)){
				$error = "<span class=success>Data Deleted</span>";
			}
		} else{
			$error = "<span class=success>Diagnostic is used Before It can not be deleted!</span>";
		}
	}
	if(@$_POST['save_diagno']){
		//try to save diagnostic or update the existing one
		if(!trim($_POST['french'])){
			$error = "<span class=error-text>No Diagnostic Saved</span>";
		} else{
			//check if we can update the existing records now
			//var_dump($_POST);
			if(@$_POST['diag_id'] && is_numeric($_POST['diag_id']) && @$_POST['diag_code']){
				//now update the existing record
				saveData("UPDATE co_diagnostic SET DiagnosticName='{$_POST['french']}', English='{$_POST['english']}' WHERE DiagnosticCode='{$_POST['diag_code']}' && DiagnosticID='{$_POST['diag_id']}'",$con);
				$error = "<span class=success>Diagnostic Updated!</span>";
				unset($_POST);
			} else{
				if(!returnSingleField("SELECT * FROM co_diagnostic WHERE DiagnosticCategoryID='".returnSingleField("SELECT 	DiagnosticCategoryID FROM co_diagnostic_category WHERE CategoryCode='{$_POST['category']}'","DiagnosticCategoryID",true,$con)."' && (DiagnosticName='{$_POST['french']}' || DiagnosticName='{$_POST['english']}')", "DiagnosticID",true,$con)){
					$code = IncrementString($sql="SELECT * FROM co_diagnostic WHERE DiagnosticCode LIKE('{$_POST['category']}%')", $field="DiagnosticCode", $static=$_POST['category'],$con);
					
					saveData($sql = "INSERT INTO co_diagnostic SET DiagnosticName='{$_POST['french']}', English='{$_POST['english']}', DiagnosticCode='{$code}', DiagnosticCategoryID='".returnSingleField("SELECT 	DiagnosticCategoryID FROM co_diagnostic_category WHERE CategoryCode='{$_POST['category']}'","DiagnosticCategoryID",true,$con)."', Reported=0",$con);
					//echo $sql;
					$error = "<span class=success>Diagnostic Saved!</span>";
					unset($_POST);
				} else{
					$error = "<span class=error-text>Diagnostic Exist!</span>";
				}
			}
		}
	}
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Diagnostic List</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" cellspacing="0" style='font-size:14px;'>
    		<tr>
    			<!-- Left container -->
    			<td>	
					<label id=out_put></label>
					<form action="upload_diagnostic_name.php" id="act_frm" method=post enctype="multipart/form-data">
						Select File:<input type=file name='excel_file' id="excel_file" class=txtfield1 style='width:320px; font-size:12px' />
						[Template file <a href='./uploads/care2xdiagx000data.xlsx'>here</a>]
					</form>
					<?= $error; ?>
					<?php
					//echo $error;
					$update = null;
					//if there is an update request find it
					
					if(@$_GET['update'] && is_numeric($_GET['update'])){
						$update = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.*, co_diagnostic_category.* FROM co_diagnostic, co_diagnostic_category WHERE co_diagnostic.DiagnosticCategoryID = co_diagnostic_category.DiagnosticCategoryID && co_diagnostic.DiagnosticID='".PDB($_GET['update'],true,$con)."'",$con),$multirows=false,$con);
					}
					//var_dump($update);
					?>
					<form action="./diag.php" method="post">
					<?php
					if($update != null){
						echo "<input type=hidden name=diag_id value='{$update['DiagnosticID']}' />";
						echo "<input type=hidden name=diag_code value='{$update['DiagnosticCode']}' />";
					}
					?>
					<table>
						<tr><td>Category</td><td>French</td><td>English</td></tr>
						<tr>
							<td>
								<?php
								//select available category now
								$category = formatResultSet(returnResultSet("SELECT * FROM co_diagnostic_category WHERE CategoryCode != ''",$con),true,$con);
								//var_dump($category);
								?>
								<select name=category class=txtfield1>
									<?php
									foreach($category as $c){
										$o = false;
										$cname = ommitStringPart($str=$c['CategoryName'],14,$o);
										echo "<option ".($c['CategoryCode'] == @$update['CategoryCode']?"selected":"")." title='{$c['CategoryName']}' value='{$c['CategoryCode']}'>{$cname}</option>";
									}
									?>
								</select>
							</td>
							<td>
								<input type=text name=french class=txtfield1 value='<?= @$_POST['french']?$_POST['french']:@$update['DiagnosticName'] ?>' />
							</td>
							<td>
								<input type=text name=english class=txtfield1 value='<?= @$_POST['english']?$_POST['english']:@$update['English'] ?>' />
							</td>
							<td>
								<input type=submit name=save_diagno class='flatbtn-blu' value='Save' />
							</td>
						</tr>
					</table>
					</form>
    			</td>
    		</tr>
    	</table>
    	</b>
	  <div class="contentWrap">
	  	<b>
			<h1>Diagnostic</h1>
			<div style='height:320px; overflow-y:auto; width:100%;'>
	  		<table width='' border='0' class = 'list' style='font-size:12px; width:100%;'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Category</th>
		  				<th>French</th>
		  				<th>English</th>
		  				<th colspan=2></th>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  				
	  				$dates = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic_category.* FROM co_diagnostic_category WHERE CategoryCode != '' ORDER BY DiagnosticCategoryID ASC",$con),$multirows=true,$con);
					for($i=0; $i<count($dates); $i++){
						echo "<tr><td colspan=6>{$dates[$i]['CategoryName']}: {$dates[$i]['CategoryCode']}</td></tr>";
						//select all diagnostic related to the active one
						$diag = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.* FROM co_diagnostic WHERE DiagnosticCategoryID='{$dates[$i]['DiagnosticCategoryID']}'",$con),$multirows=true,$con);
						
						for($t=0; $t<count($diag); $t++){
							$o=false;
							$english = ommitStringPart($diag[$t]['English'],70,$o);
							$o=false;
							$french = ommitStringPart($diag[$t]['DiagnosticName'],70,$o);
							echo "<tr><td>".($t+1)."</td><td>{$diag[$t]['DiagnosticCode']}</td><td title='{$diag[$t]['DiagnosticName']}'>{$french}</td><td title='{$diag[$t]['English']}'>{$english}</td>
							<td><a href='./diag.php?update={$diag[$t]['DiagnosticID']}' style='color:blue; text-decoration:none;'>Update</a></td>
							<td><a onclick='return confirm(\"Delete {$diag[$t]['DiagnosticName']} From Available Diagnostic\")' href='./diag.php?delete={$diag[$t]['DiagnosticID']}' style='color:blue; text-decoration:none;'>Delete</a></td>
							<!-- <td>{$diag[$t]['PECIME']}</td><td>{$diag[$t]['Reported']}</td>--></tr>";
						}
					}
	  				?>
	  			</tbody>
	  		</table>
			</div>
	  	</b>
	  </div>
	</div>
</div>
	<?php
  include_once "../footer.html";
  ?>
</body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
		$("#excel_file").change(function(){
			$("#out_put").html("<img src='../images/loading.gif' />")
			$("#act_frm").ajaxForm({
				target:"#out_put"
			}).submit();
		});
	});
</script>
<?php
	die;
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Diagnostic List</h1>
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
						<?= $error; ?>
    					<form action='' method='post'>
			    			<tr>
			    				<td><label for='in_name' >Date:</label></td>
								<td><input id="date" required type="text" name='date' class='txtfield1' style='width:150px; font-size:12px;' value = '' onclick='ds_sh(this,"date")' /></td>
							</tr><!--
			    			<tr>
			    				<td><label for='in_name' >Frequency:</label></td>
								<td>
									<select required name='frequency' id='frequency' class='txtfield1' style='width:150px; font-size:12px;'>
										<option value=''>Select Frequency</option>
										<option value='1'> Every Year </option>
										<!--<option value='2'>Every Month</option> 
										<option value='0'>Once</option>
									</select>
								</td>
							</tr>-->
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" value="Save" /></td>
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
			<h1>Diagnostic</h1>
	  		<table width='' border='0' class = 'list' style='font-size:12px;'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Diagnostic</th>
		  				<th>Code</th>
		  				<th>Pecime</th>
		  				<th>Reported</th>
		  				<!--<th>Registered By</th>-->
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  				/* $dates = formatResultSet($rslt=returnResultSet($sql="SELECT sy_conge.*, sy_users.Name FROM sy_conge, sy_users WHERE sy_conge.UserID = sy_users.UserID && sy_conge.Frequency='1' && Date < '".date("Y-m-d",time())."' ORDER BY Date ASC;",$con),$multirows=true,$con);
					//var_dump($dates);
					$frequecy = array("Once","Every Year","Every Month");
					for($i=0; $i<count($dates); $i++){
						//try to insert
						$dates[$i]['Date'] = date("Y",time()).substr($dates[$i]['Date'],4);
						//echo $dates[$i]['Date']; continue;
						if(!returnSingleField("SELECT DateID FROM sy_conge WHERE Date='{$dates[$i]['Date']}'","DateID",true,$con)){
							//now save the new date
							saveData("INSERT INTO sy_conge SET Date='{$dates[$i]['Date']}', Frequency='{$dates[$i]['Frequency']}', UserID='{$dates[$i]['UserID']}'",$con);
						}
					}
					 */
	  				$dates = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic_category.* FROM co_diagnostic_category ORDER BY CategoryName ASC",$con),$multirows=true,$con);
					//var_dump($dates);
					//$frequecy = array("Once","Every Year","Every Month");
					for($i=0; $i<count($dates); $i++){
						echo "<tr><td colspan=4>{$dates[$i]['CategoryName']}</td></tr>";
						//select all diagnostic related to the active one
						$diag = formatResultSet($rslt=returnResultSet($sql="SELECT co_diagnostic.* FROM co_diagnostic WHERE DiagnosticCategoryID='{$dates[$i]['DiagnosticCategoryID']}' ORDER BY DiagnosticName ASC",$con),$multirows=true,$con);
						
						for($t=0; $t<count($diag); $t++){
							echo "<tr><td>".($t+1)."</td><td>{$diag[$t]['DiagnosticName']}</td><td>{$diag[$t]['Code']}</td><td>{$diag[$t]['PECIME']}</td><td>{$diag[$t]['Reported']}</td></tr>";
						}
					}
	  				?>
	  			</tbody>
	  		</table>
	  	</b>
	  </div>
	</div>
</body>
</html>