<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("dm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
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
require_once "../lib2/cssmenu/dm_header.html";

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
	  <?php echo $error; 
	  /* <div id=upload_out></div>
	  <form method=post action="rcv_patient.php" id='rcv_patient_frm'>
	   <table border=0 class="a" style='background-color:#fff;'>
		
		<tr><td>Card ID:<span class=error-text>*</span></td><td>Name:<span class=error-text>*</span></td><td>Age [<label class=as_link>yyyy-mm-dd or yyyy</label>]:</td><td>Sex<span class=error-text>*</span><!--<td>Family Chief ID Card:</td><td>Family Position:</td>-->
		</tr>
		<tr>
			<td><input id="patient_search" autocomplete="off" type=text name='card_id' class='txtfield1' style='' /></td>
			<td><input type=text name='name' autocomplete="off" onkeyup='if(familyChief()){$("#father").val($("#name").val());}' id=name class='txtfield1' style='' /></td>
			<td><input type=text name='age' autocomplete="off" onblur='setTimeout("ds_hi()",1000)' id=age class='txtfield1' onclick="ds_sh(this,'age')" style='' /></td>
			<td><label><input type=radio name=sex value="Female" required id=female>Female</label> <label><input type=radio name=sex value="Male" required id=male>Male</label></td>
			
		</tr>
		<tr><td class=h_label>House Manager:<label class=as_link onclick='$("#father").val($("#name").val())'>Self</label><span class=error-text>*</span></td></td><td class=fcategory>Category<span class=error-text>*</span></td></td><td class=affection_location></td><td class=sex></td>
		</tr>
		<tr>
			
			<td><input type=text name='father' autocomplete="off" id=father class='txtfield1' style='' /></td>
			<td class=fcategorydata><input autocomplete="off" type=text name=fcategory id=fcategory value='' class=txtfield1 /></td>
			<td class=affection_locationdata><!--<input type=text name=fcategory id=fcategory value=3 class=txtfield1 />--></td>
			<td class=sexdata><!--<input type=text name=fcategory id=fcategory value=3 class=txtfield1 />--></td>
		</tr>
		<tr>
			<td>Village</td><td>Cell</td><td>Sector</td><td>District</td>
		</tr>
		<tr>
			<td><input type=text name='village' autocomplete="off" id=village class="txtfield1" style='' /></td>
			<td><input type=text name='cell' autocomplete="off" id=cell class="txtfield1" style='' /></td>
			<td><input type=text name='sector' autocomplete="off" id=sector class="txtfield1" style='' /></td>
			<td><input type=text name='district' autocomplete="off" id=district class="txtfield1" style='' /></td>
		</tr>
		<tr>
			<td colspan=4><div class='address_search' style='max-height:150px; overflow:auto;'></div></td>
		</tr>
		<tr>
			<td colspan=3>Service<span class=error-text>*</span> <br />
			<?php 
			if($service){
				$i=0; 
				foreach($service as $in){ echo "<label><input type=radio name='service' ".($in['ServiceCode'] == "CPC"?"checked":"")." value='{$in['ServiceNameID']}' id='{$in['ServiceCode']}'>{$in['ServiceCode']}</label>"; } 
			} else{
				echo "<span class=error-text>No Service Available</span>";
			}
			?></td>
		</tr>
		<tr>
			<td>Weight</td><td>Temperature</td>
		</tr>
		<tr>
			<td><input type=text name='weight' autocomplete="off" id='weight' class='txtfield1' style='' /></td>
			<td><input type=text name='temperature' autocomplete="off" id='temperature' class='txtfield1' style='' /></td>
			<td class=tm_view><label><input type=radio id='paid' name='tm' value='200' />TM Paid</label> <label><input type=radio id='not_paid' name='tm' value="0" />TM Not Paid</label></td>
		</tr>
		<tr><td colspan=4 align=center><label id='steps'></label><input type=hidden name=next_frequency id=next_frequency value='1' /><input type=hidden name=document_list id=document_list value='' /><label><input type=checkbox id=update disabled> Update</label>
		<input type=submit id=save class="flatbtn-blu" name=rcv_patient value='Save' /> <input type=reset class="flatbtn-blu" onclick='window.location="./create.php";' value='Clear' /></td></tr>
	  </table>
	  <input type=hidden id=update_ name=update value='' />
	  </form> */
	  
		//select and display all data about the entered user
		//var_dump($_SESSION);
		
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