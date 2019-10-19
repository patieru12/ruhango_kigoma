<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";

//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceName='MMI' ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$active = "report";
require_once "../lib2/cssmenu/rcp_header.html";
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
      <h1 style='' class="title_bar">Document List MMI</h1>
      <b>
	  		<input type=hidden value=0 id=edit_mode />
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <input type=hidden name=insurance value='<?= $insurance[0]['InsuranceNameID'] ?>' id=insurance />
	  <input type=hidden name=post_ value='_1' id=post />
	  <table class=list-1><tr><td>Post</td><td>Year</td><td>Month</td><td>Day</td></tr>
	  <tr>
	  <td>
	  <label><input type=checkbox checked onclick='if($("#post_sovu").prop("checked")){$("#post").val($("#post").val() + "_1")} else{ $("#post").val($("#post").val().replace("_1","")) }' id=post_sovu name=post value=1>Busoro</label> 
	  <label><input type=checkbox onclick='if($("#post_rukira").prop("checked")){$("#post").val($("#post").val() + "_2")} else{ $("#post").val($("#post").val().replace("_2","")) }' name=post id=post_rukira value=2>Post</label></td>
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
			echo "<option value='".($m<10?"0".$m:$m)."' ".((@$_GET['month']?$_GET['month']:date("m",time())) == $m?"selected":"").">{$month[$m]}</option>";
		}
		?>
	  </select>
	  </td>
	  <td class=day>
	  
	  </td><td><!--
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
	  <div class="patient_found" style='height:93%; overflow:auto;'>
		<span class=error-text>Select Post to view CBHI Patient History</span>
	  </div>
	  <div class="doc_selected">
	  
	  </div>
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
function receivePatient(id,ins=""){
	$(".patient_selected").load("receive_patient.php?key=" + id + "&ins=" + ins);
}

function findByInsurance(ins){
	//$(".patient_selected").html("");
	//$(".patient_found").load("search_patient.php?key=" + ins);
	$("#insurance").val(ins);
	
}
$(document).ready(function(){
	$(".day").load("load_day.php?year=" + $("#year").val() + "&month=" + $("#month").val() + "&day=<?= @$_GET['day'] ?>");
	/* $("#year").change(function(e){
		$(".day").load("load_day.php?year=" + $("#year").val() + "&month=" + $("#month").val());
	
	});
	$("#month").change(function(e){
		$(".day").load("load_day.php?year=" + $("#year").val() + "&month=" + $("#month").val());
	
	});
	 */
	$("#generate").click(function(e){
		$("#ds").html("");
		$("#ds").removeClass("error");
		if(!$("#insurance").val()){
			$("#ds").addClass("error");
			$("#ds").html("Select Insurance");
			return e.preventDefault();
		}
		$(".patient_found").load("document_list_mmi_sp.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val());
	});
	setTimeout(function(){
		$("#generate").click();
	},500);
	//if the search button is clicked search the patient_found
	$("#search").click(function(e){
		$(".doc_selected").html("");
		$(".doc_found").load("doc_patient.php?key="+$("#doc_search").val());
		return e.preventDefault();
	});
	$("#insurance").change(function(e){
		$(".patient_selected").html("");
		$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
	});
	$("#doc_search").keyup(function(e){
		$(".doc_selected").html("");
		$(".doc_found").load("doc_patient.php?key="+$("#doc_search").val());
		return e.preventDefault();
	});
});

$(function() {

    // if the function argument is given to overlay,
    // it is assumed to be the onBeforeLoad event listener
    $("a[rel]").overlay({

        mask: '#206095',
        effect: 'apple',
        onBeforeLoad: function() {

            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");

            // load the page specified in the trigger
            wrap.load(this.getTrigger().attr("href"));
        }

    });
});
</script>

<script type="text/javascript">
$(function(){

	$("#username").keypress(function(e){
		$("#username").removeClass("error");
	});
	$("#password").keypress(function(e){
		$("#password").removeClass("error");
	});
  $('#loginform').submit(function(e){
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
		url: "./login.php",
		data: "username=" + $("#username").val() + "&password=" + $("#password").val() + "&url=ajax",
		cache: false,
		success: function(result){
			$(".login_result").html(result);
		}
	});
    return e.preventDefault();
	
  });
  
  $('body').mousedown(function(e) {
	var clicked = $(e.target); // get the element clicked
	if (clicked.is('#overlay') || clicked.parents().is('#overlay')) {
		return; // click happened within the dialog, do nothing here
   } else { // click was outside the dialog, so close it
     //$('.overlay').hide();
	 //return false;
   }
});
});
</script>
<?php
if(@$_POST['rcv_patient']){
	?>
	<script>
		receivePatient("<?php echo @$_POST['patientid'] ?>","<?php echo @$_POST['insurance'] ?>");
	</script>
	<?php
}
?>
</body>
</html>