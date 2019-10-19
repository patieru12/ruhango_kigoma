<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cst" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST); die();
if(isset($_POST['service']) && is_numeric($_POST['service'])){
	$_SESSION['user']['ServiceID'] = $_POST['service'];
	$foundRegister = [];
	foreach($_POST AS $key=>$value){
		// $registerId = preg_replace("/^register_/", "", $key);
		// var_dump($key, "==", $value, $registerId == $value, preg_match("/^register_\d$/", $key));
		// echo "<hr />";
		if(preg_replace("/^register_/", "", $key) == $value){
			$foundRegister[] = $value;
		}
	}
	// var_dump($foundRegister, $_POST); die();
	// Here Update the Register information fromhere
	$registerID = implode(", ", $foundRegister);
	saveData("UPDATE sy_register SET consultantId='{$_SESSION['user']['UserID']}' WHERE id IN({$registerID})",$con);
	// var_dump($_POST['service']);
	$serviceId = PDB($_POST['service'], true, $con);
	$serviceCode = returnSingleField("SELECT ServiceCode FROM se_name WHERE ServiceNameID='{$serviceId}'","ServiceCode", true, $con);
	if(in_array($serviceCode, array("PF","MAT") ) ){
		/*header("Location:../pf/");
		exit;*/
		$_SESSION["user"]["special"] = $serviceCode;
	}
	header("Location:./");
	exit();
}
$error = "";
//die;
if(@$_GET['msg']){
	$error = "<span class=error>".$_GET['msg']."</span>";
}

//var_dump($_SESSION);
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceName='CBHI' && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$insurance_all = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
// var_dump($service);
$register = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT sy_register.* FROM sy_register WHERE sy_register.status=1 && (sy_register.consultantId IS NULL || sy_register.consultantId = '{$_SESSION['user']['UserID']}') ORDER BY name ASC",$con),$multirows=true,$con);
// var_dump($service);
// echo $sql;
$active = "patient";

require_once "../lib2/cssmenu/cst_header.html";
$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name WHERE se_name.Status=1 ORDER BY ServiceCode ASC",$con),$multirows=true,$con);

?>

<script>
  var hide_calendar = false;
</script>
  <div id="w">
    <div id="content" style='text-align:left;'><!--
      <h1>WELCOME TO CARE SOFTWARE</h1>-->
	  
	  <style>
		.a td{
			border:0px solid #000; padding:2px;
		}
		
		.patient_found{
			max-height:200px;
			overflow:auto;
		}
	  </style>
	  <b>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <?php echo $error; ?>
	  <div id=upload_out></div>
	  <h1>Select Current service on <?= date("D F Y", time()) ?></h1>
	  <form method=post action="se_select.php" id=''>
	  
	  <table border=0 style='background-color:#fff;'>
		
		<tr>
			<td colspan=4><div class='address_search' style='max-height:150px; overflow:auto;'></div></td>
		</tr>
		<tr>
			<td colspan=3 style='padding-bottom:10px;'>Service<span class=error-text>*</span> <br />
			<?php 
			if(count($service) > 0 ){
				$i=0; 
				// var_dump($service);
				foreach($service as $in){
					echo "<label style='padding:0 10px;'><input type=radio name='service' ".($in['ServiceCode'] == "CPC" && null != @$_POST['service']?"checked":(@$_POST['service'] == $in['ServiceNameID'])?"checked":"")."  value='{$in['ServiceNameID']}' onclick='getRegisterSet({$in['ServiceNameID']})' id='{$in['ServiceCode']}'>{$in['ServiceCode']}</label>";
				} 
			} else{
				echo "<span class=error-text>No Service Available</span>";
			}
			?></td>
		</tr>
		<tr>
			<td colspan=3 style='padding-bottom:10px;'>Register<span class=error-text>*</span> <br />
				<div id="registerSet">
					<?php 
					if($service){
						$i=0; 
						// var_dump($service);
						if($register){
							echo "<table>";
							foreach($register as $in){
								if( ($i++) % 5 == 0){
									if($i > 1){
										echo "</tr>";
									}
									echo "<tr>";
								}
								echo "<td><label title='{$in['name']}' style='padding:0 10px;'><input type=checkbox name='register_{$in['id']}' ".($in['consultantId'] == $_SESSION['user']['UserID']?"checked":"")."  value='{$in['id']}' id='{$in['registerCode']}'>{$in['name']}: {$in['registerCode']}</label></td>";
								
							} 
							echo "</tr></table>";
						}
					} else{
						echo "<span class=error-text>No Register Available</span>";
					}
					?>
				</div>
			</td>
		</tr>
		<tr><td colspan=4 align=center>
		<input type=submit id=save class="flatbtn-blu" name=rcv_patient value='Save' /> <input type=reset class="flatbtn-blu" id='reset_form' value='Clear' /></td></tr>
	  </table>
	  </form>
	  </b>
  </div>
 </div> 
  <?php
  include_once "../footer.html";
  ?>
</body>
</html>
<script type="text/javascript">
	function getRegisterSet(serviceId){
		$("#registerSet").html("Refreshing the list <br />Please Wait.....");
		$.ajax({
			type: "POST",
			url: "./registerByService.php",
			data: "serviceId=" + serviceId + "&url=ajax",
			cache: false,
			success: function(result){
				$("#registerSet").html(result)
			},
			error: function(err){
				console.log(err.responseText());
			}
		});
	}
</script>