<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";

//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);

?>
<!doctype html>
<html lang="en-US">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <title>Care Mini Version 1</title>
  <link rel="shortcut icon" href="">
  <link rel="icon" href="">
  <link rel="stylesheet" type="text/css" media="all" href="../style.css">
  <!--<link rel="stylesheet" type="text/css" media="all" href="../style_menu.css">-->
  <link rel="stylesheet" type="text/css" media="all" href="../calendarcss.css">
  <link rel="stylesheet" type="text/css" media="all" href="../apple_css.css"><!--
  <script type="text/javascript" src="../js/jquery-1.9.1.min.js"></script>-->
  <script type="text/javascript" charset="utf-8" src="../js/jquery.full.js"></script>
  <script type="text/javascript" charset="utf-8" src="../js/jquery.form.js"></script>
  <script type="text/javascript" charset="utf-8" src="../js/calendarfile.js"></script>
  <!-- jQuery plugin leanModal under MIT License http://leanmodal.finelysliced.com.au/ -->
  
</head>

<body>
  <div id="topbar">
  <a href="./index.php">Acts</a> | 
  <a href="./medecines.php">Medecines Price</a> | 
  <a href="./insurance.php">Insurance</a> | 
  <!--<a href='./create.php'>Create New Document</a> |-->
  <a href='facture.php'>Facture</a> |
  <a href='../logout.php'>Logout</a>
  </div>
  
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Facture</h1>
      <b>
	  		
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
	  <?php $i=0; foreach($insurance as $in){ echo "<label><input type=radio onclick='if($(\"#in{$in['InsuranceNameID']}\").prop(\"checked\")){ findByInsurance(\"{$in['InsuranceNameID']}\") }' id='in{$in['InsuranceNameID']}' name='insurance_' ".(@$_POST['insurance'] == $in['InsuranceNameID']?"selected":"").">{$in['InsuranceName']}</label>"; } ?>
	  <label><input type=radio name=insurance_ value='-1'>All</label><br />
	  <input type=hidden name=insurance value='' id=insurance />
	  <table class=list-1><tr><td></td><td colspan=2>Month</td></tr>
	  <tr>
	  <td>
	  <label><input type=checkbox name=post value=1>Busoro</label> 
	  <label><input type=checkbox name=post value=2>Post</label></td>
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
		$month = array(1=>"January","Febuary","March","April","May","June","July","August","September","October","November","December");
		for($m = 1; $m <= 12;$m++){
			echo "<option value='{$m}' ".(date("m",time()) == $m?"selected":"").">{$month[$m]}</option>";
		}
		?>
	  </select>
	  </td><!--<td>
	  <input type=text class=txtfield1 style='width:250px;' id=medecines value='ALL' />
	  
	  </td>--><td>
	  <input type=button class=flatbtn-blu style='padding:0 10px;' id=generate value='Generate' />
	  </td></tr>
	  </table><!--
	  <table class=list-1><tr><td>Start Date</td><td>End Date</td></tr>
	  <tr><td>
	  </td><td>
	  <input type=text readonly class=txtfield1 style='width:250px;' id=start_date onclick='ds_sh(this,"start_date")' />
	  </td><td>
	  <input type=text readonly class=txtfield1 style='width:250px;' id=end_date onclick='ds_sh(this,"end_date")' />
	  </td><td>
	  <input type=button class=flatbtn-blu style='padding:0 10px;' id=generate value='Generate' />
	  </td></tr>
	  </table>
	  <!--<center>
	  <input type=text name=search_patient style='width:300px;' placeholder="Enter Document Number" <?php echo @$_GET['key']?"value='{$_GET['key']}'":"" ?> id=doc_search class=txtfield1 />
	  <a href="#loginmodal" class="flatbtn" id="search">Search</a></center>-->
	  <table class=list-1><tr><td>
	 <div id="ds" style='min-width:100px'></div></td></tr></table>
	  <?php echo $error; ?>
	  <div class="patient_found">
		<span class=error-text>Select the Insurance to view Current Patient</span>
	  </div>
	  <div class="doc_selected">
	  
	  </div>
	  </b>
    </div>
  </div>
  
	<div class="apple_overlay" id="overlay">
	  <!-- the external content is loaded inside this tag -->
	  <div class="contentWrap"></div>
	</div>
  
  <?php
  //if the key get alelement is their searh automaticaly
  if(@$_GET['key'] && is_numeric($_GET['key'])){
	?>
	<script>
		$(document).ready(function(){
			$(".patient_found").load("search_patient.php?key="+$("#patient_search").val() + "&ins=" + $("#insurance").val());
			
		});
	</script>
	<?php
  }
  ?>
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
	
	$("#generate").click(function(e){
		$("#ds").html("");
		$("#ds").removeClass("error");
		if(!$("#insurance").val()){
			$("#ds").addClass("error");
			$("#ds").html("Select Insurance");
			return e.preventDefault();
		}
		/* if(!$("#start_date").val()){
			$("#ds").addClass("error");
			$("#ds").html("Select Start Date!");
			return e.preventDefault();
		}
		if(!$("#end_date").val()){
			$("#ds").addClass("error");
			$("#ds").html("Select End Date!");
			return e.preventDefault();
		} */
		/* if($("#start_date").val() > $("#end_date").val()){
			$("#ds").addClass("error");
			$("#ds").html("The Starting Date is Greater than the End Date!");
			return e.preventDefault();
		}
		if($("#start_date").val() == $("#end_date").val()){
			if(!confirm("Facture pour seulement un seul jour?")){
				$("#ds").addClass("error");
				$("#ds").html("Select Different Dates!");
				return e.preventDefault();
			}
		} */
		$(".patient_found").load("search_patient.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val());
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