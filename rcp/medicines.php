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
$insurance = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT in_name.* from in_name, in_category WHERE in_name.CategoryID=in_category.InsuranceCategoryID && in_name.InsuranceName='CBHI' ORDER BY InsuranceCode ASC, InsuranceName DESC",$con),$multirows=true,$con);
$centers = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT sy_center.* from sy_center ORDER BY Level ASC, CenterName ASC",$con),$multirows=true,$con);

$active = "mdtarif";
require_once "../lib2/cssmenu/rcp_header.html";
?>
  
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Medicines Tarif</h1>
      <b>
	  		
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <input type=hidden name=post_ value='_<?= $_SESSION['user']['CenterID']; ?>' id=post />
	  <table class=list-1><tr><td>Health Facility</td><td>Year</td><td>Month</td></tr>
	  <tr>
	  <td>
	  <?php
		foreach($centers as $c){
			?>
			<label><input type=checkbox <?= $_SESSION['user']['CenterID'] == $c['CenterID']?"checked":"" ?> onclick='if($("#post_<?= strtolower($c['CenterID']) ?>").prop("checked")){$("#post").val($("#post").val() + "_<?= strtolower($c['CenterID']) ?>")} else{ $("#post").val($("#post").val().replace("_<?= strtolower($c['CenterID']) ?>","")) }' id='post_<?= strtolower($c['CenterID']) ?>' value='<?= strtolower($c['CenterID']) ?>'><?= $c['CenterName'] ?></label> 
			<?php
		}
		?>
		<td>
	  <select name=year class=txtfield1 style='width:70px;' id=year>
		<?php
		for($y = date("Y",time()); $y>= $Start_Year; $y--){
			echo "<option>{$y}</option>";
		}
		?>
	  </select>
	  </td><td>
	  <select name=month class=txtfield1 style='width:140px;' id=month>
		<?php
		//$month = array(1=>"January","February","March","April","May","June","July","August","September","October","November","December");
		for($m = 1; $m <= 12;$m++){
			echo "<option value='".($m<10?"0".$m:$m)."' ".(date("m",time()) == $m?"selected":"").">{$month[$m]}</option>";
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
	  <div class="patient_found" style='height:95%; border:0px solid #000;'>
		<span class=error-text>Select Post and Month to view Used Tarif</span>
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
	
	$("#generate").click(function(e){
		$("#ds").html("");
		$("#ds").removeClass("error");
		
		$(".patient_found").html("Please Wait...<br /><img src='../images/loading.gif' />");
		$(".patient_found").load("get_medicines_tarif.php?month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val());
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
			valar clicked = $(e.target); // get the element clicked
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