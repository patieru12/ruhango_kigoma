<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("dm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}

	
	//var_dump($_POST); //die;
	$error = ""; $current = @$_GET['mdid'];

	if(@$_GET['st'] == "update" && is_numeric($_GET['mdid'])){
		$current = $_GET['mdid'];
		saveData("UPDATE sy_register SET status='".(PDB($_GET['status'],true,$con))."', consultantId=NULL WHERE id='".PDB($_GET['mdid'],true,$con)."'",$con);
	}

	if(@$_GET["remove_user"] && is_numeric($_GET["register_id"])){
		$registerid = PDB($_GET["register_id"], true, $con);
		saveData("UPDATE sy_register SET consultantId=NULL WHERE id='{$registerid}'", $con);
	}
	if(@$_GET['tp'] == "update" && is_numeric($_GET['mdid'])){
		$current = $_GET['mdid'];
		saveData("UPDATE sy_register SET type='".(PDB($_GET['type'],true,$con))."' WHERE id='".PDB($_GET['mdid'],true,$con)."'",$con);
	}
	if (isset($_POST['save'])) {
		// var_dump($_POST); die;
		$service 		= PDB($_POST['service'], true, $con);
		$name 			= mysql_real_escape_string(trim($_POST['name']));
		$registerCode  	= mysql_real_escape_string(trim($_POST['registerCode']));
		$type  			= mysql_real_escape_string(trim($_POST['type']));
		$date  			= mysql_real_escape_string(trim($_POST['date']));
		
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT id FROM sy_register WHERE registerCode='{$registerCode}'",$field="id",$data=true, $con)){
			$error = "<span class=error-text>Register Code exist in the system</span>";
		} else{
			//save new data
			if(saveData($sql="INSERT INTO sy_register SET serviceId='{$service}', name='{$name}', registerCode='{$registerCode}', type='{$type}', startDate='{$date}'",$con)){
				$error = "<span class=success>New Register can be used by consultant</span>";
			}
		}
	}

$active = "index";
require_once "../lib2/cssmenu/dm_header.html";
?>

  <script type="text/javascript">
  function save_request( tbl, fld, ref_val,ref_field, mdid){
	//alert(mdid); return false;
	$.ajax({
		type: "POST",
		url: "./save_register_request.php",
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val().replace(/ /g,"%20") + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=./?mdid=" + mdid,
		cache: false,
		success: function(result){
			//alert(result);
			$(".update_result").html(result);
			//$("." + cl).html(ex_val);
			setTimeout(function(){
				$("#edit_mode").val("0");
			}, 200);
		}
	});
  }
  function edit_function(cl,ex_val,tbl,ref_val,ref_field, fld, cl_ass='fld_txt', mdid=''){
	  
	  $("#edit_mode").val("1");
	  $("." + cl).html("<input id=focus_now class='" + cl_ass + "' onclick='' onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\",\"" + mdid + "\");' type=text value='" + ex_val + "' />");
	  $("#focus_now").focus();
  }
  </script>
  <style>
	.fld_txt{
		width:100%;
	}
  </style>
	<div id='w' style='height: auto;'>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
		<div id="content" style='height: auto; border:0px solid #000;'>
	<h1 style='margin-top:-55px'>Active Registers</h1>
	<b>
		<?= $error ?>
		</b>
			<form id='medecienId' action='./' method='post' >
				<div class='line-1'>
					<table id="" class=frm style="display: visible; background: none; margin-left: auto; margin-right: auto; ">
						<tr>
							<td>
								<table style="display: visible;border: none;">
									<tr>
										<td>Service</td>
										<td>Name</td>
										<td>Code</td>
										<td>Type</td>
										<td>Start Date</td>
									</tr>
									<tr>
										<td>
											<?php
											$services = formatResultSet($rslt=returnResultSet($sql="SELECT a.ServiceNameID AS id, CONCAT(a.ServiceCode, ' - ', a.ServiceName) AS name FROM se_name AS a WHERE a.Status = 1 ORDER BY name ASC",$con),$multirows=true,$con);
											// var_dump($services);
											?>
											<select id='serviceId' name='service' class='txtfield1' style='width:200px;' required >
												<?php
												if(count($services)> 0){
													foreach($services AS $s){
														echo "<option value='{$s['id']}'>{$s['name']}</option>";
													}
												}
												?>
											</select>
											
										</td>
										<td>
											<input id='name' name='name' class='txtfield1' style='width:200px;' required >
											
										</td>
										<td>
											<input id='unitP' name='registerCode' class='txtfield1' style='width:150px;' required >
											
										</td>
										<td>
											<label><input id="pecime" type='radio' name='type' value="1" /> PECIME</label>
											<label><input id="other" type='radio' name='type' value="0" checked /> OTHER</label>
										</td>
										<td>
											<input id="date" onclick='ds_sh(this,"date")' type='text' name='date' class='txtfield1'  style='width:100px;' value="<?= date('Y-m-d',time()) ?>" readonly />
										</td>
							<td colspan='1'>
								<center>
									<input type="submit" id='save_btn' class="flatbtn-blu" name="save" value="Save" style = " font-size:14px;" />
								</center>
							</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
						</tr>
					</table>
				</div>
			</form>
		<table border=0 style='width:100%'>
			<tr>
				<td>
					<span class=update_result></span>
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
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</div>
				</td>
				<td>
				<style type="text/css">
				  	#th th{
				  		padding: 8px;
				  		background: #999;
						 font-size:12px;
				  	}
				  	#fr tr td{
				  		padding: 5px; font-size:12px;
				  	}
				</style>
				<table class='list' id='copy' style='width:97.3%;'>
					<thead>
						<tr id='th'>
							<th style='width:15%;'>Service</th>
							<th style='width:15%;'>Register Code</th>
							<th style='width:35%;'>Register Name</th>
							<th style='width:5%;'>Type</th>
							<th style='width:14%;'>Start Date</th>
							<th style='width:30%;'>Current User</th>
							<th style='width:6%;'>Status</th>
						</tr>
					</thead>
					  
					  <input type=hidden value=0 id=edit_mode />
					<?php
						$sql = "SELECT 	a.id AS registerID,
										a.name AS registerName,
										a.registerCode AS registerCode,
										a.type AS registerType,
										a.startDate AS startDate,
										COALESCE(b.Name, '') AS currentUser,
										a.status AS status,
										COALESCE(c.ServiceCode,'') AS serviceCode
										FROM sy_register AS a
										LEFT JOIN sy_users AS b
										ON a.consultantId = b.UserID
										LEFT JOIN se_name AS c
										ON a.serviceId = c.ServiceNameID
										WHERE a.status = 1";
						//echo $sql;
						$query = mysql_query($sql);
					?>
					<tbody id='fr'>
							<?php
							$printed = "";
								$row_ = 1;
								while ($row = mysql_fetch_assoc($query)) {
									$col = 1;
									?>
									<tr>
										<td style='width:14.5%;' align=left onclick='if($("#edit_mode").val() == 0){edit_function("edit<?= $row_.$col  ?>","<?= $row['serviceCode'] ?>","sy_register","<?= $row['registerID'] ?>","id","serviceId","fld_txt","<?= $row['registerID'] ?>"); }' class='edit<?= $row_.($col++) ?>'><?= $row['serviceCode'] ?></td>
										<td style='width:14.5%;' align=left><?= $row['registerCode'] ?></td>
										<td style='width:33.9%;' align=left onclick='if($("#edit_mode").val() == 0){edit_function("edit<?= $row_.$col  ?>","<?= $row['registerName'] ?>","sy_register","<?= $row['registerID'] ?>","id","name","fld_txt","<?= $row['registerID'] ?>"); }' class='edit<?= $row_.($col++) ?>'><?= $row['registerName'] ?></td>
										<td style='width:3%;' class='link' title='Click to Toggle' onclick='if(confirm("Change From <?= $row['registerType']?"PECIME":"Other" ?> Register To  <?= $row['registerType']?"Other":"PECIME" ?> Register For <?= $row['registerName'] ?>")){ window.location="./?tp=update&mdid=<?= $row['registerID'] ?>&type=<?= !$row['registerType'] ?>"; }'><?= ($row['registerType'] == '1')? "PECIME" : "Other" ?></td>
										<td style='width:14%;' onclick='if($("#edit_mode").val() == 0){edit_function("edit<?= $row_.$col  ?>","<?= $row['startDate'] ?>","sy_register","<?= $row['registerID'] ?>","id","startDate","fld_txt","<?= $row['registerID'] ?>"); }' class='edit<?= $row_.($col++) ?>'><?= $row['startDate'] ?></td>
										<td style='width:30%;' <?= $current == $row['registerID']?"id='past_data'":"" ?>> <?php if ($row['currentUser']) { ?> <a href="./?remove_user=true&register_id=<?= $row['registerID'] ?>" style="color:blue; text-decoration: none;" onclick="return confirm('Prevent\n<?= $row['currentUser'] ?>\nFrom Using\n<?= $row['registerName'] ?> <?= $row['registerCode'] ?> Register?');" title="Click to remove this user"> <?= $row['currentUser'] ?></a><?php } ?></td>
										<td style='width:6%;' class='link' title='Click to Toggle' onclick='if(confirm("Change From <?= $row['status']?"Active":"Archived" ?> Register To  <?= $row['status']?"Archived":"Active" ?> Register For <?= $row['registerName'] ?>")){ window.location="./?st=update&mdid=<?= $row['registerID'] ?>&status=<?= !$row['status'] ?>"; }'><?= ($row['status'] == '1')? "Active" : "Archived" ?></td>
										
									</tr>
									<?php			
									$row_++;
								}
								
								?>
						</tbody>
					</table>
				</div>
				</td>
				<td style=''>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				</tr>
			</table>
		</div>
	</div>

<?php
  include_once "../footer.html";
  ?> 
<script type="text/javascript">
	$(document).ready(function(){
		$("#test_focus").focus();
		//$("#test_focus").focus(function(){
			setTimeout(
				function(e){
					$("#past_data").html($("#test_focus").val());
				},2);
		//});
		//alert();
		//change the copy table width
		setTimeout(function(){
			$("#copy").css("width:" + $("#tbl_ref").css("width") );
			//alert($("#copy").css("width"));
		},500);
		$("#filter_check").scroll(function(){
			//now track the top position of the header of the table
			$("#th").css("position:absolute");
			$("#th").css("top:0px");
			$("#th").css("left:0px");
			console.log($("#th").css("top:relative"));
		});
		$('#md_name').keyup(function(){
			var value = $(this).val();
			$.post('./ajaxfile.php',
				{
					name : value
				},
				function(data){
					if (data != 0) {
						$('#other-side').html(data);
					}
				}
			);
			/*var output = $("#unitP").attr('value',function(){
				return value;
			});*/
		});

		

		$("#unitP").keyup(function(){
			var unit = $.trim($("#unitP").val());
			var psg = (unit * 20) / 100;
			psg += parseFloat(unit);
			$("#price").attr("value",function(){
				if(unit != "" || unit.lenght > 0 ) return psg;
				else return "";
			});
		});
/* 
		$("#save_btn").click(function(){
			var cat = $("#md_cat").val();
			var name = $.trim($("#md_name").val());
			var unit = $.trim($("#unitP").val());
			if (name == "" ) alert('PLease select Name.');
			else if (cat == "" ) alert('PLease select Category.');
			else if (unit == "" ) alert('PLease select Unit Price.');
			else{
				$("#medecienId").submit();
			};
		}); */

		$("#update_btn").click(function(){
			var cat = $("#md_cat").val();
			var name = $.trim($("#md_name").val());
			var unit = $.trim($("#unitP").val());
			if (name == "" ) alert('PLease select Name.');
			else if (cat == "" ) alert('PLease select Category.');
			else if (unit == "" ) alert('PLease select Unit Price.');
			else{
				$("#medecienId").submit();
			};
		});
		

	});
</script>

</body>
</html>
