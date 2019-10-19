<?php
	session_start();
	
	$path = "";
	require_once "../lib/db_function.php";
	if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	if (isset($_POST['save_name'])) {
		
		//var_dump($_POST); die;
		$exam_register = mysql_real_escape_string(trim($_POST['exam_register']));
		$name = mysql_real_escape_string(trim($_POST['exam_name']));
		$cat  = mysql_real_escape_string(trim($_POST['exam_code']));
		$result  = mysql_real_escape_string(trim($_POST['result_cat']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT ExamID FROM la_exam WHERE ExamName='{$name}' && ExamCode='{$cat}' && ResultType='{$result}' && ExamID != '".PDB(@$_POST['id'],true,$con)."'",$field="ExamID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Exam is Still Active</span>";
		} else{
			//save new data
			$sq = (@$_POST['id']?"UPDATE":"INSERT INTO");
			if(saveData($sql="{$sq} la_exam SET RegisterID='{$exam_register}', ExamName='{$name}', ExamCode='{$cat}', ResultType='{$result}'".(@$_POST['id']?" WHERE ExamID='".PDB($_POST['id'], true, $con)."'":""),$con)){
				$error = "<span class=success>Exam Recorded Now</span>";
			}
		}
	}
	//check if is the update time
	$update = null;
	if(@$_GET['update'] && is_numeric($_GET['update'])){
		$update = formatResultSet($rslt = returnResultSet($sql="SELECT * FROM la_exam WHERE ExamID='".PDB($_GET['update'],true,$con)."'",$con),$multirows=false,$con);
	}
	
	if(@$_GET['delete']){
		//
	}
	$active = "exam";
	require_once "../lib2/cssmenu/lab_header.html";

	$registers = formatResultSet($rslt = returnResultSet($sql="SELECT * FROM la_register WHERE status=1 ORDER BY name ASC", $con), true, $con);
	?>
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
    				<table style='font-size:16px;'>
    					<form action='new_exam.php' method='post'>
						<?= @$update['ExamID']?"<input type=hidden name=id value='{$update['ExamID']}' />":"" ?>
			    			<tr>
			    				<td><label for='la_register' >Register:</label></td>
								<td>
									<select id="la_register" required name='exam_register' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$update['ExamName']; ?>' >
										<?php
										if($registers){
											foreach($registers AS $r){
												echo "<option ".($r['id'] == $update['RegisterID']?"selected":"")." value='{$r['id']}'>{$r['name']}</option>";
											}
										}
										?>
									</select>
								</td>
							</tr>
			    			<tr>
			    				<td><label for='in_name' >Name:</label></td>
								<td><input id="in_name" required type="text" name='exam_name' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$update['ExamName']; ?>' /></td>
							</tr>
			    			<tr>
			    				<td><label for='in_name' >Code:</label></td>
								<td><input id="in_name" required type="text" name='exam_code' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$update['ExamCode']; ?>' /></td>
							</tr>
							<tr>
			    				<td><label for='in_name' >Result Category:</label></td>
								<td><label><input type=radio required name=result_cat value=1 <?= @$update['ResultType'] == 1?"checked":"" ?>>Multiple Result</label><br />
								<label><input type=radio required name=result_cat value=0 <?= @$update['ResultType'] == 0?"checked":"" ?>>Single Result</label></td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$update['ExamID']? 'Update' : 'Save')  ?>" /></td>
							</tr>
			    		</form>
    				</table>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 40px;'></div>
    	</b>
	  <div class="contentWrap" style='width:100%; max-height:300px; overflow:auto;'>
	  	<b>
	  		<table width='' border='0' class = 'list'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Register</th>
		  				<th>Exam</th>
		  				<th>Code</th>
		  				<th>Result</th>
		  				<th>Action</th>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  					$g = 0;
	  					$sql = "SELECT a.*, b.name AS registerName FROM la_exam AS a INNER JOIN la_register AS b ON a.RegisterID = b.id ORDER BY registerName ASC, a.ExamName ASC";
	  					$query = mysql_query($sql)or die(mysql_error());

	  					while ($list = mysql_fetch_assoc($query)) {
	  				?>
	  					<tr style=''>
	  						<td><?= ++$g; ?></td>
	  						<td><?= $list['registerName'] ?></td>
	  						<td><?= $list['ExamName'] ?></td>
	  						<td><?= $list['ExamCode'] ?></td>
	  						<td><?= $list['ResultType']?"Multiple":"Single" ?> Result</td>
	  						<td>
	  							<a href="new_exam.php?update=<?= $list['ExamID'] ?>" title="Edit"><img src="../images/edit.png" /></a>
	  						</td><!--
	  						<td>
								<a onclick='return confirm("Delete The <?= $list['ExamName'] ?> FROM Exam List\nAvailable If the exam had Never been used!")' href="new_exam.php?delete=<?= $list['ExamID'] ?>&a=d" title="Delete"><img src='../images/delete.png' /></a>
	  						</td>-->
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
	<?php
  include_once "../footer.html";
  ?>
</body>
</html>