<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$data = array('insurance'=>array('name'=>'','id'=>''),'category'=>array('id'=>"",'code'=>""),'price'=>array('id'=>'','price'=>''),'date'=>"");
	$error = "";
	if(@$_GET['action'] == 'copy' && $_GET['date'] <= date("Y-m-d",time())){
		//copy the tarif to the current date
		$tarif = returnAllData($sql="SELECT DISTINCT Date FROM ac_price WHERE Date < '".(PDB($_GET['date'],true,$con))."' ORDER BY Date DESC LIMIT 0, 1",$con);
		//var_dump($tarif);
		//select all component of the selected tarif now
		$tarifs = returnAllData($sql="SELECT DISTINCT * FROM ac_price WHERE Date = '{$tarif[0]['Date']}'",$con);
		//var_dump($tarifs);
		
		for($i=0;$i<count($tarifs);$i++){
			//save_new tarif if not exist
			if(!$act_price = returnSingleField("SELECT ActPriceID FROM ac_price WHERE ActNameID='{$tarifs[$i]['ActNameID']}' && InsuranceCategoryID='{$tarifs[$i]['InsuranceCategoryID']}' && Date='".(PDB($_GET['date'],true,$con))."'","ActPriceID",$data=true, $con)){
				//save the new price now
				saveData("INSERT INTO ac_price SET ActNameID='{$tarifs[$i]['ActNameID']}', InsuranceCategoryID='{$tarifs[$i]['InsuranceCategoryID']}', Amount='{$tarifs[$i]['Amount']}', Date='".(PDB($_GET['date'],true,$con))."', Status=1",$con);
			} else{
				//update the record is exists before
				saveData("UPDATE ac_price SET Amount='{$tarifs[$i]['Amount']}' WHERE ActPriceID='{$act_price}'",$con);
			}
		}
					
	}
	if (isset($_POST['save_name'])) {
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['act_name']));
		$cat  = mysql_real_escape_string(trim($_POST['CategoryID']));
		$price  = mysql_real_escape_string(trim($_POST['price']));
		if(returnSingleField($sql="SELECT ActPriceID FROM ac_price WHERE ActNameID='{$name}' && InsuranceCategoryID='{$cat}' && Amount='{$price}' && Status=1",$field="ActPriceID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Price is Still Active</span>";
		} else{
			//update the current record
			saveData($sql="UPDATE ac_price SET Status=0 WHERE ActNameID='{$name}' && InsuranceCategoryID='{$cat}' ", $con);
			//save new data
			if(saveData($sql="INSERT INTO ac_price SET ActNameID='{$name}', InsuranceCategoryID='{$cat}', Amount='{$price}', Date=NOW(), Status=1",$con)){
				$error = "<span class=succees>New Price Recorded Now</span>";
			}
		}
	}
require_once "../lib2/cssmenu/cs_header.html";
?>

  <script type="text/javascript">
  function save_request( tbl, fld, ref_val,ref_field){
	$.ajax({
		type: "POST",
		url: "./save_request.php",
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val() + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=acts.php?date=<?= @$_GET['date'] ?>",
		cache: false,
		success: function(result){
			
			$(".update_result").html(result);
			$("." + cl).html(ex_val);
			$("#edit_mode").val("0");
		}
	});
  }
  function edit_function(cl,ex_val,tbl,ref_val,ref_field, fld, cl_ass='fld_txt'){
	  $("." + cl).html("<input id=focus_now class='" + cl_ass + "' onclick='' onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\");' type=text value='" + ex_val + "' />");
	  $("#focus_now").focus();
	  $("#edit_mode").val("1");
  }
  </script>
  <style>
	.fld_txt{
		width:100%;
	}
  </style>
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
    				<?php
	  					$g = 0;
	  					$sql = "SELECT ac_name.* FROM ac_name ORDER BY Name ASC";
	  					$query = mysql_query($sql)or die(mysql_error());
						$a_list = array();
						$a_list_r = array();
						//echo "<select name=act_name style='width:300px;font-size:16px;' class=txtfield1>";
	  					while ($list = mysql_fetch_assoc($query)) {
							$a_list_r[] = array("name"=>$list['Name'],"id"=>$list['ActNameID']);
	  				
	  					}
	  				?>
					
					<?php 
						$table = "in_category";
						$sql = "SELECT `InsuranceCategoryID` , `InsuranceCode`, `InsuranceCategoryName` FROM `".$table."` ORDER BY `InsuranceCode` ASC";
						$query = mysql_query($sql);
						$i_category = array();
						while ($row = mysql_fetch_assoc($query)) {
							$i_category[] = $row["InsuranceCategoryID"];
							$i_category_h[] = $row["InsuranceCategoryName"];
					
						}
				?>
    			</td>
    		</tr>
    	</table>
		<input type=hidden value=0 id=edit_mode />
    	<div style='height: 4px;'></div>
    	</b>
	  <div class="contentWrap">
	  	<b>
		<table border=0>
			<tr>
				<td>
					<span class=update_result></span>
					Tarif Changes<br />
					<style>
					.lh{
						color:blue; text-decoration:none;
					}
					.lh:hover, a#lh_active{
						color:red;
					}
					.img_link:hover{
						border-bottom:2px solid red;
					}
					</style>
					<div style='height:330px; overflow:auto;'>
					<?php
					$tarifs = returnAllData($sql="SELECT DISTINCT Date FROM ac_price ORDER BY Date DESC",$con);
					//var_dump($tarifs);
					$printed = "from ";
					if(!@$_GET['date'])
						$_GET['date'] = $tarifs[0]['Date'];
					
					foreach($tarifs as $tdate){
						echo "<a class='lh' ".(@$_GET['date']==$tdate['Date']?"id='lh_active'":"")." style='' href='./acts.php?date={$tdate['Date']}' title='Change tarif for bill made {$printed} {$tdate['Date']}'>".$tdate['Date']."</a><br />";
						$printed = "between ".$tdate['Date']." and ";
					}
					?>
					</div>
				</td>
				<td>
					<center>Tarif <?= @$_GET['date']?"du ".$_GET['date']:"Actif"; ?></center>
					<div style='height:330px; overflow:auto;'>
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
								$row = 1;
								foreach($a_list_r as $e){
							?>
								<tr style='text-align:right;'>
									<td style='text-align:left;'><?= $e['name']; ?></td>
									<?php
									$col = 1;
									foreach($i_category as $id){
										$amount = returnSingleField($sql="SELECT ActPriceID, Amount FROM ac_price WHERE ActNameID='{$e['id']}' && InsuranceCategoryID='{$id}' ".(@$_GET['date']?" && Date='".(PDB($_GET['date'],true,$con))."'":""),$field="Amount",$data=true, $con);
										$data = returnAllData($sql,$con);
										//var_dump($data);
										$amount = $data[0]['Amount'];
										if($amount == 0)
											$amount = "Free";
										if($amount == -1){
											$amount = "Not Supported";
										}
										echo "<td class='edit{$row}{$col}' onclick='if($(\"#edit_mode\").val() == 0){edit_function(\"edit{$row}{$col}\",\"{$amount}\",\"ac_price\",\"{$data[0]['ActPriceID']}\",\"ActPriceID\",\"Amount\");}'>{$amount}</td>";
										$col++;
									}
									?>
								</tr>
							<?php
							$row++;
								}
							?>
						</tbody>
						</table>
					</div>
				</td>
				<td style=''>
					<table>
						<tr>
							<td style='padding-left:4px;'>
							<br />
								Change Selected Tarif<br />
								<img class=img_link onclick='<?= @$_GET['date'] == $tarifs[(count($tarifs) - 1)]['Date']?"alert(\"No Previous Tarif Available\"); return;":"" ?>if(confirm("Copy The usable Tarif Before <?= @$_GET['date'] ?>")){ window.location="./acts.php?date=<?= @$_GET['date'] ?>&action=copy" ; }' style='cursor:pointer;' src='../images/copy.png' title='Copy tarif from last updates' /><br />
								New Tarif<br />
								<input type=text class=txtfield1 id='save_new_date' onclick='ds_sh(this,"save_new_date")' name=date value='<?= date("Y-m-d",time()) ?>' /><br />
								<img class=img_link onclick='if(confirm("Copy The usable Tarif Before " + $("#save_new_date").val())){ window.location="./acts.php?date=" + $("#save_new_date").val() + "&action=copy" ; }' style='cursor:pointer;' src='../images/save.png' title='Save New Tarif for the selected Date' />
							</td>
						</tr>
					</table>
				</td>
			</tr>
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