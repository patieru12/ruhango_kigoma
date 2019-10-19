<?php
session_start();
// var_dump($_SESSION);
require_once "../lib/db_function.php";
if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//change to create_cbhi.php

//var_dump($_POST);
$error = "";
//die;
//var_dump($_SESSION);
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category, in_forms, in_price WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceNameID = in_forms.InsuranceNameID && in_name.InsuranceNameID = in_price.InsuranceNameID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$service = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT se_name.* FROM se_name, sy_users WHERE sy_users.UserID = se_name.DirectorID && sy_users.CenterID = '{$_SESSION['user']['CenterID']}' && se_name.Status=1 ORDER BY ServiceName ASC",$con),$multirows=true,$con);
$active = "profile";
//var_dump($service); die;
require_once "../lib2/cssmenu/ph_header.html";

$tm_data = <<<Data
	<label><input type=radio name=tm value=200 />TM Paid</label> <label><input type=radio name='tm' value='0' />TM Not Paid</label>
Data;
?>

  <div id="w">
    <div id="content" style='text-align:left;'>
      <h1>Profile Configuration</h1>
	  
	  <style>
		.a td{
			border:0px solid #000; padding:2px;
		}
		
		.patient_found{
			max-height:200px;
			overflow:auto;
		}
	  </style>
	  <b style='font-size:18px;'>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <?php 
	  	echo $error;
	  ?>
	  <img src='../images/users/photo.png' style='padding:10px; float:left;width:100px; border-radius:20px;' />
	  Name: <?= @$_SESSION['user']['Name'] ?><br />
	  Username: <?= @$_SESSION['user']['Phone'] ?><br />
	  Service: <?= returnSingleField("SELECT PostName FROM sy_post WHERE PostID='".$_SESSION['user']['PostID']."'", "PostName",true,$con); ?></br />
	  Post: <?= returnSingleField("SELECT CenterName FROM sy_center WHERE CenterID='".$_SESSION['user']['CenterID']."'", "CenterName",true,$con); ?><br />
	  Status: <?= $_SESSION['user']['Status']?"Active":"Blocked" ?><br />
	  <br />&nbsp;
			<br /><?php
		if(@$_GET['msg']){
			echo "<span class=success>".$_GET['msg']."</span><br />";
		}
		if(!@$_GET['change'] || !is_numeric($_GET['user_id'])){
			?>
			<a style='text-decoration:none; color:blue' title="Click to change the password!" href='./profile.php?change=true&user_id=<?= $_SESSION['user']['UserID'] ?>'>Change Password?</a>
			<?php
		} else{
			?>
			<form id=pwdform action="./profile.php" method=post >
				Old Password<br />
				<input type=password required id=oldpwd name=oldpwd class=txtfield1 /> <br />
				New Password<br />
				<input type=password required id=pwd name=pwd class=txtfield1 /> <br />
				Re-Type Password<br />
				<input type=password required id=password name=password class=txtfield1 /> <br />
				<input type=hidden required id=username name=username class=txtfield1 value='<?= $_SESSION['user']['Phone'] ?>' /> <br />
				<input type=submit name=save_pwd class=flatbtn-blu style='font-size:12px' value="Save" /> <br />
			</form>
			<div class=login_result></div>
			<?php
			
		}
		 
		 ?>
	  </b>
  </div>
 </div> 
  <?php
  include_once "../footer.html";
  ?>
	<div class="apple_overlay" id="overlay">
	  <!-- the external content is loaded inside this tag -->
	  <div class="contentWrap"></div>
	</div>
  <!-- make all links with the 'rel' attribute open overlays -->
<script>
$(document).ready(function(){
  $('#pwdform').submit(function(e){
	var username = $("#username").val();
	var password = $("#password").val();
	if(username == ""){
		$("#username").addClass("error");
		return e.preventDefault();
	}
	
	if(password == ""){
		$("#password").addClass("error");
		return e.preventDefault();
	}
	//submit the request using JQuery Ajax function
	$.ajax({
		type: "POST",
		url: "../login.php",
		data: "username=" + $("#username").val() + "&oldpwd=" + $("#oldpwd").val() + "&password=" + $("#password").val() + "&password2=" + $("#pwd").val() + "&pwd_update=update&userid=<?= $_SESSION['user']['UserID'] ?>" + "&url=ajax",
		cache: false,
		success: function(result){
			$(".login_result").html(result);
		},
		error:function(err){
			console.log(err.statusText);
			$(".login_result").html(err.responseText);
		}
	});
    return e.preventDefault();
	
  });
});
</script>
<?php
if(@$_POST['rcv_patient']){
	?>
	<script>
		//receivePatient("<?php echo @$_POST['patientid'] ?>","<?php echo @$_POST['insurance'] ?>");
	</script>
	<?php
}
?>
</body>
</html>