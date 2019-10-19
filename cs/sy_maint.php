<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}

	
	//var_dump($_POST); //die;
	$error = ""; $current = 0;
	if(@$_GET['action'] == 'copy' && $_GET['date'] <= date("Y-m-d",time())){
		//copy the tarif to the current date
		$tarif = returnAllData($sql="SELECT DISTINCT Date FROM md_price WHERE Date < '".(PDB($_GET['date'],true,$con))."' ORDER BY Date DESC LIMIT 0, 1",$con);
		//var_dump($tarif);
		//select all component of the selected tarif now
		$tarifs = returnAllData($sql="SELECT DISTINCT * FROM md_price WHERE Date = '{$tarif[0]['Date']}'",$con);
		//var_dump($tarifs);
		
		for($i=0;$i<count($tarifs);$i++){
			//save_new tarif if not exist
			if(!$act_price = returnSingleField("SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$tarifs[$i]['MedecineNameID']}' && Date='".(PDB($_GET['date'],true,$con))."'","MedecinePriceID",$data=true, $con)){
				//save the new price now
				saveData("INSERT INTO md_price SET  MedecineNameID='{$tarifs[$i]['MedecineNameID']}', BuyingPrice='{$tarifs[$i]['BuyingPrice']}', Amount='{$tarifs[$i]['Amount']}', Date='".(PDB($_GET['date'],true,$con))."', Status=1, Emballage='".returnSingleField("SELECT Emballage FROM md_name WHERE MedecineNameID='{$tarifs[$i]['MedecineNameID']}'","Emballage",true, $con)."'",$con);
			} else{
				//update the record is exists before
				saveData("UPDATE md_price SET BuyingPrice='{$tarifs[$i]['BuyingPrice']}', Amount='{$tarifs[$i]['Amount']}', Emballage='".returnSingleField("SELECT Emballage FROM md_name WHERE MedecineNameID='{$tarifs[$i]['MedecineNameID']}'","Emballage",true, $con)."' WHERE MedecinePriceID='{$act_price}'",$con);
			}
		}
		
	}
	
	if(@$_GET['emb'] == "update" && is_numeric($_GET['mdid'])){
		$current = $_GET['mdid'];
		saveData("UPDATE md_price SET Emballage='".(PDB($_GET['emballage'],true,$con))."' WHERE MedecinePriceID='".PDB($_GET['mdid'],true,$con)."'",$con);
	}
	
	if (isset($_POST['save'])) {
		
		//var_dump($_POST); die;
		$name = mysql_real_escape_string(trim($_POST['medecienId']));
		$price_b  = mysql_real_escape_string(trim($_POST['BuyingPrice']));
		$sell_price  = $price_b + round($price_b*0.2,1);
		$date  = mysql_real_escape_string(trim($_POST['date']));
		//var_dump($name); die;
		if(returnSingleField($sql="SELECT MedecinePriceID FROM md_price WHERE MedecineNameID='{$name}' && BuyingPrice='{$price_b}' && Status=1",$field="MedecinePriceID",$data=true, $con)){
			$error = "<span class=error-text>The Existing Price is Still Active</span>";
		} else{
			//update the existing status
			saveData("UPDATE md_price SET Status=0 WHERE MedecineNameID='{$name}' && Status=1",$con);
			//save new data
			if(saveData($sql="INSERT INTO md_price SET  MedecineNameID='{$name}', BuyingPrice='{$price_b}', Amount='{$sell_price}', Date='{$date}', Status=1",$con)){
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
	<div id='w' style='height: 87%;'>
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
		<div id="content" style='height: 100%;'>
	<h1 style='margin-top:-55px'>System Backup</h1>
		<b>
		<?= $error ?>
		<table border=0 style='width:100%'>
			<tr>
				<td style='width:32%'>
					<span class=update_result></span>
					Backup History<br />
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
					<div style='height:100%; overflow:auto;'>
					<?php
					/////////////scan dir functions are going to be implemented here!!!
				
				//start scan process
				
				// require_the scan directory file
				require_once "../lib2/Scan/Class_ScanDir.php";
				
				// set the dir path
				$path = "./bckup";
				
				// instantiate the class.
				$Dir = new DirScan () ;
				
				// set filter to return only excel formats in the directory
				$Dir->SetFilterExt(array("xls","xlsx")) ;
				
				// enable filter
				$Dir->SetFilterEnable(true);
				
				// list all file extension disabled
				$Dir->SetFileExtListEnable(false);
				
				// enable scan sub directories
				$Dir->SetScanSubDirs(true);
				
				// enable Files Scanning
				$Dir->SetScanFiles(true);
				
				// enable full details
				$Dir->SetFullDetails(true);
				
				// run the scan
				$Dir->ScanDir($path,false);
				
				// display all the file found during scanning
				if(count($Dir->TabFiles) >0){
					$out = "<table border=1 style='border-collapse:collapse; padding:5px; font-size:12px;'><tr><th>Type</th><th>Date</th><th>Size</th></tr>"; $count=0;
					arsort($Dir->TabFiles);
					foreach ($Dir->TabFiles as $f) {
						$time = explode(".",$f['basename'])[0];
						//$time = explode(".",$time_part)[0];
						//var_dump($time);
						//if(preg_match("/^".preg_replace("/ /","_",trim($db->select1cell("school_report_db`.`tbl_users","Name",array("ID"=>$_SESSION['u_id']),true)))."/",$f['filename']))
							$out .= "<tr><td>Normal BackUp</td><td><a style='color:blue; text-decoration:none;' target='_blank' href='./".$f['dirname']."/".$f['filename']."'>".$time."</a></td><td>".round($f['size']*(1/1024),1)." KB</td><!--<td align=right>".date('Y-m-d h:i:s',$f['datemodify'])."</td>--></tr>";
							//echo "<pre>";//.$f["filename"]."<br>";
							//	   print_r($f);
					}
					$out .= "</table>";
					echo $out;
				}
				
				//end scan process
					?>
					</div>
				</td>
				<td>
				<center>Backup Configuration</center>
				<div style='border:0px solid #000;padding-top: 10px;'>
					<table class=list-1><tr><td>Year</td><td>Month</td></tr>
					  <tr>
						<td>
							  <select name=year class=txtfield1 style='width:70px;' id=year>
								<?php
								for($y = date("Y",time()); $y>="2015";$y--){
									echo "<option>{$y}</option>";
								}
								?>
							  </select>
							  </td><td>
						  <select name=month class=txtfield1 style='width:140px;' id=month>
							<?php
							//$month = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
							for($m = 1; $m <= 12;$m++){
								echo "<option value='".($m<10?"0".$m:$m)."' ".(date("m",time()) == $m?"selected":"").">{$month[$m]}</option>";
							}
							?>
						  </select>
						  </td><!--<td>
						  <input type=text class=txtfield1 style='width:250px;' id=medecines value='ALL' />
						  
						  </td>--><td>
						  </td></tr>
					</table>
					<input type=button class=flatbtn-blu style='padding:0 10px;' id=generate value='Start Backup' />
						  
					<div class=bckup_process></div>
				</div>
			</td>
			</tr>
		</table>
		</b>
		</div>
	</div>
<?php
  include_once "../footer.html";
  ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#test_focus").focus();
			setTimeout(
				function(e){
					$("#past_data").html($("#test_focus").val());
				},2);
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

		$("#generate").click(function(){
			$(".bckup_process").html("<img src='../images/loading.gif' />");
			//now post data for processing back system
			$.post(
				"./backupnow.php",
				{
					"date": $("#year").val() + "-" + $("#month").val()
				},
				function(data){
					$(".bckup_process").html(data);
				}
			);
		});
		

	});
</script>

</body>
</html>