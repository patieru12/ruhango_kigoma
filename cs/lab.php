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
		$name = mysql_real_escape_string(trim($_POST['exam_name']));
		$cat  = mysql_real_escape_string(trim($_POST['CategoryID']));
		$price  = mysql_real_escape_string(trim($_POST['price']));
		if(returnSingleField($sql="SELECT ExamPriceID FROM la_price WHERE ExamID='{$name}' && InsuranceTypeID='{$cat}' && Amount='{$price}'",$field="ExamPriceID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Price is Still Active</span>";
		} else{
			//update the current record
			saveData($sql="UPDATE la_price SET Status=0 WHERE ExamID='{$name}' && InsuranceTypeID='{$cat}' ", $con);
			//save new data
			if(saveData($sql="INSERT INTO la_price SET ExamID='{$name}', InsuranceTypeID='{$cat}', Amount='{$price}', Date=NOW(), Status=1",$con)){
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
</head>

<body>
  <div id="topbar">
  <a href="./index.php">WELCOME</a> | 
  <a href="./acts.php">Acts</a> | 
  <a href="./lab.php">Laboratory</a> | 
  <a href="./cst.php">Consultation</a> | 
  <a href="./medecines.php">Medecines Price</a> | 
  <a href="./insurance.php">Insurance</a> | 
  <!--<a href='./create.php'>Create New Document</a> |-->
  <a href='facture.php'>Facture</a> |
  <a href='../logout.php'>Logout</a>
  </div>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Laboratory Configuration Panel</h1>
    	<b>
		
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="1" cellspacing="0">
    		<tr >
    			<td style='align: center;'>Exams</td>
    		</tr>
    		<tr>
    			<!-- Left container -->
    			<td>
					<?= $error ?>
    				<table>
    					<form action='' method='post'>
			    			<tr>
			    				<td><label for='in_name' >Name:</label></td>
			    				<td><label for='cat' >Category:</label></td>
			    				<td><label for='in_name' >Price:</label></td>
							</tr>
							<tr>
								<td>
	  				<?php
	  					$g = 0;
	  					$sql = "SELECT la_exam.* FROM la_exam ORDER BY ExamName ASC";
	  					$query = mysql_query($sql)or die(mysql_error());
						$e_list = array();
						$e_list_r = array();
						echo "<select name=exam_name style='width:300px;font-size:16px;' class=txtfield1>";
	  					while ($list = mysql_fetch_assoc($query)) {
							$e_list_r[] = array("name"=>$list['ExamName'],"id"=>$list['ExamID']);
	  				?>
					<option value='<?= $list['ExamID'] ?>'><?= $list['ExamName']."-".$list['ExamCode'] ?></option>
	  				<?php
	  					}
	  				?>
					</select>
								</td>
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
								<td><input id="in_name" required type="text" name='price' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$date['insurance']['name']; ?>' /></td>
							
								<td colspan='2' align=center>
								<input type="submit" class="flatbtn-blu" name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" style='font-size:16px;' />
								
								</td>
							</tr>
							<tr>
								<td colspan=4 style="font-size:16px;">
								Load Excel Document <input type=file name='excel_doc' class=txtfield1 style='width:300px;font-size:16px;' />
								</td>
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
	  					<th></th>
						<?php
						foreach($i_category_h as $h){
							echo "<th>{$h}</th>";
						}
						?>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  					foreach($e_list_r as $e){
	  				?>
	  					<tr style='text-align:right;'>
	  						<td style='text-align:left;'><?= $e['name']; ?></td>
	  						<?php
							foreach($i_category as $id){
								$amount = returnSingleField($sql="SELECT Amount FROM la_price WHERE ExamID='{$e['id']}' && InsuranceTypeID='{$id}' && Status=1",$field="Amount",$data=true, $con);
								echo "<td>{$amount}</td>";
							}
							?>
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
</body>
</html>