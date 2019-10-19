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
		$name = mysql_real_escape_string(trim($_POST['MedecineName']));
		$cat  = mysql_real_escape_string(trim($_POST['CategoryID']));
		$emballage  = mysql_real_escape_string(trim($_POST['emballage']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT MedecineNameID FROM md_name WHERE MedecineName='{$name}' && MedecineCategorID='{$cat}' && Emballage='{$emballage}' && Status=1",$field="MedecineNameID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Medecine is Still Active</span>";
		} else{
			//update the existing status
			saveData("UPDATE md_name SET Status=0 WHERE MedecineName='{$name}' && MedecineCategorID='{$cat}' && Emballage='{$emballage}' && Status=1",$con);
			//save new data
			if(saveData($sql="INSERT INTO md_name SET  MedecineName='{$name}', MedecineCategorID='{$cat}', Emballage='{$emballage}', Status=1",$con)){
				$error = "<span class=succees>New Exam Recorded Now</span>";
			}
		}
	}
	
	if(@$_GET['emb'] == "update"){
		//insert new row now
		$md_id = saveAndReturnID("INSERT INTO md_name(MedecineName, MedecineCategorID, Emballage, Status) (SELECT MedecineName, MedecineCategorID, Emballage, Status FROM md_name WHERE MedecineNameID='".(PDB($_GET['mdid'],true,$con))."')", $con);
		//update the first data
		saveData("UPDATE md_name SET Status=0 WHERE MedecineNameID='".(PDB($_GET['mdid'],true,$con))."'",$con);
		saveData("UPDATE md_name SET Emballage='".(PDB($_GET['emballage'],true,$con))."' WHERE MedecineNameID='{$md_id}'",$con);
	}
	
require_once '../lib2/cssmenu/ph_header.html';
?>  
<style>
.link:hover{
	color:blue;
	text-decoration:underline;
}
</style>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
	<h1 style='margin-top:-55px'>Medicines Configuration Panel</h1>
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
			    			
								<table class=frm style="display: visible;border: none; font-size:16px;">
									<tr>
										<td>Name:</td>
										<td>Category:</td>
										<td>Emballage:</td>
									</tr>
									<tr>
										<td>
											<input type='text' id='md_name' name='MedecineName' class='txtfield1' style='width:300px;' required >
										</td>
										<td id='md_category'>
											<select required name='CategoryID' id='md_cat' class='txtfield1' style='width:300px;' >
												<option value=''> Select Here... </option>
													<?php 
														$table = "md_category";
														$sql = "SELECT * FROM `".$table."` ORDER BY `MedecineCategoryName` ASC";
															$query = mysql_query($sql);
														while ($row = mysql_fetch_assoc($query)) {
														?>
															<option value='<?= $row["MedecineCategoryID"] ?>'>
																<?= $row['MedecineCategoryName'] ?>
															</option>
														<?php
														}
													?>
											</select>
										</td>
										<td>
											<input type='radio' name='emballage' value='1' id='yesR' > Yes
											<input type='radio' name='emballage' value='0' id='noR' >	 No
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
		
				<div style='max-height: 380px; width:710px;overflow: auto; padding-top: 10px;'>
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
						$sql = "SELECT `md_name`.`MedecineNameID`,`md_name`.`MedecineName`,`md_name`.`MedecineCategorID`,`md_name`.`Emballage`,`md_category`.`MedecineCategoryName` FROM `md_name`,`md_category` WHERE `md_category`.`MedecineCategoryID` = `md_name`.`MedecineCategorID` AND `md_name`.`Status` = '1' ORDER BY `md_category`.`MedecineCategoryName` ASC, `md_name`.`MedecineName` ASC";
						$query = mysql_query($sql);
					?>
					<table style="background: none; margin-left: auto; margin-right: auto;width: 600px;padding: 5px;text-align: center;" class='list'>
						<thead>
							<tr id='th'>
								<th>Medecine Name</th><!--
								<th>Buying Price</th>
								<th>Selling Amount</th>-->
								<th>Emballage</th>
								<!--<th>Date</th>-->
							</tr>
						</thead>
						<tbody id='fr'>
							<?php
							$printed = "";
								while ($row = mysql_fetch_assoc($query)) {
									//var_dump($row);
									if($printed != $row['MedecineCategoryName']){
										echo "<tr id=th><th align=left colspan=5>{$row['MedecineCategoryName']}</th></tr>";
										$printed = $row['MedecineCategoryName'];
									}
						?>
									<tr>
										<td align=left><?= $row['MedecineName'] ?></td>
										<td class='link' title='Click to Toggle' onclick='if(confirm("Change From <?= $row['Emballage']?"Packed":"Unpacked" ?> Medicine To  <?= $row['Emballage']?"Unpacked":"Packed" ?> Medecine For <?= $row['MedecineName'] ?>")){ window.location="./medecines.php?emb=update&mdid=<?= $row['MedecineNameID'] ?>&emballage=<?= !$row['Emballage'] ?>"; }'><?= ($row['Emballage'] == '1')? "Yes" : "No" ?></td>
										
									</tr>
						<?php			
								}
							?>
						</tbody>
					</table>
				</div>
	</div>
	</div>
	
  <?php
  include_once "../footer.html";
  ?>
</body>
</html>