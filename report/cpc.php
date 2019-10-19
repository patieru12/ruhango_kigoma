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
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
require_once "../lib2/cssmenu/rcp_header.html";
?>  
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Prescription Summary Report</h1>
      <b>
	  		
		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
			   style="display: none;">
			<tr>
				<td id="ds_calclass"></td>
			</tr></table>
	<!--
	  <?php $i=0; foreach($insurance as $in){ echo "<label><input type=radio onclick='if($(\"#in{$in['InsuranceNameID']}\").prop(\"checked\")){ findByInsurance(\"{$in['InsuranceNameID']}\") }' id='in{$in['InsuranceNameID']}' name='insurance_' ".(@$_POST['insurance'] == $in['InsuranceNameID']?"selected":"").">{$in['InsuranceName']}</label>"; } ?>
	  <label><input type=radio name=insurance_ value='-1'>All</label><br />-->
	  <input type=hidden name=insurance value='' id=insurance />
	  <input type=hidden name=post_ value='_1' id=post />
	  <form action="generate_prescription_report.php" method="post" id="cpc_report">
	  <table class=list-1>
	  <tr><!--
	  <td>
	  <label><input type=checkbox onclick='if($("#post_sovu").prop("checked")){$("#post").val($("#post").val() + "_1")} else{ $("#post").val($("#post").val().replace("_1","")) }' id=post_sovu name=post1 value=1>Busoro</label> 
	  <label><input type=checkbox onclick='if($("#post_rukira").prop("checked")){$("#post").val($("#post").val() + "_2")} else{ $("#post").val($("#post").val().replace("_2","")) }' name=post2 id=post_rukira value=2>Post</label>
	  </td>-->
	  <td>
		<label><input onclick='if($("#conf_diag").prop("checked")){ if($(".view_diag_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-diag").show(1000); $(".view_diag_config").html("hide") } } else{ $("#element-diag").hide(1000); $(".view_diag_config").html("view"); } ' type=checkbox name='conf_diag' id=conf_diag>Diagnostic</label><br /><a href='#' onclick='if($(".view_diag_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-diag").show(1000); $(".view_diag_config").html("hide"); } else { $("#element-diag").hide(1000); $(".view_diag_config").html("view");}' class='view_diag_config view_config_panel' id='view_config_panel'>view</a>
		<input type=hidden name=diagnostic id="custom-diag" value='all' />
	  </td>
	  <td>
		<label><input onclick='if($("#conf_exam").prop("checked")){ if($(".view_exam_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-exam").show(1000); $(".view_exam_config").html("hide") } } else{ $("#element-exam").hide(1000); $(".view_exam_config").html("view");}' type=checkbox name=conf_exam id=conf_exam>Exam</label><br /><a href='#' onclick='if($(".view_exam_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-exam").show(1000); $(".view_exam_config").html("hide"); } else { $("#element-exam").hide(1000); $(".view_exam_config").html("view");}' class='view_exam_config view_config_panel' id='view_config_panel'>view</a>
		<br />
		<input type=hidden name=exam id="custom-exam" value='all' />
	  </td>
	  <td>
		<label><input onclick='if($("#conf_medicines").prop("checked")){ if($(".view_medicines_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-medicines").show(1000); $(".view_medicines_config").html("hide") } } else{ $("#element-medicines").hide(1000); $(".view_medicines_config").html("view");}' type=checkbox name=conf_medicines id=conf_medicines>Medicines</label><br /><a href='#' onclick='if($(".view_medicines_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-medicines").show(1000); $(".view_medicines_config").html("hide"); } else { $("#element-medicines").hide(1000); $(".view_medicines_config").html("view");}' class='view_medicines_config view_config_panel' id='view_config_panel'>view</a>
		<br />
		<input type=hidden name=medicines id="custom-medicines" value='all' />
		
	  </td>
	  <td>
		<label><input onclick='if($("#conf_acts").prop("checked")){ if($(".view_acts_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-acts").show(1000); $(".view_acts_config").html("hide") } } else{ $("#element-acts").hide(1000); $(".view_acts_config").html("view");}' type=checkbox name=conf_acts id=conf_acts>Acts</label><br /><a href='#' onclick='if($(".view_acts_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-acts").show(1000); $(".view_acts_config").html("hide"); } else { $("#element-acts").hide(1000); $(".view_acts_config").html("view");}' class='view_acts_config view_config_panel' id='view_config_panel'>view</a>
		<br />
		<input type=hidden name=acts id="custom-acts" value='all' />
		
	  </td>
	  <td>
		<label><input onclick='if($("#conf_consumable").prop("checked")){ if($(".view_consumable_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-consumable").show(1000); $(".view_consumable_config").html("hide") } } else{ $("#element-consumable").hide(1000); $(".view_consumable_config").html("view");}' type=checkbox name=conf_consumable id=conf_consumable>Consumable</label><br /><a href='#' onclick='if($(".view_consumable_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-consumable").show(1000); $(".view_consumable_config").html("hide"); } else { $("#element-consumable").hide(1000); $(".view_consumable_config").html("view");}' class='view_consumable_config view_config_panel' id='view_config_panel'>view</a>
		<input type=hidden name=consumable id="custom-consumable" value='all' />
		<br />
	  </td>
	  <td>
		<label><input onclick='if($("#conf_transfer").prop("checked")){ if($(".view_transfer_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-transfer").show(1000); $(".view_transfer_config").html("hide") } } else{ $("#element-transfer").hide(1000); $(".view_transfer_config").html("view");}' type=checkbox name=conf_transfer id=conf_transfer>Transfer</label><br /><a href='#' onclick='if($(".view_transfer_config").html() == "view"){ $(".config-data").hide(); $(".view_config_panel").html("view"); $("#element-transfer").show(1000); $(".view_transfer_config").html("hide"); } else { $("#element-transfer").hide(1000); $(".view_transfer_config").html("view");}' class='view_transfer_config view_config_panel' id='view_config_panel'>view</a><br />
		
		<input type=hidden name=transfer id="custom-transfer" value='all' style='width:80px;' />
	  </td><td>
	  <input type=text name=start_date class=txtfield1 style='width:100px;' id=start_date onclick='ds_sh(this,"start_date")' value='<?= date("Y-m-d",time()) ?>' />
	  
	  </td><td>
	  <input type=text  name=end_date class=txtfield1 style='width:100px;' id=end_date onclick='ds_sh(this,"end_date")' value='<?= date("Y-m-d",time()) ?>' />
	  <input type=submit name=generate id=generate value='View' class=flatbtn-blu style='font-size:12px;' />
	  
	  </td>
	  </tr>
	  </table>
	  </form>
	  </b>
	  
	  <h1 style=''>CPC Daily Report</h1>
      <b>
	  <table style='border:0px solid #000;' class=list-1><tr><td>
	 <div id="ds" style='min-width:100px'></div></td></tr></table>
	  <?php echo $error; ?>
	  <!-- Divisions to view select element -->
	  <div class='config-data' id='element-diag'></div>
	  <div class='config-data' id='element-exam'></div>
	  <div class='config-data' id='element-medicines'></div>
	  <div class='config-data' id='element-acts'></div>
	  <div class='config-data' id='element-consumable'></div>
	  <div class='config-data' id='element-transfer'>
			<label title='Check this to preview all Transfer'><input onclick='if($("#_transfer-all").prop("checked")){ $("#custom-transfer").val("all"); $("._custom-transfer-data").removeProp("checked"); } else { $("#custom-transfer").val(""); $("._custom-transfer-data").removeProp("checked"); }' checked type=checkbox id='_transfer-all' value='all'>All</label><br />
			<label title='View in Service' ><input class='_custom-transfer-data' onclick='if($(this).prop("checked")){ if($("#custom-transfer").val() == "all"){ $("#custom-transfer").val(""); } $("#_transfer-all").removeProp("checked"); $("#custom-transfer").val($("#custom-transfer").val() + "0;"); } else{ $("#custom-transfer").val($("#custom-transfer").val().replace(/0;/g,"")); }' type=checkbox value='0'  />In Service</label><br />
			<label title='View in Service' ><input class='_custom-transfer-data' onclick='if($(this).prop("checked")){ if($("#custom-transfer").val() == "all"){ $("#custom-transfer").val(""); } $("#_transfer-all").removeProp("checked"); $("#custom-transfer").val($("#custom-transfer").val() + "1;"); } else{ $("#custom-transfer").val($("#custom-transfer").val().replace(/1;/g,"")); }' type=checkbox value='1'  />No Transfer</label><br />
			<label title='View in Service' ><input class='_custom-transfer-data' onclick='if($(this).prop("checked")){ if($("#custom-transfer").val() == "all"){ $("#custom-transfer").val(""); } $("#_transfer-all").removeProp("checked"); $("#custom-transfer").val($("#custom-transfer").val() + "2;"); } else{ $("#custom-transfer").val($("#custom-transfer").val().replace(/2;/g,"")); }' type=checkbox value='2'  />Transfer</label><br />
			<label title='View in Service' ><input class='_custom-transfer-data' onclick='if($(this).prop("checked")){ if($("#custom-transfer").val() == "all"){ $("#custom-transfer").val(""); } $("#_transfer-all").removeProp("checked"); $("#custom-transfer").val($("#custom-transfer").val() + "3;"); } else{ $("#custom-transfer").val($("#custom-transfer").val().replace(/3;/g,"")); }' type=checkbox value='3'  />Ambulance</label><br />
		</select>
	  </div>
	  <!-- END OF Divisions to view select element -->
	  
	  <div class="patient_found" style='height:80%; overflow:auto;'>
		<span class=error-text></span>
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
	//load all diagnostic now in the div now and wait for filter
	$("#element-diag").load("./sp/diag.php");
	$("#element-exam").load("./sp/exam.php");
	$("#element-medicines").load("./sp/medicines.php");
	$("#element-acts").load("./sp/acts.php");
	$("#element-consumable").load("./sp/consummable.php");
	
	//hide all config-data division
	$(".config-data").hide();
	$("#generate").click(function(e){
		if(!$("#start_date").val().trim()){
			return e.preventDefault();
		}
		if(!$("#end_date").val().trim()){
			return e.preventDefault();
		}
		if($("#end_date").val().trim() == $("#start_date").val().trim()){
			if(!confirm("Generate Prescription Report For 1 " + $("#end_date").val() + " Only?"))
				return e.preventDefault();
		}
		if($("#end_date").val().trim() < $("#start_date").val().trim()){
			alert("Date Error Please Start should be Less than End!");
				return e.preventDefault();
		}
		$(".patient_found").html('');
		$(".patient_found").html('<img src="../images/loading.gif" alt="Searching"/>'); 
		$("#cpc_report").ajaxForm({ 
			target: '.patient_found'
		}).submit(); 
	
		return e.preventDefault();
	});
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