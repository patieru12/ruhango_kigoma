<?php
	session_start();
	
	$path = "";
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	if (isset($_POST['save_name'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['exam_name']));
		$cat  = mysql_real_escape_string(trim($_POST['exam_code']));
		$result  = mysql_real_escape_string(trim($_POST['result_cat']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT ExamID FROM la_exam WHERE ExamName='{$name}' && ExamCode='{$cat}' && ResultType='{$result}'",$field="ExamID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Exam is Still Active</span>";
		} else{
			//save new data
			if(saveData($sql="INSERT INTO la_exam SET  ExamName='{$name}', ExamCode='{$cat}', ResultType='{$result}'",$con)){
				$error = "<span class=succees>New Exam Recorded Now</span>";
			}
		}
	}
	$active = "exam";
	require_once "../lib2/cssmenu/cs_header.html";
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
    					<form action='' method='post'>
			    			<tr>
			    				<td><label for='in_name' >Name:</label></td>
								<td><input id="in_name" required type="text" name='exam_name' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$date['insurance']['name']; ?>' /></td>
							</tr>
			    			<tr>
			    				<td><label for='in_name' >Code:</label></td>
								<td><input id="in_name" required type="text" name='exam_code' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$date['insurance']['name']; ?>' /></td>
							</tr>
							<tr>
			    				<td><label for='in_name' >Result Category:</label></td>
								<td><label><input type=radio required name=result_cat value=1>Multiple Result</label><br />
								<label><input type=radio required name=result_cat value=0>Single Result</label></td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" /></td>
							</tr>
			    		</form>
    				</table>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 40px;'></div>
    	</b>
	  <div class="contentWrap" style='width:600px; max-height:300px; overflow:auto;'>
	  	<b>
	  		<table width='' border='0' class = 'list'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Exam</th>
		  				<th>Code</th>
		  				<th>Result Type</th><!--
		  				<th></th>-->
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<?php
	  					$g = 0;
	  					$sql = "SELECT la_exam.* FROM la_exam ORDER BY ExamName ASC";
	  					$query = mysql_query($sql)or die(mysql_error());

	  					while ($list = mysql_fetch_assoc($query)) {
	  				?>
	  					<tr style=''>
	  						<td><?= ++$g; ?></td>
	  						<td><?= $list['ExamName'] ?></td>
	  						<td><?= $list['ExamCode'] ?></td>
	  						<td><?= $list['ResultType']?"Multiple":"Single" ?> Result</td>
	  						<!--<td width='150px'>
	  							<a href="?id=<?= $list['InsuranceNameID'] ?>&a=e">Edit</a>
	  							<a onclick='return false' href="?id=<?= $list['InsuranceNameID'] ?>&a=d">Delete</a>
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
	</div>
  <?php
  include_once "../footer.html";
  ?>
</body>
</html>