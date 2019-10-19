<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	//$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	if (isset($_POST['save_name'])) {
		$name = mysql_real_escape_string(trim($_POST['exam_name']));
		$result  = mysql_real_escape_string(trim($_POST['result']));
		//var_dump($_POST); die;
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT ResultID FROM la_result WHERE ExamID='{$name}' && ResultName='{$result}' && Appear=1",$field="ResultID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Result is Still Active</span>";
		} else{
			//save new data
			if(saveData($sql="INSERT INTO la_result SET  ExamID='{$name}', ResultName='{$result}', Appear=1",$con)){
				$error = "<span class=succees>New Exam Recorded Now</span>";
			}
		}
	}
	
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Laboratory Results Configuration Panel</h1>
    	<b>
		
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="1" cellspacing="0">
    		<tr >
    			<td style='align: center;'>New Possible Result</td>
    		</tr>
    		<tr>
    			<!-- Left container -->
    			<td>
    				<table class=frm>
    					<form action='' method='post'>
			    			<tr>
			    				<td><label for='in_name' >Exam Name:</label></td>
								<td><label for='in_name' >Result:</label></td>
							</tr>
							<tr>
								<td>
	  				<?php
	  					$g = 0;
	  					$sql = "SELECT la_exam.* FROM la_exam ORDER BY ExamName ASC";
	  					$query = mysql_query($sql)or die(mysql_error());
						echo "<select name=exam_name style='width:300px;font-size:16px;' class=txtfield1>";
	  					while ($list = mysql_fetch_assoc($query)) {
	  				?>
					<option value='<?= $list['ExamID'] ?>'><?= $list['ExamName']."-".$list['ExamCode'] ?></option>
	  				<?php
	  					}
	  				?>
					</select></td>
								<td><input id="in_name" required type="text" name='result' class='txtfield1' style='width:300px;font-size:16px;' value = '<?= @$date['insurance']['name']; ?>' /></td>
							<td colspan='2' align=center><input type="submit" style='font-size:16px;' class="flatbtn-blu" name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" /></td>
							</tr>
			    		</form>
    				</table>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 3px;'></div>
    	</b>
	  <div class="contentWrap" style='max-height:300px; max-width:400px; overflow:auto;'>
	  	<b>
	  		<table width='300px' border='0' class = 'list'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Result</th>
		  				<th>&nbsp;</th>
	  				</tr>
	  			</thead>
	  				<?php
	  					$g = 0;
	  					$sql = "SELECT la_exam.* FROM la_exam ORDER BY ExamName ASC";
	  					$query = mysql_query($sql)or die(mysql_error());
						$exam_name = array();
	  					while ($list = mysql_fetch_assoc($query))
							$exam_name[] = $list;
						foreach($exam_name as $list){
							//select all result list for this activity
							$result = returnAllDataInTable($tbl="la_result",$con, $condition = "WHERE ExamID='{$list['ExamID']}' && Appear=1");
	  				?>
	  					<tr style='' style='font-weight:bold;background: #ddd;'>
	  						<th colspan=3 style='text-align:left;'><?= $list['ExamName'] ?>-<?= $list['ExamCode'] ?></th>
	  						
	  					</tr>
	  				<?php
					if($result){
						$i=1;
						foreach($result as $r){
							?>
							<tr>
								<td><?= $i++ ?></td>
								<td><?= $r['ResultName'] ?></td>
								<td> <a href=''>edit</a></td>
							<tr>
							<?php
						}
					} else{
						echo "<tr><td colspan=3>No Result</td></tr>";
					}
	  					}
	  				?>
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