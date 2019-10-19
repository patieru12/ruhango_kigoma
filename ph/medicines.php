<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_POST);
$error = "";

//die;
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$centers = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT sy_center.* from sy_center ORDER BY Level ASC, CenterName ASC",$con),$multirows=true,$con);

$active = "report";
require_once "../lib2/cssmenu/ph_header.html";
?>
  
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Daily Medicines Consumption</h1>
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
	  <input type=hidden name=post_ value='_<?= $_SESSION['user']['CenterID']; ?>' id=post />
	  <table class=list-1><tr><td></td><td>Start Date</td><td>End Date</td><td>Medicine</td></tr>
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
	  <input type=text name=start_date class=txtfield1 style='width:150px;' id=start_date onclick='ds_sh(this,"start_date")' value='<?= date("Y-m-d",time()) ?>' />
	  <!--<select name=year class=txtfield1 style='width:70px;' id=year>
		<?php
		/* for($y = date("Y",time()); $y>="2015";$y--){
			echo "<option>{$y}</option>";
		} */
		?>
	  </select>-->
	  </td><td>
	  <input type=text  name=end_date class=txtfield1 style='width:150px;' id=end_date onclick='ds_sh(this,"end_date")' value='<?= date("Y-m-d",time()) ?>' />
	  <!--<select name=month class=txtfield1 style='width:140px;' id=month>
		<?php
		//$month = array(1=>"January","Febuary","March","April","May","June","July","August","September","October","November","December");
		/* for($m = 1; $m <= 12;$m++){
			echo "<option value='{$m}' ".(date("m",time()) == $m?"selected":"").">{$month[$m]}</option>";
		} */
		?>
	  </select>-->
	  </td><td>
	  <input type=text class=txtfield1 style='width:250px;' id=medecines value='ALL' />
	  
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
		<span class=error-text>Press Generate to view Daily Consumption of Medicines</span>
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
  
  <!-- make all links with the 'rel' attribute open overlays -->
<script>


function findByInsurance(ins){
	//$(".patient_selected").html("");
	//$(".patient_found").load("search_patient.php?key=" + ins);
	$("#insurance").val(ins);
	
}
$(document).ready(function(){
	/* $("#medecines").autocomplete("./auto/medecines.php", {
		selectFirst: true
	}); */
	$("#generate").click(function(e){
		$("#ds").html("");
		$("#ds").removeClass("error");
		
		if(!$("#medecines").val()){
			$("#ds").addClass("error");
			$("#ds").html("You Submit Empty Request the Response is All Medicines!");
			$("#medecines").val("ALL");
			//return e.preventDefault();
		}
		if(!$("#post").val()){
			$("#ds").addClass("error");
			$("#ds").html("Please Select the Post!");
			return e.preventDefault();
		}
		$(".patient_found").html("Please Wait...<br /><img src='../images/loading.gif' />");
		$(".patient_found").load("consu_medicines_daily.php?startdate=" + $("#start_date").val() + "&end_date=" + $("#end_date").val() + "&medicine=" + $("#medecines").val().replace(" ","%20") + "&post=" + $("#post").val());
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