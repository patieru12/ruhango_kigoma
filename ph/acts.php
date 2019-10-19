<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	
	//var_dump($_POST); //die;
	$error = "";
	if (isset($_POST['save_name'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['name']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT ActNameID FROM ac_name WHERE Name='{$name}'",$field="ActNameID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Act is Still Active</span>";
		} else{
			//save new data
			if(saveData($sql="INSERT INTO ac_name SET  Name='{$name}'",$con)){
				$error = "<span class=succees>New Act Recorded Now</span>";
			}
		}
	}
	
require_once '../lib2/cssmenu/ph_header.html';
?>  
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Acts Configuration Panel</h1>
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
    					<form action='' method='post'>
			    			
								<table style="display: visible;border: none; font-size:16px;">
									<tr>
										<td>Name</td>
									</tr>
									<tr>
										<td>
											<input type='text' id='md_name' name='name' class='txtfield1' style='width:300px;' required >
										</td>
								<td colspan='' align=center>
								<input type="submit" class="flatbtn-blu" name="<?= (@$_GET['a'] == 'e' )? 'edit_name' : 'save_name'  ?>" value="<?= (@$_GET['a'] == 'e' )? 'Update' : 'Save'  ?>" style='font-size:16px;' />
								
								</td>
									</tr>
								</table>
			    		</form>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 4px;'></div>
    	</b>
		
				<div style='max-height: 330px; width:710px;overflow: auto; padding-top: 10px;'>
					  <style type="text/css">
					  	#th th{
					  		padding: 8px;
					  		background: #999;
					  	}
					  	#fr tr td{
					  		padding: 5px;
					  	}
					  </style>
					<?php
						$sql = "SELECT `ac_name`.* FROM `ac_name` ORDER BY `Name` ASC";
						$query = mysql_query($sql);
					?>
					<table style="background: none; margin-left: auto; margin-right: auto;width: 600px;padding: 5px;text-align: center;" class='list'>
						<thead>
							<tr id='th'>
								<th>Name</th>
							</tr>
						</thead>
						<tbody id='fr'>
							<?php
							$printed = "";
								while ($row = mysql_fetch_assoc($query)) {
									
						?>
									<tr>
										<td align=left><?= $row['Name'] ?></td>
										
									</tr>
						<?php			
								}
							?>
						</tbody>
					</table>
				</div>
	</div>
</body>
</html>