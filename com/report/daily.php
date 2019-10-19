<?php
session_start();
//var_dump($_SESSION);
require_once "../../lib/db_function.php";
if("com" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";

//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceName='CBHI' ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$centers = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT sy_center.* from sy_center ORDER BY Level ASC, CenterName ASC",$con),$multirows=true,$con);

$active = "report";
$systemPath = "../../";
$modulePath = "../";
require_once "../../lib2/cssmenu/com_header.html";
?>
  <script type="text/javascript">
  function save_request( tbl, fld, ref_val,ref_field){
	$.ajax({
		type: "POST",
		url: "./save_file_change_request.php",
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val() + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=ajax",
		cache: false,
		success: function(result){
			$("#generate").click();
			$("#edit_mode").val("0");
			$(".update_result").html(result);
		}
	});
  }
  function edit_function(cl,ex_val,tbl,ref_val,ref_field, fld, app='', file=''){
	  
	  $("." + cl).html("<input id=focus_now class='fld_txt' onclick='' onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\");' type=text value='" + ex_val + "' />");
	  
	  $("#focus_now").focus();
	  $("#edit_mode").val("1");
	  
  }
  </script>
  
  <style>
	.fld_txt{
		width:80%;
		border:1px solid #00a;
	}
  </style>
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Daily Reception Report Summary</h1>
      <b>
	  		<input type=hidden value=0 id=edit_mode />
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <input type=hidden name=insurance value='<?= $insurance[0]['InsuranceNameID'] ?>' id=insurance />
	  <input type=hidden name=post_ value='_<?= $_SESSION['user']['CenterID']; ?>' id=post />
	  <table class=list-1><tr><td>Post</td><td>Year</td><td>Month</td><td>Day</td></tr>
	  <tr>
	  <td>
	  <?php
		foreach($centers as $c){
			?>
			<label><input type=checkbox <?= $_SESSION['user']['CenterID'] == $c['CenterID']?"checked":"" ?> onclick='if($("#post_<?= strtolower($c['CenterName']) ?>").prop("checked")){$("#post").val($("#post").val() + "_<?= strtolower($c['CenterID']) ?>")} else{ $("#post").val($("#post").val().replace("_<?= strtolower($c['CenterID']) ?>","")) }' id='post_<?= strtolower($c['CenterName']) ?>' value='<?= strtolower($c['CenterID']) ?>'><?= $c['CenterName'] ?></label> 
			<?php
		}
		?>
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
		//$month = array(1=>"January","Febuary","March","April","May","June","July","August","September","October","November","December");
		for($m = 1; $m <= 12;$m++){
			echo "<option value='".($m < 10?"0".$m:$m)."' ".(date("m",time()) == $m?"selected":"").">{$month[$m]}</option>";
		}
		?>
	  </select>
	  </td>
	  <td class=day>
	  <select name=year class=txtfield1 style='width:70px;' id=year>
		<?php
		for($y = date("Y",time()); $y>="2015";$y--){
			echo "<option>{$y}</option>";
		}
		?>
	  </select>
	  </td><!--<td>
	  <input type=text class=txtfield1 style='width:250px;' id=medecines value='ALL' />
	  
	  </td>--><td>
	  <input type=button class=flatbtn-blu style='padding:0 10px;' id=generate value='Generate' />
	  </td></tr>
	  </table>
	  <table class=list-1><tr><td>
	 <div id="ds" style='min-width:100px'></div></td></tr></table>
	  <?php echo $error; ?>
	  <span class=update_result></span>
	  <input type=hidden id=filter_ />
	  <div class="patient_found" style='height:92%; border:0px solid #000;'>
		<span class=error-text>Select Post to view Daily Records</span>
	  </div>
	  <div class="doc_selected">
	  
	  </div>
	  </b>
    </div>
	
  </div>
  <?php
  include_once "../../footer.html";
  ?>
  
	<div class="apple_overlay" id="overlay">
	  <!-- the external content is loaded inside this tag -->
	  <div class="contentWrap"></div>
	</div>
  <!-- make all links with the 'rel' attribute open overlays -->
<script>

$(document).ready(function(){
	$(".day").load("load_day.php?year=" + $("#year").val() + "&month=" + $("#month").val());
	$("#year").change(function(e){
		$(".day").load("load_day.php?year=" + $("#year").val() + "&month=" + $("#month").val());
		//then load the data automatically after 800 milliseconds
		/* setTimeout(function(){
			$("#generate").click();
		},800); */
	});
	$("#month").change(function(e){
		$(".day").load("load_day.php?year=" + $("#year").val() + "&month=" + $("#month").val());
		//then load the data automatically after 800 milliseconds
		/* setTimeout(function(){
			$("#generate").click();
		},800); */
	});
	
	$("#generate").click(function(e){
		$("#ds").html("");
		$("#ds").removeClass("error");
		if(!$("#insurance").val()){
			$("#ds").addClass("error");
			$("#ds").html("Select Insurance");
			return e.preventDefault();
		}
		$(".patient_found").html("Please Wait...<br /><img src='../../images/loading.gif' />");
		$(".patient_found").load("daily_data.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val());
	});
	
});
</script>
</body>
</html>