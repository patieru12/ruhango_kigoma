<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("dm" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";

//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$centers = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT sy_center.* from sy_center ORDER BY Level ASC, CenterName ASC",$con),$multirows=true,$con);

$active = "report";
require_once "../lib2/cssmenu/dm_header.html";

?>
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Diagnostic Report</h1>
      <b>
					
		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
			   style="display: none;">
			<tr>
				<td id="ds_calclass"></td>
			</tr></table>
	 	<form action="Sample_09_Diagnostics.php" method=post id=sis_report >
			<input type=hidden name=post_ value='_<?= $_SESSION['user']['CenterID']; ?>' id=post />
			<table class=list-1><tr><td></td><td>Start Date</td><td>End Date</td></tr>
			  	<tr>
			  		<td>
			  			<?php
						foreach($centers as $c){
							?>
							<label><input type=checkbox <?= $_SESSION['user']['CenterID'] == $c['CenterID']?"checked":"" ?> onclick='if($("#post_<?= strtolower($c['CenterName']) ?>").prop("checked")){$("#post").val($("#post").val() + "_<?= strtolower($c['CenterID']) ?>")} else{ $("#post").val($("#post").val().replace("_<?= strtolower($c['CenterID']) ?>","")) }' id='post_<?= strtolower($c['CenterName']) ?>' value='<?= strtolower($c['CenterID']) ?>'><?= $c['CenterName'] ?></label> 
							<?php
						}
						?>
					</td>
			  		<td>
			  			<input type=text name=start_date class=txtfield1 style='width:150px;' id=start_date onclick='ds_sh(this,"start_date")' value='<?= date("Y-m-01",time()) ?>' />
			  		</td>
			  		<td>
			  			<input type=text  name=end_date class=txtfield1 style='width:150px;' id=end_date onclick='ds_sh(this,"end_date")' value='<?= date("Y-m-d",time()) ?>' />
			  		</td>
			  		<td>
			  			<input type=button class=flatbtn-blu style='padding:0 10px;' id=generate value='Generate' />
			  		</td>
			  	</tr>
			</table>
	  </form>
	  <div class=generate_report style="border:0px solid #000; overflow: auto; max-height:90%;"></div>
	  </b>
    </div>
  </div>
  <?php
  include_once "../footer.html";
  ?>
  <style type="text/css">
  	div.generate_report table.list tr td{
  		font-size: 12px;
  	}
  	div.generate_report table.list tr td.number{
  		text-align: right;
  		padding-right: 2px;
  	}
  	div.generate_report table.list tr th{
  		font-size: 12px;
  		font-weight: bold;
  	}
  </style>
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
	$(".diagnostic").load("./conf/diagnostic.php");
	$(".special").load("./conf/special_diagnostic.php");
	//$(".update_special").load("./conf/diagnostic.php");
	
	$(".save_specials").click(function(e){
			alert($("#new_special").val());
		});
		
	$("#generate").click(function(e){
		
		$(".generate_report").html('');
		$(".generate_report").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
		$("#sis_report").ajaxForm({ 
			target: '.generate_report'
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