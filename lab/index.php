<?php
header("location:./new_result.php");
return;
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";
//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name ORDER BY InsuranceName ASC",$con),$multirows=true,$con);
$active = "index";
require_once"../lib2/cssmenu/lab_header.html" ;
?>
  <script type="text/javascript">
  /* function save_request( tbl, fld, ref_val,ref_field, fl){
	//alert(fl);
	$.ajax({
		type: "POST",
		url: "./" + fl,
		data: "tbl=" + tbl + "&field=" + fld + "&val=" + $("#focus_now").val() + "&ref_field=" + ref_field + "&ref_val=" + ref_val + "&url=ajax",
		cache: false,
		success: function(result){
			$("#save_req").html(result);
			$("#search").click();
		}
	});
  }
  
  function edit_function(cl,ex_val,tbl,ref_val,ref_field, fld, fl){
	  $("." + cl).html("<input id=focus_now onkeypress='isEnterKey(event,\"search\")' onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\",\"" + fl + "\");' class=full type=text value='" + ex_val + "' />");
	  //$("." + cl).html("<input id=focus_now onkeypress='isEnterKey(event,\"search\")' onblur='save_request(\""+ tbl +"\",\""+ fld +"\",\""+ ref_val +"\",\""+ ref_field +"\",\"" + fl + "\");' class=full type=text value='" + ex_val + "' />");
	  $("#focus_now").focus();
	  $("#edit_mode").val("1");
  } */
  </script>
  <style>
	.full{
		width:80%;
	}
  </style>
  <div id="w">
    <div id="content" style='min-height:470px;'>
      <h1 class="title_bar">Fill Exam Records</h1>
      <b>
	  <input type=hidden value="0" id=edit_mode />
	  <center>
	  <div id="save_req"></div>
	  <input type=text name=search_patient style='width:300px;' placeholder="Enter Patient Reference" <?php echo @$_GET['key']?"value='{$_GET['key']}'":"" ?> id=doc_search class=txtfield1 />
	  <a href="#loginmodal" class="flatbtn" id="search">Search</a></center>
	  
	  <?php echo $error; ?>
	  <div class="frm_out"></div>
	  <div class="doc_found" style='background-color:#fff; max-height:160px; overflow:auto;'>
	  
	  </div>
	  <div class="doc_selected" style='background-color:#fff;'>
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
  
  <?php
  //if the key get alelement is their searh automaticaly
  /* if(@$_GET['key'] && is_numeric($_GET['key'])){
	?>
	<script>
		$(document).ready(function(){
			$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
			
		});
	</script>
	<?php
  } */
  ?>
  <!-- make all links with the 'rel' attribute open overlays -->
<script>
function receivePatient(id, checked_data){
	//alert(checked_data);
	$("#doc_search").val(checked_data);
	
	setTimeout(function(e){$("#search").click()},100);
	
	setTimeout(function(e){
		$(".doc_selected").load("receive_patient.php?key=" + id );
	}, 200);
}
$(document).ready(function(){
	$("#doc_search").focus();
	//if the search button is clicked search the patient_found
	$("#search").click(function(e){
		$(".doc_selected").html("");
		$(".doc_found").load("find_patient.php?key="+$("#doc_search").val().replace(/ /g,"%20"));
		return e.preventDefault();
	});
	$("#insurance").change(function(e){
		$(".patient_selected").html("");
		$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
	});
	var data_sent;
	data_sent = false;
	$("#doc_search").keyup(function(e){
		if(!data_sent){
			data_sent = true;
			$(".doc_selected").html("");
			setTimeout(function(){
				$(".doc_found").load("find_patient.php?key="+$("#doc_search").val().replace(/ /g,"%20"));
				data_sent = false;
			},2000);
		}
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
if(@$_GET['rcv_patient']){
	?>
	<script>
		receivePatient("<?php echo @$_GET['patientid'] ?>","<?php echo @$_GET['key'] ?>");
	</script>
	<?php
}
?>
</body>
</html>