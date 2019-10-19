<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	if(@$_POST['save_date']){
		//var_dump($_POST);
		if(@$_POST['date'] || !preg_match("/^[0-9]{4}-[0-9]{2}[0-9]{2}$/",$_POST['date']) || $_POST['date'] < date("Y-m-d",time())){
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
	if(@$_GET['act'] == 'delete' && is_numeric($_GET['id'])){
		saveData("DELETE FROM sy_conge WHERE DateID='".PDB($_GET['id'],true,$con)."'",$con);
	}
	
	
require_once "../lib2/cssmenu/cs_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>List of Valid Closed Days</h1>
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
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn-blu" style='font-size:12px;' name="save_date" value="Save" /></td>
							</tr>
			    		</form>
    				</table>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 10px;'></div>
    	</b>
	  <div class="contentWrap">
	  	<b>
			<h1>Active Closed Days</h1>
	  		<table width='' border='0' class = 'list' style='font-size:12px;'>
	  			<thead style='font-weight:bold;background: #ddd;'>
	  				<tr class='th'>
	  					<th>ID</th>
		  				<th>Date</th>
		  				<th colspan=2>&nbsp;</th>
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
	  				$dates = formatResultSet($rslt=returnResultSet($sql="SELECT sy_conge.*, sy_users.Name FROM sy_conge, sy_users WHERE sy_conge.UserID = sy_users.UserID ORDER BY Date DESC",$con),$multirows=true,$con);
					//var_dump($dates);
					//$frequecy = array("Once","Every Year","Every Month");
					for($i=0; $i<count($dates); $i++){
						echo "<tr ".(preg_match("/^".date("Y",time())."/",$dates[$i]['Date'])?"bgcolor=#e4e4e4":"")."><td>".($i+1)."</td><td>{$dates[$i]['Date']}</td><!--<td>Edit</td>--><td><a href='./ferier.php?act=delete&id={$dates[$i]['DateID']}' style='color:blue; text-decoration:none;' onclick='return confirm(\"Delete {$dates[$i]['Date']} From Closed Days\")'>Delete</a></td></tr>";
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